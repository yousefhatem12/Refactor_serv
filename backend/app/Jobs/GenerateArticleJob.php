<?php

namespace App\Jobs;

use App\Models\AiCredential;
use App\Models\AiGenerationItem;
use App\Models\AiGenerationRequest;
use App\Services\Ai\AiProviderManager;
use App\Services\Ai\ArticleContentService;
use App\Services\Ai\InternalLinkService;
use App\Services\Ai\GeneratedPostService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateArticleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 180;

    protected int $itemId;

    /**
     * Create a new job instance.
     *
     * @param int $itemId
     */
    public function __construct(int $itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $item = AiGenerationItem::findOrFail($this->itemId);
        $request = $item->request;

        // Skip if already processed successfully
        if ($item->status === 'completed') {
            return;
        }

        $item->update([
            'status' => 'generating',
            'attempts' => $item->attempts + 1,
        ]);

        $request->update(['status' => 'generating']);

        try {
            // 1. Retrieve Active AI Credential
            $credential = AiCredential::where('provider', $request->provider)
                ->where('is_active', true)
                ->first();

            if (!$credential) {
                throw new \Exception("No active AI credential configured for provider: {$request->provider}");
            }

            $token = $credential->getDecryptedToken();
            if (empty($token)) {
                throw new \Exception("AI Credential API token is empty or invalid.");
            }

            // 2. Build instructions prompt
            $prompt = ArticleContentService::buildPrompt(
                $item->keyword,
                $request->secondary_keywords ?? [],
                $request->language,
                $credential->default_article_length ?? 1200
            );

            // 3. Resolve Provider and run self-correction loop up to 3 times
            $provider = AiProviderManager::getProvider($request->provider);
            $temperature = $credential->temperature ?? 0.7;
            // DeepSeek needs room for JSON markup, FAQs and HTML. Bilingual
            // output is roughly twice the size of a single-language article.
            $isReasoner = str_contains(strtolower($request->model), 'reasoner');
            $maxTokens = $isReasoner
                ? min(65536, max(32768, (int) ceil(($credential->default_article_length ?? 1200) * ($request->language === 'both' ? 10 : 6))))
                : ($request->language === 'both'
                    ? min(16384, max(8192, (int) ceil(($credential->default_article_length ?? 1200) * 5.5)))
                    : min(16384, max(6144, (int) ceil(($credential->default_article_length ?? 1200) * 3.5))));
            
            $attempts = 0;
            $maxAttempts = 3;
            $bestResponse = null;
            $bestQualityReport = null;
            $lastException = null;

            while ($attempts < $maxAttempts) {
                $attempts++;
                
                try {
                    $parsedResponse = $provider->generateStructuredContent(
                        $prompt,
                        $request->model,
                        $temperature,
                        $maxTokens,
                        $token
                    );

                    $qualityReport = ArticleContentService::validateAndGenerateReport($parsedResponse, $item->keyword, $request->language);

                    // If zero errors and zero warnings, it's perfect!
                    if (count($qualityReport['errors']) === 0 && count($qualityReport['warnings']) === 0) {
                        $bestResponse = $parsedResponse;
                        $bestQualityReport = $qualityReport;
                        break;
                    }

                    // Keep track of the best attempt (prioritize zero hard errors, then lowest warnings count)
                    if ($bestQualityReport === null || (count($qualityReport['errors']) === 0 && count($qualityReport['warnings']) < count($bestQualityReport['warnings']))) {
                        $bestResponse = $parsedResponse;
                        $bestQualityReport = $qualityReport;
                    }

                    // Prepare feedback loop instructions for the next attempt
                    if ($attempts < $maxAttempts) {
                        $temperature = max(0.2, $temperature - 0.15); // reduce creativity to boost rule compliance
                        
                        $issues = array_merge($qualityReport['errors'], $qualityReport['warnings']);
                        $issuesList = implode("\n- ", $issues);

                        $prompt = "You previously generated a response for the topic '{$item->keyword}', but it did not meet the quality guidelines and had these issues:\n" .
                                  "- {$issuesList}\n\n" .
                                  "Please rewrite and correct ALL of the listed issues. Ensure that:\n" .
                                  "1. Every single paragraph has between 40 and 90 words.\n" .
                                  "2. Sentence lengths average between 15 and 22 words.\n" .
                                  "3. Do NOT use any AI clichés (like 'لا شك أن', 'في عالم اليوم', 'مما لا شك فيه', 'علاوة على ذلك', etc.).\n" .
                                  "4. Follow the original schema requirements. Return valid JSON matching the exact schema:\n" .
                                  ArticleContentService::buildPrompt($item->keyword, $request->secondary_keywords ?? [], $request->language, $credential->default_article_length ?? 1200);
                    }

                } catch (\Throwable $e) {
                    $lastException = $e;
                    Log::warning("GenerateArticleJob self-correction attempt {$attempts} for item {$item->id} failed: " . $e->getMessage());
                    
                    if ($attempts >= $maxAttempts && $bestResponse === null) {
                        throw $e;
                    }
                }
            }

            if ($bestResponse === null) {
                throw $lastException ?? new \Exception("Failed to generate article after {$maxAttempts} attempts.");
            }

            $parsedResponse = $bestResponse;
            // Convert only real, relevant site content into clickable internal links.
            $parsedResponse = InternalLinkService::apply($parsedResponse, $request->language, $item->keyword);
            $qualityReport = $bestQualityReport;
            $qualityReport = ArticleContentService::validateAndGenerateReport($parsedResponse, $item->keyword, $request->language);

            if (count($qualityReport['errors']) > 0) {
                throw new \Exception("AI generated content failed validation: " . implode(' | ', $qualityReport['errors']));
            }

            // 5. Build post payload and create the draft safely within transactional scope
            DB::transaction(function () use ($item, $request, $parsedResponse, $qualityReport) {
                $post = GeneratedPostService::createDraft([
                    'title' => [
                        'en' => $parsedResponse['title_en'] ?? ($request->language === 'ar' ? null : $item->keyword),
                        'ar' => $parsedResponse['title_ar'] ?? ($request->language === 'en' ? null : $item->keyword),
                    ],
                    'slug' => $parsedResponse['slug'] ?? null,
                    'content' => [
                        'en' => (!empty($parsedResponse['introduction_en']) || !empty($parsedResponse['body_en'])) 
                            ? ("<p>" . ($parsedResponse['introduction_en'] ?? '') . "</p>\n" . ($parsedResponse['body_en'] ?? ''))
                            : null,
                        'ar' => (!empty($parsedResponse['introduction_ar']) || !empty($parsedResponse['body_ar']))
                            ? ("<p>" . ($parsedResponse['introduction_ar'] ?? '') . "</p>\n" . ($parsedResponse['body_ar'] ?? ''))
                            : null,
                    ],
                    'blog_category_id' => $request->category_id,
                    'author_id' => $request->author_id,
                    'seo_title' => [
                        'en' => $parsedResponse['seo_title_en'] ?? null,
                        'ar' => $parsedResponse['seo_title_ar'] ?? null,
                    ],
                    'seo_description' => [
                        'en' => $parsedResponse['meta_description_en'] ?? null,
                        'ar' => $parsedResponse['meta_description_ar'] ?? null,
                    ],
                ]);

                // Update item reference
                $item->update([
                    'status' => 'completed',
                    'post_id' => $post->id,
                    'parsed_response' => $parsedResponse,
                    'quality_report' => $qualityReport,
                    'usage_metadata' => $parsedResponse['usage_metadata'] ?? null,
                    'error_message' => null,
                ]);

                // Increment request counter
                $item->request->increment('completed_items');
            });

            $this->checkRequestCompletion($request);

        } catch (\Throwable $e) {
            Log::error("GenerateArticleJob failed for item {$this->itemId}: " . $e->getMessage());

            // Laravel will retry thrown exceptions. Do not count an item as
            // permanently failed until the final queue attempt.
            if ($this->attempts() >= $this->tries) {
                $item->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $request->increment('failed_items');
                $this->checkRequestCompletion($request);
            } else {
                $item->update([
                    'status' => 'pending',
                    'error_message' => $e->getMessage(),
                ]);
            }

            // Fail job
            throw $e;
        }
    }

    /**
     * Backoff schedule for queue retries.
     */
    public function backoff(): array
    {
        return [10, 30];
    }

    private function checkRequestCompletion(AiGenerationRequest $request): void
    {
        $request->refresh();
        $totalItems = $request->total_items;
        $processed = $request->completed_items + $request->failed_items;

        if ($processed >= $totalItems) {
            $status = 'completed';
            if ($request->failed_items == $totalItems) {
                $status = 'failed';
            } elseif ($request->failed_items > 0) {
                $status = 'partially_completed';
            }
            
            $request->update([
                'status' => $status,
                'completed_at' => now(),
            ]);
        }
    }
}

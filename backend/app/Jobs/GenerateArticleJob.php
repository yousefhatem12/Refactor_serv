<?php

namespace App\Jobs;

use App\Models\AiCredential;
use App\Models\AiGenerationItem;
use App\Models\AiGenerationRequest;
use App\Services\Ai\AiProviderManager;
use App\Services\Ai\ArticleContentService;
use App\Services\Ai\BrandContextService;
use App\Services\Ai\DeepSeekProvider;
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
    // Keep this safely below the worker timeout (300 seconds in production).
    public int $timeout = 270;

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

        if ($request->status === 'cancelled') {
            return;
        }

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
            $settings = $request->settings_snapshot ?? [];
            $targetLength = (int) ($settings['length'] ?? $credential->default_article_length ?? 1200);
            $temperature = (float) ($settings['temperature'] ?? $credential->temperature ?? 0.7);
            $arabicDialect = (string) ($settings['arabic_dialect'] ?? 'egyptian');
            $suggestKeywords = (bool) ($settings['suggest_keywords'] ?? true);
            $categoryName = is_array($request->category?->name)
                ? implode(' ', array_filter($request->category->name))
                : (string) ($request->category?->name ?? '');
            $brandContext = BrandContextService::build($item->keyword, $request->language, $categoryName);

            $prompt = ArticleContentService::buildPrompt(
                $item->keyword,
                $request->secondary_keywords ?? [],
                $request->language,
                $targetLength,
                $arabicDialect,
                $suggestKeywords,
                $brandContext
            );

            if ($request->generation_mode === 'topic_cluster' && !empty($settings['cluster_plan'])) {
                $currentTopic = collect($settings['cluster_plan'])->firstWhere('keyword', $item->keyword) ?? [];
                $clusterTopics = collect($settings['cluster_plan'])->pluck('keyword')->filter()->implode(' | ');
                $role = ($currentTopic['search_intent'] ?? null) === 'pillar' ? 'PILLAR' : 'SUPPORTING';
                $prompt .= "\n\nTOPIC CLUSTER CONTEXT — MANDATORY:\n";
                $prompt .= "This is the {$role} article in one coordinated cluster. Full cluster topics: {$clusterTopics}.\n";
                $prompt .= "This article's unique search intent is: " . ($currentTopic['search_intent'] ?? 'informational') . ". Its exclusive angle is: " . ($currentTopic['angle'] ?? 'Cover only this article topic in depth') . ".\n";
                $prompt .= "Stay inside this article's scope, avoid duplicating the other cluster articles, and mention the pillar/supporting topics naturally where useful so the internal-linking system can connect them later.\n";
            }

            // 3. Resolve Provider and run self-correction loop up to 3 times
            $provider = AiProviderManager::getProvider($request->provider);
            // Reasoning needs room for thinking plus JSON, but 32K–65K tokens
            // made normal requests take several minutes and get reclaimed by
            // the database queue. These limits cover a full article without
            // making one job monopolize the worker.
            $isReasoner = str_contains(strtolower($request->model), 'reasoner');
            $maxTokens = $isReasoner
                ? ($request->language === 'both'
                    ? min(24000, max(16000, (int) ceil($targetLength * 12)))
                    : min(16000, max(9000, (int) ceil($targetLength * 8))))
                : ($request->language === 'both'
                    ? min(16384, max(8192, (int) ceil($targetLength * 5.5)))
                    : min(16384, max(6144, (int) ceil($targetLength * 3.5))));
            if ($request->provider === 'deepseek') {
                $maxTokens = $request->language === 'both'
                    ? DeepSeekProvider::BILINGUAL_ARTICLE_TOKENS
                    : DeepSeekProvider::SINGLE_LANGUAGE_ARTICLE_TOKENS;
            }
            
            $attempts = 0;
            // Warnings can be repaired on demand. Regenerating a complete
            // article three times for warnings is expensive and slow.
            $maxAttempts = 2;
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
                    $parsedResponse = ArticleContentService::sanitizeHtmlFields($parsedResponse);

                    $qualityReport = ArticleContentService::validateAndGenerateReport($parsedResponse, $item->keyword, $request->language);

                    // A structurally valid article is saved immediately. The
                    // user can invoke Fix warnings later for soft quality work.
                    if (count($qualityReport['errors']) === 0) {
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
                        
                        $issues = $qualityReport['errors'];
                        $issuesList = implode("\n- ", $issues);

                        $prompt = "You previously generated a response for the topic '{$item->keyword}', but it did not meet the quality guidelines and had these issues:\n" .
                                  "- {$issuesList}\n\n" .
                                  "Please rewrite and correct ALL of the listed issues. Ensure that:\n" .
                                  "1. Every single paragraph has between 40 and 90 words.\n" .
                                  "2. Sentence lengths average between 15 and 22 words.\n" .
                                  "3. Do NOT use any AI clichés (like 'لا شك أن', 'في عالم اليوم', 'مما لا شك فيه', 'علاوة على ذلك', etc.).\n" .
                                  "4. Follow the original schema requirements. Return valid JSON matching the exact schema:\n" .
                                  ArticleContentService::buildPrompt($item->keyword, $request->secondary_keywords ?? [], $request->language, $targetLength, $arabicDialect, $suggestKeywords, $brandContext);
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

            // The administrator may cancel while the provider request is in
            // flight. Never create a draft after that cancellation.
            if ($request->fresh()->status === 'cancelled') {
                return;
            }

            $parsedResponse = $bestResponse;
            $parsedResponse = ArticleContentService::sanitizeHtmlFields($parsedResponse);
            $parsedResponse = ArticleContentService::renderFaqIntoBodies($parsedResponse, $request->language);
            // Convert only real, relevant site content into clickable internal links.
            $parsedResponse = InternalLinkService::apply($parsedResponse, $request->language, $item->keyword, $categoryName);
            $parsedResponse = ArticleContentService::sanitizeHtmlFields($parsedResponse);
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
                    'ai_metadata' => [
                        'faq' => $parsedResponse['faq'] ?? [],
                        'faq_schema' => $parsedResponse['faq_schema'] ?? [],
                        'schema_type' => $parsedResponse['schema_type'] ?? 'BlogPosting',
                        'schema_markup' => $parsedResponse['schema_markup'] ?? [],
                        'internal_link_suggestions' => $parsedResponse['internal_link_suggestions'] ?? [],
                        'quality_report' => $qualityReport,
                    ],
                    'image_metadata' => $parsedResponse['image'] ?? null,
                ]);

                GeneratedPostService::autoPublishIfEnabled($post, $request);

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
            
            if ($request->generation_mode === 'topic_cluster' && $status === 'completed') {
                $claimed = AiGenerationRequest::query()
                    ->whereKey($request->id)
                    ->whereNotIn('status', ['finalizing', 'completed', 'cancelled'])
                    ->update(['status' => 'finalizing', 'error_message' => null]);

                if ($claimed === 1) {
                    dispatch(new FinalizeTopicClusterLinksJob($request->id));
                }
                return;
            }

            $request->update(['status' => $status, 'completed_at' => now()]);
        }
    }
}

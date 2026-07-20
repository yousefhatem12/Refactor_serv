<?php

namespace App\Jobs;

use App\Models\AiCredential;
use App\Models\AiGenerationItem;
use App\Services\Ai\AiProviderManager;
use App\Services\Ai\ArticleContentService;
use App\Services\Ai\InternalLinkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepairArticleQualityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 180;
    public array $backoff = [15, 45];

    public function __construct(public int $itemId) {}

    public function handle(): void
    {
        $item = AiGenerationItem::with(['request', 'post'])->findOrFail($this->itemId);
        if (!in_array($item->status, ['completed', 'repairing'], true) || !$item->post || !$item->parsed_response) {
            throw new \RuntimeException('Only a completed article with an existing draft can be repaired.');
        }

        $request = $item->request;
        $credential = AiCredential::where('provider', $request->provider)->where('is_active', true)->firstOrFail();
        $token = $credential->getDecryptedToken();
        $article = $item->parsed_response;
        $warnings = $item->quality_report['warnings'] ?? [];
        $initialWarningCount = count($warnings);
        $arabicIssues = array_values(array_filter($warnings, fn ($warning) => str_contains($warning, '(ar)') || str_contains($warning, 'Arabic')));
        $englishIssues = array_values(array_filter($warnings, fn ($warning) => str_contains($warning, '(en)') || str_contains($warning, 'English')));
        $prompt = ArticleContentService::buildPrompt(
            $item->keyword,
            $request->secondary_keywords ?? [],
            $request->language,
            $credential->default_article_length ?? 1200
        );
        $prompt .= "\n\nREPAIR MODE — FOLLOW THIS EXACTLY:\n";
        $prompt .= "Return the complete JSON schema again. Preserve the topic, useful facts, structure, slug, FAQ, image data, and every field that is not listed as a warning. Do not shorten or remove good content.\n";
        $prompt .= "Fix the Arabic fields first and treat every Arabic warning as mandatory. Arabic checklist: introduction_ar must be 100-150 words; seo_title_ar must be 50-60 characters and contain the Arabic primary keyword; meta_description_ar must be 140-160 characters, describe the service, contain the keyword, and end with a simple CTA; the Arabic primary keyword must appear naturally in the title, introduction, at least one H2, body, and conclusion; keep Arabic keyword density between 0.8% and 1.5%; use clear Modern Standard Arabic and do not add filler.\n";
        if (!empty($arabicIssues)) {
            $prompt .= "MANDATORY ARABIC WARNINGS:\n- " . implode("\n- ", $arabicIssues) . "\n";
        }
        if (!empty($englishIssues)) {
            $prompt .= "ENGLISH WARNINGS (fix only these; leave other English content unchanged):\n- " . implode("\n- ", $englishIssues) . "\n";
        }
        $prompt .= "Do not return an explanation. Output JSON only. CURRENT ARTICLE:\n" . json_encode($article, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $isReasoner = str_contains(strtolower($request->model), 'reasoner');
        $maxTokens = $isReasoner
            ? min(65536, max(32768, (int) ceil(($credential->default_article_length ?? 1200) * ($request->language === 'both' ? 10 : 6))))
            : ($request->language === 'both'
                ? min(16384, max(8192, (int) ceil(($credential->default_article_length ?? 1200) * 5.5)))
                : min(16384, max(6144, (int) ceil(($credential->default_article_length ?? 1200) * 3.5))));
        $provider = AiProviderManager::getProvider($request->provider);
        $repaired = $article;
        $report = $item->quality_report ?? ArticleContentService::validateAndGenerateReport($article, $item->keyword, $request->language);
        $bestWarningCount = $initialWarningCount;
        $lastError = null;
        for ($repairAttempt = 1; $repairAttempt <= 3; $repairAttempt++) {
            try {
                $attemptPrompt = $prompt . "\nThis is repair attempt {$repairAttempt} of 3. The previous result was not accepted unless it reduces the warning count. Make measurable changes to every listed field.\n";
                $candidate = $provider->generateStructuredContent(
                    $attemptPrompt,
                    $request->model,
                    min(0.2, (float) ($credential->temperature ?? 0.7)),
                    $maxTokens,
                    $token
                );
                $candidate = InternalLinkService::apply($candidate, $request->language, $item->keyword);
                $candidateReport = ArticleContentService::validateAndGenerateReport($candidate, $item->keyword, $request->language);
                if (!empty($candidateReport['errors'])) {
                    throw new \RuntimeException('Quality repair returned invalid content: ' . implode(' | ', $candidateReport['errors']));
                }
                $candidateWarningCount = count($candidateReport['warnings']);
                if ($candidateWarningCount < $bestWarningCount || $repairAttempt === 1) {
                    $repaired = $candidate;
                    $report = $candidateReport;
                    $bestWarningCount = $candidateWarningCount;
                }
                if ($candidateWarningCount === 0) {
                    break;
                }
            } catch (\Throwable $e) {
                $lastError = $e;
                Log::warning('RepairArticleQualityJob attempt failed', ['item_id' => $item->id, 'attempt' => $repairAttempt, 'error' => $e->getMessage()]);
            }
        }
        Log::info('RepairArticleQualityJob completed', ['item_id' => $item->id, 'warnings_before' => $initialWarningCount, 'warnings_after' => $bestWarningCount]);
        if ($lastError && $repaired === $article) {
            throw $lastError;
        }

        $repaired = InternalLinkService::apply($repaired, $request->language, $item->keyword);
        $report = ArticleContentService::validateAndGenerateReport($repaired, $item->keyword, $request->language);

        $item->post->update([
            'title' => ['en' => $repaired['title_en'] ?? null, 'ar' => $repaired['title_ar'] ?? null],
            'slug' => $repaired['slug'] ?? $item->post->slug,
            'content' => [
                'en' => !empty($repaired['introduction_en']) || !empty($repaired['body_en']) ? '<p>' . ($repaired['introduction_en'] ?? '') . '</p>\n' . ($repaired['body_en'] ?? '') : null,
                'ar' => !empty($repaired['introduction_ar']) || !empty($repaired['body_ar']) ? '<p>' . ($repaired['introduction_ar'] ?? '') . '</p>\n' . ($repaired['body_ar'] ?? '') : null,
            ],
            'seo_title' => ['en' => $repaired['seo_title_en'] ?? null, 'ar' => $repaired['seo_title_ar'] ?? null],
            'seo_description' => ['en' => $repaired['meta_description_en'] ?? null, 'ar' => $repaired['meta_description_ar'] ?? null],
        ]);
        $item->update(['status' => 'completed', 'parsed_response' => $repaired, 'quality_report' => $report, 'error_message' => null]);
        $this->finishRequest($item);
    }

    public function failed(\Throwable $exception): void
    {
        $item = AiGenerationItem::with('request')->find($this->itemId);
        if ($item) {
            $item->update([
                'status' => 'completed',
                'error_message' => 'Quality repair failed: ' . mb_substr($exception->getMessage(), 0, 500),
            ]);
            $this->finishRequest($item);
        }
        Log::error('RepairArticleQualityJob failed', [
            'item_id' => $this->itemId,
            'error' => $exception->getMessage(),
        ]);
    }

    private function finishRequest(AiGenerationItem $item): void
    {
        $request = $item->request->fresh();
        if (!$request->items()->where('status', 'repairing')->exists()) {
            $request->update(['status' => 'completed']);
        }
    }
}

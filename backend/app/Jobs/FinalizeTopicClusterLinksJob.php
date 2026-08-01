<?php

namespace App\Jobs;

use App\Models\AiGenerationRequest;
use App\Services\Ai\ArticleContentService;
use App\Services\Ai\GeneratedHtmlSanitizer;
use App\Services\Ai\GeneratedPostService;
use App\Services\Ai\TopicClusterLinkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinalizeTopicClusterLinksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public array $backoff = [10, 30];

    public function __construct(public int $requestId) {}

    public function handle(): void
    {
        DB::transaction(function (): void {
            $request = AiGenerationRequest::query()->lockForUpdate()->findOrFail($this->requestId);
            if ($request->status === 'cancelled') {
                return;
            }
            if ($request->generation_mode !== 'topic_cluster') {
                throw new \RuntimeException('Only topic-cluster requests can be finalized.');
            }

            $items = $request->items()->with('post')->orderBy('id')->get();
            if ($items->count() !== $request->total_items || $items->contains(fn ($item) => $item->status !== 'completed' || !$item->post || !$item->parsed_response)) {
                throw new \RuntimeException('Topic cluster cannot be linked until every article draft is complete.');
            }

            foreach ($items as $item) {
                $article = TopicClusterLinkService::apply(
                    $item->parsed_response,
                    $item,
                    $items,
                    $request->language,
                    $request->main_keyword
                );
                $report = ArticleContentService::validateAndGenerateReport($article, $item->keyword, $request->language);
                if ($report['errors'] !== []) {
                    throw new \RuntimeException('Cluster linking produced invalid article content: ' . implode(' | ', $report['errors']));
                }

                $content = [];
                foreach (['ar', 'en'] as $lang) {
                    $intro = $article["introduction_{$lang}"] ?? null;
                    $body = $article["body_{$lang}"] ?? null;
                    $content[$lang] = ($intro || $body)
                        ? GeneratedHtmlSanitizer::sanitize('<p>' . ($intro ?? '') . '</p>' . ($body ?? ''))
                        : null;
                }

                $metadata = $item->post->ai_metadata ?? [];
                $metadata['internal_link_suggestions'] = $article['internal_link_suggestions'] ?? [];
                $metadata['quality_report'] = $report;

                $item->post->update(['content' => $content, 'ai_metadata' => $metadata]);
                GeneratedPostService::autoPublishIfEnabled($item->post, $request, clusterFinalized: true);
                $item->update([
                    'parsed_response' => $article,
                    'quality_report' => $report,
                    'error_message' => null,
                ]);
            }

            $request->update([
                'status' => 'completed',
                'error_message' => null,
                'completed_at' => now(),
            ]);
        });
    }

    public function failed(\Throwable $exception): void
    {
        AiGenerationRequest::query()
            ->whereKey($this->requestId)
            ->where('status', '!=', 'cancelled')
            ->update([
                'status' => 'partially_completed',
                'error_message' => mb_substr('Topic cluster linking failed: ' . $exception->getMessage(), 0, 2000),
            ]);

        Log::error('FinalizeTopicClusterLinksJob failed', [
            'request_id' => $this->requestId,
            'error' => $exception->getMessage(),
        ]);
    }
}

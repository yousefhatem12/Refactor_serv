<?php

namespace App\Jobs;

use App\Models\AiCredential;
use App\Models\AiGenerationRequest;
use App\Models\NavLink;
use App\Models\Page;
use App\Models\Service;
use App\Models\User;
use App\Services\Ai\AiPageContentService;
use App\Services\Ai\AiProviderManager;
use App\Services\Ai\BrandContextService;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAiPageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 270;
    public array $backoff = [15, 45];

    public function __construct(public int $pageId) {}

    public function handle(): void
    {
        $page = Page::findOrFail($this->pageId);
        if (!$page->ai_generated || !in_array($page->ai_status, ['queued', 'generating', 'failed'], true)) {
            return;
        }

        $metadata = is_array($page->ai_metadata) ? $page->ai_metadata : [];
        $generationRequest = $this->generationRequest($page);
        $brief = is_array($metadata['brief'] ?? null) ? $metadata['brief'] : [];
        $providerName = (string) ($metadata['provider'] ?? '');
        $model = (string) ($metadata['model'] ?? '');
        if ($providerName === '' || $model === '' || trim((string) ($brief['page_brief'] ?? '')) === '') {
            throw new \RuntimeException('AI page generation metadata is incomplete.');
        }

        $page->update([
            'ai_status' => 'generating',
            'is_published' => false,
            'ai_metadata' => array_merge($metadata, [
                'started_at' => now()->toIso8601String(),
                'error' => null,
            ]),
        ]);
        $generationRequest?->update([
            'status' => 'generating',
            'started_at' => now(),
            'error_message' => null,
        ]);

        try {
            $credential = AiCredential::query()
                ->where('provider', $providerName)
                ->where('is_active', true)
                ->firstOrFail();
            $token = $credential->getDecryptedToken();
            if (!$token) {
                throw new \RuntimeException("No usable API token is configured for {$providerName}.");
            }

            $serviceName = '';
            if (!empty($brief['service_id'])) {
                $service = Service::query()->where('is_active', true)->find($brief['service_id']);
                if ($service) {
                    $serviceName = self::localizedText($service->name);
                }
            }
            $contextQuery = implode(' ', array_filter([
                $brief['primary_keyword'] ?? null,
                $brief['page_brief'] ?? null,
                $serviceName,
            ]));
            $brandContext = BrandContextService::build($contextQuery, 'both', $serviceName, websiteWide: true);
            $prompt = AiPageContentService::buildPrompt($brief, $brandContext);
            $provider = AiProviderManager::getProvider($providerName);
            $maxTokens = match ($brief['page_depth'] ?? 'standard') {
                'compact' => 8192,
                'extended' => 16384,
                default => AiPageContentService::DEFAULT_MAX_TOKENS,
            };
            $response = $provider->generateStructuredContent(
                $prompt,
                $model,
                (float) ($brief['temperature'] ?? 0.7),
                $maxTokens,
                $token
            );
            $generated = AiPageContentService::validateAndNormalize($response, $brief);

            $slug = self::uniqueSlug($generated['slug'], $page->id);
            $blocks = $generated['blocks'];
            $autoPublish = (bool) ($brief['auto_publish'] ?? false);
            $freshMetadata = is_array($page->fresh()->ai_metadata) ? $page->fresh()->ai_metadata : $metadata;
            $page->update([
                'slug' => $slug,
                'title' => $generated['title'],
                'subtitle' => $generated['subtitle'],
                'content' => ['en' => null, 'ar' => null],
                'seo_title' => $generated['seo_title'],
                'seo_description' => $generated['seo_description'],
                'sections' => [
                    'template' => 'custom',
                    // Filament edits blocks; the localized aliases keep the
                    // existing public PageResource contract compatible.
                    'blocks' => $blocks,
                    'en' => $blocks,
                    'ar' => $blocks,
                ],
                'is_published' => $autoPublish,
                'ai_status' => $autoPublish ? 'approved' : 'pending_review',
                'ai_metadata' => array_merge($freshMetadata, [
                    'requested_template' => $brief['page_template'] ?? 'custom',
                    'visual_direction' => $generated['visual_direction'],
                    'usage_metadata' => $generated['usage_metadata'],
                    'completed_at' => now()->toIso8601String(),
                    'error' => null,
                ]),
            ]);
            $generationRequest?->update([
                'status' => 'completed',
                'completed_items' => 1,
                'failed_items' => 0,
                'completed_at' => now(),
                'error_message' => null,
            ]);

            // Auto-add to navigation if a placement was chosen
            if ($autoPublish && !empty($brief['nav_placement_parent_id'])) {
                self::createNavLink($page, (int) $brief['nav_placement_parent_id']);
            }

            self::notifyCreator($page, $autoPublish, false);
        } catch (\Throwable $exception) {
            $freshMetadata = is_array($page->fresh()->ai_metadata) ? $page->fresh()->ai_metadata : $metadata;
            $page->update([
                'ai_status' => 'failed',
                'is_published' => false,
                'ai_metadata' => array_merge($freshMetadata, [
                    'error' => mb_substr($exception->getMessage(), 0, 1500),
                    'failed_at' => now()->toIso8601String(),
                ]),
            ]);
            $generationRequest?->update([
                'status' => 'failed',
                'completed_items' => 0,
                'failed_items' => 1,
                'error_message' => mb_substr($exception->getMessage(), 0, 1500),
            ]);
            Log::error('GenerateAiPageJob failed', ['page_id' => $page->id, 'error' => $exception->getMessage()]);
            throw $exception;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $page = Page::find($this->pageId);
        if (!$page) {
            return;
        }
        $metadata = is_array($page->ai_metadata) ? $page->ai_metadata : [];
        $page->update([
            'ai_status' => 'failed',
            'is_published' => false,
            'ai_metadata' => array_merge($metadata, ['error' => mb_substr($exception->getMessage(), 0, 1500)]),
        ]);
        $this->generationRequest($page)?->update([
            'status' => 'failed',
            'completed_items' => 0,
            'failed_items' => 1,
            'error_message' => mb_substr($exception->getMessage(), 0, 1500),
        ]);
        self::notifyCreator($page, false, true);
    }

    private function generationRequest(Page $page): ?AiGenerationRequest
    {
        $requestId = data_get($page->ai_metadata, 'generation_request_id');
        return $requestId ? AiGenerationRequest::find($requestId) : null;
    }

    private static function uniqueSlug(string $slug, int $ignoreId): string
    {
        $base = $slug;
        $candidate = $base;
        $suffix = 2;
        while (Page::query()->where('slug', $candidate)->where('id', '!=', $ignoreId)->exists()) {
            $candidate = $base . '-' . $suffix++;
        }
        return $candidate;
    }

    private static function createNavLink(Page $page, int $parentId): void
    {
        $parent = NavLink::find($parentId);
        if (!$parent) {
            return;
        }

        // Avoid duplicate nav links for the same slug
        $url = '/' . ltrim($page->slug, '/');
        $alreadyExists = NavLink::where('url', $url)->where('parent_id', $parentId)->exists();
        if ($alreadyExists) {
            return;
        }

        $title = is_array($page->title) ? $page->title : ['en' => (string) $page->title, 'ar' => (string) $page->title];
        $maxSort = NavLink::where('parent_id', $parentId)->max('sort_order') ?? 0;

        NavLink::create([
            'label'      => [
                'en' => $title['en'] ?? $title['ar'] ?? $page->slug,
                'ar' => $title['ar'] ?? $title['en'] ?? $page->slug,
            ],
            'url'        => $url,
            'parent_id'  => $parentId,
            'sort_order' => $maxSort + 1,
            'is_active'  => true,
        ]);
    }

    private static function localizedText(mixed $value): string
    {
        if (is_array($value)) {
            return trim((string) ($value['en'] ?? $value['ar'] ?? ''));
        }
        return trim((string) $value);
    }

    private static function notifyCreator(Page $page, bool $published, bool $failed): void
    {
        $creatorId = data_get($page->ai_metadata, 'created_by');
        $creator = $creatorId ? User::find($creatorId) : null;
        if (!$creator) {
            return;
        }

        $notification = Notification::make()
            ->title($failed ? 'AI page generation failed' : ($published ? 'AI page generated and published' : 'AI page draft is ready'))
            ->body($failed ? 'Open Dynamic Pages to inspect the error and retry.' : 'Open Dynamic Pages to review and edit the generated sections.');
        $failed ? $notification->danger() : $notification->success();
        $notification->sendToDatabase($creator);
    }
}

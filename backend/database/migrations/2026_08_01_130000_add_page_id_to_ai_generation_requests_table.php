<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ai_generation_requests', function (Blueprint $table): void {
            $table->foreignId('page_id')
                ->nullable()
                ->after('category_id')
                ->constrained('pages')
                ->nullOnDelete();
        });

        // Make previously generated AI pages visible in Generation History too.
        DB::table('pages')
            ->where('ai_generated', true)
            ->orderBy('id')
            ->get()
            ->each(function (object $page): void {
                if (DB::table('ai_generation_requests')->where('page_id', $page->id)->exists()) {
                    return;
                }

                $metadata = json_decode((string) ($page->ai_metadata ?? '{}'), true);
                $metadata = is_array($metadata) ? $metadata : [];
                $title = json_decode((string) ($page->title ?? '{}'), true);
                $title = is_array($title) ? $title : [];
                $status = match ((string) ($page->ai_status ?? '')) {
                    'approved', 'pending_review' => 'completed',
                    'failed' => 'failed',
                    'generating' => 'generating',
                    default => 'pending',
                };
                $completed = $status === 'completed' ? 1 : 0;
                $failed = $status === 'failed' ? 1 : 0;

                $requestId = DB::table('ai_generation_requests')->insertGetId([
                    'created_by' => $metadata['created_by'] ?? null,
                    'provider' => $metadata['provider'] ?? 'unknown',
                    'model' => $metadata['model'] ?? 'unknown',
                    'generation_mode' => 'ai_page',
                    'idempotency_key' => 'ai-page-' . $page->id,
                    'request_hash' => hash('sha256', 'ai-page:' . $page->id),
                    'settings_snapshot' => json_encode([
                        'page_id' => $page->id,
                        'brief' => $metadata['brief'] ?? [],
                    ]),
                    'main_keyword' => $title['en'] ?? $title['ar'] ?? ('AI Page #' . $page->id),
                    'secondary_keywords' => json_encode([]),
                    'language' => 'both',
                    'article_count' => 1,
                    'category_id' => null,
                    'page_id' => $page->id,
                    'author_id' => null,
                    'status' => $status,
                    'total_items' => 1,
                    'completed_items' => $completed,
                    'failed_items' => $failed,
                    'error_message' => $metadata['error'] ?? null,
                    'started_at' => $metadata['started_at'] ?? $page->created_at,
                    'completed_at' => $status === 'completed' ? ($metadata['completed_at'] ?? $page->updated_at) : null,
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                ]);

                $metadata['generation_request_id'] = $requestId;
                DB::table('pages')->where('id', $page->id)->update([
                    'ai_metadata' => json_encode($metadata),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('ai_generation_requests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('page_id');
        });
    }
};

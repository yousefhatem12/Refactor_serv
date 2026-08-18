<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes for the filters and ordering used by the public API,
     * Filament lists, the database queue, and the AI dashboard.
     */
    public function up(): void
    {
        foreach ($this->indexes() as [$table, $columns, $name]) {
            $this->addIndex($table, $columns, $name);
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->indexes()) as [$table, $columns, $name]) {
            if (Schema::hasTable($table) && Schema::hasIndex($table, $name)) {
                Schema::table($table, function (Blueprint $blueprint) use ($name): void {
                    $blueprint->dropIndex($name);
                });
            }
        }
    }

    /**
     * @return array<int, array{0: string, 1: array<int, string>, 2: string}>
     */
    private function indexes(): array
    {
        return [
            // Public content feeds.
            ['posts', ['is_published', 'created_at'], 'posts_published_created_at_idx'],
            ['posts', ['is_published', 'is_trending', 'created_at'], 'posts_published_trending_created_at_idx'],
            ['posts', ['category'], 'posts_category_idx'],
            ['pages', ['is_published', 'updated_at'], 'pages_published_updated_at_idx'],
            ['products', ['is_active', 'id'], 'products_active_id_idx'],
            ['careers', ['is_active', 'id'], 'careers_active_id_idx'],
            ['careers', ['is_active', 'department', 'id'], 'careers_active_department_id_idx'],
            ['faqs', ['is_active', 'id'], 'faqs_active_id_idx'],
            ['projects', ['category', 'id'], 'projects_category_id_idx'],
            ['projects', ['is_featured', 'id'], 'projects_featured_id_idx'],

            // Navigation and shared settings.
            ['nav_links', ['parent_id', 'is_active', 'sort_order'], 'nav_links_parent_active_sort_idx'],
            ['footer_links', ['sort_order'], 'footer_links_sort_order_idx'],
            ['pricing_plans', ['service_id', 'is_active', 'sort_order'], 'pricing_service_active_sort_idx'],
            ['pricing_plans', ['is_active', 'sort_order'], 'pricing_active_sort_idx'],
            ['bank_accounts', ['country', 'sort_order'], 'bank_accounts_country_sort_idx'],
            ['bank_accounts', ['sort_order'], 'bank_accounts_sort_idx'],
            ['services', ['parent_id'], 'services_parent_id_idx'],

            // Queue and admin tables.
            ['jobs', ['queue', 'reserved_at', 'available_at'], 'jobs_queue_reserved_available_idx'],
            ['contact_messages', ['type', 'created_at'], 'contact_messages_type_created_idx'],
            ['contact_messages', ['status', 'created_at'], 'contact_messages_status_created_idx'],
            ['project_consultations', ['status', 'created_at'], 'consultations_status_created_idx'],
            ['career_applications', ['status', 'created_at'], 'career_apps_status_created_idx'],
            ['quotations', ['status', 'created_at'], 'quotations_status_created_idx'],
            ['notifications', ['notifiable_type', 'notifiable_id', 'read_at'], 'notifications_notifiable_read_idx'],
            ['users', ['phone'], 'users_phone_idx'],

            // AI dashboard and worker lookups. These are skipped until the AI
            // migrations have created the tables.
            ['ai_generation_requests', ['request_hash', 'status'], 'ai_requests_hash_status_idx'],
            ['ai_generation_requests', ['status', 'created_at'], 'ai_requests_status_created_idx'],
            ['ai_generation_requests', ['generation_mode', 'created_at'], 'ai_requests_mode_created_idx'],
            ['ai_generation_items', ['request_id', 'status'], 'ai_items_request_status_idx'],
        ];
    }

    /**
     * Add an index only when the target schema contains every requested
     * column. This keeps the deployment safe across older production copies.
     *
     * @param array<int, string> $columns
     */
    private function addIndex(string $table, array $columns, string $name): void
    {
        if (! Schema::hasTable($table) || Schema::hasIndex($table, $name)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return;
            }
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $name): void {
            $blueprint->index($columns, $name);
        });
    }
};

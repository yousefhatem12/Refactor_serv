<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $toDrop = [
            ['posts', 'posts_slug_idx'],
            ['projects', 'projects_slug_idx'],
            ['blog_categories', 'blog_categories_slug_idx'],
            ['project_categories', 'project_categories_slug_idx'],
            ['careers', 'careers_slug_idx'],
            ['services', 'services_slug_active_idx'],
            ['products', 'products_slug_active_idx'],
            ['pages', 'pages_slug_published_idx'],
        ];

        foreach ($toDrop as [$table, $indexName]) {
            if (Schema::hasTable($table) && Schema::hasIndex($table, $indexName)) {
                Schema::table($table, function (Blueprint $bp) use ($indexName): void {
                    $bp->dropIndex($indexName);
                });
            }
        }

        $toAdd = [
            ['services', ['is_active', 'sort_order'], 'services_active_sort_idx'],
            ['services', ['is_active'], 'services_active_idx'],
            ['products', ['is_active', 'sort_order'], 'products_active_sort_idx'],
            ['pages', ['is_published'], 'pages_published_idx'],
            ['pages', ['is_active'], 'pages_active_idx'],
            ['social_links', ['is_active'], 'social_links_active_idx'],
            ['contact_messages', ['created_at'], 'contact_messages_created_at_idx'],
            ['quotations', ['status', 'created_at'], 'quotations_status_created_idx'],
            ['project_consultations', ['status', 'created_at'], 'consultations_status_created_idx'],
            ['ai_credentials', ['is_active', 'provider'], 'ai_credentials_active_provider_idx'],
        ];

        foreach ($toAdd as [$table, $columns, $indexName]) {
            $this->addIndex($table, $columns, $indexName);
        }
    }

    public function down(): void
    {
        $toDrop = [
            ['services', 'services_active_sort_idx'],
            ['services', 'services_active_idx'],
            ['products', 'products_active_sort_idx'],
            ['pages', 'pages_published_idx'],
            ['pages', 'pages_active_idx'],
            ['social_links', 'social_links_active_idx'],
            ['contact_messages', 'contact_messages_created_at_idx'],
            ['ai_credentials', 'ai_credentials_active_provider_idx'],
        ];

        foreach ($toDrop as [$table, $indexName]) {
            if (Schema::hasTable($table) && Schema::hasIndex($table, $indexName)) {
                Schema::table($table, function (Blueprint $bp) use ($indexName): void {
                    $bp->dropIndex($indexName);
                });
            }
        }
    }

    /**
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

        Schema::table($table, function (Blueprint $bp) use ($columns, $name): void {
            $bp->index($columns, $name);
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds indexes for slug-based lookups and boolean filter columns that were
 * missing from the first performance migration. Each index is guarded with
 * hasIndex() / hasColumn() checks so it is safe to run against any schema
 * version.
 */
return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            // Slug lookups (used in show() endpoints).
            ['posts',            ['slug'],              'posts_slug_idx'],
            ['projects',         ['slug'],              'projects_slug_idx'],
            ['services',         ['slug', 'is_active'], 'services_slug_active_idx'],
            ['products',         ['slug', 'is_active'], 'products_slug_active_idx'],
            ['pages',            ['slug', 'is_published'], 'pages_slug_published_idx'],
            ['blog_categories',  ['slug'],              'blog_categories_slug_idx'],
            ['project_categories',['slug'],             'project_categories_slug_idx'],
            ['careers',          ['slug'],              'careers_slug_idx'],

            // Boolean filter columns used in public content listings.
            ['testimonials',  ['is_active'],            'testimonials_active_idx'],
            ['team_members',  ['is_active'],            'team_members_active_idx'],
            ['features',      ['is_active'],            'features_active_idx'],
            ['nav_links',     ['is_active'],            'nav_links_active_idx'],
        ];

        foreach ($indexes as [$table, $columns, $name]) {
            $this->addIndex($table, $columns, $name);
        }
    }

    public function down(): void
    {
        $indexes = [
            ['posts',            'posts_slug_idx'],
            ['projects',         'projects_slug_idx'],
            ['services',         'services_slug_active_idx'],
            ['products',         'products_slug_active_idx'],
            ['pages',            'pages_slug_published_idx'],
            ['blog_categories',  'blog_categories_slug_idx'],
            ['project_categories','project_categories_slug_idx'],
            ['careers',          'careers_slug_idx'],
            ['testimonials',     'testimonials_active_idx'],
            ['team_members',     'team_members_active_idx'],
            ['features',         'features_active_idx'],
            ['nav_links',        'nav_links_active_idx'],
        ];

        foreach ($indexes as [$table, $name]) {
            if (Schema::hasTable($table) && Schema::hasIndex($table, $name)) {
                Schema::table($table, fn (Blueprint $bp) => $bp->dropIndex($name));
            }
        }
    }

    /** @param string[] $columns */
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
        Schema::table($table, fn (Blueprint $bp) => $bp->index($columns, $name));
    }
};

<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Project::truncate();
        Schema::enableForeignKeyConstraints();

        $wpPortfolioPath = database_path('seeders/data/wp_portfolio.json');
        if (! file_exists($wpPortfolioPath)) {
            return;
        }

        $projects = json_decode((string) file_get_contents($wpPortfolioPath), true);
        if (! is_array($projects) || empty($projects)) {
            return;
        }

        $now = now()->toDateTimeString();
        $rows = [];

        foreach ($projects as $p) {
            $rows[] = [
                'type' => $p['type'] ?? 'portfolio',
                'slug' => $p['slug'],
                'name' => json_encode($p['name'], JSON_UNESCAPED_UNICODE),
                'category' => $p['category'] ?? 'websites',
                'description' => json_encode($p['description'], JSON_UNESCAPED_UNICODE),
                'image' => $p['image'] ?? 'projects/image-1.webp',
                'client' => $p['client'] ?? '',
                'url' => $p['url'] ?? '#',
                'tags' => json_encode($p['tags'] ?? [], JSON_UNESCAPED_UNICODE),
                'is_featured' => ! empty($p['is_featured']),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('projects')->insert($chunk);
        }
    }
}

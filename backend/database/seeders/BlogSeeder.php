<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Post::truncate();
        BlogCategory::truncate();
        Schema::enableForeignKeyConstraints();

        $author = User::first();
        $authorId = $author ? $author->id : 1;

        $categories = [
            'design' => [
                'slug' => 'design',
                'name' => ['en' => 'Web Design & UI/UX', 'ar' => 'تصميم المواقع والموشن جرافيك'],
            ],
            'development' => [
                'slug' => 'development',
                'name' => ['en' => 'Web & Mobile Programming', 'ar' => 'برمجة المواقع وتطبيقات الجوال'],
            ],
            'marketing' => [
                'slug' => 'marketing',
                'name' => ['en' => 'Digital Marketing & SEO', 'ar' => 'التسويق الإلكتروني والسيو'],
            ],
            'technology' => [
                'slug' => 'technology',
                'name' => ['en' => 'Web Hosting & Technology', 'ar' => 'استضافة المواقع والتقنية'],
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $key => $catData) {
            $categoryModels[$key] = BlogCategory::create($catData);
        }

        $wpPostsPath = database_path('seeders/data/wp_posts.json');
        if (! file_exists($wpPostsPath)) {
            return;
        }

        $posts = json_decode((string) file_get_contents($wpPostsPath), true);
        if (! is_array($posts) || empty($posts)) {
            return;
        }

        $rows = [];
        foreach ($posts as $post) {
            $catKey = $post['category'] ?? 'design';
            $catModel = $categoryModels[$catKey] ?? $categoryModels['design'];
            $date = $post['created_at'] ?? '2024-01-01 12:00:00';

            $rows[] = [
                'title' => json_encode($post['title'], JSON_UNESCAPED_UNICODE),
                'slug' => $post['slug'],
                'content' => json_encode($post['content'], JSON_UNESCAPED_UNICODE),
                'image' => $post['image'] ?? 'posts/Link → post-landscape-1.jpg.webp',
                'blog_category_id' => $catModel->id,
                'category' => $catKey,
                'is_published' => true,
                'is_trending' => ! empty($post['is_trending']),
                'author_id' => $authorId,
                'seo_title' => json_encode($post['seo_title'], JSON_UNESCAPED_UNICODE),
                'seo_description' => json_encode($post['seo_description'], JSON_UNESCAPED_UNICODE),
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }

        foreach (array_chunk($rows, 10) as $chunk) {
            DB::table('posts')->insert($chunk);
        }
    }
}

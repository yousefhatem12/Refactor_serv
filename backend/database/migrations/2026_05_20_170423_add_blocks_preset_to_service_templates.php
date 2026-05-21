<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_templates', function (Blueprint $table) {
            $table->json('blocks_preset')->nullable()->after('default_hero_image');
        });

        // Seed presets from template-one/two/three
        // template-one: SocialMediaHero + Features + Portfolio + HowWeWork + Offers + Contact + Blogs
        \DB::table('service_templates')->where('slug', 'template-one')->update([
            'blocks_preset' => json_encode([
                ['type' => 'hero', 'data' => []],
                ['type' => 'features', 'data' => []],
                ['type' => 'portfolio', 'data' => ['section_title' => ['en' => '', 'ar' => '']]],
                ['type' => 'how_we_work_timeline', 'data' => []],
                ['type' => 'hosting_offers', 'data' => []],
                ['type' => 'contact_request', 'data' => ['style' => 'social_media']],
                ['type' => 'blogs', 'data' => []],
            ]),
        ]);

        // template-two: SocialMediaHero + Features + Portfolio(mobile) + HowWeWork(mobile) + Request + Blogs
        \DB::table('service_templates')->where('slug', 'template-two')->update([
            'blocks_preset' => json_encode([
                ['type' => 'hero', 'data' => []],
                ['type' => 'features', 'data' => []],
                ['type' => 'portfolio', 'data' => ['is_mobile_app' => true, 'section_title' => ['en' => '', 'ar' => '']]],
                ['type' => 'how_we_work_mobile', 'data' => []],
                ['type' => 'contact_request', 'data' => ['style' => 'web_design']],
                ['type' => 'blogs', 'data' => []],
            ]),
        ]);

        // template-three: WebDevHero + Features + Portfolio + Offers + SplitContent x2 + Blogs
        \DB::table('service_templates')->where('slug', 'template-three')->update([
            'blocks_preset' => json_encode([
                ['type' => 'hero', 'data' => []],
                ['type' => 'features', 'data' => []],
                ['type' => 'portfolio', 'data' => ['section_title' => ['en' => '', 'ar' => '']]],
                ['type' => 'hosting_offers', 'data' => []],
                ['type' => 'split_content', 'data' => ['image_on_right' => true]],
                ['type' => 'split_content', 'data' => ['image_on_right' => false]],
                ['type' => 'blogs', 'data' => []],
            ]),
        ]);
    }

    public function down(): void
    {
        Schema::table('service_templates', function (Blueprint $table) {
            $table->dropColumn('blocks_preset');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('hero_style')->default('social_media_hero'); // social_media_hero | web_dev_hero
            $table->string('default_hero_image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed the 3 built-in templates
        \DB::table('service_templates')->insert([
            [
                'name' => 'Template One',
                'slug' => 'template-one',
                'hero_style' => 'social_media_hero',
                'default_hero_image' => '/template/template-one-hero.png',
                'description' => 'Marketing / SEO style — SocialMedia Hero with custom background',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Template Two',
                'slug' => 'template-two',
                'hero_style' => 'social_media_hero',
                'default_hero_image' => '/template/template-two-hero.png',
                'description' => 'App Development style — SocialMedia Hero with custom background',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Template Three',
                'slug' => 'template-three',
                'hero_style' => 'web_dev_hero',
                'default_hero_image' => '/template/template-three-hero.png',
                'description' => 'Corporate / Identity style — WebDev Hero with custom background',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_templates');
    }
};

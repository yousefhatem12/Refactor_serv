<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->timestamps();
        });

        // Insert default categories to keep it working out of the box
        $defaults = [
            ['slug' => 'websites', 'name' => json_encode(['en' => 'Websites', 'ar' => 'مواقع إلكترونية'])],
            ['slug' => 'mobile', 'name' => json_encode(['en' => 'Mobile Apps', 'ar' => 'تطبيقات جوال'])],
            ['slug' => 'motion', 'name' => json_encode(['en' => 'Motions Graphics', 'ar' => 'موشن جرافيكس'])],
            ['slug' => 'digital', 'name' => json_encode(['en' => 'Digital Identity', 'ar' => 'هوية بصرية'])],
            ['slug' => 'social1', 'name' => json_encode(['en' => 'Social Media', 'ar' => 'سوشيال ميديا'])],
        ];

        foreach ($defaults as $data) {
            \Illuminate\Support\Facades\DB::table('project_categories')->insert($data);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_categories');
    }
};

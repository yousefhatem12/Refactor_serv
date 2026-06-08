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
        if (!Schema::hasColumn('projects', 'slug')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('slug')->nullable()->unique()->after('type');
            });
        }

        // Fill existing projects' slugs
        $projects = \Illuminate\Support\Facades\DB::table('projects')->get();
        foreach ($projects as $project) {
            $name = $project->name;
            if (str_starts_with($name, '{')) {
                $decoded = json_decode($name, true);
                $name = $decoded['en'] ?? $decoded['ar'] ?? 'project';
            }
            $slug = \Illuminate\Support\Str::slug($name);
            // Ensure uniqueness
            $originalSlug = $slug;
            $count = 1;
            while (\Illuminate\Support\Facades\DB::table('projects')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            \Illuminate\Support\Facades\DB::table('projects')->where('id', $project->id)->update([
                'slug' => $slug
            ]);
        }

        // Make slug column required
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

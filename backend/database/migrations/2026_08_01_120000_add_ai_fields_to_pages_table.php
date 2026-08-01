<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->boolean('is_published')->default(true)->after('sections');
            $table->boolean('ai_generated')->default(false)->after('is_published');
            $table->string('ai_status')->nullable()->after('ai_generated');
            $table->json('ai_metadata')->nullable()->after('ai_status');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn(['is_published', 'ai_generated', 'ai_status', 'ai_metadata']);
        });
    }
};

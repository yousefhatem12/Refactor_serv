<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('ai_generated')->default(false)->after('is_trending');
            $table->string('ai_status')->nullable()->after('ai_generated'); // pending_review, approved, rejected
            $table->json('ai_metadata')->nullable()->after('ai_status');
            $table->json('image_metadata')->nullable()->after('ai_metadata');
        });
    }

    public function down(): void {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['ai_generated', 'ai_status', 'ai_metadata', 'image_metadata']);
        });
    }
};

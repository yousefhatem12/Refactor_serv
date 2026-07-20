<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ai_generation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('provider');
            $table->string('model');
            $table->string('generation_mode'); // one_per_keyword, topic_cluster
            $table->string('idempotency_key')->unique();
            $table->string('request_hash')->index();
            $table->json('settings_snapshot')->nullable();
            $table->string('main_keyword');
            $table->json('secondary_keywords')->nullable();
            $table->string('language');
            $table->integer('article_count');
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, generating, completed, failed, partially_completed, cancelled
            $table->integer('total_items')->default(0);
            $table->integer('completed_items')->default(0);
            $table->integer('failed_items')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('ai_generation_requests');
    }
};

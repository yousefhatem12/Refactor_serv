<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ai_generation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('ai_generation_requests')->cascadeOnDelete();
            $table->string('keyword');
            $table->string('status')->default('pending'); // pending, generating, completed, failed
            $table->foreignId('post_id')->nullable()->constrained('posts')->nullOnDelete();
            $table->text('error_message')->nullable();
            $table->json('parsed_response')->nullable();
            $table->json('quality_report')->nullable();
            $table->json('usage_metadata')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('ai_generation_items');
    }
};

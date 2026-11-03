<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ai_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->unique();
            $table->text('api_token');
            $table->string('token_hint', 4)->nullable();
            $table->string('default_model')->nullable();
            $table->string('default_language')->nullable();
            $table->integer('default_article_length')->nullable();
            $table->decimal('temperature', 3, 2)->nullable();
            $table->integer('max_articles_per_request')->default(10);
            $table->boolean('is_active')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('token_updated_at')->nullable();
            $table->timestamp('last_tested_at')->nullable();
            $table->string('last_test_status')->nullable();
            $table->text('last_test_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('ai_credentials');
    }
};

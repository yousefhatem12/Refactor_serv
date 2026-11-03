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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('category');
            $table->json('title');
            $table->json('description');
            $table->string('image')->nullable();
            $table->string('demo_link')->nullable();
            $table->string('order_link')->nullable();
            $table->json('features')->nullable();
            $table->json('tech_specs')->nullable();
            $table->json('why_choose_us')->nullable();
            $table->json('samples')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

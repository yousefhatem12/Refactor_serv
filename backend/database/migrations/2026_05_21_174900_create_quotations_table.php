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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('request_type')->nullable();
            $table->string('budget')->nullable();
            $table->string('website_link')->nullable();
            $table->string('content_managers')->nullable();
            $table->string('pre_designed_logo')->nullable();
            $table->string('design_images')->nullable();
            $table->text('vision_goals')->nullable();
            $table->text('product_brief')->nullable();
            $table->text('color_preferences')->nullable();
            $table->text('reference_sites')->nullable();
            $table->text('additional_notes')->nullable();
            $table->json('files')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};

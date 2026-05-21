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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable name
            $table->json('owner'); // Translatable owner name
            $table->json('account'); // Translatable account number/details
            $table->string('image')->nullable(); // Bank logo image path
            $table->string('type')->default('bank'); // bank, wallet, international
            $table->string('country')->default('egypt'); // egypt, ksa
            $table->string('brand_color')->nullable(); // CSS gradient classes
            $table->string('shadow')->nullable(); // CSS box-shadow value
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};

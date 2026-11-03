<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('project_type')->nullable();
            $table->string('has_docs')->nullable();
            $table->string('budget')->nullable();
            $table->string('timeline')->nullable();
            $table->string('decision_maker')->nullable();
            $table->text('references')->nullable();
            $table->string('name');
            $table->string('phone', 50);
            $table->string('email');
            $table->string('company')->nullable();
            $table->string('status')->default('new');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_consultations');
    }
};

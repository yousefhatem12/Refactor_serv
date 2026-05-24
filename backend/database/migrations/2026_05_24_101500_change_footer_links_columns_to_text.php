<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('footer_links', function (Blueprint $table) {
            $table->text('label')->change();
            $table->text('url')->change();
        });
    }

    public function down(): void
    {
        Schema::table('footer_links', function (Blueprint $table) {
            $table->string('label')->change();
            $table->string('url')->change();
        });
    }
};

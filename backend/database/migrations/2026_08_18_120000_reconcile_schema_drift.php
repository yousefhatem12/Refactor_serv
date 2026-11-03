<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair schema differences that are already referenced by the application.
     *
     * This migration is intentionally additive and guarded so it can be run on
     * an existing production database without changing old migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('services') && ! Schema::hasColumn('services', 'parent_id')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('services')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('quotations') && ! Schema::hasColumn('quotations', 'status')) {
            Schema::table('quotations', function (Blueprint $table): void {
                $table->string('status', 32)->default('unread')->after('files');
            });
        }
    }

    /**
     * The migration only reconciles live schemas. Keeping rollback non-
     * destructive prevents removing columns that may have been supplied by a
     * previous/manual deployment on a shared production database.
     */
    public function down(): void
    {
        // Intentionally left empty; this is a safe schema reconciliation.
    }
};

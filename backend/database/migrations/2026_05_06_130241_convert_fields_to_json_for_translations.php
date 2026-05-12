<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'projects' => ['name', 'description'],
            'services' => ['name', 'description'],
            'posts' => ['title', 'content'],
            'faqs' => ['question', 'answer'],
        ];

        foreach ($tables as $table => $columns) {
            foreach ($columns as $column) {
                // 1. Add temp JSON column
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->json($column . '_json')->nullable();
                });

                // 2. Migrate data
                $records = DB::table($table)->get();
                foreach ($records as $record) {
                    $val = $record->{$column};
                    $json = json_encode(['en' => $val, 'ar' => $val]); // Defaulting Arabic to English for now
                    DB::table($table)->where('id', $record->id)->update([$column . '_json' => $json]);
                }

                // 3. Drop old column
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });

                // 4. Rename temp to original
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->renameColumn($column . '_json', $column);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('json_for_translations', function (Blueprint $table) {
            //
        });
    }
};

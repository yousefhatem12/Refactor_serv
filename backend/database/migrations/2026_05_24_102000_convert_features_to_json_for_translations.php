<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->json('title_json')->nullable();
            $table->json('description_json')->nullable();
        });

        $records = DB::table('features')->get();
        foreach ($records as $record) {
            $titleJson = json_encode(['en' => $record->title, 'ar' => $record->title]);
            $descJson = json_encode(['en' => $record->description, 'ar' => $record->description]);
            DB::table('features')->where('id', $record->id)->update([
                'title_json' => $titleJson,
                'description_json' => $descJson
            ]);
        }

        Schema::table('features', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        Schema::table('features', function (Blueprint $table) {
            $table->renameColumn('title_json', 'title');
            $table->renameColumn('description_json', 'description');
        });
    }

    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->string('title_str')->nullable();
            $table->text('description_text')->nullable();
        });

        $records = DB::table('features')->get();
        foreach ($records as $record) {
            $title = '';
            $desc = '';
            if ($record->title) {
                $decodedTitle = json_decode($record->title, true);
                $title = is_array($decodedTitle) ? ($decodedTitle['en'] ?? $decodedTitle['ar'] ?? '') : $record->title;
            }
            if ($record->description) {
                $decodedDesc = json_decode($record->description, true);
                $desc = is_array($decodedDesc) ? ($decodedDesc['en'] ?? $decodedDesc['ar'] ?? '') : $record->description;
            }
            DB::table('features')->where('id', $record->id)->update([
                'title_str' => $title,
                'description_text' => $desc
            ]);
        }

        Schema::table('features', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        Schema::table('features', function (Blueprint $table) {
            $table->renameColumn('title_str', 'title');
            $table->renameColumn('description_text', 'description');
        });
    }
};

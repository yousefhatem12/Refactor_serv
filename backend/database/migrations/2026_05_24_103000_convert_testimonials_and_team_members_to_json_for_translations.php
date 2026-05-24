<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Testimonials Table
        Schema::table('testimonials', function (Blueprint $table) {
            $table->json('client_name_json')->nullable();
            $table->json('position_json')->nullable();
            $table->json('review_json')->nullable();
        });

        $testimonials = DB::table('testimonials')->get();
        foreach ($testimonials as $t) {
            $nameJson = json_encode(['en' => $t->client_name, 'ar' => $t->client_name]);
            $posJson = json_encode(['en' => $t->position, 'ar' => $t->position]);
            $revJson = json_encode(['en' => $t->review, 'ar' => $t->review]);
            DB::table('testimonials')->where('id', $t->id)->update([
                'client_name_json' => $nameJson,
                'position_json' => $posJson,
                'review_json' => $revJson,
            ]);
        }

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'position', 'review']);
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->renameColumn('client_name_json', 'client_name');
            $table->renameColumn('position_json', 'position');
            $table->renameColumn('review_json', 'review');
        });

        // 2. Team Members Table
        Schema::table('team_members', function (Blueprint $table) {
            $table->json('name_json')->nullable();
            $table->json('role_json')->nullable();
            $table->json('bio_json')->nullable();
        });

        $members = DB::table('team_members')->get();
        foreach ($members as $m) {
            $nameJson = json_encode(['en' => $m->name, 'ar' => $m->name]);
            $roleJson = json_encode(['en' => $m->role, 'ar' => $m->role]);
            $bioJson = json_encode(['en' => $m->bio, 'ar' => $m->bio]);
            DB::table('team_members')->where('id', $m->id)->update([
                'name_json' => $nameJson,
                'role_json' => $roleJson,
                'bio_json' => $bioJson,
            ]);
        }

        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['name', 'role', 'bio']);
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->renameColumn('name_json', 'name');
            $table->renameColumn('role_json', 'role');
            $table->renameColumn('bio_json', 'bio');
        });
    }

    public function down(): void
    {
        // 1. Testimonials Table
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('client_name_str')->nullable();
            $table->string('position_str')->nullable();
            $table->text('review_text')->nullable();
        });

        $testimonials = DB::table('testimonials')->get();
        foreach ($testimonials as $t) {
            $name = '';
            $pos = '';
            $rev = '';
            if ($t->client_name) {
                $decoded = json_decode($t->client_name, true);
                $name = is_array($decoded) ? ($decoded['en'] ?? $decoded['ar'] ?? '') : $t->client_name;
            }
            if ($t->position) {
                $decoded = json_decode($t->position, true);
                $pos = is_array($decoded) ? ($decoded['en'] ?? $decoded['ar'] ?? '') : $t->position;
            }
            if ($t->review) {
                $decoded = json_decode($t->review, true);
                $rev = is_array($decoded) ? ($decoded['en'] ?? $decoded['ar'] ?? '') : $t->review;
            }
            DB::table('testimonials')->where('id', $t->id)->update([
                'client_name_str' => $name,
                'position_str' => $pos,
                'review_text' => $rev,
            ]);
        }

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['client_name', 'position', 'review']);
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->renameColumn('client_name_str', 'client_name');
            $table->renameColumn('position_str', 'position');
            $table->renameColumn('review_text', 'review');
        });

        // 2. Team Members Table
        Schema::table('team_members', function (Blueprint $table) {
            $table->string('name_str')->nullable();
            $table->string('role_str')->nullable();
            $table->text('bio_text')->nullable();
        });

        $members = DB::table('team_members')->get();
        foreach ($members as $m) {
            $name = '';
            $role = '';
            $bio = '';
            if ($m->name) {
                $decoded = json_decode($m->name, true);
                $name = is_array($decoded) ? ($decoded['en'] ?? $decoded['ar'] ?? '') : $m->name;
            }
            if ($m->role) {
                $decoded = json_decode($m->role, true);
                $role = is_array($decoded) ? ($decoded['en'] ?? $decoded['ar'] ?? '') : $m->role;
            }
            if ($m->bio) {
                $decoded = json_decode($m->bio, true);
                $bio = is_array($decoded) ? ($decoded['en'] ?? $decoded['ar'] ?? '') : $m->bio;
            }
            DB::table('team_members')->where('id', $m->id)->update([
                'name_str' => $name,
                'role_str' => $role,
                'bio_text' => $bio,
            ]);
        }

        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['name', 'role', 'bio']);
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->renameColumn('name_str', 'name');
            $table->renameColumn('role_str', 'role');
            $table->renameColumn('bio_str', 'bio');
        });
    }
};

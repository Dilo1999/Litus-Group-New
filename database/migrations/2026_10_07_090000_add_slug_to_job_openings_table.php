<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_openings', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        // Backfill unique slugs for existing openings (each job gets its own /careers/{slug} page).
        $used = [];
        foreach (DB::table('job_openings')->orderBy('id')->get(['id', 'title']) as $job) {
            $base = Str::slug($job->title) ?: 'job';
            $slug = $base;
            for ($i = 2; in_array($slug, $used, true); $i++) {
                $slug = $base.'-'.$i;
            }
            $used[] = $slug;
            DB::table('job_openings')->where('id', $job->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('job_openings', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};

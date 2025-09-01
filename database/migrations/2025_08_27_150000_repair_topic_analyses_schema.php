<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // If the table doesn't exist at all, create it with the correct schema.
        if (!Schema::hasTable('topic_analyses')) {
            Schema::create('topic_analyses', function (Blueprint $table) {
                $table->id();
                $table->json('urls_list');                 // original URLs (array)
                $table->string('urls_signature')->nullable(); // signature for caching
                $table->json('analysis_result')->nullable();  // clusters JSON from OpenAI
                $table->json('openai_metadata')->nullable();  // model/usage/id/etc
                $table->timestamps();
                $table->index('urls_signature', 'topic_analyses_urls_signature_index');
            });
            return;
        }

        // Otherwise, add any missing columns without relying on "after" clauses.
        Schema::table('topic_analyses', function (Blueprint $table) {
            if (!Schema::hasColumn('topic_analyses', 'urls_list')) {
                $table->json('urls_list')->nullable();
            }
            if (!Schema::hasColumn('topic_analyses', 'analysis_result')) {
                $table->json('analysis_result')->nullable();
            }
            if (!Schema::hasColumn('topic_analyses', 'openai_metadata')) {
                $table->json('openai_metadata')->nullable();
            }
            if (!Schema::hasColumn('topic_analyses', 'urls_signature')) {
                $table->string('urls_signature')->nullable();
            }
        });

        // Ensure the index exists (ignore if it already does).
        try {
            Schema::table('topic_analyses', function (Blueprint $table) {
                $table->index('urls_signature', 'topic_analyses_urls_signature_index');
            });
        } catch (\Throwable $e) {
            // Index probably exists already; ignore.
        }

        // Backfill signature for existing rows that have urls_list but no signature yet.
        try {
            $rows = DB::table('topic_analyses')->select('id', 'urls_list', 'urls_signature')->get();
            foreach ($rows as $row) {
                if (!empty($row->urls_signature)) { continue; }
                $arr = json_decode($row->urls_list ?? '[]', true);
                if (!is_array($arr)) { $arr = []; }
                $arr = array_values(array_unique(array_filter(array_map('trim', $arr))));
                sort($arr, SORT_NATURAL | SORT_FLAG_CASE);
                $sig = sha1(json_encode($arr, JSON_UNESCAPED_SLASHES));
                DB::table('topic_analyses')->where('id', $row->id)->update(['urls_signature' => $sig]);
            }
        } catch (\Throwable $e) {
            // If anything goes wrong, leave existing rows as-is.
        }
    }

    public function down(): void
    {
        // This is a repair migration; we won't drop the entire table.
        // If you really need to rollback columns, do it carefully:
        try {
            if (Schema::hasColumn('topic_analyses', 'urls_signature')) {
                Schema::table('topic_analyses', function (Blueprint $table) {
                    try { $table->dropIndex('topic_analyses_urls_signature_index'); } catch (\Throwable $e) {}
                    $table->dropColumn('urls_signature');
                });
            }
        } catch (\Throwable $e) {
            // Ignore rollback errors.
        }
    }
};

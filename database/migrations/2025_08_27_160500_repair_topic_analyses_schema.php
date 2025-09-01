<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Create full table if missing
        if (!Schema::hasTable('topic_analyses')) {
            Schema::create('topic_analyses', function (Blueprint $table) {
                $table->id();
                $table->json('urls_list');
                $table->string('urls_signature')->nullable();
                $table->json('analysis_result')->nullable();
                $table->json('openai_metadata')->nullable();
                $table->timestamps();
                $table->index('urls_signature', 'topic_analyses_urls_signature_index');
            });
            return;
        }

        // Add any missing columns
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

        // Ensure index (ignore if exists)
        try {
            Schema::table('topic_analyses', function (Blueprint $table) {
                $table->index('urls_signature', 'topic_analyses_urls_signature_index');
            });
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // Non-destructive rollback for safety
        try {
            if (Schema::hasColumn('topic_analyses', 'urls_signature')) {
                Schema::table('topic_analyses', function (Blueprint $table) {
                    try { $table->dropIndex('topic_analyses_urls_signature_index'); } catch (\Throwable $e) {}
                    $table->dropColumn('urls_signature');
                });
            }
        } catch (\Throwable $e) {}
    }
};

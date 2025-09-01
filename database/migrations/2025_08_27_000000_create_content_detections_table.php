<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('content_detections', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Input & source
            $table->longText('input_text')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->string('source_type', 32)->default('text');
            $table->string('language', 12)->nullable();

            // Dedupe
            $table->string('content_hash', 64)->index();

            // Remote model scores
            $table->decimal('score_roberta_openai', 5, 4)->nullable();
            $table->decimal('score_dialogpt_detector', 5, 4)->nullable();
            $table->decimal('score_hello_simpleai_roberta', 5, 4)->nullable();
            $table->decimal('score_gpt2_detector', 5, 4)->nullable();

            // Local analysis
            $table->json('stats')->nullable();
            $table->json('complexity')->nullable();
            $table->json('stylometry')->nullable();
            $table->json('features')->nullable();

            // Aggregation
            $table->decimal('ensemble_score', 5, 4)->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->string('label', 32)->nullable();

            // Details & errors
            $table->json('model_breakdown')->nullable();
            $table->json('errors')->nullable();

            // Meta
            $table->string('status', 24)->default('completed');
            $table->unsignedSmallInteger('version')->default(1);
            $table->timestamp('processed_at')->nullable();

            // Perf & audit
            $table->unsignedInteger('hf_api_calls')->default(0);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->ipAddress('request_ip')->nullable();
            $table->string('user_agent', 512)->nullable();

            // Timestamps / soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['status', 'processed_at']);
            $table->index('ensemble_score');
            $table->index('label');
            $table->index('user_id');
            $table->unique(['content_hash', 'language', 'version'], 'uniq_content_lang_version');
        });
    }

    public function down(): void {
        Schema::dropIfExists('content_detections');
    }
};

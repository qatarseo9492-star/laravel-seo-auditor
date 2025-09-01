<?php use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema; return new class extends 
Migration {
    public function up(): void { if (!Schema::hasTable('topic_analyses')) return; Schema::table('topic_analyses', function (Blueprint $table) { if 
            (Schema::hasColumn('topic_analyses', 'urls_signature')) {
                return; // already added
            }
            if (Schema::hasColumn('topic_analyses', 'urls_list')) { $table->string('urls_signature', 255)->nullable()->after('urls_list');
            } else {
                $table->string('urls_signature', 255)->nullable();
            }
        });
    }
    public function down(): void { if (!Schema::hasTable('topic_analyses')) return; Schema::table('topic_analyses', function (Blueprint $table) { if 
            (Schema::hasColumn('topic_analyses', 'urls_signature')) {
                $table->dropColumn('urls_signature');
            }
        });
    }
};

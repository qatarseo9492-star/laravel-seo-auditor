<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar_path')) {
                // Place after email if it exists; otherwise just add the column
                if (Schema::hasColumn('users', 'email')) {
                    $table->string('avatar_path', 255)->nullable()->after('email');
                } else {
                    $table->string('avatar_path', 255)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'avatar_path')) {
                $table->dropColumn('avatar_path');
            }
        });
    }
};





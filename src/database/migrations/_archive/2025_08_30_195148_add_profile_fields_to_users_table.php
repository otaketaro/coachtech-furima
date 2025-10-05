<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar_path')) {
                $table->string('avatar_path', 255)->nullable()->after('password');
            }
            if (! Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->after('avatar_path');
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->string('address', 255)->nullable()->after('postal_code');
            }
            if (! Schema::hasColumn('users', 'building')) {
                $table->string('building', 255)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 既に存在しない場合のdropを避けるため個別チェック
            if (Schema::hasColumn('users', 'avatar_path')) {
                $table->dropColumn('avatar_path');
            }
            if (Schema::hasColumn('users', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('users', 'building')) {
                $table->dropColumn('building');
            }
        });
    }
};

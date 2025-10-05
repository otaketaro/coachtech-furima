<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // 基本
            $table->string('name');                // バリデーションはアプリ側で20文字制限
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // プロフィール列（テストで参照・更新）
            $table->string('avatar_path', 255)->nullable();
            $table->string('postal_code', 16)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('building', 255)->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

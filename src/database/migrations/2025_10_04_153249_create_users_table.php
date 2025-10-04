<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email")->unique();
            $table->timestamp("email_verified_at")->nullable();
            $table->string("password");

            // 2FA
            $table->text("two_factor_secret")->nullable();
            $table->text("two_factor_recovery_codes")->nullable();
            $table->timestamp("two_factor_confirmed_at")->nullable();

            // プロフィール列（両対応）
            $table->string("image_path")->nullable();     // 旧実装互換
            $table->string("avatar_path", 255)->nullable(); // 新実装互換
            $table->string("postal_code", 16)->nullable();
            $table->string("address", 255)->nullable();
            $table->string("building", 255)->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("users");
    }
};

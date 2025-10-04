<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('likes', function (Blueprint $table) {
            // 複合ユニーク（名前を明示）
            $table->unique(['user_id', 'item_id'], 'likes_user_item_unique');
        });
    }

    public function down(): void
    {
        // 外部キー制約を一時無効化（MySQLでindexを安全に落とすため）
        Schema::disableForeignKeyConstraints();

        Schema::table('likes', function (Blueprint $table) {
            // 複合ユニークを名前でdrop（列配列指定より確実）
            $table->dropUnique('likes_user_item_unique');

            // 保険：外部キーが要求する索引要件を満たすよう個別indexを付与
            // （既に存在する場合はDBが無視するので安全）
            $table->index('user_id');
            $table->index('item_id');
        });

        Schema::enableForeignKeyConstraints();
    }
};

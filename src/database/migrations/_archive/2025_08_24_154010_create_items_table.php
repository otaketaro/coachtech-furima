<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // 出品者（users.id 参照）
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 基本情報
            $table->string('title');
            $table->string('brand')->nullable();
            $table->text('description');

            // 価格・状態など
            $table->unsignedInteger('price');
            $table->string('condition', 32); // 例: good / fair / bad（表示名はアプリ側で扱う）
            $table->enum('status', ['selling', 'sold'])->default('selling');

            // 画像
            $table->string('image_path'); // storage/app/public に保存する想定

            $table->timestamps();

            // よく使う組み合わせにインデックス
            $table->index(['status', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
}

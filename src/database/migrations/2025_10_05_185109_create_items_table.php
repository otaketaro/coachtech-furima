<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            // 出品者
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // 基本情報
            $table->string('title');
            $table->string('brand')->nullable();
            $table->text('description');

            // 価格・状態
            $table->unsignedInteger('price');
            $table->string('condition', 32);
            $table->enum('status', ['selling', 'sold'])->default('selling');

            // 画像
            $table->string('image_path');

            $table->timestamps();

            // 検索用
            $table->index(['status', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

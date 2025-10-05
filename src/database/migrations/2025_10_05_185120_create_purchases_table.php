<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            // 1商品=1購入
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();

            $table->enum('payment_method', ['convenience', 'card']);

            // 価格・状態（後付け分を最初から反映）
            $table->unsignedInteger('price');
            $table->enum('status', ['trading', 'completed'])->default('completed');

            // 配送先スナップショット
            $table->string('shipping_postal_code', 16);
            $table->string('shipping_address');
            $table->string('shipping_building')->nullable();

            $table->timestamps();

            $table->unique('item_id');
            $table->index(['buyer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

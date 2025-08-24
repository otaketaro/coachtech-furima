<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            // 1商品=1購入（sold判定で利用）
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            // 購入者
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();

            // 決済方法
            $table->enum('payment_method', ['convenience', 'card']);

            // 購入時点の配送先スナップショット
            $table->string('shipping_postal_code', 16);
            $table->string('shipping_address');
            $table->string('shipping_building')->nullable();

            $table->timestamps();

            // 1商品につき1レコード
            $table->unique('item_id');
            $table->index(['buyer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
}

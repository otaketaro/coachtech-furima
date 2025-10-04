<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("items", function (Blueprint $table) {
            $table->id();

            $table->foreignId("user_id")->constrained()->cascadeOnDelete();

            $table->string("title");
            $table->string("brand")->nullable();
            $table->text("description");

            $table->unsignedInteger("price");
            $table->string("condition", 32);
            $table->enum("status", ["selling", "sold"])->default("selling");

            $table->string("image_path");

            $table->timestamps();

            $table->index(["status", "price"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("items");
    }
};

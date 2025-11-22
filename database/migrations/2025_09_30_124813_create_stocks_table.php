<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')
            ->constrained();
            $table->foreignId('hinban_id')
            ->constrained();
            $table->bigInteger('pcs');
            $table->bigInteger('zaikogaku');
            $table->timestamps();
            // インデックス設定
            $table->index('shop_id', 'idx_shop_id');
            $table->index('hinban_id', 'idx_hinban_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};

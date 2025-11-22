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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->date('sales_date');
            $table->foreignId('shop_id')
            ->constrained();
            $table->foreignId('hinban_id')
            ->constrained();
            $table->bigInteger('pcs');
            $table->integer('tanka');
            $table->bigInteger('kingaku');
            $table->bigInteger('arari');
            $table->integer('YM');
            $table->integer('YW');
            $table->integer('YMD');
            $table->integer('Y');
            $table->timestamps();
            // インデックス設定
            $table->index('shop_id', 'idx_shop_id');
            $table->index('hinban_id', 'idx_hinban_id');
            $table->index('sales_date', 'idx_sales_date');                   // 期間検索
            $table->index(['sales_date', 'hinban_id'], 'idx_date_hinban');   // ランキング用
            $table->index(['shop_id', 'sales_date'], 'idx_shop_date');       // 店舗集計用
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

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
            $table->foreignId('sku_id')
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

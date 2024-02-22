<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderBahanBakuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 145)->nullable();
            $table->bigInteger('chiller_id')->unsigned()->nullable();
            $table->bigInteger('chiller_out')->unsigned()->nullable();
            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->bigInteger('order_item_id')->unsigned()->nullable();
            $table->string('type', 145)->nullable();
            $table->string('proses_ambil', 145)->nullable();
            $table->json('data_chiller')->nullable();
            $table->json('data_order')->nullable();
            $table->json('data_order_item')->nullable();
            $table->integer('bb_item')->nullable();
            $table->double('bb_berat')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('key', 256)->nullable();
            $table->bigInteger('status')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_bahan_baku');
    }
}

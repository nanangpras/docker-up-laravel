<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable()->unsigned();
            $table->bigInteger('item_id')->nullable()->unsigned();
            $table->string('nama_detail', 255)->nullable();
            $table->string('partner', 255)->nullable();
            $table->text('alamat_kirim')->nullable();
            $table->string('wilayah', 255)->nullable();
            $table->string('no_so', 255)->nullable();
            $table->string('part', 255)->nullable();
            $table->string('bumbu', 255)->nullable();
            $table->string('memo', 255)->nullable();
            $table->string('unit', 255)->nullable();
            $table->double('rate', 255)->nullable();
            $table->string('sku', 255)->nullable();
            $table->integer('potong')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('kode', 145)->nullable();
            $table->integer('qty')->nullable();
            $table->integer('fulfillment_qty')->nullable();
            $table->double('berat')->nullable();
            $table->double('fulfillment_berat')->nullable();
            $table->double('harga')->nullable();
            $table->datetime('kr_proses')->nullable();
            $table->datetime('kr_selesai')->nullable();
            $table->string('retur_tujuan', 245)->nullable();
            $table->string('retur_status', 245)->nullable();
            $table->integer('retur_qty')->nullable();
            $table->double('retur_berat')->nullable();
            $table->string('retur_notes', 355)->nullable();
            $table->integer('status')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });

        Schema::table('order_items', function ($table) {
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
        });

        Schema::table('order_items', function ($table) {
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retur_item', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('retur_id')->nullable()->unsigned();
            $table->bigInteger('item_id')->nullable()->unsigned();
            $table->bigInteger('orderitem_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('kategori')->nullable();
            $table->text('catatan')->nullable();
            $table->string('tujuan')->nullable();
            $table->double('part')->nullable();
            $table->double('qty')->nullable();
            $table->double('berat')->nullable();
            $table->string('unit')->nullable();
            $table->double('rate')->nullable();
            $table->integer('internal_id_gudang')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('retur_item');
    }
}

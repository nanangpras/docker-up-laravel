<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreateChillerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chiller', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('production_id')->unsigned()->nullable();
            $table->string('table_name', 145)->nullable();
            $table->integer('table_id')->nullable();
            $table->string('asal_tujuan', 145)->nullable();
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->string('item_name', 245)->nullable();
            $table->string('jenis', 55)->nullable();
            $table->string('type', 55)->nullable();
            $table->string('kategori', 55)->nullable();
            $table->string('regu', 200)->nullable();
            $table->text('label')->nullable();
            $table->integer('qty_item')->nullable();
            $table->date('tanggal_potong', 55)->nullable();
            $table->integer('no_mobil')->nullable();
            $table->double('berat_item')->nullable();
            $table->date('tanggal_produksi')->nullable();
            $table->integer('keranjang')->nullable();
            $table->double('berat_keranjang')->nullable();
            $table->integer('stock_item')->nullable();
            $table->double('stock_berat')->nullable();
            $table->integer('status')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('chiller', function ($table) {
            $table->foreign('production_id')
                ->references('id')
                ->on('productions')
                ->onDelete('cascade');

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
        Schema::dropIfExists('chiller');
    }
}

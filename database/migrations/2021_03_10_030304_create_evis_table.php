<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreateEvisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evis', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('production_id')->unsigned()->nullable();
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->string('jenis', 145)->nullable();
            $table->string('peruntukan', 145)->nullable();
            $table->date('tanggal_potong')->nullable();
            $table->integer('total_item')->nullable();
            $table->double('berat_item')->nullable();
            $table->integer('keranjang')->nullable();
            $table->double('berat_keranjang')->nullable();
            $table->integer('stock_item')->nullable();
            $table->double('berat_stock')->nullable();
            $table->integer('status')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('evis', function ($table) {
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
        Schema::dropIfExists('evis');
    }
}

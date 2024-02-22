<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abf', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('production_id')->unsigned()->nullable();
            $table->integer('no_mobil')->unsigned()->nullable();
            $table->string('table_name', 145)->nullable();
            $table->integer('table_id')->nullable();
            $table->string('asal_tujuan', 145)->nullable();
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->bigInteger('item_id_lama')->nullable();
            $table->string('item_name', 245)->nullable();
            $table->string('jenis', 55)->nullable();
            $table->string('type', 55)->nullable();
            $table->integer('qty_item')->nullable();
            $table->double('berat_item')->nullable();
            $table->string('tujuan', 255)->nullable();
            $table->integer('pallete')->nullable();
            $table->string('packaging', 255)->nullable();
            $table->integer('expired')->nullable();
            $table->string('jenis_stock', 255)->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });

        Schema::table('abf', function ($table) {
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
        Schema::dropIfExists('abf');
    }
}

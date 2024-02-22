<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreateGradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grading', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trans_id')->unsigned()->nullable();
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->string('grade_item', 45)->nullable();
            $table->string('jenis_karkas', 45)->nullable();
            $table->integer('total_item')->nullable();
            $table->double('berat_item')->nullable();
            $table->integer('stock_item')->nullable();
            $table->double('stock_berat')->nullable();
            $table->integer('keranjang')->nullable();
            $table->double('berat_keranjang')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('grading', function ($table) {
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');

            $table->foreign('trans_id')
                ->references('id')
                ->on('productions')
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
        Schema::dropIfExists('grading');
    }
}

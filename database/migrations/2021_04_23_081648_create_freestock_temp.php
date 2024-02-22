<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreestockTemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_stocktemp', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('freestock_id')->nullable();
            $table->bigInteger('freestocklist_id')->nullable();
            $table->bigInteger('item_id')->nullable();
            $table->bigInteger('kategori')->nullable();
            $table->string('regu')->nullable();
            $table->date('tanggal_produksi')->nullable();
            $table->text('label')->nullable();
            $table->double('qty')->nullable();
            $table->double('berat')->nullable();
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
        Schema::dropIfExists('free_stocktemp');
    }
}

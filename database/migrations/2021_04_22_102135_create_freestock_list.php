<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreestockList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_stocklist', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('freestock_id')->nullable()->unsigned();
            $table->bigInteger('chiller_id')->unsigned()->nullable();
            $table->bigInteger('outchiller')->nullable();
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->double('qty')->nullable();
            $table->string('regu')->nullable();
            $table->double('berat')->nullable();
            $table->double('sisa')->nullable();
            $table->double('sisa_berat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::table('free_stocklist', function ($table) {
            $table->foreign('item_id')
            ->references('id')
                ->on('items')
                ->onDelete('cascade');

            $table->foreign('chiller_id')
            ->references('id')
                ->on('chiller')
                ->onDelete('cascade');

            $table->foreign('freestock_id')
            ->references('id')
                ->on('free_stock')
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
        Schema::dropIfExists('free_stocklist');
    }
}

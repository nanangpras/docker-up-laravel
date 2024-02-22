<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvisProsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evis_proses', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('item_id')->unsigned()->nullable();
                $table->bigInteger('evis_id')->unsigned()->nullable();
                $table->bigInteger('chiller_id')->unsigned()->nullable();
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

        Schema::table('evis_proses', function ($table) {
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
        });

        Schema::table('evis_proses', function ($table) {
            $table->foreign('chiller_id')
                ->references('id')
                ->on('chiller')
                ->onDelete('cascade');
        });

        Schema::table('evis_proses', function ($table) {
            $table->foreign('evis_id')
                ->references('id')
                ->on('evis')
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
        Schema::dropIfExists('evis_proses');
    }
}

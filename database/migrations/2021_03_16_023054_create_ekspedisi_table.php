<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEkspedisiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ekspedisi', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('driver_id')->nullable()->unsigned();
            $table->string('nama', 155)->nullable();
            $table->string('no_polisi', 45)->nullable();
            $table->bigInteger('wilayah_id')->nullable()->unsigned();
            $table->string('truk', 45)->nullable();
            $table->datetime('keluar')->nullable();
            $table->datetime('kembali')->nullable();
            $table->double('berat')->nullable();
            $table->double('qty')->nullable();
            $table->integer('status')->nullable();
            $table->integer('no_urut')->nullable();
            $table->timestamps();
            $table->string('key', 256)->nullable();
            $table->softDeletes();
        });

        Schema::table('ekspedisi', function ($table) {
            $table->foreign('driver_id')
                ->references('id')
                ->on('driver')
                ->onDelete('cascade');

            // $table->foreign('order_id')
            //     ->references('id')
            //     ->on('orders')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ekspedisi');
    }
}

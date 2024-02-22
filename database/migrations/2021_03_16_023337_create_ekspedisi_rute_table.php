<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEkspedisiRuteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ekspedisi_rute', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ekspedisi_id')->nullable()->unsigned();
            $table->bigInteger('urutan')->nullable();
            $table->string('nama', 45)->nullable();
            $table->string('alamat', 45)->nullable();
            $table->string('telp', 45)->nullable();
            $table->string('kelurahan', 145)->nullable();
            $table->string('kecamatan', 145)->nullable();
            $table->string('kota', 145)->nullable();
            $table->string('provinsi', 145)->nullable();
            $table->string('kode_pos', 45)->nullable();
            $table->bigInteger('order_id')->nullable()->unsigned();
            $table->bigInteger('order_item_id')->nullable()->unsigned();
            $table->bigInteger('wilayah_id')->nullable()->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('status')->nullable();
            $table->date('delivery_date')->nullable();
            $table->time('delivery_time')->nullable();
            $table->string('key', 256)->nullable();
        });

        Schema::table('ekspedisi_rute', function ($table) {
            $table->foreign('ekspedisi_id')
                ->references('id')
                ->on('ekspedisi')
                ->onDelete('cascade');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
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
        Schema::dropIfExists('ekspedisi_rute');
    }
}

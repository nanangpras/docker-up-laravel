<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 45)->nullable();
            $table->string('nama', 100)->nullable();
            $table->string('alamat', 45)->nullable();
            $table->string('no_polisi', 45)->nullable();
            $table->string('telp', 45)->nullable();
            $table->string('kelurahan', 145)->nullable();
            $table->string('kecamatan', 145)->nullable();
            $table->string('kota', 145)->nullable();
            $table->string('provinsi', 145)->nullable();
            $table->string('kode_pos', 45)->nullable();
            $table->integer('driver_kirim')->nullable();
            $table->integer('driver_exspedisi')->nullable();
            $table->integer('status')->nullable();
            $table->string('key', 256)->nullable();
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
        Schema::dropIfExists('driver');
    }
}

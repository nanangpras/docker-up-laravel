<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->string('code', 45)->nullable();
            $table->string('nama', 45)->nullable();
            $table->string('alamat', 45)->nullable();
            $table->string('telp', 45)->nullable();
            $table->string('kelurahan', 145)->nullable();
            $table->string('kecamatan', 145)->nullable();
            $table->string('kota', 145)->nullable();
            $table->string('provinsi', 145)->nullable();
            $table->string('kode_pos', 45)->nullable();
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
        Schema::dropIfExists('company');
    }
}

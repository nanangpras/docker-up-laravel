<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 155)->nullable();
            $table->bigInteger('user_id')->nullable()->unsigned();
            $table->bigInteger('netsuite_internal_id')->nullable();
            $table->string('alamat', 355)->nullable();
            $table->string('telp', 45)->nullable();
            $table->string('kelurahan', 345)->nullable();
            $table->string('kecamatan', 345)->nullable();
            $table->string('kota', 345)->nullable();
            $table->string('provinsi', 345)->nullable();
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
        Schema::dropIfExists('marketing');
    }
}

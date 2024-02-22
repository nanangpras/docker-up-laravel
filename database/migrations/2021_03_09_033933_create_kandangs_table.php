<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreateKandangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kandang', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('supplier_id')->unsigned();
            $table->string('name', 45)->nullable();
            $table->string('alamat', 145)->nullable();
            $table->string('telp', 45)->nullable();
            $table->string('nama_kandang', 145)->nullable();
            $table->string('kelurahan', 145)->nullable();
            $table->string('kecamatan', 145)->nullable();
            $table->string('kota', 145)->nullable();
            $table->string('provinsi', 145)->nullable();
            $table->string('kode_pos', 45)->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });

        Schema::table('kandang', function ($table) {
            $table->foreign('supplier_id')
                ->references('id')
                ->on('supplier')
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
        Schema::dropIfExists('kandang');
    }
}

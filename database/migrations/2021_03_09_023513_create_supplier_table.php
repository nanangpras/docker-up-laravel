<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreateSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('netsuite_internal_id')->nullable();
            $table->string('nama', 345)->nullable();
            $table->string('alamat', 545)->nullable();
            $table->string('telp', 45)->nullable();
            $table->string('kode', 145)->nullable();
            $table->string('kategori', 245)->nullable();
            $table->string('peruntukan', 245)->nullable();
            $table->text('wilayah')->nullable();
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
        Schema::dropIfExists('supplier');
    }
}

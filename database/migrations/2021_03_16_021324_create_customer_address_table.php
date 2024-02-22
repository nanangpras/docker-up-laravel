<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_address', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('wilayah', 105)->nullable();
            $table->string('telp', 45)->nullable();
            $table->string('kelurahan', 255)->nullable();
            $table->string('kecamatan', 255)->nullable();
            $table->string('kota', 255)->nullable();
            $table->string('provinsi', 255)->nullable();
            $table->string('kode_pos', 45)->nullable();
            $table->bigInteger('distribution_center')->nullable();
            $table->bigInteger('customer_id')->nullable()->unsigned();
            $table->bigInteger('parent_id')->nullable()->unsigned();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });

        Schema::table('customer_address', function ($table) {
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
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
        Schema::dropIfExists('customer_address');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->nullable()->unsigned();
            $table->bigInteger('netsuite_internal_id')->nullable();
            $table->string('id_so', 245)->nullable();
            $table->string('no_so', 245)->nullable();
            $table->string('no_po', 245)->nullable();
            $table->date('tanggal_so')->nullable();
            $table->string('sales_id', 245)->nullable();
            $table->string('sales_channel', 245)->nullable();
            $table->string('wilayah', 245)->nullable();
            $table->string('nama', 245)->nullable();
            $table->string('partner', 245)->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->string('alamat', 345)->nullable();
            $table->string('alamat_kirim', 345)->nullable();
            $table->string('keterangan', 345)->nullable();
            $table->string('kode', 45)->nullable();
            $table->string('no_invoice', 45)->nullable();
            $table->datetime('invoice_created_at')->nullable();
            $table->string('telp', 45)->nullable();
            $table->string('kelurahan', 145)->nullable();
            $table->string('kecamatan', 145)->nullable();
            $table->string('kota', 145)->nullable();
            $table->string('provinsi', 145)->nullable();
            $table->string('kode_pos', 45)->nullable();
            $table->datetime('kp_proses')->nullable();
            $table->datetime('kp_selesai')->nullable();
            $table->datetime('kr_proses')->nullable();
            $table->datetime('kr_selesai')->nullable();
            $table->integer('status')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });


        // ALTER TABLE `orders` ADD `no_so` VARCHAR(255) NULL AFTER `id`, ADD `id_so` VARCHAR(255) NOT NULL AFTER `no_so`, ADD `no_po` VARCHAR(255) NOT NULL AFTER `id_so`, ADD `wilayah` VARCHAR(255) NOT NULL AFTER `no_po`, ADD `sales_id` VARCHAR(255) NOT NULL AFTER `wilayah`;

        Schema::table('orders', function ($table) {
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
        Schema::dropIfExists('orders');
    }
}

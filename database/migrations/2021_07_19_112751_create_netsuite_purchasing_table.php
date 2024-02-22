<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetsuitePurchasingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netsuite_purchasing', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('netsuite_log_id')->nullable();
            $table->string('activity', 145)->nullable();

            $table->string('document_number', 255)->nullable();
            $table->string('type_po', 255)->nullable();
            $table->string('internal_id', 255)->nullable();
            $table->string('item', 255)->nullable();
            $table->string('rate', 255)->nullable();
            $table->string('vendor', 255)->nullable();
            $table->string('vendor_name', 255)->nullable();
            $table->string('ukuran_ayam', 255)->nullable();
            $table->string('qty', 255)->nullable();
            $table->string('jumlah_ayam', 255)->nullable();
            $table->string('tipe_ekspedisi', 255)->nullable();
            $table->string('jenis_ayam', 255)->nullable();
            $table->string('jumlah_do', 255)->nullable();
            $table->string('tanggal_kirim', 255)->nullable();
            $table->string('internal_id_po', 255)->nullable();
            $table->string('po_subsidiary', 255)->nullable();

            $table->string('internal_id_vendor', 255)->nullable();
            $table->string('nama_vendor', 255)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('no_telp', 255)->nullable();
            $table->string('jenis_ekspedisi', 255)->nullable();
            $table->string('wilayah_vendor', 255)->nullable();
            $table->string('vendor_subsidiary', 255)->nullable();

            $table->text('data_item')->nullable();

            $table->string('internal_id_item', 255)->nullable();
            $table->string('sku', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('category_item', 255)->nullable();
            $table->string('item_subsidiary', 255)->nullable();

            $table->date('last_update')->nullable();
            $table->integer('server_update')->nullable();
            $table->integer('local_crawl')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });

        // Schema::table('productions', function ($table) {
        //     $table->foreign('purchasing_id')
        //         ->references('id')
        //         ->on('purchasing')
        //         ->onDelete('cascade');
        // });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netsuite_purchasing');
    }
}

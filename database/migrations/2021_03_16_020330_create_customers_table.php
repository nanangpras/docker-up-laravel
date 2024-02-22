<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 200)->nullable();
            $table->string('nama', 200)->nullable();
            $table->string('alamat', 200)->nullable();
            $table->string('telp', 200)->nullable();
            $table->bigInteger('marketing_id')->nullable()->unsigned();
            $table->bigInteger('netsuite_internal_id')->nullable();
            $table->string('nama_marketing', 200)->nullable();
            $table->string('kelurahan', 200)->nullable();
            $table->string('kecamatan', 200)->nullable();
            $table->string('kota', 200)->nullable();
            $table->string('provinsi', 200)->nullable();
            $table->string('kode_pos', 200)->nullable();
            $table->string('parent_nama', 200)->nullable();
            $table->bigInteger('parent_id')->nullable()->unsigned();
            $table->string('nama_perusahaan', 200)->nullable();
            $table->string('direktor', 200)->nullable();
            $table->string('direktor_no_telp', 200)->nullable();
            $table->string('purchasing_manager', 200)->nullable();
            $table->string('purchasing_manager_no_telp', 200)->nullable();
            $table->string('alamat_warehouse', 200)->nullable();
            $table->string('purchasing_no_telp', 200)->nullable();
            $table->string('purchasing_fax', 200)->nullable();
            $table->string('purchasing_staff', 200)->nullable();
            $table->string('purchasing_staff_no_telp', 200)->nullable();
            $table->string('receiving_staff', 200)->nullable();
            $table->string('receiving_staff_no_telp', 200)->nullable();
            $table->string('accounting_staff', 200)->nullable();
            $table->string('accounting_staff_no_telp', 200)->nullable();
            $table->string('kategori', 200)->nullable();
            $table->string('type_customer', 200)->nullable();
            $table->string('pabrik', 200)->nullable();
            $table->string('jenis_po', 200)->nullable();
            $table->string('pengiriman', 200)->nullable();
            $table->string('syarat_pembayaran', 200)->nullable();
            $table->string('jadwal_tukar_faktur', 200)->nullable();
            $table->string('metode_pembayaran', 200)->nullable();
            $table->string('no_rekening', 200)->nullable();
            $table->string('bank', 200)->nullable();
            $table->string('bank_cabang', 200)->nullable();
            $table->string('bank_nama', 200)->nullable();
            $table->string('remarks', 200)->nullable();
            $table->string('status', 200)->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
        });

        Schema::table('customers', function ($table) {
            $table->foreign('marketing_id')
                ->references('id')
                ->on('marketing')
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
        Schema::dropIfExists('customers');
    }
}

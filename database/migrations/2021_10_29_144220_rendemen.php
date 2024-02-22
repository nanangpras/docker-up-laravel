<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Rendemen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_rendemen', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->integer('subsidiary_id')->nullable();
            $table->string('subsidiary', 20)->nullable();
            $table->double('rendemen_total', 20)->nullable();
            $table->double('rendemen_tangkap', 20)->nullable();
            $table->double('rendemen_kirim', 20)->nullable();
            $table->double('berat_rpa', 20)->nullable();
            $table->double('berat_grading', 20)->nullable();
            $table->double('berat_evis', 20)->nullable();
            $table->double('darah_bulu', 20)->nullable();
            $table->double('ekor_rpa', 20)->nullable();
            $table->double('ekor_grading', 20)->nullable();
            $table->double('selisih_ekor', 20)->nullable();
            $table->double('jumlah_supplier', 20)->nullable();
            $table->double('jumlah_po_mobil', 20)->nullable();
            $table->double('selesai_potong', 20)->nullable();
            $table->double('ekor_do', 20)->nullable();
            $table->double('berat_do', 20)->nullable();
            $table->double('rerata_do', 20)->nullable();
            $table->double('ekoran_seckel', 20)->nullable();
            $table->double('kg_terima', 20)->nullable();
            $table->double('rerata_terima_lb', 20)->nullable();
            $table->double('susut_tangkap', 20)->nullable();
            $table->double('susut_kirim', 20)->nullable();
            $table->double('susut_seckel', 20)->nullable();
            $table->double('ekoran_grading', 20)->nullable();
            $table->double('selisih_seckel_grading', 20)->nullable();
            $table->double('rerata_grading', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rendemen');
    }
}

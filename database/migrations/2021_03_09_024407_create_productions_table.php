<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->nullable();
            $table->bigInteger('purchasing_id')->unsigned()->nullable();
            $table->date('sc_tanggal_masuk')->nullable();
            $table->time('sc_jam_masuk')->nullable();
            $table->string('sc_hari', 45)->nullable();
            $table->string('sc_no_polisi', 45)->nullable();
            $table->bigInteger('sc_pengemudi_id')->unsigned()->nullable();
            $table->string('sc_pengemudi', 255)->nullable();
            $table->bigInteger('target_id')->nullable();
            $table->double('sc_pengemudi_target')->nullable();
            $table->double('sc_pengemudi_uang_jalan')->nullable();
            $table->string('sc_nama_kandang', 145)->nullable();
            $table->string('sc_alamat_kandang', 445)->nullable();
            $table->string('sc_wilayah', 145)->nullable();
            $table->integer('sc_status')->nullable();
            $table->string('po_jenis_ekspedisi', 255)->nullable();
            $table->integer('no_urut')->nullable();
            $table->string('no_do')->nullable();
            $table->string('no_lpah')->nullable();
            $table->integer('sc_penerima_id')->nullable();
            $table->integer('sc_ekor_do')->nullable();
            $table->double('sc_berat_do')->nullable();
            $table->double('sc_rerata_do')->nullable();
            $table->integer('sc_user_id')->nullable();
            $table->time('lpah_jam_bongkar')->nullable();
            $table->date('lpah_tanggal_potong')->nullable();
            $table->time('lpah_jam_potong')->nullable();
            $table->double('lpah_berat_kotor')->nullable();
            $table->string('lpah_initial_tanggal_potong', 145)->nullable();
            $table->string('lpah_initial_potong', 145)->nullable();
            $table->double('lpah_berat_susut')->nullable();
            $table->double('lpah_persen_susut')->nullable();
            $table->double('lpah_berat_terima')->nullable();
            $table->double('lpah_rerata_terima')->nullable();
            $table->integer('lpah_jumlah_keranjang')->nullable();
            $table->double('lpah_berat_keranjang')->nullable();
            $table->integer('ekoran_seckle')->nullable();
            $table->double('lpah_basah')->nullable();
            $table->double('lpah_kering')->nullable();
            $table->datetime('lpah_proses')->nullable();
            $table->datetime('lpah_selesai')->nullable();
            $table->integer('lpah_status')->nullable();
            $table->integer('lpah_netsuite_status')->nullable();
            $table->integer('lpah_user_id')->nullable();
            $table->double('qc_ekor_ayam_mati')->nullable();
            $table->double('qc_persen_ayam_mati')->nullable();
            $table->double('qc_berat_ayam_mati')->nullable();
            $table->double('qc_ekor_ayam_merah')->nullable();
            $table->double('qc_persen_ayam_merah')->nullable();
            $table->double('qc_berat_ayam_merah')->nullable();
            $table->integer('qc_user_id')->nullable();
            $table->datetime('qc_proses')->nullable();
            $table->datetime('qc_selesai')->nullable();
            $table->integer('qc_status')->nullable();
            $table->integer('ppic_acc')->nullable();
            $table->string('ppic_tujuan', 40)->nullable();
            $table->integer('evis_user_id')->nullable();
            $table->datetime('evis_proses')->nullable();
            $table->datetime('evis_selesai')->nullable();
            $table->integer('evis_status')->nullable();
            $table->integer('grading_user_id')->nullable();
            $table->datetime('grading_proses')->nullable();
            $table->datetime('grading_selesai')->nullable();
            $table->integer('grading_status')->nullable();
            $table->string('status', 40)->nullable()->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('productions', function ($table) {
            $table->foreign('purchasing_id')
                ->references('id')
                ->on('purchasing')
                ->onDelete('cascade');

                $table->foreign('sc_pengemudi_id')
                ->references('id')
                ->on('driver')
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
        Schema::dropIfExists('productions');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableQcPostmortem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_qc_postmortem', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('qc_id')->nullable()->unsigned();
            $table->bigInteger('production_id')->nullable()->unsigned();
            // kondisi tembolok (kosong, penuh, setengah)
            $table->string('tembolok_kondisi', 145)->nullable();
            // dalam bentuk gram
            $table->double('tembolok_jumlah')->nullable();

            // 0 tidak ada
            $table->integer('ayam_merah')->nullable();
            $table->integer('kehijauan')->nullable();

            // normal, ada, tidak ada
            $table->string('jeroan_hati', 145)->nullable();
            $table->string('jeroan_jantung', 145)->nullable();
            $table->string('jeroan_ampela', 145)->nullable();
            $table->string('jeroan_usus', 145)->nullable();
            $table->double('rerata')->nullable();
            $table->integer('memar_dada')->nullable();
            $table->integer('memar_paha')->nullable();
            $table->integer('memar_sayap')->nullable();
            $table->integer('patah_sayap')->nullable();
            $table->integer('patah_kaki')->nullable();
            $table->integer('keropeng_kaki')->nullable();
            $table->integer('keropeng_sayap')->nullable();
            $table->integer('keropeng_dada')->nullable();
            $table->integer('keropeng_pg')->nullable();
            $table->integer('keropeng_dengkul')->nullable();
            
            $table->string('catatan', 355)->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });

        Schema::table('table_qc_postmortem', function ($table) {
            $table->foreign('production_id')
                ->references('id')
                ->on('productions')
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
        Schema::dropIfExists('table_qc_postmortem');
    }
}

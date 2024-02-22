<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableQcAntemortem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_qc_antemortem', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('qc_id')->nullable()->unsigned();
            $table->bigInteger('production_id')->nullable()->unsigned();
            $table->string('basah_bulu', 145)->nullable();
            $table->string('keaktifan', 145)->nullable();
            $table->string('cairan', 145)->nullable();
            $table->integer('ayam_mati')->nullable();
            $table->double('ayam_mati_kg')->nullable();
            $table->integer('ayam_sakit')->nullable();
            $table->string('ayam_sakit_nama')->nullable();
            $table->string('catatan', 355)->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });

        Schema::table('table_qc_antemortem', function ($table) {
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
        Schema::dropIfExists('table_qc_antemortem');
    }
}

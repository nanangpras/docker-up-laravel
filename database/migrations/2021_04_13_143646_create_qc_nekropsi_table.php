<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQcNekropsiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_nekropsi', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('production_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('kondisi_umum')->nullable();
            $table->string('kematian')->nullable();
            $table->string('sp_hidung')->nullable();
            $table->string('sp_trakea')->nullable();
            $table->string('sp_paru')->nullable();
            $table->string('sp_kantung_udara')->nullable();
            $table->string('sp_jantung')->nullable();
            $table->string('sistem_rangka')->nullable();
            $table->string('sistem_otot')->nullable();
            $table->string('sp_tembolok')->nullable();
            $table->string('sp_lambung')->nullable();
            $table->string('sp_usus')->nullable();
            $table->string('sp_hati')->nullable();
            $table->string('sp_limpa')->nullable();
            $table->string('sp_proventriculus')->nullable();
            $table->string('sp_fabricius')->nullable();
            $table->string('sp_mata')->nullable();
            $table->string('sistem_kekebalan_tubuh')->nullable();
            $table->text('diagnosa')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('qc_nekropsi', function ($table) {
            $table->foreign('production_id')
            ->references('id')
            ->on('productions')
            ->onDelete('cascade');
        });

        Schema::table('qc_nekropsi', function ($table) {
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
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
        Schema::dropIfExists('qc_nekropsi');
    }
}

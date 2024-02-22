<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SebaranKarkas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_sebarankarkas', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->integer('subsidiary_id')->nullable();
            $table->string('subsidiary', 20)->nullable();
            $table->bigInteger('item_id')->nullable();
            $table->string('sku', 30)->nullable();
            $table->string('nama', 100)->nullable();
            $table->double('qty', 20)->nullable();
            $table->double('berat', 20)->nullable();
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
        Schema::dropIfExists('laporan_sebarankarkas');
    }
}

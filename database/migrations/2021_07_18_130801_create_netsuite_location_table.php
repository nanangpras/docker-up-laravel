<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetsuiteLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netsuite_location', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('netsuite_log_id')->nullable();
            $table->string('activity', 145)->nullable();
            $table->integer('internal_id_location')->nullable();
            $table->string('nama_location', 255)->nullable();
            $table->string('kategori', 255)->nullable();
            $table->string('subsidiary', 255)->nullable();
            $table->integer('subsidiary_id')->nullable();
            $table->date('last_update')->nullable();
            $table->integer('server_update')->nullable();
            $table->integer('local_crawl')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('netsuite_location');
    }
}

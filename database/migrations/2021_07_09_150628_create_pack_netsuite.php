<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackNetsuite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netsuite', function (Blueprint $table) {
            $table->id();
            $table->string('record_type', 100)->nullable();
            $table->string('label', 50)->nullable();
            $table->date('trans_date')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('paket_id')->nullable();
            $table->string('tabel', 100)->nullable();
            $table->bigInteger('tabel_id')->nullable();
            $table->integer('subsidiary_id')->nullable();
            $table->string('subsidiary', 100)->nullable();
            $table->integer('id_location')->nullable();
            $table->string('location', 100)->nullable();
            $table->integer('script')->nullable();
            $table->integer('deploy')->nullable();
            $table->text('data_content')->nullable();
            $table->integer('response_id')->nullable();
            $table->text('response')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netsuite');
    }
}

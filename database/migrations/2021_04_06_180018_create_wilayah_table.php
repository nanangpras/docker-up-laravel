<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWilayahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wilayah', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 155)->nullable();
            $table->string('slug', 355)->nullable();
            $table->integer('parent_id')->nullable();
            $table->bigInteger('netsuite_internal_id')->nullable();
            $table->string('icon', 345)->nullable();
            $table->string('longitude', 345)->nullable();
            $table->string('latitude', 345)->nullable();
            $table->integer('status')->nullable();
            $table->integer('urutan')->nullable();
            $table->string('key', 256)->nullable();
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
        Schema::dropIfExists('wilayah');
    }
}

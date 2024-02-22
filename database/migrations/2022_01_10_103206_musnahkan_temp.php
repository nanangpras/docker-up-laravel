<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MusnahkanTemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musnahkan_temp', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('musnahkan_id')->nullable();
            $table->bigInteger('gudang_id')->nullable();
            $table->bigInteger('item_id')->nullable();
            $table->double('qty', 20)->nullable();
            $table->double('berat', 20)->nullable();
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
        Schema::dropIfExists('musnahkan_temp');
    }
}

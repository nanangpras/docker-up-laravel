<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OpenBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openbalance', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('gudang', 40)->nullable();
            $table->bigInteger('item_id')->nullable();
            $table->string('tipe_item', 20)->nullable();
            $table->date('tanggal')->nullable();
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
        Schema::dropIfExists('openbalance');
    }
}

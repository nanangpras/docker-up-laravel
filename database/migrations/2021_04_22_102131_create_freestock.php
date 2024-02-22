<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreestock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('free_stock', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor')->nullable();
            $table->date('tanggal')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('kategori')->nullable();
            $table->string('regu')->nullable();
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
        Schema::dropIfExists('free_stock');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retur', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->nullable()->unsigned();
            $table->bigInteger('qc_id')->nullable()->unsigned();
            $table->string('id_so', 245)->nullable();
            $table->string('no_so', 245)->nullable();
            $table->date('tanggal_retur')->nullable();
            $table->date('retur_approve')->nullable();
            $table->integer('status')->nullable();
            $table->string('key', 256)->nullable();
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
        Schema::dropIfExists('retur');
    }
}

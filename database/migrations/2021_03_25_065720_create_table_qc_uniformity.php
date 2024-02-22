<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableQcUniformity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_qc_uniformity', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('qc_id')->nullable()->unsigned();
            $table->bigInteger('production_id')->nullable()->unsigned();
            $table->double('berat')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });

        Schema::table('table_qc_uniformity', function ($table) {
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
        Schema::dropIfExists('table_qc_uniformity');
    }
}

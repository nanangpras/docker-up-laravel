<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreateLpahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lpah', function (Blueprint $table) {
            $table->id();
            $table->double('qty')->nullable();
            $table->double('berat')->nullable();
            $table->string('type', 45)->nullable();
            $table->bigInteger('production_id')->unsigned()->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });

        Schema::table('lpah', function ($table) {
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
        Schema::dropIfExists('lpah');
    }
}

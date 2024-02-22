<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wo', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 145)->nullable();
            $table->bigInteger('table_id')->nullable();
            $table->bigInteger('bom')->unsigned()->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('status')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });

        Schema::table('wo', function ($table) {
            $table->foreign('bom')
                ->references('id')
                ->on('bom')
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
        Schema::dropIfExists('wo');
    }
}

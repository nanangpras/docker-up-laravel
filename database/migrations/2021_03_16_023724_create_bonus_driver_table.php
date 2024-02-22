<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusDriverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_driver', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trans_id')->nullable()->unsigned();
            $table->double('target')->nullable();
            $table->double('hasil')->nullable();
            $table->bigInteger('driver_id')->nullable()->unsigned();
            $table->string('key', 256)->nullable();
            $table->timestamps();
        });

        Schema::table('bonus_driver', function ($table) {
            $table->foreign('trans_id')
                ->references('id')
                ->on('productions')
                ->onDelete('cascade');

            $table->foreign('driver_id')
                ->references('id')
                ->on('driver')
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
        Schema::dropIfExists('bonus_driver');
    }
}

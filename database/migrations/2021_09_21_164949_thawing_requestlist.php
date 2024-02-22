<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ThawingRequestlist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thawing_requestlist', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('thawing_id')->nullable();
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
        Schema::dropIfExists('thawing_requestlist');
    }
}

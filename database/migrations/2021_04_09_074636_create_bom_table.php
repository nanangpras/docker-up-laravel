<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom', function (Blueprint $table) {
            $table->id();
            $table->string('bom_name', 255)->nullable();
            $table->string('bom_desc', 355)->nullable();
            $table->integer('netsuite_internal_id')->nullable();
            $table->integer('menu_order')->nullable();
            $table->double('cost')->nullable();
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
        Schema::dropIfExists('bom');
    }
}

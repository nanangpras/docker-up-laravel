<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->text('icon')->nullable();
            $table->string('slug')->nullable();
            $table->string('option_type')->nullable();
            $table->string('position')->nullable();
            $table->text('option_name')->nullable();
            $table->text('option_value')->nullable();
            $table->text('data')->nullable();
            $table->integer('editable')->nullable();
            $table->integer('menu_order')->nullable();
            $table->integer('status')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('options');
    }
}

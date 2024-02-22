<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReturTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_retur', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('produt_id')->nullable();
            $table->bigInteger('order_id')->nullable()->unsigned();
            $table->double('berat')->nullable();
            $table->double('total_item')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('product_retur', function ($table) {
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            // $table->foreign('order_id')
            //     ->references('id')
            //     ->on('orders')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_retur');
    }
}

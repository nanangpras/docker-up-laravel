<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReturPurchase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retur_purchase', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchasing_id')->nullable();
            $table->bigInteger('purchaseitem_id')->nullable();
            $table->double('qty', 20)->nullable();
            $table->double('berat', 20)->nullable();
            $table->string('alasan', 100)->nullable();
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
        Schema::dropIfExists('retur_purchase');
    }
}

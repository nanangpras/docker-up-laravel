<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_item', function (Blueprint $table) {
            $table->id();
            $table->string('item_po')->nullable();
            $table->bigInteger('internal_id_po')->nullable();
            $table->bigInteger('purchasing_id')->unsigned()->nullable();
            $table->double('harga')->nullable();
            $table->string('ukuran_ayam', 10)->nullable();
            $table->bigInteger('jumlah_do')->nullable();
            $table->string('jenis_ayam', 45)->nullable();
            $table->double('berat_ayam')->nullable();
            $table->double('jumlah_ayam')->nullable();
            $table->double('terima_berat_item')->nullable();
            $table->double('terima_jumlah_item')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('purchase_item', function ($table) {
            $table->foreign('purchasing_id')
                ->references('id')
                ->on('purchasing')
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
        Schema::dropIfExists('purchase_item');
    }
}

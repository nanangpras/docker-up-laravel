<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductGudangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_gudang', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->nullable()->unsigned();
            $table->string('sub_item', 255)->nullable();
            $table->string('table_name', 145)->nullable();
            $table->integer('table_id')->nullable();
            $table->string('no_so')->nullable();
            $table->bigInteger('order_id')->nullable();
            $table->bigInteger('order_item_id')->nullable();
            $table->double('qty')->nullable();
            $table->double('berat_timbang')->nullable();
            $table->double('berat')->nullable();
            $table->text('notes')->nullable();
            $table->string('packaging', 145)->nullable();
            $table->integer('palete')->nullable();
            $table->integer('potong')->nullable();
            $table->integer('expired')->nullable();
            $table->date('production_date')->nullable();
            $table->string('production_code')->nullable();
            $table->string('type', 45)->nullable();
            $table->string('stock_type', 45)->nullable();
            $table->string('jenis_trans', 45)->nullable();
            $table->bigInteger('abf_id')->nullable()->unsigned();
            $table->bigInteger('gudang_id')->nullable()->unsigned();
            $table->bigInteger('no_urut')->nullable();
            $table->bigInteger('chiller_id')->nullable()->unsigned();
            $table->bigInteger('gudang_id_keluar')->nullable();
            $table->integer('status')->nullable();

            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();

        });

        Schema::table('product_gudang', function ($table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');

            $table->foreign('gudang_id')
                ->references('id')
                ->on('gudang')
                ->onDelete('cascade');

            $table->foreign('abf_id')
                ->references('id')
                ->on('abf')
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
        Schema::dropIfExists('product_gudang');
    }
}

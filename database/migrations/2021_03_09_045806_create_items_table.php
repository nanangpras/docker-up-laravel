<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('netsuite_internal_id')->nullable();
            $table->string('nama', 145)->nullable();
            $table->string('main_product', 45)->nullable();
            $table->string('by_product', 45)->nullable();
            $table->string('jenis', 45)->nullable();
            $table->string('code_item', 45)->nullable();
            $table->string('sku', 45)->nullable();
            $table->string('slug', 45)->nullable();
            $table->string('type', 45)->nullable();
            $table->double('berat_kali')->nullable();
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('key', 256)->nullable();
            $table->bigInteger('status')->nullable();
        });

        Schema::table('items', function ($table) {
            $table->foreign('category_id')
                ->references('id')
                ->on('category')
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
        Schema::dropIfExists('items');
    }
}

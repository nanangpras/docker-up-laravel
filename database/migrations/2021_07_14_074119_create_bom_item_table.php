<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom_item', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bom_id')->unsigned()->nullable();
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->string('sku', 145)->nullable();
            $table->double('level')->nullable();
            $table->double('component_yield')->nullable();
            $table->double('bom_qty_per_assembly')->nullable();
            $table->double('qty_per_assembly')->nullable();
            $table->double('qty_per_top_level_assembly')->nullable();
            $table->string('unit')->nullable();
            $table->string('kategori')->nullable();
            $table->integer('status')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('bom_item', function ($table) {
            $table->foreign('bom_id')
                ->references('id')
                ->on('bom')
                ->onDelete('cascade');

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
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
        Schema::dropIfExists('bom_item');
    }
}

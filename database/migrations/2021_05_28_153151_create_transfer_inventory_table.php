<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_inventory', function (Blueprint $table) {
            $table->id();
            $table->text('memo')->nullable();
            $table->string('table_name', 145)->nullable();
            $table->integer('table_id')->nullable();
            $table->string('from_gudang', 255)->nullable();
            $table->string('to_gudang', 255)->nullable();
            $table->text('transfer_data')->nullable();
            $table->string('key', 256)->nullable();
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
        Schema::dropIfExists('transfer_inventory');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpesifikasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spesifikasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->bigInteger('bom')->unsigned()->nullable();
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('spesifikasi', function ($table) {
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });

        Schema::table('spesifikasi', function ($table) {
            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');
        });

        Schema::table('spesifikasi', function ($table) {
            $table->foreign('bom')
                ->references('id')
                ->on('bom')
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
        Schema::dropIfExists('spesifikasi');
    }
}

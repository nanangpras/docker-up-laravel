<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGudangTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gudang', function (Blueprint $table) {
            $table->id();
            $table->string('code', 45)->nullable();
            $table->bigInteger('company_id')->nullable()->unsigned();
            $table->bigInteger('netsuite_internal_id')->nullable();
            $table->string('kategori', 255)->nullable();
            $table->integer('subsidiary_id')->nullable();
            $table->string('subsidiary', 255)->nullable();

            $table->bigInteger('status')->nullable();
            $table->timestamps();
            $table->string('key', 256)->nullable();
            $table->softDeletes();
        });

        Schema::table('gudang', function ($table) {
            $table->foreign('company_id')
                ->references('id')
                ->on('company')
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
        Schema::dropIfExists('gudang');
    }
}

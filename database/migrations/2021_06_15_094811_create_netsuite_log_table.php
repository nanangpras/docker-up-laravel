<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetsuiteLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netsuite_log', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('activity', 145)->nullable();
            $table->string('table_name', 145)->nullable();
            $table->string('label', 355)->nullable();
            $table->bigInteger('table_id')->nullable();
            $table->text('table_data')->nullable();
            $table->string('sync', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->datetime('sync_start_at')->nullable();
            $table->datetime('sync_completed_at')->nullable();
            $table->string('key', 256)->nullable();
            $table->bigInteger('sync_status')->nullable();
            $table->integer('admin_read')->nullable();
            $table->integer('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netsuite_log');
    }
}

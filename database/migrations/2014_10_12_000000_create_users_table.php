<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->datetime('email_verified_at')->nullable();
            $table->bigInteger('company_id')->nullable()->unsigned();
            $table->string('password');
            $table->string('pin', 45)->nullable();
            $table->string('phone', 45)->nullable();
            $table->datetime('phone_verified_at')->nullable();
            $table->string('account_type', 45)->nullable();
            $table->string('account_role', 45)->nullable();
            $table->text('group_role')->nullable();
            $table->string('photo', 145)->nullable();
            $table->datetime('last_login')->nullable();
            $table->string('fcm_token', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->bigInteger('status')->nullable();
            $table->string('key', 256)->nullable();
            $table->softDeletes();
        });

        // Schema::table('users', function ($table) {
        //     $table->foreign('company_id')
        //         ->references('id')
        //         ->on('company')
        //         ->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

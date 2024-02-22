<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetsuiteBomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netsuite_bom', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('netsuite_log_id')->nullable();
            $table->string('activity', 145)->nullable();

            $table->integer('internal_id_bom')->nullable();
            $table->string('bom_name', 255)->nullable();
            $table->string('internal_subsidiary_id', 255)->nullable();
            $table->string('subsidiary', 255)->nullable();
            $table->string('memo', 255)->nullable();
            $table->text('data_item')->nullable();

            $table->date('last_update')->nullable();
            $table->integer('server_update')->nullable();
            $table->integer('local_crawl')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
            $table->softDeletes();

        //     "bom":{
        //         "internal_id_bom":null,
        //         "bom_name":null,
        //         "internal_subsidiary_id":"6",
        //         "subsidiary":"HOLDING : RPA : CGL",
        //         "memo":"WO-1",
        //         "item":[
        //            {
        //               "internal_id_item":"1824",
        //               "type":"Finished Goods",
        //               "sku":"1100000001",
        //               "name":"AYAM KARKAS BROILER (RM)",
        //               "qty":1,
        //               "unit":"7"
        //            },
        //            {
        //               "internal_id_item":"1998",
        //               "type":"By Product",
        //               "sku":"1211810004",
        //               "name":"HATI AMPELA KOTOR BROILER",
        //               "qty":0.05,
        //               "unit":"7"
        //            },
        //            {
        //               "internal_id_item":"2012",
        //               "type":"By Product",
        //               "sku":"1211840002",
        //               "name":"KEPALA LEHER BROILER",
        //               "qty":0.05,
        //               "unit":"7"
        //            },
        //            {
        //               "internal_id_item":"2008",
        //               "type":"By Product",
        //               "sku":"1211830001",
        //               "name":"KAKI KOTOR BROILER",
        //               "qty":0.05,
        //               "unit":"7"
        //            },
        //            {
        //               "internal_id_item":"2006",
        //               "type":"By Product",
        //               "sku":"1211820005",
        //               "name":"USUS BROILER",
        //               "qty":0.05,
        //               "unit":"7"
        //            }
        //         ]
        //      }
        //   },

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netsuite_bom');
    }
}

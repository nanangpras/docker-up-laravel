<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetsuiteSalesorderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netsuite_sales_order', function (Blueprint $table) {
            
            $table->id();
            $table->bigInteger('netsuite_log_id')->nullable();
            $table->string('activity', 145)->nullable();

            $table->string('internal_id_customer', 255)->nullable();
            $table->string('nama_customer', 255)->nullable();
            $table->string('category_customer', 255)->nullable();
            $table->string('id_sales', 255)->nullable();
            $table->string('sales', 255)->nullable();
            $table->string('customer_subsidiary', 255)->nullable();
            $table->string('internal_id_parent', 255)->nullable();
            $table->string('sales_channel', 255)->nullable();
            
            $table->string('internal_id_so', 255)->nullable();
            $table->string('nomor_so', 255)->nullable();
            $table->string('nomor_po', 255)->nullable();
            $table->string('tanggal_kirim', 255)->nullable();
            $table->string('tanggal_so', 255)->nullable();
            $table->string('customer_partner', 255)->nullable();
            $table->string('alamat_customer_partner', 255)->nullable();
            $table->string('wilayah', 255)->nullable();
            $table->string('memo', 255)->nullable();
            $table->string('alamat_ship_to', 255)->nullable();
            $table->string('internal_subsidiary_id', 255)->nullable();
            $table->string('so_subsidiary', 255)->nullable();
            
            $table->text('data_item')->nullable();
        
            $table->date('last_update')->nullable();
            $table->integer('server_update')->nullable();
            $table->integer('local_crawl')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
            $table->softDeletes();


            // "data_customer":{
            //     "internal_id_customer":"13124",
            //     "nama_customer":null,
            //     "category_customer":"",
            //     "id_sales":"",
            //     "sales":"",
            //     "subsidiary":"1",
            //     "internal_id_parent":"17346 - Elva Masfufa"
            //  },
            //  "data_sales_order":{
            //     "internal_id_so":"56248",
            //     "nomor_so":"SO.MPP.2020.11.00177",
            //     "nomor_po":"",
            //     "internal_id_customer":"13124",
            //     "nama_customer":"Elva Masfufa",
            //     "tanggal_kirim":"",
            //     "tanggal_so":"23-Nov-2020",
            //     "customer_partner":"",
            //     "alamat_customer_partner":"",
            //     "wilayah":null,
            //     "id_sales":"",
            //     "sales":"",
            //     "memo":"Kode Unik",
            //     "sales_channel":"ONLINE",
            //     "alamat_ship_to":"",
            //     "internal_subsidiary_id":"1",
            //     "subsidiary":"MPP"
            //  },
            //  "data_item":[
            //     {
            //        "internal_id_item":"1728",
            //        "sku":"21210501",
            //        "name":"AYAM MEMAR FROZEN (0.8-0.9 KG)",
            //        "category_item":"",
            //        "subsidiary":"1",
            //        "description_item":"Ayam Memar Frozen Ukuran 0.8-0.9 kg",
            //        "qty":3,
            //        "unit":"4",
            //        "rate":21000
            //     }
            //  ]

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netsuite_sales_order');
    }
}

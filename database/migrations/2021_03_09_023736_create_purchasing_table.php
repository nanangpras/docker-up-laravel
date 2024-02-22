<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;

class CreatePurchasingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('purchasing', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->nullable();
            $table->string('jenis_po')->nullable();
            $table->string('type_po')->nullable();
            $table->string('item_po')->nullable();
            $table->bigInteger('internal_id_po')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->double('harga_penawaran')->nullable();
            $table->double('harga_deal')->nullable();
            $table->bigInteger('company_id')->nullable();
            $table->bigInteger('supplier_id')->unsigned()->nullable();
            $table->string('ukuran_ayam', 10)->nullable();
            $table->bigInteger('jumlah_per_mobil')->nullable();
            $table->string('type_ekspedisi', 45)->nullable();
            $table->string('jenis_ayam', 45)->nullable();
            $table->double('berat_ayam')->nullable();
            $table->double('jumlah_ayam')->nullable();
            $table->bigInteger('status')->nullable();
            $table->date('tanggal_potong')->nullable();
            $table->bigInteger('jumlah_po')->nullable();
            $table->string('key', 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('purchasing', function ($table) {
            $table->foreign('supplier_id')
            ->references('id')
                ->on('supplier')
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
        Schema::dropIfExists('purchasing');
    }
}

<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\Company;
use App\Models\DataOption;
use App\Models\Item;
use App\Models\Production;
use App\Models\PurchaseItem;
use App\Models\Purchasing;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CrawlPONonLB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlPONonLB:process {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl Purchasing Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $app_crawl = env("APP_CRAWL", "gsheet");


        $update_data    =   0;
        $insert_data    =   0;
        $commit         = false;

        $tanggal = $this->option('tanggal') ?? date('Y-m-d');
        $log = $this->option('log') ?? "off";
        echo "Process ...".$tanggal."...\n";

            DB::beginTransaction();

            $file   = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQ5Of_0Oy5rmjPvDOmxqQirdvbyyUe7NiTTU4OimFVea66fVYXzPrIg3iw_PjK4id8deLzgEWwn-FbI/pub?output=csv";

            $fileData=fopen($file,'r');

                $update_data    =   0;
                $insert_data    =   0;

                $no = 0;
                while (($line = fgetcsv($fileData)) !== FALSE) {

                    try {
                        //code...

                        if($line[0]!="" && $no>0){
                                
                            $status = 2;
                            $commit = false;

                            $selected_ukuran = 0;
                            $selected_jumlah = (float)$line[7] ?? 0;

                            echo $selected_jumlah." --- \n";

                            $nama = str_replace("EBA-", "", $line[2]);
                            $supp = Supplier::where('kode', $line[2])->first();

                            if(!$supp){
                                $supp = Supplier::where('nama', $nama)->first();

                                if(!$supp){
                                    $supp                       = new Supplier();
                                    $supp->nama                 = $nama;
                                    $supp->netsuite_internal_id = "";
                                    $supp->alamat               = "";
                                    $supp->kategori             = "";
                                    $supp->telp                 = "";
                                    $supp->peruntukan           = "EBA";
                                    $supp->kode                 = $line[2];
                                    $supp->wilayah              = "";
                                    $supp->key                  = AppKey::generate();
    
                                    $supp->save();
                                    
                                }
                            }


                            $tanggal_potong = date('Y-m-d',strtotime($this->custom_date_format($line[8])));
                            if($tanggal_potong=="1970-01-01" || $line[8]==""){
                                $tanggal_potong = date('Y-m-d', strtotime('tomorrow'));
                            }
    
                            if($line[4]=="Kirim"){
                                $eksepedisi = "kirim";
                            }else{
                                $eksepedisi = "tangkap";
                            }

                            $sc_wilayah = "";

                            $jumlah_po = 1;
                            

                            if($supp){

                                $purchasing = Purchasing::where('no_po', $line[22])->first();

                                if($purchasing){
                                    $update_data = $update_data+1;
                                }

                                if(!$purchasing){
                                    $purchasing = new Purchasing;
                                    $purchasing->status             = $status;
                                    $insert_data = $insert_data+1;
                                }

                                $po_item = Item::where('nama', $line[2])->first();

                                $purchasing->no_po              = $line[22];
                                $purchasing->user_id            = 1;
                                $purchasing->harga_penawaran    = $line[4];
                                $purchasing->harga_deal         = $line[6];
                                $purchasing->company_id         = NULL;
                                $purchasing->supplier_id        = $supp->id;
                                $purchasing->ukuran_ayam        = $selected_ukuran;
                                $purchasing->jumlah_per_mobil   = $selected_jumlah;
                                $purchasing->jumlah_ayam        = $selected_jumlah*$jumlah_po;
                                $purchasing->jenis_ayam         = "broiler";
                                $purchasing->jenis_po           = "PO Non Karkas";
                                $purchasing->type_po            = "PO Non Karkas";
                                $purchasing->item_po            = $po_item->sku ?? "1100000011";
                                $purchasing->type_ekspedisi     = "kirim";
                                $purchasing->key                = AppKey::generate();
                                $purchasing->tanggal_potong     = $tanggal_potong;
                                $purchasing->jumlah_po          = $jumlah_po;

                                $purchasing->save();

                                $exist_item = PurchaseItem::where('purchasing_id', $purchasing->id)->where('item_po', $po_item->sku)->first();
                                if(!$exist_item){
                                    $exist_item = new PurchaseItem();
                                }

                                $exist_item->internal_id_po  = $po_item->id;
                                $exist_item->purchasing_id  = $purchasing->id;
                                $exist_item->item_po        = $po_item->sku;
                                $exist_item->harga          = $line[6];
                                $exist_item->ukuran_ayam    = $po_item->ukuran_ayam;
                                $exist_item->jumlah_do      = $po_item->jumlah_do;
                                $exist_item->jenis_ayam     = $po_item->nama;
                                $exist_item->description    = $po_item->nama ?? "";
                                $exist_item->berat_ayam     = $selected_jumlah;
                                $exist_item->jumlah_ayam    = $selected_jumlah;

                                $exist_item->save();

                                if($log=="on"){
                                    echo "Update PO Item ";
                                    echo $po_item->sku." - ".$selected_jumlah." - ".$selected_jumlah." \n";
                                }
                                
                                $production_update              = false;

                                if($jumlah_po!=$purchasing->jumlah_po){
                                    $production_update          = true;
                                    $purchasing->jumlah_po      = $jumlah_po;
                                }

                                $purchasing->save();

                                $purc_prod = $purchasing->purcprod;

                                if($production_update==true){
                                    if(count($purc_prod)>$jumlah_po){
                                        foreach($purc_prod as $no => $p):
                                            if($no>$jumlah_po){
                                                if($p->sc_status=='2'){
                                                    $p->delete();
                                                }
                                            }
                                        endforeach;
                                    }else if(count($purc_prod)<$jumlah_po){

                                        for ($x = count($purc_prod); $x < $purchasing->jumlah_po; $x++) {

                                            echo "Masuk sini";

                                            $produksi                       =   new Production();
                                            $produksi->no_po                =   $purchasing->no_po;
                                            $produksi->purchasing_id        =   $purchasing->id;
                                            $produksi->sc_alamat_kandang    =   "";
                                            $produksi->sc_wilayah           =   "";
                                            $produksi->prod_tanggal_potong  =   $tanggal_potong ?? NULL;
                                            $produksi->prod_pending         =   0;
                                            $produksi->sc_nama_kandang      =   "";
                                            $produksi->key                  =   AppKey::generate();
                                            $produksi->save();

                                            echo json_encode($produksi);

                                            if($produksi){
                                                $commit = true;
                                            }else{
                                                $commit = false;
                                            }

                                        }
                                    }
                                }else{

                                    for ($x = count($purc_prod); $x < $purchasing->jumlah_po; $x++) {

                                        echo "Masuk sini";

                                        $produksi                       =   new Production();
                                        $produksi->no_po                =   $purchasing->no_po;
                                        $produksi->purchasing_id        =   $purchasing->id;
                                        $produksi->sc_alamat_kandang    =   "";
                                        $produksi->sc_wilayah           =   "";
                                        $produksi->prod_tanggal_potong  =   $tanggal_potong ?? NULL;
                                        $produksi->prod_pending         =   0;
                                        $produksi->sc_nama_kandang      =   "";
                                        $produksi->key                  =   AppKey::generate();
                                        $produksi->save();

                                        echo json_encode($produksi);

                                        if($produksi){
                                            $commit = true;
                                        }else{
                                            $commit = false;
                                        }

                                    }
                                }

                                foreach($purc_prod as $p):

                                    $produksi                       =   $p;
                                    $produksi->no_po                =   $purchasing->no_po;
                                    $produksi->purchasing_id        =   $purchasing->id;
                                    $produksi->sc_alamat_kandang    =   "";
                                    $produksi->sc_wilayah           =   "";
                                    $produksi->prod_tanggal_potong  =   $tanggal_potong ?? NULL;
                                    $produksi->prod_pending         =   0;
                                    $produksi->sc_nama_kandang      =   "";
                                    $produksi->po_jenis_ekspedisi   =   $purchasing->type_ekspedisi;
                                    $produksi->save();

                                    if($produksi){
                                        $commit = true;
                                    }else{
                                        $commit = false;
                                    }

                                endforeach;

                            }

                        }

                    } catch (\Throwable $th) {
                        //throw $th;
                        DB::rollBack();
                        $response['meta']["status"]     =   200;
                        $response['meta']["message"]    =   "GAGAL";
                        echo $th->getMessage();
                    }

                    $no++;
                        
                }

                if($commit==true){
                    DB::commit();
                    $response['meta']["status"]     =   200;
                    $response['meta']["message"]    =   "OK";
                }else{
                    DB::rollBack();
                    $response['meta']["status"]     =   200;
                    $response['meta']["message"]    =   "GAGAL";
                }


                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;

                echo response()->json($response, $response['meta']["status"]). "\n";

        }


        public function custom_date_format($tanggal){
            try {

                //tanggal/bulan/tahun
                //2/1/0
                $split 	  = explode('-', str_replace("/", "-",$tanggal));
                $tgl_system = $split[2] . '-' . $split[1] . '-' . $split[0];
                $tanggal = date('Y-m-d', strtotime($tgl_system));
                return $tanggal;
            } catch (\Throwable $th) {
                return $tanggal;
            }

        }
}

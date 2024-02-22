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
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CrawlPurchasing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlPurchasing:process {--tanggal=} {--log=}';

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

        if($app_crawl=='cgl_cloud'){

            DB::beginTransaction();


            $url        = DataOption::getOption('cloud_url') ?? 'https://muhhusniaziz.com/';
            // $url = "localhost:8000";
            // echo "Crawl Process ".$url."/api/netsuite/get-po";

            $tanggal    = $this->option('tanggal') ?? date('Y-m-d');
            $log        = $this->option('log') ?? "off";
            $subsidiary = env('NET_SUBSIDIARY', 'CGL') ?? "";

            echo "Process ...".$tanggal."...\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/api/netsuite/get-po?tanggal=".$tanggal."&subsidiary=".$subsidiary);
            // curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710',
                'Content-Type: application/json'
            ]);

            $server_output  =   curl_exec($ch);
            $purchasing     = json_decode($server_output);

            if(count($purchasing)<=0){
                $response['meta']["status"]     =   200;
                $response['meta']["message"]    =   "DATA KOSONG";

                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;

                echo response()->json($response, $response['meta']["status"]). "\n";

                return false;
            }


            foreach($purchasing as $row):

                $now = date('d/m/Y');

                        $status = 2;
                        $commit = false;

                        $selected_ukuran = "";
                        $selected_jumlah = "";

                        if($row->type_po=="PO LB" || $row->type_po=="PO Maklon"){
                            if($row->ukuran_ayam!=null){
                                $selected_ukuran = $row->ukuran_ayam;
                                $selected_jumlah = (float)$row->qty;
                            }else{
                                $selected_ukuran = "1.4 - 1.6";
                                $selected_jumlah = 1920;
                            }
                        }

                        $supp = Supplier::where('netsuite_internal_id', $row->internal_id_vendor)->first();
                        if(!$supp){
                            $supp = Supplier::where('nama', $row->nama_vendor)->first();
                            if(!$supp){
                                $supp                       = new Supplier();
                                $supp->nama                 = $row->nama_vendor;
                                $supp->netsuite_internal_id = $row->internal_id_vendor;
                                $supp->alamat               = $row->alamat;
                                $supp->kategori             = $row->jenis_ekspedisi;
                                $supp->telp                 = $row->no_telp;
                                $supp->peruntukan           = $row->vendor_subsidiary;
                                $supp->kode                 = $row->vendor_subsidiary."-".$row->nama_vendor;
                                $supp->wilayah              = $row->wilayah_vendor;
                                $supp->key                  = AppKey::generate();

                                $supp->save();

                                if($log=="on"){
                                    echo "Vendor insert : ".$purchasing->internal_id_vendor." - ".$row->nama_vendor."\n";
                                }
                                
                            }
                        }

                        $tanggal_potong = date('Y-m-d',strtotime($this->custom_date_format($row->tanggal_kirim)));
                        if($tanggal_potong=="1970-01-01" || $row->tanggal_kirim==""){
                            $tanggal_potong = date('Y-m-d', strtotime('tomorrow'));
                        }

                        $eksepedisi = $row->tipe_ekspedisi ?? "Kirim";
                        $sc_wilayah = "";

                        $jumlah_po = 1;
                        if($row->jumlah_do!=null){
                            $jumlah_po=$row->jumlah_do;
                        }

                        $company_id = 1;
                        if($row->item_subsidiary!=null){
                            $c = Company::where('code', $row->item_subsidiary)->first();
                            if($c){
                                $company_id=$c->id;
                            }
                        }

                        if($supp){

                            $purchasing = Purchasing::where('no_po', $row->document_number)->first();

                            if(!$purchasing){

                                $purchasing = new Purchasing;

                                if($purchasing){
                                    if($log=="on"){
                                        echo "PO insert : ";
                                    }
                                        
                                }

                                $insert_data++;

                            }else{
                                $update_data++;
                                if($log=="on"){
                                    echo "PO Update : ";
                                }
                                

                            }

                            $purchasing->no_po              = $row->document_number;
                            $purchasing->user_id            = 1;
                            $purchasing->harga_penawaran    = $row->rate;
                            $purchasing->harga_deal         = $row->rate;
                            $purchasing->company_id         = $company_id;
                            $purchasing->supplier_id        = $supp->id;
                            $purchasing->ukuran_ayam        = $selected_ukuran;

                            // Jika hanya PO LB
                            if($row->type_po=="PO LB" or $row->type_po=="PO Maklon"){

                                if ($row->sku == "1100000011") {
                                    $purchasing->jenis_ayam         = "broiler";
                                }elseif ($row->sku == "1100000004") {
                                    $purchasing->jenis_ayam         = "kampung";
                                }elseif ($row->sku == "1100000009") {
                                    $purchasing->jenis_ayam         = "parent";
                                }elseif ($row->sku == "1100000005") {
                                    $purchasing->jenis_ayam         = "pejantan";
                                }else{
                                    $purchasing->jenis_ayam         = "broiler";
                                }

                            }else{
                                    $purchasing->jenis_ayam         = "broiler";
                            }

                            $purchasing->internal_id_po     = $row->internal_id_po;
                            $purchasing->jenis_po           = $row->type_po;
                            $purchasing->type_po            = $row->type_po;
                            $purchasing->item_po            = $row->sku;
                            $purchasing->type_ekspedisi     = strtolower($eksepedisi);


                            $purchasing->key                = AppKey::generate();
                            $purchasing->tanggal_potong     = $tanggal_potong;

                            $production_update              = false;

                            if($jumlah_po!=$purchasing->jumlah_po){
                                $production_update          = true;
                                $purchasing->jumlah_po      = $jumlah_po;
                            }
                            
                            if ($purchasing->status != 1) {
                                $purchasing->status     =   $status ;
                            } else {
                                if ($jumlah_po > $purchasing->jumlah_po) {
                                    $purchasing->status     =   $status ;
                                }
                            }
                            $purchasing->save();

                            $purc_prod = $purchasing->purcprod;

                            if($purchasing){
                                if($log=="on"){
                                    echo $purchasing->no_po." - ".$row->nama_vendor." - ".$purchasing->jumlah_po." - ".$purchasing->ukuran_ayam."\n";
                                }
                                    
                            }


                            $alamat_bill = explode("\n",$row->alamat);

                            try {
                                //code...
                                $alamat_wilayah = preg_replace('/\s/', '', $alamat_bill[2]) ?? $row->wilayah_vendor;
                                if ($alamat_wilayah == 'GUNUNGPUYUH,GUNUNGPUYUH,KOTASUKABUMIJAWABARAT') {
                                    $alamat_wilayah = 'BREBES';
                                } else if ($alamat_wilayah == 'SUKABUMI43133') {
                                    $alamat_wilayah = 'SUKABUMI';
                                }
                            } catch (\Throwable $th) {
                                //throw $th;
                                $alamat_wilayah = "NONE";
                            }

                            if($production_update==true){
                                if(count($purc_prod)>$jumlah_po){
                                    foreach($purc_prod as $no => $p):
                                        if($no>$jumlah_po){
                                            if($p->sc_status=='2'){
                                                $p->delete();
                                            }
                                        }
                                    endforeach;

                                    $hasilPengurangan = count($purc_prod) - $jumlah_po;
                                    if ($hasilPengurangan > 0) {
                                        Production::where('purchasing_id', $purchasing->id)->where('sc_status', NULL)->take($hasilPengurangan)->delete();
                                    }
                                }else if(count($purc_prod)<$jumlah_po){
                                    for ($x = count($purc_prod); $x < $purchasing->jumlah_po; $x++) {
                                        $produksi                       =   new Production();
                                        $produksi->no_po                =   $purchasing->no_po;
                                        $produksi->purchasing_id        =   $purchasing->id;
                                        $produksi->sc_alamat_kandang    =   $alamat_wilayah ?? "";
                                        $produksi->sc_wilayah           =   $alamat_wilayah ?? "";
                                        $produksi->prod_tanggal_potong  =   $tanggal_potong ?? NULL;
                                        $produksi->prod_pending         =   0;
                                        $produksi->sc_nama_kandang      =   $row->nama_vendor ?? "";
                                        $produksi->key                  =   AppKey::generate();
                                        $produksi->save();

                                        if($produksi){
                                            $commit = true;
                                        }else{
                                            $commit = false;
                                        }

                                        if($produksi){
                                            if($log=="on"){
                                                echo "produksi insert : ";
                                            }
                                                
                                        }
                                    }
                                }
                            }

                            foreach($purc_prod as $p):

                                $produksi                       =   $p;
                                $produksi->no_po                =   $purchasing->no_po;
                                $produksi->purchasing_id        =   $purchasing->id;
                                $produksi->sc_alamat_kandang    =   $alamat_wilayah ?? "";
                                $produksi->sc_wilayah           =   $alamat_wilayah ?? "";
                                $produksi->prod_tanggal_potong  =   $tanggal_potong ?? NULL;
                                $produksi->prod_pending         =   0;
                                $produksi->sc_nama_kandang      =   $row->nama_vendor ?? "";
                                $produksi->po_jenis_ekspedisi   =   $purchasing->type_ekspedisi;
                                $produksi->save();

                                if($produksi){
                                    $commit = true;
                                }else{
                                    $commit = false;
                                }

                                if($produksi){
                                    if($log=="on"){
                                        echo "produksi update : ".$produksi->id." - ".$row->nama_vendor." - ".$purchasing->jumlah_po." - ".$purchasing->ukuran_ayam."\n";
                                    }
                                }

                            endforeach;

                            try {
                                //code...
                                $purchase_item = json_decode($row->data_item);

                                if(count($purchase_item)>0){

                                    $id_purchase_item = array();

                                    foreach($purchase_item as $po_item):

                                        $exist_item = PurchaseItem::where('purchasing_id', $purchasing->id)->where('item_po', $po_item->sku)->where('internal_id_po', $po_item->line)->first();
                                        if(!$exist_item){
                                            $exist_item = new PurchaseItem();
                                            if($log=="on"){
                                                echo "Insert PO Item ";
                                            }
                                        }

                                        $exist_item->internal_id_po  = $po_item->line;
                                        $exist_item->purchasing_id  = $purchasing->id;
                                        $exist_item->item_po        = $po_item->sku;
                                        $exist_item->harga          = $po_item->rate;
                                        $exist_item->ukuran_ayam    = $po_item->ukuran_ayam;
                                        $exist_item->jumlah_do      = $po_item->jumlah_do;
                                        $exist_item->jenis_ayam     = $po_item->jenis_ayam;
                                        $exist_item->description    = $po_item->keterangan ?? "";
                                        $exist_item->keterangan     = $po_item->name ?? "";
                                        $exist_item->berat_ayam     = $po_item->qty;
                                        $exist_item->jumlah_ayam    = $po_item->qty_pcs;

                                        $exist_item->save();

                                        if($log=="on"){
                                            echo "Update PO Item ";
                                            echo $po_item->sku." - ".$po_item->qty." - ".$po_item->qty_pcs." \n";
                                        }

                                        $id_purchase_item[] = $exist_item->id;

                                    endforeach;

                                }

                                try {
                                    //code...
                                    $deleted_order_item = PurchaseItem::where('purchasing_id', $purchasing->id)->whereNotIn('id', $id_purchase_item)->get();
                                    foreach($deleted_order_item as $d):
                                        if($log=="on"){
                                            echo "deleted :".$d->id."\n";
                                        }
                                        $d->delete();
                                    endforeach;
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }


                                // Set auto grading/abf/etc
                                $this->set_po_nonlb($purchasing->id);

                            } catch (\Throwable $th) {
                                //throw $th;
                                echo $th->getMessage()."\n";
                                echo "Tidak ada data item \n";
                            }

                        }

                    endforeach;


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


        }else{


            $ukuran = ["<1.1","20 Up","1.1 - 1.3","1.2 - 1.4", "1.3 - 1.5","1.4 - 1.6","1.5 - 1.7","1.6 - 1.8","1.8 - 2.0","1.9 - 2.1","2.0 - 2.2"];

            $jumlah = [2500,1500,2500,2200,2000,1920,1920, 1920,1500,1500,1500];

            DB::beginTransaction();

            $file   = "https://docs.google.com/spreadsheets/d/e/2PACX-1vRb82mXIYlJz8nEmnK0dKeJgd4zjSQGbFpyHy9eUIYlTkUDaTV5AghWN2JEG58aq5J4oBnEud_ShlLb/pub?output=csv";

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

                            $selected_ukuran = "";
                            $selected_jumlah = "";
                            if($line[11]!=null){
                                $pos = array_search($line[11], $ukuran);
                                $selected_ukuran = $ukuran[$pos];
                                $selected_jumlah = $jumlah[$pos];
                            }

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


                            $tanggal_potong = date('Y-m-d',strtotime($this->custom_date_format($line[6])));
                            if($tanggal_potong=="1970-01-01" || $line[6]==""){
                                $tanggal_potong = date('Y-m-d', strtotime('tomorrow'));
                            }
    
                            if($line[4]=="Kirim"){
                                $eksepedisi = "kirim";
                            }else{
                                $eksepedisi = "tangkap";
                            }

                            $sc_wilayah = "";

                            $jumlah_po = 0;
                            if($line[5]!=null){
                                $jumlah_po=(integer)$line[5];
                            }

                            if($supp){

                                $purchasing = Purchasing::where('no_po', $line[12])->first();

                                if($purchasing){
                                    $update_data = $update_data+1;
                                }

                                if(!$purchasing){
                                    $purchasing = new Purchasing;
                                    $purchasing->status             = $status;
                                    $insert_data = $insert_data+1;
                                }

                                $purchasing->no_po              = $line[12];
                                $purchasing->user_id            = 1;
                                $purchasing->harga_penawaran    = $line[4];
                                $purchasing->harga_deal         = $line[4];
                                $purchasing->company_id         = NULL;
                                $purchasing->supplier_id        = $supp->id;
                                $purchasing->ukuran_ayam        = $selected_ukuran;
                                $purchasing->jumlah_per_mobil   = $selected_jumlah;
                                $purchasing->jumlah_ayam        = $selected_jumlah*$jumlah_po;
                                $purchasing->jenis_ayam         = "broiler";
                                $purchasing->jenis_po           = "PO LB";
                                $purchasing->type_po            = "PO LB";
                                $purchasing->item_po            = "1100000011";
                                $purchasing->type_ekspedisi     = "kirim";
                                $purchasing->key                = AppKey::generate();
                                $purchasing->tanggal_potong     = $tanggal_potong;
                                $purchasing->jumlah_po          = $jumlah_po;

                                $purchasing->save();

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


        public function set_po_nonlb($purchasing_id){

            $purchasing = Purchasing::find($purchasing_id);
            if($purchasing){

                $tipe   =   0 ;
                foreach($purchasing->purchasing_item as $no => $itm){
                    $tipe   +=  stripos($itm->jenis_ayam, 'FROZEN') ;
                }

                if($purchasing->type_po == "PO Karkas"){
                 
                    foreach($purchasing->purcprod as $row){

                        $production_id   =   $row->id;

                        if ($tipe > 0){
                            $tujuan          =   'abf';
                        }else if($tipe==0){
                            $tujuan          =   'grading';
                        }

                        $prod            =   Production::find($production_id);
                
                        if($prod->ppic_acc != "2"){

                            if ($tujuan == 'evis') {
                                $prod->grading_status   =   1;
                                $prod->evis_status      =   null;
                                $prod->lpah_status      =   1;
                                $prod->ppic_acc         =   2;
                                $prod->ppic_tujuan      =   $tujuan;
                            } elseif ($tujuan == 'grading') {
                                $prod->grading_status   =   null;
                                $prod->evis_status      =   1;
                                $prod->lpah_status      =   1;
                                $prod->ppic_acc         =   2;
                                $prod->ppic_tujuan      =   $tujuan;
                            } elseif ($tujuan == 'abf') {
                                $prod->grading_status   =   1;
                                $prod->evis_status      =   1;
                                $prod->lpah_status      =   1;
                                $prod->ppic_acc         =   2;
                                $prod->ppic_tujuan      =   $tujuan;
                            } elseif ($tujuan == 'chiller') {
                                $prod->grading_status   =   1;
                                $prod->evis_status      =   1;
                                $prod->lpah_status      =   1;
                                $prod->ppic_acc         =   2;
                                $prod->ppic_tujuan      =   $tujuan;
                            }

                            $prod->sc_status            =   1 ;
                            // $prod->no_urut              =   Production::nomor_urut_nolb($request->tanggal) ;
                            $prod->sc_tanggal_masuk     =   Carbon::now();
                            $prod->sc_jam_masuk         =   Carbon::now();
                            $prod->sc_hari              =   date('l');
                            $prod->save();
                    
                            $purchase           =   Purchasing::find($prod->purchasing_id) ;
                            $purchase->status   =   1 ;
                            $purchase->save() ;

                        }

                    }

                }

            }
        }
}
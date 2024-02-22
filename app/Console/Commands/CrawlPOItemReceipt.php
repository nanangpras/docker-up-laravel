<?php

namespace App\Console\Commands;

use App\Models\Adminedit;
use App\Models\AppKey;
use App\Models\Category;
use App\Models\DataOption;
use App\Models\Item;
use App\Models\Pembelianheader;
use App\Models\PembelianItemReceipt;
use App\Models\Pembelian;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\Request;

class CrawlPOItemReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlPOItemReceipt:process {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activity item po item receipt';

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

            $url = DataOption::getOption('cloud_url') ?? 'https://muhhusniaziz.com/';

            $tanggal    = $this->option('tanggal') ?? date('Y-m-d');
            $log        = $this->option('log') ?? "off";
            $subsidiary = env('NET_SUBSIDIARY') ?? "";
            echo "Process ...".$tanggal."...\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/api/netsuite/get-po-item-receipt?tanggal=".$tanggal."&subsidiary=".$subsidiary);
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

            $server_output      =   curl_exec($ch);
            $item             = json_decode($server_output);

            if(count($item)<=0){
                $response['meta']["status"]     =   200;
                $response['meta']["message"]    =   "DATA KOSONG";

                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;

                echo response()->json($response, $response['meta']["status"]). "\n";

                return false;
            }

            foreach($item as $row):

                if($log=="on"){
                    echo "No PO Item ";
                    echo $row->document_number." \n";
                }

                $pembelian_header   = Pembelianheader::where('document_number', $row->document_number)->first();

                if($pembelian_header){


                    if($pembelian_header->keterangan==$row->status_po){

                    }else{

                        // Data PR
                        $pembelian             = Pembelian::find($pembelian_header->pembelian_id);
                        // LOG
                        if($pembelian){
                            $log               =   new Adminedit ;
                            $log->data         =   json_encode($row);
                            $log->table_name   =   'pembelian' ;
                            $log->table_id     =   $pembelian->id ;
                            $log->activity     =   'pr' ;
                            $log->status       =   1 ;
                            $log->content      =   $pembelian_header->document_number." ".$row->status_po;
                            $log->type         =   'input' ;
                            $log->save();
                        }

                        $pembelian_header->keterangan = $row->status_po;
                        $pembelian_header->save();
                    }

                    try {
                        //code...
                        $purchase_item = json_decode($row->data_item);

                        if(count($purchase_item)>0){

                            foreach($purchase_item as $po_item):


                                $item = Item::where('sku', $po_item->sku)->first();

                                $exist_item = PembelianItemReceipt::where('pembelian_header_id', $pembelian_header->id)
                                                                    ->where('no_po', $pembelian_header->document_number)
                                                                    ->where('item_id', $item->id)
                                                                    ->where('line_id', $po_item->line)
                                                                    ->first();

                                $content = "";

                                if(!$exist_item){
                                    $exist_item = new PembelianItemReceipt();
                                    $insert_data++;
                                    if($po_item->item_receipt_qty>0){
                                        $content    =   $item->nama." (".$po_item->item_receipt_qty.")" ;
                                        $tipe       =   "tambah" ;
                                    }
                                    if($log=="on"){
                                        echo "Insert PO Item ".$content;
                                    }
                                }else{
                                    
                                    if($po_item->item_receipt_qty>0){
                                        $update_data++;
                                        $content    =   "UPDATE ITEM RECEIPT " . $item->nama." (".$po_item->item_receipt_qty.")" ;
                                        $tipe       =   "update" ;
                                    }
                                    if($log=="on"){
                                        echo "Update PO Item ".$content;
                                    }
                                }

                                $exist_item->no_po                  = $pembelian_header->document_number;
                                $exist_item->pembelian_header_id    = $pembelian_header->id;
                                $exist_item->line_id                = $po_item->line;
                                $exist_item->item_id                = $item->id ?? NULL;
                                $exist_item->qty                    = $po_item->item_receipt_qty;

                                $exist_item->save();

                                // Data PR
                                $pembelian             = Pembelian::find($pembelian_header->pembelian_id);
                                // LOG
                                if($pembelian){

                                    if($content!=""){
                                        $log               = Adminedit::where('table_name', 'pembelian')
                                                                        ->where('table_id', $pembelian->id)
                                                                        ->where('activity', 'pr')
                                                                        ->where('type', $tipe)
                                                                        ->where('content', $content)
                                                                        ->first();
                                        if(!$log){
                                            $log               =   new Adminedit ;
                                            $log->data         =   json_encode($exist_item);
                                            $log->table_name   =   'pembelian' ;
                                            $log->table_id     =   $pembelian->id ;
                                            $log->activity     =   'pr' ;
                                            $log->status       =   1 ;
                                            $log->content      =   $content;
                                            $log->type         =   $tipe ;
                                            $log->save();
                                        }
                                    }
                                }

                                if($log=="on"){
                                    echo " Log ";
                                    echo $po_item->sku." - ".$po_item->qty." - ".$po_item->item_receipt_qty." \n";
                                }

                            endforeach;

                        }

                    } catch (\Throwable $th) {
                        //throw $th;
                        echo $th->getMessage()."\n";
                        echo "Tidak ada data item \n";
                    }

                    if($log=="on"){
                        echo $pembelian_header->id." - ".$pembelian_header->document_number." - ".$pembelian_header->netsuite_internal_id." - ".$pembelian_header->no_pr."\n";
                    }
                }


            endforeach;

            $response['meta']["status"]             =   200;
            $response['meta']["message"]            =   "OK";
            $response['response']["data_insert"]    =   $insert_data;
            $response['response']["data_update"]    =   $update_data;

            echo response()->json($response, $response['meta']["status"]) . "\n";


    }
}



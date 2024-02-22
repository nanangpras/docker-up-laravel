<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\DataOption;
use App\Models\Item;
use App\Models\MarketingSO;
use App\Models\MarketingSOList;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Console\Command;

class CrawlMarketingSO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlMarketingSO:process {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl Marketing SO';

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

            $url = 'http://8.219.1.73:8082';

            $tanggal    = $this->option('tanggal') ?? date('Y-m-d');
            $log        = $this->option('log') ?? "off";
            $subsidiary = env('NET_SUBSIDIARY', 'CGL') ?? "";
            echo "Process ...".$tanggal."...\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/api/marketing/get-marketing-so?tanggal=".$tanggal."&subsidiary=".$subsidiary);
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

            $server_output      = curl_exec($ch);
            $so                 = json_decode($server_output);
            
            try {
                //code...
                if(!$so){
                    $response['meta']["status"]     =   200;
                    $response['meta']["message"]    =   "DATA KOSONG";
    
                    $response['response']["data_insert"]    =   $insert_data;
                    $response['response']["data_update"]    =   $update_data;
    
                    echo response()->json($response, $response['meta']["status"]). "\n";
    
                    return false;
                }
            } catch (\Throwable $th) {
                //throw $th;
                echo $th->getMessage();
                return false;
            }

            foreach($so as $row):

                $header = MarketingSO::where('id', $row->id)->first();
                if(!$header){
                    $header                         =   new MarketingSO() ;
                }
                
                $header->id                     =   $row->id ;
                $header->tanggal_so             =   $row->tanggal_so ;
                $header->tanggal_kirim          =   $row->tanggal_kirim ;
                $header->internal_id_customer   =   $row->internal_id_customer ;
                $header->no_so                  =   $row->no_so ;
                $header->customer_id            =   Customer::where('netsuite_internal_id', $row->internal_id_customer)->first()->id ?? $row->customer_id;
                $header->user_id                =   $row->user_id ;
                $header->memo                   =   $row->memo ;
                $header->po_number              =   $row->po_number ;
                $header->subsidiary             =   $row->subsidiary ;
                $header->wilayah                    =   $row->wilayah ;
                $header->department                 =   $row->department ;
                $header->cabang                     =   $row->cabang ;
                $header->gudang                     =   $row->gudang ;
                $header->sales_channel              =   $row->sales_channel ;
                $header->netsuite_status            =   $row->netsuite_status ;
                $header->netsuite_id                =   $row->netsuite_id ;
                $header->send_to_ns                 =   $row->send_to_ns ;
                $header->status                     =   $row->status ;
                $header->edited                     =   $row->status ;
                $header->created_at                 =   $row->created_at ;
                $header->updated_at                 =   $row->updated_at ;
                $header->deleted_at                 =   $row->deleted_at ;
                if($row->netsuite_closed_status=="Closed"){
                    $header->netsuite_closed_status = "Closed";
                }

                $header->save();

                foreach($row->list_item as $so_list){

                    $list   = MarketingSOList::withTrashed()->where('id', $so_list->id)->first();
                    if(!$list){
                        $list                   =   new MarketingSOList() ;
                    }

                    $list->id =   $so_list->id ;
                    $list->line_id =   $so_list->line_id ;
                    $list->marketing_so_id =   $so_list->marketing_so_id ;
                    $list->item_id =   $so_list->item_id ;
                    $list->item_nama =   $so_list->item_nama ;
                    $list->tax_code =   $so_list->tax_code ;
                    $list->parting =   $so_list->parting ;
                    $list->plastik =   $so_list->plastik ;
                    $list->bumbu =   $so_list->bumbu ;
                    $list->memo =   $so_list->memo ;
                    $list->internal_memo =   $so_list->internal_memo ;
                    $list->qty =   $so_list->qty ;
                    $list->berat =   $so_list->berat ;
                    $list->rate =   $so_list->rate ;
                    $list->harga =   $so_list->harga ;
                    $list->harga_cetakan =   $so_list->harga_cetakan ;
                    $list->department_item =   $so_list->department_item ;
                    $list->cabang_item =   $so_list->cabang_item ;
                    $list->gudang_item =   $so_list->gudang_item ;
                    $list->description_item =   $so_list->description_item ;
                    $list->sales_channel_item =   $so_list->sales_channel_item ;
                    $list->status =   $so_list->status ;
                    $list->edited =   $so_list->edited ;
                    $list->created_at =   $so_list->created_at ;
                    $list->updated_at =   $so_list->updated_at ;
                    $list->deleted_at =   $so_list->deleted_at ;
                    // if($so_list->status_so=="Closed"){
                    //     $list->netsuite_closed_status = "Closed";
                    // }
                    $list->save();
                    
                }

                $update_data++;

            endforeach;

            $response['meta']["status"]             =   200;
            $response['meta']["message"]            =   "OK";
            $response['response']["data_insert"]    =   $insert_data;
            $response['response']["data_update"]    =   $update_data;

            echo response()->json($response, $response['meta']["status"]) . "\n";
    }
}

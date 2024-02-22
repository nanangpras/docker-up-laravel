<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Category;
use App\Models\DataOption;
use App\Models\Gudang;
use App\Models\Item;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\Request;

class CrawlBOM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlBOM:process  {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activity BOM crawl';

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
            // $url = "localhost:8000";
            // echo "Crawl Process ".$url."/api/netsuite/get-po";
            
            $tanggal = $this->option('tanggal') ?? date('Y-m-d');
            $log = $this->option('log') ?? "off";
            echo "Process ...".$tanggal."...\n";

            $subsidiary = env('NET_SUBSIDIARY') ?? "";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/api/netsuite/get-bom?tanggal=".$tanggal."&subsidiary=".$subsidiary);
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
            $bom     = json_decode($server_output);
            
            if(count($bom)<=0){
                $response['meta']["status"]     =   200;
                $response['meta']["message"]    =   "DATA KOSONG";

                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;

                echo response()->json($response, $response['meta']["status"]). "\n";

                return false;
            }

            foreach($bom as $row):

                $data = Bom::where('netsuite_internal_id', $row->internal_id_bom)->first();

                if($data){

                }else{

                    $data = Bom::where('bom_name', $row->bom_name)->first();
                    if(!$data){
                        $data = new Bom();
                    }
                }

                $data->bom_name             = $row->bom_name;
                $data->netsuite_internal_id = $row->internal_id_bom;
                $data->bom_desc             = $row->memo;

                $data->save();

                $data_item                  = json_decode($row->data_item);

                $array_item = [];
                foreach($data_item as $b_item):

                    $item                       = Item::where('sku', $b_item->sku)->first();

                    if($item){

                        $bom_item               = BomItem::where('bom_id', $data->id)->where('sku', $b_item->sku)->first();
                        if($bom_item){
                            
                            if($log=="on"){
                                echo "item update : ".$data->netsuite_internal_id." ".$data->bom_name." ".$bom_item->sku."\n";
                            }

                        }else{
                            $bom_item                               = new BomItem();

                            if($log=="on"){
                                echo "item insert : ".$data->netsuite_internal_id." ".$data->bom_name." ".$bom_item->sku."\n";
                            }
                        }

                            $array_item[] = $bom_item->id;

                            $bom_item->bom_id                       = $data->id;
                            $bom_item->item_id                      = $item->id;
                            $bom_item->sku                          = $b_item->sku;
                            $bom_item->level                        = "1";
                            $bom_item->kategori                     = $b_item->type;
                            $bom_item->component_yield              = NULL;
                            $bom_item->bom_qty_per_assembly         = $b_item->qty;
                            $bom_item->qty_per_assembly             = $b_item->qty;
                            $bom_item->qty_per_top_level_assembly   = $b_item->qty;
                            $bom_item->save();

                    }

                endforeach;

                $deleted_bom_item = BomItem::where('bom_id', $data->id)->whereNotIn('id', $array_item)->get();
                foreach($deleted_bom_item as $d):
                    if($log=="on"){
                        echo "deleted :".$d->id."\n";
                    }
                    $d->delete();
                endforeach;
                

            endforeach;

            $response['meta']["status"]             =   200;
            $response['meta']["message"]            =   "OK";

            echo response()->json($response, $response['meta']["status"]) . "\n";

    }
}



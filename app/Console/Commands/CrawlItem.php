<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\Category;
use App\Models\DataOption;
use App\Models\Item;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\Request;

class CrawlItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlItem:process {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activity item crawl';

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
        $error_data    =   0;
        $commit         = false;

            $url = DataOption::getOption('cloud_url') ?? 'https://muhhusniaziz.com/';

            $tanggal    = $this->option('tanggal') ?? date('Y-m-d');
            $log        = $this->option('log') ?? "off";
            $subsidiary = env('NET_SUBSIDIARY') ?? "";
            echo "Process ...".$tanggal."...\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/api/netsuite/get-item?tanggal=".$tanggal."&subsidiary=".$subsidiary);
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

                try {
                    //code...
                    $data = Item::where('netsuite_internal_id', $row->internal_id_item)->withTrashed()->first();

                    if($data){
                        $update_data++;
                    }else{
                        
                        $data = Item::where('sku', $row->sku)->withTrashed()->first();
                        if(!$data){
                            $insert_data++;
                            $data                       = new Item();
                            $data->id                   = $row->app_id ?? NULL;

                        }else{
                            $update_data++;
                        }
                    }

                    $category               = Category::where('nama', $row->category_item)->first();

                    if ($category) {
                        $data->category_id  = $category->id ?? "23";
                    } else {
                        $category           = new Category();
                        $category->nama     = $row->category_item;
                        $category->slug     = strtolower(str_replace(" ", "-", preg_replace('/[^A-Za-z0-9 !@#$%^&*()-.]/u','', strip_tags(strtolower(str_replace(" ", "-", $row->category_item))))));
                        $category->save();

                        $data->category_id  = $category->id ?? "23";
                    }


                    $data->nama                 = $row->nama_item;
                    $data->nama_alias           = $row->nama_item;
                    $data->berat_kali           = $row->faktor_kali_berat;
                    $data->sku                  = $row->sku ?? NULL;
                    $data->code_item            = $row->subsidiary ?? NULL;
                    $data->subsidiary           = $row->subsidiary ?? NULL;
                    $data->tax_code             = $row->tax_code ?? NULL;
                    $data->tax_code_id          = $row->tax_code_id ?? NULL;
                    $data->tax_rate             = $row->tax_rate ?? NULL;

                    if($data->type==$row->stock_unit){
                        if ($row->stock_unit == "") {
                            $data->type                 = "N/A";
                        }
                    }else{
                        if($row->stock_unit==""){
                            $data->type                 = "N/A";
                        }else{
                            $data->type                 = $row->stock_unit ?? "N/A";
                        }
                    }
                    $data->netsuite_internal_id = $row->internal_id_item;
                    $data->status               = $row->inactive == 0 ? 1 : 0;

                    $data->save();

                    if($log=="on"){
                        echo $data->id." - ".$data->sku." - ".$data->netsuite_internal_id." - ".$data->nama."\n";
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                    echo $th->getMessage()."\n";
                    $error_data++;
                }

            endforeach;

            $response['meta']["status"]             =   200;
            $response['meta']["message"]            =   "OK";
            $response['response']["data_insert"]    =   $insert_data;
            $response['response']["data_update"]    =   $update_data;
            $response['response']["error_data"]     =   $error_data;

            echo response()->json($response, $response['meta']["status"]) . "\n";


    }
}



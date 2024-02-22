<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\Category;
use App\Models\DataOption;
use App\Models\Gudang;
use App\Models\Item;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\Request;

class CrawlLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlLocation:process {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activity location crawl';

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

        $no             = 0;
        $no_saved       = 0;
        $no_unsaved     = 0;
        $no_updated     = 0;

        if($app_crawl=='cgl_cloud'){


            $tanggal = $this->option('tanggal') ?? date('Y-m-d');
            $log = $this->option('log') ?? "off";
            echo "Process ...".$tanggal."...\n";

            $url = DataOption::getOption('cloud_url') ?? 'https://muhhusniaziz.com/';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/api/netsuite/get-location?tanggal=".$tanggal);
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
            $location           = json_decode($server_output);

            foreach($location as $row):

                try{

                    $data = Gudang::where('code', $row->nama_location)->first();

                    if($data){

                        $data->code                 = $row->nama_location;
                        $data->netsuite_internal_id = $row->internal_id_location ?? NULL;
                        $data->kategori             = $row->kategori ?? NULL;
                        $data->subsidiary_id        = $row->subsidiary_id ?? NULL;
                        $data->subsidiary           = $row->subsidiary ?? NULL;
                        $data->status               = $row->status == 0 ? 1 : 0;

                        $data->save();
                        $no_updated++;

                        if($log=="on"){
                            echo "update : ".$data->netsuite_internal_id." ".$data->code."\n";
                        }

                    }else{

                        $data                       = new Gudang();
                        $data->code                 = $row->nama_location;
                        $data->netsuite_internal_id = $row->internal_id_location ?? NULL;
                        $data->kategori             = $row->kategori ?? NULL;
                        $data->subsidiary_id        = $row->subsidiary_id ?? NULL;
                        $data->subsidiary           = $row->subsidiary ?? NULL;
                        $data->status               = $row->status == 0 ? 1 : 0;

                        $data->save();

                        $no_saved++;

                        if($log=="on"){
                            echo "insert : ".$data->netsuite_internal_id." ".$data->code."\n";
                        }

                    }

                }catch(\Throwable $th){
                    $no_unsaved++;
                }

            endforeach;
        }else{

            $file = "https://docs.google.com/spreadsheets/d/e/2PACX-1vSzagnkDwZribPOSzCZMLT2n0Zy7b-eUhksRF9HvEV0XU4bwzjGrM7tVg_KvWw5c_PSU7WnYh6a3zwN/pub?output=csv";

            $fileData=fopen($file,'r');


            while (($line = fgetcsv($fileData)) !== FALSE) {

                if($no!=0){

                    try{

                        $data = Gudang::where('code', $line[1])->first();

                        if($data){

                            $data->code                 = $line[1];
                            $data->netsuite_internal_id = $line[0] ?? NULL;

                            $data->save();
                            $no_updated++;

                            echo "update : ".$data->netsuite_internal_id." ".$data->code."\n";

                        }else{

                            $data                       = new Gudang();
                            $data->code                 = $line[1];
                            $data->netsuite_internal_id = $line[0] ?? NULL;

                            $data->save();

                            $no_saved++;

                            echo "insert : ".$data->netsuite_internal_id." ".$data->code."\n";

                        }

                    }catch(\Throwable $th){
                        $no_unsaved++;
                    }

                }
                $no++;
            }

            fclose($fileData);

        }

        $response['meta']["status"]             =   200;
        $response['meta']["message"]            =   "OK";
        $response['response']["data_insert"]    =   $no_saved;
        $response['response']["data_update"]    =   $no_updated;
        $response['response']["data_failed"]    =   $no_unsaved;

        echo response()->json($response, $response['meta']["status"]) . "\n";

        $data = response()->json($response, $response['meta']["status"]);

        $logs = array(
            'time'  => date("F j, Y, H:i:s"),
            'data'  => $data
        );

        $log = json_encode($logs);

    }
}



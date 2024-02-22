<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\DataOption;
use App\Models\Supplier;
use App\Models\Supplier_address;
use Illuminate\Console\Command;

class CrawlVendor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlVendor:process {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activity vendor crawl';

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
        $update_data_address    =   0;
        $insert_data_address    =   0;
        $commit         = false;

            $url = DataOption::getOption('cloud_url') ?? 'https://muhhusniaziz.com/';

            $tanggal    = $this->option('tanggal') ?? date('Y-m-d');
            $log        = $this->option('log') ?? "off";
            $subsidiary = env('NET_SUBSIDIARY') ?? "";
            echo "Process ...".$tanggal."...\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/api/netsuite/get-vendor?tanggal=".$tanggal."&subsidiary=".$subsidiary);
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
            $vendor             = json_decode($server_output);
            
            if(count($vendor)<=0){
                $response['meta']["status"]     =   200;
                $response['meta']["message"]    =   "DATA KOSONG";

                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;

                echo response()->json($response, $response['meta']["status"]). "\n";

                return false;
            }

            foreach($vendor as $row):

                $data = Supplier::where('netsuite_internal_id', $row->internal_id_vendor)->withTrashed()->first();

                if($data){
                    $update_data++;
                }else{
                    
                    $data = Supplier::where('nama', $row->nama_vendor)->where('peruntukan', $row->subsidiary)->withTrashed()->first();
                    if(!$data){
                        $insert_data++;
                        $data = new Supplier();
                    }else{
                        $update_data++;
                    }
                }

                $data->nama                 = $row->nama_vendor;
                $data->wilayah              = $row->wilayah_vendor;
                $data->peruntukan           = $row->subsidiary;
                $data->entityid             = $row->entityid;
                $data->netsuite_internal_id = $row->internal_id_vendor;

                $data->save();

                $data_alamat                = json_decode($row->data_alamat);

                if($log=="on"){
                    echo $data->id." - ".$data->kode." - ".$data->netsuite_internal_id." - ".$data->nama." || Alamat : ".count($data_alamat)."\n";
                }

                foreach($data_alamat as $b_alamat):

                    $v_alamat                       = Supplier_address::where('netsuite_internal_id', $b_alamat->internal_id_alamat_vendor)->first();

                    if(!$v_alamat){
                        $v_alamat = new Supplier_address();
                        $insert_data_address++;
                    }else{
                        $update_data_address++;
                    }

                    $v_alamat->nama                     = $row->nama_vendor;
                    $v_alamat->alamat                   = $b_alamat->alamat_vendor;
                    $v_alamat->netsuite_internal_id     = $b_alamat->internal_id_alamat_vendor;
                    $v_alamat->supplier_id              = $data->id;
                    $v_alamat->save();

                endforeach;
                

            endforeach;

            $response['meta']["status"]             =   200;
            $response['meta']["message"]            =   "OK";
            $response['response']["data_insert"]    =   $insert_data;
            $response['response']["data_update"]    =   $update_data;
            $response['response']["data_insert_address"]    =   $insert_data_address;
            $response['response']["data_update_address"]    =   $update_data_address;

            echo response()->json($response, $response['meta']["status"]) . "\n";

    }
}

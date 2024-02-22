<?php

namespace App\Console\Commands;

use App\Models\DataOption;
use App\Models\MarketingSO;
use Illuminate\Console\Command;

class CrawlMarketingSOStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlMarketingSOStatus:process {--tanggal=} {--log=} {--range}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $no             =   0;
        $update_data    =   0;
        $insert_data    =   0;

        $app_crawl = env("APP_CRAWL", "gsheet");

            $url        = DataOption::getOption('cloud_url') ?? 'https://muhhusniaziz.com/';
            // echo "Crawl Process ".$url."/api/netsuite/get-location";
            $tanggal    = $this->option('tanggal') ?? date('Y-m-d');
            $log        = $this->option('log') ?? "off";
            $range      = $this->option('range') ?? "";
            $subsidiary = env('NET_SUBSIDIARY') ?? "";
            echo "Process ".$tanggal."...\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/api/netsuite/get-so?tanggal=".$tanggal."&range=".$range."&subsidiary=".$subsidiary);
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
            
            $so             = json_decode($server_output);
            
            //if(count($so) > 0){
            // $cek            = array();
            // foreach($so as $duplicate){
            //     $cek[] = array(
            //         'netsuite_log_id'   => $duplicate->netsuite_log_id,
            //         'status_so'         => $duplicate->status_so 
            //     );
            // }
            // }

            // dd($cek);

            if(count($so)<=0){
                $response['meta']["status"]     =   200;
                $response['meta']["message"]    =   "DATA KOSONG";

                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;

                echo response()->json($response, $response['meta']["status"]). "\n";

                return false;
            }

            foreach($so as $row):

                    $marketing_so = MarketingSO::where('no_so', $row->nomor_so)->first();
                    if($marketing_so){

                        if($marketing_so->status!="3" && $marketing_so->status!="0" && $row->status_so!="Closed"){
                            $marketing_so->status = 3;
                            $marketing_so->verified += 1;
                            $marketing_so->save();
                            $update_data++;
                        }


                        if($row->status_so=="Closed"){
                            $marketing_so->netsuite_closed_status = "Closed";
                            $marketing_so->save();
                            $update_data++;
                        }
                    }

            endforeach;

        
        $response['meta']["status"]             =   200;
        $response['meta']["message"]            =   "OK";
        $response['response']["data_insert"]    =   $insert_data;
        $response['response']["data_exist"]     =   $update_data;

        echo response()->json($response, $response['meta']["status"]) . "\n";
    }
}

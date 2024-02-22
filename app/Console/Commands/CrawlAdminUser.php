<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\Category;
use App\Models\DataOption;
use App\Models\Item;
use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\HttpFoundation\Request;

class CrawlAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlAdminUser:process {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activity user crawl';

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


        $update_data    =   0;
        $insert_data    =   0;
        $commit         = false;

            $url = DataOption::getOption('cloud_url') ?? 'https://muhhusniaziz.com/';

            $tanggal    = $this->option('tanggal') ?? date('Y-m-d');
            $log        = $this->option('log') ?? "off";
            $subsidiary = env('NET_SUBSIDIARY', 'CGL') ?? "";
            echo "Process ...".$tanggal."...\n";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url."/api/master/get-user?tanggal=".$tanggal."&subsidiary=".$subsidiary);
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
            $user             = json_decode($server_output);
            
            if(count($user)<=0){
                $response['meta']["status"]     =   200;
                $response['meta']["message"]    =   "DATA KOSONG";

                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;

                echo response()->json($response, $response['meta']["status"]). "\n";

                return false;
            }

            foreach($user as $row):

                $usr = User::where('email',$row->email)->first();

                if(!$usr){
                    $usr                   =   new User ;
                }

                echo $usr->email."\n";

                $usr->id               =   $row->id ;
                $usr->name             =   $row->name ;
                $usr->email            =   $row->email ;
                $usr->company_id       =   $row->company_id ;
                $usr->subsidiary       =   $row->subsidiary ;
                $usr->password         =   $row->password ;
                $usr->account_role     =   $row->account_role ;
                $usr->account_type     =   $row->account_type ;
                $usr->group_role       =   $row->group_role ;
                $usr->status           =   $row->status ;

                try {
                    //code...
                    $usr->save() ;
                } catch (\Throwable $th) {
                    //throw $th;
                    echo $th->getMessage()."\n";
                }
                

                if($log=="on"){
                    echo $usr->id." - ".$usr->nama." - ".$usr->netsuite_internal_id." - ".$usr->subsidiary."\n";
                }

            endforeach;

            $response['meta']["status"]             =   200;
            $response['meta']["message"]            =   "OK";
            $response['response']["data_insert"]    =   $insert_data;
            $response['response']["data_update"]    =   $update_data;

            echo response()->json($response, $response['meta']["status"]) . "\n";


    }
}



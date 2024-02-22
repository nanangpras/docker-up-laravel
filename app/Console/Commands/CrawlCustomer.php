<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\Customer;
use App\Models\Customer_address;
use App\Models\DataOption;
use App\Models\Marketing;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CrawlCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlCustomer:process {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activity customer crawl';

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
            curl_setopt($ch, CURLOPT_URL, $url."/api/netsuite/get-customer?tanggal=".$tanggal."&subsidiary=".$subsidiary);
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
            $customer           = json_decode($server_output);
            
            if(count($customer)<=0){
                $response['meta']["status"]     =   200;
                $response['meta']["message"]    =   "DATA KOSONG";

                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;

                echo response()->json($response, $response['meta']["status"]). "\n";

                return false;
            }

            foreach($customer as $row):

                $c                              =   Customer::where('netsuite_internal_id', $row->internal_id_customer)->first();
                if (!$c) {
                    
                    $data_alamat                = json_decode($row->data_alamat);
                    if(count($data_alamat)==0){
                        $is_parent = true;
                        $c                          =   Customer::where('nama', $row->nama_customer)->where('is_parent', '1')->where('kode', 'like', '%'. $row->subsidiary . '%')->first();
                    }else{
                        $is_parent = false;
                        $c                          =   Customer::where('nama', $row->nama_customer)->whereIn('is_parent', [0, NULL])->where('kode', 'like', '%'. $row->subsidiary . '%')->first();
                    }
                        
                    if(!$c){
                        $c                      = new Customer();
                        $insert_data++;
                    }else{
                        $update_data++;
                    }
                }else{
                    $update_data++;
                }

                $count_kode_pecah_parent    = 0;
                $kode_pecah_parent          = (explode(" : ", $row->entityid));
                $count_kode_pecah_parent    = count($kode_pecah_parent);
                $parent_kode                = NULL;

                if($count_kode_pecah_parent>0){
                    $count_kode_pecah_parent = $count_kode_pecah_parent-1;
                    $parent_kode                = (explode(" - ", $kode_pecah_parent[0]))[0] ?? NULL;
                }
                $final_kode                 = (explode(" - ", $kode_pecah_parent[$count_kode_pecah_parent]))[0] ?? NULL;

                $is_parent = false;


                $c->netsuite_internal_id        =   $row->internal_id_customer ?? NULL;
                $c->nama                        =   $row->nama_customer ?? NULL;
                $c->tax_code                    =   $row->tax_code ?? NULL;
                $c->tax_code_id                 =   $row->tax_code_id ?? NULL;
                $c->tax_rate                    =   $row->tax_rate ?? NULL;
                $c->kode                        =   $final_kode;
                $c->kategori                    =   $row->category_customer ?? NULL;
                // $c->status                      =   $row->status ?? NULL;
                
                if($is_parent==false){
                    $parent_id = Customer::where('kode', $parent_kode)->first();
                    if($parent_id){
                        $c->parent_id           =   $parent_id->id ?? NULL;
                    }
                }
                

                if($row->sales_rep_internal_id ?? FALSE){
                    $marketing_id               = Marketing::where('netsuite_internal_id', $row->sales_rep_internal_id)->first();
                    if(!$marketing_id){
                        $marketing_id           = Marketing::where('nama', $row->sales_rep_nama)->first();
                        if(!$marketing_id){
                            $marketing_id       = new Marketing();
                        }
                    }

                    $marketing_id->nama                  = $row->sales_rep_nama;
                    $marketing_id->netsuite_internal_id  = $row->sales_rep_internal_id;
                    $marketing_id->save();


                    $user                   =   User::where('name', $row->sales_rep_nama)->first();
                    if($user){

                    }else{
                        $user                   =   new User ;
                        $user->name             =   $row->sales_rep_nama ;
                        $user->email            =   strtolower(str_replace(" ", "",$row->sales_rep_nama)."@".env('NET_SUBSIDIARY').".com") ;
                        $user->password         =   Hash::make("12345678") ;
                        $user->account_role     =   "admin" ;
                        $user->group_role       =   38 ;
                        $user->netsuite_internal_id       =   113596 ;
                        $user->account_type     =   1 ;
                        $user->status           =   1 ;
                        $user->save() ;
                    }

                    $c->marketing_id    = $marketing_id->id;
                    $c->nama_marketing  = $marketing_id->nama;
                }

                
                
                $data_alamat                = json_decode($row->data_alamat);
                
                if($log=="on"){
                    echo $c->id." - ".$c->kode." - ".$c->netsuite_internal_id." - ".$c->nama." || Alamat : ".count($data_alamat)."\n";
                }

                if(count($data_alamat)==0){
                    $c->is_parent = 1;
                }
                // ELSE NYA BIKIN BUG GA MUNCUL DATA LION SUPERINDO 380
                // else{
                //     $c->is_parent = 0;
                // }

                $c->save();
                
                foreach($data_alamat as $b_alamat):

                    if($b_alamat->internal_id_alamat_customer ?? FALSE){

                        $v_alamat                       = Customer_address::where('netsuite_internal_id', $b_alamat->internal_id_alamat_customer)->first();
                        
                        if(!$v_alamat){
                            $v_alamat                   = new Customer_address();
                            $insert_data_address++;
                        }else{
                            $update_data_address++;
                        }
                        
                        $v_alamat->nama                     = $row->nama_customer;
                        $v_alamat->alamat                   = $b_alamat->alamat_customer;
                        $v_alamat->netsuite_internal_id     = $b_alamat->internal_id_alamat_customer;
                        $v_alamat->wilayah                  = (explode("\n", $b_alamat->internal_id_alamat_customer))[2] ?? NULL;
                        $v_alamat->kota                     = (explode("\n", $b_alamat->internal_id_alamat_customer))[2] ?? NULL;
                        $v_alamat->customer_id              = $c->id;
                        $v_alamat->save();

                    }

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

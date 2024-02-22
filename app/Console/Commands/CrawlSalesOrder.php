<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\Category;
use App\Models\Customer;
use App\Models\DataOption;
use App\Models\Item;
use App\Models\Marketing;
use App\Models\MarketingSO;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Claims\Custom;

class CrawlSalesOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlSalesOrder:process  {--tanggal=} {--log=}  {--range=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activity Sales Order Crawl';

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
            $subsidiary = env('NET_SUBSIDIARY', 'CGL') ?? "";
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

            if(count($so)<=0){
                $response['meta']["status"]     =   200;
                $response['meta']["message"]    =   "DATA KOSONG";

                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;

                echo response()->json($response, $response['meta']["status"]). "\n";

                return false;
            }

            foreach($so as $row):

                if(env('NET_SUBSIDIARY', 'CGL')==$row->so_subsidiary){
                    //Customer
                    $cust                       =   Customer::where('nama', $row->nama_customer)->first();

                    if($cust){
                        if($cust->internal_id_customer==""){
                            $cust->netsuite_internal_id=$row->internal_id_customer;
                            $cust->save();
                        }
                    }

                    if(!$cust){

                        $cust                   =    Customer::where('netsuite_internal_id', $row->internal_id_customer)->first();

                        if(!$cust){
                            $cust                       = new Customer();
                        }
                        
                        $cust->nama             =   $row->nama_customer;
                        $cust->kategori         =   $row->category_customer;
                        $cust->type_customer    =   1;
                        $cust->kategori         =   $row->sales_channel;

                        $marketing_id             = Marketing::where('nama', $row->sales)->first();
                        if($row->sales!=""){
                            if($marketing_id){
                                $cust->marketing_id   =   $marketing_id->id ?? NULL;
                            }else{
                                $marketing_id         = new Marketing;
                                $marketing_id->nama   = $row->sales;
                                $marketing_id->netsuite_internal_id   = $row->id_sales;
                                $marketing_id->save();

                                $cust->marketing_id   =   $marketing_id->id ?? NULL;
                            }

                            $user                   =   User::where('name', $row->sales)->first();
                            if($user){

                            }else{
                                $user                   =   new User ;
                                $user->name             =   $row->sales ;
                                $user->email            =   strtolower(str_replace(" ", "",$row->sales)."@".env('NET_SUBSIDIARY').".com") ;
                                $user->password         =   Hash::make("12345678") ;
                                $user->account_role     =   "admin" ;
                                $user->group_role       =   38 ;
                                $user->netsuite_internal_id       =   113596 ;
                                $user->account_type     =   1 ;
                                $user->status           =   1 ;
                                $user->save() ;
                            }
                        }

                        $cust->key                  = AppKey::generate();

                        $cust->save();

                    }

                    $order                          =   Order::where('no_so', $row->nomor_so)->where('netsuite_internal_id', $row->internal_id_so)->first();
                    $headerMarketingSO              =   MarketingSO::where('no_so', $row->nomor_so)->first();
                    if (!$order) {
                        $order = new Order();
                    }

                    $order->id_so               =   $row->internal_id_so ;
                    $order->netsuite_internal_id               =   $row->internal_id_so ;
                    $order->no_so               =   $row->nomor_so ;
                    $order->no_po               =   $row->nomor_po ;
                    // $order->status_so           =   $row->status_so ?? NULL ;
                    if ($headerMarketingSO) {
                        if($headerMarketingSO->status_so == "Closed") {
                            $order->status_so           =   $headerMarketingSO->netsuite_closed_status ?? $row->status_so ;
                        } else {
                            $order->status_so           =  $row->status_so ?? NULL;
                        }
                    } else {
                        $order->status_so           =  $row->status_so ?? NULL;
                    }
                    $order->nama                =   $row->nama_customer ;

                    $order->customer_id         =   $cust->id ?? NULL;

                    $order->partner             =   $row->customer_partner;
                    $order->tanggal_kirim       =   $this->custom_date_format($row->tanggal_kirim);
                    $order->tanggal_so          =   $this->custom_date_format($row->tanggal_so);
                    $order->alamat              =   $row->alamat_customer_partner;
                    $order->alamat_kirim        =   $row->alamat_ship_to;
                    $wilayah                    =   "";
                    $splitWilayah               =   explode("\n",$row->alamat_ship_to);
                    if($splitWilayah){
                        $order->wilayah         =   $splitWilayah[2] ?? '';
                    }else{
                        $order->wilayah         =   $wilayah;
                    }
                    $order->sales_id            =   $row->id_sales;
                    $order->keterangan          =   $row->memo;
                    $order->sales_channel       =   $row->sales_channel;

                    if ($order->save()) {

                        $item           = json_decode($row->data_item);
                        $jumlah_item    = count($item);
                        if($log=="on"){
                            echo " === SO ".$order->no_so." : Jumlah item ".$jumlah_item."\n";
                        }
                        $inserted_id    = [];

                        foreach($item as $line_id => $it):

                            $line_id = $it->line ?? $line_id;

                            if($it->sku!="PPN 0%"){

                                $order_item             = OrderItem::where('nama_detail', $it->name)->where('line_id', $line_id)->where('order_id', $order->id)->first();
                                
                                $item                   = Item::where('sku', $it->sku)->first();

                                if(!$item){
                                    $item                   = new Item;
                                    $item->nama             = $it->name;
                                    $item->nama_alias       = $it->name;
                                    $item->sku              = $it->sku;
                                    $item->netsuite_internal_id  = $it->internal_id_item;

                                    $category               = Category::where('nama', $row->category_item ?? "")->first();

                                    if($category){
                                        $item->category_id      = $category->id ?? "22";
                                    }else{
                                        $category           = new Category();
                                        $category->nama     = $row->category_item ?? "NONE" ;
                                        $category->slug     = strtolower(str_replace(" ", "-", preg_replace('/[^A-Za-z0-9 !@#$%^&*()-.]/u','', strip_tags(strtolower(str_replace(" ", "-", $row->category_item ?? "NONE"))))));
                                        $category->save();
                                    }
                                    $item->category_id      = $category->id ?? "22";
                                    $item->slug             = strtolower(str_replace(" ", "-", preg_replace('/[^A-Za-z0-9 !@#$%^&*()-.]/u','', strip_tags(strtolower(str_replace(" ", "-", $it->name))))));
                                    $item->status           = '1';

                                    $item->save();
                                    if($log=="on"){
                                        echo "item insert : Item :".$item->sku." - ".$item->nama." - ".$item->internal_id_item."\n";
                                    }
                                }

                                $edit   =   FALSE ;
                                if(!$order_item){

                                    if($item){
                                        $edit   =   TRUE ;

                                        $order_item                   =   new OrderItem();
                                        $insert_data++;

                                        if($order_item){
                                            if($log=="on"){
                                                echo "insert : ";
                                            }
                                        }
                                    }

                                }


                                if($order_item){

                                    if(($order_item->qty    ==  $it->qty_pcs)     &&
                                        ($order_item->berat ==  $it->qty)         &&
                                        ($order_item->bumbu ==  $it->bumbu)       &&
                                        ($order_item->part  ==  $it->part)        &&
                                        ($order_item->memo  ==  $it->memo)        &&
                                        ($order_item->rate  ==  ($it->harga_per_pcs ?? 0))){
                                            if($log=="on"){
                                                echo "skip : ".$order->id."\n";
                                            }
                                    }else{
                                            if ($item->nama == "UNDEF_NO PPN") {
                                                if($log=="on"){
                                                    echo "skip : ".$item->nama."\n";
                                                }
                                            } else {
                                                $order_item->order_id         =   $order->id;
                                                $order_item->sku              =   $item->sku;
                                                $order_item->item_id          =   $item->id;
                                                $order_item->line_id          =   $line_id ?? "";
                                                $order_item->nama_detail      =   $item->nama;
                                                $order_item->keterangan       =   $it->description_item;
                                                $order_item->partner          =   $row->customer_partner;
                                                $order_item->alamat_kirim     =   $row->alamat_ship_to;
                                                $wilayah                      =   "";
                                                $splitWilayah                 =   explode("\n",$row->alamat_ship_to);
                                                if($splitWilayah){
                                                    $order->wilayah           =   $splitWilayah[2] ?? '';
                                                }else{
                                                    $order->wilayah           =   $wilayah;
                                                }
                                                $order_item->part             =   $it->part;
                                                $order_item->qty              =   $it->qty_pcs;
                                                $order_item->berat            =   $it->qty;
                                                $order_item->unit             =   $it->unit;
                                                $order_item->rate             =   $it->harga_per_pcs ?? 0;
                                                $order_item->harga            =   NULL;
    
                                                // if($it->unit=="Kilogram"){
                                                //     $order_item->rate             =   $it->rate ?? 0;
                                                //     if(env('NET_SUBSIDIARY', 'CGL')=='EBA'){
                                                //         $order_item->harga            =   $it->harga_per_pcs ?? 0;
                                                //     }
                                                // }else{
                                                //     $order_item->rate             =   $it->harga_per_pcs ?? 0;
                                                //     $order_item->harga            =   $it->rate ?? 0;
                                                // }
                                                
                                                $order_item->bumbu            =   $it->bumbu;
                                                $order_item->memo             =   $it->memo;
                                                $order_item->description_item =   $it->description_item;
    
                                                if($edit == TRUE){
                                                    $order_item->edited           = 0;
                                                }else{
                                                    $order_item->edited           = ($order_item->edited ?? 0) + 1;
    
                                                    if($log=="on"){
                                                        echo "edit : ".$order->id."\n";
                                                    }
    
                                                }
                                                $order_item->save();
    
                                                $update_data++;
    
                                                if($item){
                                                    if($log=="on"){
                                                        echo "update : ";
                                                    }
                                                }

                                        }
                                    }

                                }

                                $inserted_id[] = $order_item->id;
                                if($log=="on"){
                                    echo $order->no_so." : ".$row->nama_customer." Item :".$item->sku." - ".$item->nama." - ".$it->internal_id_item." - ".$it->qty_pcs." - ".$it->qty."\n";
                                }

                            }

                        endforeach;

                        $deleted_order_item = OrderItem::where('order_id', $order->id)->whereNotIn('id', $inserted_id)->get();
                        foreach($deleted_order_item as $d):
                            if($log=="on"){
                                echo "deleted :".$d->id."\n";
                            }
                            $d->delete();
                        endforeach;
                    }

                    $update_data++;

                }


            endforeach;


        $response['meta']["status"]             =   200;
        $response['meta']["message"]            =   "OK";
        $response['response']["data_insert"]    =   $insert_data;
        $response['response']["data_exist"]     =   $update_data;

        echo response()->json($response, $response['meta']["status"]) . "\n";
    }


    public function custom_date_format($tanggal){
        try {
            $split 	  = explode('-', str_replace("/", "-",$tanggal));
            $tgl_system = $split[2] . '-' . $split[1] . '-' . $split[0];
            $tanggal = date('Y-m-d', strtotime($tgl_system));
            return $tanggal;
        } catch (\Throwable $th) {
            return $tanggal;
        }

    }

}

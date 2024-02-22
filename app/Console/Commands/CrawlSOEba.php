<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\Category;
use App\Models\Customer;
use App\Models\DataOption;
use App\Models\Item;
use App\Models\Marketing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Claims\Custom;

class CrawlSOEba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlSOEba:process  {--tanggal=} {--log=}  {--range=}';

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
        $file           =   "https://docs.google.com/spreadsheets/d/e/2PACX-1vRY8zGOpctu4GHyb_Q1LbT7Eq4B6ULfWip7QNBDPy9PnIbVqH9k4Wx0CPzIIb-56uFRDC7n4Re3NbTQ/pub?output=csv";

        try {
            //code...
            $fileData       =   fopen($file, 'r');
        } catch (\Throwable $th) {
            //throw $th;
            echo $th->getMessage();
            return false;
        }

        $import_data    =   [];
        $no             =   0;
        $update_data    =   0;
        $insert_data    =   0;

        DB::beginTransaction();

        $nomor = 0;
        while (($line = fgetcsv($fileData)) !== FALSE) {

            if ($no != 0) {
                $order                       =   Order::where('no_so', $line[1])->first();

                // if($line[5]!=""){
                //     $tanggal_kirim = $this->custom_date_format($line[5]);
                // }else{
                //     $tanggal_kirim = date('Y-m-d', strtotime('+1 days'));
                // }

                // if($tanggal_kirim=="2022-03-17"){
                //     $nomor++;
                //     echo $nomor.".".$line[19]."\n";
                // }

                if ($order) {

                    $order->id_so               =   $line[0] ;
                    $order->no_so               =   $line[1] ;
                    $order->no_po               =   $line[2] ;
                    $order->nama                =   $line[4] ;

                    $cust                       =   Customer::where('nama', $line[4])->first();
                    if(!$cust){
                        $cust = new Customer();
                        $cust->nama = $line[4];
                        $cust->save();
                    }

                        if($line[5]!=""){
                            $tanggal_kirim = $this->custom_date_format($line[5]);
                        }else{
                            $tanggal_kirim = date('Y-m-d', strtotime('+1 days'));
                        }

                        if($line[6]!=""){
                            $tanggal_so = $this->custom_date_format($line[6]);
                        }else{
                            $tanggal_so = date('Y-m-d');
                        }
                        

                    $order->customer_id         =   $cust->id ?? NULL;
                    $order->partner             =   $line[7];
                    $order->tanggal_kirim       =   $tanggal_kirim;
                    $order->tanggal_so          =   $tanggal_so;
                    $order->alamat              =   $line[8];
                    $order->alamat_kirim        =   $line[13];
                    $order->wilayah             =   $line[9];
                    $order->sales_id            =   $line[11];
                    $order->keterangan          =   $line[12];
                    $order->sales_channel       =   $line[13];

                    if ($order->save()) {

                        $proceed    = true;
                        $nama_item = substr($line[28], 1);
                        $order_item = OrderItem::where('nama_detail', $nama_item)->where('key', $line[2])->where('order_id', $order->id)->first();

                        if($order_item){

                            $item                   =   Item::where('nama', $nama_item)->first();

                            // echo json_encode($item);

                            if($item){

                                $order_item->order_id         =   $order->id;
                                $order_item->sku              =   $item->sku;
                                $order_item->item_id          =   $item->id;
                                $order_item->nama_detail      =   $item->nama;
                                $order_item->keterangan       =   $line[25];
                                $order_item->part             =   $line[20];
                                $order_item->qty              =   $line[24];
                                $order_item->berat            =   $line[21];
                                $order_item->unit             =   $line[22];
                                $order_item->rate             =   $line[23];
                                $order_item->bumbu            =   $line[25];
                                $order_item->key              =   $line[2];
                                $order_item->save();

                                $update_data++;

                                // if($item){
                                // echo "update : ".$order->no_so." - ".$item->sku." - ".$item->nama." - ".$line[24]." - ".$line[21]."\n";
                                // }
                            }

                        }else{

                            $item                   =   Item::where('nama', $nama_item)->first();

                            // echo json_encode($item);

                            if($item){
                                
                                $order_item                   =   new OrderItem();

                                if($order_item){
                                    

                                    $order_item->order_id         =   $order->id;
                                    $order_item->sku              =   $item->sku;
                                    $order_item->item_id          =   $item->id;
                                    $order_item->nama_detail      =   $item->nama;
                                    $order_item->keterangan       =   $line[25];
                                    $order_item->part             =   $line[19];
                                    $order_item->qty              =   $line[24];
                                    $order_item->berat            =   $line[21];
                                    $order_item->unit             =   $line[22];
                                    $order_item->rate             =   $line[23];
                                    $order_item->key              =   $line[2];
                                    $order_item->memo             =   $line[26];
                                    $order_item->save();

                                    $insert_data++;

                                    // if($item){
                                    //     echo "insert : ".$order->no_so." - ".$item->sku." - ".$item->nama." - ".$line[24]." - ".$line[21]."\n";
                                    // }
                                }

                            }

                        }

                        
                    }
                    
                } else {

                        $nama_item = substr($line[28], 1);
                        $order = new Order();
                        $order->id_so               =   $line[0] ;
                        $order->no_so               =   $line[1] ;
                        $order->no_po               =   $line[2] ;
                        $order->nama                =   $line[4] ;
                        $cust                       =   Customer::where('nama', $line[4])->first();
                        if(!$cust){
                            $cust = new Customer();
                            $cust->nama = $line[4];
                            $cust->save();
                        }


                            if($line[5]!=""){
                                $tanggal_kirim = $this->custom_date_format($line[5]);
                            }else{
                                $tanggal_kirim = date('Y-m-d', strtotime('+1 days'));
                            }
    
                            if($line[6]!=""){
                                $tanggal_so = $this->custom_date_format($line[6]);
                            }else{
                                $tanggal_so = date('Y-m-d');
                            }
                            
                        
                        $order->customer_id         =   $cust->id ?? NULL;
                        $order->tanggal_kirim       =   $tanggal_kirim;
                        $order->tanggal_so          =   $tanggal_so;
                        $order->alamat              =   $line[8];
                        $order->wilayah             =   $line[9];
                        $order->sales_id            =   $line[10];
                        $order->keterangan          =   $line[12];
                        $order->sales_channel       =   $line[13];

                        if ($order->save()) {

                            $proceed    = true;

                            $item                   =   Item::where('nama', $nama_item)->first();

                            if($item){
                                
                                $order_item                   =   new OrderItem();

                                if($order_item){

                                    $order_item->order_id         =   $order->id;
                                    $order_item->sku              =   $item->sku;
                                    $order_item->item_id          =   $item->id;
                                    $order_item->nama_detail      =   $item->nama;
                                    $order_item->keterangan       =   $line[26];
                                    $order_item->part             =   $line[19];
                                    $order_item->qty              =   $line[23];
                                    $order_item->berat            =   $line[20];
                                    $order_item->unit             =   $line[21];
                                    $order_item->rate             =   $line[22];
                                    $order_item->key              =   $line[2];
                                    $order_item->save();

                                }

                            }

                            $insert_data++;
                            // if($item){
                            // echo "insert : ".$order->no_so." - ".$item->sku." - ".$item->nama." - ".$line[24]." - ".$line[21]."\n";
                            // }
                        }
                        
                }

                $import_data[] = $order;
            }
            $no++;
        }

        fclose($fileData);

        DB::commit();

        $response['meta']["status"]             =   200;
        $response['meta']["message"]            =   "OK";
        $response['response']["data_insert"]    =   $insert_data;
        $response['response']["data_exist"]    =   $update_data;

        echo response()->json($response, $response['meta']["status"]) . "\n";

        $data = response()->json($response, $response['meta']["status"]);

        $logs = array(
            'time'  => date("F j, Y, H:i:s"),
            'data'  => $data
        );

        $log = json_encode($logs);

        // file_put_contents('storage/logs/SO/SO-' . date("Y-m-d") . '.log', $log . PHP_EOL, FILE_APPEND);
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

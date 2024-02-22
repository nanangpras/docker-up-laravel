<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\NetsuiteBom;
use App\Models\NetsuiteCustomer;
use App\Models\NetsuiteItem;
use App\Models\NetsuiteLocation;
use App\Models\NetsuiteLog;
use App\Models\NetsuitePurchasing;
use App\Models\NetsuiteSalesOrder;
use App\Models\NetsuiteVendor;
use App\Models\User;
use App\Models\NetsuitePOItemReceipt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NetsuiteController extends Controller
{
    //

    public function index(Request $request){

        $type   = $request->record_type;
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        return view('netsuite.index', compact('type', 'mulai', 'sampai'));

    }

    public function list(Request $request){

        $type   = $request->type;
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        $search = $request->search ?? "";

        $data = NetsuiteLog::with(['data_purchasing', 'data_po_item_receipt', 'data_salesorder', 'data_bom', 'data_location', 'data_vendor', 'data_customer', 'data_item'])->orderBy('id', 'desc');
        if($type!=""){
            $data = $data->where('activity', $type);
        }

        if($search!=""){
            $data = $data->where('table_data', 'like', '%'.$type.'%');
        }

        $data = $data->whereBetween('created_at', [$mulai." 00:00:01", $sampai." 23:59:59"]);
        $data = $data->paginate(15);

        return view('netsuite.list_table', compact('type', 'mulai', 'sampai', 'data', 'search'));

    }

    public function receivePO(Request $request){

        if(file_get_contents('php://input')!=""){

            try{

                DB::beginTransaction();

                $data = $request->all();

                $netsuite                   =   new NetsuiteLog() ;
                $netsuite->activity         =   'purchase-order' ;
                $netsuite->label            =   $data['record_type'] ;
                $netsuite->table_data       =   json_encode($data);
                $netsuite->sync_status      =   "0" ;
                $netsuite->status           =   1 ;


                try {
                    //code...
                    if($netsuite->save()){

                        //List data
                        $data_resp = array();
                        foreach($data['data'] as $row){


                            $po = NetsuitePurchasing::where('internal_id_po', $row['data_purchasing']['internal_id_po'])->first();

                            // if($row['data_purchasing']['tanggal_kirim']!=""){

                                if(!$po){

                                    $po                           = new NetsuitePurchasing();

                                    $rsp = array(
                                        'code'          => "1",
                                        'activity'      => "Insert",
                                        'status'        => "Success",
                                        'message'       => "Save PO success",
                                        'internal_id_po' => $row['data_purchasing']['internal_id_po']
                                    );

                                }else{

                                    $rsp = array(
                                        'code'          => "1",
                                        'activity'      => "Updated",
                                        'status'        => "Success",
                                        'message'       => "Save PO success",
                                        'internal_id_po' => $row['data_purchasing']['internal_id_po']
                                    );

                                }

                                //Data purchasing
                                $po->document_number                = $row['data_purchasing']['document_number'];
                                $po->type_po                        = $row['data_purchasing']['type_po'];
                                $po->vendor                         = $row['data_purchasing']['vendor'];
                                $po->vendor_name                    = $row['data_purchasing']['vendor_name'];
                                $po->tipe_ekspedisi                 = $row['data_purchasing']['tipe_ekspedisi'];
                                $po->po_subsidiary                  = $row['data_purchasing']['subsidiary'];
                                $po->tanggal_kirim                  = $row['data_purchasing']['tanggal_kirim'] ?? date('Y-m-d');
                                $po->internal_id_po                 = $row['data_purchasing']['internal_id_po'];

                                //Data vendor
                                $po->internal_id_vendor             = $row['data_vendor']['internal_id_vendor'];
                                $po->nama_vendor                    = $row['data_vendor']['nama_vendor'];
                                $po->alamat                         = $row['data_vendor']['alamat'];
                                $po->no_telp                        = $row['data_vendor']['no_telp'];
                                $po->jenis_ekspedisi                = $row['data_vendor']['jenis_ekspedisi'];
                                $po->wilayah_vendor                 = $row['data_vendor']['wilayah_vendor'];
                                $po->vendor_subsidiary              = $row['data_vendor']['subsidiary'];

                                // PO Multi line
                                try {
                                    //code...
                                    $data_first = $row['data_item'][0];

                                    $po->internal_id                    = $data_first['internal_id_item'];
                                    $po->item                           = $data_first['item'];
                                    $po->rate                           = $data_first['rate'];
                                    $po->ukuran_ayam                    = $data_first['ukuran_ayam'];
                                    $po->qty                            = $data_first['qty'];
                                    $po->jumlah_ayam                    = $data_first['qty_pcs'];
                                    $po->jenis_ayam                     = $data_first['jenis_ayam'];
                                    $po->jumlah_do                      = $data_first['jumlah_do'];

                                    // Data item asli
                                    $po->internal_id_item               = $data_first['internal_id_item'];
                                    $po->sku                            = $data_first['sku'];
                                    $po->name                           = $data_first['name'];
                                    $po->category_item                  = $data_first['category_item'];
                                    $po->item_subsidiary                = $data_first['subsidiary'];

                                    $po->data_item                      = json_encode($row['data_item']);

                                } catch (\Throwable $th) {

                                    //throw $th;
                                    // PO single line
                                    // Data Item dalam Data Purchasing
                                    $po->internal_id                    = $row['data_purchasing']['internal_id_item'];
                                    $po->item                           = $row['data_purchasing']['item'];
                                    $po->rate                           = $row['data_purchasing']['rate'];
                                    $po->ukuran_ayam                    = $row['data_purchasing']['ukuran_ayam'];
                                    $po->qty                            = $row['data_purchasing']['qty'];
                                    $po->jenis_ayam                     = $row['data_purchasing']['jenis_ayam'];
                                    $po->jumlah_do                      = $row['data_purchasing']['jumlah_do'];
                                    $po->tanggal_kirim                  = $row['data_purchasing']['tanggal_kirim'];
                                    $po->internal_id_po                 = $row['data_purchasing']['internal_id_po'];

                                    // Data item asli
                                    $po->internal_id_item               = $row['data_item']['internal_id_item'];
                                    $po->sku                            = $row['data_item']['sku'];
                                    $po->name                           = $row['data_item']['name'];
                                    $po->category_item                  = $row['data_item']['category_item'];
                                    $po->item_subsidiary                = $row['data_item']['subsidiary'];

                                    $po->data_item                      = json_encode(array(array(
                                                                            "internal_id_item"  => $row['data_item']['internal_id_item'],
                                                                            "sku"               => $row['data_item']['sku'],
                                                                            "name"              => $row['data_item']['name'],
                                                                            "category_item"     => $row['data_item']['category_item'],
                                                                            "subsidiary"        => $row['data_item']['subsidiary'],
                                                                            "jenis_ayam"        => $row['data_purchasing']['jenis_ayam'],
                                                                            "jumlah_do"         => $row['data_purchasing']['jumlah_do'],
                                                                            "internal_id"       => $row['data_purchasing']['internal_id_item'],
                                                                            "item"              => $row['data_purchasing']['item'],
                                                                            "rate"              => $row['data_purchasing']['rate'],
                                                                            "ukuran_ayam"       => $row['data_purchasing']['ukuran_ayam'],
                                                                            "qty"               => $row['data_purchasing']['qty'],
                                                                            "qty_pcs"           => $row['data_purchasing']['qty_pcs'] ?? 0
                                                                        )));

                                }


                                // Tambahan log
                                $po->server_update                  = $po->server_update+1;
                                $po->last_update                    = date('Y-m-d H:i:s');
                                $po->netsuite_log_id                = $netsuite->id;


                                $rsp['apps_id']                 = (String)$po->id;

                                if($po->save()){
                                    $data_resp[] = $rsp;
                                }

                            // }else{
                            //     $rsp = array(
                            //         'code'          => "0",
                            //         'activity'      => "Error while receive PO",
                            //         'status'        => "Failed",
                            //         'message'       => "Tanggal kirim kosong",
                            //         'internal_id_po' => $row['data_purchasing']['internal_id_po']
                            //     );

                            //     $data_resp[] = $rsp;
                            // }

                        }
                    }

                    $response = array(
                        'code'      => "1",
                        'status'    => "Success",
                        'message'   => "Save PO success",
                        'apps_id'   => $netsuite->id,
                        'data'      => $data_resp
                    );

                    DB::commit();

                } catch (\Throwable $th) {
                    //throw $th;
                    DB::rollBack() ;
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => $th->getMessage(),
                        'apps_id'   => NULL
                    );
                }


                $log = array(
                    'timestamp' => date("Y-m-d H:i:s"),
                    'ip'        => $_SERVER['REMOTE_ADDR'],
                    'data'      => file_get_contents('php://input')
                );

                $data = json_encode($log);
                $path = 'netsuite/po';
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true);
                }

                file_put_contents($path.'/netsuite_po_'.date("Y-m-d").'.json', $data.PHP_EOL, FILE_APPEND);
                file_put_contents($path.'/netsuite_'.$netsuite->id.'.json', file_get_contents('php://input'));


            }catch(\Throwable $th){
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => $th->getMessage(),
                    'apps_id'   => NULL
                );
            }

            return $response;
        }
    }
    public function receivePOItemReceipt(Request $request){

        if(file_get_contents('php://input')!=""){

            try{

                DB::beginTransaction();

                $data = $request->all();

                $netsuite                   =   new NetsuiteLog() ;
                $netsuite->activity         =   'po-item-receipt' ;
                $netsuite->label            =   $data['record_type'] ;
                $netsuite->table_data       =   json_encode($data);
                $netsuite->sync_status      =   "0" ;
                $netsuite->status           =   1 ;


                try {
                    //code...
                    if($netsuite->save()){

                        //List data
                        $data_resp = array();
                        foreach($data['data'] as $row){


                            $po = NetsuitePOItemReceipt::where('internal_id_po', $row['data_purchasing']['internal_id_po'])->first();

                            if(!$po){

                                $po                           = new NetsuitePOItemReceipt();

                                $rsp = array(
                                    'code'          => "1",
                                    'activity'      => "Insert",
                                    'status'        => "Success",
                                    'message'       => "Save PO Item Receipt success",
                                    'internal_id_po' => $row['data_purchasing']['internal_id_po']
                                );

                            }else{

                                $rsp = array(
                                    'code'          => "1",
                                    'activity'      => "Updated",
                                    'status'        => "Success",
                                    'message'       => "Save PO Item Receipt success",
                                    'internal_id_po' => $row['data_purchasing']['internal_id_po']
                                );

                            }

                            //Data purchasing
                            $po->document_number                = $row['data_purchasing']['document_number'];
                            $po->type_po                        = $row['data_purchasing']['type_po'];
                            $po->vendor_name                    = $row['data_purchasing']['vendor_name'];
                            $po->activity                       = 'po-item-receipt';
                            $po->po_subsidiary                  = $row['data_purchasing']['subsidiary'];
                            $po->internal_id_po                 = $row['data_purchasing']['internal_id_po'];
                            $po->status_po                      = $row['data_purchasing']['status_po'];

                            // PO Multi line
                            $po->data_item                      = json_encode($row['data_item']);

                            // Tambahan log
                            $po->server_update                  = $po->server_update+1;
                            $po->last_update                    = date('Y-m-d H:i:s');
                            $po->netsuite_log_id                = $netsuite->id;


                            $rsp['apps_id']                 = (String)$po->id;

                            if($po->save()){
                                $data_resp[] = $rsp;
                            }

                        }
                    }

                    $response = array(
                        'code'      => "1",
                        'status'    => "Success",
                        'message'   => "Save PO Item Receipt success",
                        'apps_id'   => $netsuite->id,
                        'data'      => $data_resp
                    );

                    DB::commit();

                } catch (\Throwable $th) {
                    //throw $th;
                    DB::rollBack() ;
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => $th->getMessage(),
                        'apps_id'   => NULL
                    );
                }


                $log = array(
                    'timestamp' => date("Y-m-d H:i:s"),
                    'ip'        => $_SERVER['REMOTE_ADDR'],
                    'data'      => file_get_contents('php://input')
                );

                $data = json_encode($log);
                $path = 'netsuite/po_item_receipt';
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true);
                }

                file_put_contents($path.'/netsuite_po_item_receipt'.date("Y-m-d").'.json', $data.PHP_EOL, FILE_APPEND);
                file_put_contents($path.'/netsuite_'.$netsuite->id.'.json', file_get_contents('php://input'));


            }catch(\Throwable $th){
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => $th->getMessage(),
                    'apps_id'   => NULL
                );
            }

            return $response;
        }
    }

    public function receiveSO(Request $request){
        if(file_get_contents('php://input')!=""){

            try{

                $data = $request->all();

                $netsuite                   =   new NetsuiteLog() ;
                $netsuite->activity         =   'sales-order' ;
                $netsuite->label            =   $data['record_type'] ;
                $netsuite->table_data       =   json_encode($data);
                $netsuite->sync_status      =   "0" ;
                $netsuite->status           =   1 ;


                try {
                    //code...
                    if($netsuite->save()){

                        //List data
                        $data_resp = array();
                        foreach($data['data'] as $row){

                            $so = NetsuiteSalesOrder::where('internal_id_so', $row['data_sales_order']['internal_id_so'])->first();

                            if(!$so){

                                $so                           = new NetsuiteSalesOrder();

                                $rsp = array(
                                    'code'          => "1",
                                    'activity'      => "Insert",
                                    'status'        => "Success",
                                    'message'       => "Save SO success",
                                    'internal_id_so' => $row['data_sales_order']['internal_id_so']
                                );

                            }else{

                                $rsp = array(
                                    'code'          => "1",
                                    'activity'      => "Updated",
                                    'status'        => "Success",
                                    'message'       => "Save SO success",
                                    'internal_id_so' => $row['data_sales_order']['internal_id_so']
                                );

                            }

                            $so->internal_id_customer                   = $row['data_customer']['internal_id_customer'];
                            $so->nama_customer                          = $row['data_customer']['nama_customer'];
                            $so->category_customer                      = $row['data_customer']['category_customer'];
                            $so->id_sales                               = $row['data_customer']['id_sales'];
                            $so->sales                                  = $row['data_customer']['sales'];
                            $so->so_subsidiary                          = $row['data_customer']['subsidiary'];
                            $so->internal_id_parent                     = $row['data_customer']['internal_id_parent'];

                            $so->internal_id_so                 = $row['data_sales_order']['internal_id_so'];
                            $so->nomor_so                       = $row['data_sales_order']['nomor_so'];
                            $so->nomor_po                       = $row['data_sales_order']['nomor_po'];
                            $so->status_so                      = $row['data_sales_order']['status_so'] ?? NULL;
                            $so->nama_customer                  = $row['data_sales_order']['nama_customer'];
                            $so->tanggal_kirim                  = $row['data_sales_order']['tanggal_kirim'];
                            $so->tanggal_so                     = $row['data_sales_order']['tanggal_so'];
                            $so->customer_partner               = $row['data_sales_order']['customer_partner'];
                            $so->alamat_customer_partner        = $row['data_sales_order']['alamat_customer_partner'];
                            $so->wilayah                        = $row['data_sales_order']['wilayah'];
                            $so->id_sales                       = $row['data_sales_order']['id_sales'];
                            $so->sales                          = $row['data_sales_order']['sales'];
                            $so->memo                           = $row['data_sales_order']['memo'];
                            $so->sales_channel                  = $row['data_sales_order']['sales_channel'];
                            $so->alamat_ship_to                 = $row['data_sales_order']['alamat_ship_to'];
                            $so->so_subsidiary                  = $row['data_sales_order']['subsidiary'];

                            $so->data_item                      = json_encode($row['data_item']);

                            $so->server_update                  = $so->server_update+1;
                            $so->last_update                    = date('Y-m-d H:i:s');
                            $so->netsuite_log_id                = $netsuite->id;


                            $rsp['apps_id']                 = (String)$so->id;
                            $rsp['data_so']                 = $so;

                            if($so->save()){
                                $data_resp[] = $rsp;
                            }

                            if($row['data_sales_order']['sales_channel']==""){
                                $response = array(
                                    'code'    => "0",
                                    'status'   => "Failed",
                                    'message'   => "Format data tidak sesuai",
                                    'apps_id'   => NULL
                                );

                                return $response;
                            }

                        }
                    }

                    $response = array(
                        'code'      => "1",
                        'status'    => "Success",
                        'message'   => "Save SO success",
                        'apps_id'   => $netsuite->id,
                        'data'      => $data_resp
                    );

                } catch (\Throwable $th) {
                    //throw $th;
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => $th->getMessage(),
                        'apps_id'   => NULL
                    );
                }

                $log = array(
                    'timestamp' => date("Y-m-d H:i:s"),
                    'ip'        => $_SERVER['REMOTE_ADDR'],
                    'data'      => file_get_contents('php://input')
                );

                $data = json_encode($log);
                $path = 'netsuite/so';
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true);
                }

                file_put_contents($path.'/netsuite_so_'.date("Y-m-d").'.json', $data.PHP_EOL, FILE_APPEND);
                file_put_contents($path.'/netsuite_'.$netsuite->id.'.json', file_get_contents('php://input'));

            }catch(\Throwable $th){
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "Format data tidak sesuai",
                    'apps_id'   => NULL
                );
            }

            return $response;
        }
    }

    public function receiveLocation(Request $request){
        if(file_get_contents('php://input')!=""){

            try {

                $data = $request->all();

                $netsuite                   =   new NetsuiteLog() ;
                $netsuite->activity         =   'location' ;
                $netsuite->label            =   $data['record_type'] ;
                $netsuite->table_data       =   json_encode($data['data']);
                $netsuite->sync_status      =   "0" ;
                $netsuite->status           =   1 ;

                try {
                    //code...
                    if($netsuite->save()){

                        //List data
                        $data_resp = array();
                        foreach($data['data'] as $row){

                            $location = NetsuiteLocation::where('internal_id_location', $row['data_location']['internal_id_location'])->first();

                            if($location){
                                $location->nama_location            = $row['data_location']['nama_location'];
                                $location->internal_id_location     = $row['data_location']['internal_id_location'];
                                $location->kategori                 = $row['data_location']['kategori_gudang'] ?? NULL;
                                $location->subsidiary_id            = $row['data_location']['subsidiary_id'] ?? NULL;
                                $location->subsidiary               = $row['data_location']['subsidiary_name'] ?? NULL;
                                $location->status                   = $row['data_location']['isinactive'] ?? NULL;

                                $location->server_update            = $location->server_update+1;
                                $location->last_update              = date('Y-m-d H:i:s');
                                $location->netsuite_log_id          = $netsuite->id;
                                $location->save();

                                $rsp = array(
                                    'code'          => "1",
                                    'activity'      => "Updated",
                                    'status'        => "Success",
                                    'message'       => "Save Location success",
                                    'apps_id'       => $location->id,
                                    'internal_id_location' => $row['data_location']['internal_id_location']
                                );

                                $data_resp[] = $rsp;

                            }else{
                                $location                           = new NetsuiteLocation();
                                $location->nama_location            = $row['data_location']['nama_location'];
                                $location->internal_id_location     = $row['data_location']['internal_id_location'];
                                $location->kategori                 = $row['data_location']['kategori_gudang'] ?? NULL;
                                $location->subsidiary_id            = $row['data_location']['subsidiary_id'] ?? NULL;
                                $location->subsidiary               = $row['data_location']['subsidiary_name'] ?? NULL;
                                $location->status                   = $row['data_location']['isinactive'] ?? NULL;

                                $location->server_update            = 0;
                                $location->netsuite_log_id          = $netsuite->id;
                                $location->last_update              = date('Y-m-d H:i:s');
                                $location->save();

                                $rsp = array(
                                    'code'          => "1",
                                    'activity'      => "Insert",
                                    'status'        => "Success",
                                    'message'       => "Save Location success",
                                    'apps_id'       => $location->id,
                                    'internal_id_location' => $row['data_location']['internal_id_location']
                                );

                                $data_resp[] = $rsp;
                            }

                        }
                    }

                    $response = array(
                        'code'      => "1",
                        'status'    => "Success",
                        'message'   => "Save Location success",
                        'apps_id'   => $netsuite->id,
                        'data'      => $data_resp
                    );

                } catch (\Throwable $th) {
                    //throw $th;
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => $th->getMessage(),
                        'apps_id'   => NULL
                    );
                }

                $log = array(
                    'timestamp' => date("Y-m-d H:i:s"),
                    'ip'        => $_SERVER['REMOTE_ADDR'],
                    'data'      => file_get_contents('php://input')
                );

                $data = json_encode($log);
                $path = 'netsuite/location';
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true);
                }

                file_put_contents($path.'/netsuite_location_'.date("Y-m-d").'.json', $data.PHP_EOL, FILE_APPEND);
                file_put_contents($path.'/netsuite_'.$netsuite->id.'.json', file_get_contents('php://input'));

            }catch(\Throwable $th){
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "Format data tidak sesuai",
                    'apps_id'   => NULL
                );
            }

            return $response;

        }
    }



    public function receiveBOM(Request $request){
        if(file_get_contents('php://input')!=""){

            try {

                $data = $request->all();

                $netsuite                   =   new NetsuiteLog() ;
                $netsuite->activity         =   'bom' ;
                $netsuite->label            =   $data['record_type'] ;
                $netsuite->table_data       =   json_encode($data['data']);
                $netsuite->sync_status      =   "0" ;
                $netsuite->status           =   1 ;

                try {
                    //code...
                    if($netsuite->save()){

                        //List data
                        $data_resp = array();
                        foreach($data['data'] as $row){

                            $bom = NetsuiteBom::where('internal_id_bom', $row['bom']['internal_id_bom'])->first();

                            if($bom){

                                $rsp = array(
                                    'code'          => "1",
                                    'activity'      => "Updated",
                                    'status'        => "Success",
                                    'message'       => "Save BOM success",
                                    'internal_id_bom' => $row['bom']['internal_id_bom']
                                );

                            }else{

                                $bom                           = new NetsuiteBom();

                                $rsp = array(
                                    'code'          => "1",
                                    'activity'      => "Insert",
                                    'status'        => "Success",
                                    'message'       => "Save BOM success",
                                    'internal_id_bom' => $row['bom']['internal_id_bom']
                                );

                            }

                            $bom->internal_id_bom               = $row['bom']['internal_id_bom'];
                            $bom->bom_name                      = $row['bom']['bom_name'];
                            $bom->internal_subsidiary_id        = $row['bom']['internal_subsidiary_id'];
                            $bom->subsidiary                    = $row['bom']['subsidiary'];
                            $bom->memo                          = $row['bom']['memo'];
                            $bom->data_item                     = json_encode($row['bom']['item']);

                            $bom->server_update            = 0;
                            $bom->netsuite_log_id          = $netsuite->id;
                            $bom->last_update              = date('Y-m-d H:i:s');
                            $bom->save();

                            $rsp['apps_id']                 = (String)$bom->id;

                            $data_resp[] = $rsp;
                        }

                        $response = array(
                            'code'      => "1",
                            'status'    => "Success",
                            'message'   => "Save BOM success",
                            'apps_id'   => $netsuite->id,
                            'data'      => $data_resp
                        );
                    }

                } catch (\Throwable $th) {
                    //throw $th;
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => $th->getMessage(),
                        'apps_id'   => NULL
                    );
                }

                $log = array(
                    'timestamp' => date("Y-m-d H:i:s"),
                    'ip'        => $_SERVER['REMOTE_ADDR'],
                    'data'      => file_get_contents('php://input')
                );

                $data = json_encode($log);
                $path = 'netsuite/bom';
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true);
                }

                file_put_contents($path.'/netsuite_bom_'.date("Y-m-d").'.json', $data.PHP_EOL, FILE_APPEND);
                file_put_contents($path.'/netsuite_'.$netsuite->id.'.json', file_get_contents('php://input'));

            }catch(\Throwable $th){
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "Format data tidak sesuai",
                    'apps_id'   => NULL
                );
            }

            return $response;

        }
    }

    public function receiveItem(Request $request){
        if(file_get_contents('php://input')!=""){

            try {

                $data = $request->all();

                $netsuite                   =   new NetsuiteLog() ;
                $netsuite->activity         =   'item' ;
                $netsuite->label            =   $data['record_type'] ;
                $netsuite->table_data       =   json_encode($data['data']);
                $netsuite->sync_status      =   "0" ;
                $netsuite->status           =   1 ;

                try {
                    //code...
                    if($netsuite->save()){

                        //List data
                        $data_resp = array();
                        foreach($data['data'] as $row){

                            $item = NetsuiteItem::where('sku', $row['data_item']['sku'])->first();
                            if($item){
                                $item = NetsuiteItem::where('internal_id_item', $row['data_item']['internal_id_item'])->first();
                            }

                            if(!$item){
                                $item                           = new NetsuiteItem();
                            }

                            $item->nama_item            = $row['data_item']['nama_item'];
                            $item->sku                  = $row['data_item']['sku'];
                            $item->internal_id_item     = $row['data_item']['internal_id_item'];
                            $item->category_item        = $row['data_item']['category_item'] ?? NULL;
                            $item->faktor_kali_berat    = $row['data_item']['faktor_kali_berat'] ?? NULL;
                            $item->stock_unit           = $row['data_item']['stock_unit'] ?? NULL;
                            $item->purchase_unit        = $row['data_item']['purchase_unit'] ?? NULL;
                            $item->sale_unit            = $row['data_item']['sale_unit'] ?? NULL;
                            $item->tax_schedule         = $row['data_item']['tax_schedule'] ?? NULL;
                            $item->tax_code                 = $row['data_item']['tax_code'] ?? NULL;
                            $item->tax_code_id              = $row['data_item']['tax_code_id'] ?? NULL;
                            $item->tax_rate                 = $row['data_item']['tax_rate'] ?? NULL;
                            $item->inactive                 = $row['data_item']['inactive'] ?? NULL;
                            $item->subsidiary               = $row['data_item']['subsidiary'] ?? NULL;
                            $item->status                   = $row['data_item']['inactive'] ?? NULL;

                            $item->server_update            = $item->server_update+1;
                            $item->netsuite_log_id          = $netsuite->id;
                            $item->last_update              = date('Y-m-d H:i:s');
                            $item->save();

                            $item->app_id                   = $item->id;
                            $item->save();

                            $rsp = array(
                                'code'          => "1",
                                'activity'      => "Insert",
                                'status'        => "Success",
                                'message'       => "Save Item success",
                                'apps_id'       => $item->id,
                                'internal_id_item' => $row['data_item']['internal_id_item']
                            );

                            $data_resp[] = $rsp;

                        }
                    }

                    $response = array(
                        'code'      => "1",
                        'status'    => "Success",
                        'message'   => "Save Item success",
                        'apps_id'   => $netsuite->id,
                        'data'      => $data_resp
                    );

                } catch (\Throwable $th) {
                    //throw $th;
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => $th->getMessage(),
                        'apps_id'   => NULL
                    );
                }

                $log = array(
                    'timestamp' => date("Y-m-d H:i:s"),
                    'ip'        => $_SERVER['REMOTE_ADDR'],
                    'data'      => file_get_contents('php://input')
                );

                $data = json_encode($log);
                $path = 'netsuite/item';
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true);
                }

                file_put_contents($path.'/netsuite_item_'.date("Y-m-d").'.json', $data.PHP_EOL, FILE_APPEND);
                file_put_contents($path.'/netsuite_'.$netsuite->id.'.json', file_get_contents('php://input'));

            }catch(\Throwable $th){
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "Format data tidak sesuai",
                    'apps_id'   => NULL
                );
            }

            return $response;

        }
    }
    public function receiveItemNS(Request $request){
        if(file_get_contents('php://input')!=""){

            try {

                $data = $request->all();

                $netsuite                   =   new NetsuiteLog() ;
                $netsuite->activity         =   'item' ;
                $netsuite->label            =   $data['record_type'] ;
                $netsuite->table_data       =   json_encode($data['data']);
                $netsuite->sync_status      =   "0" ;
                $netsuite->status           =   1 ;

                try {
                    //code...
                    if($netsuite->save()){

                        //List data
                        $data_resp = array();
                        foreach($data['data'] as $row){

                            $item = NetsuiteItem::where('internal_id_item', $row['data_item']['internal_id_item'])->first();

                            if(!$item){
                                $item                           = new NetsuiteItem();
                            }

                            $item->nama_item            = $row['data_item']['nama_item'];
                            $item->sku                  = $row['data_item']['sku'];
                            $item->internal_id_item     = $row['data_item']['internal_id_item'];
                            $item->category_item        = $row['data_item']['category_item'] ?? NULL;
                            $item->faktor_kali_berat    = $row['data_item']['faktor_kali_berat'] ?? NULL;
                            $item->stock_unit           = $row['data_item']['stock_unit'] ?? NULL;
                            $item->purchase_unit        = $row['data_item']['purchase_unit'] ?? NULL;
                            $item->sale_unit            = $row['data_item']['sale_unit'] ?? NULL;
                            $item->tax_schedule         = $row['data_item']['tax_schedule'] ?? NULL;
                            $item->tax_code                 = $row['data_item']['tax_code'] ?? NULL;
                            $item->tax_code_id              = $row['data_item']['tax_code_id'] ?? NULL;
                            $item->tax_rate                 = $row['data_item']['tax_rate'] ?? NULL;
                            $item->inactive                 = $row['data_item']['inactive'] ?? NULL;
                            $item->subsidiary               = $row['data_item']['subsidiary'] ?? NULL;
                            $item->status                   = $row['data_item']['inactive'] ?? NULL;

                            $item->server_update            = $item->server_update+1;
                            $item->netsuite_log_id          = $netsuite->id;
                            $item->last_update              = date('Y-m-d H:i:s');
                            $item->save();

                            $rsp = array(
                                'code'          => "1",
                                'activity'      => "Insert",
                                'status'        => "Success",
                                'message'       => "Save Item success",
                                'apps_id'       => $item->id,
                                'internal_id_item' => $row['data_item']['internal_id_item']
                            );

                            $data_resp[] = $rsp;

                        }
                    }

                    $response = array(
                        'code'      => "1",
                        'status'    => "Success",
                        'message'   => "Save Item success",
                        'apps_id'   => $netsuite->id,
                        'data'      => $data_resp
                    );

                } catch (\Throwable $th) {
                    //throw $th;
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => $th->getMessage(),
                        'apps_id'   => NULL
                    );
                }

                $log = array(
                    'timestamp' => date("Y-m-d H:i:s"),
                    'ip'        => $_SERVER['REMOTE_ADDR'],
                    'data'      => file_get_contents('php://input')
                );

                $data = json_encode($log);
                $path = 'netsuite/item';
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true);
                }

                file_put_contents($path.'/netsuite_item_'.date("Y-m-d").'.json', $data.PHP_EOL, FILE_APPEND);
                file_put_contents($path.'/netsuite_'.$netsuite->id.'.json', file_get_contents('php://input'));

            }catch(\Throwable $th){
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "Format data tidak sesuai",
                    'apps_id'   => NULL
                );
            }

            return $response;

        }
    }

    public function receiveCustomer(Request $request){
        if(file_get_contents('php://input')!=""){

            try {

                $data = $request->all();

                $netsuite                   =   new NetsuiteLog() ;
                $netsuite->activity         =   'customer' ;
                $netsuite->label            =   $data['record_type'] ;
                $netsuite->table_data       =   json_encode($data['data']);
                $netsuite->sync_status      =   "0" ;
                $netsuite->status           =   1 ;

                try {
                    //code...
                    if($netsuite->save()){

                        //List data
                        $data_resp = array();
                        foreach($data['data'] as $row){

                            $customer = NetsuiteCustomer::where('internal_id_customer', $row['data_customer']['internal_id_customer'])->first();

                            if(!$customer){
                                $customer                           = new NetsuiteCustomer();
                            }

                            $customer->nama_customer            = $row['data_customer']['nama_customer'];
                            $customer->internal_id_customer     = $row['data_customer']['internal_id_customer'];
                            $customer->category_customer        = $row['data_customer']['category_customer'] ?? NULL;
                            $customer->entityid                 = $row['data_customer']['entityid'] ?? NULL;
                            $customer->sales_rep_internal_id    = $row['data_customer']['sales_rep_internal_id'] ?? NULL;
                            $customer->sales_rep_nama           = $row['data_customer']['sales_rep_nama'] ?? NULL;
                            $customer->data_alamat              = json_encode($row['data_customer']['data_alamat'] ?? []);
                            $customer->inactive                 = $row['data_customer']['inactive'] ?? NULL;
                            $customer->subsidiary               = $row['data_customer']['subsidiary'] ?? NULL;
                            $customer->internal_id_parent       = $row['data_customer']['internal_id_parent'] ?? NULL;
                            $customer->tax_code_id              = $row['data_customer']['tax_code_id'] ?? NULL;
                            $customer->tax_code                 = $row['data_customer']['tax_code'] ?? NULL;
                            $customer->tax_rate                 = $row['data_customer']['tax_rate'] ?? NULL;
                            $customer->status                   = $row['data_customer']['inactive'] ?? NULL;

                            $customer->server_update            = $customer->server_update+1;
                            $customer->netsuite_log_id          = $netsuite->id;
                            $customer->last_update              = date('Y-m-d H:i:s');
                            $customer->save();

                            $data_resp[]                        = $customer;
                        }
                    }

                    $response = array(
                        'code'      => "1",
                        'status'    => "Success",
                        'message'   => "Save Customer success",
                        'apps_id'   => $netsuite->id,
                        'data'      => $data_resp
                    );

                } catch (\Throwable $th) {
                    //throw $th;
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => $th->getMessage(),
                        'apps_id'   => NULL
                    );
                }

                $log = array(
                    'timestamp' => date("Y-m-d H:i:s"),
                    'ip'        => $_SERVER['REMOTE_ADDR'],
                    'data'      => file_get_contents('php://input')
                );

                $data = json_encode($log);
                $path = 'netsuite/customer';
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true);
                }

                file_put_contents($path.'/netsuite_customer_'.date("Y-m-d").'.json', $data.PHP_EOL, FILE_APPEND);
                file_put_contents($path.'/netsuite_'.$netsuite->id.'.json', file_get_contents('php://input'));

            }catch(\Throwable $th){
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => json_encode($th),
                    'apps_id'   => NULL
                );
            }

            return $response;

        }
    }

    public function receiveVendor(Request $request){
        if(file_get_contents('php://input')!=""){

            try {

                $data = $request->all();

                $netsuite                   =   new NetsuiteLog() ;
                $netsuite->activity         =   'vendor' ;
                $netsuite->label            =   $data['record_type'] ;
                $netsuite->table_data       =   json_encode($data['data']);
                $netsuite->sync_status      =   "0" ;
                $netsuite->status           =   1 ;

                try {
                    //code...
                    if($netsuite->save()){

                        //List data
                        $data_resp = array();
                        foreach($data['data'] as $row){

                            $vendor = NetsuiteVendor::where('internal_id_vendor', $row['data_vendor']['internal_id_vendor'])->first();

                            if(!$vendor){
                                $vendor                           = new NetsuiteVendor();
                            }

                            $vendor->nama_vendor            = $row['data_vendor']['nama_vendor'];
                            $vendor->internal_id_vendor     = $row['data_vendor']['internal_id_vendor'];
                            $vendor->category_vendor        = $row['data_vendor']['category_vendor'] ?? NULL;
                            $vendor->wilayah_vendor         = $row['data_vendor']['wilayah_vendor'] ?? NULL;
                            $vendor->entityid               = $row['data_vendor']['entityid'] ?? NULL;
                            $vendor->data_alamat            = json_encode($row['data_vendor']['data_alamat'] ?? []);

                            $vendor->inactive                 = $row['data_vendor']['inactive'] ?? NULL;
                            $vendor->subsidiary               = $row['data_vendor']['subsidiary'] ?? NULL;
                            $vendor->status                   = $row['data_vendor']['inactive'] ?? NULL;

                            $vendor->server_update            = $vendor->server_update+1;
                            $vendor->netsuite_log_id          = $netsuite->id;
                            $vendor->last_update              = date('Y-m-d H:i:s');
                            $vendor->save();

                            $data_resp[]                      = $vendor;

                        }
                    }

                    $response = array(
                        'code'      => "1",
                        'status'    => "Success",
                        'message'   => "Save Vendor success",
                        'apps_id'   => $netsuite->id,
                        'data'      => $data_resp
                    );

                } catch (\Throwable $th) {
                    //throw $th;
                    $response = array(
                        'code'    => "0",
                        'status'   => "Failed",
                        'message'   => $th->getMessage(),
                        'apps_id'   => NULL
                    );
                }

                $log = array(
                    'timestamp' => date("Y-m-d H:i:s"),
                    'ip'        => $_SERVER['REMOTE_ADDR'],
                    'data'      => file_get_contents('php://input')
                );

                $data = json_encode($log);
                $path = 'netsuite/vendor';
                if(!File::exists($path)) {
                    File::makeDirectory($path, 0777, true);
                }

                file_put_contents($path.'/netsuite_vendor_'.date("Y-m-d").'.json', $data.PHP_EOL, FILE_APPEND);
                file_put_contents($path.'/netsuite_'.$netsuite->id.'.json', file_get_contents('php://input'));

            }catch(\Throwable $th){
                $response = array(
                    'code'    => "0",
                    'status'   => "Failed",
                    'message'   => "Format data tidak sesuai",
                    'apps_id'   => NULL
                );
            }

            return $response;

        }
    }

    public function getDataPO(Request $request){
        $subsidiary = $request->subsidiary ?? '';
        $tanggal    = $request->tanggal ?? date('Y-m-d');

        if ($subsidiary == '') {
            if ($request->tanggal == 'all') {
                $data   = NetsuitePurchasing::get();
            } else {
                $data   = NetsuitePurchasing::where('last_update', $tanggal)->get();
            }
        } else {
            $data       = NetsuitePurchasing::where('last_update', $tanggal)->where('po_subsidiary', $subsidiary)->get();
        }
        return $data;
    }

    public function getDataSO(Request $request){

        $range      = $request->range ?? "";
        $subsidiary = $request->subsidiary ?? '';
        $tanggal    = $request->tanggal ?? date('Y-m-d');
        if ($subsidiary == '') {
            if ($request->tanggal == 'all') {
                $data = NetsuiteSalesOrder::get();
            } else {
                $data = NetsuiteSalesOrder::where('last_update', $tanggal)->get();
            }
        } else  if($range==""){
            $data = NetsuiteSalesOrder::where('last_update', $tanggal)->where('so_subsidiary', $subsidiary)->get();
        }else{
            $data = NetsuiteSalesOrder::where('updated_at' , '>' ,date('Y-m-d H:i:s', strtotime('- '.($range).' hours')))->where('so_subsidiary', $subsidiary)->get();
        }
        return $data;
    }

    public function getDataPOItermReceipt(Request $request){
        $subsidiary = $request->subsidiary ?? '';
        $tanggal    = $request->tanggal ?? date('Y-m-d');
        if ($subsidiary == '') {
            $data       = NetsuitePOItemReceipt::where('last_update', 'like', '%'.$tanggal.'%')->get();
        } else {
            $data       = NetsuitePOItemReceipt::where('last_update', 'like', '%'.$tanggal.'%')->where('po_subsidiary', $subsidiary)->get();
        }
        return $data;
    }

    public function getDataBOM(Request $request){

        $subsidiary = $request->subsidiary ?? '';
        $tanggal = $request->tanggal ?? date('Y-m-d');

        if ($subsidiary == '') {
            if ($request->tanggal == 'all') {
                $data = NetsuiteBom::get();
            } else {
                $data = NetsuiteBom::where('last_update', $tanggal)->get();
            }
        } else if($request->tanggal == "all" && $subsidiary != ''){
            $data = NetsuiteBom::where('subsidiary', 'like', '%'.$subsidiary.'%')->get();
        }else{
            $data = NetsuiteBom::where('last_update', $tanggal)->where('subsidiary', 'like', '%'.$subsidiary.'%')->get();
        }
        return $data;
    }

    public function getDataLocation(Request $request){

        $subsidiary = $request->subsidiary ?? 'CGL';

        if($request->tanggal == "all"){
            $data = NetsuiteLocation::where('subsidiary', $subsidiary)->get();
        }else{
            $tanggal = $request->tanggal ?? date('Y-m-d');
            $data = NetsuiteLocation::where('last_update', $tanggal)->where('subsidiary', $subsidiary)->get();
        }

        return $data;
    }

    public function getDataCustomer(Request $request){

        $subsidiary = $request->subsidiary ?? '';
        $tanggal = $request->tanggal ?? date('Y-m-d');

        if ($subsidiary == '') {
            if ($request->tanggal == 'all') {
                $data = NetsuiteCustomer::get();
            } else {
                $data = NetsuiteCustomer::where('last_update', $tanggal)->get();
            }
        } else if($request->tanggal == "all" && $subsidiary != ''){
            $data = NetsuiteCustomer::where('subsidiary', $subsidiary)->get();
        } else {
            $data = NetsuiteCustomer::where('last_update', $tanggal)->where('subsidiary', $subsidiary)->get();
        }

        return $data;
    }

    public function getDataVendor(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $subsidiary = $request->subsidiary ?? '';
        if ($subsidiary == '') {
            if ($request->tanggal == 'all') {
                $data = NetsuiteVendor::get();
            } else {
                $data = NetsuiteVendor::where('last_update', $tanggal)->get();
            }
        } else if ($request->tanggal == "all" && $subsidiary != '') {
            $data = NetsuiteVendor::where('subsidiary', $subsidiary)->get();
        }else{
            $data = NetsuiteVendor::where('last_update', $tanggal)->where('subsidiary', $subsidiary)->get();
        }

        return $data;
    }

    public function getDataItem(Request $request){
        $subsidiary = $request->subsidiary ?? '';
        $tanggal = $request->tanggal ?? date('Y-m-d');

        if ($subsidiary == '') {
            if ($request->tanggal == 'all') {
                $data = NetsuiteItem::get();
            } else {
                $data = NetsuiteItem::where('last_update', $tanggal)->get();
            }
        } else if($request->tanggal == "all" && $subsidiary != ''){
            $data = NetsuiteItem::where('subsidiary', 'like', '%'.$subsidiary.'%')->get();
        }else{
            $tanggal = $request->tanggal ?? date('Y-m-d');
            $data = NetsuiteItem::where('last_update', $tanggal)->where('subsidiary', 'like', '%'.$subsidiary.'%')->get();
        }

        return $data;
    }


    public function loginFromNetsuite(Request $request){
        $token  = $request->token;
        $role   = $request->role;

        $client = Client::where('token', $token)->first();
        if(!$client){
            return "TOKEN TIDAK VALID";
        }

        if($role=='purchasing'){
            $user   = User::where('name', 'purchasing')->first();
            if($user){
                $auth   = Auth::loginUsingId($user->id);
                return redirect(url('admin/dashboard'));
            }else{
                return "Error";
            }
        }elseif($role=='marketing'){
            $user   = User::where('name', 'marketing')->first();
            if($user){
                $auth   = Auth::loginUsingId($user->id);
                return redirect(url('admin/dashboard'));
            }else{
                return "USER TIDAK TERSEDIA";
            }
        }else{
            return "Error";
        }
    }
    public function AuthLoginFromNetsuite(Request $request){
        $token          = $request->_token;
        $subsidiary     = $request->subsidiary;
        $role           = $request->role;
        $key            = $request->key;
        // $href           = $request->href;

        $client         = Client::where('token', $token)->first();
        if(!$client){
            return "TOKEN TIDAK VALID";
        }

        if($subsidiary == 'EBA'){
            $href       = "http://eba2022.myddns.me/progress_report";
            // $href       = "http://eba.cyberolympus.com/progress_report";
            // $href       = "http://localhost:5000/progress_report";
        }else
        if($subsidiary =='CGL'){
            $href       = "http://cgl2022.myddns.me:8889/progress_report";
            // $href       = "http://cgl.cyberolympus.com/progress_report";
            // $href       = "http://localhost:5000/progress_report";
        }else{
            abort(404);
        }

        if($client){
            if($role == 'marketing'){
                $user                   = User::where('name', 'marketing')
                                                // ->where(function($q) use($subsidiary){
                                                //     if($subsidiary == 'EBA'){
                                                //         $q->where('company_id',2);
                                                //     }
                                                //     if($subsidiary == 'CGL'){
                                                //         $q->where('company_id',1);
                                                //     }
                                                // })
                                                ->first();

                if($user){
                    // $auth               = Auth::loginUsingId($user->id);
                    if($key == 'marketing'){
                        $string         = base64_encode("Kode acak kode diacak acak acak acak kode biar dapat kode kode yang diacak");
                        $url            = $href."/view?role=".$role."&_token=".$token."&name=".$subsidiary."&key=".$key."&GenerateToken=".$string;
                        return response()->json([
                            'status'    => 1,
                            'message'   => "success",
                            'url'       => $url
                        ],200);
                    }else
                    if($key == 'retur'){
                        $string         = base64_encode("hyperlink untuk SO CGL dan ditujukan untuk melihat progress QC Retur");
                        $url            = $href."/view?role=".$role."&_token=".$token."&name=".$subsidiary."&key=".$key."&GenerateToken=".$string;
                        return response()->json([
                            'status'    => 1,
                            'message'   => "success",
                            'url'       => $url
                        ],200);
                    }
                }else{
                    abort(404);
                }
            }
            else
            if($role == 'purchasing'){
                $user                   = User::where('name', 'purchasing')
                                                // ->where(function($q) use($subsidiary){
                                                //     if($subsidiary == 'EBA'){
                                                //         $q->where('company_id',2);
                                                //     }
                                                //     if($subsidiary == 'CGL'){
                                                //         $q->where('company_id',1);
                                                //     }
                                                // })
                                                ->first();
                if($user){
                    // $auth           = Auth::loginUsingId($user->id);
                    $string         = base64_encode("lor rel dul rel lor rel dol rel rel di lor rel enckrypsi acak acakaan");
                    $url            = $href."/view?role=".$role."&_token=".$token."&name=".$subsidiary."&key=".$key."&GenerateToken=".$string;
                    return response()->json([
                        'status'    => 1,
                        'message'   => "success",
                        'url'       => $url
                    ],200);
                }else{
                    abort(404);
                }
            }
            // else
            // if($role == 'retur'){
            //     $user               = User::where('name', 'marketing')->first();
            //     if($subsidiary=='' && $token == ''){
            //         abort(404);
            //     }else{
            //         $string         = base64_encode("hyperlink untuk SO CGL dan ditujukan untuk melihat progress QC Retur");
            //         $url            = $href."/view?role=retur&_token=".$token."&name=".$subsidiary."&GenerateToken=".$string;
            //         return response()->json([
            //             'status'    => 1,
            //             'message'   => "success",
            //             'url'       => $url
            //         ],200);
            //     }
            // }
        }
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{


    public function crawlSO(Request $req){
        $file = "https://docs.google.com/spreadsheets/d/e/2PACX-1vSzHx3PAkgNBdWlMGJLN7QGMwttt_c54-i1585CRhtrFtdpiL8ZZkd8LbXZqlLx3X6nlBJanfMBbVSl/pub?output=csv";

        $fileData=fopen($file,'r');

        $import_data = [];
        $no = 0;
        $update_data = 0;
        $insert_data = 0;
        while (($line = fgetcsv($fileData)) !== FALSE) {

            if($no!=0){
                $item                   = Item::where('nama', $line[2])->first();
                if($item){

                    $category           = Category::where('nama', $line[3])->first();
                    
                    $item->nama         = $line[2];
                    $item->category_id  = $category->id ?? "1";
                    $item->save();
                    $update_data++;

                }else{

                    $category           = Category::where('nama', $line[3])->first();

                    $item               = new Item();
                    $item->nama         = $line[2];
                    $item->category_id  = $category->id ?? "1";
                    $item->slug         = strtolower(str_replace(" ", "-", preg_replace('/[^A-Za-z0-9 !@#$%^&*()-.]/u','', strip_tags(strtolower(str_replace(" ", "-", $line[2]))))));
                    $item->status       = 1;
                    $item->save();
                    $insert_data++;

                }

                $import_data[] = $item;
            }
            $no++;
        }

        fclose($fileData);

        if($req->next=="item"){
            return redirect()->route( 'item.index' )->with( [ 'status' => "1" , 'message' => "Crawl selesai, ". $insert_data." Data baru, ". $update_data." Data Update, "] );
        }else{
            $response['meta']["status"]             =   200;
            $response['meta']["message"]            =   "OK";
            $response['response']["data_insert"]    =   $insert_data;
            $response['response']["data_update"]    =   $update_data;
            $response['response']["data"]           =   $import_data;

            return response()->json($response, $response['meta']["status"]);
        }

    }

    public function store(Request $request)
    {
        $proceed = false;
        DB::beginTransaction();

        $order                      =   new Order;
        $order->id_so               =   $request->internal_id_so ;
        $order->no_so               =   $request->nomor_so ;
        $order->no_po               =   $request->nomor_po ;
        $order->nama                =   $request->nama_customer ;
        $order->tanggal_kirim       =   $request->tanggal_kirim;
        $order->tanggal_so          =   $request->tanggal_so;
        $order->alamat              =   $request->alamat;
        $order->wilayah             =   $request->wilayah;
        $order->sales_id            =   $request->sales_rep;
        $order->keterangan          =   $request->memo;
        $order->sales_channel       =   $request->sales_channel;

        // $order->telp                =   $konsumen->telp ?? NULL;
        // $order->kelurahan           =   $konsumen->kelurahan ?? NULL;
        // $order->kecamatan           =   $konsumen->kecamatan ?? NULL;
        // $order->kota                =   $konsumen->kota ?? NULL;
        // $order->provinsi            =   $konsumen->provinsi ?? NULL;
        // $order->kode_pos            =   $konsumen->kode_pos ?? NULL;
        if ($order->save()) {
            $proceed = true;
        }

        $order_detail   =   json_decode($request->order_detail, FALSE);
        for ($x = 0; $x < COUNT($order_detail); $x++) {
            $item   =   Item::where('id', $order_detail[$x]->item_id)->first();
            $list                   =   new OrderItem;
            $list->order_id         =   $order->id;
            $list->sku              =   $order_detail[$x]->sku;
            $list->keterangan       =   $order_detail[$x]->description_item;
            $list->part             =   $order_detail[$x]->part;
            $list->qty              =   $order_detail[$x]->qty;
            $list->unit             =   $order_detail[$x]->unit;
            $list->rate             =   $order_detail[$x]->rate;

            // $list->item_id          =   $order_detail[$x]->item_id;
            // $list->nama_detail      =   $item->nama;
            // $list->berat            =   $order_detail[$x]->berat;
            // $list->harga            =   $order_detail[$x]->harga ?? NULL;
            if ($list->save()) {
                $proceed = true;
            } else {
                $proceed = false;
            }
        }

        if ($proceed == true) {
            DB::commit();
            $response['meta']["status"]     =   200;
            $response['meta']["message"]    =   "OK";
            $response["response"] =
            [
                "nama"          =>  $request->nama_customer,
                "tanggal_kirim" =>  $request->tanggal_kirim,
                "order_detail"  =>  $request->order_detail,
            ];
        } else {
            DB::rollBack();
            $response['meta']["status"]     =   400;
            $response['meta']["message"]    =   "Server Error";
            $response["response"] =
            [];
        }


        return response()->json($response, $response['meta']["status"]);
    }

    public function getData(Request $request)
    {
        $data   =   Order::where(function($query) use ($request){
                        if ($request->kode) {
                            $query->where('kode', $request->kode);
                        }
                    })->get();


        $row    =   [];
        foreach ($data as $item) {
            $row[]  =   [
                'data_order'    =>  [
                    'data_customer' =>  [
                        'kode'          =>  $item->kode,
                        'nama'          =>  $item->nama,
                        'tanggal_kirim' =>  $item->tanggal_kirim,
                        'nomor_invoice' =>  $item->no_invoice ? $item->nomor_invoice : NULL,
                    ],
                    'data_item_order'   =>  $this->list_item($item)
                ]
            ];
        }

        return $row;
    }

    private function list_item($item)
    {
        $row    =   [];
        foreach ($item->daftar_order as $item) {
            $row[]  =   [
                'nama_item'     =>  $item->nama_detail,
                'jumlah_item'   =>  $item->qty,
                'berat_item'    =>  $item->berat,
            ];
        }

        return $row;
    }
}

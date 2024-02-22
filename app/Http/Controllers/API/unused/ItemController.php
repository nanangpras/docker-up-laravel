<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function store(Request $request)
    {
        if (Category::find($request->kategori)) {
            $post                   =   new Item;
            $post->nama             =   $request->nama_item;
            $post->jenis            =   $request->jenis;
            $post->main_product     =   $request->main_product;
            $post->by_product       =   $request->by_product;
            $post->sku              =   $request->sku;
            $post->code_item        =   $request->kode_item;
            $post->category_id      =   $request->kategori;
            $post->status           =   $request->status;
            $post->save();

            $response['meta']["status"]     =   200;
            $response['meta']["message"]    =   "OK";
            $response["response"] =
            [
                "nama_item"     =>  $request->nama_item,
                "jenis"         =>  $request->jenis,
                "main_product"  =>  $request->main_product,
                "by_product"    =>  $request->by_product,
                "sku"           =>  $request->sku,
                "kode_item"     =>  $request->kode_item,
                "kategori"      =>  $request->kategori,
                "status"        =>  $request->status
            ];

        } else {
            $response['meta']["status"]     =   400;
            $response['meta']["message"]    =   "Category not found";
        }

        return response()->json($response, $response['meta']["status"]);

    }

    public function crawlItem(Request $req){
        $file = "https://docs.google.com/spreadsheets/d/e/2PACX-1vT-5Nv2xiD4b7RZoftjr3WkTkl2rBMxIpPZ8ZymayeCg7pbea4tU1VnLc7ivTUEf_1FofEGrYdz2hq_/pub?output=csv";

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
}

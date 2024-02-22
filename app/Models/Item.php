<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Evis;
use App\Models\Grading;
use App\Models\Product_gudang;
use App\Models\Chiller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class Item extends Model
{

    use SoftDeletes;
    protected $appends  =   ['sisa_qty', 'sisa_berat'];

    public function getSisaQtyAttribute()
    {
        return  Chiller::where('item_id', $this->id)
                ->where('status', 2)
                ->sum('stock_item');
    }

    public function getSisaBeratAttribute()
    {
        return  Chiller::where('item_id', $this->id)
                ->where('status', 2)
                ->sum('stock_berat');
    }

    public static function item_sku($sku)
    {
        return Item::where('sku', $sku)->withTrashed()->first();
    }

    public static function get_item_internal_id_by_id($id)
    {
        $item = Item::where('id', $id)->first();

        if($item){
            return $item->netsuite_internal_id;
        }else{
            return "";
        }
    }

    public function itemkat()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public static function arrayRegu()
    {
        $arrayRegu = [
            ['nama' => 'Whole', 'value' => 'whole'],
            ['nama' => 'Parting', 'value' => 'parting'],
            ['nama' => 'Marinasi', 'value' => 'marinasi'],
            ['nama' => 'Frozen', 'value' => 'frozen'],
            ['nama' => 'Boneless', 'value' => 'boneless'],
            ['nama' => 'Evis', 'value' => 'evis'],
        ];
    
        return $arrayRegu;
    }

    public static function item_jenis($id)
    {
        $item  = Item::find($id);
        $jenis = "";
        if($item){

            // Check ayam KARKAS dan ayam utuh
            if (substr($item->sku, 0, 5) == "12111" || substr($item->sku, 0, 5) == "12112") {
                $jenis = "normal";
            // Check ayam memar
            } elseif (substr($item->sku, 0, 5) == "12113") {
                $jenis = "memar";
            // Check ayam pejantan
            }elseif (substr($item->sku, 0, 4) == "1213" || substr($item->sku, 0, 4) == "1223") {
                $jenis = "pejantan";
            // Check ayam parent
            }elseif (substr($item->sku, 0, 4) == "1214" || substr($item->sku, 0, 4) == "1224") {
                $jenis = "parent";
            // Check ayam kampung
            }elseif (substr($item->sku, 0, 4) == "1212" || substr($item->sku, 0, 4) == "1222") {
                $jenis = "kampung";
            }

        }
        return $jenis;
    }

    public function itemevi()
    {
        return $this->hasMany(Evis::class, 'id', 'item_id')->withTrashed();
    }

    public function itemgrad()
    {
        return $this->hasMany(Grading::class, 'id', 'item_id')->withTrashed();
    }

    public function itemproductgudang()
    {
        return $this->hasMany(Product_gudang::class, 'id', 'product_id')->withTrashed();
    }

    public function list_item()
    {
        return $this->hasMany(Chiller::class, 'item_id', 'id')->where('jenis', 'masuk')->where('status', 2);
    }
    public function list_item_bonless()
    {
        return $this->hasMany(Chiller::class, 'item_id', 'id')->where('jenis', 'masuk')->where('status', 2)->where('type', 'bahan-baku');
    }

    public static function daftar_sku($jenis)
    {
        $item_parting = Item::where('nama', "LIKE", '%AYAM PARTING%')->get();
        $item =  Item::where(function($query) use ($jenis) {
                    if ($jenis == 'whole') {
                        if(env('NET_SUBSIDIARY', 'CGL')=="EBA"){
                            $query->whereIn('category_id', [5, 6, 11, 14, 1,17]);
                        }else{
                            $query->whereIn('category_id', [1,17]);
                        }
                        $query->orWhere('nama', "LIKE", "%tunggir%");
                        $query->orWhere('nama', "LIKE", "%lemak%");
                        $query->orWhere('nama', "LIKE", "%maras%");
                        $query->orWhere('nama', "LIKE", "%triming%");
                        $query->orWhere('nama', "LIKE", "KEPALA LEHER BROILER");
                        $query->orWhereIn('sku', ['1211600015', '1211600012']);

                        
                    }
                    if ($jenis == 'parting') {
                        if(env('NET_SUBSIDIARY', 'CGL')=="EBA"){
                            $query->whereIn('category_id', [5, 6, 11, 2]);
                        }else{
                            $query->whereIn('category_id', [5, 6, 11, 2]);
                        }
                        $query->orWhere('nama', "LIKE", "%tunggir%");
                        $query->orWhere('nama', "LIKE", "%lemak%");
                        $query->orWhere('nama', "LIKE", "%maras%");
                        $query->orWhere('nama', "LIKE", "%memar%");
                        $query->orWhere('nama', "LIKE", "%triming%");
                        
                    }
                    if ($jenis == 'marinasi') {
                        if(env('NET_SUBSIDIARY', 'CGL')=="EBA"){
                            $query->whereIn('category_id', [5, 6, 11, 3,5,6, 5, 6, 11, 2]);
                        }else{
                            $query->whereIn('category_id', [3,5,6]);
                        }
                        $query->orWhere('nama', "LIKE", "%tunggir%");
                        $query->orWhere('nama', "LIKE", "%lemak%");
                        $query->orWhere('nama', "LIKE", "%maras%");
                        $query->orWhere('nama', "LIKE", "%triming%");
                        $query->orWhere('nama', "LIKE", "%marinasi%");
                        $query->orWhereIn('sku', ['1211600033']);
                    }
                    if ($jenis == 'frozen') {
                        if(env('NET_SUBSIDIARY', 'CGL')=="EBA"){
                            $query->whereIn('category_id', [5, 6, 11, 7,8,9,10]);
                        }else{
                            $query->whereIn('category_id', [18,7,8,9,10]);
                        }
                        $query->orWhere('nama', "LIKE", "%FROZEN%");
                        $query->orWhere('nama', "LIKE", "%tunggir%");
                        $query->orWhere('nama', "LIKE", "%lemak%");
                        $query->orWhere('nama', "LIKE", "%maras%");
                        $query->orWhere('nama', "LIKE", "%triming%");
                        $query->orWhere('nama', "LIKE", "%UTUH PEJANTAN FROZEN%");
                    }
                })->where(function($query2){
                    $query2->where('nama', 'not like', '%(RM)%');
                    // $query2->where('nama', 'not like', '%UTUH%');
                    $query2->where('nama', 'not like', '%REPACK%');
                    $query2->where('nama', 'not like', '%THAWING%');
                    $query2->where('nama', 'not like', '%KAMPUNG%');
                    // $query2->where('nama', 'not like', '%PEJANTAN%');
                    // $query2->where('nama', 'not like', '%PARENT%');
                })
                ->get();
                
        if ($jenis == 'parting') {
            $merged = $item_parting->merge($item);
            // $result = $merged->all();
            // dd($merged);
            return $merged;
        }else{
            return $item;
        }
    }

    public static function daftar_sku_update($jenis)
    {
        $idcategory     = array();
        if($jenis != 'frozen'){
            if($jenis =='marinasi'){
                $query          = Category::where('nama','=','M')->get();
            }else 
            if($jenis == 'whole'){
                    $query          = Category::where(function($q) use ($jenis){
                                                    $q->where('nama','LIKE','%'.$jenis.'%');
                                                    $q->orwhere('nama','LIKE','%carcass%');
                                                })
                                                ->where('nama','NOT LIKE','%frozen%')
                                                ->get();
            }else{
                $query          = Category::where('nama','LIKE',''.$jenis.'%')->where('nama','NOT LIKE','%frozen%')->get();
            }
        }else{
            $query          = Category::where('nama','LIKE','%'.$jenis.'%')->get();
        }

        if($query->count() > 0){
            foreach($query as $lp){
                $idcategory[]  = $lp->id;
            }
        }else{
            $idcategory     = [];
        }
        $newData            = $idcategory;
        if($newData){
            $stringData     = implode(",",$newData);
            $item           = Item::where(function($q){
                                        $q->where('subsidiary','LIKE', '%'.Session::get('subsidiary').'%');
                                        $q->orWhere('code_item','LIKE','%'.Session::get('subsidiary').'%');
                                    })
                                    ->where(function($q2) use ($stringData,$jenis){
                                        $q2->whereIn('category_id',[$stringData]);
                                        $q2->orWhere('access','LIKE','%'.$jenis.'%');
                                        $q2->where('category_id','!=', '25');
                                    })
                                    ->where(function($s){
                                        $s->where('nama', 'NOT LIKE', '%(RM)%');
                                        $s->where('nama', 'NOT LIKE', '%REPACK%');
                                        $s->where('nama', 'NOT LIKE', '%KAMPUNG%');
                                        $s->where('nama', 'NOT LIKE', '%THAWING%');
                                        // $s->where('nama', 'NOT LIKE', '%UTUH%');
                                        // $s->where('nama', 'NOT LIKE', '%AKUMULASI SUSUT%');
                                    })
                                    // ->orWhere('nama', "LIKE", "%tunggir%")
                                    // ->orWhere('nama', "LIKE", "%lemak%")
                                    // ->orWhere('nama', "LIKE", "%maras%")
                                    // ->orWhere('nama', "LIKE", "%triming%")
                                    // ->orderBy('nama','ASC')
                                    ->get();
            return $item;
        }else{
            return [];
        }
    }
    
    public static function logso($key, $id){
        $data = Item::find($id);
        if($key == 'sku'){
            return $data->sku ?? "";
        } else {
            return $data->nama ?? "";
        }
    }


    public static function item_fresh_to_frozen($id, $nama){
        $item_finish                =   Item::where('nama', str_replace(' FROZEN', '', $nama)." FROZEN")->first();

        if(!$item_finish){
            $sku_lama = Item::find($id)->sku ?? 0;

            $sku_baru = substr($sku_lama, 0,2)."2".substr($sku_lama, 3,7);
            $item_finish = Item::where('sku', $sku_baru)->first();
        }

        return $item_finish;
    }

    public static function item_frozen_to_fresh($id, $nama){
        $item_finish                =   Item::where('nama', str_replace(' FROZEN', '', $nama))->first();

        if(!$item_finish){
            $sku_lama = Item::find($id)->sku ?? 0;

            $sku_baru = substr($sku_lama, 0,2)."1".substr($sku_lama, 3,7);
            $item_finish = Item::where('sku', $sku_baru)->first();
        }

        return $item_finish;
    }

    public static function plastik_group($nama){
           
            $plastik_group = "POLOS";
            if (str_contains($nama, 'MOJO')) {
                $plastik_group = "MOJO";
            } elseif (str_contains($nama, 'AVIDA')) {
                $plastik_group = "AVIDA";
            } elseif (str_contains($nama, 'MEYER')) {
                $plastik_group = "MEYER";
            } elseif ($nama == ""){
                $plastik_group = $nama;
            } else {
                $plastik_group = "POLOS";
            }

            return $plastik_group;
    }

    public static function getDataFromItem($id,$data){
        $query          = Item::select($data)->where('id',$id)->get();
        if($query->count() > 0){
            foreach($query as $value){
                $hasil  = $value->$data;
            }
        }else{
            $hasil      = '';
        }

        return $hasil;
    }
}

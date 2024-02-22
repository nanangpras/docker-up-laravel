<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\FreestockList;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class FreestockTemp extends Model
{
    use SoftDeletes;
    protected $table = 'free_stocktemp';

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function stocklist()
    {
        return $this->belongsTo(FreestockList::class, 'freestocklist_id', 'id');
    }

    public function chiller()
    {
        return $this->belongsTo(Chiller::class, 'chiller_id', 'id');
    }

    public function freetempchiller()
    {
        return $this->belongsTo(Chiller::class, 'id', 'table_id')->select('id')->where('table_name', 'free_stocktemp');
    }

    public function tempchiller()
    {
        return $this->belongsTo(Chiller::class, 'id', 'table_id')->where('table_name', 'free_stocktemp');
    }

    public function free_stock()
    {
        return $this->belongsTo(Freestock::class, 'freestock_id', 'id') ;
    }

    public function konsumen()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function bumbu()
    {
        return $this->belongsTo(Bumbu::class, 'bumbu_id', 'id');
    }

    public function bumbu_detail()
    {
        return $this->belongsTo(BumbuDetail::class, 'bumbu_detail_id', 'id');
    }


    public static function getKodeChiller($table_id,$regu){
        $query = Chiller::select('id')->where('table_id',$table_id)->where('table_name','free_stocktemp')->where('regu',$regu)->get();
        if(count($query) > 0){
            foreach($query as $q){
                $result     = $q->id;
            }
        }else{
            $result     = '';
        }
        return $result;
    }

    public static function getKodeABF($table_id){
        $query = Abf::select('id')->where('table_id',$table_id)->where('table_name','chiller')->get();
        return $query;
    }
    public static function getfreestockid($table_id){
        $query = FreestockTemp::select('freestock_id')->where('id',$table_id)->get();
        if(count($query) > 0){
            foreach($query as $q){
                $result     = $q->freestock_id;
            }
        }else{
            $result         = '';
        }
        return $result;
    }

    public static function cek_non_wo_produksi($regu,$tanggal_awal,$tanggal_akhir)
    {
        $cek_prod = FreestockTemp::select('item_id', DB::raw("SUM(berat) AS berat"), DB::raw("SUM(qty) AS qty"),'items.sku')
                        ->where('free_stock.regu', $regu)
                        ->where('free_stock.status', '3')
                        // ->whereNull('free_stock.orderitem_id')
                        ->whereNull('free_stock.netsuite_send')
                        ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                        ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
                        ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')
                        ->groupBy('item_id')
                        ->get();
        return $cek_prod;
    }
}

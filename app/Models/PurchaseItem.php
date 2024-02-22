<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class PurchaseItem extends Model
{
    //
    protected $table    =   'purchase_item';
    use SoftDeletes;

    public function purchase()
    {
        return $this->belongsTo(Purchasing::class, 'purchasing_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public static function totalPenerimaan($itemPo, $purchasingId){
        $query = DB::select("select item_po, purchasing_id,jumlah_ayam,berat_ayam, SUM(terima_berat_item) AS total_berat_terima, SUM(terima_jumlah_item) AS total_jumlah_terima 
                FROM purchase_item WHERE item_po='".$itemPo."' AND purchasing_id='".$purchasingId."' AND deleted_at IS NULL GROUP BY item_po, purchasing_id, jumlah_ayam, berat_ayam");
        
        return $query ?? [];
    }

    public static function detailPoItemReceipt($itemPo){
        $query = DB::select("select item_po, internal_id_po, purchasing_id, harga, ukuran_ayam, keterangan,jenis_ayam, 
                            berat_ayam, jumlah_ayam, SUM(terima_berat_item) AS total_berat_terima, SUM(terima_jumlah_item) AS total_qty_terima 
                            FROM purchase_item WHERE purchasing_id='".$itemPo."' AND deleted_at IS NULL GROUP BY item_po, internal_id_po, keterangan, harga,ukuran_ayam,jenis_ayam,berat_ayam,jumlah_ayam
        
        ");

        return $query ?? [];
    }
}

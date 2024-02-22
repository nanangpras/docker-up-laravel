<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abf extends Model
{
    //
    use SoftDeletes;
    protected $table = 'abf';
    protected $appends  =   ['status_abf','asal','sisa_qty','sisa_berat'];

    public function konsumen()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function abforderitem()
    {
        return $this->belongsTo(OrderItem::class, 'item_id', 'id');
    }

    public function abf_chiller()
    {
        return $this->belongsTo(Chiller::class, 'table_id', 'id');
    }

    public function abf_freetemp()
    {
        return $this->belongsTo(FreestockTemp::class, 'table_id', 'id');
    }

    public function to_orderbb()
    {
        return $this->belongsTo(Bahanbaku::class, 'table_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
    public function itemlama()
    {
        return $this->belongsTo(Item::class, 'item_id_lama', 'id');
    }

    public function returitem()
    {
        return $this->belongsTo(ReturItem::class, 'table_id', 'retur_id')->where('item_id', $this->item_id);
    }

    public function thawing()
    {
        return $this->belongsTo(BomItem::class, 'item_id', 'item_id')->whereIn('bom_id', Bom::select('id')->where('bom_name', 'LIKE', "%THAWING%"));
    }

    public function abf_gudang()
    {
        return $this->hasMany(Product_gudang::class, 'table_id', 'id')->where('table_name', 'abf');
    }

    public function hasil_timbang()
    {
        return $this->hasMany(Product_gudang::class, 'table_id', 'id')->where('table_name', 'abf')->where('status', '0');
    }

    public function hasil_timbang_selesai()
    {
        return $this->hasMany(Product_gudang::class, 'table_id', 'id')->where('table_name', 'abf')->where('status', '2');
    }
    public function detailTimbangCS()
    {
        return $this->hasMany(Product_gudang::class, 'table_id', 'id')->where('table_name', 'abf')->whereIn('status', ['0','2']);
    }

    public function getSisaQtyAttribute()
    {
        return $this->qty_item - Product_gudang::where('table_name', 'abf')->where('table_id', $this->id)->where('status', '0')->sum('qty');
    }
    public function getSisaBeratAttribute()
    {
        return $this->berat_item - Product_gudang::where('table_name', 'abf')->where('table_id', $this->id)->where('status', '0')->sum('berat');
    }

    public function getStatusAbfAttribute()
    {
        if ($this->status == 1) {
            return "<span class='status status-info'>Pending</span>";
        }
        if ($this->status == 2) {
            return "<span class='status status-success'>Masuk</span>";
        }
        if ($this->status == 4) {
            return "<span class='status status-danger'>Keluar</span>";
        }
    }

    public function getAsalAttribute()
    {
        if ($this->asal_tujuan == 'kepala_produksi' or $this->asal_tujuan == 'free_stock') {
            return 'Free Stock';
        }
        elseif ($this->asal_tujuan == 'orderproduksi') {
            return 'Order Produksi';
        }
        else {
            return $this->asal_tujuan;
        }
    }


    public static function recalculate_abf($id){
        $abf = Abf::find($id);
        if($abf){
            $berat_awal     = $abf->berat_awal;
            $qty_awal       = $abf->qty_awal;

            $berat_pending  = 0;
            $qty_pending    = 0;
            foreach ($abf->hasil_timbang as $i => $row){
                $berat_pending  = $berat_pending+$row->berat_awal;
                $qty_pending    = $qty_pending+$row->qty_awal;
            }

            $berat_selesai  = 0;
            $qty_selesai    = 0;
            foreach ($abf->hasil_timbang_selesai as $i => $row){
                $berat_selesai  = $berat_selesai+$row->berat_awal;
                $qty_selesai    = $qty_selesai+$row->qty_awal;
            }

            $abf->berat_item = $berat_awal-$berat_pending-$berat_selesai;
            $abf->qty_item = $qty_awal-$qty_pending-$qty_selesai;

            $abf->save();
        }
    }
}

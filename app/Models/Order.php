<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ekspedisi_rute;
use App\Models\Customer;
use App\Models\Product_retur;
use App\Models\Sales_invoice_item;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    //
    protected $table    =   'orders';
    protected $appends  =   ['nomor_invoice', 'status_order', 'jumlah_rute'];
    use SoftDeletes;

    public function getNomorInvoiceAttribute()
    {
        return "CGL.INV" . date('Ym', strtotime($this->invoice_created_at)) . str_pad((string)$this->no_invoice, 3, "0", STR_PAD_LEFT) ;
    }

    public function ordercustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function list_order()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function get_do()
    {
        return  $this->hasMany(Bahanbaku::class, 'order_id', 'id')
                ->groupBy('no_do')->select(DB::raw("SUM(bb_item) AS qty"), DB::raw("SUM(bb_berat) AS berat"), 'no_do', 'nama');
                // ->whereNotIn('no_do', Ekspedisi_rute::select('no_do'));
    }

    public function daftar_order()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function list_daftar_order()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
    public function daftar_retur()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id')->where('retur_berat', '>', 0);
    }
    public function daftar_order_full()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id')->orderBy('nama_detail', 'ASC')->where('deleted_at', NULL);
    }
    public function daftar_order_bb()
    {
        return $this->hasMany(Bahanbaku::class, 'order_id', 'id');
    }

    public function daftar_order_frozen()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id')->where('nama_detail', 'LIKE', "%FROZEN%");
    }

    public function list_retur()
    {
        return $this->hasMany(Retur::class, 'id_so', 'id_so');
    }

    public function orderproductretur()
    {
        return $this->hasMany(Product_retur::class, 'id', 'order_id');
    }

    public function orderekpedisirute()
    {
        return $this->hasMany(Ekspedisi_rute::class, 'no_so', 'no_so');
    }

    public function ordersalesitem()
    {
        return $this->hasMany(Sales_invoice_item::class, 'id', 'order_id');
    }

    public function netsuite_closed(){
        return $this->hasOne(MarketingSO::class,'no_so','no_so');
    }
    public function order_batal(){
        return $this->hasOne(MarketingSO::class,'no_so','no_so')->where('status',0);
    }
    public function marketing_so()
    {
        return $this->belongsTo(MarketingSO::class, 'no_so', 'no_so');
    }
    public function marketingnama()
    {
        return $this->belongsTo(Marketing::class, 'sales_id', 'netsuite_internal_id');
    }

    public function fulfillNetsuite() {
        return $this->hasOne(Netsuite::class, 'tabel_id', 'id')->where('label', 'itemfulfill')->where('tabel', 'orders')->select('id','failed','status','response_id','document_code','trans_date','document_no','count','respon_time','tabel_id');
    }

    public function cekDataOrderBahanBaku(){
        return $this->hasMany(Bahanbaku::class,'order_id','id')->where('status', 1);
    }

    public function getNetsuite(){
        return $this->hasOne(Netsuite::class, 'tabel_id', 'id')->where('label', 'itemfulfill')->where('tabel', 'orders');
    }

    public static function item_order($id, $type = FALSE)
    {
        if ($type == 'bonless') {
            return OrderItem::orderBy('nama_detail', 'ASC')->where('order_id', $id)->where('retur_status', null)->whereIn('item_id', Item::select('id')->where('category_id', 5))->get();
        }
        elseif ($type == 'parting') {
            return OrderItem::orderBy('nama_detail', 'ASC')->where('order_id', $id)->where('retur_status', null)->whereIn('item_id', Item::select('id')->where('category_id', 2))->get();
        }
        elseif ($type == 'mariansi') {
            return OrderItem::orderBy('nama_detail', 'ASC')->where('order_id', $id)->where('retur_status', null)->whereIn('item_id', Item::select('id')->where('category_id', 3))->get();
        }
        elseif ($type == 'whole') {
            return OrderItem::orderBy('nama_detail', 'ASC')->where('order_id', $id)->where('retur_status', null)->whereIn('item_id', Item::select('id')->where('category_id', 1))->get();
        }
        elseif ($type == 'frozen') {
            return OrderItem::orderBy('nama_detail', 'ASC')->where('order_id', $id)->where('retur_status', null)->whereIn('item_id', Item::select('id')->whereIn('category_id', [7,8,9,10,11]))->get();
        }
        elseif ($type == 'sampingan') {
            return OrderItem::orderBy('nama_detail', 'ASC')->where('order_id', $id)->where('retur_status', null)->whereIn('item_id', Item::select('id')->where('category_id', 4))->get();
        }
        else {
            return OrderItem::orderBy('nama_detail', 'ASC')->where('order_id', $id)->where('retur_status', null)->get();
        }
    }

    public static function order_regu($id, $type = FALSE)
    {

        $kategori = [];
        if ($type == 'boneless') {
            $kategori = [5,6, 11];
        } elseif ($type == 'parting') {
            $kategori = [2];
        } elseif ($type == 'parting marinasi') {
            $kategori = [3,9];
        } elseif ($type == 'whole chicken') {
            $kategori = [1];
        } elseif ($type == 'frozen') {
            $kategori = [7, 8, 9, 13];
        } elseif ($type == 'evis'){
            $kategori = [4];
        }

        if ($type!=FALSE) {
            return  OrderItem::where('order_id', $id)
                    ->where('retur_status', null)
                    ->whereIn('item_id', Item::select('id')
                    ->whereIn('category_id', $kategori))
                    ->get();
        }
        else {
            return OrderItem::where('order_id', $id)->where('retur_status', null)->get();
        }
    }

    public static function bahan_baku($id, $item)
    {
        return Bahanbaku::where('order_id', $id)->where('order_item_id', $item)->get();
    }

    public function getBahanBaku()
    {
        return $this->hasMany(Bahanbaku::class, 'order_id', 'id');
    }

    public static function nomor_invoice($tanggal)
    {
        $nomor  =   Order::select('no_invoice')
                    ->whereYear('tanggal_kirim', date('Y', strtotime($tanggal)))
                    ->whereMonth('tanggal_kirim', date('m', strtotime($tanggal)))
                    ->orderBy('no_invoice', 'DESC')
                    ->limit(1)
                    ->first();

        return $nomor ? (int)$nomor->no_invoice + 1 : 1 ;
    }

    public function getStatusOrderAttribute()
    {
        if ($this->status == null) {
            return "<div class='status status-danger'>Pending</div>";
        }
        // elseif ($this->status == 1) {
        //     return "<div class='status status-danger'>Proses Kepala Regu</div>";
        // }
        // elseif ($this->status == 2) {
        //     return "<div class='status status-warning'>Selesai Proses Kepala Regu</div>";
        // }
        elseif ($this->status == 5) {
            return "<div class='status status-warning'>Penyiapan Produksi</div>";
        }
        elseif ($this->status == 6) {
            return "<div class='status status-info'>Ekspedisi Siap Kirim</div>";
        }
        elseif ($this->status == 7) {
            return "<div class='status status-success'>Pengiriman</div>";
        }
        elseif ($this->status == 8) {
            return "<div class='status status-danger'>Retur</div>";
        }
        elseif ($this->status == 10) {
            return "<div class='status status-success'>Selesai</div>";
        }

    }

    // public function getStatusAttribute($value)
    // {
    //     if($value == 1){
    //         return "Pending";
    //     } else if ($value == 3){
    //         return "Verified";
    //     }
    // }

    public static function getInternalMemo($nomorSO, $idOrderItem) {
        $marketingSO     = MarketingSO::where('no_so', $nomorSO)->first();
        $orderitem       = OrderItem::where('id', $idOrderItem)->first();

        // return $orderitem;
        if ($marketingSO && $orderitem) {
            foreach ($marketingSO->itemActual as $itemList) {
                if ($orderitem->line_id == $itemList->line_id) {
                    return $itemList->internal_memo;
                }
            }
        } else {
            return '';
        }

    }


    public function getJumlahRuteAttribute()
    {
        return Ekspedisi_rute::where('no_so', $this->no_so)
                ->groupBy('no_so')
                ->count();
    }
}

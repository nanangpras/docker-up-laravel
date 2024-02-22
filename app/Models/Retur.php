<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Retur extends Model
{
    use SoftDeletes;

    protected $table    =   'retur';
    protected $appends  =   ['grup_item_retur', 'grup_ra_item_retur', 'grup_retur_nonso'];

    public function to_customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function data_order()
    {
        return $this->belongsTo(Order::class, 'id_so', 'id_so') ;
    }

    public function to_itemretur()
    {
        return $this->hasMany(ReturItem::class, 'retur_id', 'id');
    }

    public function with_so()
    {
        return $this->hasMany(ReturItem::class, 'retur_id', 'id');
    }

    public function to_order() {
        return $this->hasOne(Order::class, 'netsuite_internal_id', 'id_so');
    }

    public function to_netsuite() {
        return $this->hasOne(Netsuite::class, 'tabel_id', 'id')->where('label','receipt_return')->where('tabel', 'retur');
    }

    public function getItemTukarRetur() {
        return $this->hasOne(Adminedit::class, 'table_id', 'id')->where('table_name', 'retur_item')->where('type','retur')->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan');
    }

    public function getGrupItemReturAttribute()
    {
        return  ReturItem::select(DB::raw('SUM(retur_item.berat) AS abot'), DB::raw("SUM(retur_item.qty) AS total"), 'order_items.sku', 'retur_item.unit', 'order_items.item_id', 'retur_item.rate', 'retur_item.line_request')
                ->join('order_items', 'order_items.id', '=', 'retur_item.orderitem_id')
                ->where('retur_id', $this->id)
                ->groupBy('unit', 'orderitem_id', 'retur_item.line_request')
                ->get();
    }

    public function getGrupRaItemReturAttribute()
    {
        return  ReturItem::select(DB::raw('SUM(retur_item.berat) AS abot'), DB::raw("SUM(retur_item.qty) AS total"), 'order_items.sku', 'order_items.item_id', 'retur_item.rate', 'retur_item.line_request')
                ->join('order_items', 'order_items.id', '=', 'retur_item.orderitem_id')
                ->where('retur_id', $this->id)
                ->groupBy('orderitem_id', 'retur_item.line_request')
                ->get();
    }

    public function getGrupReturNonsoAttribute() {
        return  ReturItem::select(DB::raw('SUM(retur_item.berat) AS abot'), DB::raw("SUM(retur_item.qty) AS total"), 'sku', 'unit')
                ->where('retur_id', $this->id)
                ->groupBy('sku', 'unit')
                ->get();
    }

}

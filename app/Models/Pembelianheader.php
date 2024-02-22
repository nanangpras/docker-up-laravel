<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Pembelianheader extends Model
{
    use SoftDeletes;
    protected $table    = 'pembelianheader';
    protected $guarded = ['id'];
    protected $appends  = ['nomor_po_app'];

    public function list_pembelian()
    {
        return $this->hasMany(Pembelianlist::class, 'headbeli_id', 'id')->withTrashed();
    }

    public function list_ongkir()
    {
        return $this->belongsTo(Pembelianlist::class, 'id', 'headbeli_id')->whereIn('item_id', Item::select('id')->whereIn('sku', ['7000000009', '7000000011', '7000000012']));
    }

    public function list_po_item_receipt()
    {
        return $this->hasMany(PembelianItemReceipt::class, 'pembelian_header_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function getNomorPoAppAttribute()
    {
        $data   =   Pembelianheader::select(DB::raw('max(SUBSTRING(app_po, -4)) as nom'))
            ->where('subsidiary', Session::get('subsidiary'))
            ->whereMonth('tanggal', date('m'))
            ->orderBy('app_po', 'DESC')
            ->limit(1)
            ->first();

        if ($data->nom == null) {
            $nomor =  1;
        } else {
            $nomor =  $data->nom + 1;
        }

        return Session::get('subsidiary') . '.APP.PO.' . date('Y.m.') . str_pad((string)$nomor, 4, "0", STR_PAD_LEFT);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;
use App\Models\Production;
use App\Models\Nekropsi;
use App\Models\Purchasing_payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Purchasing extends Model
{
    //
    protected $table    =   'purchasing';
    protected $appends  =   ['wilayah_daerah', 'ayam_mati', 'nama_kandang', 'supir', 'result_mati', 'nama_po'];

     use SoftDeletes;

    public function purcsupp()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function purcprod()
    {
        return $this->hasMany(Production::class, 'purchasing_id', 'id');
    }

    public function purcitem()
    {
        return $this->belongsTo(Item::class, 'item_po', 'sku');
    }

    public function nekrop()
    {
        return $this->belongsTo(Nekropsi::class, 'id', 'purchasing_id');
    }

    public function purchasingpayment()
    {
        return $this->hasMany(Purchasing_payment::class, 'id', 'purchasing_id')->withTrashed();
    }

    public function unload_data()
    {
        return $this->hasMany(Production::class, 'purchasing_id', 'id')->where('no_urut', '!=', NULL);
    }

    public function retur_purchase()
    {
        return $this->hasMany(Returpurchase::class, 'purchasing_id', 'id') ;
    }

    public function getJumlahProduksiAttribute()
    {
        return  Production::select('id')
            ->where('purchasing_id', $this->id)
            ->where('no_urut', NULL)
            ->count();
    }

    public function getWilayahDaerahAttribute()
    {
        $data   =   Production::select('sc_wilayah')
            ->where('purchasing_id', $this->id)
            ->first();

        return $data->sc_wilayah ?? NULL;
    }

    public function getNamaKandangAttribute()
    {
        $data   =   Production::select('sc_nama_kandang')
            ->where('purchasing_id', $this->id)
            ->first();

        return $data->sc_nama_kandang ?? NULL;
    }

    public function getSupirAttribute()
    {
        $data   =   Production::select('sc_pengemudi')
            ->where('purchasing_id', $this->id)
            ->get();

        $row    =   '';
        foreach ($data as $item) {
            $row    .=  $item->sc_pengemudi . ', ';
        }

        return substr($row, 0, -2);
    }

    public function getAyamMatiAttribute()
    {
        $data   =   Antemortem::select(DB::raw("SUM(ayam_mati) as mati"))
            ->whereIn('production_id', Production::select('id')->where('purchasing_id', $this->id))
            ->first();

        return $data->mati ?? NULL;
    }

    public function getResultMatiAttribute()
    {
        $data   =   Antemortem::select('ayam_mati')
            ->whereIn('production_id', Production::select('id')->where('purchasing_id', $this->id))
            ->get();

        $row    =   '';
        foreach ($data as $item) {
            $row    .=  $item->ayam_mati . ', ';
        }

        return substr($row, 0, -2);
    }

    public static function daftar_produksi($id)
    {
        return Production::where('purchasing_id', $id)
            ->where('no_urut', NULL)
            ->get();
    }

    public static function daftar_polain($id)
    {
        return Production::where('purchasing_id', $id)
            ->where('sc_status', NULL)
            ->get();
    }


    public function getStatusPurchaseAttribute()
    {
        if ($this->status == 1) {
            return "<span class='status status-success'>Selesai</span>";
        }
        if ($this->status == 2) {
            return "<span class='status status-warning'>Pending</span>";
        }
        if ($this->status == 0) {
            return "<span class='status status-danger'>Batal</span>";
        }
    }

    public function getNamaPoAttribute()
    {
        if ($this->type_po == 'karkas') {
            return 'Karkas';
        } elseif ($this->type_po == 'frozen') {
            return 'Ayam Frozen';
        } elseif ($this->type_po == 'maklon') {
            return 'Maklon';
        }
    }

    public static function polainnya($id)
    {
        return Production::where('purchasing_id', $id)->where('sc_status', null)->count();
    }

    public function purchasing_item()
    {
        return $this->hasMany(PurchaseItem::class, 'purchasing_id', 'id');
    }

    public function purchasing_item2()
    {
        return $this->hasMany(PurchaseItem::class, 'purchasing_id', 'id')->groupBy('keterangan', 'description', 'harga', 'jenis_ayam', 'ukuran_ayam', 'jumlah_do')->orderBy('id');
    }

    public function purchasing_frozen()
    {
        return $this->hasMany(PurchaseItem::class, 'purchasing_id', 'id')->where('description', "LIKE", "%FROZEN%");
    }

    public static function cekPurchasingStatus($id, $tanggal){
        $newtgl         = date('Y-m-d', strtotime('+2 days', strtotime($tanggal)));
        $now            = Carbon::now();
        if($now > $newtgl){
            $query          = Purchasing::find($id);
            $result         = $query->status;
        }else{
            $result         = "OK";
        }   
        return $result;
    }
}

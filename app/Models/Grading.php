<?php

namespace App\Models;

use App\Classes\Applib;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Production;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Grading extends Model
{
    use SoftDeletes ;
    protected $table    =   'grading';
    protected $appends  =   ['tujuan'];

    public function graditem()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
    public function gradngToitem()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function gradprod()
    {
        return $this->belongsTo(Production::class, 'trans_id', 'id');
    }

    public static function urlChiller($idProduction, $berat, $qty, $tanggalProduksi, $itemId, $nomorPO) {
        $chiller = Chiller::where('table_id', $idProduction)
                    ->where('berat_item', $berat)
                    ->where('qty_item', $qty)
                    ->where('tanggal_produksi', $tanggalProduksi)
                    ->where('item_id', $itemId)
                    ->where('label', $nomorPO)
                    ->where('asal_tujuan', 'karkasbeli')
                    ->where('jenis', 'masuk')
                    ->first();

        return $chiller->id ?? '#';
    }

    public static function sebaran_karkas($id, $tanggal_awal, $tanggal_akhir, $type='total_item')
    {
        return  Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->whereBetween('tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('created_at','>=',Applib::DefaultTanggalAudit())))
                ->where('item_id', $id)
                ->sum($type);
    }

    public static function new_sebaran_karkas_persupplier($id, $supplier_id, $tanggal_awal, $tanggal_akhir, $type='total_item')
    {
        return   Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('created_at','>=',Applib::DefaultTanggalAudit()))
                        ->join('productions', 'productions.id', '=', 'grading.trans_id')->join('purchasing', 'purchasing.id', '=', 'productions.purchasing_id')
                        ->join('items', 'items.id', '=', 'grading.item_id')
                        ->join('supplier', 'supplier.id', '=', 'purchasing.supplier_id')
                        ->select('items.nama AS item_grading','supplier.nama AS nama_supplier', 'supplier.id AS id_supplier',  DB::raw('SUM(grading.total_item) AS qty_grading'))
                        ->groupBy('supplier.id', 'grading.item_id')
                        ->where('purchasing.supplier_id', $supplier_id)
                        ->where('grading.item_id', $id)
                        ->where('grading.created_at','>=',Applib::DefaultTanggalAudit())
                        ->sum($type);
    }
    public static function sebaran_karkas_supplier($id_supplier, $id_item, $tanggal_awal, $tanggal_akhir) {

        $datas = [];
        foreach($id_supplier as $supplier) {
            $datas[]   =        Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])->where('created_at','>=',Applib::DefaultTanggalAudit()))
                                ->join('productions', 'productions.id', '=', 'grading.trans_id')->join('purchasing', 'purchasing.id', '=', 'productions.purchasing_id')
                                ->join('items', 'items.id', '=', 'grading.item_id')
                                ->join('supplier', 'supplier.id', '=', 'purchasing.supplier_id')
                                ->select('items.nama AS item_grading','supplier.nama AS nama_supplier', 'supplier.id AS id_supplier',  DB::raw('SUM(grading.total_item) AS qty_grading'))
                                ->groupBy('supplier.id', 'grading.item_id')
                                ->where('purchasing.supplier_id', $supplier->id_supplier)
                                ->where('grading.item_id', $id_item)
                                ->where('grading.created_at','>=',Applib::DefaultTanggalAudit())
                                ->first();
        }

        return $datas;
    }

    public static function recalculate($grading)
    {
        $grad   =   Grading::where('id', $grading)->first() ;
        $data   =   Chiller::where('asal_tujuan', 'gradinggabungan')
                    ->where('item_id', $grad->item_id)
                    ->whereDate('tanggal_produksi', $grad->tanggal_potong)
                    ->where('status', 2)
                    ->first();

        if ($data) {
            $data->qty_item     =   Grading::whereDate('tanggal_potong', $grad->tanggal_potong)
                                    ->where('item_id', $grad->item_id)
                                    ->where('id', '!=', $grad->id)
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('total_item');

            $data->berat_item   =   Grading::whereDate('tanggal_potong', $grad->tanggal_potong)
                                    ->where('item_id', $grad->item_id)
                                    ->where('id', '!=', $grad->id)
                                    ->where('created_at','>=',Applib::DefaultTanggalAudit())
                                    ->sum('berat_item');

            $data->stock_item   =   $data->stock_item - $grad->total_item ;
            $data->stock_berat  =   $data->stock_berat - $grad->berat_item ;

            $data->save() ;
        }
    }

    public static function total_grading($produksi, $tipe)
    {
        return Grading::where('trans_id', $produksi)->sum($tipe) ;
    }

    public static function count_item_grading($item, $tanggal, $tipe)
    {
        return Grading::where('tanggal_potong', $tanggal)
                ->where('item_id', $item)
                ->whereNull('status')
                ->get()
                ->sum($tipe) ;
    }

    public function gradchill()
    {
        return $this->belongsTo(Chiller::class, 'item_id', 'item_id')->where('asal_tujuan', 'gradinggabungan')
            ->where('jenis', 'masuk')
            ->whereDate('tanggal_produksi', $this->tanggal_potong)
            ->where('type', 'bahan-baku')
            ->where('status', 2);
    }

    public function getTujuanAttribute()
    {
        if ($this->asal_tujuan == 'jualsampingan' and $this->table_name == 'evis') {
            return 'Sampingan (Evis)';
        }
        if ($this->asal_tujuan == 'stock' and $this->table_name == 'evis') {
            return 'Stock (Evis)';
        }
        if ($this->asal_tujuan == 'musnahkan' and $this->table_name == 'evis') {
            return 'Musnahkan (Evis)';
        }
        if ($this->asal_tujuan == 'kiriman' and $this->table_name == 'evis') {
            return 'Kiriman (Evis)';
        }
        if ($this->asal_tujuan == 'karyawan' and $this->table_name == 'evis') {
            return 'Karyawan (Evis)';
        }
        if ($this->asal_tujuan == 'baru' and $this->table_name == 'grading') {
            return 'Baru (Grading)';
        }
        if ($this->asal_tujuan == 'retur') {
            return 'Retur (Ekspedisi)';
        }
        if ($this->asal_tujuan == 'karyawan') {
            return 'Penjualan Karyawan';
        }
        if ($this->asal_tujuan == 'belum_terpakai') {
            return 'Belum Terpakai';
        }
        if ($this->asal_tujuan == 'boneless') {
            return 'Boneless';
        }
    }

    public static function ProsentaseGradingNormal($id,$status){
        if($status == '1' ){
            $sumAllGrading          = Grading::where('trans_id',$id)->sum('berat_item');
            $gradingNormal          = Grading::where('trans_id',$id)->where('jenis_karkas','normal')->sum('berat_item');
            $gradingUtuh            = Grading::where('trans_id',$id)->where('jenis_karkas','utuh')->sum('berat_item');

            $getProsentase          = (($gradingNormal + $gradingUtuh) / $sumAllGrading) * 100;
            return number_format($getProsentase,2);
        }else{
            return number_format(0,2);
        }
    }

    public static function ProsentaseGradingMemar($id,$status){
        if($status == '1'){
            $sumAllGrading          = Grading::where('trans_id',$id)->sum('berat_item');
            $gradingMemar           = Grading::where('trans_id',$id)->where('jenis_karkas','memar')->sum('berat_item');
            $getProsentase          = ($gradingMemar / $sumAllGrading) * 100;
            return number_format($getProsentase,2);
        }else{
            return number_format(0,2);
        }
    }
   
}

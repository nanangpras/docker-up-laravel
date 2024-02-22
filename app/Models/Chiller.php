<?php

namespace App\Models;

use App\Classes\Applib;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Production;
use Product_assign;
use App\Models\Item;
use App\Models\FreestockList;
use App\Models\Freestock;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class Chiller extends Model
{
    use SoftDeletes;
    protected $table    =   'chiller';
    protected $appends  =   ['tujuan', 'status_chiler', 'request_pending', 'status_free'];
    protected $fillable =   ['item_id'];

    public function getStatusChilerAttribute()
    {
        if ($this->status == 1) {
            return "Pending";
        }
        if ($this->status == 2) {
            return "Masuk";
        }
        if ($this->status == 3) {
            return "Pending";
        }
        if ($this->status == 4) {
            return "Keluar";
        }
    }

    public function getStatusFreeAttribute()
    {
        if ($this->table_name == 'free_stocklist' && $this->status == 3) {
            return 'Pending Chiller';
        }
        if ($this->table_name == 'free_stocklist' && $this->status == 4) {
            return 'Acc Chiller';
        }
    }

    public function getRequestPendingAttribute()
    {
        $frestock   =   FreestockList::where('chiller_id', $this->id)
                        ->whereIn('freestock_id', Freestock::select('id')->where('status', 1))
                        ->get();

        $total      =   0;
        $order      =   0;
        foreach ($frestock as $item) {
            $data   =   Chiller::where('table_id', $item->id)
                        ->where('status', 3)
                        ->first();

            $total  +=  $data->qty_item ?? 0;
        }

        if ($total > 0) {
            $order_bb   =   OrderItem::where('status', '!=', NULL)->get();

            foreach ($order_bb as $item) {
                $bb     =   Bahanbaku::where('order_item_id', $item->id)
                            ->where('chiller_id', $this->id)
                            ->whereIn('chiller_out', Chiller::select('id')->where('status', 3))
                            ->get();

                foreach ($bb as $row) {
                    $order  +=  $row->bb_item ?? 0;
                }
            }

        }

        return $total + $order;
    }

    public static function hitung_chiller($type, $asal, $id, $tanggal)
    {
        $grading    =   Chiller::where('item_id', $id)
                        ->where('asal_tujuan', $asal)
                        ->whereDate('tanggal_produksi', $tanggal)
                        ->where('status', 2)
                        ->get();

        $row    =   '';
        foreach ($grading as $raw) {

            $freestock  =   FreestockList::where('item_id', $id)
                            ->where('chiller_id', $raw->id)
                            ->sum($type);

            $alokasi    =   Bahanbaku::where('order_item_id', $id)
                            ->where('chiller_out', $raw->id)
                            ->sum($type == 'qty' ? 'bb_item' : 'bb_berat');

            $row    .=  ($raw) ? "<a target='_blank' class='text-dark' href='" . route('chiller.show', $raw->id) . "'>" : "" ;
            // $row    .=  number_format((($grading->sum($type == 'qty' ? 'qty_item' : 'berat_item')) - $freestock - $alokasi), $type == 'qty' ? 0 : 2);
            $row    .=  number_format(($type == 'qty' ? $raw->qty_item : $raw->berat_item) - ($freestock - $alokasi), $type == 'qty' ? 0 : 2);
            $row    .=  ($raw) ? "</a>" : "" ;
            $row    .=  "<br>" ;
        }

        return $row ;
    }

    public static function hitung_sisa($type, $tanggal)
    {
      
        // if($type=='qty'){

        //     $grading = Grading::whereIn('trans_id', Production::select('id')->whereDate('lpah_tanggal_potong', $tanggal))->sum('total_item');
        //     $freestock   =   FreestockList::where(function($query) use ($tanggal){
        //                         $query->whereIn('regu', ['whole', 'marinasi', 'parting', 'boneless', 'frozen']) ;
        //                     })
        //                     ->whereIn('freestock_id', Freestock::select('id')->where('status', 3)->whereDate('tanggal', $tanggal))
        //                     ->where(function($query) use ($tanggal){
        //                         $query->whereIn('chiller_id', Chiller::select('id')->where('status', 2)->where('asal_tujuan', 'gradinggabungan'));
        //                     })
        //                     ->sum('qty');

        //     $sampingan  = FreestockList::hitung_jual_sampingan($tanggal,'qty');
        // }else{

        //     $grading = Grading::whereIn('trans_id', Production::select('id')->whereDate('lpah_tanggal_potong', $tanggal))->sum('berat_item');
        //     $freestock   =   FreestockList::where(function($query) use ($tanggal){
        //                         $query->whereIn('regu', ['whole', 'marinasi', 'parting', 'boneless', 'frozen']) ;
        //                     })
        //                     ->whereIn('freestock_id', Freestock::select('id')->where('status', 3)->whereDate('tanggal', $tanggal))
        //                     ->where(function($query) use ($tanggal){
        //                         $query->whereIn('chiller_id', Chiller::select('id')->where('status', 2)->where('asal_tujuan', 'gradinggabungan'));
        //                     })
        //                     ->sum('berat');

        //     $sampingan  = FreestockList::hitung_jual_sampingan($tanggal,'berat');
        // }

        // return $grading - $freestock - $sampingan;
        return 0;
    }

    public function getTujuanAttribute()
    {
        if ($this->asal_tujuan == 'evisampingan' and $this->table_name == 'evis') {
            return 'Sampingan (Evis)';
        }
        if ($this->asal_tujuan == 'evisgabungan') {
            return 'Sampingan (Evis)';
        }
        if ($this->asal_tujuan == 'evisstock' and $this->table_name == 'evis') {
            return 'Stock (Evis)';
        }
        if ($this->asal_tujuan == 'evismusnahkan' and $this->table_name == 'evis') {
            return 'Musnahkan (Evis)';
        }
        if ($this->asal_tujuan == 'eviskiriman' and $this->table_name == 'evis') {
            return 'Kiriman (Evis)';
        }
        if ($this->asal_tujuan == 'eviskaryawan' and $this->table_name == 'evis') {
            return 'Karyawan (Evis)';
        }
        if ($this->asal_tujuan == 'baru' and $this->table_name == 'grading') {
            return 'Baru (Grading)';
        }
        if ($this->asal_tujuan == 'gradinggabungan') {
            return 'Baru (Grading)';
        }
        if ($this->asal_tujuan == 'karkasbeli') {
            return 'Grading Non LB';
        }
        if ($this->asal_tujuan == 'evisbeli') {
            return 'Evis Non LB';
        }
        if ($this->asal_tujuan == 'hasilbeli') {
            return 'Hasil Produksi Non LB';
        }
        if ($this->asal_tujuan == 'baru' and $this->table_name == 'production') {
            return 'Baru (Purchase)';
        }
        if ($this->asal_tujuan == 'retur') {
            return 'Retur (Ekspedisi)';
        }
        if ($this->asal_tujuan == 'karyawan') {
            return 'Penjualan Karyawan';
        }
        if ($this->asal_tujuan == 'free_stock') {
            return 'Free Stock';
        }
        if ($this->asal_tujuan == 'belum_terpakai') {
            return 'Belum Terpakai';
        }
        if ($this->asal_tujuan == 'kepala_produksi') {
            return 'Kepala Produksi';
        }
        if ($this->asal_tujuan == 'kepala_regu') {
            return 'Kepala Regu';
        }
        if ($this->asal_tujuan == 'boneless') {
            return 'Kepala Regu Boneless';
        }
        if ($this->asal_tujuan == 'krparting') {
            return 'Kepala Regu Parting';
        }
        if ($this->asal_tujuan == 'krpartingmarinasi') {
            return 'Kepala Regu Parting Marinasi';
        }
        if ($this->asal_tujuan == 'krwhole') {
            return 'Kepala Regu Whole Chicken';
        }
        if ($this->asal_tujuan == 'krfrozen') {
            return 'Kepala Regu Frozen';
        }
        if ($this->asal_tujuan == 'jualsampingan') {
            return 'Penjualan Sampingan';
        }
        if ($this->asal_tujuan == 'orderproduksi') {
            return 'Order';
        }
        if ($this->asal_tujuan == 'thawing') {
            return 'Thawing';
        }
        if ($this->asal_tujuan == 'open_balance') {
            return 'Open Balance';
        }
        if ($this->asal_tujuan == 'tukar_item') {
            return 'Tukar Item';
        }
    }

    public function chilprod()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }

    public function chillerproductass()
    {
        return $this->hasMany(Product_assign::class, 'id', 'chiller_id')->withTrashed();
    }

    public function chillitem()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function chillorderitem()
    {
        return $this->belongsTo(OrderItem::class, 'table_id', 'id');
    }

    public function chillerorderbb()
    {
        return $this->belongsTo(Bahanbaku::class, 'table_id', 'id');
    }

    public function order_item()
    {
        return $this->hasMany(Bahanbaku::class, 'chiller_out', 'id');
    }

    public function chillertofreestocktemp()
    {
        return $this->belongsTo(FreestockTemp::class, 'table_id', 'id');
    }

    public function ambil_chiller()
    {
        return $this->hasMany(FreestockList::class, 'chiller_id', 'id');
    }

    public function alokasi_order()
    {
        return $this->hasMany(Bahanbaku::class, 'chiller_out', 'id')->where('proses_ambil', '!=', 'frozen');
    }

    public function ambil_abf()
    {
        return $this->hasMany(Abf::class, 'table_id', 'id')->where('table_name', 'chiller');
    }

    public function inventory_adjustment()
    {
        return $this->hasMany(Chiller::class, 'table_id', 'id')->where('table_name', 'chiller')->where('type', 'inventory_adjustment');
    }

    public function konsumen()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function countEvis()
    {
        return $this->hasMany(FreestockList::class, 'chiller_id', 'id');
    }
    
    public function countNonLB(){
         return $this->hasMany(Production::class,'prod_tanggal_potong','tanggal_produksi');
    } 

    public static function countPengambilanBB($id,$type){
        return FreestockList::where('id',$id)->whereIn('freestock_id', Freestock::select('id')->whereIn('status', [1,2]))->sum($type);
    }
    public static function countAlokasiBB($id,$type){
        return Bahanbaku::where('chiller_out',$id)->whereIn('id', Bahanbaku::select('chiller_out')->whereIn('status', [1]))->sum($type);
    }
    public static function coh($regu=FALSE, $jenis=FALSE, $tanggal_awal=FALSE, $tanggal_akhir=FALSE)
    {
        return  Chiller::whereBetween('tanggal_produksi', [$tanggal_awal, $tanggal_akhir])
                ->whereIn('table_id', FreestockTemp::select('id')->where(function($query) use ($jenis){
                    if ($jenis == 'booking') {
                        $query->where('customer_id', '!=', NULL) ;
                    }
                    if ($jenis == 'free') {
                        $query->where('customer_id', NULL) ;
                    }
                }))
                ->where('table_name', 'free_stocktemp')
                ->where(function($query) use($regu) {
                    if ($regu) {
                        $query->where('regu', $regu);
                    }
                })
                ->where('created_at','>=',Applib::DefaultTanggalAudit())
                ->sum('stock_berat');
    }


    public static function regu_ambil_bb_fresh($tanggal, $regu, $kondisi){
        $data = DB::select("select chiller.asal_tujuan, free_stock.id, items.nama, free_stocklist.qty, free_stocklist.berat, free_stock.created_at
        from free_stocklist join free_stock
        on free_stock.id=free_stocklist.freestock_id
        join items on free_stocklist.item_id = items.id
        join chiller on chiller.id = free_stocklist.chiller_id
        WHERE free_stock.tanggal = '".$tanggal."'
        and free_stock.status = 3
        and free_stock.regu = '".$regu."'
        and free_stocklist.bb_kondisi = '".$kondisi."'
        and chiller.asal_tujuan = 'gradinggabungan'");

        return $data;

    }

    public static function jualsampingan_bb_fresh($tanggal){
        $data = DB::select("select *, bb_item as qty, bb_berat as berat from `order_bahan_baku` where
        `order_item_id` in (select `id` from `order_items` where
        `chiller_out` in (select `id` from `chiller` where date(`tanggal_produksi`) = '".$tanggal."'
        and `chiller`.`deleted_at` is null)) and
        `order_item_id` in (select `id` from `order_items` where
        `item_id` in (select `id` from `items` where `category_id` = '1'))
        and order_bahan_baku.created_at like '%".$tanggal."%'
        and `proses_ambil` = 'sampingan' and `order_bahan_baku`.`deleted_at` is null");

        return $data;

    }


    public static function recalculate_chiller($id){

        $chiller = Chiller::where('id', $id)->first();

        $used_qty   = 0;
        $used_berat = 0;

        if($chiller){

            if(Chiller::recalculate_chiller_stock($chiller->id)){

                foreach($chiller->ambil_chiller as $s):
                    if($s->free_stock){
                        if($s->free_stock->status=="3"){
                            $used_qty   = $used_qty+$s->qty;
                            $used_berat = $used_berat+$s->berat;
                        }
                    }
                endforeach;

                foreach($chiller->alokasi_order as $s):
                    if($s->status=="2"){
                        $used_qty   = $used_qty+$s->bb_item;
                        $used_berat = $used_berat+$s->bb_berat;
                    }
                endforeach;

                foreach($chiller->ambil_abf as $s):
                    $used_qty   = $used_qty+$s->qty_awal;
                    $used_berat = $used_berat+$s->berat_awal;
                endforeach;

                $ia_qty     = 0;
                $ia_berat   = 0;
                foreach($chiller->inventory_adjustment as $s):
                    $ia_qty   = $ia_qty+$s->qty_item;
                    $ia_berat = $ia_berat+$s->berat_item;
                endforeach;
                
                // $digunakan              = $chiller->berat_item - strval($used_berat) - strval($ia_berat); 


                $chiller->stock_item    = $chiller->qty_item - $used_qty + $ia_qty;
                $chiller->stock_berat   = $chiller->berat_item - $used_berat + $ia_berat;

                
                $chiller->save();

                return true;

            }else{
                return false;
            }

            

        }else{

            return false;

        }

    }

    public static function recalculate_chiller_stock($id){

        $chiller = Chiller::where('id', $id)->first();
        // dd($chiller);

        $used_qty   = 0;
        $used_berat = 0;

        if($chiller){

            if($chiller->asal_tujuan=="gradinggabungan"){
                if ($chiller) {
                    $chiller->qty_item      =   Grading::join('productions', 'grading.trans_id', '=', 'productions.id')
                                                ->whereDate('tanggal_potong', $chiller->tanggal_potong)
                                                ->where('item_id', $chiller->item_id)
                                                ->where('productions.grading_status', 1)
                                                ->whereNotNull('productions.no_urut')
                                                ->get()
                                                ->sum('total_item');
    
                    $chiller->berat_item    =   Grading::join('productions', 'grading.trans_id', '=', 'productions.id')
                                                ->whereDate('tanggal_potong', $chiller->tanggal_potong)
                                                ->where('item_id', $chiller->item_id)
                                                ->where('productions.grading_status', 1)
                                                ->whereNotNull('productions.no_urut')
                                                ->get()
                                                ->sum('berat_item');
                }
            }
            
            if($chiller->asal_tujuan=="evisgabungan"){
                if ($chiller) {
                    $chiller->qty_item      =   Evis::join('productions', 'evis.production_id', '=', 'productions.id')
                                                ->whereDate('tanggal_potong', $chiller->tanggal_potong)
                                                ->where('item_id', $chiller->item_id)
                                                ->where('productions.evis_status', 1)
                                                ->get()
                                                ->sum('total_item');
    
                    $chiller->berat_item    =   Evis::join('productions', 'evis.production_id', '=', 'productions.id')
                                                ->whereDate('tanggal_potong', $chiller->tanggal_potong)
                                                ->where('item_id', $chiller->item_id)
                                                ->where('productions.evis_status', 1)
                                                ->get()
                                                ->sum('berat_item');
                }
            }

            if($chiller->asal_tujuan=="retur"){
                if($chiller){
                    $chiller->qty_item      =   ReturItem::join('retur','retur.id','retur_item.retur_id')
                                                ->whereDate('tanggal_retur', $chiller->tanggal_potong)
                                                ->where('retur_id', $chiller->table_id)
                                                ->where('item_id', $chiller->item_id)
                                                ->get()
                                                ->sum('qty');
    
                    $chiller->berat_item    =   ReturItem::join('retur','retur.id','retur_item.retur_id')
                                                ->whereDate('tanggal_retur', $chiller->tanggal_potong)
                                                ->where('retur_id', $chiller->table_id)
                                                ->where('item_id', $chiller->item_id)
                                                ->get()
                                                ->sum('berat');
                }
            }

            $chiller->save();

            return true;

        }else{

            return false;

        }

    }

    public static function chiller_soh_update($tanggal){
        $data  = DB::select("
                            SELECT
                            item_id, 
                            item_name, 
                            (
                                round(SUM(IF(tanggal_produksi < '".$tanggal. "', IF(status=2,qty_item,0),0))) - 
                                round(SUM(IF(tanggal_produksi < '".$tanggal. "', IF(status=4,qty_item,0),0)))
                            ) AS qty_saldo_awal,
                            (
                                round(SUM(IF(tanggal_produksi < '".$tanggal. "', IF(STATUS=2,berat_item,0),0))) - 
                                round(SUM(IF(tanggal_produksi < '".$tanggal. "', IF(STATUS=4,berat_item,0),0)))
                            ) AS berat_saldo_awal,
                            (
                                round(SUM(IF(tanggal_produksi = '".$tanggal. "', IF(STATUS=2,qty_item,0),0)))
                            ) AS qty_inbound,
                            (
                                round(SUM(IF(tanggal_produksi = '".$tanggal. "', IF(STATUS=2,berat_item,0),0)))
                            ) AS berat_inbound,
                            (
                                round(SUM(IF(tanggal_produksi = '".$tanggal. "', IF(STATUS=4,qty_item,0),0)))
                            ) AS qty_outbound,
                            (
                                round(SUM(IF(tanggal_produksi = '".$tanggal. "', IF(STATUS=4,berat_item,0),0)))
                            ) AS berat_outbound,
                            (
                                round(SUM(IF(tanggal_produksi < '".$tanggal. "', IF(STATUS=2,qty_item,0),0))) - round(SUM(IF(tanggal_produksi < '".$tanggal. "', IF(STATUS=4,qty_item,0),0))) + ( round(SUM(IF(tanggal_produksi = '".$tanggal. "', IF(STATUS=2,qty_item,0),0))) - round(SUM(IF(tanggal_produksi = '".$tanggal. "', IF(STATUS=4,qty_item,0),0))) )
                            ) AS qty_saldo_akhir,
                            (
                                round(SUM(IF(tanggal_produksi < '".$tanggal. "', IF(STATUS=2,berat_item,0),0))) - round(SUM(IF(tanggal_produksi < '".$tanggal. "', IF(STATUS=4,berat_item,0),0))) + ( round(SUM(IF(tanggal_produksi = '".$tanggal. "', IF(STATUS=2,berat_item,0),0))) - round(SUM(IF(tanggal_produksi = '".$tanggal. "', IF(STATUS=4,berat_item,0),0))) )
                            ) AS berat_saldo_akhir
                            from chiller 
                            WHERE deleted_at IS NULL 
                            GROUP BY item_id,item_name

        ");
        return $data;
    }
    public static function saldo_awal_soh($item, $tanggal, $type)
    {
        return Chiller::where('item_id', $item)->where('jenis', 'masuk')->where('tanggal_produksi', '<', $tanggal)->sum($type) - Chiller::where('item_id', $item)->where('jenis', 'keluar')->where('tanggal_produksi', '<', $tanggal)->sum($type);
    }

    public static function stock_masuk_soh($item, $tanggal, $type)
    {
        return Chiller::where('item_id', $item)->where('jenis', 'masuk')->where('tanggal_produksi', $tanggal)->sum($type);
    }

    public static function stock_keluar_soh($item, $tanggal, $type)
    {
        return Chiller::where('item_id', $item)->where('jenis', 'keluar')->where('tanggal_produksi', $tanggal)->sum($type);
    }

    public static function getAllDataBahanBaku($mulai, $akhir,$ayam,$normal,$memar){
        return Chiller::select("item_id", "item_name", "tanggal_produksi", "chiller.jenis", "chiller.type", "asal_tujuan", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
                        ->join('items','chiller.item_id','items.id')
                        ->where('chiller.jenis', 'masuk')
                        ->whereIn('chiller.type', ['bahan-baku','hasil-produksi'])
                        ->whereBetween('tanggal_produksi', [$mulai, $akhir])
                        ->where(function($query) use ($ayam,$normal,$memar){
                            if($ayam == 'KARKAS' && $normal == "NORMAL" && $memar == ''){
                                $query->where('category_id',1);
                                $query->where('item_name','NOT LIKE','%MEMAR%');
                            }

                            if($ayam == 'KARKAS' && $normal == '' && $memar == 'MEMAR'){
                                $query->where('category_id',1);
                                $query->where('item_name','LIKE','%MEMAR%');
                            }

                            if($ayam == 'AYAM UTUH' && $normal == "NORMAL" && $memar == ''){
                                $query->where('category_id',17);
                                $query->where('item_name','NOT LIKE','%MEMAR%');
                            }

                            if($ayam == 'AYAM UTUH' && $normal == '' && $memar == 'MEMAR'){
                                $query->where('category_id',17);
                                $query->where('item_name','LIKE','%MEMAR%');
                            }
                        })
                        // ->whereNotIn('category_id',[4,10])
                        ->groupBy("item_id")
                        ->orderBy('item_id')
                        ->get() ;
    }
    public static function getDataBahanBaku($mulai, $akhir,$normal,$memar){
        return Chiller::select("item_id", "item_name", "tanggal_produksi", "jenis", "type", "asal_tujuan", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
                        ->where('jenis', 'masuk')
                        ->where('type', 'bahan-baku')
                        ->whereBetween('tanggal_produksi', [$mulai, $akhir])
                        ->where(function($query) use ($normal,$memar){
                            if($normal == "NORMAL" && $memar == ''){
                                $query->where('item_name','NOT LIKE','%MEMAR%');
                            }else
                            if($normal == '' && $memar == 'MEMAR'){
                                $query->where('item_name','LIKE','%MEMAR%');
                            }else{
                                $query->where('item_name','LIKE','%%');
                            }
                        })
                        ->whereNotIn('item_id',Item::select('id')->where('category_id',[4,10]))
                        ->groupBy("item_id")
                        ->orderBy('item_id')
                        ->get() ;
    }
    public static function getDataMasterBahanBakuKarkas(){
        $dd = array(
            '03-04'   => '03-04',
            '04-05'   => '04-05',
            '05-06'   => '05-06',
            '06-07'   => '06-07',
            '07-08'   => '07-08',
            '08-09'   => '08-09',
            '09-10'   => '09-10',
            '10-11'   => '10-11',
            '11-12'   => '11-12',
            '12-13'   => '12-13',
            '13-14'   => '13-14',
            '14-15'   => '14-15',
            '15-16'   => '15-16',
            '16-17'   => '16-17',
            '17-18'   => '17-18',
            '18-19'   => '18-19',
            '19-20'   => '19-20',
            '20-21'   => '20-21',
            '21-22'   => '21-22',
            '22-23'   => '22-23',
            '23-24'   => '23-24',
            '24-25'   => '24-25',
            '25 UP'   => '25 UP'
        );
        return $dd;
    }
    public static function getDataMasterBahanBaku($mulai, $akhir,$caridata,$type){
        return Chiller::select("item_id", "item_name", "tanggal_produksi", "jenis", "type", "asal_tujuan", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
                        ->where('jenis', 'masuk')
                        ->where(function($query) use ($caridata,$type){
                            if($caridata == "sampingan"){
                                $query->whereIn('type',$type);
                                $query->whereIn('item_id',Item::select('id')->where('category_id',[4,6,10]));
                            }
                            if($caridata == "boneless"){
                                $query->whereIn('type',$type);
                                $query->where('item_name','LIKE','%BONELESS%');
                            }
                        })
                        ->whereBetween('tanggal_produksi', [$mulai, $akhir])
                        ->groupBy("item_id")
                        ->orderBy('item_id')
                        ->get() ;
    }

    public static function getDataMasterBahanBakuFF($mulai, $akhir){
        return Chiller::select("item_id", "item_name", "tanggal_produksi", "jenis", "type", "asal_tujuan","customer_id", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
                        ->whereBetween('tanggal_produksi', [$mulai, $akhir])
                        ->where('jenis', 'masuk')
                        ->where('type','hasil-produksi')
                        ->whereNotNull('customer_id')
                        ->where(function($query){
                            $query->where('stock_berat','>',0);
                            $query->OrWhere('stock_item','>', 0);
                        })
                        ->groupBy('item_id','customer_id')
                        ->orderBy('item_id')
                        ->get() ;
    }
    public static function getDataMasterBahanBakuRetur($mulai, $akhir){
        return Chiller::select("item_id", "item_name", "tanggal_produksi", "jenis", "type", "asal_tujuan","customer_id","label", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
                        ->whereBetween('tanggal_produksi', [$mulai, $akhir])
                        ->where('asal_tujuan', 'retur')
                        ->where('jenis', 'masuk')
                        ->whereIn('type',['hasil-produksi','bahan-baku'])
                        ->whereNotNull('label')
                        ->where(function($query){
                            $query->where('stock_berat','>',0);
                            $query->OrWhere('stock_item','>', 0);
                        })
                        ->groupBy('item_id','label')
                        ->orderBy('item_id')
                        ->get() ;
    }
    public static function getAllDataBahanOther($mulai, $akhir){
        return Chiller::select("item_id", "item_name", "tanggal_produksi", "chiller.jenis", "chiller.type", "asal_tujuan", DB::raw("round(sum(qty_item), 2) AS qty"), DB::raw("round(sum(berat_item), 2) AS berat"), DB::raw("round(sum(stock_item), 2) AS stock_qty"), DB::raw("round(sum(stock_berat), 2) AS stock_berat"))
                        ->join('items','chiller.item_id','items.id')
                        ->whereBetween('tanggal_produksi', [$mulai, $akhir])
                        ->where('chiller.jenis', 'masuk')
                        ->where(function($query){
                            $query->where('asal_tujuan','!=','evisgabungan');
                            $query->where('asal_tujuan','!=','gradinggabungan');
                            $query->where('asal_tujuan','!=','retur');
                            $query->where('item_name','NOT LIKE','%BONELESS%');
                        })
                        ->where(function($query2){
                            $query2->where('stock_berat','>',0);
                            $query2->OrWhere('stock_item','>', 0);
                        })
                        ->whereIn('chiller.type', ['bahan-baku','hasil-produksi'])
                        ->whereNull('customer_id')
                        ->whereIn('category_id',[1,2,3,5])
                        ->groupBy("item_id")
                        ->orderBy('item_id')
                        ->get() ;
    }

    public static function ambilsisachiller($chiller_id, $name, $name2,$name3,$bbid=FALSE){
        $dataChiller            = Chiller::where('id', $chiller_id)->sum($name);
        $dataFreestockList      = FreestockList::select(DB::raw("round(ifnull(sum($name2),0),2) as sum"))
                                                ->where( function($q) use ($chiller_id,$bbid){
                                                    $q->where('chiller_id', $chiller_id);
                                                    if($bbid){
                                                        $q->where('id','!=',$bbid);
                                                    }
                                                })
                                                // ->get()
                                                // ->sum($name2);
                                                ->first()
                                                ->sum;

        $dataAlokasiSiapKirim   = Bahanbaku::select(DB::raw("round(ifnull(sum($name3),0),2) as sum"))
                                            ->where( function($q) use ($chiller_id,$bbid){
                                            $q->where('chiller_out', $chiller_id);
                                            $q->whereIn('status',[1,2]);
                                            $q->where('proses_ambil','!=','frozen');
                                            if($bbid){
                                                $q->where('id','!=',$bbid);
                                            }
                                        })
                                        // ->get()
                                        // ->sum($name3);
                                        ->first()
                                        ->sum;
        
        $dataAlokasiAbf         = Abf::select(DB::raw("round(ifnull(sum($name),0),2) as sum"))
                                        ->where(function($q) use ($chiller_id){
                                            $q->where('table_id',$chiller_id);
                                            $q->where('table_name','chiller');
                                            $q->where('jenis','masuk');
                                        })
                                        // ->get()
                                        // ->sum($name);
                                        ->first()
                                        ->sum;
        
        $dataAlokasiMusnahkan   = Musnahkantemp::select(DB::raw("round(ifnull(sum($name2),0),2) as sum"))
                                                ->whereIn('gudang_id',['2','4','23','24'])
                                                ->where('item_id', $chiller_id)
                                                ->whereIn('musnahkan_id', Musnahkan::select('id')->whereNull('deleted_at'))
                                                // ->get()
                                                // ->sum($name2);
                                                ->first()
                                                ->sum;
        
        $totalReal              = $dataChiller - $dataFreestockList - $dataAlokasiSiapKirim - $dataAlokasiAbf - $dataAlokasiMusnahkan;
        return $totalReal;
    }

    public static function getIdPurc($id){
        $query                     = Purchasing::where('id',$id)->where('jenis_po','PO Karkas')->get();
        // dd($query->count());
        if ($query->count() > 0) {
            foreach ($query as $h) {
                $hasil = $h->id;
            }
        } else {
            $hasil = '';
        }
        return $hasil;
    }
    public static function getProdId($id){
        $query                     = Production::where('purchasing_id',$id)->get();
        // dd($query->count());
        if ($query->count() > 0) {
            foreach ($query as $h) {
                $hasil = $h->id;
            }
        } else {
            $hasil = '';
        }
        return $hasil;
    }


    public static function renameData($tujuan)
    {
        if ($tujuan == 'evisampingan') {
            return 'Sampingan (Evis)';
        }
        if ($tujuan == 'evisgabungan') {
            return 'Sampingan (Evis)';
        }
        if ($tujuan == 'evisstock') {
            return 'Stock (Evis)';
        }
        if ($tujuan == 'evismusnahkan') {
            return 'Musnahkan (Evis)';
        }
        if ($tujuan == 'eviskiriman') {
            return 'Kiriman (Evis)';
        }
        if ($tujuan == 'eviskaryawan') {
            return 'Karyawan (Evis)';
        }
        if ($tujuan == 'baru') {
            return 'Baru (Grading)';
        }
        if ($tujuan == 'gradinggabungan') {
            return 'Baru (Grading)';
        }
        if ($tujuan == 'karkasbeli') {
            return 'Grading Non LB';
        }
        if ($tujuan == 'evisbeli') {
            return 'Evis Non LB';
        }
        if ($tujuan == 'hasilbeli') {
            return 'Hasil Produksi Non LB';
        }
        if ($tujuan == 'baru') {
            return 'Baru (Purchase)';
        }
        if ($tujuan == 'retur') {
            return 'Retur (Ekspedisi)';
        }
        if ($tujuan == 'karyawan') {
            return 'Penjualan Karyawan';
        }
        if ($tujuan == 'free_stock') {
            return 'Free Stock';
        }
        if ($tujuan == 'belum_terpakai') {
            return 'Belum Terpakai';
        }
        if ($tujuan == 'kepala_produksi') {
            return 'Kepala Produksi';
        }
        if ($tujuan == 'kepala_regu') {
            return 'Kepala Regu';
        }
        if ($tujuan == 'boneless') {
            return 'Kepala Regu Boneless';
        }
        if ($tujuan == 'krparting') {
            return 'Kepala Regu Parting';
        }
        if ($tujuan == 'krpartingmarinasi') {
            return 'Kepala Regu Parting Marinasi';
        }
        if ($tujuan == 'krwhole') {
            return 'Kepala Regu Whole Chicken';
        }
        if ($tujuan == 'krfrozen') {
            return 'Kepala Regu Frozen';
        }
        if ($tujuan == 'jualsampingan') {
            return 'Penjualan Sampingan';
        }
        if ($tujuan == 'orderproduksi') {
            return 'Order';
        }
        if ($tujuan == 'thawing') {
            return 'Thawing';
        }
        if ($tujuan == 'open_balance') {
            return 'Open Balance';
        }
        if ($tujuan == 'tukar_item') {
            return 'Tukar Item';
        }
    }

    public static function inject_recalculate_chiller($id){

        $chiller = Chiller::where('id', $id)->first();

        $used_qty   = 0;
        $used_berat = 0;

        if($chiller){

            if(Chiller::recalculate_chiller_stock($chiller->id)){

                foreach($chiller->ambil_chiller as $s):
                    if($s->free_stock){
                        if($s->free_stock->status=="3"){
                            $used_qty   = $used_qty+$s->qty;
                            $used_berat = $used_berat+$s->berat;
                        }
                    }
                endforeach;

                foreach($chiller->alokasi_order as $s):
                    if($s->status=="2"){
                        $used_qty   = $used_qty+$s->bb_item;
                        $used_berat = $used_berat+$s->bb_berat;
                    }
                endforeach;

                foreach($chiller->ambil_abf as $s):
                    $used_qty   = $used_qty+$s->qty_awal;
                    $used_berat = $used_berat+$s->berat_awal;
                endforeach;

                $ia_qty     = 0;
                $ia_berat   = 0;
                foreach($chiller->inventory_adjustment as $s):
                    $ia_qty   = $ia_qty+$s->qty_item;
                    $ia_berat = $ia_berat+$s->berat_item;
                endforeach;
                
                $cekDataBeratItem       = floatval($chiller->berat_item);
                $cekDataBeratPenggunaan = $used_berat + $ia_berat;
                $totalberatakhir        = $cekDataBeratItem - $cekDataBeratPenggunaan;
                
                $chiller->stock_item    = $chiller->qty_item - $used_qty + $ia_qty;
                $chiller->stock_berat   = number_format((float)$totalberatakhir, 2, '.', '');
                
                $chiller->save();

                return true;

            }else{
                return false;
            }

            

        }else{

            return false;

        }

    }

    public static function injectRecalculateData($id){
        return self::inject_recalculate_chiller($id);
    }
}

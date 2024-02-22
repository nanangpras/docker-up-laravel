<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Chiller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class FreestockList extends Model
{
    use SoftDeletes;
    protected $table = 'free_stocklist';

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function free_stock()
    {
        return $this->hasOne(Freestock::class, 'id', 'freestock_id');
    }

    public function chiller()
    {
        return $this->belongsTo(Chiller::class, 'chiller_id', 'id');
    }

    public function freechiller()
    {
        return $this->belongsTo(Chiller::class, 'id', 'table_id')->where('table_name', 'free_stocklist');
    }


    public static function hitung_diambil($id, $type, $semua=TRUE)
    {
        return  FreestockList::where('chiller_id', $id)
                ->where(function($query) use($semua) {
                    if ($semua) {
                        $query->where('bb_kondisi', 'baru');
                    }
                })
                ->whereIn('freestock_id', Freestock::select('id')->whereIn('status', [1,2])->pluck('id'))
                ->sum($type);
    }

    public static function getCountEvis($id, $type){
        return FreestockList::where('freestock_id',$id)
                            ->whereIn('freestock_id', Freestock::select('id')->where('status','1'))
                            ->sum($type);
    }
    public static function hitung_produksi($asal, $type, $tanggal, $regu=FALSE, $lama=FALSE, $sp=FALSE)
    {
                    if($asal=="thawing"){
                        $bb_kondisi = "thawing";
                    }elseif($asal=="retur"){
                        $bb_kondisi = "retur";
                    }elseif($asal == ['hasilbeli','evisbeli','karkasbeli']){
                            $bb_kondisi = ['hasilbeli','evisbeli','karkasbeli'];
                    }else{
                        $bb_kondisi = ($lama ? 'lama' : 'baru');
                    }

                    $data   =   FreestockList::where(function($query) use ($regu,$asal){
                        if ($regu) {
                            $query->where('regu', $regu) ;
                        } else {
                            $query->whereIn('regu', $asal == 'thawing' ? ['whole', 'marinasi', 'parting', 'boneless'] : ['whole', 'marinasi', 'parting', 'boneless', 'frozen']) ;
                        }
                    })
                    ->where(function($query) use ($asal, $bb_kondisi){
                        if($asal ==['hasilbeli','evisbeli','karkasbeli']){
                            $query->whereIn('bb_kondisi', $bb_kondisi);
                        }else{
                            $query->where('bb_kondisi', $bb_kondisi);
                        }
                    })
                    ->whereIn('freestock_id', Freestock::select('id')->where('status', 3)->whereDate('tanggal', $tanggal))
                    ->where(function($query) use ($asal){
                        if(is_array($asal)){
                            $query->whereIn('chiller_id', Chiller::select('id')->where('status', 2)->whereIn('asal_tujuan', $asal));
                        }else{
                            $query->whereIn('chiller_id', Chiller::select('id')->where('status', 2)->where('asal_tujuan', $asal));
                        }
                    })
                    ->sum($type);

        if ($sp) {
            return $data + $sp ;
        } else {
            return $data ;
        }
    }

    public static function hitung_produksi_update($mulai,$akhir,$params){

        if($params == 'beli'){
            $kondisi    = ['karkasbeli','hasilbeli'];
            $asal       = ['karkasbeli','hasilbeli'];
        }elseif($params == 'retur'){
            $kondisi    = 'retur';
            $asal       = 'retur';
        }elseif($params == 'thawing'){
            $kondisi    = 'thawing';
            $asal       = 'thawing';
        }elseif($params == 'lama'){
            $kondisi    = 'lama';
            $asal       = 'gradinggabungan';
        }elseif($params == 'baru'){
            $kondisi    = 'baru';
            $asal       = 'gradinggabungan';
        }
        $data 	= FreestockList::select('free_stock.tanggal','free_stocklist.regu',DB::raw('sum(qty) as qty'),DB::raw('sum(berat) as berat'))
                                ->LeftJoin('items','free_stocklist.item_id','items.id')
                                ->LeftJoin('free_stock', 'free_stock.id', 'free_stocklist.freestock_id')
                                ->where(function($q) use($params,$kondisi){
                                    if($params != 'beli'){
                                        $q->where('free_stocklist.bb_kondisi',$kondisi);
                                    }else{
                                        $q->whereIn('free_stocklist.bb_kondisi',$kondisi);
                                    }
                                })
                                ->whereIn('freestock_id', Freestock::select('id')->where('status', 3)->whereBetween('tanggal', [$mulai, $akhir]))
                                ->where(function($query) use($params, $asal){
                                    if($params != 'beli'){
                                        $query->whereIn('chiller_id', Chiller::select('id')->where('status', 2)->where('asal_tujuan', $asal));
                                        // $query->where('chiller.asal_tujuan',$asal);
                                    }else{
                                        $query->whereIn('chiller_id', Chiller::select('id')->where('status', 2)->whereIn('asal_tujuan', $asal));
                                        // $query->whereIn('chiller.asal_tujuan',$asal);
                                    }
                                })
                                ->groupBy('tanggal', 'free_stocklist.regu')
                                ->get();

        return $data ;
    }

    public static function hitung_jual_sampingan($tanggal, $type){

        if($type=="berat"){
            $sampingan = DB::Select("SELECT sum(berat_item) as berat from chiller
            join items on chiller.item_id = items.id
            join order_bahan_baku on order_bahan_baku.id = chiller.table_id
            where table_name = 'order_bahanbaku'
            and order_bahan_baku.proses_ambil = 'sampingan'
            and date(tanggal_produksi) ='".$tanggal."'
            and items.category_id = '1'
            and chiller.deleted_at is null");

            return $sampingan[0]->berat ?? 0;
        }
        if($type=="qty"){
            $sampingan = DB::Select("SELECT sum(qty_item) as qty from chiller
            join items on chiller.item_id = items.id
            join order_bahan_baku on order_bahan_baku.id = chiller.table_id
            where table_name = 'order_bahanbaku'
            and order_bahan_baku.proses_ambil = 'sampingan'
            and date(tanggal_produksi) ='".$tanggal."'
            and items.category_id = '1'
            and chiller.deleted_at is null");

            return $sampingan[0]->qty ?? 0;
        }
    }

    public static function hitung_bb_ayam_lama($tanggal_produksi, $tanggal_bb){

        $ayam_lama = DB::select("select sum(qty) as lama_qty, sum(berat) as lama_berat from free_stocklist join free_stock
        on free_stock.id=free_stocklist.freestock_id
        join items on free_stocklist.item_id = items.id
        join chiller on chiller.id = free_stocklist.chiller_id
        WHERE free_stock.tanggal = '".$tanggal_produksi."'
        AND free_stocklist.`deleted_at` IS NULL
        and free_stock.status = 3
        and free_stocklist.bb_kondisi = 'lama'
        and chiller.asal_tujuan = 'gradinggabungan'
        and chiller.tanggal_produksi = '".$tanggal_bb."'
        GROUP BY chiller.tanggal_produksi");

        return $ayam_lama ?? [];

    }
    public static function hitung_bb_ayam_lama_lebih2hari($tanggal_produksi, $tanggal_bb){
        if($tanggal_bb == 'lebihlama'){
            $newtgl     = date('Y-m-d',strtotime('-2 days', strtotime($tanggal_produksi)));
            $ayam_lama  = DB::select("select sum(qty) as lama_qty, sum(berat) as lama_berat from free_stocklist join free_stock
                on free_stock.id=free_stocklist.freestock_id
                join items on free_stocklist.item_id = items.id
                join chiller on chiller.id = free_stocklist.chiller_id
                WHERE free_stock.tanggal = '".$tanggal_produksi."'
                AND free_stocklist.`deleted_at` IS NULL
                and free_stock.status = 3
                and free_stocklist.bb_kondisi = 'lama'
                and chiller.asal_tujuan = 'gradinggabungan'
                and chiller.tanggal_produksi < '".$newtgl."'
                GROUP BY free_stocklist.regu");

            return $ayam_lama ?? [];
        }
    }

    public static function get_all_bahanbaku_beli($mulai,$akhir){
        $bahanbakubeli  = DB::select("select tanggal_produksi, SUM(qty_item) AS qty_item, SUM(berat_item) AS berat_item FROM chiller WHERE asal_tujuan IN ('hasilbeli','karkasbeli') AND tanggal_produksi BETWEEN '".$mulai."' AND '".$akhir."' GROUP BY tanggal_produksi");

        return $bahanbakubeli ?? [];
    }

    public static function get_all_bahanbaku_thawing($mulai,$akhir){
        $bahanbakuthawing  = DB::select("select tanggal_produksi, SUM(qty_item) AS qty_item, SUM(berat_item) AS berat_item FROM chiller WHERE asal_tujuan='thawing' AND tanggal_produksi BETWEEN '".$mulai."' AND '".$akhir."' GROUP BY tanggal_produksi");

        return $bahanbakuthawing ?? [];
    }

    public static function get_all_bahanbaku_sampingan($mulai,$akhir){
        $bahanbakusampingan = DB::select("select * FROM chiller
                                JOIN items ON chiller.item_id = items.id
                                JOIN order_bahan_baku ON order_bahan_baku.id = chiller.table_id
                                WHERE table_name = 'order_bahanbaku'
                                AND order_bahan_baku.proses_ambil = 'sampingan'
                                AND DATE(tanggal_produksi) BETWEEN '".$mulai."' AND '".$akhir."'
                                AND items.category_id = '1'
                                AND chiller.deleted_at IS NULL");

        return $bahanbakusampingan ?? [];
    }

    public static function get_all_sisachiller($mulai,$akhir){
        $bahanbakusisachiller = DB::select("select `c`.`tanggal_produksi`,sum(bb_item) as total_bb_item ,sum(bb_berat) as total_bb_berat  FROM `order_bahan_baku` AS `a`
                                LEFT JOIN `order_items` as `b` ON `a`.`order_item_id`=`b`.`id`
                                LEFT JOIN `chiller` as `c` ON `a`.`chiller_out` = `c`.`id`
                                LEFT JOIN `items` as `d` ON `b`.`item_id` = `d`.`id`
                                WHERE DATE ( `tanggal_produksi` ) BETWEEN '".$mulai."' AND '".$akhir."'
                                AND `d`.`category_id` = '1'
                                AND `a`.`proses_ambil` = 'sampingan'
                                AND `a`.`deleted_at` IS NULL
                                AND `b`.`deleted_at` IS NULL
                                AND `c`.`deleted_at` IS NULL
                                AND `d`.`deleted_at` IS NULL
                                GROUP BY `c`.`tanggal_produksi`");

        return $bahanbakusisachiller ?? [];
    }
    public static function cek_bb_non_wo($regu,$tanggal_awal,$tanggal_akhir)
    {
        $cek_bb = FreestockList::select('free_stocklist.item_id', DB::raw("SUM(berat) AS berat") , DB::raw("SUM(free_stocklist.qty) AS qty"),'chiller.type', 'items.sku')
                                ->leftJoin('items', 'items.id', '=', 'free_stocklist.item_id')
                                ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                ->where('free_stock.regu', $regu)
                                ->where('free_stock.status', '3')
                                // ->whereNull('free_stock.orderitem_id')
                                ->whereNull('free_stock.netsuite_send')
                                ->whereBetween('free_stock.tanggal', [$tanggal_awal,$tanggal_akhir])
                                ->groupBy('item_id')
                                ->where('chiller.type','hasil-produksi')
                                ->get();
        return $cek_bb;
    }

    public static function CheckTanggalProduksi($id){
        $query= Chiller::where('id',$id)->get();
        if ($query->count() > 0) {
            foreach ($query as $h) {
                $hasil  = $h->tanggal_produksi;
            }
        } else {
            $hasil      = '';
        }
        return $hasil;
    }
}

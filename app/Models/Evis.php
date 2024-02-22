<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Production;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

class Evis extends Model
{
    //
    protected $table = 'evis';
    protected $appends  =   ['jenis_evis', 'jenis_peruntukan'];

    public function eviitem()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function eviprod()
    {
        return $this->belongsTo(Production::class, 'production_id', 'id');
    }

    public function getJenisEvisAttribute()
    {
        if ($this->jenis == 'mobil') {
            return 'Per Mobil';
        }
        if ($this->jenis == 'gabungan') {
            return 'Gabungan';
        }
    }

    public static function sebaran_evis($id, $tanggal)
    {
        return Evis::whereIn('production_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('tanggal_potong', $tanggal)))->where('item_id', $id)->sum('berat_item');
    }

    public function getJenisPeruntukanAttribute()
    {
        if ($this->peruntukan == 'jualsampingan') {
            return 'Jual Sampingan';
        } elseif ($this->peruntukan == 'stock') {
            return 'Stock';
        } elseif ($this->peruntukan == 'kiriman') {
            return 'Kiriman';
        } elseif ($this->peruntukan == 'karyawan') {
            return 'Karyawan';
        } elseif ($this->peruntukan == 'musnahkan') {
            return 'Musnahkan';
        }
    }

    public static function recalculate($id)
    {
        // return Evis::where('id', $id)->first();
        $evis   =   Evis::where('id', $id)->first() ;
        $data   =   Chiller::where('asal_tujuan', 'evisgabungan')
                    ->where('item_id', $evis->item_id)
                    ->whereDate('tanggal_produksi', $evis->tanggal_potong)
                    ->where('status', 2)
                    ->first();
        // return $data;
        if ($data) {
            $data->qty_item     =   Evis::whereDate('tanggal_potong', $evis->tanggal_potong)
                                    ->where('item_id', $evis->item_id)
                                    ->where('id', '!=', $evis->id)
                                    ->get()
                                    ->sum('total_item');

            $data->berat_item   =   Evis::whereDate('tanggal_potong', $evis->tanggal_potong)
                                    ->where('item_id', $evis->item_id)
                                    ->where('id', '!=', $evis->id)
                                    ->get()
                                    ->sum('berat_item');

            $data->stock_item   =   $data->stock_item - $evis->total_item ;
            $data->stock_berat  =   $data->stock_berat - $evis->berat_item ;

            $data->save() ;
        }
    }

    public static function laporan_evis($id, $tanggal_awal, $type='total_item')
    {
        return  Evis::whereDate('tanggal_potong', $tanggal_awal)
                ->where('item_id', $id)
                ->sum($type);
    }

    public static function hitung_total($produksi, $type)
    {
        return Evis::where('production_id', $produksi)->sum($type) ;
    }

    public static function produksi($tanggal, $sku)
    {
        return  Chiller::whereIn('item_id', Item::select('id')->where('sku', $sku))
                ->whereDate('tanggal_produksi', $tanggal)
                ->sum('berat_item');
    }

    public static function hitung_kotor($tanggal)
    {
        return Evis::whereDate('tanggal_potong', $tanggal)->sum('berat_item') ;
    }
    
    public static function getArrayProduksis($mulai,$selesai,$variable,$item){
        $data =  DB::table('chiller')
                        ->whereBetween('tanggal_produksi',[$mulai,$selesai])
                        ->where(function($query) use ($variable,$item){
                            if($variable != ''){
                                $query->where('item_name','LIKE','%'.$variable.'%');
                            }
                            if($item != ''){
                                $query->whereIn('item_id',$item);
                            }
                        })
                        // ->where('type','bahan-baku')
                        // ->where('asal_tujuan','evisgabungan')
                        // ->where('table_name','free_stocklist')
                        ->select('id','table_name','tanggal_produksi',DB::raw("SUM(berat_item) as `berat_item`"),DB::raw("SUM(stock_berat) as stock_chiller"))
                        ->groupBy('tanggal_produksi')
                        ->get();

                $array = array();
                foreach($data as $db){
                    $array[] = [
                        'id'                 => $db->id,
                        'tanggal_produksi'   => $db->tanggal_produksi,
                        'berat_item'         => $db->berat_item,
                        'stock_chiller'      => $db->stock_chiller,
                        'kondisi'            => self::getBBkondisi($db->id,$db->tanggal_produksi),
                    ];
                }
                return $array;
        // return DB::table('free_stock as a')
        //                 ->join('free_stocklist as b','a.id','b.freestock_id')
        //                 ->whereBetween('a.tanggal',[$mulai,$selesai])
        //                 ->where(function($query) use ($condition,$item){
        //                     if($condition == ''){
        //                         $query->whereIn('b.bb_kondisi',['free_stock']);
        //                         $query->whereIn('b.item_id',$item);
        //                     }else{
        //                         $query->whereIn('b.bb_kondisi',$condition);
        //                         $query->whereIn('b.item_id',$item);
        //                     }
        //                 })
        //                 ->select('a.tanggal',DB::raw("SUM(b.berat) as `$alias`"))
        //                 ->groupBy('a.tanggal')
        //                 ->get();
    }
    public static function getArrayProduksi($mulai,$selesai,$variable,$item,$kondisi){
        $data =  DB::table('chiller')
                        ->whereBetween('tanggal_produksi',[$mulai,$selesai])
                        ->where(function($query) use ($variable,$item){
                            if($variable != ''){
                                $query->where('item_name','LIKE','%'.$variable.'%');
                            }
                            if($item != ''){
                                $query->whereIn('item_id',$item);
                            }
                        })
                        ->where('jenis','masuk')
                        ->whereIn('type',['bahan-baku','hasil-produksi'])
                        ->whereIn('asal_tujuan',['free_stock','evisgabungan'])
                        // ->where(function($q) use ($kondisi){
                        //     if($kondisi == 'baru'){
                        //         $q->where('asal_tujuan','evisgabungan');
                        //         $q->where('type','bahan-baku');
                        //     }else if($kondisi == 'lama'){
                        //         $q->where('asal_tujuan','free_stock');
                        //         $q->where('type','hasil-produksi');
                        //     }else{
                        //         $q->whereIn('asal_tujuan',['free_stock','evisgabungan']);
                        //     }
                        // })
                        ->select('id','table_name','tanggal_produksi',DB::raw("SUM(berat_item) as `berat_item`"),DB::raw("SUM(stock_berat) as stock_chiller"))
                        ->groupBy('tanggal_produksi')
                        ->get();

                $array = array();
                foreach($data as $db){
                    if($db->table_name == null || $db->table_name == ''){
                        $kondisi = 'baru';
                    }else{
                        $kondisi = self::getBBkondisi($db->id,$db->tanggal_produksi);
                    }
                    $array[] = [
                        'id'                 => $db->id,
                        'tanggal_produksi'   => $db->tanggal_produksi,
                        'berat_item'         => $db->berat_item,
                        'stock_chiller'      => $db->stock_chiller,
                        'kondisi'            => $kondisi,
                    ];
                }
                return $array;
                // dd($array);
    }
    public static function getBBkondisi($id,$tanggal){
        $query                     = DB::table('free_stocklist as a')->join('free_stock as b','a.freestock_id','b.id')->where('a.chiller_id', $id)->where('b.tanggal','=',$tanggal)->get();
        // dd($query->count());
        if ($query->count() > 0) {
            foreach ($query as $h) {
                $hasil = $h->bb_kondisi;
            }
        } else {
            $hasil = 'lama';
        }
        return $hasil;
    }
    public static function getStockFrozen($mulai,$selesai,$item,$item_id){
        // return DB::table('product_gudang')
        //             ->whereBetween('product_gudang.production_date',[$mulai,$selesai])
        //             ->leftJoin('abf','abf.id','=','product_gudang.table_id')
        //             ->where(function($q) use ($item,$item_id){
        //                 if($item != ''){
        //                     $q->where('product_gudang.nama','like','%'.$item.'%');
        //                 }
        //                 if($item_id != ''){
        //                     $q->whereIn('product_gudang.product_id',$item_id);
        //                 }
        //             })
        //             ->where('product_gudang.status',2)
        //             ->groupBy('product_gudang.production_date')
        //             ->select('product_gudang.production_date', DB::raw('SUM(abf.berat_awal) as berat_frozen'))
        //             ->get();
        return DB::table('orders')
                    ->join('order_bahan_baku','order_bahan_baku.order_id','=','orders.id')
                    ->whereBetween('orders.tanggal_so',[$mulai,$selesai])
                    ->where(function ($q) use ($item,$item_id){
                        if($item != ''){
                            $q->where('order_bahan_baku.nama','like','%'.$item.'%');
                        }
                    })
                    ->where('order_bahan_baku.deleted_at',NULL)
                    ->groupBy('orders.tanggal_so')
                    ->select('orders.tanggal_so',DB::raw('SUM(order_bahan_baku.bb_berat) as berat_frozen'))
                    ->get();
    }
    // public static function UseStockFrozen($mulai,$selesai,$item,$item_id){
    //     return DB::table('orders')
    //                 ->join('order_bahan_baku','order_bahan_baku.order_id','=','orders.id')
    //                 ->whereBetween('orders.tanggal_so',[$mulai,$selesai])
    //                 ->where(function ($q) use ($item,$item_id){
    //                     if($item != ''){
    //                         $q->where('order_bahan_baku.nama','like','%'.$item.'%');
    //                     }
    //                 })
    //                 ->where('order_bahan_baku.deleted_at',NULL)
    //                 ->groupBy('orders.tanggal_so')
    //                 ->select('orders.tanggal_so',DB::raw('SUM(order_bahan_baku.bb_berat) as berat_frozen'))
    //                 ->get();
    // }

    public static function getOrderBahanBaku($mulai,$selesai,$item,$item_id)
    {
        return DB::table('chiller')
                    ->whereBetween('tanggal_produksi',[$mulai,$selesai])
                    ->where('table_name','order_bahanbaku')
                    ->where(function($q) use ($item,$item_id){
                        if($item != ''){
                            $q->where('item_name','like','%'.$item.'%');
                        }
                        if($item_id != ''){
                            $q->whereIn('item_id',$item_id);
                        }
                    })
                    ->groupBy('tanggal_produksi')
                    ->sum('berat_item');
    }

    // FUNCTION NYARI DATA PERSENTASE HARIAN

    public static function getArrayPenjualanItem($mulai,$selesai,$variable,$item,$alias){
        return DB::table('orders as a')
                    ->join('order_items as b','b.order_id','a.id')
                    ->whereBetween('a.tanggal_kirim',[$mulai,$selesai])
                    ->where(function($query) use ($variable,$item){
                        if($variable != ''){
                            $query->where('b.nama_detail','LIKE','%'.$variable.'%');
                        }
                        if($item != ''){
                            $query->whereIn('b.item_id',$item);
                        }
                    })
                    ->where('a.status','10')
                    // ->whereIn('a.sales_channel',['By Product - Paket','By Product - Retail'])
                    ->select('a.tanggal_kirim',DB::raw("SUM(b.fulfillment_berat) as `$alias`"))
                    ->groupBy('a.tanggal_kirim')
                    ->get();
    }

    // FUNCTION NYARI DATA TOTAL PERSENTASE

    public static function EcerPaket($mulai,$selesai,$item,$item_id)
    {
        return DB::table('order_items as a')
                    ->LeftJoin('orders as b','a.order_id','b.id')
                    ->whereBetween('b.tanggal_kirim',[$mulai,$selesai])
                    ->where(function($q) use ($item,$item_id){
                        if($item != ''){
                            $q->where('a.nama_detail','LIKE','%'.$item.'%');
                        }
                        if($item_id != ''){
                            $q->whereIn('a.item_id',$item_id);
                        }
                    })
                    // ->whereIn('b.sales_channel',['By Product - Paket','By Product - Retail'])
                    ->where('b.status',10)
                    ->groupBy('a.nama_detail')
                    ->sum('a.fulfillment_berat');
    }
    public static function StockChiller($mulai,$selesai,$variable,$item){
        return DB::table('chiller')
                    ->whereBetween('tanggal_produksi',[$mulai,$selesai])
                    ->where(function($query) use ($variable,$item){
                        if($variable != ''){
                            $query->where('b.item_name','LIKE','%'.$variable.'%');
                        }
                        if($item != ''){
                            $query->whereIn('item_id',$item);
                        }
                    })
                    ->where('type','pengambilan-bahan-baku')
                    ->sum('stock_berat');
    }
    public static function StockWarehouse($mulai,$selesai,$variable,$item){
        return DB::table('order_bahan_baku as a')
                    ->join('product_gudang as b', 'a.chiller_out','b.id')
                    ->whereBetween(DB::raw('DATE(a.created_at)'),[$mulai,$selesai])
                    ->where(function($query) use ($variable,$item){
                        if($variable != ''){
                            $query->where('a.nama','LIKE','%'.$variable.'%');
                        }
                        if($item != ''){
                            $query->whereIn('item_id',$item);
                        }
                    })
                    ->get();
    }
}

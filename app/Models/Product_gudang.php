<?php

namespace App\Models;

use App\Classes\Applib;
use Illuminate\Database\Eloquent\Model;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Product_gudang_item;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product_gudang extends Model
{
    use SoftDeletes ;
    protected $table = 'product_gudang';
    protected $appends  =   ['status_gudang','status_keluar'];

    public function konsumen()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function productgudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id', 'id');
    }

    public function productitems()
    {
        return $this->belongsTo(Item::class, 'product_id', 'id');
    }

    public function gudangorderitem()
    {
        return $this->belongsTo(OrderItem::class, 'table_id', 'id');
    }

    public function productpgi()
    {
        return $this->hasMany(Product_gudang_item::class, 'id', 'product_gudang_id');
    }

    public function productthawing()
    {
        return $this->hasMany(Product_gudang::class, 'gudang_id_keluar', 'id') ;
    }

    public function gudang_keluar()
    {
        return $this->hasMany(Product_gudang::class, 'gudang_id_keluar', 'id') ;
    }

    public function gudangabf()
    {
        return $this->belongsTo(Abf::class, 'table_id', 'id');
    }
    public function gudangabf2()
    {
        return $this->belongsTo(Abf::class, 'table_id', 'id');
    }

    public function gudangprod()
    {
        return $this->belongsTo(Production::class, 'product_id', 'id');
    }
    public function countOrderGudang()
    {
        return $this->hasMany(Bahanbaku::class, 'chiller_out');
    } 
    public function countOrderBB(){
        return $this->hasMany(Bahanbaku::class,'chiller_out','id')->whereIn('status',[1,2])->where('proses_ambil','frozen');
    }
    public function countOrderThawing(){
        return $this->hasMany(Thawinglist::class,'item_id','id');
    }
    public function countRegrading(){
        return $this->hasMany(Product_gudang::class,'gudang_id_keluar','id')->where('status',4);
    }
    public function countInventoryAdjustment(){
        return $this->hasMany(Product_gudang::class,'gudang_id_keluar','id')->where('type','inventory_adjustment');
    }
    public function countMusnahkan(){
        return $this->hasMany(Musnahkantemp::class,'item_id','id');
    }
    public function getStatusGudangAttribute()
    {
        if ($this->status == 1) {
            return "<span class='status status-danger'>Konfirmasi</span>";
        }
        if ($this->status == 2) {
            return "<span class='status status-info'>Masuk</span>";
        }
    }

    public function getStatusKeluarAttribute()
    {
        if ($this->status == 2) {
            return "<span class='status status-info'>Masuk</span>";
        }
        elseif ($this->status == 3) {
            return "<span class='status status-danger'>Request Thawing</span>";
        }
        elseif ($this->status == 4) {
            return "<span class='status status-info'>Keluar</span>";
        }
    }

    public function getItemTypeAttribute()
    {
        if ($this->gudangabf2) {
            if ($this->gudangabf2->asal_tujuan == 'retur') {
                return "<span class='status status-info'>Retur</span>";
            } else {
                if ($this->type == 'bahan-baku') {
                    return "<span class='status status-info'>Bahan Baku</span>";
                }
                else if ($this->type == 'freestock') {
                    return "<span class='status status-info'>Freestock</span>";
                }
                else if ($this->type == 'hasil-produksi') {
                    return "<span class='status status-info'>Hasil Produksi</span>";
                }
                else if ($this->type == 'inventory_adjustment') {
                    return "<span class='status status-info'>Inventory Adjustment</span>";
                }
                else if ($this->type == 'openbalance') {
                    return "<span class='status status-info'>Open Balance</span>";
                }
                else if ($this->type == 'siapkirim') {
                    return "<span class='status status-info'>Siap Kirim</span>";
                }
                else if ($this->type == 'thawing_request') {
                    return "<span class='status status-info'>Request Thawing</span>";
                }
            }
        } else 
        if ($this->type == 'bahan-baku') {
            return "<span class='status status-info'>Bahan Baku</span>";
        }
        else if ($this->type == 'freestock') {
            return "<span class='status status-info'>Freestock</span>";
        }
        else if ($this->type == 'hasil-produksi') {
            return "<span class='status status-info'>Hasil Produksi</span>";
        }
        else if ($this->type == 'inventory_adjustment') {
            return "<span class='status status-info'>Inventory Adjustment</span>";
        }
        else if ($this->type == 'openbalance') {
            return "<span class='status status-info'>Open Balance</span>";
        }
        else if ($this->type == 'siapkirim') {
            return "<span class='status status-info'>Siap Kirim</span>";
        }
        else if ($this->type == 'thawing_request') {
            return "<span class='status status-info'>Request Thawing</span>";
        }
    }

    public static function inbound($tanggal)
    {
        return  Product_gudang::where('jenis_trans', 'masuk')
                ->whereDate('production_date', $tanggal)
                ->where('production_date', '>=', '2022-02-04')
                ->sum('berat_awal');
    }

    public static function outbound($tanggal)
    {
        return  Product_gudang::where('jenis_trans', 'keluar')
                ->whereDate('production_date', $tanggal)
                ->where('production_date', '>=', '2022-02-04')
                ->sum('berat_awal');
    }

    public static function dailyWarehouseTransaction($startdate,$enddate,$gudangid){
        $data =  Product_gudang::whereBetween('production_date',[$startdate,$enddate])
                            ->whereIn('jenis_trans',['masuk','keluar'])
                            ->whereIn('gudang_id',$gudangid)
                            ->select('production_date',
                                    DB::raw("SUM(IF(jenis_trans='masuk', qty_awal,0)) as qty_inbound"),
                                    DB::raw("SUM(IF(jenis_trans='masuk', berat_awal,0)) as berat_inbound"),
                                    DB::raw("SUM(IF(jenis_trans='keluar', qty_awal,0)) as qty_outbound"),
                                    DB::raw("SUM(IF(jenis_trans='keluar', berat_awal,0)) as berat_outbound"),
                                    DB::raw("SUM(IF(jenis_trans='masuk', qty,0)) as sisa_qty_inbound"),
                                    DB::raw("SUM(IF(jenis_trans='masuk', berat,0)) as sisa_berat_inbound")
                            )
                            ->where('production_date','>=',Applib::BatasMinimalTanggal())
                            ->groupBy('production_date')
                            ->whereNull('deleted_at')
                            ->get();
        
        return $data;
    }

    public static function getSaldoAwal($tanggal,$gudangid,$type){
        $sql = Product_gudang::whereIn('gudang_id',$gudangid)
                                ->where('production_date','>=',Applib::BatasMinimalTanggal())
                                ->where('production_date','<',$tanggal)
                                ->select(
                                        DB::raw("SUM(IF(production_date between '".Applib::BatasMinimalTanggal()."' AND '".$tanggal."' AND jenis_trans = 'masuk', qty_awal, 0)) - SUM(IF(production_date between '".Applib::BatasMinimalTanggal()."' AND '".$tanggal."' AND jenis_trans = 'keluar', qty_awal, 0)) as qty_saldo_akhir "),
                                        DB::raw("SUM(IF(production_date BETWEEN '".Applib::BatasMinimalTanggal()."' AND '".$tanggal."' AND jenis_trans = 'masuk', berat_awal, 0)) - SUM(IF(production_date between '".Applib::BatasMinimalTanggal()."' AND '".$tanggal."' AND jenis_trans = 'keluar', berat_awal, 0)) as berat_saldo_akhir ")
                                )
                                ->get();
        if(count($sql) > 0){
            foreach ($sql as $h) {
                $hasil = $h->$type;
            }
        } else {
            $hasil = '';
        }
        return $hasil;
    }

    public static function getItemByStockType($tanggal_akhir,$type,$gudangid){
        $sql = Product_gudang::select('product_id', 'nama',DB::raw("round(SUM(berat),2) AS sisa"))
                                // ->whereBetween('production_date', [$tanggal_awal, $tanggal_akhir])
                                ->where('production_date','<=', $tanggal_akhir)
                                ->where('jenis_trans', 'masuk')
                                ->whereIn('gudang_id',$gudangid)
                                ->whereDate('production_date', '>=', Applib::BatasMinimalTanggal())
                                ->where('stock_type', $type)
                                ->groupBy('product_id','nama')
                                ->orderBy(DB::raw("round(SUM(berat),2)"), 'DESC')
                                ->get() ;
        return $sql;
    }
    public static function coh($regu=FALSE, $jenis=FALSE, $tanggal_awal=FALSE, $tanggal_akhir=FALSE)
    {
        return  Product_gudang::whereBetween('production_date', [$tanggal_awal, $tanggal_akhir])
                ->where(function($query) use ($jenis) {
                    if ($jenis == 'free' || $jenis == 'booking') {
                        $query->where('stock_type', $jenis);
                    }
                })
                ->where('production_date', '>=', '2022-02-04')
                ->where('status', 2)
                ->where('table_name', 'abf')
                ->whereIn('product_id', Item::select('id')->where(function($query) use ($regu) {
                    if ($regu == 'whole') {
                        $query->where('category_id', 7);
                    }
                    if ($regu == 'marinasi') {
                        $query->where('category_id', 9);
                    }
                    if ($regu == 'parting') {
                        $query->where('category_id', 8);
                    }
                    if ($regu == 'boneless') {
                        $query->where('category_id', 11);
                    }
                    if ($regu == 'byproduct') {
                        $query->where('category_id', 10);
                    }
                }))
                ->sum('berat');
    }

    public static function detailfilter($tanggal, $tipe, $nama,$konsumen,$lokasi,$kemasan,$subitem,$customerid){
        return Product_gudang::where('product_id', $nama)
        ->where('packaging', $kemasan)
        ->where('sub_item', $subitem)
        ->where('kategori', $konsumen)
        ->where('gudang_id', $lokasi)
        ->where('berat', '>', 0)
        ->where('customer_id', $customerid)
        ->where('production_date', '=', $tanggal)
        ->where(function($query) use ($tipe) {
            if ($tipe == 'keluar') {
                $query->where('jenis_trans', 'keluar');
            }
            if($tipe == 'masuk') {
                $query->where('jenis_trans', 'masuk');
            }
        })
        ->sum('berat');
    }

    public static function allFilter($tanggal, $tipe, $tahun, $bulan, $search, $gudang, $kemasan, $nama, $lokasi, $konsumen, $subitem, $customerid){
        $awal = Carbon::create($tahun, $bulan)->startOfMonth()->format('Y-m-d');
        if($tipe == 'hasil'){
            return Product_gudang::where('product_id', $nama)
            ->where('packaging', $kemasan)
            ->where('sub_item', $subitem)
            ->where('kategori', $konsumen)
            ->where('gudang_id', $lokasi)
            ->where('berat', '>', 0)
            ->where('customer_id', $customerid)
            ->where('production_date', '=', $tanggal)
            ->where(function($query) use ($tipe, $tanggal, $awal) {
                if ($tipe == 'keluar') {
                    $query->where('jenis_trans', 'keluar');
                }
                if($tipe == 'masuk') {
                    $query->where('jenis_trans', 'masuk');
                }
            })
            ->sum('berat');
        } else {
            return Product_gudang::where('product_id', $nama)
            ->where('packaging', $kemasan)
            ->where('sub_item', $subitem)
            ->where('kategori', $konsumen)
            ->where('gudang_id', $lokasi)
            ->where('berat', '>', 0)
            ->where('customer_id', $customerid)
            ->where('production_date', '=', $tanggal)
            ->where(function($query) use ($tipe, $tanggal, $awal) {
                if ($tipe == 'keluar') {
                    $query->where('jenis_trans', 'keluar');
                }
                if($tipe == 'masuk') {
                    $query->where('jenis_trans', 'masuk');
                }
            })
            ->sum('berat');
        }
    }

    public static function inbound_soh($tanggal, $item, $pack, $subpack, $konsumen, $tipe, $hitung)
    {
        $data   =   Product_gudang::select('qty', 'berat', 'karung_qty')
                    ->where('product_id', $item)
                    ->where('packaging', $pack)
                    ->where('subpack', $subpack)
                    ->where('customer_id', $konsumen)
                    ->whereDate('production_date', $tanggal)
                    ->where(function($query) use ($tipe) {
                        if ($tipe == 'production') {
                            $query->whereIn('table_id', Abf::select('id')->where('asal_tujuan', 'kepala_produksi')) ;
                        }
                        if ($tipe == 'retur') {
                            $query->whereIn('table_id', Abf::select('id')->where('asal_tujuan', 'retur')) ;
                        }
                        if ($tipe == 'inbound_other') {
                            $query->whereIn('table_id', Abf::select('id')->whereNotIn('asal_tujuan', ['retur', 'kepala_produksi'])) ;
                        }
                    })
                    ->where('status', 2);

        if ($hitung == 'ep') {
            return $data->sum('qty_awal') ;
        }
        if ($hitung == 'kg') {
            return $data->sum('berat_awal');
        }
        if ($hitung == 'krg') {
            return $data->sum('karung_qty');
        }
    }

    public static function wh_inbound_soh($tanggal, $item, $pack, $subpack, $konsumen)
    {
        $data   =   Product_gudang::select('qty', 'berat', 'karung_qty')
                    ->where('product_id', $item)
                    ->where('packaging', $pack)
                    ->where('subpack', $subpack)
                    ->where('customer_id', $konsumen)
                    ->whereDate('production_date', $tanggal)
                    ->where('status', 2);

        $prod_data  = clone $data;
        $retur_data = clone $data;
        $other_data = clone $data;

        
         $prod_data =  $prod_data->where(function($query){
                            $query->whereIn('table_id', Abf::select('id')->where('asal_tujuan', 'kepala_produksi')) ;
                        });
         $retur_data =  $retur_data->where(function($query){
                            $query->whereIn('table_id', Abf::select('id')->where('asal_tujuan', 'retur')) ;
                        });
         $other_data =  $other_data->where(function($query){
                            $query->whereIn('table_id', Abf::select('id')->whereNotIn('asal_tujuan', ['retur', 'kepala_produksi'])) ;
                        });

        $response_data = array(
            'production' => array(
                'ep'    => (clone $prod_data)->sum('qty_awal'),
                'kg'    => (clone $prod_data)->sum('berat_awal'),
                'krg'   => (clone $prod_data)->sum('karung_qty')
            ),
            'retur' => array(
                'ep'    => (clone $retur_data)->sum('qty_awal'),
                'kg'    => (clone $retur_data)->sum('berat_awal'),
                'krg'   => (clone $retur_data)->sum('karung_qty')
            ),
            'other' => array(
                'ep'    => (clone $other_data)->sum('qty_awal'),
                'kg'    => (clone $other_data)->sum('berat_awal'),
                'krg'   => (clone $other_data)->sum('karung_qty')
            )
        );

        return $response_data;
    }


    public static function wh_soh($tanggal, $item, $pack, $subpack, $konsumen)
    {
        // Query basic in
        $data_in   =   Product_gudang::select('qty', 'berat', 'karung_qty')
                    ->where('product_id', $item)
                    ->where('plastik_group', $pack)
                    ->where('subpack', $subpack)
                    ->where('customer_id', $konsumen)
                    ->whereDate('production_date', $tanggal)
                    ->where('status', 2);

        // Query basic out
        $data_out   =   Product_gudang::select('qty_awal', 'berat_awal', 'karung_qty')
                    ->where('product_id', $item)
                    ->where('plastik_group', $pack)
                    ->where('subpack', $subpack)
                    ->where('customer_id', $konsumen)
                    ->whereDate('production_date', $tanggal)
                    ->where('status', 4);


        $response_data = array(
            'data_in' => array(
                'ep'    => (clone $data_in)->sum('qty_awal'),
                'kg'    => (clone $data_in)->sum('berat_awal'),
                'krg'   => (clone $data_in)->sum('karung_qty')
            )
        );

        $response_out = array(
            'data_out' => array(
                'ep'    => (clone $data_out)->sum('qty_awal'),
                'kg'    => (clone $data_out)->sum('berat_awal'),
                'krg'   => (clone $data_out)->sum('karung_qty')
            )
        );

        $final = array(
            'inbound' => $response_data,
            'outbond' => $response_out
        );

        return $final;
    }

    // public static function wh_soh($tanggal, $item, $pack, $subpack, $konsumen)
    // {
    //     // Query basic in
    //     $data_in   =   Product_gudang::select('qty', 'berat', 'karung_qty')
    //                 ->where('product_id', $item)
    //                 ->where('plastik_group', $pack)
    //                 ->where('subpack', $subpack)
    //                 ->where('customer_id', $konsumen)
    //                 ->whereDate('production_date', $tanggal)
    //                 ->where('status', 2);

    //     $prod_data  = clone $data_in;
    //     $retur_data = clone $data_in;
    //     $other_data = clone $data_in;


    //     // Query basic out
    //     $data_out   =   Product_gudang::select('qty_awal', 'berat_awal', 'karung_qty')
    //                 ->where('product_id', $item)
    //                 ->where('plastik_group', $pack)
    //                 ->where('subpack', $subpack)
    //                 ->where('customer_id', $konsumen)
    //                 ->whereDate('production_date', $tanggal)
    //                 ->where('status', 4);

    //     $kirim_data  = clone $data_out;
    //     $reprod_data = clone $data_out;

        
    //      $prod_data =  $prod_data->where(function($query){
    //                         $query->whereIn('table_id', Abf::select('id')->where('asal_tujuan', 'kepala_produksi')) ;
    //                     });
    //      $retur_data =  $retur_data->where(function($query){
    //                         $query->whereIn('table_id', Abf::select('id')->where('asal_tujuan', 'retur')) ;
    //                     });
    //      $other_data =  $other_data->where(function($query){
    //                         $query->whereIn('table_id', Abf::select('id')->whereNotIn('asal_tujuan', ['retur', 'kepala_produksi'])) ;
    //                     });

    //      $kirim_data    =  $kirim_data->where('type', 'siapkirim');
    //      $reprod_data   =  $reprod_data->where('type', 'thawing_request');

    //     $response_data = array(
    //         'production' => array(
    //             'ep'    => (clone $prod_data)->sum('qty_awal'),
    //             'kg'    => (clone $prod_data)->sum('berat_awal'),
    //             'krg'   => (clone $prod_data)->sum('karung_qty')
    //         ),
    //         'retur' => array(
    //             'ep'    => (clone $retur_data)->sum('qty_awal'),
    //             'kg'    => (clone $retur_data)->sum('berat_awal'),
    //             'krg'   => (clone $retur_data)->sum('karung_qty')
    //         ),
    //         'other' => array(
    //             'ep'    => (clone $other_data)->sum('qty_awal'),
    //             'kg'    => (clone $other_data)->sum('berat_awal'),
    //             'krg'   => (clone $other_data)->sum('karung_qty')
    //         )
    //     );

    //     $response_out = array(
    //         'reprod' => array(
    //             'ep'    => (clone $reprod_data)->sum('qty_awal'),
    //             'kg'    => (clone $reprod_data)->sum('berat_awal'),
    //             'krg'   => (clone $reprod_data)->sum('karung_qty')
    //         ),
    //         'kiriman' => array(
    //             'ep'    => (clone $kirim_data)->sum('qty_awal'),
    //             'kg'    => (clone $kirim_data)->sum('berat_awal'),
    //             'krg'   => (clone $kirim_data)->sum('karung_qty')
    //         )
    //     );

    //     $final = array(
    //         'inbound' => $response_data,
    //         'outbond' => $response_out
    //     );

    //     return $final;
    // }

    public static function outbound_soh($tanggal, $item, $pack, $subpack, $konsumen, $tipe, $hitung)
    {
        if ($tipe == 'pack_slip') {
            $data   =   Bahanbaku::whereIn('chiller_out', Product_gudang::select('id')
                            ->where('product_id', $item)
                            ->where('packaging', $pack)
                            ->where('subpack', $subpack)
                            ->where('customer_id', $konsumen)
                        )
                        ->whereIn('order_id', Order::select('id')->where('tanggal_so', $tanggal));

            if ($hitung == 'ep') {
                return $data->sum('bb_item');
            }
            if ($hitung == 'kg') {
                return $data->sum('bb_berat');
            }
            if ($hitung == 'krg') {
                return $data->sum('keranjang');
            }
        }

        if ($tipe == 'reprocess') {
            $data   =   Product_gudang::select('qty', 'berat', 'karung_qty')
                        ->where('product_id', $item)
                        ->where('packaging', $pack)
                        ->where('subpack', $subpack)
                        ->where('customer_id', $konsumen)
                        ->whereDate('production_date', $tanggal)
                        ->where('type', 'thawing_request');

            if ($hitung == 'ep') {
                return $data->sum('qty_awal') ;
            }
            if ($hitung == 'kg') {
                return $data->sum('berat_awal');
            }
            if ($hitung == 'krg') {
                return $data->sum('karung_qty');
            }
        }
    }

    public static function modusKarungIsi($productId, $plastikGroup, $subItem, $tanggal, $customerId, $gradeItem, $gudangID) {
        $data = Product_gudang::select(DB::raw('count(id) as karung'), 'karung_isi')
                                ->where('product_id', $productId)
                                ->where('plastik_group', $plastikGroup)
                                ->where('sub_item', $subItem)
                                ->where('production_date', $tanggal)
                                ->where('customer_id', $customerId)
                                ->where('grade_item', $gradeItem)
                                ->where('gudang_id', $gudangID)
                                ->where('karung_isi', '!=', NULL)
                                ->groupBy('karung_isi')
                                ->orderBy('karung_isi', 'desc')
                                ->first('karung_isi');

        return  $data['karung_isi'] ?? '';
        
    }

    public static function cekTypeData($productId, $plastikGroup, $subItem, $tanggal, $customerId, $gradeItem, $gudangID) {
        $data = Product_gudang::where('product_id', $productId)
                                ->where('plastik_group', $plastikGroup)
                                ->where('sub_item', $subItem)
                                ->where('production_date', $tanggal)
                                ->where('customer_id', $customerId)
                                ->where('grade_item', $gradeItem)
                                ->where('gudang_id', $gudangID)
                                ->get();

        foreach ($data as $value) {
            if ($value->type != 'openbalance') {
                return 0;
            } else {
                return 1;
            }
        }

        
    }

    public static function ambilsisaproductgudang($gudang_id, $name, $name2,$name3,$bbid=FALSE){
        $dataGudang         = Product_gudang::where('id', $gudang_id)->sum($name);
        // $dataFreestockList  = FreestockList::where( function($q) use ($gudang_id){
        //                         $q->where('chiller_id', $gudang_id);
        //                         $q->whereNull('outchiller');
        //                     })
        //                     ->get()
        //                     ->sum($name2);

        $dataAlokasiSiapKirim = Bahanbaku::select(DB::raw("round(ifnull(sum($name3),0),2) as sum"))
                                            ->where( function($q) use ($gudang_id,$bbid){
                                            $q->where('chiller_out', $gudang_id);
                                            $q->whereIn('status',[1,2]);
                                            $q->where('proses_ambil','frozen');
                                            if($bbid){
                                                $q->where('id','!=',$bbid);
                                            }
                                        })
                                        // ->get()
                                        // ->sum($name3);
                                        ->first()
                                        ->sum;

        $dataThawing        = Thawinglist::select(DB::raw("round(ifnull(sum($name2),0),2) as sum"))
                                            ->where('item_id', $gudang_id)
                                            ->where( function($q){
                                                $q->whereNull('status');
                                                $q->orwhere('status',1);
                                            })
                                            // ->get()
                                            // ->sum($name2);
                                            ->first()
                                            ->sum;

        $regrading        = Product_gudang::select(DB::raw("round(ifnull(sum($name),0),2) as sum"))
                                            ->where('gudang_id_keluar', $gudang_id)                            
                                            ->where('status',4)
                                            ->where('type','grading_ulang')
                                            // ->get()
                                            // ->sum($name);
                                            ->first()
                                            ->sum;
        
        $dataAlokasiMusnahkan   = Musnahkantemp::select(DB::raw("round(ifnull(sum($name2),0),2) as sum"))
                                            ->whereNotIn('gudang_id',['2','4','23','24'])
                                            ->where('item_id', $gudang_id)
                                            ->whereIn('musnahkan_id', Musnahkan::select('id')->whereNull('deleted_at'))
                                            // ->get()
                                            // ->sum($name2);
                                            ->first()
                                            ->sum;
        // $totalReal          = $dataGudang - $dataFreestockList - $dataAlokasiSiapKirim;
        // $totalReal          = $dataGudang  - $dataAlokasiSiapKirim - $dataThawing ;
        $totalReal          = $dataGudang  - $dataAlokasiSiapKirim - $dataThawing - $regrading - $dataAlokasiMusnahkan ;
        return $totalReal;
    }

}

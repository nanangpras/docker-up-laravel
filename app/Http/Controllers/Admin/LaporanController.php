<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Chiller;
use App\Models\Evis;
use App\Models\Retur;
use App\Models\Order;
use App\Models\Freestock;
use App\Models\FreestockTemp;
use App\Models\Grading;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Purchasing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{

    public function index()
    {
        return view('admin.pages.laporan.admin.index');
    }

    public function laporan(Request $request)
    {

        $tanggal1    =   $request->tglmulai ?? date('Y-m-d');
        $tanggal2    =   $request->tglselesai ?? date('Y-m-d');

        if ($request->tujuan == 'lpah') {
            return view('admin.pages.laporan.admin.lpah');
        } elseif ($request->tujuan == 'evis') {
            return view('admin.pages.laporan.admin.evis');
        } elseif ($request->tujuan == 'grading') {
            return view('admin.pages.laporan.admin.grading');
        } elseif ($request->tujuan == 'hasilproduksi') {
            return view('admin.pages.laporan.admin.hasilproduksi');
        } elseif ($request->tujuan == 'ambilbb') {
            return view('admin.pages.laporan.admin.ambilbb');
        } elseif ($request->tujuan == 'siapkirim') {
            return view('admin.pages.laporan.admin.siapkirim');
        } elseif ($request->tujuan == 'sisachiller') {
            return view('admin.pages.laporan.admin.sisachiller');
        } elseif ($request->tujuan == 'abf') {
            return view('admin.pages.laporan.admin.abf');
        } elseif ($request->tujuan == 'gudang') {
            return view('admin.pages.laporan.admin.gudang');
        } elseif ($request->tujuan == 'retur') {
            return view('admin.pages.laporan.admin.retur');
        }
    }

    public function showDataTableLpah(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = Production::whereBetween('prod_tanggal_potong', [$tanggal1, $tanggal2])->where('lpah_status', '1')->orderBy('no_urut', 'asc');
        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();

        $length                         = intval($_REQUEST['length']);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($_REQUEST['start']);
        $draw                           = intval($_REQUEST['draw']);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ''){
            $cloneQuery2                = clone $sql;
            $query                      = $cloneQuery2->where(function($filter) use ($search) {
                                                $filter->orWhere('no_po', 'like', '%' . $search . '%');
                                                $filter->orWhere('sc_nama_kandang', 'like', '%' . $search . '%');
                                                $filter->orWhere('sc_pengemudi', 'like', '%' . $search . '%');
                                                $filter->orWhere('no_lpah', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            $no = $start + 1;
            foreach ($query as $val) {
                $vendor         = $val->prodpur->purcsupp->nama ? $val->prodpur->purcsupp->nama : '';
                $persensusut    = $val->lpah_persen_susut ? $val->lpah_persen_susut: 0 ." %";
                $basah          = $val->lpah_basah ? $val->lpah_basah: 0 ." %";
                $downtime       = $val->lpah_downtime ? $val->lpah_downtime : 0 ." Menit";
                $output['data'][] =
                    array(
                        $no,
                        $val->prod_tanggal_potong,
                        $val->no_urut. " ( ".$vendor." )",
                        $val->sc_pengemudi,
                        $val->no_lpah,
                        $val->sc_jam_masuk,
                        $val->lpah_jam_bongkar,
                        number_format($val->sc_ekor_do, 0),
                        number_format($val->sc_berat_do, 2),
                        $val->sc_rerata_do,
                        number_format($val->ekoran_seckle, 0),
                        number_format($val->lpah_berat_terima, 2),
                        $val->lpah_rerata_terima,
                        number_format($val->qc_ekor_ayam_mati, 0)." Ekor / ".$val->qc_berat_ayam_mati." Kg",
                        $val->qc_ekor_ayam_merah." Ekor / ".$val->qc_berat_ayam_merah." Kg",
                        $val->qc_tembolok." Kg",
                        $val->lpah_kebersihan_keranjang,
                        $persensusut,
                        $basah,
                        $downtime,
                        // number_format($val->lpah_berat_susut, 2),
                        // $val->no_do,
                        // $val->sc_no_polisi,
                        // $val->keaktifan,
                        // $val->lpah_persen_susut,
                        // number_format($val->sc_ekor_do != 0 ? ($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 : 0, 2),
                        // number_format($val->sc_berat_do != 0 ? ($val->qc_berat_ayam_mati / $val->sc_berat_do) * 100 : 0, 2)
                    );
                $no++;
            }
            $cloneQuery3                    = clone $sql;
            $rows                           = $cloneQuery3->where(function($filter) use ($search) {
                                                    $filter->orWhere('no_po', 'like', '%' . $search . '%');
                                                    $filter->orWhere('sc_nama_kandang', 'like', '%' . $search . '%');
                                                    $filter->orWhere('sc_pengemudi', 'like', '%' . $search . '%');
                                                    $filter->orWhere('no_lpah', 'like', '%' . $search . '%');
                                                })
                                                ->get();

            $output['draw']                 = $draw;
            $output['recordsTotal']         = $output['recordsFiltered']      = $rows->count();

        }
        else{
            $cloneQuery4                    = clone $sql;
            $query                          = $cloneQuery4->take($length)->skip($start)->get();
            
            $no = $start + 1;
            foreach ($query as $val) {
                $vendor         = $val->prodpur->purcsupp->nama ? $val->prodpur->purcsupp->nama : '';
                $persensusut    = $val->lpah_persen_susut ? $val->lpah_persen_susut." %": '';
                $basah          = $val->lpah_basah ? $val->lpah_basah." %": '';
                $downtime       = $val->lpah_downtime ? $val->lpah_downtime." Menit" : '';
                $output['data'][] =
                    array(
                        $no,
                        $val->prod_tanggal_potong,
                        $val->no_urut. " ( ".$vendor." )",
                        $val->sc_pengemudi,
                        $val->no_lpah,
                        $val->sc_jam_masuk,
                        $val->lpah_jam_bongkar,
                        number_format($val->sc_ekor_do, 0),
                        number_format($val->sc_berat_do, 2),
                        $val->sc_rerata_do,
                        number_format($val->ekoran_seckle, 0),
                        number_format($val->lpah_berat_terima, 2),
                        $val->lpah_rerata_terima,
                        number_format($val->qc_ekor_ayam_mati, 0)." Ekor / ".$val->qc_berat_ayam_mati." Kg",
                        $val->qc_ekor_ayam_merah." Ekor / ".$val->qc_berat_ayam_merah." Kg",
                        $val->qc_tembolok." Kg",
                        $val->lpah_kebersihan_keranjang,
                        $persensusut,
                        $basah,
                        $downtime,
                        // number_format($val->lpah_berat_susut, 2),
                        // $val->no_do,
                        // $val->sc_no_polisi,
                        // $val->keaktifan,
                        // $val->lpah_persen_susut,
                        // number_format($val->sc_ekor_do != 0 ? ($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 : 0, 2),
                        // number_format($val->sc_berat_do != 0 ? ($val->qc_berat_ayam_mati / $val->sc_berat_do) * 100 : 0, 2)
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function showDataTableEvis(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = Evis::leftJoin('productions','evis.production_id','productions.id')
                                                ->leftJoin('purchasing','productions.purchasing_id','purchasing.id')
                                                ->leftJoin('items','evis.item_id','items.id')
                                                ->leftJoin('supplier','purchasing.supplier_id','supplier.id')
                                                ->select(
                                                        'purchasing.no_po AS no_po',
                                                        'productions.no_do AS no_do',
                                                        'productions.sc_pengemudi AS sc_pengemudi',
                                                        'productions.sc_jam_masuk AS sc_jam_masuk',
                                                        'productions.no_lpah AS no_lpah',
                                                        'productions.no_urut AS no_urut',
                                                        'productions.lpah_jam_bongkar AS lpah_jam_bongkar',
                                                        'evis.tanggal_potong AS tanggal_potong',
                                                        'evis.item_id AS item_id',
                                                        'items.nama AS nama',
                                                        'evis.total_item AS total_item',
                                                        'evis.berat_item AS berat_item',
                                                        'supplier.nama AS supplier'
                                                )
                                                ->whereBetween('evis.tanggal_potong', [$tanggal1, $tanggal2]);
        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();

        $length                         = intval($_REQUEST['length']);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($_REQUEST['start']);
        $draw                           = intval($_REQUEST['draw']);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ''){
            $cloneQuery2                = clone $sql;
            $query                      = $cloneQuery2->where(function($filter) use ($search) {
                                                $filter->orWhere('supplier.nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('productions.no_do', 'like', '%' . $search . '%');
                                                $filter->orWhere('productions.sc_pengemudi', 'like', '%' . $search . '%');
                                                $filter->orWhere('items.nama', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_potong,
                        $val->supplier,
                        $val->no_urut,
                        $val->no_do,
                        $val->sc_pengemudi,
                        $val->sc_jam_masuk,
                        $val->lpah_jam_bongkar,
                        $val->nama,
                        $val->total_item,
                        $val->berat_item
                    );
                $no++;
            }
            $cloneQuery3                    = clone $sql;
            $rows                           = $cloneQuery3->where(function($filter) use ($search) {
                                                    $filter->orWhere('supplier.nama', 'like', '%' . $search . '%');
                                                    $filter->orWhere('productions.no_do', 'like', '%' . $search . '%');
                                                    $filter->orWhere('productions.sc_pengemudi', 'like', '%' . $search . '%');
                                                    $filter->orWhere('items.nama', 'like', '%' . $search . '%');
                                            })
                                            ->get();

            $output['draw']                 = $draw;
            $output['recordsTotal']         = $output['recordsFiltered']      = $rows->count();

        }
        else{
            $cloneQuery4                    = clone $sql;
            $query                          = $cloneQuery4->take($length)->skip($start)->get();
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_potong,
                        $val->supplier,
                        $val->no_urut,
                        $val->no_do,
                        $val->sc_pengemudi,
                        $val->sc_jam_masuk,
                        $val->lpah_jam_bongkar,
                        $val->nama,
                        $val->total_item,
                        $val->berat_item
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function showDataTableGrading(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = Grading::leftJoin('productions','grading.trans_id','productions.id')
                                                    ->leftJoin('purchasing','productions.purchasing_id','purchasing.id')
                                                    ->leftJoin('items','grading.item_id','items.id')
                                                    ->leftJoin('supplier','purchasing.supplier_id','supplier.id')
                                                    ->select(
                                                        'productions.sc_pengemudi AS sc_pengemudi',
                                                        'purchasing.type_po AS type_po',
                                                        'purchasing.no_po AS no_po',
                                                        'grading.item_id AS item_id',
                                                        'items.nama AS nama',
                                                        'productions.sc_nama_kandang AS sc_nama_kandang',
                                                        'productions.sc_tanggal_masuk AS sc_tanggal_masuk',
                                                        'productions.sc_jam_masuk AS sc_jam_masuk',
                                                        'grading.total_item AS total_item',
                                                        'grading.berat_item AS berat_item',
                                                        'purchasing.tanggal_potong AS tanggal_potong',
                                                        'productions.no_urut AS no_urut',
                                                        'productions.no_lpah AS no_lpah',
                                                        'productions.no_do AS no_do',
                                                        'productions.lpah_jam_bongkar AS lpah_jam_bongkar',
                                                        'supplier.nama AS supplier'
                                                    )
                                                    ->whereBetween('grading.tanggal_potong', [$tanggal1, $tanggal2])
                                                    ->whereNull('grading.deleted_at');
        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();

        $length                         = intval($_REQUEST['length']);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($_REQUEST['start']);
        $draw                           = intval($_REQUEST['draw']);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ''){
            $cloneQuery2                = clone $sql;
            $query                      = $cloneQuery2
                                            ->where(function($filter) use ($search) {
                                                $filter->orWhere('items.nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('supplier.nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('productions.no_do', 'like', '%' . $search . '%');
                                                $filter->orWhere('productions.no_lpah', 'like', '%' . $search . '%');
                                                $filter->orWhere('productions.sc_pengemudi', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_potong,
                        $val->supplier,
                        $val->no_urut,
                        $val->no_do,
                        $val->sc_pengemudi,
                        $val->sc_jam_masuk,
                        $val->lpah_jam_bongkar,
                        $val->nama,
                        $val->total_item,
                        $val->berat_item
                    );
                $no++;
            }
            $cloneQuery3                    = clone $sql;
            $rows                           = $cloneQuery3
                                                ->where(function($filter) use ($search) {
                                                    $filter->orWhere('items.nama', 'like', '%' . $search . '%');
                                                    $filter->orWhere('supplier.nama', 'like', '%' . $search . '%');
                                                    $filter->orWhere('productions.no_do', 'like', '%' . $search . '%');
                                                    $filter->orWhere('productions.no_lpah', 'like', '%' . $search . '%');
                                                    $filter->orWhere('productions.sc_pengemudi', 'like', '%' . $search . '%');
                                                })
                                                ->get();

            $output['draw']                 = $draw;
            $output['recordsTotal']         = $output['recordsFiltered']      = $rows->count();

        }
        else{
            $cloneQuery4                    = clone $sql;
            $query                          = $cloneQuery4->take($length)->skip($start)->get();
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_potong,
                        $val->supplier,
                        $val->no_urut,
                        $val->no_do,
                        $val->sc_pengemudi,
                        $val->sc_jam_masuk,
                        $val->lpah_jam_bongkar,
                        $val->nama,
                        $val->total_item,
                        $val->berat_item
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function showDataTableFG(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = FreestockTemp::leftJoin('free_stock','free_stocktemp.freestock_id','free_stock.id')
                                                        ->select(
                                                            'free_stocktemp.item_id AS item_id',
                                                            'free_stocktemp.prod_nama AS item_name',
                                                            'free_stocktemp.regu AS regu',
                                                            'free_stocktemp.qty AS qty',
                                                            'free_stocktemp.berat AS berat',
                                                            'free_stocktemp.id AS table_id',
                                                            'free_stocktemp.deleted_at AS deleted_at',
                                                            'free_stock.tanggal AS tanggal_produksi'
                                                        )
                                                        ->where(function($q){
                                                            $q->whereNull('free_stock.deleted_at');
                                                            $q->whereNull('free_stocktemp.deleted_at');
                                                            $q->where('free_stock.status',3);
                                                        })
                                                        ->whereBetween('tanggal_produksi', [$tanggal1, $tanggal2])
                                                        ->orderBy('free_stocktemp.id','DESC');
        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();

        $length                         = intval($_REQUEST['length']);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($_REQUEST['start']);
        $draw                           = intval($_REQUEST['draw']);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ''){
            $cloneQuery2                = clone $sql;
            $query                      = $cloneQuery2->where(function($filter) use ($search) {
                                                $filter->orWhere('free_stock.regu', 'like', '%' . $search . '%');
                                                $filter->orWhere('free_stocktemp.prod_nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('free_stocktemp.qty', 'like', '%' . $search . '%');
                                                $filter->orWhere('free_stocktemp.berat', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            $no = $start + 1;
            foreach ($query as $val) {
                // if($val->table_name == 'free_stocktemp'){
                    $getFSId        = FreestockTemp::getfreestockid($val->table_id);
                    // if($getFSId != ''){
                    //     $ns_send    = Freestock::getNetsuiteSend($getFSId);
                    //     if($ns_send == '0'){
                    //         $tipe   = "<span class='badge badge-danger'>Non WO</span>";
                    //     }else{
                    //         $tipe   = "<span class='badge badge-primary'>Pakai WO</span>";
                    //     }
                    // }
                // }else{
                    // $tipe           = '';
                // }

                if($val->regu == ''){
                    $linkitem       = $val->item_name;
                }else{
                    $linkitem       = "<a href=".url('admin/produksi-regu?kategori='.$val->regu.'&produksi='.$getFSId).">".$val->item_name."</a>";
                }
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_produksi,
                        $val->regu,
                        $linkitem,
                        $val->qty,
                        $val->berat,
                        // $tipe
                    );
                $no++;
            }
            $cloneQuery3                    = clone $sql;
            $rows                           = $cloneQuery3->where(function($filter) use ($search) {
                                                    $filter->orWhere('free_stock.regu', 'like', '%' . $search . '%');
                                                    $filter->orWhere('free_stocktemp.prod_nama', 'like', '%' . $search . '%');
                                                    $filter->orWhere('free_stocktemp.qty', 'like', '%' . $search . '%');
                                                    $filter->orWhere('free_stocktemp.berat', 'like', '%' . $search . '%');
                                                })
                                                ->get();

            $output['draw']                 = $draw;
            $output['recordsTotal']         = $output['recordsFiltered']      = $rows->count();

        }
        else{
            $cloneQuery4                    = clone $sql;
            $query                          = $cloneQuery4->take($length)->skip($start)->get();
            $no = $start + 1;
            foreach ($query as $val) {
                // if($val->table_name == 'free_stocktemp'){
                    $getFSId        = FreestockTemp::getfreestockid($val->table_id);
                    // if($getFSId){
                    //     $ns_send    = Freestock::getNetsuiteSend($getFSId);
                    //     if($ns_send == '0'){
                    //         $tipe   = "<span class='badge badge-danger'>Non WO</span>";
                    //     }else{
                    //         $tipe   = "<span class='badge badge-primary'>Pakai WO</span>";
                    //     }
                    // }
                // }else{
                    // $tipe           = '';
                // }

                if($val->regu == ''){
                    $linkitem       = $val->item_name;
                }else{
                    $linkitem       = "<a href=".url('admin/produksi-regu?kategori='.$val->regu.'&produksi='.$getFSId).">".$val->item_name."</a>";
                }
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_produksi,
                        $val->regu,
                        $linkitem,
                        $val->qty,
                        $val->berat,
                        // $tipe
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function showDataTableBB(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = Chiller::leftJoin('free_stocklist','chiller.id','free_stocklist.outchiller')
                                                    ->leftJoin('free_stock','free_stocklist.freestock_id','free_stock.id')
                                                    // ->leftJoin('chiller as d','d.id','free_stocklist.chiller_id')
                                                    ->select(
                                                        'chiller.item_name AS item_name',
                                                        'chiller.asal_tujuan AS asal_tujuan',
                                                        'chiller.tanggal_produksi AS tanggal_produksi',
                                                        'chiller.id AS chiller_id',
                                                        'free_stocklist.outchiller AS idout',
                                                        'chiller.qty_item AS qty_item',
                                                        'chiller.berat_item AS berat_item',
                                                        'free_stocklist.item_id AS item_id',
                                                        'free_stocklist.id AS idoutb',
                                                        'free_stocklist.berat AS beratambil',
                                                        'free_stocklist.qty AS qtyambil',
                                                        'free_stocklist.regu AS regu',
                                                        'free_stock.tanggal AS tanggal_free',
                                                        'free_stocklist.bb_kondisi AS jenis_bb',
                                                        'free_stock.netsuite_send AS netsuite_send'
                                                    )
                                                    ->where(function($q){
                                                        $q->whereNull('chiller.deleted_at');
                                                        $q->whereNull('free_stocklist.deleted_at');
                                                        $q->whereNull('free_stock.deleted_at');
                                                    })
                                                    ->whereBetween('free_stock.tanggal', [$tanggal1, $tanggal2]);
        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();

        $length                         = intval($request->length);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($request->start);
        $draw                           = intval($request->draw);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ""){
            $cloneQuery2                = clone $sql;
            $query                      = $cloneQuery2
                                            ->where(function($filter) use ($search) {
                                                $filter->orWhere('chiller.asal_tujuan', 'like', '%' . $search . '%');
                                                $filter->orWhere('chiller.item_name', 'like', '%' . $search . '%');
                                                $filter->orWhere('chiller.qty_item', 'like', '%' . $search . '%');
                                                $filter->orWhere('chiller.berat_item', 'like', '%' . $search . '%');
                                                $filter->orWhere('free_stocklist.qty', 'like', '%' . $search . '%');
                                                $filter->orWhere('free_stocklist.berat', 'like', '%' . $search . '%');
                                                $filter->orWhere('free_stocklist.bb_kondisi', 'like', '%' . $search . '%');
                                                $filter->orWhere('free_stocklist.regu', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            $no = $start + 1;
            foreach ($query as $val) {
                // if($val->netsuite_send == '0'){
                //     $tipe   = "<span class='badge badge-danger'>Non WO</span>";
                // }else{
                //     $tipe   = "<span class='badge badge-primary'>Pakai WO</span>";
                // }
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_produksi,
                        $val->tanggal_free,
                        $val->asal_tujuan,
                        $val->regu,
                        $val->item_name,
                        number_format($val->qty_item),
                        number_format($val->berat_item),
                        number_format($val->qtyambil),
                        number_format($val->beratambil),
                        $val->jenis_bb,
                        // $tipe
                    );
                $no++;
            }
            $cloneQuery3            = clone $sql;
            $rows                   = $cloneQuery3->where(function($filter) use ($search) {
                                            $filter->orWhere('chiller.asal_tujuan', 'like', '%' . $search . '%');
                                            $filter->orWhere('chiller.item_name', 'like', '%' . $search . '%');
                                            $filter->orWhere('chiller.qty_item', 'like', '%' . $search . '%');
                                            $filter->orWhere('chiller.berat_item', 'like', '%' . $search . '%');
                                            $filter->orWhere('free_stocklist.qty', 'like', '%' . $search . '%');
                                            $filter->orWhere('free_stocklist.berat', 'like', '%' . $search . '%');
                                            $filter->orWhere('free_stocklist.bb_kondisi', 'like', '%' . $search . '%');
                                            $filter->orWhere('free_stocklist.regu', 'like', '%' . $search . '%');
                                        })
                                        ->get();
            $output['draw']                 = $draw;
            $output['recordsTotal']         = $output['recordsFiltered']      = $rows->count();
        }
        else{
            $cloneQuery4                    = clone $sql;
            $query                          = $cloneQuery4->take($length)->skip($start)->get();
            $no = $start + 1;
            foreach ($query as $val) {
                // if($val->netsuite_send == '0'){
                //     $tipe   = "<span class='badge badge-danger'>Non WO</span>";
                // }else{
                //     $tipe   = "<span class='badge badge-primary'>Pakai WO</span>";
                // }
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_produksi,
                        $val->tanggal_free,
                        $val->asal_tujuan,
                        $val->regu,
                        $val->item_name,
                        $val->qty_item,
                        $val->berat_item,
                        $val->qtyambil,
                        $val->beratambil,
                        $val->jenis_bb,
                        // $tipe
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function showDataTableSiapKirim(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = Order::join('order_items','orders.id','order_items.order_id')
                                                    ->join('items','order_items.item_id','items.id')
                                                    ->join('category','items.category_id','category.id')
                                                    ->select(
                                                        'order_items.id AS order_item_id',
                                                        'orders.no_so AS no_so',
                                                        'orders.no_do AS no_do',
                                                        'orders.nama AS nama',
                                                        'orders.sales_channel AS sales_channel',
                                                        'orders.tanggal_so AS tanggal_so',
                                                        'orders.tanggal_kirim AS tanggal_kirim',
                                                        'orders.keterangan AS keterangan_header',
                                                        'items.sku AS sku',
                                                        'items.nama AS item',
                                                        'order_items.part AS part',
                                                        'category.nama AS kategori_item',
                                                        'order_items.bumbu AS bumbu',
                                                        'order_items.memo AS memo',
                                                        'order_items.keterangan AS keterangan_line',
                                                        'order_items.qty AS qty',
                                                        'order_items.berat AS berat',
                                                        'order_items.fulfillment_qty AS fulfillment_qty',
                                                        'order_items.fulfillment_berat AS fulfillment_berat',
                                                        'order_items.tidak_terkirim_catatan AS tidak_terkirim_catatan',
                                                        'order_items.retur_qty AS retur_qty',
                                                        'order_items.retur_berat AS retur_berat',
                                                        'order_items.status AS statusitem'
                                                    )
                                                    ->whereBetween('orders.tanggal_kirim', [$tanggal1, $tanggal2])
                                                    ->orderBy('orders.no_so','ASC');
        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();

        $length                         = intval($_REQUEST['length']);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($_REQUEST['start']);
        $draw                           = intval($_REQUEST['draw']);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ''){
            $cloneQuery2                = clone $sql;
            $query                      = $cloneQuery2->where(function($filter) use ($search) {
                                                $filter->orWhere('orders.no_so', 'like', '%' . $search . '%');
                                                $filter->orWhere('orders.no_do', 'like', '%' . $search . '%');
                                                $filter->orWhere('orders.nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('orders.sales_channel', 'like', '%' . $search . '%');
                                                $filter->orWhere('orders.keterangan', 'like', '%' . $search . '%');
                                                $filter->orWhere('items.sku', 'like', '%' . $search . '%');
                                                $filter->orWhere('items.nama', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->no_so,
                        $val->no_do,
                        $val->nama,
                        $val->sales_channel,
                        $val->kategori_item,
                        $val->tanggal_so,
                        $val->tanggal_kirim,
                        $val->keterangan_header,
                        $val->sku,
                        $val->item,
                        $val->part,
                        $val->bumbu,
                        $val->keterangan_line,
                        $val->fulfillment_qty,
                        $val->fulfillment_berat,
                        $val->tidak_terkirim_catatan,
                        $val->retur_qty,
                        $val->retur_berat
                    );
                $no++;
            }
            $cloneQuery3                = clone $sql;
            $rows                       = $cloneQuery3->where(function($filter) use ($search) {
                                                $filter->orWhere('orders.no_so', 'like', '%' . $search . '%');
                                                $filter->orWhere('orders.no_do', 'like', '%' . $search . '%');
                                                $filter->orWhere('orders.nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('orders.sales_channel', 'like', '%' . $search . '%');
                                                $filter->orWhere('orders.keterangan', 'like', '%' . $search . '%');
                                                $filter->orWhere('items.sku', 'like', '%' . $search . '%');
                                                $filter->orWhere('items.nama', 'like', '%' . $search . '%');
                                            })
                                            ->get();

            $output['draw']             = $draw;
            $output['recordsTotal']     = $output['recordsFiltered']      = $rows->count();
        }
        else{
            $cloneQuery4                = clone $sql;
            $query                      = $cloneQuery4->take($length)->skip($start)->get();
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->no_so,
                        $val->no_do,
                        $val->nama,
                        $val->sales_channel,
                        $val->kategori_item,
                        $val->tanggal_so,
                        $val->tanggal_kirim,
                        $val->keterangan_header,
                        $val->sku,
                        $val->item,
                        $val->part,
                        $val->bumbu,
                        $val->keterangan_line,
                        $val->fulfillment_qty,
                        $val->fulfillment_berat,
                        $val->tidak_terkirim_catatan,
                        $val->retur_qty,
                        $val->retur_berat
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function showDataTableSisaChiller(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = Chiller::select('item_id','item_name','jenis','type','tanggal_produksi','asal_tujuan',
                                                DB::raw('ROUND(SUM(`chiller`.`qty_item`),2) AS `qty` '),
                                                DB::raw('ROUND(SUM(`chiller`.`berat_item`),2) AS `berat`'),
                                                DB::raw('ROUND(SUM(`chiller`.`stock_item`),2) AS `stock_qty`'),
                                                DB::raw('ROUND(SUM(`chiller`.`stock_berat`),2) AS `stock_berat`')
                                            )
                                            ->where('jenis','masuk')
                                            ->whereIn('type',['hasil-produksi','bahan-baku'])
                                            ->whereIn('asal_tujuan',['gradinggabungan','evisgabungan','retur','thawing','free_stock','open_balance','hasilbeli','abfbeli'])
                                            ->whereBetween('tanggal_produksi', [$tanggal1, $tanggal2])
                                            ->groupBy('item_id','item_name','jenis','type','tanggal_produksi','asal_tujuan')
                                            ->orderBy('item_id','DESC');

        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();
        
        $length                         = intval($_REQUEST['length']);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($_REQUEST['start']);
        $draw                           = intval($_REQUEST['draw']);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ''){
            $cloneQuery2                = clone $sql;
            $query                      = $cloneQuery2->where(function($filter) use ($search) {
                                                $filter->orWhere('asal_tujuan', 'like', '%' . $search . '%');
                                                $filter->orWhere('item_name', 'like', '%' . $search . '%');
                                                $filter->orWhere('type', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            
           
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_produksi,
                        $val->asal_tujuan,
                        $val->type,
                        $val->item_name,
                        $val->qty,
                        $val->berat,
                        $val->stock_qty,
                        $val->stock_berat
                    );
                $no++;
            }

            $cloneQuery3                    = clone $sql;
            $rows                           = $cloneQuery3->where(function($filter) use ($search) {
                                                    $filter->orWhere('asal_tujuan', 'like', '%' . $search . '%');
                                                    $filter->orWhere('item_name', 'like', '%' . $search . '%');
                                                    $filter->orWhere('type', 'like', '%' . $search . '%');
                                                })
                                                ->get();           

            $output['draw']                 = $draw;
            $output['recordsTotal']         = $output['recordsFiltered']      = $rows->count();
        }
        else{
            $cloneQuery4                    = clone $sql;
            $query                          = $cloneQuery4->take($length)->skip($start)->get();
            
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->tanggal_produksi,
                        $val->asal_tujuan,
                        $val->type,
                        $val->item_name,
                        $val->qty,
                        $val->berat,
                        $val->stock_qty,
                        $val->stock_berat
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function showDataTableAbf(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = Abf::where('jenis', 'masuk')->whereBetween('created_at', [$tanggal1 . " 00:00:00", $tanggal2 . " 23:59:59"])->where('status', '1');
        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();

        $length                         = intval($_REQUEST['length']);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($_REQUEST['start']);
        $draw                           = intval($_REQUEST['draw']);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ''){
            $cloneQuery2                = clone $sql;
            $query                      = $cloneQuery2->where(function($filter) use ($search) {
                                                $filter->orWhere('item_name', 'like', '%' . $search . '%');
                                                $filter->orWhere('packaging', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            $no = $start + 1;
            foreach ($query as $val) {
                if ($val->table_name == 'chiller'){
                    $exp            = json_decode($val->abf_chiller->label ?? false);
                    $br             = "<br>";
                    $tunggir        = '';
                    $lemak          = '';
                    $maras          = '';
                    $subitem        = '';
                    $parting        = '';
                    $plastikjenis   = '';
                    $plastikqty     = '';

                    if($exp){
                        if($exp->plastik ?? ''){
                            $plastikjenis   = $exp->plastik->jenis ?? '';
                            $plastikqty     = $exp->plastik->qty ?? '';
                        }
                        if($exp->additional ?? ''){
                            $tunggir = $exp->additional->tunggir ? 'Tanpa Tunggir, ' : ''; 
                            $lemak   = $exp->additional->lemak ? 'Tanpa Lemak, ' : '' ;  
                            $maras   = $exp->additional->maras ? 'Tanpa Maras' : '' ;
                        }
                        if($exp->sub_item ?? false){
                            $subitem = "Customer : ".$exp->sub_item ?? '';
                        } 
                        if($exp->parting->qty ?? ''){
                            $parting = "Parting : ".$exp->parting->qty;
                        }
                        
                    }
                    $labeling   = "<div class='status status-success'>
                                        <div class='row'>
                                            <div class='col pr-1'>
                                                ".$plastikjenis."
                                            </div>
                                            <div class='col-auto pl-1'>
                                                <span class='float-right'>// ".$plastikqty." Pcs</span>
                                            </div>
                                        </div>
                                    </div>"
                                    .$br
                                    .$tunggir
                                    .$lemak
                                    .$maras.                                        
                                    "<div class='row mt-1 text-info'>
                                        <div class='col pr-1'>".$subitem."</div>
                                        <div class='col-auto pl-1'>".$parting."</div>
                                    </div>";
                }

                if ($val->table_name == 'free_stocktemp'){
                        $exp            = json_decode($val->abf_freetemp->label ?? false);
                        $br             = "<br>";
                        $tunggir        = '';
                        $lemak          = '';
                        $maras          = '';
                        $subitem        = '';
                        $parting        = '';
                        $plastikjenis   = '';
                        $plastikqty     = '';
                        if ($exp){
                            $br = "<br>";
                            if($exp->plastik ?? ''){
                                $plastikjenis   = $exp->jenis ?? '';
                                $plastikqty     = $exp->qty ?? '';
                            }
                            if ($exp->additional ?? ''){
                                $tunggir = $exp->additional->tunggir ? 'Tanpa Tunggir, ' : ''; 
                                $lemak   = $exp->additional->lemak ? 'Tanpa Lemak, ' : '' ;  
                                $maras   = $exp->additional->maras ? 'Tanpa Maras' : '' ;
                            }
                            if($exp->sub_item ?? ''){
                                $subitem = "Customer : ".$exp->sub_item ?? '';
                            } 
                            if($exp->parting->qty ?? ''){
                                $parting = "Parting : ".$exp->parting->qty;
                            }
                        }
                        $labeling   = "<div class='status status-success'>
                                            <div class='row'>
                                                <div class='col pr-1'>
                                                    ".$plastikjenis."
                                                </div>
                                                <div class='col-auto pl-1'>
                                                    <span class='float-right'>// " .$plastikqty ." Pcs</span>
                                                </div>
                                            </div>
                                        </div>"
                                        .$br
                                        .$tunggir
                                        .$lemak
                                        .$maras.
                                        "<div class='row mt-1 text-info'>
                                            <div class='col pr-1'>".$subitem."</div>
                                            <div class='col-auto pl-1'>".$parting."</div>
                                        </div>";
                }

                            
                $output['data'][] =
                    array(
                        $no,
                        $val->item_name. $labeling,
                        $val->packaging,
                        date('d/m/Y', strtotime($val->created_at)),
                        number_format($val->qty_item > 0 ? $val->qty_item : '0'),
                        number_format($val->qty_item > 0 ? $val->qty_item : '0',2)
                    );
                $no++;
            }
            $cloneQuery3                = clone $sql;
            $rows                       = $cloneQuery3->where(function($filter) use ($search) {
                                                $filter->orWhere('item_name', 'like', '%' . $search . '%');
                                                $filter->orWhere('packaging', 'like', '%' . $search . '%');
                                            })
                                            ->get();

            $output['draw']             = $draw;
            $output['recordsTotal']     = $output['recordsFiltered']      = $rows->count();
        }
        else{
            $cloneQuery4                = clone $sql;
            $query                      = $cloneQuery4->take($length)->skip($start)->get();
            $no = $start + 1;
            foreach ($query as $val) {
                if ($val->table_name == 'chiller'){
                    $exp            = json_decode($val->abf_chiller->label ?? false);
                    $br             = "<br>";
                    $tunggir        = '';
                    $lemak          = '';
                    $maras          = '';
                    $subitem        = '';
                    $parting        = '';
                    $plastikjenis   = '';
                    $plastikqty     = '';

                    if($exp){
                        if($exp->plastik ?? ''){
                            $plastikjenis   = $exp->plastik->jenis ?? '';
                            $plastikqty     = $exp->plastik->qty ?? '';
                        }
                        if($exp->additional ?? ''){
                            $tunggir = $exp->additional->tunggir ? 'Tanpa Tunggir, ' : ''; 
                            $lemak   = $exp->additional->lemak ? 'Tanpa Lemak, ' : '' ;  
                            $maras   = $exp->additional->maras ? 'Tanpa Maras' : '' ;
                        }
                        if($exp->sub_item ?? false){
                            $subitem = "Customer : ".$exp->sub_item ?? '';
                        } 
                        if($exp->parting->qty ?? ''){
                            $parting = "Parting : ".$exp->parting->qty;
                        }
                        
                    }
                    $labeling   = "<div class='status status-success'>
                                        <div class='row'>
                                            <div class='col pr-1'>
                                                ".$plastikjenis."
                                            </div>
                                            <div class='col-auto pl-1'>
                                                <span class='float-right'>// ".$plastikqty." Pcs</span>
                                            </div>
                                        </div>
                                    </div>"
                                    .$br
                                    .$tunggir
                                    .$lemak
                                    .$maras.                                        
                                    "<div class='row mt-1 text-info'>
                                        <div class='col pr-1'>".$subitem."</div>
                                        <div class='col-auto pl-1'>".$parting."</div>
                                    </div>";
                }

                if ($val->table_name == 'free_stocktemp'){
                        $exp            = json_decode($val->abf_freetemp->label ?? false);
                        $br             = "<br>";
                        $tunggir        = '';
                        $lemak          = '';
                        $maras          = '';
                        $subitem        = '';
                        $parting        = '';
                        $plastikjenis   = '';
                        $plastikqty     = '';
                        if ($exp){
                            $br = "<br>";
                            if($exp->plastik ?? ''){
                                $plastikjenis   = $exp->jenis ?? '';
                                $plastikqty     = $exp->qty ?? '';
                            }
                            if ($exp->additional ?? ''){
                                $tunggir = $exp->additional->tunggir ? 'Tanpa Tunggir, ' : ''; 
                                $lemak   = $exp->additional->lemak ? 'Tanpa Lemak, ' : '' ;  
                                $maras   = $exp->additional->maras ? 'Tanpa Maras' : '' ;
                            }
                            if($exp->sub_item ?? ''){
                                $subitem = "Customer : ".$exp->sub_item ?? '';
                            } 
                            if($exp->parting->qty ?? ''){
                                $parting = "Parting : ".$exp->parting->qty;
                            }
                        }
                        $labeling   = "<div class='status status-success'>
                                            <div class='row'>
                                                <div class='col pr-1'>
                                                    ".$plastikjenis."
                                                </div>
                                                <div class='col-auto pl-1'>
                                                    <span class='float-right'>// " .$plastikqty ." Pcs</span>
                                                </div>
                                            </div>
                                        </div>"
                                        .$br
                                        .$tunggir
                                        .$lemak
                                        .$maras.
                                        "<div class='row mt-1 text-info'>
                                            <div class='col pr-1'>".$subitem."</div>
                                            <div class='col-auto pl-1'>".$parting."</div>
                                        </div>";
                }
                $output['data'][] =
                    array(
                        $no,
                        $val->item_name . $labeling,
                        $val->packaging,
                        date('d/m/Y', strtotime($val->created_at)),
                        number_format($val->qty_item > 0 ? $val->qty_item : '0'),
                        number_format($val->qty_item > 0 ? $val->qty_item : '0',2)
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function showDataTableGudang(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = Product_gudang::where('table_name', 'abf')
                                            ->where('berat', '!=', NULL)
                                            ->whereBetween('created_at', [$tanggal1 . ' 00:00:00', $tanggal2 . ' 23:59:59'])
                                            ->where('status', '!=', 0)
                                            ->orderBy('id', 'DESC');
        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();

        $length                         = intval($_REQUEST['length']);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($_REQUEST['start']);
        $draw                           = intval($_REQUEST['draw']);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ''){
        $cloneQuery2                    = clone $sql;
        $query                          = $cloneQuery2->where(function($filter) use ($search) {
                                                $filter->orWhere('nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('packaging', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            $no = $start + 1;
            foreach ($query as $row) {
                $output['data'][] =
                    array(
                        $no,
                        $row->productgudang->code ?? '',
                        $row->sub_item ?? '',
                        $row->productitems->sku ?? '',
                        $row->productitems->nama ?? '',
                        $row->packaging,
                        date('Y-m-d', strtotime($row->production_date)),
                        number_format($row->qty),
                        number_format($row->berat_timbang, 2),
                        number_format($row->berat, 2),
                        number_format($row->palete),
                        number_format($row->expired) ." Bulan",
                        $row->stock_type,
                        $row->jenis_trans
                    );
                $no++;
            }
            $cloneQuery3                = clone $sql;
            $rows                       = $cloneQuery3->where(function($filter) use ($search) {
                                                $filter->orWhere('nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('packaging', 'like', '%' . $search . '%');
                                            })
                                            ->get();

            $output['draw']             = $draw;
            $output['recordsTotal']     = $output['recordsFiltered']      = $rows->count();
        }
        else{
            $cloneQuery4                = clone $sql;
            $query                      = $cloneQuery4->take($length)->skip($start)->get();
            $no = $start + 1;
            foreach ($query as $row) {
                $output['data'][] =
                    array(
                        $no,
                        $row->productgudang->code ?? '',
                        $row->sub_item ?? '',
                        $row->productitems->sku ?? '',
                        $row->productitems->nama ?? '',
                        $row->packaging,
                        date('Y-m-d', strtotime($row->production_date)),
                        number_format($row->qty),
                        number_format($row->berat_timbang, 2),
                        number_format($row->berat, 2),
                        number_format($row->palete),
                        number_format($row->expired) ." Bulan",
                        $row->stock_type,
                        $row->jenis_trans
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function showDataTableRetur(Request $request){
        $tanggal1                       = $request->tglmulai ?? date('Y-m-d');
        $tanggal2                       = $request->tglselesai ?? date('Y-m-d');
        $sql                            = Retur::leftJoin('netsuite','retur.id','netsuite.tabel_id')
                                                ->Join('retur_item','retur_item.retur_id','retur.id')
                                                ->Join('items','retur_item.item_id','items.id')
                                                ->Join('customers','customers.id','retur.customer_id')
                                                ->select(
                                                    'customers.id AS customer_id',
                                                    'customers.nama AS nama',
                                                    'retur.no_so AS no_so',
                                                    'retur.tanggal_retur AS tanggal_retur',
                                                    'items.nama AS item',
                                                    'retur_item.qty AS qty',
                                                    'retur_item.berat AS berat',
                                                    'retur_item.tujuan AS tujuan',
                                                    'retur_item.kategori AS kategori',
                                                    'retur_item.unit AS unit',
                                                    'retur_item.penanganan AS penanganan',
                                                    'retur_item.catatan AS catatan',
                                                    'netsuite.response AS response'
                                                )
                                                ->where('retur.status',2)
                                                ->where('netsuite.record_type','receipt_return')
                                                ->whereBetween('retur.tanggal_retur', [$tanggal1, $tanggal2]);
        $cloneQuery1                    = clone $sql;
        $total                          = $cloneQuery1->get()->count();

        $length                         = intval($_REQUEST['length']);
        $length                         = $length < 0 ? $total : $length;
        $start                          = intval($_REQUEST['start']);
        $draw                           = intval($_REQUEST['draw']);

        $search                         = $_REQUEST['search']["value"];

        $output                         = array();
        $output['data']                 = array();

        $end                            = $start + $length;
        $end                            = $end > $total ? $total : $end;

        if($search != ''){
        $cloneQuery2                    = clone $sql;
        $query                          = $cloneQuery2->where(function($filter) use ($search) {
                                                $filter->orWhere('customers.nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('retur.no_so', 'like', '%' . $search . '%');
                                                $filter->orWhere('retur.tanggal_retur', 'like', '%' . $search . '%');
                                                $filter->orWhere('items.nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('retur_item.catatan', 'like', '%' . $search . '%');
                                            })
                                            ->take($length)
                                            ->skip($start)
                                            ->get();
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->nama,
                        $val->item,
                        $val->tanggal_retur,
                        $val->qty,
                        $val->berat,
                        $val->tujuan,
                        $val->kategori,
                        $val->catatan
                    );
                $no++;
            }
            $cloneQuery3                = clone $sql;
            $rows                       = $cloneQuery3->where(function($filter) use ($search) {
                                                $filter->orWhere('customers.nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('retur.no_so', 'like', '%' . $search . '%');
                                                $filter->orWhere('retur.tanggal_retur', 'like', '%' . $search . '%');
                                                $filter->orWhere('items.nama', 'like', '%' . $search . '%');
                                                $filter->orWhere('retur_item.catatan', 'like', '%' . $search . '%');
                                            })
                                            ->get();

            $output['draw']                 = $draw;
            $output['recordsTotal']         = $output['recordsFiltered']      = $rows->count();
        }
        else{
            $cloneQuery4                    = clone $sql;
            $query                          = $cloneQuery4->take($length)->skip($start)->get();
            $no = $start + 1;
            foreach ($query as $val) {
                $output['data'][] =
                    array(
                        $no,
                        $val->nama,
                        $val->item,
                        $val->tanggal_retur,
                        $val->qty,
                        $val->berat,
                        $val->tujuan,
                        $val->kategori,
                        $val->catatan
                    );
                $no++;
            }
            $output['draw']             = $draw;
            $output['recordsTotal']     = $total;
            $output['recordsFiltered']  = $total;
        }

        return response()->json($output);
    }

    public function datastock(Request $request){
        $mulai                  =   $request->mulai ?? date("Y-m-d");
        $akhir                  =   $request->akhir ?? date("Y-m-d");

        if($request->key == 'view_page'){
            $mulai              = $request->mulai;
            $akhir              = $request->akhir;

            if($request->loadkarkas == 'YES'){
                $bbkarkas               = Chiller::getDataMasterBahanBakuKarkas();
                $karkasbarunormal       = Chiller::getAllDataBahanBaku($akhir,$akhir,'KARKAS','NORMAL','');
                $karkasbarumemar        = Chiller::getAllDataBahanBaku($akhir,$akhir,'KARKAS','','MEMAR');
                $karkaslamanormal       = Chiller::getAllDataBahanBaku($mulai,date('Y-m-d', strtotime($akhir ."-1 days")),'KARKAS','NORMAL','');
                $karkaslamamemar        = Chiller::getAllDataBahanBaku($mulai,date('Y-m-d', strtotime($akhir .'-1 days')),'KARKAS','','MEMAR');
                $ayamutuhbarunormal     = Chiller::getAllDataBahanBaku($akhir,$akhir,'AYAM UTUH','NORMAL','');
                $ayamutuhbarumemar      = Chiller::getAllDataBahanBaku($akhir,$akhir,'AYAM UTUH','','MEMAR');
                $ayamutuhlamanormal     = Chiller::getAllDataBahanBaku($mulai,date('Y-m-d', strtotime($akhir ."-1 days")),'AYAM UTUH','NORMAL','');
                $ayamutuhlamamemar      = Chiller::getAllDataBahanBaku($mulai,date('Y-m-d', strtotime($akhir .'-1 days')),'AYAM UTUH','','MEMAR');

                // $databbkarkas   = array();
                foreach($bbkarkas as $value){
                    $qty_baru       = 0;
                    $berat_baru     = 0;
                    foreach($karkasbarunormal as $kkbaru){
                        if($value == substr($kkbaru['item_name'],-5)){
                            $qty_baru            = $kkbaru['stock_qty'];
                            $berat_baru          = $kkbaru['stock_berat'];
                        }

                    }

                    $qty_memar_baru       = 0;
                    $berat_memar_baru     = 0;
                    foreach($karkasbarumemar as $kkmemarbaru){
                        if($value == substr($kkmemarbaru['item_name'],-5)){
                            $qty_memar_baru            = $kkmemarbaru['stock_qty'];
                            $berat_memar_baru          = $kkmemarbaru['stock_berat'];
                        }

                    }

                    $qty_lama       = 0;
                    $berat_lama     = 0;
                    foreach($karkaslamanormal as $kklama){
                        if($value == substr($kklama['item_name'],-5)){
                            $qty_lama            = $kklama['stock_qty'];
                            $berat_lama          = $kklama['stock_berat'];
                        }

                    }

                    $qty_memar_lama       = 0;
                    $berat_memar_lama     = 0;
                    foreach($karkaslamamemar as $kkmemarlama){
                        if($value == substr($kkmemarlama['item_name'],-5)){
                            $qty_memar_lama           = $kkmemarlama['stock_qty'];
                            $berat_memar_lama         = $kkmemarlama['stock_berat'];
                        }

                    }

                    //AYAM UTUH
                    $qty_utuh_baru       = 0;
                    $berat_utuh_baru     = 0;
                    foreach($ayamutuhbarunormal as $utuhbaru){
                        if($value == substr($kkbaru['item_name'],-5)){
                            $qty_utuh_baru            = $utuhbaru['stock_qty'];
                            $berat_utuh_baru          = $utuhbaru['stock_berat'];
                        }

                    }

                    $qty_utuh_memar_baru       = 0;
                    $berat_utuh_memar_baru     = 0;
                    foreach($ayamutuhbarumemar as $utuhmemarbaru){
                        if($value == substr($utuhmemarbaru['item_name'],-5)){
                            $qty_utuh_memar_baru            = $utuhmemarbaru['stock_qty'];
                            $berat_utuh_memar_baru          = $utuhmemarbaru['stock_berat'];
                        }

                    }

                    $qty_utuh_lama       = 0;
                    $berat_utuh_lama     = 0;
                    foreach($ayamutuhlamanormal as $utuhlama){
                        if($value == substr($utuhlama['item_name'],-5)){
                            $qty_utuh_lama            = $utuhlama['stock_qty'];
                            $berat_utuh_lama          = $utuhlama['stock_berat'];
                        }

                    }

                    $qty_utuh_memar_lama       = 0;
                    $berat_utuh_memar_lama     = 0;
                    foreach($ayamutuhlamamemar as $utuhmemarlama){
                        if($value == substr($utuhmemarlama['item_name'],-5)){
                            $qty_utuh_memar_lama           = $utuhmemarlama['stock_qty'];
                            $berat_utuh_memar_lama         = $utuhmemarlama['stock_berat'];
                        }

                    }

                    $databbkarkas[]= array(
                        'namaitem'                  => $value,
                        'qty_baru'                  => $qty_baru,
                        'berat_baru'                => $berat_baru,
                        'qty_memar_baru'            => $qty_memar_baru,
                        'berat_memar_baru'          => $berat_memar_baru,
                        'qty_lama'                  => $qty_lama,
                        'berat_lama'                => $berat_lama,
                        'qty_memar_lama'            => $qty_memar_lama,
                        'berat_memar_lama'          => $berat_memar_lama,
                        'qty_utuh_baru'             => $qty_utuh_baru,
                        'berat_utuh_baru'           => $berat_utuh_baru,
                        'qty_utuh_memar_baru'       => $qty_utuh_memar_baru,
                        'berat_utuh_memar_baru'     => $berat_utuh_memar_baru,
                        'qty_utuh_lama'             => $qty_utuh_lama,
                        'berat_utuh_lama'           => $berat_utuh_lama,
                        'qty_utuh_memar_lama'       => $qty_utuh_memar_lama,
                        'berat_utuh_memar_lama'     => $berat_utuh_memar_lama,
                    );
                }
                $data = [
                    'datakarkas'                    => $databbkarkas,
                ];
                return view('admin.pages.laporan.datastock.component.view-data-karkas', compact('mulai', 'akhir','data'));
            }

            if($request->loadSampingan == 'YES'){
                $bbsamppingan           = Chiller::getDataMasterBahanBaku($mulai,$akhir,'sampingan',['bahan-baku','hasil-produksi','inventory-adjustment']);
                $databbsampingan        = array();
                foreach($bbsamppingan as $value){
                    $databbsampingan[]= array(
                        'namaitem'                  => $value->item_name,
                        'qty'                       => $value->stock_qty,
                        'berat'                     => $value->stock_berat,
                    );
                }

                $data = [
                    'datasampingan'                 => $databbsampingan
                ];
                return view('admin.pages.laporan.datastock.component.view-data-sampingan', compact('mulai', 'akhir','data'));
            }
            if($request->loadBoneless == 'YES'){
                $bbboneless           = Chiller::getDataMasterBahanBaku($mulai,$akhir,'boneless',['hasil-produksi']);
                $databbboneless        = array();
                foreach($bbboneless as $value){
                    $databbboneless[]= array(
                        'namaitem'                  => $value->item_name,
                        'qty'                       => $value->stock_qty,
                        'berat'                     => $value->stock_berat,
                    );
                }

                $data = [
                    'databoneless'                 => $databbboneless
                ];
                return view('admin.pages.laporan.datastock.component.view-data-boneless', compact('mulai', 'akhir','data'));
            }
            if($request->loadFulfillment == 'YES'){
                $bbfulfillment           = Chiller::getDataMasterBahanBakuFF($mulai,$akhir);
                $databbfulfillment       = array();
                foreach($bbfulfillment as $value){
                    $databbfulfillment[]= array(
                        'customer'                  => self::getNamaCustomer('customers',$value->customer_id,'nama'),
                        'namaitem'                  => $value->item_name,
                        'qty'                       => $value->stock_qty,
                        'berat'                     => $value->stock_berat,
                    );
                }
                // dd($databbfulfillment);

                $data = [
                    'datafulfillment'               => $databbfulfillment
                ];
                return view('admin.pages.laporan.datastock.component.view-data-fulfillment', compact('mulai', 'akhir','data'));
            }
            if($request->loadRetur == 'YES'){
                $bbretur           = Chiller::getDataMasterBahanBakuRetur($mulai,$akhir);
                $databbretur       = array();
                foreach($bbretur as $value){
                    $databbretur[]= array(
                        'customer'                  => $value->label,
                        'tanggal'                   => $value->tanggal_produksi,
                        'namaitem'                  => $value->item_name,
                        'qty'                       => $value->stock_qty,
                        'berat'                     => $value->stock_berat,
                    );
                }
                // dd($databbretur);

                $data = [
                    'dataretur'                     => $databbretur
                ];
                return view('admin.pages.laporan.datastock.component.view-data-retur', compact('mulai', 'akhir','data'));
            }
            if($request->loadOther == 'YES'){
                $bbother           = Chiller::getAllDataBahanOther($mulai,$akhir);
                $databbother       = array();
                foreach($bbother as $value){
                    $databbother[]= array(
                        'tanggal'                   => $value->tanggal_produksi,
                        'namaitem'                  => $value->item_name,
                        'qty'                       => $value->stock_qty,
                        'berat'                     => $value->stock_berat,
                    );
                }
                // dd($databbother);

                $data = [
                    'dataother'                     => $databbother
                ];
                return view('admin.pages.laporan.datastock.component.view-data-other', compact('mulai', 'akhir','data'));
            }
            return view('admin.pages.laporan.datastock.component.view-data-stock', compact('mulai', 'akhir'));
        }
        return view('admin.pages.laporan.datastock.stock', compact('mulai', 'akhir'));
    }

    public function export(Request $request)
    {
        //  return $request->all();
        $tanggal1    =   $request->tglmulai;
        $tanggal2    =   $request->tglselesai;

        if ($request->tujuan == 'lpah') {
            // $produksi   =   Production::whereIn('purchasing_id', Purchasing::select('id')->whereBetween('tanggal_potong', [$tanggal1, $tanggal2]))->where('sc_status', '1')->orderBy('no_urut', 'asc')->get();
            $lpah       =   Production::whereBetween('prod_tanggal_potong', [$tanggal1, $tanggal2])->where('lpah_status', '1')->orderBy('no_urut', 'asc')->get();
            $file = "lpah_" . $tanggal1 . "_" . $tanggal2 . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
            <table class="table default-table" width="100%" id="lpahTable">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>TANGGAL</th>
                    <th>MOBIL</th>
                    <th>SOPIR</th>
                    <th>No. LPAH</th>
                    <th>JAM MULAI</th>
                    <th>JAM SELESAI</th>
                    <th>EKOR DO</th>
                    <th>KG DO</th>
                    <th>RATA" DO</th>
                    <th>EKOR SACKLE</th>
                    <th>KG TIMBANGAN</th>
                    <th>RATA" TIMBANGAN</th>
                    <th>KEMATIAN</th>
                    <th>AYAM SAKIT</th>
                    <th>TEMBOLOK</th>
                    <th>KEBERSIHAN KERANJANG</th>
                    <th>SUSUT AYAM</th>
                    <th>BASAH</th>
                    <th>DOWN TIME</th>
                </tr>
            </thead>';

            foreach ($lpah as $i => $val) {
                $html .= '
                <tr>
                <td>' . ++$i . '</td>
                <td>' . $val->prod_tanggal_potong . '</td>
                <td>' . $val->no_urut . ' (' . $val->prodpur->purcsupp->nama . ') ' . '</td>
                <td>' . $val->sc_pengemudi . '</td>
                <td>' . $val->no_lpah . '</td>
                <td>' . $val->sc_jam_masuk . '</td>
                <td>' . $val->lpah_jam_bongkar . '</td>
                <td>' . number_format($val->sc_ekor_do, 0) . ' Ekor' . '</td>
                <td>' . number_format($val->sc_berat_do, 2) . ' Kg'  .'</td>
                <td>' . $val->sc_rerata_do . ' Kg' .'</td>
                <td>' . number_format($val->ekoran_seckle, 0) . ' Ekor' .'</td>
                <td>' . number_format($val->lpah_berat_terima, 2) . ' Kg' . '</td>
                <td>' . $val->lpah_rerata_terima . ' Kg' . '</td>
                <td>' . number_format($val->qc_ekor_ayam_mati, 0) . ' Ekor' . ' / ' . $val->qc_berat_ayam_mati . ' Kg' .'</td>
                <td>' . $val->qc_ekor_ayam_merah . ' E' .  ' / ' . $val->qc_berat_ayam_merah . ' Kg' . '</td>
                <td>' . $val->qc_tembolok . ' Kg' . '</td>
                <td>' . $val->lpah_kebersihan_keranjang . '</td>
                <td>' . $val->lpah_persen_susut . ' %' . '</td>
                <td>' . $val->lpah_basah . ' %' . '</td>
                <td>' . $val->lpah_downtime . ' Menit' . '</td>
                </tr>
                ';
            }
        } elseif ($request->tujuan == 'evis') {
            $evisbb         = Evis::leftJoin('productions','evis.production_id','productions.id')
                                    ->leftJoin('purchasing','productions.purchasing_id','purchasing.id')
                                    ->leftJoin('items','evis.item_id','items.id')
                                    ->leftJoin('supplier','purchasing.supplier_id','supplier.id')
                                    ->select(
                                            'purchasing.no_po AS no_po',
                                            'productions.no_do AS no_do',
                                            'productions.sc_pengemudi AS sc_pengemudi',
                                            'productions.sc_jam_masuk AS sc_jam_masuk',
                                            'productions.no_lpah AS no_lpah',
                                            'productions.no_urut AS no_urut',
                                            'productions.lpah_jam_bongkar AS lpah_jam_bongkar',
                                            'evis.tanggal_potong AS tanggal_potong',
                                            'evis.item_id AS item_id',
                                            'items.nama AS nama',
                                            'evis.total_item AS total_item',
                                            'evis.berat_item AS berat_item',
                                            'supplier.nama AS supplier'
                                    )
                                    ->whereBetween('evis.tanggal_potong', [$tanggal1, $tanggal2])
                                    ->get();

            $file = "evis_" . $tanggal1 . "_" . $tanggal2 . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
                <table class="table default-table" id="export-table-lpah">
                    <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>FARM</th>
                        <th>MOBIL</th>
                        <th>No. DO</th>
                        <th>DRIVER</th>
                        <th>JAM MASUK</th>
                        <th>JAM BONGKAR</th>
                        <th>ITEM</th>
                        <th>Qty</th>
                        <th>BERAT</th>
                    </tr>
                    </thead>';
            foreach ($evisbb as $i => $val) {
                $html .= '
                        <tr>
                        <td>' . ++$i . '</td>
                        <td>' . $val->tanggal_potong . '</td>
                        <td>' . $val->supplier . '</td>
                        <td>' . $val->no_urut . '</td>
                        <td>' . $val->no_do . '</td>
                        <td>' . $val->sc_pengemudi . '</td>
                        <td>' . $val->sc_jam_masuk . '</td>
                        <td>' . $val->lpah_jam_bongkar . '</td>
                        <td>' . $val->nama . '</td>
                        <td class="text-right">' . $val->total_item . '</td>
                        <td class="text-right">' . $val->berat_item . '</td>
                    </tr>
                        ';
            }
        } elseif ($request->tujuan == 'grading') {
            $grading        =   Grading::leftJoin('productions','grading.trans_id','productions.id')
                                ->leftJoin('purchasing','productions.purchasing_id','purchasing.id')
                                ->leftJoin('items','grading.item_id','items.id')
                                ->leftJoin('supplier','purchasing.supplier_id','supplier.id')
                                ->select(
                                    'productions.sc_pengemudi AS sc_pengemudi',
                                    'purchasing.type_po AS type_po',
                                    'purchasing.no_po AS no_po',
                                    'grading.item_id AS item_id',
                                    'items.nama AS nama',
                                    'productions.sc_nama_kandang AS sc_nama_kandang',
                                    'productions.sc_tanggal_masuk AS sc_tanggal_masuk',
                                    'productions.sc_jam_masuk AS sc_jam_masuk',
                                    'grading.total_item AS total_item',
                                    'grading.berat_item AS berat_item',
                                    'purchasing.tanggal_potong AS tanggal_potong',
                                    'productions.no_urut AS no_urut',
                                    'productions.no_lpah AS no_lpah',
                                    'productions.no_do AS no_do',
                                    'productions.lpah_jam_bongkar AS lpah_jam_bongkar',
                                    'supplier.nama AS supplier'
                                )
                                ->whereBetween('grading.tanggal_potong', [$tanggal1, $tanggal2])
                                ->whereNull('grading.deleted_at')
                                ->get();

            $file = "grading_" . $tanggal1 . "_" . $tanggal2 . date('y-m-d-h_i_s') . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
                <table class="table default-table" id="export-table-lpah">
                    <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>FARM</th>
                        <th>MOBIL</th>
                        <th>No. DO</th>
                        <th>DRIVER</th>
                        <th>JAM MASUK</th>
                        <th>JAM BONGKAR</th>
                        <th>ITEM</th>
                        <th>Qty</th>
                        <th>BERAT</th>
                    </tr>
                    </thead>';

            foreach ($grading as $i => $val) {
                $html .= '
                    <tr>
                        <td>' . ++$i . '</td>
                        <td>' . $val->tanggal_potong . '</td>
                        <td>' . $val->supplier . '</td>
                        <td>' . $val->no_urut . '</td>
                        <td>' . $val->no_do . '</td>
                        <td>' . $val->sc_pengemudi . '</td>
                        <td>' . $val->sc_jam_masuk . '</td>
                        <td>' . $val->lpah_jam_bongkar . '</td>
                        <td>' . $val->nama . '</td>
                        <td class="text-right">' . $val->total_item . '</td>
                        <td class="text-right">' . $val->berat_item . '</td>
                    </tr>
                    ';
            }
        } elseif ($request->tujuan == 'hasilproduksi') {
            $hasilproduksi      = FreestockTemp::leftJoin('free_stock','free_stocktemp.freestock_id','free_stock.id')
                                                ->select(
                                                    'free_stocktemp.item_id AS item_id',
                                                    'free_stocktemp.prod_nama AS item_name',
                                                    'free_stocktemp.regu AS regu',
                                                    'free_stocktemp.qty AS qty',
                                                    'free_stocktemp.berat AS berat',
                                                    'free_stocktemp.id AS table_id',
                                                    'free_stocktemp.deleted_at AS deleted_at',
                                                    'free_stock.tanggal AS tanggal_produksi'
                                                )
                                                ->where(function($q){
                                                    $q->whereNull('free_stock.deleted_at');
                                                    $q->whereNull('free_stocktemp.deleted_at');
                                                    $q->where('free_stock.status',3);
                                                })
                                                ->whereBetween('tanggal_produksi', [$tanggal1, $tanggal2])
                                                ->orderBy('free_stocktemp.id','DESC')
                                                ->get();

            $file = "hasilproduksi_" . $tanggal1 . "_" . $tanggal2 . date('y-m-d-h_i_s') . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
            <table class="table default-table" id="export-table-lpah">
                <thead>
                    <tr class="text-center">
                    <th>No</th>
                    <th>TANGGAL</th>
                    <th>REGU</th>
                    <th>ITEM</th>
                    <th>Qty</th>
                    <th>BERAT</th>
                    <th>#</th>
                    </tr>
                </thead>
            ';

            foreach ($hasilproduksi as $i => $val) {
                // if($val->table_name == 'free_stocktemp'){
                    // $getFSId        = FreestockTemp::getfreestockid($val->table_id);
                    // if($getFSId){
                    //     $ns_send    = Freestock::getNetsuiteSend($getFSId);
                    //     if($ns_send == '0'){
                    //         $tipe   = "Non WO";
                    //     }else{
                    //         $tipe   = "Pakai WO";
                    //     }
                    // }
                // }else{
                    // $tipe           = '';
                // }
                $html .= '
                <tr>
                <td>' . ++$i . '</td>
                <td>' . $val->tanggal_produksi . '</td>
                <td>' . $val->regu . '</td>
                <td>' . $val->item_name . '</td>
                <td class="text-right">' . $val->qty . '</td>
                <td class="text-right">' . $val->berat . '</td>
            </tr>
                ';
            }
        } elseif ($request->tujuan == 'ambilbb') {
            $ambilbb  =   Chiller::leftJoin('free_stocklist','chiller.id','free_stocklist.outchiller')
                                        ->leftJoin('free_stock','free_stocklist.freestock_id','free_stock.id')
                                        // ->leftJoin('chiller as d','d.id','free_stocklist.chiller_id')
                                        ->select(
                                            'chiller.item_name AS item_name',
                                            'chiller.asal_tujuan AS asal_tujuan',
                                            'chiller.tanggal_produksi AS tanggal_produksi',
                                            'chiller.id AS chiller_id',
                                            'free_stocklist.outchiller AS idout',
                                            'chiller.qty_item AS qty_item',
                                            'chiller.berat_item AS berat_item',
                                            'free_stocklist.item_id AS item_id',
                                            'free_stocklist.id AS idoutb',
                                            'free_stocklist.berat AS beratambil',
                                            'free_stocklist.qty AS qtyambil',
                                            'free_stocklist.regu AS regu',
                                            'free_stock.tanggal AS tanggal_free',
                                            'free_stocklist.bb_kondisi AS jenis_bb',
                                            'free_stock.netsuite_send AS netsuite_send'
                                        )
                                        ->where(function($q){
                                            $q->whereNull('chiller.deleted_at');
                                            $q->whereNull('free_stocklist.deleted_at');
                                            $q->whereNull('free_stock.deleted_at');
                                        })
                                        ->whereBetween('free_stock.tanggal', [$tanggal1, $tanggal2])
                                        ->get();

            $file = "ambilbb_" . $tanggal1 . "_" . $tanggal2 . date('y-m-d-h_i_s') . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
            <table class="table default-table" id="export-table-lpah">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>TANGGAL BAHAN BAKU</th>
                    <th>TANGGAL AMBIL</th>
                    <th>ASAL</th>
                    <th>REGU</th>
                    <th>ITEM</th>
                    <th>QTY BB</th>
                    <th>BERAT BB</th>
                    <th>QTY AMBIL</th>
                    <th>BERAT AMBIL</th>
                    <th>JENIS</th>
                    <th>#</th>
                </tr>
                </thead>
                ';

            foreach ($ambilbb as $i => $val) {
                // if($val->netsuite_send == '0'){
                //     $tipe   = "Non WO";
                // }else{
                //     $tipe   = "Pakai WO";
                // }
                $html .= '
                    <tr>
                    <td>' . ++$i . '</td>
                    <td>' . $val->tanggal_produksi . '</td>
                    <td>' . $val->tanggal_free . '</td>
                    <td>' . $val->asal_tujuan . '</td>
                    <td>' . $val->regu . '</td>
                    <td>' . $val->item_name . '</td>
                    <td class="text-right" style="mso-number-format:General; text-align:center;">' . $val->qty_item . '</td>
                    <td class="text-right" style="mso-number-format:0.00; text-align:center;">' . $val->berat_item . '</td>
                    <td class="text-right" style="mso-number-format:General; text-align:center;">' . $val->qtyambil . '</td>
                    <td class="text-right" style="mso-number-format:0.00; text-align:center;">' .   $val->beratambil . '</td>
                    <td class="text-right">' . $val->jenis_bb . '</td>
                </tr>
                    ';
            }
        } elseif ($request->tujuan == 'siapkirim') {
            $siapkirim  =   Order::join('order_items','orders.id','order_items.order_id')
                                    ->join('items','order_items.item_id','items.id')
                                    ->join('category','items.category_id','category.id')
                                    ->select(
                                        'order_items.id AS order_item_id',
                                        'orders.no_so AS no_so',
                                        'orders.no_do AS no_do',
                                        'orders.nama AS nama',
                                        'orders.sales_channel AS sales_channel',
                                        'orders.tanggal_so AS tanggal_so',
                                        'orders.tanggal_kirim AS tanggal_kirim',
                                        'orders.keterangan AS keterangan_header',
                                        'items.sku AS sku',
                                        'items.nama AS item',
                                        'order_items.part AS part',
                                        'category.nama AS kategori_item',
                                        'order_items.bumbu AS bumbu',
                                        'order_items.memo AS memo',
                                        'order_items.keterangan AS keterangan_line',
                                        'order_items.qty AS qty',
                                        'order_items.berat AS berat',
                                        'order_items.fulfillment_qty AS fulfillment_qty',
                                        'order_items.fulfillment_berat AS fulfillment_berat',
                                        'order_items.tidak_terkirim_catatan AS tidak_terkirim_catatan',
                                        'order_items.retur_qty AS retur_qty',
                                        'order_items.retur_berat AS retur_berat',
                                        'order_items.status AS statusitem'
                                    )
                                    ->whereBetween('orders.tanggal_kirim', [$tanggal1, $tanggal2])
                                    ->orderBy('orders.no_so','ASC')
                                    ->get();


            $file = "siapkirim_" . $tanggal1 . "_" . $tanggal2 . date('y-m-d-h_i_s') . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
            <table class="table default-table" id="export-table-lpah">
                <thead>
                <tr class="text-center">
                    <th>NO</th>
                    <th>NO SO</th>
                    <th>NO DO</th>
                    <th>CUSTOMER</th>
                    <th>SALES CHANNEL</th>
                    <th>KATEGORI</th>
                    <th>TANGGAL SO</th>
                    <th>TANGGAL KIRIM</th>
                    <th>KETERANGAN HEADER</th>
                    <th>SKU</th>
                    <th>ITEM</th>
                    <th>PART</th>
                    <th>BUMBU</th>
                    <th>KETERANGAN ITEM</th>
                    <th>QTY</th>
                    <th>BERAT</th>
                    <th>FULFILLMENT QTY</th>
                    <th>FULFILLMENT BERAT</th>
                    <th>TIDAK TERKIRIM</th>
                    <th>RETUR QTY</th>
                    <th>RETUR BERAT</th>
                </tr>
                </thead>';

            foreach ($siapkirim as $i => $val) {
                if($val->no_do == '' || $val->no_do == null){
                    $no_do = '#';
                }else{
                    $no_do = $val->no_do;
                }
                $html .= '
                    <tr>
                    <td>'.++$i.'</td>
                    <td>'.$val->no_so.'</td>
                    <td>'. $no_do.'</td>
                    <td>'.$val->nama.'</td>
                    <td>'.$val->sales_channel.'</td>
                    <td>'.$val->kategori_item.'</td>
                    <td>'.$val->tanggal_so.'</td>
                    <td>'.$val->tanggal_kirim.'</td>
                    <td>'.$val->keterangan_header.'</td>
                    <td>'.$val->sku.'</td>
                    <td>'.$val->item.'</td>
                    <td>'.$val->part.'</td>
                    <td>'.$val->bumbu.'</td>
                    <td>'.$val->keterangan_line.'</td>
                    <td>'. $val->fulfillment_qty .'</td>
                    <td style="mso-number-format:0.0; text-align:center;>'. $val->fulfillment_berat .'</td>
                    <td>'.$val->tidak_terkirim_catatan.'</td>
                    <td>'.$val->retur_qty.'</td>
                    <td>'.$val->retur_berat.'</td>
                </tr>
                    ';
            }
        } elseif ($request->tujuan == 'sisachiller') {
            $sisachiller  =   Chiller::select('item_id','item_name','jenis','type','tanggal_produksi','asal_tujuan',
                                                DB::raw('ROUND(SUM(`chiller`.`qty_item`),2) AS `qty` '),
                                                DB::raw('ROUND(SUM(`chiller`.`berat_item`),2) AS `berat`'),
                                                DB::raw('ROUND(SUM(`chiller`.`stock_item`),2) AS `stock_qty`'),
                                                DB::raw('ROUND(SUM(`chiller`.`stock_berat`),2) AS `stock_berat`')
                                            )
                                            ->where('jenis','masuk')
                                            ->whereIn('type',['hasil-produksi','bahan-baku'])
                                            ->whereIn('asal_tujuan',['gradinggabungan','evisgabungan','retur','thawing','free_stock','open_balance','hasilbeli','abfbeli'])
                                            ->whereBetween('tanggal_produksi', [$tanggal1, $tanggal2])
                                            ->groupBy('item_id','item_name','jenis','type','tanggal_produksi','asal_tujuan')
                                            ->orderBy('item_id','DESC')
                                            ->get();

            $file = "SisaChiller_" . $tanggal1 . "_" . $tanggal2 . date('y-m-d-h_i_s') . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
            <table class="table default-table" id="export-table-lpah">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>TANGGAL</th>
                    <th>ASAL</th>
                    <th>TYPE</th>
                    <th>ITEM</th>
                    <th>QTY</th>
                    <th>BERAT</th>
                    <th>STOCK QTY</th>
                    <th>STOCK BERAT</th>
                </tr>
                </thead>';

            foreach ($sisachiller as $i => $val) {
                $html .= '
                    <tr>
                    <td>' . ++$i . '</td>
                    <td>' . $val->tanggal_produksi . '</td>
                    <td>' . $val->asal_tujuan . '</td>
                    <td>' . $val->type . '</td>
                    <td>' . $val->item_name . '</td>
                    <td class="text-right">' . $val->qty . '</td>
                    <td class="text-right">' . $val->berat . '</td>
                    <td class="text-right">' . $val->stock_qty . '</td>
                    <td class="text-right">' . $val->stock_berat . '</td>
                </tr>
                    ';
            }
        } elseif ($request->tujuan == 'gudang') {
            $gudang    =   Product_gudang::where('table_name', 'abf')
                            ->where('berat', '!=', NULL)
                            ->whereBetween('created_at', [$tanggal1 . ' 00:00:00', $tanggal2 . ' 23:59:59'])
                            ->where('status', '!=', 0)
                            ->orderBy('id', 'DESC')
                            ->get();

            $file = "Gudang_" . $tanggal1 . "_" . $tanggal2 . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
            <table class="table default-table" id="export-table-lpah">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Gudang</th>
                    <th>Sub Item</th>
                    <th>SKU</th>
                    <th>Item</th>
                    <th>Packaging</th>
                    <th>Tanggal Produksi</th>
                    <th>Qty</th>
                    <th>Berat Timbang</th>
                    <th>Berat ABF</th>
                    <th>Pallete</th>
                    <th>Expired</th>
                    <th>Stock</th>
                    <th>Jenis Transaksi</th>
                </tr>
                </thead>';

                foreach ($gudang as $i => $row) {
                    $html .= '
                        <tr>
                            <td>' .++$i .'</td>
                            <td>' .($row->productgudang->code ?? "") .'</td>
                            <td>' .$row->sub_item .'</td>
                            <td>' .$row->productitems->sku .'</td>
                            <td>' .$row->productitems->nama .'</td>
                            <td>' .$row->packaging .'</td>
                            <td>' .date('Y-m-d', strtotime($row->production_date)) .'</td>
                            <td>' .number_format($row->qty) .'</td>
                            <td>' .number_format($row->berat_timbang, 2) .'</td>
                            <td>' .number_format($row->berat, 2) .'</td>
                            <td>' .number_format($row->palete) .'</td>
                            <td>' .number_format($row->expired) .' Bulan</td>
                            <td>' .$row->stock_type .'</td>
                            <td>' .$row->jenis_trans .'</td>
                        </tr>';
                }
        } elseif ($request->tujuan == 'abf') {
            $abf    =   Abf::where('jenis', 'masuk')
                        ->whereBetween('created_at', [$tanggal1 . " 00:00:00", $tanggal2 . " 23:59:59"])
                        ->where('status', '1')
                        ->get();

            $file = "ABF_" . $tanggal1 . "_" . $tanggal2 . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
            <table class="table default-table" id="export-table-lpah">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Item</th>
                    <th>SKU</th>
                    <th>Parting</th>
                    <th>Customer</th>
                    <th>Packaging</th>
                    <th>Tanggal</th>
                    <th>Qty</th>
                    <th>Berat</th>
                </tr>
                </thead>';

                foreach ($abf as $i => $row) {
                    if ($row->table_name == 'chiller') {
                        $exp = json_decode($row->abf_chiller->label);

                    } else {
                        $exp = json_decode($row->abf_freetemp->label ?? false);
                    }
                    $html .= '
                    <tr>
                        <td>' .++$i .'</td>
                        <td>'. $row->item_name .'</td>
                        <td>'. $row->item->sku .'</td>
                        <td>'. ($exp->parting->qty ?? '') .'</td>
                        <td>'. ($exp->sub_item ?? '') .'</td>
                        <td>'. $row->packaging .'</td>
                        <td>'. date('d/m/Y', strtotime($row->created_at))  .'</td>
                        <td>'. number_format($row->qty_item )  .'</td>
                        <td>'. number_format($row->berat_item ).'</td>
                    </tr>';

                }

        } elseif ($request->tujuan == 'retur') {
            $retur      =   Retur::leftJoin('netsuite','retur.id','netsuite.tabel_id')
                                    ->Join('retur_item','retur_item.retur_id','retur.id')
                                    ->Join('items','retur_item.item_id','items.id')
                                    ->Join('customers','customers.id','retur.customer_id')
                                    ->select(
                                        'customers.id AS customer_id',
                                        'customers.nama AS nama',
                                        'retur.no_so AS no_so',
                                        'retur.tanggal_retur AS tanggal_retur',
                                        'items.nama AS item',
                                        'retur_item.qty AS qty',
                                        'retur_item.berat AS berat',
                                        'retur_item.tujuan AS tujuan',
                                        'retur_item.kategori AS kategori',
                                        'retur_item.unit AS unit',
                                        'retur_item.penanganan AS penanganan',
                                        'retur_item.catatan AS catatan',
                                        'netsuite.response AS response'
                                    )
                                    ->where('retur.status',2)
                                    ->where('netsuite.record_type','receipt_return')
                                    ->whereBetween('tanggal_retur', [$tanggal1, $tanggal2])
                                    ->get();

            $file = "Retur_" . $tanggal1 . "_" . $tanggal2 . ".xls";
            $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
            <table class="table default-table" id="export-table-lpah">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Customer</th>
                    <th>Item</th>
                    <th>Tanggal Retur</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th>Tujuan</th>
                    <th>Kategori</th>
                    <th>Catatan</th>
                </tr>
                </thead>';

                foreach ($retur as $i => $val) {
                    $html .= '
                    <tr>
                        <td>' .++$i .'</td>
                        <td>' .$val->nama . '</td>
                        <td>' .$val->item . '</td>
                        <td>' .$val->tanggal_retur . '</td>
                        <td>' .$val->qty . '</td>
                        <td>' .$val->berat . '</td>
                        <td>' .$val->tujuan . '</td>
                        <td>' .$val->kategori . '</td>
                        <td>' .$val->catatan . '</td>
                    </tr>';
                }
        }

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $html;
    }

    public static function getNamaCustomer($table,$id,$selected)
    {
        $query                          = DB::table($table)->where('id',$id)->get();
        if ($query->count() > 0) {
            foreach ($query as $h) {
                $hasil = $h->$selected;
            }
        } else {
            $hasil = '';
        }
        return $hasil;
    }
}

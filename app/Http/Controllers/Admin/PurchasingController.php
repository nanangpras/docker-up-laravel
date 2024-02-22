<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppKey;
use App\Models\Bahanbaku;
use App\Models\Chiller;
use App\Models\Driver;
use App\Models\Evis;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Grading;
use App\Models\Log;
use App\Models\Lpah;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Returalasan;
use App\Models\Returpurchase;
use App\Models\Supplier;
use App\Models\Target;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchasingController extends Controller
{

    public function index(Request $request)
    {
        return view('admin.pages.purchasing.index');
    }
    public function purch(Request $request)
    {
        // if (User::setIjin(1)) {
            $q                      =   $request->q ?? '';
            $tanggal_potong_awal    =   $request->tanggal_potong_awal ?? date('Y-m-d');
            $tanggal_potong_akhir   =   $request->tanggal_potong_akhir ?? date('Y-m-d');

            $purchase   =   Purchasing::whereIn('status', [1, 2])
                            ->whereBetween('tanggal_potong', [$tanggal_potong_awal, $tanggal_potong_akhir])
                            ->orderBy('tanggal_potong', 'DESC')
                            ->get();

            $purchase   =   $purchase->filter(function ($item) use ($q) {
                $res = true;
                if ($q != "") {
                    $res =  (false !== stripos($item->purcsupp->nama, $q)) ||
                            (false !== stripos($item->ukuran_ayam, $q)) ||
                            (false !== stripos($item->wilayah_daerah, $q)) ||
                            (false !== stripos($item->type_ekspedisi, $q)) ||
                            (false !== stripos($item->status_purchase, $q)) ||
                            (false !== stripos($item->harga_penawaran, $q)) ||
                            (false !== stripos($item->harga_deal, $q)) ||
                            (false !== stripos(number_format($item->harga_penawaran), $q)) ||
                            (false !== stripos(number_format($item->harga_deal), $q)) ||
                            (false !== stripos(number_format($item->jumlah_po), $q)) ||
                            (false !== stripos($item->jumlah_po, $q)) ||
                            (false !== stripos(date('d/m/y', strtotime($item->tanggal_potong)), $q)) ||
                            (false !== stripos($item->tanggal_potong, $q));
                }
                return $res;
            });

            $hitung =   [
                'total'     =>  $purchase->count(),
                'pending'   =>  $purchase->where('status', 2)->count(),
            ];

            $purchase   =   $purchase->paginate(30);

            $supplier   =   Supplier::pluck('nama', 'id');

            return view('admin.pages.purchasing.purchasing', compact('purchase', 'supplier', 'hitung', 'q', 'tanggal_potong_awal', 'tanggal_potong_akhir'));
        // }
        // return redirect()->route('index');
    }

    public function show(Request $request, $id)
    {
        // if (User::setIjin(1)) {
            $data       =   Purchasing::find($id);
            
            if ($data) {
                if ($request->key == 'mobil') {
                    $produksi   =   Production::where('purchasing_id', $id)
                                    ->where('id', $request->id)
                                    ->first();
                    if($request->subkey == 'nopol_autocomplete'){
                        $search      = $request->q;
                        $nopol       = Production::nomorpolisi($search);
                        return $nopol;
                    }
                    if ($produksi) {
                        $supir      =   Driver::whereNotIn('id', Production::select('sc_pengemudi_id')
                                            ->where('purchasing_id', $id)
                                            ->where('sc_pengemudi_id', '!=', NULL)
                                            ->pluck('sc_pengemudi_id')
                                        )
                                        ->pluck('nama', 'id');

                        $target     =   Target::get();

                        return view('admin.pages.purchasing.show_mobil', compact('data', 'produksi', 'supir', 'target'));
                    } else {
                        return "<div class='card'><div class='card-body'>Data tidak ditemukan</div></div>" ;
                    }
                } else {
                    $produksi   =   Production::where('purchasing_id', $id)->get();
                    return view('admin.pages.purchasing.show', compact('data', 'produksi'));
                }
            }
            return redirect()->route('purchasing.index');
        // }
        // return redirect()->route("index");
    }

    public function store(Request $request, $id)
    {
        // if (User::setIjin(1)) {
            $data   =   Purchasing::find($id);

            if ($data) {
                $produksi   =   Production::where('purchasing_id', $id)
                                ->where('id', $request->id)
                                ->first();

                if ($produksi) {

                    if ($request->data_supir == '') {
                        $result['status']   =   400;
                        $result['msg']      =   'Supir wajib dipilih';
                        return $result;
                    }

                    if ($request->no_polisi == '') {
                        $result['status']   =   400;
                        $result['msg']      =   'No Polisi wajib diisikan';
                        return $result;
                    } else {
                        $sama   =   Production::where('sc_no_polisi', $request->no_polisi)
                                    ->where('purchasing_id', $data->id)
                                    ->where('id', '!=', $produksi->id)
                                    ->count();

                        if ($sama > 0) {
                            $result['status']   =   400 ;
                            $result['msg']      =   'No Polisi sudah digunakan' ;
                            return $result ;
                        }
                    }

                    $target                         =   Target::find($request->target);

                    if (!$target) {
                        $result['status']   =   400;
                        $result['msg']      =   'Alamat kandang wajib dipilih';
                        return $result;
                    }

                    $produksi->sc_pengemudi_id      =   $request->data_supir;
                    $produksi->sc_pengemudi         =   $produksi->proddriver->nama ?? NULL;
                    $produksi->sc_no_polisi         =   $request->no_polisi;
                    $produksi->sc_nama_kandang      =   $request->nama_kandang;
                    $produksi->prod_tanggal_potong      =   $request->prod_tanggal_potong;

                    $produksi->target_id            =   $target->id;
                    $produksi->sc_pengemudi_target  =   $target->target;
                    $produksi->sc_alamat_kandang    =   $target->alamat;
                    $produksi->save();
                }
            }
        // }
        // return redirect()->route("index");
    }

    public function hitung_prosentase($id)
    {
        $row    =   '<div class="small"><b>PROSENTASE SUSUT POTONG</b></div>' ;
        $row    .=  Lpah::prosentase($id) ;

        return $row ;
    }

    public function laporan(Request $request)
    {
        // if (User::setIjin(1)) {
            if ($request->key == 'report') {
                $data   =   Production::find($request->id);

                if ($data) {

                    $grading    =   [] ;
                    foreach ($data->prodgrad as $row) {
                        $grading[]  =   [
                            'timbang'   =>  $row,
                            'chiller'   =>  [
                                'data'      =>  $row->gradchill,
                                'produksi'  =>  [
                                    'total' =>  [
                                        'berat' =>  FreestockList::where('chiller_id', $row->gradchill->id)->sum('berat'),
                                        'qty'   =>  FreestockList::where('chiller_id', $row->gradchill->id)->sum('qty'),
                                    ],
                                    'data'  =>  $row->gradchill->ambil_chiller
                                ]
                            ]
                        ] ;
                    }

                    // $result =   [
                    //     'purchase'  =>  [ $data->prodpur ],
                    //     'produksi'  =>  [ $data ],
                    //     'evis'      =>  $data->prodevis,
                    //     'grading'   =>  [
                    //         'total' =>  [
                    //             'berat' =>  Grading::total_grading($data->id, 'berat_item'),
                    //             'qty'   =>  Grading::total_grading($data->id, 'stock_item'),
                    //         ],
                    //         'data'  =>  $grading
                    //     ]
                    // ] ;

                    // return $result ;

                    $result =   [] ;
                    foreach (Production::where('id', 1033)->get() as $row) {
                        $purchase   =   Purchasing::select('id', 'no_po', 'jenis_po', 'item_po', 'internal_id_po', 'harga_penawaran', 'harga_deal', 'supplier_id', 'ukuran_ayam', 'type_ekspedisi', 'jenis_ayam', 'tanggal_potong', 'status')
                                        ->where('id', $row->purchasing_id)
                                        ->first()
                                        ->setAppends([]);

                        $lpah_isi   =   Lpah::where('production_id', $row->id)->where('type', 'isi') ;
                        $lpah_kosong=   Lpah::where('production_id', $row->id)->where('type', 'kosong') ;

                        $evis   =   [] ;
                        foreach ($row->prodevis as $vis) {
                            $evichil    =   Chiller::select('id', 'item_id', 'item_name', 'asal_tujuan', 'jenis', 'type', 'regu', 'berat_item AS berat', 'qty_item AS qty', 'stock_berat AS sisa_berat', 'stock_item AS sisa_qty')
                                            ->where('asal_tujuan', 'evisgabungan')
                                            ->where('item_id', $vis->item_id)
                                            ->whereDate('tanggal_produksi', $vis->tanggal_potong)
                                            ->first();

                            $prodevis   =   FreestockList::select('free_stocklist.id', 'freestock_id', 'free_stock.regu', 'tanggal', 'chiller_id', 'item_id', 'berat', 'qty')
                                            ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                            ->where('chiller_id', $evichil->id);

                            $finished   =   FreestockTemp::select('free_stocktemp.id', 'freestock_id', 'item_id', 'nama', 'regu', 'tanggal_produksi', 'berat', 'qty')
                                            ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
                                            ->whereIn('freestock_id', FreestockList::select('freestock_id')->where('chiller_id', $evichil->id)) ;

                            $hasil_jadi =   [] ;
                            foreach ($finished->get() as $list) {
                                $alokasi    =   Bahanbaku::select('id', 'order_id', 'order_item_id', 'proses_ambil', 'bb_berat AS berat', 'bb_item AS qty')
                                                ->whereIn('chiller_out', Chiller::select('id')->where('table_id', $list->id)->where('table_name', 'free_stocktemp'))
                                                ->get() ;

                                $hasil_jadi[]   =   [
                                    'produksi'  =>  $list,
                                    'alokasi'   =>  $alokasi
                                ];
                            }

                            $alokasi    =   Bahanbaku::select('id', 'order_id', 'order_item_id', 'proses_ambil', 'bb_berat AS berat', 'bb_item AS qty')
                                            ->where('chiller_out', $evichil->id) ;


                            $evis[]  =   [
                                'item_id'   =>  $vis->item_id ,
                                'item_name' =>  $vis->eviitem->nama ,
                                'qty'       =>  $vis->total_item ,
                                'berat'     =>  $vis->berat_item ,
                                'chiller'   =>  [
                                    'masuk'     =>  $evichil,
                                    'pengambilan'   =>  [
                                        'total'     =>  [
                                            'berat' =>  $prodevis->sum('berat') + $alokasi->sum('bb_berat'),
                                            'qty'   =>  $prodevis->sum('qty') + $alokasi->sum('bb_item')
                                        ],
                                        'produksi'  =>  [
                                            'jumlah'    =>  [
                                                'berat' =>  $prodevis->sum('berat'),
                                                'qty'   =>  $prodevis->sum('qty')
                                            ],
                                            'ambil_bb'      =>  $prodevis->get(),
                                            'finished_good' =>  [
                                                'total' =>  [
                                                    'berat' =>  $finished->sum('berat'),
                                                    'qty'   =>  $finished->sum('qty')
                                                ],
                                                'hasil' =>  $hasil_jadi
                                            ]
                                        ],
                                        'alokasi_order' =>  [
                                            'jumlah'    =>    [
                                                'berat' =>  (float)$alokasi->sum('bb_berat'),
                                                'qty'   =>  (float)$alokasi->sum('bb_item')
                                            ],
                                            'data'      =>  $alokasi->get()
                                        ]
                                    ]
                                ]
                            ] ;
                        }

                        $result[]   =   [
                                'purchasing'    =>  $purchase,
                                'produksi'      =>  [
                                    'id'                =>  $row->id,
                                    'purchasing_id'     =>  $row->purchasing_id,
                                    'nomor_po'          =>  $row->no_po,
                                    'tanggal_potong'    =>  $row->prod_tanggal_potong,
                                    'kandang'           =>  $row->sc_nama_kandang,
                                    'alamat_kandang'    =>  $row->sc_alamat_kandang,
                                    'jenis_ekspedisi'   =>  $row->po_jenis_ekspedisi ,
                                    'security'          =>  [
                                        'driver'        =>  $row->sc_pengemudi ,
                                        'nomor_polisi'  =>  $row->sc_no_polisi ,
                                        'tanggal_masuk' =>  $row->sc_tanggal_masuk ,
                                        'jam_masuk'     =>  $row->sc_jam_masuk ,
                                        'nomor_urut'    =>  $row->no_urut,
                                        'ekor_do'       =>  $row->sc_ekor_do,
                                        'berat_do'      =>  $row->sc_berat_do,
                                        'rerata_do'     =>  $row->sc_rerata_do,
                                        'lpah'          =>  [
                                            'nomor_lpah'        =>  $row->no_lpah ,
                                            'tanggal_lpah'      =>  $row->lpah_tanggal_potong ,
                                            'jambongkar_lpah'   =>  $row->lpah_jam_bongkar ,
                                            'berat_kotor'       =>  $row->lpah_berat_kotor ,
                                            'berat_bersih'      =>  $row->lpah_berat_terima,
                                            'keranjang_isi'     =>  $lpah_isi->sum('berat') ,
                                            'keranjang_kosong'  =>  $lpah_kosong->sum('berat') ,
                                            // 'timbang'   =>  [
                                            //     'isi'       =>  $lpah_isi->pluck('berat') ,
                                            //     'keranjang' =>  $lpah_kosong->pluck('berat')
                                            // ]
                                            'evis'              =>  [
                                                'total'     =>  [
                                                    'berat' =>  (float)Evis::hitung_total($row->id, 'berat_item'),
                                                    'qty'   =>  (float)Evis::hitung_total($row->id, 'total_item')
                                                ],
                                                'timbang'   =>  $evis
                                            ]
                                        ] ,
                                    ]
                                ]
                        ];
                    }

                    $msg['message']['status']   =   200;
                    $msg['message']['notif']    =   'success';
                    $msg['data']      =   $result;
                    return $msg;
                } else {
                    $msg['message']['status']   =   400;
                    $msg['message']['notif']    =   'not found';
                    $msg['data']      =   [];
                    return $msg ;
                }

            } else {

                $mulai              =   $request->mulai ?? date('Y-m-d');
                $selesai            =   $request->selesai ?? date('Y-m-d');

                $result             =   Purchasing::where(function ($query) use ($mulai, $selesai) {
                                            if ($mulai and $selesai) {
                                                $query->whereBetween('tanggal_potong', [$mulai, $selesai]);
                                            }
                                        })->get();

                $hitungEkoranAyam   =   Purchasing::whereBetween('tanggal_potong', [$mulai, $selesai])->join('productions', 'productions.purchasing_id', '=', 'purchasing.id')
                                        ->select('ukuran_ayam', DB::raw("sum(ekoran_seckle) as ekor"))
                                        ->groupBy('ukuran_ayam')
                                        ->orderBy('ukuran_ayam')
                                        ->where('ekoran_seckle', '>', 0)
                                        ->pluck('ukuran_ayam', 'ekor');

                $tangkap    = 0;
                $kirim      = 0;
                $jumlah_po  = 0;
                // $uk8_10     = 0;
                // $uk10_12    = 0;
                // $uk12_14    = 0;
                // $uk13_15    = 0;
                // $uk14_16    = 0;
                // $uk15_17    = 0;
                // $uk16_18    = 0;
                // $uk18_20    = 0;
                // $uk20_22    = 0;
                // $uk12_15    = 0;
                // $uk13_16    = 0;
                // $uk14_17    = 0;
                // $uk15_18    = 0;
                $maklon     = 0;
                $broiler    = 0;
                $pejantan   = 0;
                $kampung    = 0;
                $berat_ayam = 0;
                $jumlah_ayam = 0;
                foreach ($result as $row) {
                    if ($row->type_ekspedisi == 'tangkap') {
                        $tangkap    += 1;
                    }
                    if ($row->type_ekspedisi == 'kirim') {
                        $kirim  += 1;
                    };
                    $jumlah_po  += $row->jumlah_po;

                    // if ($row->ukuran_ayam == '0.8 - 1.0') {
                    //     $uk8_10 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.0 - 1.2') {
                    //     $uk10_12 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.2 - 1.4') {
                    //     $uk12_14 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.2 - 1.5') {
                    //     $uk12_15 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.3 - 1.5') {
                    //     $uk13_15 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.3 - 1.6') {
                    //     $uk13_16 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.4 - 1.6') {
                    //     $uk14_16 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.4 - 1.7') {
                    //     $uk14_17 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.5 - 1.7') {
                    //     $uk15_17 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.5 - 1.8') {
                    //     $uk15_18 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.6 - 1.8') {
                    //     $uk16_18 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '1.8 - 2.0') {
                    //     $uk18_20 +=  1;
                    // }
                    // if ($row->ukuran_ayam == '2.0 - 2.2') {
                    //     $uk20_22 +=  1;
                    // }

                    if ($row->jenis_ayam == 'maklon') {
                        $maklon = +1;
                    }
                    if ($row->jenis_ayam == 'broiler') {
                        $broiler    = +1;
                    }
                    if ($row->jenis_ayam == 'pejantan') {
                        $pejantan   = +1;
                    }
                    if ($row->jenis_ayam == 'kampung') {
                        $kampung    = +1;
                    }
                    $berat_ayam     +=  $row->berat_ayam;
                    $jumlah_ayam    +=  $row->jumlah_ayam;
                }

                $hasil      =   [
                    'tangkap'       =>  $tangkap,
                    'kirim'         =>  $kirim,
                    'jumlah_po'     =>  $jumlah_po,
                    // 'uk8_10'        =>  $uk8_10,
                    // 'uk10_12'       =>  $uk10_12,
                    // 'uk12_14'       =>  $uk12_14,
                    // 'uk13_15'       =>  $uk13_15,
                    // 'uk14_16'       =>  $uk14_16,
                    // 'uk15_17'       =>  $uk15_17,
                    // 'uk16_18'       =>  $uk16_18,
                    // 'uk18_20'       =>  $uk18_20,
                    // 'uk20_22'       =>  $uk20_22,
                    // 'uk12_15'       =>  $uk12_15,
                    // 'uk13_16'       =>  $uk13_16,
                    // 'uk14_17'       =>  $uk14_17,
                    // 'uk15_18'       =>  $uk15_18,
                    'maklon'        =>  $maklon,
                    'broiler'       =>  $broiler,
                    'pejantan'      =>  $pejantan,
                    'kampung'       =>  $kampung,
                    'berat_ayam'    =>  $berat_ayam,
                    'jumlah_ayam'   =>  $jumlah_ayam,
                ];

                $top5   =   Purchasing::select('supplier_id', DB::raw("COUNT(supplier_id) AS supplier"))
                            ->where(function ($query) use ($mulai, $selesai) {
                                if ($mulai and $selesai) {
                                    $query->whereBetween('tanggal_potong', [$mulai, $selesai]);
                                }
                            })
                            ->groupBy('supplier_id')
                            ->orderBy('supplier', 'DESC')
                            ->limit(5)
                            ->get();

                $susut  =   Production::where('no_urut', '!=', NULL)
                            ->where('qc_user_id', '!=', NULL)
                            ->where(function ($query) use ($mulai, $selesai) {
                                if ($mulai and $selesai) {
                                    $query->whereBetween('lpah_tanggal_potong', [$mulai." 00:00:01", $selesai." 23:59:59"]);
                                }
                            })
                            ->get();

                return view('admin.pages.laporan.laporan-purchasing', compact('mulai', 'selesai', 'hasil', 'top5', 'susut', 'hitungEkoranAyam'));
            }
        // }
        // return redirect()->route("index");
    }

    public function target(Request $request)
    {
        if ($request->key == 'input') {
            return view('admin.pages.purchasing.target_input');
        } else if ($request->key == 'daftar') {
            $data   =   Target::orderBy('id', 'DESC')
                        ->get() ;

            return view('admin.pages.purchasing.target_daftar', compact('data'));
        } else {
            return view('admin.pages.purchasing.target');
        }
    }

    public function targetstore(Request $request)
    {
        if (!$request->alamat) {
            $result['status']   =   400 ;
            $result['msg']      =   "Alamat wajib diisikan" ;
            return $result ;
        }

        if (!$request->target) {
            $result['status']   =   400;
            $result['msg']      =   "Toleransi wajib diisikan";
            return $result;
        }

        DB::beginTransaction();

        $target                 =   new Target ;
        $target->alamat         =   $request->alamat ;
        $target->target         =   $request->target ;
        if (!$target->save()) {
            DB::rollBack() ;
        }

        DB::commit() ;
    }

    public function targetupdate(Request $request)
    {
        $data       =   Target::find($request->x_code) ;
        if ($data) {
            $data->alamat   =   $request->alamat;
            $data->target   =   $request->target;
            $data->save() ;

            return back()->with('status', 1)->with('message', 'Toleransi berhasil diperbaharui');
        }
        return back()->with('status', 2)->with('message', 'Proses Gagal');
    }

    public function targetdestroy(Request $request)
    {
        $data   =   Target::find($request->id);

        if ($data) {
            $data->delete() ;
        } else {
            $result['status']   =   400;
            $result['msg']      =   "Target tidak ditemukan";
            return $result;
        }

    }

    public function laporanlpah(Request $request)
    {
        $tanggal_potong_awal     =   $request->tanggal_potong_awal ?? date('Y-m-d');
        $tanggal_potong_akhir    =   $request->tanggal_potong_akhir ?? date('Y-m-d');

        $produksi   =   Production::whereIn('purchasing_id', Purchasing::select('id')
                        ->whereBetween('tanggal_potong', [$tanggal_potong_awal, $tanggal_potong_akhir]))
                        ->where('sc_status', '1')
                        ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                        ->get();

        return view('admin.pages.purchasing.lpah', compact('produksi', 'tanggal_potong_awal', 'tanggal_potong_akhir'));
    }

    public function bonus(Request $request)
    {
        // $mulai          = $request->mulai ?? date('Y-m-d');
        // $selesai        = $request->selesai ?? date('Y-m-d');
        // $data_wilayah   = $request->wilayah ?? '';
        
        if($request->key == 'wilayah'){
            $mulai          = $request->mulai ?? date('Y-m-d');
            $selesai        = $request->selesai ?? date('Y-m-d');
            $data_wilayah   = $request->wilayah ?? '';
            $wilayah        = Production::select('sc_wilayah')
                                        ->whereBetween('sc_tanggal_masuk', [$mulai, $selesai])
                                        ->where('sc_wilayah', '!=', 'NONE')
                                        ->where(function($query) {
                                            $query->where('po_jenis_ekspedisi', 'tangkap')->orWhere('sc_wilayah', 'BREBES');
                                        })
                                        ->where('sc_nama_kandang', '!=', NULL)
                                        ->where(function($query) use ($request) {
                                            if ($request->supir) {
                                                if ($request->supir != 'all') {
                                                    // $query->where('sc_pengemudi', $request->supir) ;
                                                    $query->orWhere('sc_pengemudi', 'LIKE' , '%'.ucwords($request->supir).'%') ;
                                                    $query->orWhere('sc_pengemudi', 'LIKE' , '%'.strtoupper($request->supir).'%') ;
                                                    $query->orWhere('sc_pengemudi', 'LIKE' , '%'.strtolower($request->supir).'%') ;
                                                    $query->orWhere('sc_pengemudi', 'LIKE' , '%'.$request->supir.'%') ;
                                                }
                                            }
                                        })
                                        ->where(function($query) use ($request) {
                                            if ($request->ekspedisi) {
                                                if ($request->ekspedisi == 'tangkap' || $request->ekspedisi == 'kirim') {
                                                    $query->whereHas('prodpur', function ($subQuery) use ($request) {
                                                        $subQuery->where('type_ekspedisi', $request->ekspedisi);
                                                    });
                                                } else {
                                                }
                                            }
                                        })
                                        ->groupBy('sc_wilayah')
                                        ->get();
            return view('admin.pages.purchasing.bonus.data-wilayah', compact('wilayah','data_wilayah'));
        } else
        if ($request->key == 'supir') {
            $driver =   Driver::where('nama', $request->id)->first();
            return $driver ? $driver->no_polisi : '' ;
        } else
        if ($request->key == 'view') {
            $mulai          = $request->mulai ?? date('Y-m-d');
            $selesai        = $request->selesai ?? date('Y-m-d');
            $data_wilayah   = $request->wilayah ?? '';
            $data   =   Production::with('prodpur')->whereBetween('sc_tanggal_masuk', [$request->mulai ?? date("Y-m-d"), $request->selesai ?? date("Y-m-d")])
                        ->where(function($query) {
                                $query->where('po_jenis_ekspedisi', 'tangkap')->orWhere('sc_wilayah', 'BREBES');
                        })
                        ->where('sc_nama_kandang', '!=', NULL)
                        ->where(function($query) use ($request, $data_wilayah) {
                            if ($request->supir) {
                                if ($request->supir != 'all') {
                                    $query->where('sc_pengemudi', $request->supir) ;
                                }
                            }
                            if($data_wilayah){
                                $query->where('sc_wilayah', $data_wilayah);
                            }
                        })
                        ->where(function($query) use ($request) {
                            if ($request->ekspedisi) {
                                if ($request->ekspedisi == 'tangkap' || $request->ekspedisi == 'kirim') {
                                    $query->whereHas('prodpur', function ($subQuery) use ($request) {
                                        $subQuery->where('type_ekspedisi', $request->ekspedisi);
                                    });
                                } else {
                                }
                            }
                        })
                        ->orderBy('sc_tanggal_masuk', 'ASC')
                        ->get();
            if(count($data) > 0){
                return view('admin.pages.purchasing.bonus.data', compact('data','data_wilayah'));
            } else {
                $data   =   Production::whereBetween('sc_tanggal_masuk', [$request->mulai ?? date("Y-m-d"), $request->selesai ?? date("Y-m-d")])
                            ->where(function($query) {
                                $query->where('po_jenis_ekspedisi', 'tangkap')->orWhere('sc_wilayah', 'BREBES');
                            })
                            ->where('sc_nama_kandang', '!=', NULL)
                            ->where(function($query) use ($request, $data_wilayah) {
                                if ($request->supir) {
                                    if ($request->supir != 'all') {
                                        $query->where('sc_pengemudi', $request->supir) ;
                                    }
                                }
                            })
                            ->orderBy('sc_tanggal_masuk', 'ASC')
                            ->get();
                            return view('admin.pages.purchasing.bonus.data', compact('data','data_wilayah'));
            }
        } else
        if ($request->key == 'resume') {
            $mulai          = $request->mulai ?? date('Y-m-d');
            $selesai        = $request->selesai ?? date('Y-m-d');
            $data_wilayah   = $request->wilayah ?? '';
            
            $kandang    =   Production::select('sc_wilayah', 'lpah_persen_susut AS susut','sc_tanggal_masuk')
                            ->whereBetween('sc_tanggal_masuk', [$mulai, $selesai])
                            ->where(function($query) {
                                $query->where('po_jenis_ekspedisi', 'tangkap')->orWhere('sc_wilayah', 'BREBES');
                            })
                            ->where('sc_nama_kandang', '!=', NULL)
                            ->where(function($query) use ($request, $data_wilayah) {
                                if ($request->supir) {
                                    if ($request->supir != 'all') {
                                        $query->where('sc_pengemudi', $request->supir) ;
                                    }
                                }
                                if($data_wilayah){
                                    $query->where('sc_wilayah', $data_wilayah);
                                }
                            })
                            ->get();

            $susut  =   Production::select(DB::raw("(SUM(lpah_persen_susut)/ SUM(IF((lpah_persen_susut), 1, 0))) AS susut"), DB::raw("SUM(IF((lpah_persen_susut), 1, 0)) as jumlah"))
                        ->whereBetween('prod_tanggal_potong', [$mulai, $selesai])
                        ->where(function($query) {
                            $query->where('po_jenis_ekspedisi', 'tangkap')->orWhere('sc_wilayah', 'BREBES');
                        })
                        ->where('sc_nama_kandang', '!=', NULL)
                        ->where(function($query) use ($request, $data_wilayah) {
                            if ($request->supir != 'all') {
                                $query->where('sc_pengemudi_id', $request->supir) ;
                            }
                            if($data_wilayah){
                                $query->where('sc_wilayah', $data_wilayah);
                            }
                        })
                        ->first();

            $datachart = Production::with('prodpur')
                                ->whereBetween('sc_tanggal_masuk', [$mulai , $selesai])
                                ->where(function($query) {
                                    $query->where('po_jenis_ekspedisi', 'tangkap')->orWhere('sc_wilayah', 'BREBES');
                                })
                                ->where('sc_nama_kandang', '!=', NULL)
                                ->where(function($query) use ($request) {
                                    if ($request->supir) {
                                        if ($request->supir != 'all') {
                                            $query->where('sc_pengemudi', $request->supir) ;
                                            // $query->orWhere('sc_pengemudi', 'LIKE' , '%'.ucwords($request->supir).'%') ;
                                            // $query->orWhere('sc_pengemudi', 'LIKE' , '%'.strtoupper($request->supir).'%') ;
                                            // $query->orWhere('sc_pengemudi', 'LIKE' , '%'.strtolower($request->supir).'%') ;
                                            // $query->orWhere('sc_pengemudi', 'LIKE' , '%'.$request->supir.'%') ;
                                        }
                                    }
                                })
                                ->where(function($query) use ($request) {
                                    if ($request->wilayah) {
                                        if ($request->wilayah != '') {
                                            $query->where('sc_wilayah', $request->wilayah) ;
                                        }
                                    }
                                })
                                ->where(function($query) use ($request) {
                                    if ($request->ekspedisi) {
                                        if ($request->ekspedisi == 'tangkap' || $request->ekspedisi == 'kirim') {
                                            $query->whereHas('prodpur', function ($subQuery) use ($request) {
                                                $subQuery->where('type_ekspedisi', $request->ekspedisi);
                                            });
                                        } else {
                                        }
                                    }
                                })
                                ->orderBy('sc_tanggal_masuk', 'ASC');

           

            $toleransi_chart  =   [] ;
            $susut_chart  =   [] ;
            $driver = [];
            foreach ($datachart->get() as $key => $value) {
                $toleransi = Target::where('alamat', 'like', '%' . preg_replace('/\s+/', '', $value->sc_wilayah) . '%')->orderBy('id', 'DESC')->first()->target ?? 0 ;
                $toleransi_chart[] =  floatval($value->lpah_persen_susut) ? floatval($toleransi) : 0;
                $susut_chart[] = floatval($value->lpah_persen_susut) ?? 0;
                $driver[] = $value->sc_pengemudi . '+'. $value->sc_tanggal_masuk;
            }

            $datachart   = $datachart->selectRaw('CONCAT(sc_pengemudi , " ", sc_tanggal_masuk) as nama')->pluck('nama');
            $alokasi     =   "[{name: 'Toleransi',data: ";
            $alokasi    .=  json_encode($toleransi_chart) ;
            $alokasi    .=  "}, {name: 'Susut',data: ";
            $alokasi    .=  json_encode($susut_chart);
            $alokasi    .=  "}]";


            return view('admin.pages.purchasing.bonus.resume', compact('susut', 'kandang','alokasi','susut_chart','toleransi_chart','datachart','data_wilayah'));
        } else {
            $supir  =   Driver::where('driver_exspedisi', 1)->pluck('nama', 'id');

            return view('admin.pages.purchasing.bonus.index', compact('supir'));
        }
    }

    public function supplier(Request $request)
    {
        if ($request->key == 'view') {
            $awal   =   $request->awal ?? date("Y-m-d") ;
            $akhir  =   $request->akhir ?? date("Y-m-d") ;

            $data   =   Purchasing::whereBetween('tanggal_potong', [$awal, $akhir])
                        ->where('supplier_id', $request->supplier)
                        ->orderBy('tanggal_potong', 'ASC') ;

            $data2  =   $data->get();
            $data   =   $data->paginate(20) ;

            return view('admin.pages.purchasing.supplier.data', compact('data', 'data2', 'awal', 'akhir', 'request'));
        } else {
            $supplier   =   Supplier::whereIn('id', Purchasing::select('supplier_id'))
                            ->get();

            return view('admin.pages.purchasing.supplier.index', compact('supplier'));
        }
    }


    public function retur(Request $request, $id)
    {
        $data   =   Purchasing::where('id', $id)
                    ->where('type_po', '!=', 'PO LB')
                    ->first() ;

        if ($data) {
            $alasan =   Returalasan::get() ;
            return view('admin.pages.purchasing.retur.index', compact('data', 'alasan'));
        } else {
            return redirect()->route('purchasing.index');
        }

    }


    public function returstore(Request $request, $id)
    {
       $data   =   Purchasing::where('id', $id)
                    ->where('type_po', '!=', 'PO LB')
                    ->first() ;

        if ($data) {
            for ($x=0; $x < COUNT($request->id); $x++) {
                if (($request->berat[$x]) && ($request->qty[$x])) {
                    $retur                  =   new Returpurchase ;
                    $retur->purchasing_id   =   $data->id ;
                    $retur->purchaseitem_id =   $request->id[$x] ;
                    $retur->qty             =   $request->qty[$x] ;
                    $retur->berat           =   $request->berat[$x] ;
                    $retur->alasan          =   $request->alasan ;
                    $retur->tanggal         =   $request->tanggal ;
                    $retur->penginput       =   $request->penginput ;
                    $retur->status          =   1 ;
                    $retur->save() ;
                }
            }

            return back()->with('status', 1)->with('message', 'Tambah retur berhasil') ;
        } else {
            return redirect()->route('purchasing.index');
        }
    }
}

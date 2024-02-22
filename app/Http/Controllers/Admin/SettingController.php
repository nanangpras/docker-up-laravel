<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Adminedit;
use App\Models\Chiller;
use App\Models\Production;
use App\Models\Purchasing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class SettingController extends Controller
{
    public function supplier(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $diterima   =   Production::whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO LB', 'PO Maklon'])->where('tanggal_potong', $tanggal))
            ->orderBy('no_urut', 'ASC')
            ->get();

        $purch      =   Purchasing::where('tanggal_potong', $tanggal)->get();

        return view('admin.pages.setting.editsupplier', compact('diterima', 'tanggal', 'purch'));
    }

    public function supplierstore(Request $request)
    {
        // return $request->all();
        $prod                   =   Production::find($request->x_code);
        $supp                   =   Purchasing::find($request->supplier);

        $prod->purchasing_id    =   $request->supplier;
        $prod->no_po            =   $supp->no_po;
        $prod->sc_nama_kandang  =   $supp->purcsupp->nama;
        $prod->save();

        $count                  =   Production::where('purchasing_id', $supp->id)->where('sc_status', null)->count();
        if ($count > 0) {
            $supp->status   =   2;
        } else {
            $supp->status   =   1;
        }

        $supp->save();

        return back()->with('status', 1)->with('message', 'Berhasil Update');


    }

    public function bukaTransaksi(Request $request){
        $destination    = $request->destination;
        // dd($destination);
        $tanggal_awal   = $request->startdate;
        $tanggal_akhir  = $request->enddate;
        $tb_chillerfg   = FALSE;
        $tb_chillerbb   = FALSE;
        $tb_abf         = FALSE;
        if ($destination) {
            for ($i=0; $i < Count($destination) ; $i++) {
                if ($destination[$i] == 'chillerfg') {
                    $tb_chillerfg = TRUE;
                };
                if ($destination[$i] == 'chillerbb') {
                    $tb_chillerbb = TRUE;
                };
                if ($destination[$i] == 'tbabf') {
                    $tb_abf       = TRUE;
                };

            }
        }

        //validasi tujuan
        if ($tb_chillerfg == TRUE) {
            // dd("Open cutoff chiller Sedang diproses...");
            if ($tanggal_awal && $destination && $tanggal_akhir) {

                $content =[
                    "tanggal_awal"          => $tanggal_awal,
                    "tanggal_akhir"         => $tanggal_akhir,
                    "tujuan"                => [
                        "chiller"           => 'chillerfg',
                    ],
                ];

                Chiller::where('status_cutoff',1)->whereBetween('tanggal_produksi',[$tanggal_awal,$tanggal_akhir])->whereIn('type',['hasil-produksi'])->update(['status_cutoff' => NULL]);

                $log                        =  new Adminedit();
                $log->user_id               =  Auth::user()->id ;
                $log->table_name            =  "chiller";
                $log->table_id              =  null ;
                $log->type                  =  'update' ;
                $log->activity              =  'open_cut_off chiller hasil produksi';
                $log->content               =  'Open Cut Off Periode Tanggal' . ' '  . $tanggal_awal." - ".$tanggal_akhir;
                $log->data                  =  json_encode([
                        'affected_data'     => $content,
                ]) ;
                $log->save();

                // return back()->with('status', 1)->with('message', 'Transaksi berhasil dibuka');

            }else{
                $response['status'] =   400;
                $response['msg']    =   'Salah satu field tidak terpenuhi';
                return $response;
            }
        } 
        if ($tb_chillerbb == TRUE) {

            $content =[
                "tanggal_awal"          => $tanggal_awal,
                "tanggal_akhir"         => $tanggal_akhir,
                "tujuan"                => [
                    "chiller"           => 'chillerbb'
                ],
            ];

            if ($tanggal_awal && $destination && $tanggal_akhir) {
                
                Chiller::where('status_cutoff',1)->whereBetween('tanggal_produksi',[$tanggal_awal,$tanggal_akhir])->where('type','bahan-baku')->update(['status_cutoff' => NULL]);

                $log                        =  new Adminedit();
                $log->user_id               =  Auth::user()->id ;
                $log->table_name            =  "chiller";
                $log->table_id              =  null ;
                $log->type                  =  'update' ;
                $log->activity              =  'open_cut_off chiller bahan baku';
                $log->content               =  'Open Cut Off Periode Tanggal' . ' '  . $tanggal_awal." - ".$tanggal_akhir;
                $log->data                  =  json_encode([
                        'affected_data'     => $content,
                ]) ;
                $log->save();
                // return back()->with('status', 1)->with('message', 'Transaksi berhasil dibuka');

            }else{
                $response['status'] =   400;
                $response['msg']    =   'Salah satu field tidak terpenuhi';
                return $response;
            }
        } 
        if ($tb_abf == TRUE) {
            $content =[
                "tanggal_awal"          => $tanggal_awal,
                "tanggal_akhir"         => $tanggal_akhir,
                "tujuan"                => [
                    "abf"               => 'abf'
                ],
            ];

            if ($tanggal_awal && $destination && $tanggal_akhir) {
               
                Abf::where('status_cutoff',1)->whereBetween('tanggal_masuk',[$tanggal_awal,$tanggal_akhir])->update(['status_cutoff' => NULL]);

                $log                        =   new Adminedit();
                $log->user_id               =   Auth::user()->id ;
                $log->table_name            =   "abf";
                $log->table_id              =   null ;
                $log->type                  =   'update' ;
                $log->activity              =   'open_cut_off abf';
                $log->content               =   'Open Cut Off Periode Tanggal' . ' '  . $tanggal_awal." - ".$tanggal_akhir;
                $log->data                  =   json_encode([
                        'affected_data'     => $content,
                ]) ;
                $log->save();

                // return back()->with('status', 1)->with('message', 'Transaksi Berhasil Dibuka');

            }else{
                $response['status'] =   400;
                $response['msg']    =   'Salah satu field tidak terpenuhi';
                return $response;
            }
        }
        if(!$destination){
            $response['status'] =   400;
            $response['msg']    =   'Tujuan kosong, Gagal Membuka data transaksi';
            return $response;
        }

        $response['status'] =   200;
        $response['msg']    =   'Transaksi berhasil dibuka';
        return $response;
    }

    public function tutupTransaksi(Request $request){
        
        if($request->key == 'historycutoff'){
            $master         = Adminedit::where('activity','cut_off')->orderBy('id','DESC')->take(10)->get();
            return view('admin.pages.setting.cutoff.viewperiode',compact('master'));
        }
        else{
            // return $request->all();
            // $tahun = $request->tahun;
            $tabel = $request->tujuan;
            // $bulan = $request->bulan;
            $tanggal_awal    = $request->cutoffMulai;
            $tanggal_akhir  = $request->cutoffAkhir;

            $tb_chiller = FALSE;
            $tb_abf     = FALSE;
            //check tujuan tabel
            if ($tabel) {
                for ($i=0; $i < Count($tabel) ; $i++) {
                    if ($tabel[$i] == 'chiller') {
                        $tb_chiller = TRUE;
                    };
                    if ($tabel[$i] == 'abf') {
                        $tb_abf = TRUE;
                    };

                }
            }
            //validasi tujuan
            if ($tb_chiller == TRUE && $tb_abf == TRUE) {
                if ($tanggal_awal && $tabel && $tanggal_akhir) {

                    //GET DATA SEBELUM TERKENA UPDATE
                    $dataABF                    = Abf::whereBetween('tanggal_masuk',[$tanggal_awal,$tanggal_akhir])
                                                    ->where(function($q){
                                                        if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                                            $q->whereNotIn('item_id',[4529,11341]);
                                                        }else{
                                                            $q->whereNotIn('item_id',[8246,11299]);
                                                        }
                                                    })
                                                    ->where('status_cutoff',null)->get();
                    $dataChiller                = Chiller::whereBetween('tanggal_produksi',[$tanggal_awal,$tanggal_akhir])
                                                    ->where(function($q){
                                                        if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                                            $q->whereNotIn('item_id',[4529,11341]);
                                                        }else{
                                                            $q->whereNotIn('item_id',[8246,11299]);
                                                        }
                                                    })
                                                    ->where('status_cutoff',null)->get();

                    // // NYARI ID ABF YANG TERPENGARUH
                    // $coll_abf                   = array();
                    // foreach($dataABF as $abf){
                    //     $coll_abf[]             = $abf->id;
                    // }
                    // $alias_abf                  = $coll_abf;

                    // // NYARI ID CHILLER YANG TERPENGARUH
                    // $coll_chiller               = array();
                    // foreach($dataChiller as $chil){
                    //     $coll_chiller[]         = $chil->id;
                    // }
                    // $alias_chiller              = $coll_chiller;

                    $content =[
                        "tanggal_awal"          => $tanggal_awal,
                        "tanggal_akhir"         => $tanggal_akhir,
                        "tujuan"                => [
                            "chiller"           => $tb_chiller,
                            "abf"               => $tb_abf
                        ],
                        // "affected_id_abf"       => $alias_abf,
                        // "affected_id_chiller"   => $alias_chiller
                    ];

                    $cek_data_abf = Abf::whereBetween('tanggal_masuk',[$tanggal_awal,$tanggal_akhir])
                                    ->where(function($q){
                                        if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                            $q->whereNotIn('item_id',[4529,11341]);
                                        }else{
                                            $q->whereNotIn('item_id',[8246,11299]);
                                        }
                                    })
                                    ->where('status_cutoff',1)->get();
                    $cek_data_chiller = Chiller::whereBetween('tanggal_produksi',[$tanggal_awal,$tanggal_akhir])
                                    ->where(function($q){
                                        if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                            $q->whereNotIn('item_id',[4529,11341]);
                                        }else{
                                            $q->whereNotIn('item_id',[8246,11299]);
                                        }
                                    })
                                    ->where('status_cutoff',1)->get();

                    // if (count($cek_data_abf) > 0 && count($cek_data_chiller) > 0) {
                    //     $response['status'] =   400;
                    //     $response['msg']    =   'Data sudah ditutup';
                    //     return $response;
                    // }

                    // // semua validasi lolos
                    Abf::whereBetween('tanggal_masuk',[$tanggal_awal,$tanggal_akhir])
                        ->where(function($q){
                            if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                $q->whereNotIn('item_id',[4529,11341]);
                            }else{
                                $q->whereNotIn('item_id',[8246,11299]);
                            }
                        })
                        ->update(['status_cutoff' => 1]);
                    Chiller::whereBetween('tanggal_produksi',[$tanggal_awal,$tanggal_akhir])
                            ->where(function($q){
                                if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                    $q->whereNotIn('item_id',[4529,11341]);
                                }else{
                                    $q->whereNotIn('item_id',[8246,11299]);
                                }
                            })
                            ->update([
                                'status_cutoff' => 1
                            ]);

                    $log                        =   new Adminedit();
                    $log->user_id               =   Auth::user()->id ;
                    $log->table_name            =   "chiller,abf";
                    $log->table_id              =   null ;
                    $log->type                  =   'input' ;
                    $log->activity              =   'cut_off';
                    $log->content               =   'Cut Off Periode Tanggal' . ' '  . $tanggal_awal." - ".$tanggal_akhir;
                    $log->data                  =   json_encode([
                            'affected_data'     => $content,
                    ]) ;
                    $log->save();

                    return back()->with('status', 1)->with('message', 'Transaksi data sudah tutup');

                }else{
                    $response['status'] =   400;
                    $response['msg']    =   'Salah satu field tidak terpenuhi';
                    return $response;
                }
            }else if ($tb_abf == TRUE) {

                //GET DATA SEBELUM TERKENA UPDATE
                $dataABF                    = Abf::whereBetween('tanggal_masuk',[$tanggal_awal,$tanggal_akhir])
                                                ->where(function($q){
                                                    if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                                        $q->whereNotIn('item_id',[4529,11341]);
                                                    }else{
                                                        $q->whereNotIn('item_id',[8246,11299]);
                                                    }
                                                })
                                                ->where('status_cutoff',null)->get();

                // NYARI ID ABF YANG TERPENGARUH
                $coll_abf                   = array();
                foreach($dataABF as $abf){
                    $coll_abf[]             = $abf->id;
                }
                $alias_abf                  = $coll_abf;

                $content =[
                    "tanggal_awal"          => $tanggal_awal,
                    "tanggal_akhir"         => $tanggal_akhir,
                    "tujuan"                => [
                        "abf"               => $tb_abf
                    ],
                    // "affected_id_abf"       => $alias_abf
                ];

                if ($tanggal_awal && $tabel && $tanggal_akhir) {
                    $cek_data_abf = Abf::whereBetween('tanggal_masuk',[$tanggal_awal,$tanggal_akhir])
                                    ->where(function($q){
                                        if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                            $q->whereNotIn('item_id',[4529,11341]);
                                        }else{
                                            $q->whereNotIn('item_id',[8246,11299]);
                                        }
                                    })
                                    ->where('status_cutoff',1)->get();

                    // if (count($cek_data_abf) > 0) {
                    //     $response['status'] =   400;
                    //     $response['msg']    =   'Data sudah ditutup';
                    //     return $response;
                    // }
                    // semua validasi lolos
                    Abf::whereBetween('tanggal_masuk',[$tanggal_awal,$tanggal_akhir])
                        ->where(function($q){
                            if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                $q->whereNotIn('item_id',[4529,11341]);
                            }else{
                                $q->whereNotIn('item_id',[8246,11299]);
                            }
                        })
                        ->update(['status_cutoff' => 1]);
                    Chiller::whereBetween('tanggal_produksi',[$tanggal_awal,$tanggal_akhir])
                            ->where(function($q){
                                if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                    $q->whereNotIn('item_id',[4529,11341]);
                                }else{
                                    $q->whereNotIn('item_id',[8246,11299]);
                                }
                            })
                            ->where('kategori',1)
                            ->update(['status_cutoff' => 1]);

                    $log                        =   new Adminedit();
                    $log->user_id               =   Auth::user()->id ;
                    $log->table_name            =   "abf";
                    $log->table_id              =   null ;
                    $log->type                  =   'input' ;
                    $log->activity              =   'cut_off';
                    $log->content               =   'Cut Off Periode Tanggal' . ' '  . $tanggal_awal." - ".$tanggal_akhir;
                    $log->data                  =   json_encode([
                            'affected_data'     => $content,
                    ]) ;
                    $log->save();
                    return back()->with('status', 1)->with('message', 'Transaksi data sudah tutup');

                }else{
                    $response['status'] =   400;
                    $response['msg']    =   'Salah satu field tidak terpenuhi';
                    return $response;
                }
            }else if ($tb_chiller == TRUE) {
                //GET DATA SEBELUM TERKENA UPDATE
                $dataChiller                = Chiller::whereBetween('tanggal_produksi',[$tanggal_awal,$tanggal_akhir])
                                                ->where(function($q){
                                                    if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                                        $q->whereNotIn('item_id',[4529,11341]);
                                                    }else{
                                                        $q->whereNotIn('item_id',[8246,11299]);
                                                    }
                                                })
                                                ->where('status_cutoff',null)->get();

                // NYARI ID CHILLER YANG TERPENGARUH
                $coll_chiller               = array();
                foreach($dataChiller as $chil){
                    $coll_chiller[]         = $chil->id;
                }
                $alias_chiller              = $coll_chiller;

                $content =[
                    "tanggal_awal"          => $tanggal_awal,
                    "tanggal_akhir"         => $tanggal_akhir,
                    "tujuan"                => [
                        "chiller"           => $tb_chiller
                    ],
                    // "affected_id_chiller"   => $alias_chiller
                ];

                if ($tanggal_awal && $tabel && $tanggal_akhir) {
                    $cek_data_chiller = Chiller::whereBetween('tanggal_produksi',[$tanggal_awal,$tanggal_akhir])
                                    ->where(function($q){
                                        if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                            $q->whereNotIn('item_id',[4529,11341]);
                                        }else{
                                            $q->whereNotIn('item_id',[8246,11299]);
                                        }
                                    })
                                    ->where('status_cutoff',1)->get();
                    // if (count($cek_data_chiller) > 0) {
                    //     $response['status'] =   400;
                    //     $response['msg']    =   'Data sudah ditutup';
                    //     return $response;
                    // }
                    // semua validasi lolos
                    Chiller::whereBetween('tanggal_produksi',[$tanggal_awal,$tanggal_akhir])
                            ->where(function($q){
                                if(env('NET_SUBSIDIARY', 'CGL') == 'CGL'){
                                    $q->whereNotIn('item_id',[4529,11341]);
                                }else{
                                    $q->whereNotIn('item_id',[8246,11299]);
                                }
                            })
                            ->update([
                                'status_cutoff' => 1
                            ]);


                    $log                        =   new Adminedit();
                    $log->user_id               =   Auth::user()->id ;
                    $log->table_name            =   "chiller";
                    $log->table_id              =   null ;
                    $log->type                  =   'input' ;
                    $log->activity              =   'cut_off';
                    $log->content               =   'Cut Off Periode Tanggal' . ' '  . $tanggal_awal." - ".$tanggal_akhir;
                    $log->data                  =   json_encode([
                            'affected_data'     => $content,
                    ]) ;
                    $log->save();

                    return back()->with('status', 1)->with('message', 'Transaksi data sudah tutup');

                }else{
                    $response['status'] =   400;
                    $response['msg']    =   'Salah satu field tidak terpenuhi';
                    return $response;
                }
            }else{
                $response['status'] =   400;
                $response['msg']    =   'Tujuan kosong, Gagal menutup data transaksi';
                return $response;
            }
        }
    }
}

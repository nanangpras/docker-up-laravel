<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\Antemortem;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Log;
use App\Models\Lpah;
use App\Models\Postmortem;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Netsuite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LpahController extends Controller
{
    public function index(Request $request)
    {
        if (User::setIjin(3)) {
            $tanggalawal    =   $request->tanggalawal ?? date('Y-m-d');
            $tanggalakhir   =   $request->tanggalakhir ?? date('Y-m-d');
            $tanggal        =   $tanggalakhir;
            $data           =   Production::
                                            where('no_urut', '!=', NULL)
                                            ->whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO LB','PO Maklon']))
                                            ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                            // ->where(function ($query) use ($tanggal) {
                                            //     if ($tanggal != '') {
                                            //         $query->whereDate('prod_tanggal_potong', $tanggal);
                                            //     } else {
                                            //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                                            //     }
                                            // })
                                            ->whereIn('sc_status', [1, 0])
                                            ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                                            ->get();

            $done       =   Production::where('lpah_status', 1)
                            ->whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO LB','PO Maklon']))
                            ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                            // ->where(function ($query) use ($tanggal) {
                            //     if ($tanggal != '') {
                            //         $query->whereDate('prod_tanggal_potong', $tanggal);
                            //     } else {
                            //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                            //     }
                            // })
                            ->count('id');

            $pending    =   Production::where('sc_status', 1)
                            ->whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO LB','PO Maklon']))
                            ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                            // ->where(function ($query) use ($tanggal) {
                            //     if ($tanggal != '') {
                            //         $query->whereDate('prod_tanggal_potong', $tanggal);
                            //     } else {
                            //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                            //     }
                            // })
                            ->count('id');

            $berat      =   Production::where('sc_status', 1)
                            ->whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO LB','PO Maklon']))
                            ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                            // ->where(function ($query) use ($tanggal) {
                            //     if ($tanggal != '') {
                            //         $query->whereDate('prod_tanggal_potong', $tanggal);
                            //     } else {
                            //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                            //     }
                            // })
                            ->sum('sc_berat_do');

            $ekor      =   Production::where('sc_status', 1)
                            ->whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO LB','PO Maklon']))
                            ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                            // ->where(function ($query) use ($tanggal) {
                            //     if ($tanggal != '') {
                            //         $query->whereDate('prod_tanggal_potong', $tanggal);
                            //     } else {
                            //         $query->whereDate('prod_tanggal_potong', Carbon::now());
                            //     }
                            // })
                            ->sum('sc_ekor_do');

            $mobil_lama       =   Production::where('no_urut', '!=', NULL)
                                            ->whereIn('purchasing_id', Purchasing::select('id')
                                                    ->whereIn('type_po', ['PO LB','PO Maklon'])
                                                    ->where(function ($query) use ($tanggal) {
                                                        $query->whereDate('tanggal_potong', date('Y-m-d', strtotime('yesterday')));
                                                    })
                                                )
                                            ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                            ->where('sc_status', 1)
                                            ->whereIn('lpah_status', [NULL, 1, 2])
                                            ->whereIn('lpah_tanggal_potong', [date('Y-m-d')])
                                            ->get();


            $hitung =   [
                'done'          =>  $done,
                'pending'       =>  $pending,
                'berat_total'   =>  $berat,
                'total_ekor'    =>  $ekor,
            ];

            if ($request->key == 'history_timbang_checker'){
                $id = $request->id;

                return view('admin.pages.lpah.form.history-edit-timbang',compact('id'));
            }
            if ($request->key == 'history_lpah_cheker'){
                $id = $request->id;

                return view('admin.pages.lpah.form.history-lpah',compact('id'));
            }
            if ($request->key == 'history_edit_lpah'){
                $id = $request->id;

                return view('admin.pages.lpah.form.history-lpah',compact('id'));
            }
        
            if($request->key == 'penerimaanlpah'){
                return view('admin.pages.lpah.component.dataLpah', compact( 'data','mobil_lama'));
            }
            if($request->key == 'hitungtotal'){
                return view('admin.pages.lpah.component.hitungtotal', compact( 'hitung'));
            }
            return view('admin/pages/lpah/index', compact( 'data', 'hitung','tanggalawal','tanggalakhir', 'mobil_lama'));
            
            
        } 
        return redirect()->route("index");

    }

    public function store(Request $request)
    {
        if (User::setIjin(3)) {
            if (($request->key == 'selesai') || ($request->key == 'simpan')) {
                DB::beginTransaction();

                $lpah           =   Production::find($request->x_code);

                if ($lpah->ekoran_seckle == 0) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Ekoran Sekle belum diinputkan');
                }

                if ($lpah->lpah_user_nama == "" || $lpah->lpah_user_nama == NULL) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Nama petugas belum diinputkan');
                }

                if ($lpah->po_jenis_ekspedisi == "tangkap") {
                    $susut      =   $lpah->sc_berat_do - $lpah->berat_bersih_lpah;
                    $persen     =   round(((($lpah->sc_berat_do - $lpah->berat_bersih_lpah) / $lpah->sc_berat_do) * 100), 2);
                } else {
                    $susut      =   $lpah->sc_berat_do - $lpah->berat_bersih_lpah - $lpah->qc_berat_ayam_mati;
                    $persen     =   round(((($lpah->sc_berat_do - $lpah->berat_bersih_lpah - $lpah->qc_berat_ayam_mati) / $lpah->sc_berat_do) * 100), 2);
                }


                if ($lpah->prodpur->type_po == "PO Maklon") {
                    $susut      =   $lpah->sc_berat_do - $lpah->berat_bersih_lpah;
                    $persen     =   round(((($lpah->sc_berat_do - $lpah->berat_bersih_lpah) / $lpah->sc_berat_do) * 100), 2);
                }

                // $lpah->lpah_tanggal_potong          =   date("Y-m-d");
                // $lpah->lpah_jam_potong              =   date("H:i:s");
                $lpah->lpah_berat_kotor             =   $lpah->berat_lpah;
                $lpah->lpah_berat_susut             =   $susut;
                $lpah->lpah_persen_susut            =   $persen;

                // RUMUS BARU
                // Subtotal berat = (Ayam + Keranjang) - keranjang kosong - ayam mati - basah bulu
                // rerata = sub total berat / (ekor lpah+ayam merah)
                $lpah->lpah_rerata_terima           =   round($lpah->berat_bersih_lpah / ($lpah->ekoran_seckle + $lpah->qc_ekor_ayam_merah), 1);

                // berat ayam merah = rerata * ekor ayam merah
                $lpah->qc_berat_ayam_merah          =   $lpah->qc_ekor_ayam_merah * round($lpah->berat_bersih_lpah / ($lpah->ekoran_seckle + $lpah->qc_ekor_ayam_merah), 1);
                
                // berat terima = Subtotal berat - berat ayam merah
                $lpah->lpah_berat_terima            =   round($lpah->berat_bersih_lpah - ($lpah->qc_ekor_ayam_merah * round($lpah->berat_bersih_lpah / ($lpah->ekoran_seckle + $lpah->qc_ekor_ayam_merah), 1)),1);

                // dd (round($lpah->lpah_berat_terima / ($lpah->ekoran_seckle + $lpah->qc_ekor_ayam_merah), 2), $lpah->qc_ekor_ayam_merah * round($lpah->lpah_berat_terima / ($lpah->ekoran_seckle + $lpah->qc_ekor_ayam_merah), 2), $lpah->berat_bersih_lpah - ($lpah->qc_ekor_ayam_merah * round($lpah->lpah_berat_terima / ($lpah->ekoran_seckle + $lpah->qc_ekor_ayam_merah), 2)));

                $lpah->lpah_user_id                 =   Auth::user()->id;
                $lpah->lpah_berat_keranjang         =   Lpah::where('production_id', $lpah->id)->where('type', 'kosong')->sum('berat');
                $lpah->lpah_status                  =   ($request->key == 'simpan') ? 3 : 1;

                if (!$lpah->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                if ($request->key == 'selesai') {
                    $sync               =   $lpah;
                    $sync['data_lpah']  =   Lpah::where('production_id', $request->x_code)->get();

                    $adminlog               =   new Adminedit;
                    $adminlog->user_id      =   Auth::user()->id;
                    $adminlog->table_name   =   'productions';
                    $adminlog->table_id     =   $lpah->id;
                    $adminlog->type         =   'edit';
                    $adminlog->activity     =   'lpah';
                    // $adminlog->content      =   'Memperbaharui data LPAH';
                    $adminlog->content      =   'Menyelesaikan proses LPAH' ;
                    $adminlog->status       =   1;
                    $adminlog->data         = json_encode([
                        'header'            => $lpah,
                    ]) ; 
                    if (!$adminlog->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }

                    DB::commit();
                    return back()->with('status', 1)->with('message', 'LPAH berhasil diselesaikan');
                } else {

                    // $adminlog               =   new Adminedit ;
                    // $adminlog->user_id      =   Auth::user()->id ;
                    // $adminlog->table_name   =   'productions' ;
                    // $adminlog->table_id     =   $lpah->id ;
                    // $adminlog->type         =   'input' ;
                    // $adminlog->activity     =   'lpah' ;
                    // // $adminlog->content      =   'Memperbaharui data LPAH';
                    // // $adminlog->content      =   'Menyelesaikan proses LPAH' ;
                    // $adminlog->status       =   1 ;
                    // $adminlog->data         = json_encode([
                    //     'header'            => $lpah,
                    // ]) ;
                    // if (!$adminlog->save()) {
                    //     DB::rollBack();
                    //     return back()->with('status', 2)->with('message', 'Proses gagal');
                    // }

                    DB::commit();
                    return back()->with('status', 1)->with('message', 'LPAH berhasil disimpan');
                }

            } else {
                $lpah                       =   Production::find($request->x_code);
                $lpah->lpah_tanggal_potong  =   Carbon::now();
                $lpah->lpah_jam_bongkar     =   Carbon::now();
                $lpah->lpah_status          =   2;
                $lpah->save();

                if($lpah->lpah_tanggal_potong!=$lpah->prod_tanggal_potong){
                    $lpah->prod_tanggal_potong  = $lpah->lpah_tanggal_potong;
                    // $lpah->prod_pending         = "1";
                    $lpah->save();
                }

                return redirect()->route('lpah.show', $lpah->id)->with('status', 1)->with('message', 'Selesaikan penerimaan masuk penerimaan ayam hidup');
            }
        }
        return redirect()->route("index");
    }

    public function show(Request $request, $id)
    {
        if (User::setIjin(3)) {
            if ($request->key == 'timbangisi') {
                $data       =   Lpah::where('type', 'isi')
                    ->where('production_id', $id)
                    ->orderBy('id', 'DESC')
                    ->get();

                $produksi   =   Production::select('lpah_status')
                    ->where('id', $id)
                    ->first();

                return view('admin.pages.lpah.isi', compact('data', 'produksi'));
            } else

            if ($request->key == 'timbangkosong') {
                $data       =   Lpah::where('type', 'kosong')
                                ->where('production_id', $id)
                                ->orderBy('id', 'DESC')
                                ->get();

                $produksi   =   Production::select('lpah_status')
                                ->where('id', $id)
                                ->first();

                return view('admin.pages.lpah.kosong', compact('data', 'produksi'));
            } else

            if ($request->key == 'info') {
                $data   =   Production::find($id);
                return view('admin.pages.lpah.info', compact('data'));
            } else
            if ($request->key == 'edit_checker') {
                if (User::setIjin(33)) {
                    $data   =   Production::where('id', $id)
                                ->first();

                    return view('admin.pages.lpah.show', compact('data'));
                }
                return redirect()->route('lpah.index')->with('status', 2)->with('message', 'Anda tidak memiliki akses ke halaman tersebut');
            
            } else {
                $data   =   Production::where('id', $id)
                            ->whereIn('lpah_status', [1, 2, 3])
                            ->first();

                if ($data) {
                    if ($data->lpah_status == 2) {
                        return view('admin.pages.lpah.show', compact('data'));
                    }
                    if (($data->lpah_status == 1) || ($data->lpah_status == 3)) {
                        $ceklogedit = Adminedit::where('table_id', $id)->where('table_name', 'productions')->where('type', 'edit')->count();
                        return view('admin.pages.lpah.detail', compact('data','ceklogedit'));
                    }
                }

                return redirect()->route('lpah.index')->with('status', 2)->with('message', 'Data tidak ditemukan');
            }
        }
        return redirect()->route("index");
    }

    public function add(Request $request, $id)
    {
        if (User::setIjin(3)) {
            $data   =   Production::select('id')
                ->whereIn('lpah_status', [1, 2,3])
                ->where('id', $id)
                ->first();

            if ($data) {
                $lpah                   =   new Lpah;
                $lpah->berat            =   $request->berat;
                $lpah->type             =   $request->type;
                $lpah->production_id    =   $id;
                $lpah->save();
            }
        }
        return redirect()->route("index");
    }

    public function rollback($id)
    {
        if (User::setIjin(3)) {
            $data   =   Production::where('id', $id)
                ->where('lpah_status', 1)
                ->first();

            if ($data) {
                DB::beginTransaction();

                $data->lpah_status  =   2;
                if (!$data->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                $netsuite   =   Netsuite::where('tabel', 'productions')
                                ->where('tabel_id', $data->id)
                                ->where('record_type', 'itemreceipt')
                                ->first();

                $netsuite->status   =   0;
                if (!$netsuite->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                DB::commit();

                return back()->with('status', 1)->with('message', 'Silahkan lakukan pembaharuan data penerimaan masuk');
            }

            return back()->with('status', 2)->with('message', 'Data penerimaan masuk tidak ditemukan');
        }
        return redirect()->route("index");
    }

    public function cart($id)
    {
        if (User::setIjin(3)) {
            $produksi   =   Production::find($id);
            return view('admin.pages.lpah.keranjang', compact('produksi'));
        }
        return redirect()->route("index");
    }

    public function susut($id)
    {
        $susut  =   Production::find($id);
        return view('admin.pages.lpah.susut', compact('susut'));
    }

    public function jambongkar(Request $request, $id)
    {
        $data                   =   Production::find($id) ;
        $data->lpah_user_nama   =   $request->nama_petugas ?? NULL ;
        $data->lpah_jam_bongkar =   $request->jam_bongkar ;
        $data->sc_jam_masuk     =   $request->sc_jam_masuk ;
        $data->lpah_jam_selesai =   $request->jam_selesai ;
        $data->save();
    }

    public function updatesusut(Request $request, $id)
    {

        if (User::setIjin(3)) {
            // dd($request->all());
            $data   =   Production::find($id);
            $ceklog = Adminedit::where('table_id',$id)->where('table_name','productions')->where('type','edit')->count();
            
            $produksi                               =   Production::find($id);
            $produksi->ekoran_seckle                =   $request->ekor_seckle;
            $produksi->qc_ekor_ayam_mati            =   $request->mati;
            $produksi->qc_berat_ayam_mati           =   $request->matikg;
            $produksi->ekoran_seckle                =   $request->ekoran_seckle;
            $produksi->lpah_jumlah_keranjang        =   $request->keranjang;


            // REQUEST BARU UNTUK PENGURANGAN MELALUI QTY AYAM MERAH
            // $request->hitungAyam == 1 ? $produksi->qc_hitung_ayam_merah = 1 : $produksi->qc_hitung_ayam_merah = 0;

            $produksi->qc_ekor_ayam_merah           =   $request->ekorayammerah;
            if ($request->ekorayammerah != NULL || $request->ekorayammerah != 0) {
                $produksi->qc_berat_ayam_merah          =   $request->ekorayammerah * $produksi->lpah_rerata_terima;
                $produksi->qc_hitung_ayam_merah = 1;
            } else {
                $produksi->qc_hitung_ayam_merah = 0;
            }
            
            // $produksi->qc_berat_ayam_merah          =   $request->ayammerah;


            $produksi->lpah_kebersihan_keranjang    =   $request->kebersihanKeranjang;
            $produksi->lpah_downtime                =   Production::hitung_downtime($produksi->lpah_jam_bongkar, $produksi->lpah_jam_selesai, $request->ekoran_seckle);
            if(env('NET_SUBSIDIARY', 'CGL')=='CGL'){
                $produksi->qc_tembolok              =   $request->tembolok;
            }

            $produksi->save();

            if($data->berat_bersih_lpah>0){
                $isi        =   Lpah::where('production_id', $id)->where('type', 'isi')->sum('berat');
                $kosong     =   Lpah::where('production_id', $id)->where('type', 'kosong')->sum('berat');
                $basah      =   Antemortem::select('basah_bulu')
                                ->where('production_id', $id)
                                ->first();

                if ($basah) {
                    $bersih     =   ($isi - $kosong - ($data->po_jenis_ekspedisi == 'kirim' ? ($produksi->qc_berat_ayam_mati - $basah->basah_bulu) : $basah->basah_bulu )) ;
                } else {
                    $bersih     =   0 ;
                }

                $produksi->qc_persen_ayam_mati  =   $bersih > 0 ? round((100 - ((($bersih - $produksi->qc_berat_ayam_mati) / $bersih) * 100)), 2) : 0;
            }

            $produksi->save();

            if ($ceklog < 1) {
                $log             = new Adminedit();

                $log->user_id    = Auth::user()->id ;
                $log->table_id   = $id;
                $log->table_name = 'productions';
                $log->type       = 'edit';
                $log->activity   = 'lpah';
                $log->content    = 'Data awal LPAH';
                $log->data       = json_encode([
                    'header' => $data,
                ]);
                if (!$log->save()) {
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }
            }

            $logadmin               =   Adminedit::where('table_name', 'productions')->where('table_id',$id)->where('type', 'edit')->where('content','!=','Data awal LPAH')->count() ;
            $logupdate              =   new Adminedit();
            $logupdate->table_id    = $id;
            $logupdate->user_id     = Auth::user()->id ;;
            $logupdate->table_name  = 'productions';
            $logupdate->type        = 'edit';
            $logupdate->activity    = 'lpah';
            $logupdate->content     = 'Data Edit LPAH - '.($logadmin+1);
            $logupdate->data        = json_encode([
                'header' => $produksi,
            ]);

            if (!$logupdate->save()) {
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }
            

            $antem                              =   Antemortem::where('production_id', $id)->first() ?? new Antemortem;
            $antem->qc_id                       =   Auth::user()->id;
            $antem->ayam_mati                   =   $produksi->qc_ekor_ayam_mati;
            $antem->ayam_mati_kg                =   $produksi->qc_berat_ayam_mati;
            $antem->production_id               =   $data->id;
            $antem->save();

            $postm                              =   Postmortem::where('production_id', $id)->first() ?? new Postmortem;
            $postm->qc_id                       =   Auth::user()->id;
            $postm->production_id               =   $data->id;
            $postm->ayam_merah                  =   $request->ayammerah;
            $postm->save();
        }
        return redirect()->route("index");
    }

    public function update(Request $request, $id)
    {
        if (User::setIjin(3)) {
            // UPDATE SUSUT
            if ($request->key == 'updateurut') {
                $produksi   =   Production::find($request->id);

                if ($produksi) {
                    $produksi->no_urut  =   $request->nourut;
                    $produksi->save();

                    return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
                }

                return back()->with('status', 2)->with('message', 'Data tidak ditemukan');
            } else

            // EDIT KERANJANG
            if ($request->key == 'editkeranjang') {
                $data   =   Production::whereIn('lpah_status', [1, 2, 3])
                            ->where('id', $id)
                            ->first();

                if ($data) {
                    DB::beginTransaction() ;

                    $lpah           =   Lpah::find($request->row_id);
                    if ($request->act == 'checker') {
                    $old            =   Lpah::find($request->row_id);
                    }
                    $lpah->berat    =   $request->berat;
                    $lpah->type     =   $request->tipe_timbang;
                    if (!$lpah->save()) {
                        DB::rollBack() ;
                        $return['status']   =   400 ;
                        $return['msg']      =   "Update gagal" ;
                        return $return ;
                    }

                    if ($request->act == 'checker') {
                        $json   =   [
                            'item_lama' =>  $old,
                            'item_baru' =>  $lpah
                        ];

                        $edit                       =   new Adminedit;
                        $edit->user_id              =   Auth::user()->id;
                        $edit->table_name           =   'lpah';
                        $edit->table_id             =   $lpah->id;
                        $edit->activity             =   'checker';
                        $edit->content              =   'EDIT TIMBANGAN LPAH';
                        $edit->type                 =   'edit';
                        $edit->data                 =   json_encode($json);
                        $edit->key                  =   $data->id;
                        $edit->status               =   1;
                        if (!$edit->save()) {
                            DB::rollBack();
                            $return['status']   =   400;
                            $return['msg']      =   "Update gagal";
                            return $return;
                        }
                    }

                    DB::commit() ;
                    $return['status']   =   200;
                    $return['msg']      =   "Update berhasil";
                    return $return;
                }

                $return['status']   =   400;
                $return['msg']      =   "Update gagal";
                return $return;

            } else {
                $data   =   Production::where('lpah_status', 2)
                            ->where('id', $id)
                            ->first();

                if ($data) {

                    if($data->po_jenis_ekspedisi == "tangkap"){
                        $susut      =   $data->sc_berat_do - $data->berat_bersih_lpah;
                        $persen     =   round(((($data->sc_berat_do - $data->berat_bersih_lpah) / $data->sc_berat_do) * 100), 2);
                    }else{
                        $susut      =   $data->sc_berat_do - $data->berat_bersih_lpah - $data->qc_berat_ayam_mati;
                        $persen     =   round(((($data->sc_berat_do - $data->berat_bersih_lpah - $data->qc_berat_ayam_mati) / $data->sc_berat_do) * 100), 2);
                    }

                    if($data->prodpur->type_po=="PO Maklon"){
                        $susut      =   $data->sc_berat_do - $data->berat_bersih_lpah;
                        $persen     =   round(((($data->sc_berat_do - $data->berat_bersih_lpah) / $data->sc_berat_do) * 100), 2);
                    }

                    DB::beginTransaction();

                    $lpah                               =   Production::find($data->id);
                    // $lpah->lpah_tanggal_potong          =   date("Y-m-d");
                    // $lpah->lpah_jam_potong              =   date("H:i:s");
                    $lpah->lpah_berat_kotor             =   $data->berat_lpah;
                    $lpah->lpah_berat_susut             =   $susut;
                    $lpah->lpah_persen_susut            =   $persen;
                    $lpah->lpah_berat_terima            =   $data->berat_bersih_lpah;
                    $lpah->lpah_rerata_terima           =   round($lpah->berat_bersih_lpah / $lpah->ekoran_seckle, 2);
                    $lpah->lpah_user_id                 =   Auth::user()->id;
                    $lpah->lpah_berat_keranjang         =   Lpah::where('production_id', $data->id)->where('type', 'kosong')->sum('berat');
                    $lpah->lpah_status                  =   1;

                    if (!$lpah->save()) {
                        DB::rollBack();
                        return redirect()->route('lpah.index')->with('status', 2)->with('message', 'Proses gagal');
                    }

                    DB::commit();
                    return redirect()->route('lpah.index')->with('status', 1)->with('message', 'Penerimaan masuk berhasil diselesaikan');
                }

                return redirect()->route('lpah.index')->with('status', 2)->with('message', 'Terjadi kesalahan saat menyelesaikan penerimaan masuk');
            }

        }
        return redirect()->route("index");
    }


    public function updateDo(Request $request)
    {
        if (User::setIjin(3)) {

            $produksi                       =   Production::find($request->id);

            if ($produksi) {
                $produksi->sc_berat_do              =   $request->sc_berat_do;
                $produksi->sc_ekor_do               =   $request->sc_ekor_do;
                $produksi->sc_rerata_do             =   number_format((($request->sc_berat_do / $request->sc_ekor_do) ?? 0), 2);
                $produksi->save();

                return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
            }

            return back()->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route("index");
    }

    public function laporan(Request $request)
    {
        $tanggal_mulai      =   $request->tanggal_mulai ?? date('Y-m-d');
        $tanggal_selesai    =   $request->tanggal_selesai ?? date('Y-m-d');
        $produksi           =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')
                                ->whereBetween('tanggal_potong', [$tanggal_mulai, $tanggal_selesai]))
                                ->where('lpah_status', '1')
                                ->where('no_lpah', '!=', NULL)
                                ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                                ->get();

        return view('admin.pages.bukubesar.laproan', compact('produksi', 'tanggal_mulai', 'tanggal_selesai'));
    }

    public function laporanayammerah(Request $request){
        $tanggal_mulai      = $request->tanggal_mulai ?? date("Y-m-d");
        $tanggal_selesai    = $request->tanggal_selesai ?? date("Y-m-d");
        $jenis_ekspedisi    = $request->jenis_ekspedisi ?? 'all';
        
        if($request->key == "showData"){
            $produksi           =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')
                                                ->whereBetween('tanggal_potong', [$tanggal_mulai, $tanggal_selesai]))
                                                ->where(function($q) use ($jenis_ekspedisi){
                                                    if($jenis_ekspedisi != 'all'){
                                                        $q->where('po_jenis_ekspedisi',$jenis_ekspedisi);
                                                    }
                                                })
                                                ->where('lpah_status', '1')
                                                ->where('no_lpah', '!=', NULL)
                                                ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                                                ->get();
        
            $download   = false;
            $hidden     = true;
            return view('admin.pages.purchasing.penerimaan.view_ayam_merah',compact('tanggal_mulai','tanggal_selesai','jenis_ekspedisi','produksi','download','hidden'));
        }
        if($request->key == "download"){
            $produksi           =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB')
                                            ->whereBetween('tanggal_potong', [$tanggal_mulai, $tanggal_selesai]))
                                            ->where(function($q) use ($jenis_ekspedisi){
                                                if($jenis_ekspedisi != 'all'){
                                                    $q->where('po_jenis_ekspedisi',$jenis_ekspedisi);
                                                }
                                            })
                                            ->where('lpah_status', '1')
                                            ->where('no_lpah', '!=', NULL)
                                            ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                                            ->get();
            $download = true;
            $hidden   = false;
            return view('admin.pages.purchasing.penerimaan.view_ayam_merah',compact('tanggal_mulai','tanggal_selesai','jenis_ekspedisi','produksi','download','hidden'));
        }

        return view('admin.pages.purchasing.penerimaan.index',compact('tanggal_mulai','tanggal_selesai'));
    }

    public function inject_susut()
    {
        $data   =   Production::where('lpah_status', 1)
                    ->get();

        foreach ($data as $row) {

            $lpah   = $row;
            $persen = 0;
            if($row->po_jenis_ekspedisi == "tangkap"){
                $susut      =   $lpah->sc_berat_do - $lpah->berat_bersih_lpah;
                if($lpah->sc_berat_do>0){
                    $persen     =   round(((($lpah->sc_berat_do - $lpah->berat_bersih_lpah) / $lpah->sc_berat_do) * 100), 2);
                }
            }else{
                $susut      =   $lpah->sc_berat_do - $lpah->berat_bersih_lpah - $lpah->qc_berat_ayam_mati;
                if($lpah->sc_berat_do>0){
                    $persen     =   round(((($lpah->sc_berat_do - $lpah->berat_bersih_lpah - $lpah->qc_berat_ayam_mati) / $lpah->sc_berat_do) * 100), 2);
                }
            }

            if($row->prodpur->type_po=="PO Maklon"){
                $susut      =   $lpah->sc_berat_do - $lpah->berat_bersih_lpah;
                if($lpah->sc_berat_do>0){
                    $persen     =   round(((($lpah->sc_berat_do - $lpah->berat_bersih_lpah) / $lpah->sc_berat_do) * 100), 2);
                }
            }

            if($row->berat_bersih_lpah>0){
                $row->qc_persen_ayam_mati       =   round((100 - ((($row->berat_bersih_lpah - $row->qc_berat_ayam_mati) / $row->berat_bersih_lpah) * 100)), 2);
                $isi        =   Lpah::where('production_id', $row->id)->where('type', 'isi')->sum('berat');
                $kosong     =   Lpah::where('production_id', $row->id)->where('type', 'kosong')->sum('berat');
                $basah      =   Antemortem::select('basah_bulu')
                                ->where('production_id', $row->id)
                                ->first();

                $bersih     =   ($isi - $kosong - ($row->po_jenis_ekspedisi == 'kirim' ? ($row->qc_berat_ayam_mati - $basah->basah_bulu) : $basah->basah_bulu));

                $row->qc_persen_ayam_mati   =   round((100 - ((($bersih - $row->qc_berat_ayam_mati) / $bersih) * 100)), 2);
            }

            $row->lpah_berat_susut          =   $susut;
            $row->lpah_persen_susut         =   $persen;

            $row->save() ;
        }

        echo "Sukses" ;
    }


    public function inject_rerata()
    {
        $data   =   Production::where('lpah_status', 1)
                    ->get();

        foreach ($data as $row) {
            $row->lpah_rerata_terima    =   round($row->berat_bersih_lpah / $row->ekoran_seckle, 2) ;
            $row->save() ;
        }

        echo "Sukses" ;
    }

    public function inject_ayammerah()
    {
        foreach (Production::get() as $row) {
            if (($row->qc_ekor_ayam_merah) AND (!$row->qc_berat_ayam_merah)) {
                $row->qc_berat_ayam_merah   =   $row->qc_ekor_ayam_merah ;
                $row->save() ;

                $row->qc_ekor_ayam_merah    =   NULL ;
                $row->save();
            }
        }

        echo "sukses";
    }

    public function updatedata(Request $request){
        // if (User::setIjin(33)) {
            $produksi                               = Production::find($request->id);
            
            DB::beginTransaction();

            $produksi->sc_berat_do                  =   $request->berat_do;
            $produksi->sc_ekor_do                   =   $request->total_do;
            $produksi->ekoran_seckle                =   $request->ekoran_seckle;
            
            if ($produksi->save()) {
                $proceed    =   true;
            }

            $logadmin                           = Adminedit::where('table_name', 'productions')->where('table_id',$request->id)->where('type', 'edit')->where('content','!=','Data awal LPAH')->count() ;
            
            $updated                            = $produksi->setAppends([]);
            $logupdate                          = new Adminedit ;
            $logupdate->user_id                 = Auth::user()->id ;
            $logupdate->table_name              = 'productions' ;
            $logupdate->table_id                = $request->id ;
            $logupdate->type                    = 'edit' ;
            $logupdate->activity                = 'lpah' ;
            $logupdate->content                 = 'Data Edit LPAH - '.($logadmin+1);
            $logupdate->data                    = json_encode([
                    'header'                    => $updated,
            ]) ;
            
            $logupdate->save();

            if (!$logupdate->save()) {
                $result['status']               =   400;
                $result['msg']                  =   "Update failed";
                return $result;
            }

            if ($proceed == true) {
                DB::commit();
                $result['msg']                      =   "Ubah Data Berhasil, Halaman Akan Direfresh";
                return $result;
            } else {
                DB::rollBack();
                $result['status']                   =   400;
                $result['msg']                      =   "Update failed";
                return $result;
            }
            
        // }
    }
}
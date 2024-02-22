<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Adminedit;
use App\Models\Antemortem;
use App\Models\Driver;
use App\Models\Grading;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Log;
use App\Models\Netsuite;
use App\Models\Product_gudang;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\User;
use App\Rules\PilihSupir;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Null_;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        if (User::setIjin(2)) {
            if ($request->key == 'supir') {
                return Driver::find($request->id) ;
            }else{
                $tanggal            =   $request->tanggal ?? date('Y-m-d');
                $purchase           =   Purchasing::select('id', 'supplier_id', 'type_ekspedisi', 'ukuran_ayam', 'jenis_po', 'type_po', 'no_po', 'item_po','tanggal_potong')
                                        ->whereIn('status', [2])
                                        ->whereIn('type_po', ['PO Maklon','PO LB'])
                                        ->whereDate('tanggal_potong', $tanggal)
                                        ->get();

                $purchase_lainnya   =   Purchasing::select('id', 'supplier_id', 'type_ekspedisi', 'ukuran_ayam', 'jenis_po', 'type_po', 'no_po', 'jumlah_po', 'item_po')
                                        ->whereIn('status', [2])
                                        ->whereIn('type_po', ['PO Karkas', 'PO Evis', 'PO Boneles', 'PO Non Karkas'])
                                        ->whereDate('tanggal_potong', $tanggal)
                                        ->get();

                $purchlain          =   Purchasing::where('status', 2)
                                        ->whereIn('type_po', ['PO Karkas', 'PO Frozen', 'PO Maklon', 'PO Evis', 'PO Non Karkas'])
                                        ->whereDate('tanggal_potong', $tanggal)
                                        ->get();

                $diterima           =   Production::where('sc_status', '!=', NULL)
                                        ->whereIn('purchasing_id', Purchasing::select('id')
                                            ->whereIn('type_po', ['PO LB', 'PO Maklon'])
                                        )
                                        ->where('prod_tanggal_potong', $tanggal)
                                        ->orderBy('no_urut', 'ASC')
                                        ->get();

                $diterimanonlb      =   Production::where('sc_status', '!=', NULL)
                                        ->whereIn('purchasing_id', Purchasing::select('id')
                                        ->whereNotIn('type_po', ['PO LB', 'PO Maklon'])
                                        )
                                        ->where('prod_tanggal_potong', $tanggal)
                                        ->orderBy('no_urut', 'ASC')
                                        ->get();

                $pending            =   Production::where('sc_status', NULL)
                                        ->whereIn('purchasing_id', Purchasing::select('id'))
                                        ->where('prod_tanggal_potong', $tanggal)
                                        ->get();

                $supir              =   Driver::where('driver_exspedisi', 1)->pluck('nama', 'id');
                $view               =   Session::get('driver') ? (Session::get('driver') == 'tangkap' ? true : false) : false;

                $cekEmailProduksi   = false;
                $cekEmail           =  Auth::user()->name;
                $cekTrueEmail   = str_contains($cekEmail, 'Admin Produksi');
                if ($cekTrueEmail) {
                    $cekEmailProduksi = true;
                }

                return view('admin.pages.security', compact('purchase', 'diterima', 'pending', 'supir', 'tanggal', 'purchase_lainnya', 'purchlain','diterimanonlb', 'view', 'cekEmailProduksi'));
            }
        }
        return redirect()->route("index");
    }

    public function AutoCompleteData(Request $request){
        $master         = Production::where('purchasing_id',$request->auto_id)->get();
        if ($master->count() > 0) {
            foreach ($master as $t) {
                $data['sc_nama_kandang']    = $t->sc_nama_kandang;
                $data['sc_alamat_kandang']  = $t->sc_alamat_kandang;
                return response()->json($data);
            }
        } else {
            $data['sc_nama_kandang']        = '';
            $data['sc_alamat_kandang']      = '';
            return response()->json($data);
        }
    }
    public function searchDriver(Request $request)
    {
        $search = $request->cari;
      
        $driver = Driver::select('nama')
                    ->where('nama', 'LIKE', '%'. $search. '%')
                    ->groupBy('nama')
                    ->orderBy('nama','DESC')
                    ->get();
        $arr = array();
        if ($driver->count() > 0) {
            foreach ($driver as $key => $value) {
                $arr[] = $value->nama;
            }
        } else {
            $arr = '';
        }

        // return $arr;
 
        return response()->json($arr);
    }

    public function store(Request $request)
    {
        // if (User::setIjin(2)) {
            if ($request->key == 'tukar_supplier') {
                $prod                   =   Production::find($request->x_code);

                $change                 =   Production::where('no_urut', $request->no_urut)
                                            ->whereDate('prod_tanggal_potong', $prod->prod_tanggal_potong)
                                            ->first() ;

                $old                    =   Production::where('no_urut', $request->no_urut)
                                            ->whereDate('prod_tanggal_potong', $prod->prod_tanggal_potong)
                                            ->first() ;
                
                if ($change && $old) {
                    $change->purchasing_id      =   $prod->purchasing_id ;
                    $change->no_po              =   $prod->no_po ;
                    $change->sc_nama_kandang    =   $prod->sc_nama_kandang ;
                    $change->sc_alamat_kandang  =   $prod->sc_alamat_kandang ;
                    $change->sc_wilayah         =   $prod->sc_wilayah ;
                    $change->po_jenis_ekspedisi =   $prod->po_jenis_ekspedisi ;
                    // $change->sc_ekor_do         =   $prod->sc_ekor_do ;
                    // $change->sc_berat_do        =   $prod->sc_berat_do ;
                    // $change->sc_rerata_do       =   $prod->sc_rerata_do ;
    
                    $prod->purchasing_id        =   $old->purchasing_id ;
                    $prod->no_po                =   $old->no_po ;
                    $prod->sc_nama_kandang      =   $old->sc_nama_kandang ;
                    $prod->sc_alamat_kandang    =   $old->sc_alamat_kandang ;
                    $prod->sc_wilayah           =   $old->sc_wilayah ;
                    $prod->po_jenis_ekspedisi   =   $old->po_jenis_ekspedisi ;
                    // $prod->sc_ekor_do           =   $old->sc_ekor_do ;
                    // $prod->sc_berat_do          =   $old->sc_berat_do ;
                    // $prod->sc_rerata_do         =   $old->sc_rerata_do ;
    
                    // dump($change);
                    // dd($prod);
                    $change->save();
                    $prod->save();
                    return back()->with('status', 1)->with('message', 'Berhasil Update');
                } else {
                    return back()->with('status', 2)->with('message', 'Nomor urut ' .$request->no_urut.' tidak ada');
                }

            } else {

                DB::beginTransaction();

                $berat_replace  =   (float)str_replace(',', '.', $request->berat_do);
                $purchas        =   Purchasing::find($request->purchase);
                $no_polisi      =   strtoupper(str_replace(" ", "", $request->no_polisi));

                if ($purchas) {
                    Session::flash("driver", $purchas->type_ekspedisi);
                } else {
                    DB::rollBack() ;
                    return back()->with('status', 2)->with('message', 'Silahkan pilih ekspedisi'); 
                }
                $validator  =    Validator::make($request->all(), [
                    "purchase"      =>  [ Rule::exists('purchasing', 'id')->whereIn('status', [1, 2]) ],
                    "supir"         =>  [ new PilihSupir($purchas) ],
                    "berat_do"      =>  'required',
                    "no_polisi"     =>  'required|string|max:45',
                ]);

                if ($validator->fails()) {
                    DB::rollBack() ;
                    return back()->with('status', 2)->with('message', 'Data tidak lengkap. Silahkan ulangi kembali');
                }

                $produksi        =   Production::find($request->production);
                
                if ($purchas->type_ekspedisi == 'tangkap') {
                    $driver                     =   Driver::where('id', $request->supir)->first();
                    
                    $produksi->sc_pengemudi     =   $driver->nama ?? $request->supir;
                    $produksi->sc_pengemudi_id  =   $driver->id ?? NULL;
                    
                } else {
                    $driver          =   Driver::where('nama', 'like', '%' . $request->supir . '%')->first();
                    $data   =   Production::select('id')
                                ->where('purchasing_id', $request->purchase)
                                ->where('no_urut', NULL)
                                ->orderBy('id', 'ASC')
                                ->limit(1)
                                ->first();

                    if (!$data) {
                        $p          =   Purchasing::find($request->purchase);
                        $p->status  =   1;
                        if (!$p->save()) {
                            DB::rollBack() ;
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }
                        DB::commit();

                        return back()->with('status', 2)->with('message', 'Data kosong');
                    }

                    $produksi                   =   Production::find($data->id);
                    $produksi->sc_pengemudi     =   $driver->nama ?? $request->supir;
                    $produksi->sc_pengemudi_id  =   $driver->id ?? NULL;
                }

                if ($purchas->type_po == 'PO LB' or $purchas->type_po == 'PO Maklon') {

                    $rerata =   ($request->ekor_do > 0) ? ($berat_replace / $request->ekor_do) : 0 ;
                    $notif =    0 ;

                    if ($purchas->ukuran_ayam == '0.5 - 0.7') {
                        $notif = '0.5';
                    } elseif ($purchas->ukuran_ayam == '0.6 - 0.8') {
                        $notif = '0.6';
                    } elseif ($purchas->ukuran_ayam == '0.7 - 0.9') {
                        $notif = '0.7';
                    } elseif ($purchas->ukuran_ayam == '0.8 - 1.0') {
                        $notif = '0.8';
                    } elseif ($purchas->ukuran_ayam == '0.9 - 1.1') {
                        $notif = '0.9';
                    } elseif ($purchas->ukuran_ayam == '1.0 - 1.2') {
                        $notif = '1';
                    } elseif ($purchas->ukuran_ayam == '1.1 - 1.3') {
                        $notif = '1.1';
                    } elseif ($purchas->ukuran_ayam == '1.2 - 1.5') {
                        $notif = '1.35';
                    } elseif ($purchas->ukuran_ayam == '1.2 - 1.4') {
                        $notif = '1.2';
                    } elseif ($purchas->ukuran_ayam == '1.3 - 1.6') {
                        $notif = '1.45';
                    } elseif ($purchas->ukuran_ayam == '1.3 - 1.5') {
                        $notif = '1.3';
                    } elseif ($purchas->ukuran_ayam == '1.4 - 1.7') {
                        $notif = '1.55';
                    } elseif ($purchas->ukuran_ayam == '1.4 - 1.6') {
                        $notif = '1.4';
                    } elseif ($purchas->ukuran_ayam == '1.5 - 1.8') {
                        $notif = '1.65';
                    } elseif ($purchas->ukuran_ayam == '1.5 - 1.7') {
                        $notif = '1.5';
                    } elseif ($purchas->ukuran_ayam == '1.6 - 1.8') {
                        $notif = '1.6';
                    } elseif ($purchas->ukuran_ayam == '1.7 - 1.9') {
                        $notif = '1.7';
                    } elseif ($purchas->ukuran_ayam == '1.8 - 2.0') {
                        $notif = '1.8';
                    } elseif ($purchas->ukuran_ayam == '1.9 - 2.1') {
                        $notif = '1.9';
                    } elseif ($purchas->ukuran_ayam == '2.0 - 2.2') {
                        $notif = '2';
                    } elseif ($purchas->ukuran_ayam == '2.1 - 2.3') {
                        $notif = '2.1';
                    } elseif ($purchas->ukuran_ayam == '2.2 - 2.4') {
                        $notif = '2.2';
                    } elseif ($purchas->ukuran_ayam == '2.3 - 2.5') {
                        $notif = '2.3';
                    } elseif ($purchas->ukuran_ayam == '2.4 - 2.6') {
                        $notif = '2.4';
                    }

                    if ($rerata < $notif) {
                        // Ayam kecil, aslinya 3 tetapi sekarang dibuat 1 dulu karena PPIC belum jalan
                        $produksi->sc_status    =   1;
                    } elseif ($rerata >= $notif) {
                        $produksi->sc_status    =   1;
                    }
                    $produksi->sc_rerata_do         =   number_format(($rerata), 2);
                } else {
                    $produksi->sc_status        =   1;
                }

                $produksi->po_jenis_ekspedisi   =   $purchas->type_ekspedisi;
                $produksi->sc_tanggal_masuk     =   Carbon::now();
                if ($purchas->type_po == 'PO LB' OR $purchas->type_po == 'PO Maklon') {
                    $produksi->no_urut          =   $request->nourut;
                }
                $produksi->no_lpah              =   Production::nomor_lpah($produksi->id);
                $produksi->sc_jam_masuk         =   $request->sc_jam_masuk ?? date('H:m:s');
                $produksi->sc_hari              =   date('l');
                $produksi->no_do                =   $request->no_do;
                $produksi->sc_ekor_do           =   $request->ekor_do;
                $produksi->sc_berat_do          =   $berat_replace;
                $produksi->sc_no_polisi         =   $no_polisi;
                $produksi->sc_nama_kandang      =   $request->nama_kandang;
                $produksi->sc_alamat_kandang    =   $request->alamat_kandang;

                $produksi->sc_penerima_id       =   Auth::user()->id;
                $produksi->sc_user_id           =   Auth::user()->id;
                if ($purchas->type_po != 'PO LB' or $purchas->type_po != 'PO Maklon') {
                    $produksi->ppic_acc         =   1;
                }
                
                if (!$produksi->save()) {
                    DB::rollBack() ;
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                $finished   =   Production::where('purchasing_id', $request->purchase)
                                ->where('no_urut', NULL)
                                ->count();

                if ($finished == 0) {
                    $purchased          =   Purchasing::find($request->purchase);
                    $purchased->status  =   1;
                    if (!$purchased->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
                }

                // Log activity
                $log                =   new Adminedit ;
                $log->user_id       =   Auth::user()->id ;
                $log->table_name    =   'productions' ;
                $log->table_id      =   $produksi->id ;
                $log->type          =   'input' ;
                $log->activity      =   'security' ;
                $log->content       =   'Input kedatangan mobil' ;
                $log->data          =   json_encode($request->all()) ;
                if (!$log->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                DB::commit();
                return back()->with('status', 1)->with('message', 'Data berhasil ditambahkan');

            }
        // }
        // return redirect()->route("index");
    }
    public function reset(Request $request)
    {
        DB::beginTransaction();
        $purchase   =   Purchasing::whereIn('id', Production::select('purchasing_id')->where('id', $request->x_code))
                        ->first() ;

        if ($purchase->status == 1) {
            $purchase->status   =   2 ;
            if (!$purchase->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }
        }

            $data                           = Production::where('id', $request->x_code)->first();

            $data->sc_tanggal_masuk         = NULL;
            $data->sc_jam_masuk             = NULL;
            $data->sc_hari                  = NULL;
            $data->sc_no_polisi             = NULL;
            $data->sc_pengemudi             = NULL;
            $data->sc_status                = NULL;
            $data->no_urut                  = NULL;
            $data->no_do                    = NULL;
            $data->no_lpah                  = NULL;
            $data->no_lpah                  = NULL;
            $data->sc_penerima_id           = NULL;
            $data->sc_ekor_do               = NULL;
            $data->sc_berat_do              = NULL;
            $data->sc_rerata_do             = NULL;
            $data->sc_rerata_do             = NULL;
            $data->sc_user_id               = NULL;
            $data->lpah_jam_bongkar         = NULL;
            $data->lpah_tanggal_potong      = NULL;
            $data->lpah_tanggal_potong      = NULL;
            $data->lpah_berat_kotor         = NULL;
            $data->lpah_berat_susut         = NULL;
            $data->lpah_persen_susut        = NULL;
            $data->lpah_berat_terima        = NULL;
            $data->lpah_rerata_terima       = NULL;
            $data->lpah_jumlah_keranjang    = NULL;
            $data->lpah_berat_keranjang     = NULL;
            $data->ekoran_seckle            = NULL;
            $data->lpah_status              = NULL;
            $data->lpah_netsuite_status     = NULL;
            $data->lpah_user_id             = NULL;
            $data->lpah_user_nama           = NULL;
            $data->lpah_user_nama           = NULL;
            $data->wo_netsuite_status       = NULL;
            $data->qc_ekor_ayam_mati        = NULL;
            $data->qc_persen_ayam_mati      = NULL;
            $data->qc_berat_ayam_mati       = NULL;
            $data->qc_ekor_ayam_merah       = NULL;
            $data->qc_berat_ayam_merah      = NULL;
            $data->qc_tembolok              = NULL;
            $data->qc_user_id               = NULL;
            $data->qc_proses                = NULL;
            $data->qc_under                 = NULL;
            $data->qc_over                  = NULL;
            $data->qc_uniform               = NULL;
            $data->qc_status                = NULL;
            $data->ppic_acc                 = NULL;
            $data->evis_user_id             = NULL;
            $data->evis_user_name           = NULL;
            $data->evis_proses              = NULL;
            $data->evis_selesai             = NULL;
            $data->evis_status              = NULL;
            $data->evis_status              = NULL;
            $data->grading_user_id          = NULL;
            $data->grading_user_nama        = NULL;
            $data->grading_user_nama        = NULL;
            $data->grading_selesai          = NULL;
            $data->grading_status           = NULL;
            $data->grading_status           = NULL;

            if (!$data->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            $antem = Antemortem::where('production_id', $data->id)->first();

            if($antem){
                $antem->delete();
            }

            // Log activity
            $log                =   new Adminedit ;
            $log->user_id       =   Auth::user()->id ;
            $log->table_name    =   'productions' ;
            $log->table_id      =   $data->id ;
            $log->type          =   'reset' ;
            $log->activity      =   'security' ;
            $log->content       =   'Reset data kedatangan mobil' ;
            $riwayat            =   Adminedit::where('type', 'input')->where('table_name', 'productions')->where('table_id', $data->id)->orderBy('id', 'DESC')->first() ;
            $log->data          =   $riwayat->data ?? NULL ;
            if (!$log->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            DB::commit();
            return redirect()->back()->with('status', 1)->with('message', 'Data berhasil direset');
    }

    public function update(Request $request)
    {

        DB::beginTransaction();

        $supir  =   (string)$request->supir;
        if (User::setIjin(2)) {
            $data           =   Production::where('id', $request->x_code)->first();

            $sopir          =   Driver::where('nama', 'like', '%' . $request->supir . '%')->first();

            $berat_replace  =   (float)str_replace(',', '.', $request->berat_do);
            $purchas        =   Purchasing::find($data->purchasing_id);

            $rerata         =   ($request->ekor_do > 0) ? ($berat_replace / $request->ekor_do) : 0 ;
            $no_polisi      =   strtoupper(str_replace(" ", "", $request->no_polisi));

            if ($data) {
                $produksi                       =   Production::find($data->id);
                $produksi->sc_pengemudi         =   $request->supir;
                $produksi->sc_jam_masuk         =   $request->sc_jam_masuk;
                $produksi->no_do                =   $request->no_do;
                $produksi->sc_ekor_do           =   $request->ekor_do;
                $produksi->sc_berat_do          =   $berat_replace;
                $produksi->sc_no_polisi         =   $no_polisi;
                $produksi->sc_nama_kandang      =   $request->nama_kandang;
                $produksi->sc_alamat_kandang    =   $request->alamat_kandang;
                $produksi->sc_pengemudi_target  =   $request->target;
                $produksi->sc_pengemudi         =   $request->supir;
                $produksi->sc_pengemudi_id      =   $sopir->id ?? "";
                $produksi->sc_user_id           =   Auth::user()->id;
                $produksi->sc_rerata_do         =   round($rerata, 2);

                // dd($data);

                if ($purchas->type_po == 'PO LB' or $purchas->type_po == 'PO Maklon') {

                    if ($purchas->ukuran_ayam == '0.5 - 0.7') {
                        $notif = '0.5';
                    } elseif ($purchas->ukuran_ayam == '0.6 - 0.8') {
                        $notif = '0.6';
                    } elseif ($purchas->ukuran_ayam == '0.7 - 0.9') {
                        $notif = '0.7';
                    } elseif ($purchas->ukuran_ayam == '0.8 - 1.0') {
                        $notif = '0.8';
                    } elseif ($purchas->ukuran_ayam == '0.9 - 1.1') {
                        $notif = '0.9';
                    } elseif ($purchas->ukuran_ayam == '1.0 - 1.2') {
                        $notif = '1';
                    } elseif ($purchas->ukuran_ayam == '1.1 - 1.3') {
                        $notif = '1.1';
                    } elseif ($purchas->ukuran_ayam == '1.2 - 1.4') {
                        $notif = '1.2';
                    } elseif ($purchas->ukuran_ayam == '1.3 - 1.5') {
                        $notif = '1.3';
                    } elseif ($purchas->ukuran_ayam == '1.4 - 1.6') {
                        $notif = '1.4';
                    } elseif ($purchas->ukuran_ayam == '1.5 - 1.7') {
                        $notif = '1.5';
                    } elseif ($purchas->ukuran_ayam == '1.6 - 1.8') {
                        $notif = '1.6';
                    } elseif ($purchas->ukuran_ayam == '1.7 - 1.9') {
                        $notif = '1.7';
                    } elseif ($purchas->ukuran_ayam == '1.8 - 2.0') {
                        $notif = '1.8';
                    } elseif ($purchas->ukuran_ayam == '1.9 - 2.1') {
                        $notif = '1.9';
                    } elseif ($purchas->ukuran_ayam == '2.0 - 2.2') {
                        $notif = '2';
                    } elseif ($purchas->ukuran_ayam == '2.1 - 2.3') {
                        $notif = '2.1';
                    } elseif ($purchas->ukuran_ayam == '2.2 - 2.4') {
                        $notif = '2.2';
                    } elseif ($purchas->ukuran_ayam == '2.3 - 2.5') {
                        $notif = '2.3';
                    } elseif ($purchas->ukuran_ayam == '2.4 - 2.6') {
                        $notif = '2.4';
                    } elseif ($purchas->ukuran_ayam == '1.2 - 1.5') {
                        $notif = '1.35';
                    } elseif ($purchas->ukuran_ayam == '1.3 - 1.6') {
                        $notif = '1.45';
                    } elseif ($purchas->ukuran_ayam == '1.4 - 1.7') {
                        $notif = '1.55';
                    } elseif ($purchas->ukuran_ayam == '1.5 - 1.8') {
                        $notif = '1.65';
                    }

                    if ($rerata < $notif) {
                        // Ayam kecil, aslinya 3 tetapi sekarang dibuat 1 dulu karena PPIC belum jalan
                        $produksi->sc_status    =   1;
                    } elseif ($rerata >= $notif) {
                        $produksi->sc_status    =   1;
                    }
                } else {
                    $produksi->sc_status        =   1;
                }

                if (!$produksi->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                $ceklog             = Adminedit::where('activity','security')->where('table_id',$data->id)->where('table_name','productions')->where('type','edit-security')->count();
                if ($ceklog < 1) {
                    $logawal               =   new Adminedit();
                    $logawal->user_id      =   Auth::user()->id;
                    $logawal->table_name   =   'productions';
                    $logawal->table_id     =   $data->id;
                    $logawal->content      =   'Data awal security';
                    $logawal->type         =  'edit-security';
                    $logawal->activity     =   'security';
                    $logawal->data         = json_encode([
                        'data' => $data,
                    ]);
                    $logawal->status       =   1;
                    if (!$logawal->save()) {
                        return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                    }
                }
                $edit               =   new Adminedit ;
                $edit->user_id      =   Auth::user()->id;
                $edit->table_name   =   'productions';
                $edit->table_id     =   $data->id;
                $edit->content      =   $request->alasan;
                $edit->type         =  'edit-security';
                $edit->activity     =   'security';
                $edit->data         = json_encode([
                    'data' => $produksi,
                ]);
                $edit->status       =   1;

                if (!$edit->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                $produksi->no_urut  =   $request->no_urut;
                if (!$produksi->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                DB::commit();
                return back()->with('status', 1)->with('message', 'Data berhasil diperbaharui');
            }
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route("index");
    }

    public function edit(Request $request)
    {
        if (User::setIjin(2)) {
            $data   =   Production::find($request->row_id);
            $row    =   [
                'pengemudi'         =>  $data->sc_pengemudi_id,
                'berat_do'          =>  $data->sc_berat_do,
                'ekor_do'           =>  $data->sc_ekor_do,
                'no_polisi'         =>  $data->sc_no_polisi,
                'nama_kandang'      =>  $data->sc_nama_kandang,
                'alamat_kandang'    =>  $data->sc_alamat_kandang,
            ] ;

            return $row ;
        }
        return redirect()->route("index");
    }
}

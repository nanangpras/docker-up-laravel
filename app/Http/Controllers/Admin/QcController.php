<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\Antemortem;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Log;
use App\Models\Nekropsi;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\Postmortem;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Retur;
use App\Models\ReturItem;
use App\Models\Returpurchase;
use App\Models\Unifomity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QcController extends Controller
{
    public function index(Request $request)
    {
        if (User::setIjin(4)) {
            // $tanggal    =   $request->tanggal ?? Carbon::now()->format('Y-m-d');
            $tanggalawal        =   $request->tanggalawal ?? Carbon::now()->format('Y-m-d');
            $tanggalakhir       =   $request->tanggalakhir ?? Carbon::now()->format('Y-m-d');

            $data               =   Production::select('purchasing.tanggal_potong', 'productions.*')
                ->leftJoin('purchasing', 'purchasing.id', '=', 'productions.purchasing_id')
                ->where('no_urut', '!=', NULL)
                ->where('sc_status', 1)
                ->whereBetween('tanggal_potong', [$tanggalawal, $tanggalakhir])
                ->where('po_jenis_ekspedisi', '<>', null)
                ->orderByRaw('tanggal_potong ASC, no_urut ASC')
                ->get();
            $retur_po   =   Returpurchase::whereBetween('tanggal', [($request->tanggalawal ?? date("Y-m-d")), ($request->tanggalakhir ?? date("Y-m-d"))])
                ->paginate(10);
            $purch       =   Purchasing::whereIn('status', [1])->get();
            $hash        = $request->navigate ?? '';
            $customer    = Customer::pluck('nama','id');
            $category    = Category::pluck('nama','id');
            // dd($request->all());
            // if($request->status){
            //     return view('admin.pages.qc.index' . '#' . $request->status, compact('tanggalawal', 'tanggalakhir', 'data', 'purch', 'retur_po'));
            // }
            return view('admin.pages.qc.index', compact('tanggalawal', 'tanggalakhir', 'data', 'purch', 'retur_po', 'hash','customer','category'));
        }
        return redirect()->route("index");
    }

    public function siap_kirim_export(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $search    =   $request->search ?? "";
        return view('admin.pages.penyiapan.export');
    }

    public function create()
    {
        if (User::setIjin(4)) {
            //
        }
        return redirect()->route("index");
    }


    public function antem(Request $request, $id)
    {
        if (User::setIjin(4)) {
            $data   =   Production::find($id);

            if ($data) {

                DB::beginTransaction();

                $antem                          =   Antemortem::where('production_id', $data->id)->first();
                $antem->qc_id                   =   Auth::user()->id;
                $antem->basah_bulu              =   $request->basah_bulu;
                $antem->keaktifan               =   $request->keaktifan;
                $antem->cairan                  =   $request->cairan;
                $antem->ayam_sakit              =   $request->ayam_sakit;
                $antem->ayam_sakit_nama         =   $request->ayam_sakit_nama;
                if (!$antem->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                $data->qc_ekor_ayam_mati        =   $request->ayam_mati;
                $data->qc_persen_ayam_mati      =   round((100 - ((($data->sc_berat_do - $antem->ayam_mati_kg) / $data->sc_berat_do) * 100)), 2);
                $data->qc_berat_ayam_mati       =   $antem->ayam_mati_kg;
                // $data->qc_ekor_ayam_merah       =   NULL;
                // $data->qc_persen_ayam_merah     =   NULL;
                // $data->qc_berat_ayam_merah      =   NULL;
                $data->lpah_berat_terima        =   $data->berat_bersih_lpah;
                $data->lpah_kebersihan_keranjang =   $request->kebersihanKeranjang;
                $data->qc_user_id               =   Auth::user()->id;
                $data->qc_proses                =   date('Y-m-d H:i:s');
                $data->qc_status                =   1;
                if (!$data->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }


                $persen                         =   round(((($data->sc_berat_do - $data->berat_bersih_lpah) / $data->sc_berat_do) * 100), 2);
                $data->lpah_persen_susut        =   $persen;
                if (!$data->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                $adminlog               =   new Adminedit;
                $adminlog->user_id      =   Auth::user()->id;
                $adminlog->table_name   =   'productions';
                $adminlog->table_id     =   $data->id;
                $adminlog->type         =   'input';
                $adminlog->activity     =   'qc';
                $adminlog->content      =   'Menyelesaikan proses Antemortem';
                $adminlog->status       =   1;
                if (!$adminlog->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses gagal');
                }

                DB::commit();
                return back()->with('status', 1)->with('message', 'Ubah antemortem berhasil');
            }
            return redirect()->route('qc.index')->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route("index");
    }

    public function post(Request $request, $id)
    {

        if (User::setIjin(4)) {

            $produksi   =   Production::where('id', $id)->first();
            $postmortem =   Postmortem::where('production_id', $id)->first();

            DB::beginTransaction();

            if ($produksi) {

                if ($produksi->qc_tembolok != $request->totaltembolok) {
                    $adminlog               =   new Adminedit;
                    $adminlog->user_id      =   Auth::user()->id;
                    $adminlog->table_name   =   'productions';
                    $adminlog->table_id     =   $produksi->id;
                    $adminlog->type         =   'input';
                    $adminlog->activity     =   'qc';
                    $adminlog->content      =   'Mengubah data tembolok';
                    $adminlog->status       =   1;
                    if (!$adminlog->save()) {
                        DB::rollBack();
                        return redirect()->to(url()->previous() . "#tab-2")->with('status', 2)->with('message', 'Proses Gagal');
                    }
                }

                $produksi->qc_tembolok      = $request->totaltembolok;
                if (!$produksi->save()) {
                    DB::rollBack();
                    return redirect()->to(url()->previous() . "#tab-2")->with('status', 2)->with('message', 'Proses Gagal');
                }
            }

            $postmortem->tembolok_kondisi   =   $request->kondisi;
            $postmortem->qc_id              =   Auth::user()->id;
            $postmortem->tembolok_jumlah    =   $request->jumlah;
            $postmortem->ayam_merah         =   $request->ayammerah;
            $postmortem->jeroan_hati        =   json_encode($request->hati);
            $postmortem->jeroan_jantung     =   json_encode($request->jantung);
            $postmortem->jeroan_ampela      =   json_encode($request->ampela);
            $postmortem->jeroan_usus        =   json_encode($request->usus);
            $postmortem->catatan            =   $request->catatan;
            $postmortem->kehijauan          =   $request->dengkul;
            $postmortem->memar_dada         =   $request->memar_dada;
            $postmortem->memar_paha         =   $request->memar_paha;
            $postmortem->memar_sayap        =   $request->memar_sayap;
            $postmortem->patah_sayap        =   $request->patah_sayap;
            $postmortem->patah_kaki         =   $request->patah_kaki;
            $postmortem->keropeng_kaki      =   $request->keropeng_kaki;
            $postmortem->keropeng_sayap     =   $request->keropeng_sayap;
            $postmortem->keropeng_dada      =   $request->keropeng_dada;
            $postmortem->keropeng_pg        =   $request->keropeng_pg;
            $postmortem->keropeng_dengkul   =   $request->keropeng_dengkul;
            if (!$postmortem->save()) {
                DB::rollBack();
                return redirect()->to(url()->previous() . "#tab-2")->with('status', 2)->with('message', 'Proses Gagal');
            }

            $adminlog               =   new Adminedit;
            $adminlog->user_id      =   Auth::user()->id;
            $adminlog->table_name   =   'productions';
            $adminlog->table_id     =   $produksi->id;
            $adminlog->type         =   'input';
            $adminlog->activity     =   'qc';
            $adminlog->content      =   'Menyelesaikan proses Postmortem';
            $adminlog->status       =   1;
            if (!$adminlog->save()) {
                DB::rollBack();
                return redirect()->to(url()->previous() . "#tab-2")->with('status', 2)->with('message', 'Proses Gagal');
            }

            DB::commit();
            return redirect()->to(url()->previous() . "#tab-2")->with('status', 1)->with('message', 'Berhasil Update');
        }
        return redirect()->route("index");
    }

    public function store(Request $request)
    {
        if (User::setIjin(4)) {
            //
        }
        return redirect()->route("index");
    }

    public function show($id)
    {
        if (User::setIjin(4)) {
            $data   =   Production::find($id);
            if ($data) {
                $get_mobil = Production::select('id','no_urut','sc_no_polisi', 'sc_pengemudi')->where('sc_tanggal_masuk',$data->sc_tanggal_masuk)->get();
                $antem  =   Antemortem::where('production_id', $data->id)->first();
                if ($antem) {
                    $postm     =   Postmortem::where('production_id', $data->id)->first();
                    $unifo     =   Unifomity::where('production_id', $data->id)->first();

                    //hati
                    if (json_decode($postm->jeroan_hati) !== null) {

                        if (in_array("peradangan", json_decode($postm->jeroan_hati))) {
                            $peradangan = "checked";
                        }
                        if (in_array("perkejuan", json_decode($postm->jeroan_hati))) {
                            $perkejuan = "checked";
                        }
                        if (in_array("kekuningan", json_decode($postm->jeroan_hati))) {
                            $kekuningan = "checked";
                        }
                        if (in_array("bercak", json_decode($postm->jeroan_hati))) {
                            $bercak = "checked";
                        }
                        if (in_array("normal", json_decode($postm->jeroan_hati))) {
                            $normal = "checked";
                        }
                    }

                    // ampela
                    if (json_decode($postm->jeroan_ampela) !== null) {

                        if (in_array("peradangan", json_decode($postm->jeroan_ampela))) {
                            $peradanganampela = "checked";
                        }
                        if (in_array("perkejuan", json_decode($postm->jeroan_ampela))) {
                            $perkejuanampela = "checked";
                        }
                        if (in_array("kekuningan", json_decode($postm->jeroan_ampela))) {
                            $kekuninganampela = "checked";
                        }
                        if (in_array("bercak", json_decode($postm->jeroan_ampela))) {
                            $bercakampela = "checked";
                        }
                        if (in_array("normal", json_decode($postm->jeroan_ampela))) {
                            $normalampela = "checked";
                        }
                    }
                    // jantung
                    if (json_decode($postm->jeroan_jantung) !== null) {
                        if (in_array("pembengkakan", json_decode($postm->jeroan_jantung))) {
                            $pembengkakan = "checked";
                        }
                        if (in_array("penebalan", json_decode($postm->jeroan_jantung))) {
                            $penebalan = "checked";
                        }
                        if (in_array("normal", json_decode($postm->jeroan_jantung))) {
                            $jantungnormal = "checked";
                        }
                    }
                    // usus
                    if (json_decode($postm->jeroan_usus) !== null) {
                        if (in_array("peradangan", json_decode($postm->jeroan_usus))) {
                            $peradanganusus = "checked";
                        }
                        if (in_array("pendarahan", json_decode($postm->jeroan_usus))) {
                            $pendarahan = "checked";
                        }
                        if (in_array("cacing", json_decode($postm->jeroan_usus))) {
                            $cacing = "checked";
                        }
                        if (in_array("normal", json_decode($postm->jeroan_usus))) {
                            $cacingnormal = "checked";
                        }
                    }

                    $hati       =   ['peradangan' => $peradangan ?? '', 'perkejuan' => $perkejuan ?? '', 'kekuningan' => $kekuningan ?? '', 'bercak' => $bercak ?? '', 'normal' => $normal ?? ''];
                    $ampela     =   ['peradangan' => $peradanganampela ?? '', 'perkejuan' => $perkejuanampela ?? '', 'kekuningan' => $kekuninganampela ?? '', 'bercak' => $bercakampela ?? '', 'normal' => $normalampela ?? ''];
                    $jantung    =   ['pembengkakan' => $pembengkakan ?? '', 'penebalan' => $penebalan ?? '', 'normal' => $jantungnormal ?? ''];
                    $usus       =   ['peradangan' => $peradanganusus ?? '', 'pendarahan' => $pendarahan ?? '', 'cacing' => $cacing ?? '', 'normal' => $cacingnormal ?? ''];
                    return view('admin/pages/qc/detail', compact('data', 'antem', 'postm', 'unifo', 'hati', 'ampela', 'jantung', 'usus','get_mobil'));
                }
            }

            return redirect()->route('qc.index')->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route("index");
    }

    public function add(Request $request, $id)
    {
        if (User::setIjin(4)) {
            $data                   =  new Unifomity;
            $data->berat            = $request->berat / 100;
            $data->production_id    = $id;
            $data->qc_id            =   Auth::user()->id;
            $data->save();

            $un       =   Unifomity::where('production_id', $id)->get();

            $uni    = 0;
            $under  = 0;
            $over   = 0;

            foreach ($un as $row) {

                if ($row->uniprod->prodpur->ukuran_ayam == '0.3 - 0.5') {
                    if ($row->berat >= '0.3' and $row->berat <= '0.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.3') {
                        $under += 1;
                    }
                    if ($row->berat > '0.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.4 - 0.6') {
                    if ($row->berat >= '0.4' and $row->berat <= '0.6') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.4') {
                        $under += 1;
                    }
                    if ($row->berat > '0.6') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.5 - 0.7') {
                    if ($row->berat >= '0.5' and $row->berat <= '0.7') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.5') {
                        $under += 1;
                    }
                    if ($row->berat > '0.7') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.6 - 0.8') {
                    if ($row->berat >= '0.6' and $row->berat <= '0.8') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.6') {
                        $under += 1;
                    }
                    if ($row->berat > '0.8') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.7 - 0.9') {
                    if ($row->berat >= '0.7' and $row->berat <= '0.9') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.7') {
                        $under += 1;
                    }
                    if ($row->berat > '0.9') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.8 - 1.0') {
                    if ($row->berat >= '0.8' and $row->berat <= '1.0') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.8') {
                        $under += 1;
                    }
                    if ($row->berat > '1.0') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.9 - 1.1') {
                    if ($row->berat >= '0.9' and $row->berat <= '1.1') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.9') {
                        $under += 1;
                    }
                    if ($row->berat > '1.1') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.0 - 1.2') {
                    if ($row->berat >= '1.0' and $row->berat <= '1.2') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.0') {
                        $under += 1;
                    }
                    if ($row->berat > '1.2') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.1 - 1.3') {
                    if ($row->berat >= '1.1' and $row->berat <= '1.3') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.1') {
                        $under += 1;
                    }
                    if ($row->berat > '1.3') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.2 - 1.5') {
                    if ($row->berat >= '1.2' and $row->berat <= '1.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.2') {
                        $under += 1;
                    }
                    if ($row->berat > '1.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.2 - 1.4') {
                    if ($row->berat >= '1.2' and $row->berat <= '1.4') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.2') {
                        $under += 1;
                    }
                    if ($row->berat > '1.4') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.3 - 1.6') {
                    if ($row->berat >= '1.3' and $row->berat <= '1.6') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.3') {
                        $under += 1;
                    }
                    if ($row->berat > '1.6') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.3 - 1.5') {
                    if ($row->berat >= '1.3' and $row->berat <= '1.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.3') {
                        $under += 1;
                    }
                    if ($row->berat > '1.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.4 - 1.7') {
                    if ($row->berat >= '1.4' and $row->berat <= '1.7') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.4') {
                        $under += 1;
                    }
                    if ($row->berat > '1.7') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.4 - 1.6') {
                    if ($row->berat >= '1.4' and $row->berat <= '1.6') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.4') {
                        $under += 1;
                    }
                    if ($row->berat > '1.6') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.5 - 1.8') {
                    if ($row->berat >= '1.5' and $row->berat <= '1.8') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.5') {
                        $under += 1;
                    }
                    if ($row->berat > '1.8') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.5 - 1.7') {
                    if ($row->berat >= '1.5' and $row->berat <= '1.7') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.5') {
                        $under += 1;
                    }
                    if ($row->berat > '1.7') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.6 - 1.8') {
                    if ($row->berat >= '1.6' and $row->berat <= '1.8') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.6') {
                        $under += 1;
                    }
                    if ($row->berat > '1.8') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.7 - 1.9') {
                    if ($row->berat >= '1.7' and $row->berat <= '1.9') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.7') {
                        $under += 1;
                    }
                    if ($row->berat > '1.9') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.8 - 2.0') {
                    if ($row->berat >= '1.8' and $row->berat <= '2.0') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.8') {
                        $under += 1;
                    }
                    if ($row->berat > '2.0') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.9 - 2.1') {
                    if ($row->berat >= '1.9' and $row->berat <= '2.1') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.9') {
                        $under += 1;
                    }
                    if ($row->berat > '2.1') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.0 - 2.2') {
                    if ($row->berat >= '2.0' and $row->berat <= '2.2') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.0') {
                        $under += 1;
                    }
                    if ($row->berat > '2.2') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.1 - 2.3') {
                    if ($row->berat >= '2.1' and $row->berat <= '2.3') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.1') {
                        $under += 1;
                    }
                    if ($row->berat > '2.3') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.2 - 2.4') {
                    if ($row->berat >= '2.2' and $row->berat <= '2.4') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.2') {
                        $under += 1;
                    }
                    if ($row->berat > '2.4') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.3 - 2.5') {
                    if ($row->berat >= '2.3' and $row->berat <= '2.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.3') {
                        $under += 1;
                    }
                    if ($row->berat > '2.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.5 - Up') {
                    if ($row->berat >= '2.5' and $row->berat <= '2.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.5') {
                        $under += 1;
                    }
                    if ($row->berat > '2.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.2 up') {
                    if ($row->berat >= '2.2' and $row->berat <= '3') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.2') {
                        $under += 1;
                    }
                    if ($row->berat > '3') {
                        $over += 1;
                    }
                }
            }

            $prod               =   Production::find($id);
            $prod->qc_under     =   $under;
            $prod->qc_over      =   $over;
            $prod->qc_uniform   =   $uni;
            $prod->save();
        }
        return redirect()->route("index");
    }

    public function summary($id)
    {
        if (User::setIjin(4)) {
            $data       =   Unifomity::where('production_id', $id)->get();


            $total        = 0;
            $kurang       = 0;
            $noltiga      = 0;
            $nolempat     = 0;
            $nollima      = 0;
            $nolenam      = 0;
            $noltujuh     = 0;
            $noldelapan   = 0;
            $nolsembilan  = 0;
            $satu         = 0;
            $satusatu     = 0;
            $satudua      = 0;
            $satutiga     = 0;
            $satuempat    = 0;
            $satulima     = 0;
            $satuenam     = 0;
            $satutujuh    = 0;
            $satudelapan  = 0;
            $satusembilan = 0;
            $dua          = 0;
            $duasatu      = 0;
            $duadua       = 0;
            $duatiga      = 0;
            $duaempat     = 0;
            $dualima      = 0;

            foreach ($data as $row) {
                $total  +=  1;

                if ($row->berat < '0.3') {
                    $kurang += 1;
                } elseif ($row->berat >= '0.3' and $row->berat <= '0.4') {
                    $noltiga += 1;
                } elseif ($row->berat > '0.4' and $row->berat <= '0.5') {
                    $nolempat += 1;
                } elseif ($row->berat > '0.5' and $row->berat <= '0.6') {
                    $nollima += 1;
                } elseif ($row->berat > '0.6' and $row->berat <= '0.7') {
                    $nolenam += 1;
                } elseif ($row->berat > '0.7' and $row->berat <= '0.8') {
                    $noltujuh += 1;
                } elseif ($row->berat > '0.8' and $row->berat <= '0.9') {
                    $noldelapan += 1;
                } elseif ($row->berat > '0.9' and $row->berat <= '1.0') {
                    $nolsembilan += 1;
                } elseif ($row->berat > '1.0' and $row->berat <= '1.1') {
                    $satu += 1;
                } elseif ($row->berat > '1.1' and $row->berat <= '1.2') {
                    $satusatu += 1;
                } elseif ($row->berat > '1.2' and $row->berat <= '1.3') {
                    $satudua += 1;
                } elseif ($row->berat > '1.3' and $row->berat <= '1.4') {
                    $satutiga += 1;
                } elseif ($row->berat > '1.4' and $row->berat <= '1.5') {
                    $satuempat += 1;
                } elseif ($row->berat > '1.5' and $row->berat <= '1.6') {
                    $satulima += 1;
                } elseif ($row->berat > '1.6' and $row->berat <= '1.7') {
                    $satuenam += 1;
                } elseif ($row->berat > '1.7' and $row->berat <= '1.8') {
                    $satutujuh += 1;
                } elseif ($row->berat > '1.8' and $row->berat <= '1.9') {
                    $satudelapan += 1;
                } elseif ($row->berat > '1.9' and $row->berat <= '2.0') {
                    $satusembilan += 1;
                } elseif ($row->berat > '2.0' and $row->berat <= '2.1') {
                    $dua += 1;
                } elseif ($row->berat > '2.1' and $row->berat <= '2.2') {
                    $duasatu += 1;
                } elseif ($row->berat > '2.2' and $row->berat <= '2.3') {
                    $duadua += 1;
                } elseif ($row->berat > '2.3' and $row->berat <= '2.4') {
                    $duatiga += 1;
                } elseif ($row->berat > '2.4' and $row->berat <= '2.5') {
                    $duaempat += 1;
                } elseif ($row->berat > '2.5') {
                    $dualima += 1;
                }
            }

            if($data->count() > 0){
                $ukuranAyam = $data[0]->uniprod->prodpur->ukuran_ayam;
            }

            $rata   =   $total > 0 ? ($data->sum('berat') / $total) : 0;

            $count = [
                'total'        => $total,
                'kurang'       => $kurang,
                'noltiga'      => $noltiga,
                'nolempat'     => $nolempat,
                'nollima'      => $nollima,
                'nolenam'      => $nolenam,
                'noltujuh'     => $noltujuh,
                'noldelapan'   => $noldelapan,
                'nolsembilan'  => $nolsembilan,
                'satu'         => $satu,
                'satusatu'      => $satusatu,
                'satudua'       => $satudua,
                'satutiga'      => $satutiga,
                'satuempat'     => $satuempat,
                'satulima'      => $satulima,
                'satuenam'      => $satuenam,
                'satutujuh'     => $satutujuh,
                'satudelapan'   => $satudelapan,
                'satusembilan'  => $satusembilan,
                'dua'           => $dua,
                'duasatu'       => $duasatu,
                'duadua'        => $duadua,
                'duatiga'       => $duatiga,
                'duaempat'      => $duaempat,
                'dualima'       => $dualima,
                'ratatata'      => $rata
            ];

            $arr1 = ['nama'=> 'noltiga','jumlah' => $noltiga];
            $arr2 = ['nama'=> 'nolempat','jumlah' => $nolempat];
            $arr3 = ['nama'=> 'nollima','jumlah' => $nollima];
            $arr4 = ['nama'=> 'nolenam','jumlah' => $nolenam];
            $arr5 = ['nama'=> 'noltujuh','jumlah' => $noltujuh];
            $arr6 = ['nama'=> 'noldelapan','jumlah' => $noldelapan];
            $arr7 = ['nama'=> 'nolsembilan','jumlah' => $nolsembilan];
            $arr8 = ['nama'=> 'satu','jumlah' => $satu];
            $arr9 = ['nama'=> 'satusatu','jumlah' => $satusatu];
            $arr10 = ['nama'=> 'satudua','jumlah' => $satudua];
            $arr11 = ['nama'=> 'satutiga','jumlah' => $satutiga];
            $arr12 = ['nama'=> 'satuempat','jumlah' => $satuempat];
            $arr13 = ['nama'=> 'satulima','jumlah' => $satulima];
            $arr14 = ['nama'=> 'satuenam','jumlah' => $satuenam];
            $arr15 = ['nama'=> 'satutujuh','jumlah' => $satutujuh];
            $arr16 = ['nama'=> 'satudelapan','jumlah' => $satudelapan];
            $arr17 = ['nama'=> 'satusembilan','jumlah' => $satusembilan];
            $arr18 = ['nama'=> 'dua','jumlah' => $dua];
            $arr19 = ['nama'=> 'duasatu','jumlah' => $duasatu];
            $arr20 = ['nama'=> 'duadua','jumlah' => $duadua];
            $arr21 = ['nama'=> 'duatiga','jumlah' => $duatiga];
            $arr22 = ['nama'=> 'duaempat','jumlah' => $duaempat];
            $arr23 = ['nama'=> 'dualima','jumlah' => $dualima];
            
            $test           = [$arr1,$arr2,$arr3,$arr4,$arr5,$arr6,$arr7,$arr8,$arr9,$arr10,$arr11,$arr12,$arr13,$arr14,$arr15,$arr16,$arr17,$arr18,$arr19,$arr20,$arr21,$arr22,$arr23];
            $CollectArray   = collect($test)->sortByDesc('jumlah')->take(3);
            $collection     = array();

            foreach ($CollectArray as $r) {
                $collection[]      = $this->urutandata($id,$r['nama'],$ukuranAyam);
            }

            $list1   = [];
            $list2   = [];
            $list3   = [];

            foreach($collection[0] as $key => $b1){
                $list1[]  = array('berat' => $b1);
            }
            // foreach($collection[1] as $key => $b2){
            //     $list2[]  = array('berat' => $b2);
            // }
            // foreach($collection[2] as $key => $b3){
            //     $list3[]  = array('berat' => $b3);
            // }

            // $merger     = array_merge($list1,$list2,$list3);
            $merger     = $list1;
            
            // FILTERING 3 Data Terbesar
            $totalData  = count($merger);
            $low        = collect($merger)->sortBy('berat')->first();
            $hight      = collect($merger)->sortByDesc('berat')->first();
            $sum        = collect($merger)->sum('berat');
            $avg        = $totalData > 0 ? number_format(($sum / $totalData),2) : 0;
            $terendah   = $low ? $low['berat']: 0;
            $tertinggi  = $hight ? $hight['berat']: 0;
            
            $detailsData = array(
                'terendah'  => $terendah,
                'tertinggi' => $tertinggi,
                'ratarata'  => $avg
            );

            return view('admin.pages.qc.summary', compact('count','detailsData'));
        }
        return redirect()->route("index");
    }

    public function urutandata($id,$params,$ukuran){
            $sql = Unifomity::where('production_id', $id)
                    // ->where(function($s) use ($params){
                    //     if($params == 'noltiga'){
                    //         $s->where('berat','>=','0.3');
                    //         $s->where('berat','<','0.4');
                    //     }
                    //     if($params == 'nolempat'){
                    //         $s->where('berat','>','0.4');
                    //         $s->where('berat','<=','0.5');
                    //     }
                    //     if($params == 'nollima'){
                    //         $s->where('berat','>','0.5');
                    //         $s->where('berat','<=','0.6');
                    //     }
                    //     if($params == 'nolenam'){
                    //         $s->where('berat','>','0.6');
                    //         $s->where('berat','<=','0.7');
                    //     }
                    //     if($params == 'noltujuh'){
                    //         $s->where('berat','>','0.7');
                    //         $s->where('berat','<=','0.8');
                    //     }
                    //     if($params == 'noldelapan'){
                    //         $s->where('berat','>','0.8');
                    //         $s->where('berat','<=','0.9');
                    //     }
                    //     if($params == 'nolsembilan'){
                    //         $s->where('berat','>','0.9');
                    //         $s->where('berat','<=','1');
                    //     }
                    //     if($params == 'satu'){
                    //         $s->where('berat','>','1');
                    //         $s->where('berat','<=','1.1');
                    //     }
                    //     if($params == 'satusatu'){
                    //         $s->where('berat','>','1.1');
                    //         $s->where('berat','<=','1.2');
                    //     }
                    //     if($params == 'satudua'){
                    //         $s->where('berat','>','1.2');
                    //         $s->where('berat','<=','1.3');
                    //     }
                    //     if($params == 'satutiga'){
                    //         $s->where('berat','>','1.3');
                    //         $s->where('berat','<=','1.4');
                    //     }
                    //     if($params == 'satuempat'){
                    //         $s->where('berat','>','1.4');
                    //         $s->where('berat','<=','1.5');
                    //     }
                    //     if($params == 'satulima'){
                    //         $s->where('berat','>','1.5');
                    //         $s->where('berat','<=','1.6');
                    //     }
                    //     if($params == 'satuenam'){
                    //         $s->where('berat','>','1.6');
                    //         $s->where('berat','<=','1.7');
                    //     }
                    //     if($params == 'satutujuh'){
                    //         $s->where('berat','>','1.7');
                    //         $s->where('berat','<=','1.8');
                    //     }
                    //     if($params == 'satudelapan'){
                    //         $s->where('berat','>','1.8');
                    //         $s->where('berat','<=','1.9');
                    //     }
                    //     if($params == 'satusembilan'){
                    //         $s->where('berat','>','1.9');
                    //         $s->where('berat','<=','2');
                    //     }
                    //     if($params == 'dua'){
                    //         $s->where('berat','>','2');
                    //         $s->where('berat','<=','2.1');
                    //     }
                    //     if($params == 'duasatu'){
                    //         $s->where('berat','>','2.1');
                    //         $s->where('berat','<=','2.2');
                    //     }
                    //     if($params == 'duadua'){
                    //         $s->where('berat','>','2.2');
                    //         $s->where('berat','<=','2.3');
                    //     }
                    //     if($params == 'duatiga'){
                    //         $s->where('berat','>','2.3');
                    //         $s->where('berat','<=','2.4');
                    //     }
                    //     if($params == 'duaempat'){
                    //         $s->where('berat','>','2.4');
                    //         $s->where('berat','<=','2.5');
                    //     }
                    //     if($params == 'dualima'){
                    //         $s->where('berat','>','2.5');
                    //     }
                    // })
                    ->where(function($r) use ($ukuran){
                        if($ukuran != NULL & $ukuran != '&lt; 1.1'){
                            $r->where('berat','>=', substr($ukuran,0,3));
                            $r->where('berat','<=', substr($ukuran,-3));
                        }
                        if($ukuran == '&lt; 1.1'){
                            $r->where('berat','<', substr($ukuran,-3));
                        }
                    })
                    ->orderBy('berat','ASC')
                    ->pluck('berat');
        
        return $sql;
    }

    public function delete(Request $request, $id)
    {
        Unifomity::where('production_id', $id)
            ->where('id', $request->id)
            ->delete();

        $un       =   Unifomity::where('production_id', $id)->get();

        $uni    = 0;
        $under  = 0;
        $over   = 0;

        foreach ($un as $row) {

            if ($row->uniprod->prodpur->ukuran_ayam == '0.3 - 0.5') {
                if ($row->berat >= '0.3' and $row->berat <= '0.5') {
                    $uni += 1;
                }
                if ($row->berat < '0.3') {
                    $under += 1;
                }
                if ($row->berat > '0.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.4 - 0.6') {
                if ($row->berat >= '0.4' and $row->berat <= '0.6') {
                    $uni += 1;
                }
                if ($row->berat < '0.4') {
                    $under += 1;
                }
                if ($row->berat > '0.6') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.5 - 0.7') {
                if ($row->berat >= '0.5' and $row->berat <= '0.7') {
                    $uni += 1;
                }
                if ($row->berat < '0.5') {
                    $under += 1;
                }
                if ($row->berat > '0.7') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.6 - 0.8') {
                if ($row->berat >= '0.6' and $row->berat <= '0.8') {
                    $uni += 1;
                }
                if ($row->berat < '0.6') {
                    $under += 1;
                }
                if ($row->berat > '0.8') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.7 - 0.9') {
                if ($row->berat >= '0.7' and $row->berat <= '0.9') {
                    $uni += 1;
                }
                if ($row->berat < '0.7') {
                    $under += 1;
                }
                if ($row->berat > '0.9') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.8 - 1.0') {
                if ($row->berat >= '0.8' and $row->berat <= '1.0') {
                    $uni += 1;
                }
                if ($row->berat < '0.8') {
                    $under += 1;
                }
                if ($row->berat > '1.0') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.9 - 1.1') {
                if ($row->berat >= '0.9' and $row->berat <= '1.1') {
                    $uni += 1;
                }
                if ($row->berat < '0.9') {
                    $under += 1;
                }
                if ($row->berat > '1.1') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.0 - 1.2') {
                if ($row->berat >= '1.0' and $row->berat <= '1.2') {
                    $uni += 1;
                }
                if ($row->berat < '1.0') {
                    $under += 1;
                }
                if ($row->berat > '1.2') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.1 - 1.3') {
                if ($row->berat >= '1.1' and $row->berat <= '1.3') {
                    $uni += 1;
                }
                if ($row->berat < '1.1') {
                    $under += 1;
                }
                if ($row->berat > '1.3') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.2 - 1.5') {
                if ($row->berat >= '1.2' and $row->berat <= '1.5') {
                    $uni += 1;
                }
                if ($row->berat < '1.2') {
                    $under += 1;
                }
                if ($row->berat > '1.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.2 - 1.4') {
                if ($row->berat >= '1.2' and $row->berat <= '1.4') {
                    $uni += 1;
                }
                if ($row->berat < '1.2') {
                    $under += 1;
                }
                if ($row->berat > '1.4') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.3 - 1.6') {
                if ($row->berat >= '1.3' and $row->berat <= '1.6') {
                    $uni += 1;
                }
                if ($row->berat < '1.3') {
                    $under += 1;
                }
                if ($row->berat > '1.6') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.3 - 1.5') {
                if ($row->berat >= '1.3' and $row->berat <= '1.5') {
                    $uni += 1;
                }
                if ($row->berat < '1.3') {
                    $under += 1;
                }
                if ($row->berat > '1.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.4 - 1.7') {
                if ($row->berat >= '1.4' and $row->berat <= '1.7') {
                    $uni += 1;
                }
                if ($row->berat < '1.4') {
                    $under += 1;
                }
                if ($row->berat > '1.7') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.4 - 1.6') {
                if ($row->berat >= '1.4' and $row->berat <= '1.6') {
                    $uni += 1;
                }
                if ($row->berat < '1.4') {
                    $under += 1;
                }
                if ($row->berat > '1.6') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.5 - 1.8') {
                if ($row->berat >= '1.5' and $row->berat <= '1.8') {
                    $uni += 1;
                }
                if ($row->berat < '1.5') {
                    $under += 1;
                }
                if ($row->berat > '1.8') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.5 - 1.7') {
                if ($row->berat >= '1.5' and $row->berat <= '1.7') {
                    $uni += 1;
                }
                if ($row->berat < '1.5') {
                    $under += 1;
                }
                if ($row->berat > '1.7') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.6 - 1.8') {
                if ($row->berat >= '1.6' and $row->berat <= '1.8') {
                    $uni += 1;
                }
                if ($row->berat < '1.6') {
                    $under += 1;
                }
                if ($row->berat > '1.8') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.7 - 1.9') {
                if ($row->berat >= '1.7' and $row->berat <= '1.9') {
                    $uni += 1;
                }
                if ($row->berat < '1.7') {
                    $under += 1;
                }
                if ($row->berat > '1.9') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.8 - 2.0') {
                if ($row->berat >= '1.8' and $row->berat <= '2.0') {
                    $uni += 1;
                }
                if ($row->berat < '1.8') {
                    $under += 1;
                }
                if ($row->berat > '2.0') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.9 - 2.1') {
                if ($row->berat >= '1.9' and $row->berat <= '2.1') {
                    $uni += 1;
                }
                if ($row->berat < '1.9') {
                    $under += 1;
                }
                if ($row->berat > '2.1') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.0 - 2.2') {
                if ($row->berat >= '2.0' and $row->berat <= '2.2') {
                    $uni += 1;
                }
                if ($row->berat < '2.0') {
                    $under += 1;
                }
                if ($row->berat > '2.2') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.1 - 2.3') {
                if ($row->berat >= '2.1' and $row->berat <= '2.3') {
                    $uni += 1;
                }
                if ($row->berat < '2.1') {
                    $under += 1;
                }
                if ($row->berat > '2.3') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.2 - 2.4') {
                if ($row->berat >= '2.2' and $row->berat <= '2.4') {
                    $uni += 1;
                }
                if ($row->berat < '2.2') {
                    $under += 1;
                }
                if ($row->berat > '2.4') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.3 - 2.5') {
                if ($row->berat >= '2.3' and $row->berat <= '2.5') {
                    $uni += 1;
                }
                if ($row->berat < '2.3') {
                    $under += 1;
                }
                if ($row->berat > '2.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.5 - Up') {
                if ($row->berat >= '2.5' and $row->berat <= '2.5') {
                    $uni += 1;
                }
                if ($row->berat < '2.5') {
                    $under += 1;
                }
                if ($row->berat > '2.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.2 up') {
                if ($row->berat >= '2.2' and $row->berat <= '3') {
                    $uni += 1;
                }
                if ($row->berat < '2.2') {
                    $under += 1;
                }
                if ($row->berat > '3') {
                    $over += 1;
                }
            }
        }

        $prod               =   Production::find($id);
        $prod->qc_under     =   $under;
        $prod->qc_over      =   $over;
        $prod->qc_uniform   =   $uni;
        $prod->save();
    }

    public function inject_uniformity(Request $request, $id)
    {

        $un       =   Unifomity::where('production_id', $id)->get();

        $uni    = 0;
        $under  = 0;
        $over   = 0;

        foreach ($un as $row) {

            if ($row->uniprod->prodpur->ukuran_ayam == '0.3 - 0.5') {
                if ($row->berat >= '0.3' and $row->berat <= '0.5') {
                    $uni += 1;
                }
                if ($row->berat < '0.3') {
                    $under += 1;
                }
                if ($row->berat > '0.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.4 - 0.6') {
                if ($row->berat >= '0.4' and $row->berat <= '0.6') {
                    $uni += 1;
                }
                if ($row->berat < '0.4') {
                    $under += 1;
                }
                if ($row->berat > '0.6') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.5 - 0.7') {
                if ($row->berat >= '0.5' and $row->berat <= '0.7') {
                    $uni += 1;
                }
                if ($row->berat < '0.5') {
                    $under += 1;
                }
                if ($row->berat > '0.7') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.6 - 0.8') {
                if ($row->berat >= '0.6' and $row->berat <= '0.8') {
                    $uni += 1;
                }
                if ($row->berat < '0.6') {
                    $under += 1;
                }
                if ($row->berat > '0.8') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.7 - 0.9') {
                if ($row->berat >= '0.7' and $row->berat <= '0.9') {
                    $uni += 1;
                }
                if ($row->berat < '0.7') {
                    $under += 1;
                }
                if ($row->berat > '0.9') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.8 - 1.0') {
                if ($row->berat >= '0.8' and $row->berat <= '1.0') {
                    $uni += 1;
                }
                if ($row->berat < '0.8') {
                    $under += 1;
                }
                if ($row->berat > '1.0') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.9 - 1.1') {
                if ($row->berat >= '0.9' and $row->berat <= '1.1') {
                    $uni += 1;
                }
                if ($row->berat < '0.9') {
                    $under += 1;
                }
                if ($row->berat > '1.1') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.0 - 1.2') {
                if ($row->berat >= '1.0' and $row->berat <= '1.2') {
                    $uni += 1;
                }
                if ($row->berat < '1.0') {
                    $under += 1;
                }
                if ($row->berat > '1.2') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.1 - 1.3') {
                if ($row->berat >= '1.1' and $row->berat <= '1.3') {
                    $uni += 1;
                }
                if ($row->berat < '1.1') {
                    $under += 1;
                }
                if ($row->berat > '1.3') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.2 - 1.5') {
                if ($row->berat >= '1.2' and $row->berat <= '1.5') {
                    $uni += 1;
                }
                if ($row->berat < '1.2') {
                    $under += 1;
                }
                if ($row->berat > '1.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.2 - 1.4') {
                if ($row->berat >= '1.2' and $row->berat <= '1.4') {
                    $uni += 1;
                }
                if ($row->berat < '1.2') {
                    $under += 1;
                }
                if ($row->berat > '1.4') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.3 - 1.6') {
                if ($row->berat >= '1.3' and $row->berat <= '1.6') {
                    $uni += 1;
                }
                if ($row->berat < '1.3') {
                    $under += 1;
                }
                if ($row->berat > '1.6') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.3 - 1.5') {
                if ($row->berat >= '1.3' and $row->berat <= '1.5') {
                    $uni += 1;
                }
                if ($row->berat < '1.3') {
                    $under += 1;
                }
                if ($row->berat > '1.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.4 - 1.7') {
                if ($row->berat >= '1.4' and $row->berat <= '1.7') {
                    $uni += 1;
                }
                if ($row->berat < '1.4') {
                    $under += 1;
                }
                if ($row->berat > '1.7') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.4 - 1.6') {
                if ($row->berat >= '1.4' and $row->berat <= '1.6') {
                    $uni += 1;
                }
                if ($row->berat < '1.4') {
                    $under += 1;
                }
                if ($row->berat > '1.6') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.5 - 1.8') {
                if ($row->berat >= '1.5' and $row->berat <= '1.8') {
                    $uni += 1;
                }
                if ($row->berat < '1.5') {
                    $under += 1;
                }
                if ($row->berat > '1.8') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.5 - 1.7') {
                if ($row->berat >= '1.5' and $row->berat <= '1.7') {
                    $uni += 1;
                }
                if ($row->berat < '1.5') {
                    $under += 1;
                }
                if ($row->berat > '1.7') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.6 - 1.8') {
                if ($row->berat >= '1.6' and $row->berat <= '1.8') {
                    $uni += 1;
                }
                if ($row->berat < '1.6') {
                    $under += 1;
                }
                if ($row->berat > '1.8') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.7 - 1.9') {
                if ($row->berat >= '1.7' and $row->berat <= '1.9') {
                    $uni += 1;
                }
                if ($row->berat < '1.7') {
                    $under += 1;
                }
                if ($row->berat > '1.9') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.8 - 2.0') {
                if ($row->berat >= '1.8' and $row->berat <= '2.0') {
                    $uni += 1;
                }
                if ($row->berat < '1.8') {
                    $under += 1;
                }
                if ($row->berat > '2.0') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.9 - 2.1') {
                if ($row->berat >= '1.9' and $row->berat <= '2.1') {
                    $uni += 1;
                }
                if ($row->berat < '1.9') {
                    $under += 1;
                }
                if ($row->berat > '2.1') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.0 - 2.2') {
                if ($row->berat >= '2.0' and $row->berat <= '2.2') {
                    $uni += 1;
                }
                if ($row->berat < '2.0') {
                    $under += 1;
                }
                if ($row->berat > '2.2') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.1 - 2.3') {
                if ($row->berat >= '2.1' and $row->berat <= '2.3') {
                    $uni += 1;
                }
                if ($row->berat < '2.1') {
                    $under += 1;
                }
                if ($row->berat > '2.3') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.2 - 2.4') {
                if ($row->berat >= '2.2' and $row->berat <= '2.4') {
                    $uni += 1;
                }
                if ($row->berat < '2.2') {
                    $under += 1;
                }
                if ($row->berat > '2.4') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.3 - 2.5') {
                if ($row->berat >= '2.3' and $row->berat <= '2.5') {
                    $uni += 1;
                }
                if ($row->berat < '2.3') {
                    $under += 1;
                }
                if ($row->berat > '2.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.5 - Up') {
                if ($row->berat >= '2.5' and $row->berat <= '2.5') {
                    $uni += 1;
                }
                if ($row->berat < '2.5') {
                    $under += 1;
                }
                if ($row->berat > '2.5') {
                    $over += 1;
                }
            } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.2 up') {
                if ($row->berat >= '2.2' and $row->berat <= '3') {
                    $uni += 1;
                }
                if ($row->berat < '2.2') {
                    $under += 1;
                }
                if ($row->berat > '3') {
                    $over += 1;
                }
            }
        }

        $prod               =   Production::find($id);
        $prod->qc_under     =   $under;
        $prod->qc_over      =   $over;
        $prod->qc_uniform   =   $uni;
        $prod->save();

        return "Sukses";
    }

    public function cart($id)
    {
        if (User::setIjin(4)) {
            $data       =   Unifomity::where('production_id', $id)->get();
            $uni        = 0;
            $under      = 0;
            $over       = 0;

            foreach ($data as $row) {

                if ($row->uniprod->prodpur->ukuran_ayam == '0.3 - 0.5') {
                    if ($row->berat >= '0.3' and $row->berat <= '0.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.3') {
                        $under += 1;
                    }
                    if ($row->berat > '0.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.4 - 0.6') {
                    if ($row->berat >= '0.4' and $row->berat <= '0.6') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.4') {
                        $under += 1;
                    }
                    if ($row->berat > '0.6') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.5 - 0.7') {
                    if ($row->berat >= '0.5' and $row->berat <= '0.7') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.5') {
                        $under += 1;
                    }
                    if ($row->berat > '0.7') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.6 - 0.8') {
                    if ($row->berat >= '0.6' and $row->berat <= '0.8') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.6') {
                        $under += 1;
                    }
                    if ($row->berat > '0.8') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.7 - 0.9') {
                    if ($row->berat >= '0.7' and $row->berat <= '0.9') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.7') {
                        $under += 1;
                    }
                    if ($row->berat > '0.9') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.8 - 1.0') {
                    if ($row->berat >= '0.8' and $row->berat <= '1.0') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.8') {
                        $under += 1;
                    }
                    if ($row->berat > '1.0') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '0.9 - 1.1') {
                    if ($row->berat >= '0.9' and $row->berat <= '1.1') {
                        $uni += 1;
                    }
                    if ($row->berat < '0.9') {
                        $under += 1;
                    }
                    if ($row->berat > '1.1') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.0 - 1.2') {
                    if ($row->berat >= '1.0' and $row->berat <= '1.2') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.0') {
                        $under += 1;
                    }
                    if ($row->berat > '1.2') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.1 - 1.3') {
                    if ($row->berat >= '1.1' and $row->berat <= '1.3') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.1') {
                        $under += 1;
                    }
                    if ($row->berat > '1.3') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.2 - 1.5') {
                    if ($row->berat >= '1.2' and $row->berat <= '1.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.2') {
                        $under += 1;
                    }
                    if ($row->berat > '1.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.2 - 1.4') {
                    if ($row->berat >= '1.2' and $row->berat <= '1.4') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.2') {
                        $under += 1;
                    }
                    if ($row->berat > '1.4') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.3 - 1.6') {
                    if ($row->berat >= '1.3' and $row->berat <= '1.6') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.3') {
                        $under += 1;
                    }
                    if ($row->berat > '1.6') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.3 - 1.5') {
                    if ($row->berat >= '1.3' and $row->berat <= '1.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.3') {
                        $under += 1;
                    }
                    if ($row->berat > '1.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.4 - 1.7') {
                    if ($row->berat >= '1.4' and $row->berat <= '1.7') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.4') {
                        $under += 1;
                    }
                    if ($row->berat > '1.7') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.4 - 1.6') {
                    if ($row->berat >= '1.4' and $row->berat <= '1.6') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.4') {
                        $under += 1;
                    }
                    if ($row->berat > '1.6') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.5 - 1.8') {
                    if ($row->berat >= '1.5' and $row->berat <= '1.8') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.5') {
                        $under += 1;
                    }
                    if ($row->berat > '1.8') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.5 - 1.7') {
                    if ($row->berat >= '1.5' and $row->berat <= '1.7') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.5') {
                        $under += 1;
                    }
                    if ($row->berat > '1.7') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.6 - 1.8') {
                    if ($row->berat >= '1.6' and $row->berat <= '1.8') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.6') {
                        $under += 1;
                    }
                    if ($row->berat > '1.8') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.7 - 1.9') {
                    if ($row->berat >= '1.7' and $row->berat <= '1.9') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.7') {
                        $under += 1;
                    }
                    if ($row->berat > '1.9') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.8 - 2.0') {
                    if ($row->berat >= '1.8' and $row->berat <= '2.0') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.8') {
                        $under += 1;
                    }
                    if ($row->berat > '2.0') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '1.9 - 2.1') {
                    if ($row->berat >= '1.9' and $row->berat <= '2.1') {
                        $uni += 1;
                    }
                    if ($row->berat < '1.9') {
                        $under += 1;
                    }
                    if ($row->berat > '2.1') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.0 - 2.2') {
                    if ($row->berat >= '2.0' and $row->berat <= '2.2') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.0') {
                        $under += 1;
                    }
                    if ($row->berat > '2.2') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.1 - 2.3') {
                    if ($row->berat >= '2.1' and $row->berat <= '2.3') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.1') {
                        $under += 1;
                    }
                    if ($row->berat > '2.3') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.2 - 2.4') {
                    if ($row->berat >= '2.2' and $row->berat <= '2.4') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.2') {
                        $under += 1;
                    }
                    if ($row->berat > '2.4') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.3 - 2.5') {
                    if ($row->berat >= '2.3' and $row->berat <= '2.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.3') {
                        $under += 1;
                    }
                    if ($row->berat > '2.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.5 - Up') {
                    if ($row->berat >= '2.5' and $row->berat <= '2.5') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.5') {
                        $under += 1;
                    }
                    if ($row->berat > '2.5') {
                        $over += 1;
                    }
                } elseif ($row->uniprod->prodpur->ukuran_ayam == '2.2 up') {
                    if ($row->berat >= '2.2' and $row->berat <= '3') {
                        $uni += 1;
                    }
                    if ($row->berat < '2.2') {
                        $under += 1;
                    }
                    if ($row->berat > '3') {
                        $over += 1;
                    }
                }
            }

            $count      = ['uni' => $uni, 'under' => $under, 'over' => $over];

            // FILTERING 3 Data Terbesar
            $threeData  = Unifomity::where('production_id', $id)->orderBy('berat','ASC')->get();
            $dataSort   = Unifomity::where('production_id', $id)->orderBy('berat','ASC')->get();
            $low        = Unifomity::select('berat')->where('production_id', $id)->orderBy('berat','ASC')->first();
            $hight      = Unifomity::select('berat')->where('production_id', $id)->orderBy('berat','DESC')->first();
            $sum        = $dataSort->sum('berat');
            $totalData  = count($dataSort);

            $avg        = $totalData > 0 ? number_format(($sum / $totalData),2) : 0;
            $terendah   = $low ? $low->berat: 0;
            $tertinggi  = $hight ? $hight->berat: 0;
            
            $detailsData = array(
                'terendah'  => $terendah,
                'tertinggi' => $tertinggi,
                'ratarata'  => $avg
            );
            // VIEW
            return view('admin.pages.qc.keranjang', compact('data','detailsData' ,'id', 'count'));
        }
        return redirect()->route("index");
    }

    public function edit($id)
    {
        if (User::setIjin(4)) {
            //
        }
        return redirect()->route("index");
    }


    public function update(Request $request)
    {
        if (User::setIjin(4)) {
            $data   =   Production::find($request->x_code);

            if ($request->key == 'tukar_mobil') {
                // dd($request->all());
                DB::beginTransaction();
                $data_lama   = Production::where('id', $request->idlama)->first();
                $data_update = Production::where('id',$request->idupdate)->first();
                $dataTukar   = Production::where('id', $request->idupdate)->first();
                if ($data_update && $data_lama) {
                    $uniformity_lama = Unifomity::whereIn('production_id',[$request->idlama])->get();
                    $uniformity_baru = Unifomity::whereIn('production_id',[$request->idupdate])->get();

                    if ($uniformity_lama) {

                        foreach ($uniformity_lama as $lama) {
                            $lama->production_id = $request->idupdate;
                            $lama->save();
                        }
                    }

                    if ($uniformity_baru) {

                        foreach ($uniformity_baru as $baru) {
                            $baru->production_id = $request->idlama;
                            $baru->save();
                        }
                    }

                    $data_update->qc_over       = $data_lama->qc_over;
                    $data_update->qc_under      = $data_lama->qc_under;
                    $data_update->qc_uniform    = $data_lama->qc_uniform;

                    $data_lama->qc_over         = $dataTukar->qc_over;
                    $data_lama->qc_under        = $dataTukar->qc_under;
                    $data_lama->qc_uniform      = $dataTukar->qc_uniform;





                    $data_lama->save();
                    $data_update->save();

                    DB::commit();
                    return response()->json([
                        'message'   => 'Berhasil tukar mobil',
                        'status'    => 'success',
                        'response'  => '200'
                    ]);

                } else {
                    DB::rollBack();
                    return response()->json([
                        'message'   => 'Terjadi kesalahan',
                        'status'    => 'gagal',
                        'response'  => '400'
                    ]);
                }

            } else if ($data) {
                $antem                  =   new Antemortem;
                $antem->qc_id           =   Auth::user()->id;
                $antem->production_id   =   $data->id;
                $antem->save();

                $postm                  =   new Postmortem;
                $postm->qc_id           =   Auth::user()->id;
                $postm->production_id   =   $data->id;
                $postm->save();
                return redirect()->route('qc.show', $data->id)->with('status', 1)->with('message', 'Silahkan untuk melengkapi data antemortem dan postmortem');
            }
            return back()->with('status', 2)->with('message', 'Terjadi kesalahan saat klik proses');
        }
        return redirect()->route("index");
    }

    public function destroy($id)
    {
        if (User::setIjin(4)) {
            //
        }
        return redirect()->route("index");
    }

    public function nekropsi(Request $request)
    {
        if (User::setIjin(4)) {
            $q      =   $request->q ?? '';
            $data   =   Purchasing::whereIn('status', [1, 2])
                ->get();

            $data   =   $data->filter(function ($item) use ($q) {
                $res = true;
                if ($q != "") {
                    $res =  (false !== stripos($item->purcsupp->nama, $q)) ||
                        (false !== stripos($item->ukuran_ayam, $q)) ||
                        (false !== stripos($item->wilayah_daerah, $q)) ||
                        (false !== stripos($item->type_ekspedisi, $q)) ||
                        (false !== stripos($item->status_purchase, $q)) ||
                        (false !== stripos(number_format($item->jumlah_po), $q)) ||
                        (false !== stripos($item->jumlah_po, $q)) ||
                        (false !== stripos(number_format($item->jumlah_ayam), $q)) ||
                        (false !== stripos($item->jumlah_ayam, $q)) ||
                        (false !== stripos(date('d/m/y', strtotime($item->tanggal_potong)), $q)) ||
                        (false !== stripos($item->tanggal_potong, $q));
                }
                return $res;
            });

            $data   =   $data->paginate(30);

            return view('admin.pages.qc.nekropsi', compact('data', 'q'));
        }
        return redirect()->route("index");
    }

    public function nekropsi_show($id)
    {
        if (User::setIjin(4)) {
            // $data   =   Purchasing::find($id);
            $data   =   Production::find($id);

            if ($data) {
                // if ($data->ayam_mati >= 20) {

                $isi    =   Nekropsi::where('production_id', $data->id)->first();

                return view('admin.pages.qc.nekropsi_show', compact('data', 'isi'));
                // }
            }

            return redirect()->route('qc.nekropsi');
        }
        return redirect()->route("index");
    }

    public function nekropsi_post(Request $request, $id)
    {
        if (User::setIjin(4)) {
            $data   =   Production::find($id);
            // if ($data) {

            //     if ($data->ayam_mati >= 20) {
            $nekpo                              =   Nekropsi::where('production_id', $data->id)->first() ?? new Nekropsi;
            $nekpo->production_id               =   $data->id;
            $nekpo->user_id                     =   Auth::user()->id;
            $nekpo->kondisi_umum                =   $request->kondisi_umum;
            $nekpo->kematian                    =   $data->ayam_mati;
            $nekpo->sp_hidung                   =   $request->hidung;
            $nekpo->sp_trakea                   =   $request->trachea;
            $nekpo->sp_paru                     =   $request->paru_paru;
            $nekpo->sp_kantung_udara            =   $request->air_sacl;
            $nekpo->sp_jantung                  =   $request->jantung;
            $nekpo->sistem_rangka               =   $request->sistem_rangka;
            $nekpo->sistem_otot                 =   $request->sistem_otot;
            $nekpo->sp_tembolok                 =   $request->tembolok;
            $nekpo->sp_lambung                  =   $request->lambung;
            $nekpo->sp_usus                     =   $request->usus;
            $nekpo->sp_hati                     =   $request->hati;
            $nekpo->sistem_kekebalan_tubuh      =   $request->sistem_kekebalan_tubuh;
            $nekpo->diagnosa                    =   $request->diagnosa;
            $nekpo->sp_mata                     =   $request->mata;
            $nekpo->sp_limpa                    =   $request->limpa;
            $nekpo->sp_fabricius                =   $request->fabricius;
            $nekpo->sp_proventriculus           =   $request->proventriculus;
            if ($nekpo->nomor_surat == null) {
                $nekpo->nomor_surat             =   Nekropsi::nosurat();
            }
            $nekpo->save();

            return back()->with('status', 1)->with('message', 'Form nekropsi berhasil diselesaikan');
            //     }

            //     return back();
            // }

            // return redirect()->route('qc.nekropsi');
        }
        return redirect()->route("index");
    }

    public function laporan(Request $request)
    {
        // MILIK QC UMUM
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggalend     =   $request->tanggalend ?? date('Y-m-d');
        $report         =   $request->report ?? '';


        // MILIK QC KEMATIAN AYAM
        $tanggalawal    =   $request->startdateqc ?? date('Y-m-d');
        $tanggalakhir   =   $request->enddateqc ?? date('Y-m-d');


        if($request->key == 'qcumum'){
            $produksi   =   Production::whereIn('purchasing_id', Purchasing::select('id')->where(function ($query) use ($report) {
                                if ($report) {
                                    if ($report == 'po_lb') {
                                        $query->where('jenis_po', 'PO LB');
                                    }
                                    if ($report == 'non_lb') {
                                        $query->where('jenis_po', '!=', 'PO LB');
                                    }
                                }
                            }))
                            ->whereNotNull('no_lpah')
                            ->whereBetween('prod_tanggal_potong', [$tanggal, $tanggalend])
                            ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                            ->get();

            // $JeroanHati     = array();
            // foreach($produksi as $val){
            //     $JeroanHati[]     = $val->post->jeroan_hati ?? '0';
            // }
            // dd($JeroanHati);
           
            return view('admin.pages.bukubesar.component.view_qc_umum', compact('produksi', 'tanggal', 'tanggalend', 'request'));
        }
        else if($request->key == 'kematianayam'){
            $kematianayam   = Production::whereIn('purchasing_id', Purchasing::select('id'))
                            ->whereBetween('prod_tanggal_potong', [$tanggalawal, $tanggalakhir])
                            ->where('po_jenis_ekspedisi','tangkap')
                            ->where( function($q){
                                $q->where('qc_persen_ayam_mati', '>', 1);
                                $q->orWhere('qc_persen_ayam_mati', '=', 1);
                            })
                            ->orderByRaw('prod_tanggal_potong ASC, no_urut ASC')
                            ->get();
            return view('admin.pages.bukubesar.component.view_kematian_ayam', compact('kematianayam', 'tanggalawal', 'tanggalakhir', 'request'));
        }
        return view('admin.pages.bukubesar.index_qc', compact('tanggal', 'tanggalend','tanggalawal','tanggalakhir','request'));
    }

    public function laporanRetur(Request $request)
    {
        $tanggal1    = $request->tanggalstart ?? date('Y-m-d');
        $tanggal2    = $request->tanggalend ?? date('Y-m-d');
        $customer    = Customer::all();

        $retur      = Retur::whereBetween('tanggal_retur', [$tanggal1, $tanggal2])->get();

        if ($request->customer) {
            $retur      = Retur::whereBetween('tanggal_retur', [$tanggal1, $tanggal2])->where('customer_id', $request->customer)->get();
        }

        return view('admin.pages.qc.qc-retur', compact('retur', 'tanggal1', 'tanggal2', 'customer'));
    }

    public function retursummary(Request $request)
    {
        $tanggal1    = $request->tanggalstart ?? date('Y-m-d');
        $tanggal2    = $request->tanggalend ?? date('Y-m-d');
        $customer    = Customer::all();

        $retur       = Retur::whereBetween('tanggal_retur', [$tanggal1, $tanggal2])->get();

        if ($request->customer) {
            $retur      = Retur::whereBetween('tanggal_retur', [$tanggal1, $tanggal2])->where('customer_id', $request->customer)->get();
        }

        return view('admin.pages.retur.retur-summary', compact('retur', 'tanggal1', 'tanggal2', 'customer'));
    }

    public function exportRetur(Request $request)
    {

        $tanggal1    = $request->tanggalstart ?? date('Y-m-d');
        $tanggal2    = $request->tanggalend ?? date('Y-m-d');

        $retur      =   DB::table('VW_Retur')
                            ->whereBetween('tanggal_retur', [$tanggal1, $tanggal2])
                            ->where(function($q) use ($request){
                                if($request->customer){
                                    $q->where('customer_id', $request->customer);
                                }
                            })
                            ->get();

        $html = '<style>th,td {
            mso-number-format:"\@";
            border:thin solid black;
            }</style>';

        $html .= '
            <table class="table default-table" id="export-qc-retur">
                <thead>
                    <tr class="text-center">
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>No RA</th>
                        <th>Nama Item</th>
                        <th>Qty</th>
                        <th>Berat</th>
                        <th>Tujuan</th>
                        <th>Penanganan</th>
                        <th>Kategori</th>
                        <th>Alasan</th>
                    </tr>
                </thead>';

        $total = 0;
        $berat = 0;
        foreach ($retur as $no => $data){
            $total += $data->qty;
            $berat += $data->berat;

            if($data->response){
                try {
                    $resp = json_decode($data->response, TRUE);
                    $ra = $resp[0]['message'];
                } catch (\Throwable $th) {
                    $ra = "";
                }
            }

            $html .= '
            <tr>
                <td>' .++$no .'</td>
                <td>' .$data->tanggal_retur . '</td>
                <td>' .$data->nama. '</td>
                <td>' .$ra. '</td>
                <td>' .$data->item. '</td>
                <td>' .number_format((float)$data->qty, 2, '.', '').'</td>
                <td>' .number_format((float)$data->berat, 2, '.', ''). '</td>
                <td>' .$data->unit. '</td>
                <td>' .$data->penanganan . '</td>
                <td>' .$data->kategori . '</td>
                <td>' .$data->catatan . '</td>
            </tr>';
        }
        $html .= '</table>';

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=export-retur-".$tanggal1."-".$tanggal2.".xls");
        echo $html;
    }
    
    public function oldexportRetur(Request $request)
    {

        $tanggal1    = $request->tanggalstart ?? date('Y-m-d');
        $tanggal2    = $request->tanggalend ?? date('Y-m-d');

        $retur       = Retur::whereBetween('tanggal_retur', [$tanggal1, $tanggal2])->get();

        if ($request->customer) {
            $retur      = Retur::whereBetween('tanggal_retur', [$tanggal1, $tanggal2])->where('customer_id', $request->customer)->get();
        }

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=retur-.csv");
        $fp = fopen('php://output', 'w');

        $retur_header = array(
            "No",
            "Tanggal",
            "Customer",
            "No RA",
            "Nama Item",
            "Qty",
            "Berat",
            "Tujuan",
            "Penanganan",
            "Kategori",
            "Alasan"
        );
        fputcsv($fp, $retur_header);

        foreach ($retur as $no => $data) :
            $total = 0;
            $berat = 0;

            foreach ($data->to_itemretur as $i => $row) :

                $total += $row->qty;
                $berat += $row->berat;
                $retur_item = ReturItem::where('orderitem_id', $row->id)->first();

                $ns = Netsuite::where('tabel_id', $data->id)->where('label', 'receipt_return')->where('tabel', 'retur')->first();
                $ra = "";
                if ($ns) {

                    try {
                        //code...
                        $resp = json_decode($ns->response, TRUE);
                        $ra = $resp[0]['message'];
                    } catch (\Throwable $th) {
                        //throw $th;
                        $ra = "";
                    }
                }

                $list_retur = array(
                    ++$no,
                    $data->tanggal_retur,
                    $data->to_customer->nama,
                    $ra,
                    $row->to_item->nama,
                    str_replace(".", ",", $row->qty),
                    str_replace(".", ",", $row->berat),
                    $row->unit,
                    $row->penanganan,
                    $row->kategori,
                    $row->catatan
                );

                fputcsv($fp, $list_retur);

            endforeach;

        endforeach;

        fclose($fp);
    }

    public function export(Request $request)
    {
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggal_akhir  =   $request->tanggalakhir ?? date('Y-m-d');

        $data   =   DB::table('VW_Retur')
            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
            ->where(function ($query) use ($request) {
                if ($request->customer_data) {
                    if (is_numeric($request->customer_data)) {
                        $query->whereIn('nama', Customer::select('nama')->where('id', $request->customer_data));
                    }
                }
            })
            ->get();
        $file = "retur_" . $tanggal . " - " . $tanggal_akhir . ".xls";
        $html = '<style>th,td {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

        $html .= '
                <table class="table default-table" id="export-table-lpah">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Customer</th>
                        <th>No SO</th>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Tujuan</th>
                        <th>Kategori</th>
                        <th>Qty</th>
                        <th>Berat</th>
                        <th>Catatan</th>
                        <th>No RA</th>
                    </tr>
                    </thead>
                ';

        foreach ($data as $i => $val) {
            $resp = json_decode($val->response, TRUE);
            $ra = $resp[0]['message'] ?? '';

            $html .= '
                <tr>
                    <td>' . ++$i . '</td>
                    <td>' . $val->nama . '</td>
                    <td>' . $val->no_so . '</td>
                    <td>' . $val->tanggal_retur . '</td>
                    <td>' . $val->item . '</td>
                    <td>' . $val->tujuan . '</td>
                    <td>' . $val->kategori . '</td>
                    <td>' . $val->qty . '</td>
                    <td>' . $val->berat . '</td>
                    <td>' . $val->catatan . '</td>
                    <td>' . $ra . '</td>
                </tr>
                ';
        }

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$file");
        echo $html;
    }

    public function laporanlpah(Request $request)
    {
        $tanggal_potong_awal     =   $request->tanggal_potong_awal ?? date('Y-m-d');
        $tanggal_potong_akhir    =   $request->tanggal_potong_akhir ?? date('Y-m-d');

        $produksi   =   Production::where('sc_status', '1')
            ->whereBetween('prod_tanggal_potong', [$tanggal_potong_awal, $tanggal_potong_akhir])
            ->orderBy('no_urut', 'asc')->get();

        return view('admin.pages.qc.lpah', compact('produksi', 'tanggal_potong_awal', 'tanggal_potong_akhir'));
    }

    public function kualitaskarkas(Request $request){
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggalend     =   $request->tanggalend ?? date('Y-m-d');
        $report         =   $request->report ?? '';

        $produksi = Production::whereBetween('prod_tanggal_potong', [$tanggal, $tanggalend])
                                ->join('table_qc_postmortem','table_qc_postmortem.production_id','=','productions.id')
                                ->join('purchasing','purchasing.id','=','productions.purchasing_id')
                                ->join('supplier','supplier.id','=','purchasing.supplier_id')
                                ->select('supplier.nama as sup_nama',
                                        DB::raw('COUNT(sc_no_polisi)AS jml_mobil'),'sc_nama_kandang',
                                        DB::raw('SUM(table_qc_postmortem.memar_dada)AS count_memar_dada'),
                                        DB::raw('SUM(table_qc_postmortem.memar_paha)AS count_memar_paha'),
                                        DB::raw('SUM(table_qc_postmortem.memar_sayap)AS count_memar_sayap'),
                                        DB::raw('SUM(table_qc_postmortem.patah_sayap)AS count_patah_sayap'),
                                        DB::raw('SUM(table_qc_postmortem.patah_kaki)AS count_patah_kaki'),
                                        DB::raw('SUM(table_qc_postmortem.keropeng_kaki)AS count_keropeng_kaki'),
                                        DB::raw('SUM(table_qc_postmortem.keropeng_dada)AS count_keropeng_dada'),
                                        DB::raw('SUM(table_qc_postmortem.keropeng_sayap)AS count_keropeng_sayap'),
                                        DB::raw('SUM(table_qc_postmortem.keropeng_pg)AS count_keropeng_pg'),
                                        DB::raw('SUM(table_qc_postmortem.kehijauan)AS count_kehijauan'),
                                        DB::raw('SUM(table_qc_postmortem.tembolok_jumlah)AS count_tembolok_jumlah'),
                                        DB::raw('SUM(table_qc_postmortem.keropeng_dengkul)AS count_keropeng_dengkul'),
                                    )
                                ->where(function ($query) use ($report){
                                    if ($report) {
                                        if ($report == 'po_lb') {
                                            $query->where('jenis_po', 'PO LB');
                                        }
                                        if ($report == 'non_lb') {
                                            $query->where('jenis_po', '!=', 'PO LB');
                                        }
                                    }
                                })
                                ->groupBy('supplier.nama')
                                ->orderBy('supplier.nama','asc')
                                ->get();
        // dd($produksi);
        return view('admin.pages.qc.kualitas-karkas',compact('request','tanggal','tanggalend','produksi'));
    }
}

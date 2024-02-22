<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\AppKey;
use App\Models\Bahanbaku;
use App\Models\Grading;
use Illuminate\Http\Request;
use App\Models\Production;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Chiller;
use App\Models\Evis;
use App\Models\Gudang;
use App\Models\Log;
use App\Models\Lpah;
use App\Models\Purchasing;
use App\Models\User;
use App\Models\Netsuite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GradingController extends Controller
{

    public function index(Request $request)
    {
        if(User::setIjin(5)){
            // $tanggal    =   $request->tanggal ?? Carbon::now()->format('Y-m-d');
            $tanggalawal        =   $request->tanggalawal ?? Carbon::now()->format('Y-m-d');
            $tanggalakhir       =   $request->tanggalakhir ?? Carbon::now()->format('Y-m-d');

            if ($request->key == 'unduhdata') {
                header("Content-type: application/csv");
                header("Content-Disposition: attachment; filename=Laporan_Produksi_Grading-" . $tanggalawal . ' - ' . $tanggalakhir . ".csv");
                $fp = fopen('php://output', 'w');
                fputcsv($fp, ["sep=,"]);

                $data = array(
                    "No",
                    "Tanggal",
                    "Nomor LPAH",
                    "Supplier",
                    "Supir",
                    "Nomor Urut",
                    "Ukuran Ayam",
                    "Item",
                    "Jenis",
                    "SKU",
                    "Qty",
                    "Berat",
                );
                fputcsv($fp, $data);

                $produksi   =   Production::whereBetween('lpah_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                ->where('lpah_status', 1)
                                // Production::whereDate('grading_selesai', $tanggal)
                                ->orderByRaw('grading_selesai ASC, no_urut ASC')
                                ->get();

                foreach ($produksi as $list) {
                    $grading    =   Grading::where('trans_id', $list->id)
                                    ->get();

                    foreach ($grading as $i => $row) :
                        if($list->grading_selesai == NULL){
                            $defaultTanggal         = $list->prod_tanggal_potong;
                        }else{
                            $defaultTanggal         = $list->grading_selesai;
                        }
                        
                        $data = array(
                            $i + 1,
                            // $tanggal,
                            date('Y-m-d', strtotime($defaultTanggal)) ?? $defaultTanggal,
                            $list->no_lpah ?? '###',
                            $list->prodpur->purcsupp->nama ?? '###',
                            $list->sc_pengemudi ?? '###',
                            $list->no_urut ?? '###',
                            $list->prodpur->ukuran_ayam,
                            $row->graditem->nama,
                            $row->jenis_karkas,
                            $row->graditem->sku,
                            str_replace(".", ",", $row->total_item),
                            str_replace(".", ",", $row->berat_item),
                        );
                        fputcsv($fp, $data);
                    endforeach;
                }

                fclose($fp);

                return "";

            } else if ($request->key == "POLB") {
                $grading       =   Production::where('no_urut', '!=', NULL)
                                    ->where('po_jenis_ekspedisi', '!=', 'other')
                                    ->whereIn('purchasing_id', Purchasing::select('id')
                                    // ->whereDate('tanggal_potong', $tanggal)
                                    ->whereIn('type_po', ['PO LB','PO Maklon']))
                                    // ->whereDate('lpah_tanggal_potong', $tanggal)
                                    ->whereBetween('lpah_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                    ->whereIn('lpah_status', [1, 2, 3])
                                    ->orderByRaw('lpah_tanggal_potong ASC, no_urut ASC')
                                    ->get();

                $count          =   Production::whereIn('lpah_status', [1,2, 3])
                                    ->where('po_jenis_ekspedisi', '!=', 'other')
                                    ->whereIn('purchasing_id', Purchasing::select('id')
                                        // ->whereDate('tanggal_potong', $tanggal)
                                    ->whereIn('type_po', ['PO LB', 'PO Maklon'])
                                    )
                                    ->where(function ($query) {
                                        $query->orWhere('grading_status', NULL);
                                        $query->orWhere('grading_status', 1);
                                        $query->orWhere('grading_status', 2);
                                        $query->orWhere('grading_status', 3);
                                    })
                                    ->whereBetween('lpah_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                    ->count('id');

                $sum            =   Production::select(DB::raw("SUM(sc_berat_do) AS berat"), DB::raw("SUM(sc_ekor_do) AS ekor"))
                                    ->whereIn('lpah_status', [1,2,3])
                                    ->where('po_jenis_ekspedisi', '!=', 'other')
                                    ->whereIn('purchasing_id', Purchasing::select('id')
                                        ->whereIn('type_po', ['PO LB', 'PO Maklon'])
                                    )
                                    ->where(function ($query) {
                                        $query->orWhere('grading_status', NULL);
                                        $query->orWhere('grading_status', 1);
                                        $query->orWhere('grading_status', 2);
                                        $query->orWhere('grading_status', 3);
                                    })
                                    ->whereBetween('lpah_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                    ->first();

                $mobil_lama       =   Production::where('no_urut', '!=', NULL)
                                    ->whereIn('purchasing_id', Purchasing::select('id')
                                    ->whereIn('type_po', ['PO LB','PO Maklon']))
                                    ->where('sc_status', 1)
                                    ->whereIn('lpah_status', [1, 2])
                                    ->whereNull('grading_status')
                                    ->get();

                $berat  =   0;
                $item   =   0;
                foreach($grading as $prod){
                    foreach($prod->prodgrad as $grad){
                        if ($grad->keranjang == "0") {
                            $berat  +=  $grad->stock_berat;
                            $item   +=  $grad->stock_item;
                        }
                    }
                }

                $total  =   [
                    'jumlah'        =>  $count,
                    'berat'         =>  $sum->berat,
                    'ekor'          =>  $sum->ekor,
                    'sumberat'      =>  $berat,
                    'sumekor'       =>  $item,
                ];

                return view('admin.pages.grading.grading-polb', compact('total', 'tanggalawal', 'tanggalakhir', 'grading', 'mobil_lama'));

            } else if ($request->key == "NONLB") {

                $gradingnonlb  =   Production::whereIn('ppic_acc',[1,2,3])
                                    ->where('ppic_tujuan', 'grading')
                                    ->whereIn('purchasing_id', Purchasing::select('id')
                                    // ->whereDate('tanggal_potong', $tanggal)
                                    ->whereBetween('tanggal_potong', [$tanggalawal, $tanggalakhir])
                                    ->where('type_po', '!=', 'PO LB')
                                    ->where('type_po', '!=', 'PO Maklon'))
                                    ->get();

                $countnonlb     =   Production::whereIn('lpah_status', [1,2,3])
                                    ->where('ppic_tujuan', 'grading')
                                    ->whereIn('purchasing_id', Purchasing::select('id')
                                    // ->whereDate('tanggal_potong', $tanggal)
                                    ->whereBetween('tanggal_potong', [$tanggalawal, $tanggalakhir])
                                    ->where('type_po', '!=', 'PO LB')
                                    ->where('type_po', '!=', 'PO Maklon')
                                    )
                                    ->where(function ($query) {
                                        $query->orWhere('grading_status', NULL);
                                        $query->orWhere('grading_status', 1);
                                        $query->orWhere('grading_status', 2);
                                        $query->orWhere('grading_status', 3);
                                    })
                                    ->count('id');

                $sum            =   Production::select(DB::raw("SUM(sc_berat_do) AS berat"), DB::raw("SUM(sc_ekor_do) AS ekor"))
                                    ->whereIn('lpah_status', [1,2,3])
                                    ->where('po_jenis_ekspedisi', '!=', 'other')
                                    ->whereIn('purchasing_id', Purchasing::select('id')
                                        ->whereIn('type_po', ['PO LB', 'PO Maklon'])
                                    )
                                    ->where(function ($query) {
                                        $query->orWhere('grading_status', NULL);
                                        $query->orWhere('grading_status', 1);
                                        $query->orWhere('grading_status', 2);
                                        $query->orWhere('grading_status', 3);
                                    })
                                    ->whereBetween('lpah_tanggal_potong', [$tanggalawal, $tanggalakhir])
                                    ->first();

                $mobil_lama       =   Production::where('no_urut', '!=', NULL)
                                    ->whereIn('purchasing_id', Purchasing::select('id')
                                    ->whereIn('type_po', ['PO LB','PO Maklon']))
                                    ->where('sc_status', 1)
                                    ->whereIn('lpah_status', [1, 2])
                                    ->whereNull('grading_status')
                                    ->get();

                $beratnonlb  =   0;
                $itemnonlb   =   0;
                foreach($gradingnonlb as $prod){
                    foreach($prod->prodgrad as $grad){
                        if ($grad->keranjang == "0") {
                            $beratnonlb  +=  $grad->stock_berat;
                            $itemnonlb   +=  $grad->stock_item;
                        }
                    }
                }

                $total  =   [
                    'jumlahnonlb'   =>  $countnonlb,
                    'berat'         =>  $sum->berat,
                    'ekor'          =>  $sum->ekor,
                    'sumberatnonlb' =>  $beratnonlb,
                    'sumekornonlb'  =>  $beratnonlb,
                ];

                return view('admin.pages.grading.grading-pononlb', compact('total', 'tanggalawal', 'tanggalakhir', 'gradingnonlb', 'mobil_lama'));

            } else {
                return view('admin.pages.grading.index', compact('tanggalawal', 'tanggalakhir'));
            }
        }
        return redirect()->route("index");
    }

    public function memar(Request $request, $id)
    {
        $data       =   Production::find($id);
        $purch      =   Purchasing::find($data->purchasing_id);
        $sku        =   '12113';
        $select     = $request->select ?? "";

        if ($purch->jenis_ayam == 'Broiler') {
            $sku    =   '12113';
        }
        elseif ($purch->jenis_ayam == 'Kampung') {
            $sku    =   '12123';
        }
        elseif ($purch->jenis_ayam == 'Pejantan') {
            $sku    =   '12133';
        }
        elseif ($purch->jenis_ayam == 'Parent') {
            $sku    =   '12143';
        }

        $item            =  Item::where('sku', 'LIKE', $sku.'%')
                            ->where('sku', 'not like', '%00000')
                            ->get();

        $grading_last    =  Grading::where('trans_id', $data->id)
                            ->orderBy('id', 'desc')
                            ->first();

        $type            =  'memar';
        return view('admin.pages.grading.item-grid', compact('item', 'grading_last', 'type', 'select'));
    }

    public function pejantan(Request $request, $id)
    {
        $data   =   Production::find($id);

        $purch  =   Purchasing::find($data->purchasing_id);
        $select     = $request->select ?? "";

        $sku    =   '12131';
        if ($purch->jenis_ayam == 'Broiler') {
            $sku    =   '12111';
        }
        elseif ($purch->jenis_ayam == 'Kampung') {
            $sku    =   '12121';
        }
        elseif ($purch->jenis_ayam == 'Pejantan') {
            $sku    =   '12131';
        }
        elseif ($purch->jenis_ayam == 'Parent') {
            $sku    =   '12141';
        }

        $item            =  Item::where('nama', 'LIKE', '%PEJANTAN%')->where('sku', 'not like', '%00000')->where('nama', 'not like', '%RM%')->whereIn('category_id',[1,12, 17])->get();
        $grading_last    =  Grading::where('trans_id', $data->id)->orderBy('id', 'desc')->first();
        $type            =  'pejantan';
        return view('admin.pages.grading.item-grid', compact('item', 'grading_last', 'type', 'select'));
    }

    public function normal(Request $request, $id)
    {
        $data   =   Production::find($id);

        $purch  =   Purchasing::find($data->purchasing_id);
        $select     = $request->select ?? "";

        $sku    =   '12111';
        if ($purch->jenis_ayam == 'Broiler') {
            $sku    =   '12111';
        }
        elseif ($purch->jenis_ayam == 'Kampung') {
            $sku    =   '12121';
        }
        elseif ($purch->jenis_ayam == 'Pejantan') {
            $sku    =   '12131';
        }
        elseif ($purch->jenis_ayam == 'Parent') {
            $sku    =   '12141';
        }

        $item            =  Item::where('sku', 'LIKE', $sku.'%')->where('sku', 'not like', '%00000')->get();
        $grading_last    =  Grading::where('trans_id', $data->id)->orderBy('id', 'desc')->first();
        $type            =  'normal';
        return view('admin.pages.grading.item-grid', compact('item', 'grading_last', 'type', 'select'));
    }

    public function utuh(Request $request, $id)
    {
        $data   =   Production::find($id);

        $purch  =   Purchasing::find($data->purchasing_id);
        $select     = $request->select ?? "";

        $sku    =   '12112';
        if ($purch->jenis_ayam == 'Broiler') {
            $sku    =   '12112';
        }
        elseif ($purch->jenis_ayam == 'Kampung') {
            $sku    =   '12122';
        }
        elseif ($purch->jenis_ayam == 'Pejantan') {
            $sku    =   '12132';
        }
        elseif ($purch->jenis_ayam == 'Parent') {
            $sku    =   '12142';
        }

        $item            =  Item::where('sku', 'LIKE', $sku.'%')->where('sku', 'not like', '%00000')->orderBy('SKU', 'ASC')->get();
        $grading_last    =  Grading::where('trans_id', $data->id)->orderBy('id', 'desc')->first();
        $type            =  'normal';
        return view('admin.pages.grading.item-grid', compact('item', 'grading_last', 'type', 'select'));
    }


    public function parent(Request $request, $id)
    {
        $data   =   Production::find($id);

        $purch  =   Purchasing::find($data->purchasing_id);
        $select     = $request->select ?? "";

        // $sku    =   ['12141', '12143'];
        if ($purch->jenis_ayam == 'Broiler') {
            $sku    =   '12112';
        }
        elseif ($purch->jenis_ayam == 'Kampung') {
            $sku    =   '12122';
        }
        elseif ($purch->jenis_ayam == 'Pejantan') {
            $sku    =   '12132';
        }
        elseif ($purch->jenis_ayam == 'Parent') {
            $sku    =   '12142';
        }

        $item            =  Item::
                            where(function($query) {
                                $query->where('sku', 'like', '12141%')->orWhere('sku', 'like', '12143%');
                            })->where('sku', 'not like', '%00000')->orderBy('SKU', 'ASC')->get();
        

        $grading_last    =  Grading::where('trans_id', $data->id)->orderBy('id', 'desc')->first();
        $type            =  'normal';
        return view('admin.pages.grading.item-grid', compact('item', 'grading_last', 'type', 'select'));
    }

    public function store(Request $request)
    {
        if(User::setIjin(5)){

            DB::beginTransaction();

            $prod                   =   Production::find($request->x_code);
            $prod->grading_status   =   2;
            $prod->evis_user_id     =   Auth::user()->id;
            if (!$prod->save()) {
                DB::rollBack() ;
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }

            DB::commit();
            return redirect()->route('grading.show', $prod->id)->with('status', 1)->with('message', 'Selesaikan Grading');
        }
        return redirect()->route("index");
    }


    public function show(Request $request, $id)
    {
        // dd($request->key, $id);

        if(User::setIjin(5)){



            $data       =   Production::where('id', $id)
                            ->whereIn('grading_status', [1, 2, 3])
                            ->first();
            $summary    =   Grading::where('trans_id', $id)->orderBy('id', 'DESC')->get();
            $evis       =   Evis::where('production_id', $id)->sum('berat_item');

            $item       =   Item::where('category_id', '1')->get();

            $count      =   0;
            $sumberat   =   0;
            $sumekor    =   0;
            foreach ($summary as $row) {
                $count      +=  1;
                $sumberat   +=  $row->berat_item;
                $sumekor    +=  $row->total_item;
            }

            $total  =   [
                'jumlah'    =>  $count,
                'berat'     =>  $sumberat,
                'ekor'      =>  $sumekor
            ];

            if ($data) {
                if ($request->key == 'receiptulang') {
                    $data->grading_status       = '2';
                    $data->grading_selesai      = NULL;
                    $data->save();

                    $receiptUlang = 'true';
                    return view('admin.pages.grading.grading-detail', compact('data', 'summary', 'total', 'item', 'evis', 'receiptUlang'));

                }

                $receiptUlang = 'false';
                return view('admin.pages.grading.grading-detail', compact('data', 'summary', 'total', 'item', 'evis', 'receiptUlang'));
            }

            return redirect()->route('grading.index')->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route("index");
    }


    public function result(Request $request, $id)
    {

        if(User::setIjin(5)){
            $data       =   Production::where('id', $id)
                            ->whereIn('grading_status', [1, 2])
                            ->first();

            $summary    =   Grading::where('trans_id', $id)->orderBy('id', 'DESC')->get();

            $count      =   0;
            $sumberat   =   0;
            $sumekor    =   0;
            foreach ($summary as $row) {
                $count      +=  1;
                $sumberat   +=  $row->berat_item;
                $sumekor    +=  $row->total_item;
            }

            $total  =   [
                'jumlah'    =>  $count,
                'berat'     =>  $sumberat,
                'ekor'      =>  $sumekor,
                'rerata'    =>  $sumekor ? ($sumberat / $sumekor) : 0
            ];

            $receiptUlang = $request->receiptUlang;

            return view('admin.pages.grading.result', compact('data', 'total', 'receiptUlang'));
        }
        return redirect()->route("index");
    }

    public function kalkulasi($id)
    {
        if(User::setIjin(5)){
            $data       =   Production::where('id', $id)
                            ->whereIn('grading_status', [1, 2, 3])
                            ->first();

            $evis       =   Evis::where('production_id', $id)->sum('berat_item');
            $summary    =   Grading::where('trans_id', $id)->where('keranjang', 0)->orderBy('id', 'DESC')->get();
            $sumberat   =   0;
            $sumekor    =   0;
            foreach ($summary as $row) {
                $sumberat   +=  $row->berat_item;
                $sumekor    +=  $row->total_item;
            }

            $total  =   [
                'berat'     =>  $sumberat,
                'ekor'      =>  $sumekor
            ];

            return view('admin.pages.grading.kalkulasi', compact('data', 'total','evis'));
        }
        return redirect()->route("index");
    }

    public function add(Request $request)
    {
        if(User::setIjin(5)){
            $validator  =    Validator::make($request->all(), [
                'x_code'    =>  ['required', Rule::exists('productions', 'id')->where('grading_status', 2)],
                'berat'     =>  'required',
                'result'    =>  'required',
                'part'      =>  'required',
                'jenis'     =>  'required|in:memar,normal,utuh,pejantan,parent',
            ]);

            if ($validator->fails()) {
                $data['status'] =   400 ;
                $data['msg']    =   'Data tidak lengkap' ;
                return $data ;
            }

            $data   =   Production::where('grading_status', 2)
                        ->where('id', $request->x_code)
                        ->first();

            DB::beginTransaction() ;

            if ($request->idedit == null) {
                $grad   =   new Grading;
            } else {
                $grad   =   Grading::find($request->idedit);
            }

            $grad->total_item       =   $request->result;
            $grad->berat_item       =   $request->berat;
            $grad->stock_item       =   $request->result;
            $grad->stock_berat      =   $request->berat;
            $grad->item_id          =   $request->part;
            if($data->prodpur->type_po=="PO Karkas"){
                $grad->tanggal_potong   =   $request->tanggalKarkas;
            } else {
                $grad->tanggal_potong   =   $data->prod_tanggal_potong;
            }
            $grad->keranjang        =   $request->keranjang / 2;
            $grad->berat_keranjang  =   $request->keranjang;
            $grad->keterangan       =   $request->keterangan;
            $grad->trans_id         =   $request->x_code;
            $grad->jenis_karkas     =   $request->jenis;

            $item = Item::find($request->part);
            if($item){

                $grad->grade_item      =   Item::item_jenis($item->id) ?? "";

                if($data->prodpur->type_po=="PO Karkas"){
                    $grad->keterangan      =   $item->nama;
                }

            }

            if (!$grad->save()) {
                DB::rollBack();
                $data['status'] =   400;
                $data['msg']    =   'Proses Gagal';
                return $data;
            }

            DB::commit() ;
        }
        return redirect()->route("index");
    }

    public function edit(Request $request, $id)
    {
        if(User::setIjin(5)){
            // UPDATE TANGGAL PO NON LB 

            if ($request->key == 'updateTanggalPONonLB') {
                $tanggal                        = $request->tanggal;
                $id                             = $request->id;
                
                $grading                        = Grading::find($id);

                if ($grading) {

                    $json   =   [
                            'data_lama' =>  $grading->tanggal_potong,
                            'data_baru' =>  $tanggal
                    ];

                    $grading->tanggal_potong        = $tanggal;
                    $grading->save();

                    
                    $edit                       =   new Adminedit;
                    $edit->user_id              =   Auth::user()->id;
                    $edit->table_name           =   'grading';
                    $edit->table_id             =    $id;
                    $edit->activity             =   'grading';
                    $edit->content              =   'EDIT Tanggal bahan baku';
                    $edit->type                 =   'edit';
                    $edit->data                 =   json_encode($json);
                    $edit->key                  =   NULL ;
                    $edit->status               =   1;
                    if (!$edit->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
    
    
                    $chiller                        = Chiller::where('table_name', 'production')->where('table_id', $grading->trans_id)->where('item_id', $grading->item_id)
                                                        ->where('qty_item', $grading->stock_item)->where('berat_item', $grading->stock_berat)->first();
                    
                    if ($chiller) {

                        $chiller->tanggal_potong    = $tanggal;
                        $chiller->tanggal_produksi  = $tanggal;
                        $chiller->save();

                        return response()->json([
                            'msg'               => 'Sukses mengganti tanggal',
                            'status'            => 200,
                            'idProduction'      => $grading->trans_id
                        ]);

                    } else {
                        return response()->json([
                            'msg'       => 'Qty/Berat tidak sesuai, Data sudah digunakan',
                            'status'    => 400
                        ]);
                    }
                } else {
                    return response()->json([
                        'msg'       => 'Data tidak ditemukan',
                        'status'    => 400
                    ]);
                }




            } else 

            if ($request->key == 'petugas') {
                $data                   =   Production::find($id);
                $data->grading_user_nama=   $request->nama_petugas;
                $data->save();

                return '';
            } else {
                return Grading::find($request->row_id);
            }
        }
        return redirect()->route("index");
    }

    public function update(Request $request, $id)
    {

        if(User::setIjin(5)){
            // dd($request->all());
            $data   =   Production::whereIn('grading_status', [2, 3])
                        ->where('id', $id)
                        ->first();

            if ($data) {
                if (!$data->grading_user_nama) {
                    return back()->with('status', 2)->with('message', 'Proses Gagal. Nama petugas belum diisikan');
                }

                if ($data->prodpur->type_po == 'PO Karkas') {
                    $cekDataValidasiReceipt = Grading::where('trans_id', $data->id)->where('status', '1')->get();
                    if ($cekDataValidasiReceipt) {
                        $cekDataStatusGrading = Grading::where('trans_id', $data->id)->where('status', NULL)->get();
                        if (count($cekDataStatusGrading) < 1) {
                            return back()->with('status', 2)->with('message', 'Proses Gagal. Silahkan isi item');
                        }
                    }
                }

                DB::beginTransaction();

                if ($request->key == 'send') {
                    $data->grading_selesai  =   $request->tanggal;
                    $data->grading_status   =   1;
                    $data->grading_user_id  =   Auth::user()->id;
                    if (!$data->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Terjadi kesalahan saat menyimpan data gradding');
                    }

                    if ($data->prodpur->type_po == 'PO Karkas') {
                        $grading                =   Grading::where('trans_id', $data->id)->whereNull('status')->get() ;
                    } else {
                        $grading                =   Grading::where('trans_id', $data->id)->get() ;
                    }
                    $tglpotong              =   $grading[0]->tanggal_potong;
                    foreach ($grading as $row) {
                        if ($data->prodpur->jenis_po == 'PO Karkas') {
                            $newchiler                      =   new Chiller;
                            $newchiler->table_name          =   'production';
                            $newchiler->table_id            =   $data->id;
                            $newchiler->asal_tujuan         =   'karkasbeli';
                            $newchiler->type                =   'bahan-baku';
                            $newchiler->label               =   $data->prodpur->no_po ?? "";
                            $newchiler->item_id             =   $row->item_id;
                            $newchiler->item_name           =   $row->graditem->nama;
                            $newchiler->tanggal_potong      =   $request->tanggal;
                            $newchiler->tanggal_produksi    =   $request->tanggal;
                            $newchiler->keranjang           =   $row->keranjang;
                            $newchiler->berat_keranjang     =   $row->berat_keranjang;
                            $newchiler->qty_item            =   $row->total_item;
                            $newchiler->stock_item          =   $row->total_item;
                            $newchiler->berat_item          =   $row->berat_item;
                            $newchiler->stock_berat         =   $row->berat_item;
                            $newchiler->status              =   2;
                            $newchiler->jenis               =   'masuk';
                            if (!$newchiler->save()) {
                                DB::rollBack();
                                return back()->with('status', 2)->with('message', 'Proses gagal');
                            }
                        } else {
                            $grad   =   Chiller::
                                        where(function($query) use ($tglpotong) {
                                            if($tglpotong == Carbon::now()){
                                                $query->whereDate('tanggal_potong', Carbon::now());
                                            }else{
                                                $query->whereDate('tanggal_potong', $tglpotong);
                                            }
                                        })
                                        ->where('item_id', $row->item_id)
                                        ->where('status', 2)
                                        ->where('type', 'bahan-baku')
                                        ->where('asal_tujuan', 'gradinggabungan')
                                        ->first();

                            if ($grad) {

                                $grad->berat_keranjang      =   $grad->berat_keranjang + $row->berat_keranjang;
                                $grad->qty_item             =   $grad->qty_item + $row->total_item;
                                $grad->stock_item           =   $grad->stock_item + $row->total_item;
                                $grad->berat_item           =   $grad->berat_item + $row->berat_item;
                                $grad->stock_berat          =   $grad->stock_berat + $row->berat_item;

                                if (!$grad->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal');
                                }

                            } else {
                                $newchiler                      =   new Chiller;
                                // $newchiler->production_id       =   $data->trans_id ?? '';
                                $newchiler->asal_tujuan         =   'gradinggabungan';
                                $newchiler->type                =   'bahan-baku';
                                $newchiler->item_id             =   $row->item_id;
                                $newchiler->item_name           =   $row->graditem->nama;
                                $newchiler->tanggal_potong      =   $data->lpah_tanggal_potong ;
                                $newchiler->tanggal_produksi    =   $data->lpah_tanggal_potong ;
                                $newchiler->keranjang           =   $row->keranjang;
                                $newchiler->berat_keranjang     =   $row->berat_keranjang;
                                $newchiler->qty_item            =   $row->total_item;
                                $newchiler->stock_item          =   $row->total_item;
                                $newchiler->berat_item          =   $row->berat_item;
                                $newchiler->stock_berat         =   $row->berat_item;
                                $newchiler->status              =   2;
                                $newchiler->jenis               =   'masuk';
                                if (!$newchiler->save()) {
                                    DB::rollBack();
                                    return back()->with('status', 2)->with('message', 'Proses gagal');
                                }
                            }
                        }

                    }

                    DB::commit();

                    $grading                =   Grading::where('trans_id', $data->id)->get() ;
                    $tglpotong              =   $grading[0]->tanggal_potong;
                    foreach ($grading as $row) {
                        $grad   =   Chiller::
                                        where(function($query) use ($tglpotong) {
                                            if($tglpotong == Carbon::now()){
                                                $query->whereDate('tanggal_potong', Carbon::now());
                                            }else{
                                                $query->whereDate('tanggal_potong', $tglpotong);
                                            }
                                        })
                                        ->where('item_id', $row->item_id)
                                        ->where('status', 2)
                                        ->where('type', 'bahan-baku')
                                        ->where('asal_tujuan', 'gradinggabungan')
                                        ->first();

                        if ($grad) {

                                Chiller::recalculate_chiller($grad->id);
                        }
                    }

                    if ($data->prodpur->type_po == 'PO Karkas') {
                        Netsuite::item_receipt_grading($data->id);
                    }
                    return redirect()->route('grading.show', $data->id)->with('message', 'Proses berhasil diselesaikan');
                    // return back()->with('status', 1)->with('message', 'Proses berhasil diselesaikan');
                } else {
                    $produksi                      =   Production::find($data->id);
                    $produksi->grading_selesai     =   $request->tanggal;
                    $produksi->grading_status      =   $request->key == 'edit' ? 2 : 3;
                    $produksi->grading_user_id     =   Auth::user()->id;
                    if (!$produksi->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Terjadi kesalahan saat menyimpan data gradding');
                    }

                    DB::commit();
                    return back()->with('status', 1)->with('message', $request->key == 'edit' ? 'Silahkan perbaharui data grading' : 'Data grading berahsil disimpan');
                }

            }
            DB::rollBack() ;
            return back()->with('status', 2)->with('message', 'Terjadi kesalahan saat menyelesaikan timbangan Grading');
        }
        return redirect()->route("index");
    }

    public function injectGradingIR(Request $request, $id) {
        $data   =   Production::where('id', $id)
                    ->first();

        if ($data->prodpur->type_po == 'PO Karkas') {
            Netsuite::item_receipt_grading($data->id);
        }

        return redirect()->route('grading.show', $data->id)->with('message', 'Proses berhasil diselesaikan');
    }

    public function cart(Request $request,$id)
    {
        if(User::setIjin(5)){
            $prod   =   Production::find($id);
            $data   =   Grading::where('trans_id', $id)->where('item_id', '<>', NULL)->orderBy('id', 'DESC')->get();

            if($request->key == "history"){
                $historyId = $request->id;
                $histories = Adminedit::where('table_id', $historyId)->get();
                // dd($histories);
                return view('admin.pages.grading.modal-history', compact('histories'));
            }

            return view('admin.pages.grading.keranjang', compact('data', 'prod'));
        }
        return redirect()->route("index");
    }

    public function ubah(Request $request, $id)
    {
        $prod   =   Production::find($id);
        $data   =   Grading::where('trans_id', $id)
                    ->where('id', $request->x_code)
                    ->first() ;

        if ($request->checker) {
        // Digunakan checker untuk track data lama. Yang atas ($data) untuk proses update. Jangan dihapus ya ini :)
        $old    =   Grading::where('trans_id', $id)
                    ->where('id', $request->x_code)
                    ->first() ;
        }

        DB::beginTransaction() ;

        if ($prod->grading_status == 1) {


            if($prod->prodpur->type_po=="PO LB"){
                $clonechiller_gabung         =   Chiller::where('item_id', $data->item_id)
                                            ->where('asal_tujuan', 'gradinggabungan')
                                            ->whereDate('tanggal_produksi', $prod->lpah_tanggal_potong);
                                            // ->first() ;

                // Clone data && Recalculate Data Awal dulu baru sajikan data setelah direcalculate
                try {
                    $clonecalculate             = clone $clonechiller_gabung;
                    $calculate                  = $clonecalculate->first();

                    // Sajikan Data Setelah di Recalculate
                    $clonegabung                     = clone $clonechiller_gabung;
                    $chiller_gabung                  = $clonegabung->first();


                    if($chiller_gabung){
                        $chiller_gabung->qty_item       =   ($chiller_gabung->qty_item  - $data->total_item) + $request->ekor;
                        $chiller_gabung->stock_item     =   ($chiller_gabung->stock_item  - $data->total_item) + $request->ekor;
                        $chiller_gabung->berat_item     =   ($chiller_gabung->berat_item  - $data->berat_item) + $request->berat;
                        $chiller_gabung->stock_berat    =   ($chiller_gabung->stock_berat  - $data->berat_item) + $request->berat;

                        if (!$chiller_gabung->save()) {
                            DB::rollBack();
                            return back()->with('status', 2)->with('message', 'Proses gagal');
                        }
                    }
                } catch (\Throwable $th) {

                }

            }

            if($prod->prodpur->type_po=="PO Karkas"){
                $chiller_gabung         =   Chiller::where('item_id', $data->item_id)
                                            ->where('asal_tujuan', 'karkasbeli')
                                            ->whereDate('tanggal_produksi', $prod->lpah_tanggal_potong)
                                            ->first() ;

                if($chiller_gabung){
                    $chiller_gabung->qty_item       =   ($chiller_gabung->qty_item  - $data->total_item) + $request->ekor;
                    $chiller_gabung->stock_item     =   ($chiller_gabung->stock_item  - $data->total_item) + $request->ekor;
                    $chiller_gabung->berat_item     =   ($chiller_gabung->berat_item  - $data->berat_item) + $request->berat;
                    $chiller_gabung->stock_berat    =   ($chiller_gabung->stock_berat  - $data->berat_item) + $request->berat;


                    if (!$chiller_gabung->save()) {
                        DB::rollBack();
                        return back()->with('status', 2)->with('message', 'Proses gagal');
                    }
                }
            }
        }

        $data->total_item       =   $request->ekor ;
        $data->stock_item       =   $request->ekor;
        $data->berat_item       =   $request->berat ;
        $data->stock_berat      =   $request->berat;
        $data->keterangan       =   $request->keterangan;

        if ($request->checker) {
            $json   =   [
                'data_lama' =>  $old,
                'data_baru' =>  $data
            ];

            $edit                       =   new Adminedit;
            $edit->user_id              =   Auth::user()->id;
            $edit->table_name           =   'grading';
            $edit->table_id             =   $data->id;
            $edit->activity             =   'checker';
            $edit->content              =   'EDIT ITEM ' . $data->graditem->nama;
            $edit->type                 =   'edit';
            $edit->data                 =   json_encode($json);
            $edit->key                  =   $prod->id ;
            $edit->status               =   1;
            if (!$edit->save()) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Proses gagal');
            }
        }

        if (!$data->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses gagal');
        }

        DB::commit();

        try {
            Chiller::recalculate_chiller($calculate->id);
        } catch (\Throwable $th) {

        }

        try {
            Chiller::recalculate_chiller($chiller_gabung->id);
        } catch (\Throwable $th) {

        }

        return back()->with('status', 1)->with('message', 'Ubah data berhasil');
    }

    public function destroy(Request $request, $id)
    {
        $data   =   Grading::find($request->id) ;
        $prod   =   Production::find($id) ;

        if ($prod->grading_status == 1) {
            Grading::recalculate($request->id);
        }

        if ($request->key == 'checker') {
            $edit                       =   new Adminedit;
            $edit->user_id              =   Auth::user()->id;
            $edit->table_name           =   'grading';
            $edit->table_id             =   $data->id;
            $edit->activity             =   'checker';
            $edit->content              =   'HAPUS ITEM ' . $data->graditem->nama;
            $edit->data                 =   json_encode($data);
            $edit->type                 =   'hapus';
            $edit->key                  =   $prod->id;
            $edit->status               =   1;
            $edit->save() ;
        }

        return $data->delete();

        if ($prod->prodpur->type_po != 'PO Karkas') {
            $chiller     =   Chiller::where('item_id', $data->item_id)
                                    ->where('asal_tujuan', 'gradinggabungan')
                                    ->whereDate('tanggal_produksi', $prod->lpah_tanggal_potong)
                                    ->first();
        }

        // $calculate             = clone $chiller->first();
        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {

        }

    }
}

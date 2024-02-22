<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\DataOption;
use App\Models\Freestock;
use App\Models\Gudang;
use App\Models\Item;
use App\Models\Log;
use App\Models\Netsuite;
use App\Models\Production;
use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogController extends Controller
{
    //

    public function index(Request $request){

        $tanggal = $request->tanggal ?? date('Y-m-d');

        $logs = Log::orderBy('id', 'desc')->where('trans_date', $tanggal)->paginate(30);
        return view('admin.pages.log.index')
                ->with('logs', $logs);
    }

    public function customExportSync(Request $request){
        return view('admin.pages.log.custom-export-netsuite');
    }

    public function injectSync(Request $request){

        return false;
        $tanggal_awal = $request->awal ?? date('Y-m-d');
        $tanggal_akhir = $request->akhir ?? date('Y-m-d');
        $status         = $request->status ?? "";
        $type         = $request->type ?? "";
        $netsuite = Netsuite::orderBy('id', 'asc')->whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir]);

        if($type=="wo"){
            $netsuite = $netsuite->whereIn('record_type', ['work_order', 'wo_build']);
        }

        foreach($netsuite as $ns):

        endforeach;
    }

    public function export(Request $request){

        $tanggal = $request->tanggal ?? date('Y-m-d');

        $logs = Log::orderBy('id', 'desc')->where('trans_date', $tanggal)->paginate(30);

    }

    public function syncProcess(){

        $logs = Log::whereIn('sync_status', [0,1])->get();
        $return = array();

        foreach($logs as $row):

            DB::beginTransaction();
            $row->sync_start_at = date('Y-m-d H:i:s');

            $api_url = env('APP_CODE', '');
            if($api_url=='clg_local'){

                $url = DataOption::getOption('sync_url') ?? 'https://muhhusniaziz.com/cgl/sync.php';

                $ch = curl_init($url);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $row->table_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

                $server_output = curl_exec($ch);

                curl_close ($ch);

                $result = json_decode($server_output);
                $resp = [];
                if($result->status=="1"){
                    $row->sync = "completed";
                    $row->sync_status = 2;
                    $row->sync_completed_at = date('Y-m-d H:i:s');
                    $row->save();

                    DB::commit();

                    $resp = array(
                        'id' => $row->id,
                        'status' => "success"
                    );
                }else{
                    DB::rollBack();
                    $resp = array(
                        'id' => $row->id,
                        'status' => "failed"
                    );
                }

            }

            $return[] = $resp;

        endforeach;

        return $return;

    }

    public function syncProcessID(Request $request){
        $id = $request->id;
        Artisan::call('Sync:process --id='.$id);

        return "OK";
    }

    public function syncProcessCustom(Request $request){
        $tanggal_awal   =   $request->tanggal_awal ?? date('Y-m-d');
        $tanggal_akhir  =   $request->tanggal_akhir ?? date('Y-m-d');

        $search     =   $request->search ?? "" ;
        $status     =   $request->status ?? "" ;
        $type         = $request->type ?? "";

            $netsuite   =   Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])
                            ->where(function($query) use ($search) {

                                $multi_search = explode(";", $search);
                                if(count($multi_search)>0){
                                    for($i=0; $i<count($multi_search); $i++){
                                        $query->orWhere('label', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('location', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('id_location', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('document_code', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('record_type', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('response_id', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('id', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('paket_id', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('response', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('data_content', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('failed', 'like', '%' . $multi_search[$i] . '%') ;
                                    }
                                }else{
                                    $query->orWhere('label', 'like', '%' . $search . '%') ;
                                    $query->orWhere('location', 'like', '%' . $search . '%') ;
                                    $query->orWhere('id_location', 'like', '%' . $search . '%') ;
                                    $query->orWhere('document_code', 'like', '%' . $search . '%') ;
                                    $query->orWhere('record_type', 'like', '%' . $search . '%') ;
                                    $query->orWhere('response_id', 'like', '%' . $search . '%') ;
                                    $query->orWhere('id', 'like', '%' . $search . '%') ;
                                    $query->orWhere('paket_id', 'like', '%' . $search . '%') ;
                                    $query->orWhere('response', 'like', '%' . $search . '%') ;
                                    $query->orWhere('data_content', 'like', '%' . $search . '%') ;
                                    $query->orWhere('failed', 'like', '%' . $search . '%') ;
                                }
                            })
                            ->orderBy('id', 'desc');

            if($status == ""){

            }elseif($status=="null") {
                $netsuite = $netsuite->where('status', NULL);
            }else{
                $netsuite = $netsuite->where('status', $status);
            }

            if($type!=""){
                if($type=="wo"){
                    $netsuite = $netsuite->whereIn('record_type', ['work_order','wo_build']);
                }

                if($type=="itemfulfill"){
                    $netsuite = $netsuite->whereIn('record_type', ['item_fulfill']);
                }

                if($type=="itemreceipt"){
                    $netsuite = $netsuite->whereIn('record_type', ['itemreceipt']);
                }
                if($type=="return"){
                    $netsuite = $netsuite->whereIn('record_type', ['return_authorization', 'receipt_return']);
                }
                if($type=="transfer_inventory"){
                    $netsuite = $netsuite->whereIn('record_type', ['transfer_inventory']);
                }

                if ($type == 'wo1') {
                    $netsuite = $netsuite->whereIn('label', ['wo-1','wo-1-build']);
                }
                if ($type == 'wo2') {
                    $netsuite = $netsuite->whereIn('label', ['wo-2', 'wo-2-build','wo-2-marinasi', 'wo-2-build-marinasi','wo-2-whole', 'wo-2-build-whole','wo-2-parting', 'wo-2-build-parting', 'wo-2-frozen', 'wo-2-build-frozen', 'wo-2-boneless', 'wo-2-build-boneless','wo-2-byproduct', 'wo-2-build-byproduct']);
                }
                if ($type == 'wo3') {
                    $netsuite = $netsuite->whereIn('label', ['wo-3','wo-3-build', 'wo-3-build-abf-cs', 'wo-3-abf-cs']);
                }
                if ($type == 'wo4') {
                    $netsuite = $netsuite->whereIn('label', ['wo-4', 'wo-4-build', 'wo-4-thawing', 'wo-4-build-thawing']);
                }
                if ($type == 'wo6') {
                    $netsuite = $netsuite->whereIn('label', ['wo-6','wo-6-build']);
                }
                if ($type == 'wo7') {
                    $netsuite = $netsuite->whereIn('label', ['wo-7','wo-7-build']);
                }
            }

            $netsuite   = $netsuite->whereIn('status', [5])->update(['status'=>'2']);

            return back()->with('status', 1)->with('message', 'Integrasi berhasil dijalankan');
    }

    public function indexwo2(Request $request)
    {
        $tanggal_awal   =   $request->tanggal_awal ?? date('Y-m-d');
        $tanggal_akhir  =   $request->tanggal_akhir ?? date('Y-m-d');
        $filterregu     =   $request->filterregu ?? "";

        if ($request->key == 'data') {
            if(env('NET_SUBSIDIARY', 'CGL')=="CGL"){
                $data   =   Freestock::where('netsuite_id', NULL)
                            // ->whereNotIn('regu', ['byproduct'])
                            ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
                            ->where('status', 3)
                            ->where('netsuite_send', NULL)
                            ->where(function($query) use ($filterregu) {
                                if($filterregu != ""){
                                    $query->where('regu',$filterregu);
                                }
                            })
                            ->groupBy('regu', 'tanggal')
                            ->get() ;
            }else{
                $data   =   Freestock::where('netsuite_id', NULL)
                            ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
                            ->where('status', 3)
                            ->where('netsuite_send',NULL)
                            ->where(function($query) use ($filterregu) {
                                if($filterregu != ""){
                                    $query->where('regu',$filterregu);
                                }
                            })
                            ->groupBy('regu', 'tanggal')
                            ->get() ;
            }

            return view('admin.pages.log.wo_2create-data', compact('data'));
        } else {
            return view('admin.pages.log.wo_2create', compact('tanggal_awal', 'tanggal_akhir'));
        }
    }

    public function indexSync(Request $request){

        $tanggal_awal   =   $request->tanggal_awal ?? date('Y-m-d');
        $tanggal_akhir  =   $request->tanggal_akhir ?? date('Y-m-d');

        $search     =   $request->search ?? "" ;
        $status     =   $request->status ?? "" ;
        $type         = $request->type ?? "";
        $page         = $request->page ?? "1";

        if ($request->key == 'show') {

            $netsuite   =   Netsuite::with(['dataUsers', 'dataProductions', 'dataRetur', 'dataRetur.to_customer', 'dataProductions.prodpur', 'dataRetur.data_order', 'dataChillerTI', 'dataProductGudang', 'dataBahanBakuTI'])
                            ->select('netsuite.*')
                            ->whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])
                            ->where(function($query) use ($search) {

                                $multi_search = explode(";", $search);
                                if(count($multi_search)>0){
                                    for($i=0; $i<count($multi_search); $i++){
                                        $query->orWhere('label', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('location', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('id_location', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('document_code', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('record_type', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('response_id', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('id', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('paket_id', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('response', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('data_content', 'like', '%' . $multi_search[$i] . '%') ;
                                        $query->orWhere('failed', 'like', '%' . $multi_search[$i] . '%') ;
                                    }
                                }else{
                                    $query->orWhere('label', 'like', '%' . $search . '%') ;
                                    $query->orWhere('location', 'like', '%' . $search . '%') ;
                                    $query->orWhere('id_location', 'like', '%' . $search . '%') ;
                                    $query->orWhere('document_code', 'like', '%' . $search . '%') ;
                                    $query->orWhere('record_type', 'like', '%' . $search . '%') ;
                                    $query->orWhere('response_id', 'like', '%' . $search . '%') ;
                                    $query->orWhere('id', 'like', '%' . $search . '%') ;
                                    $query->orWhere('paket_id', 'like', '%' . $search . '%') ;
                                    $query->orWhere('response', 'like', '%' . $search . '%') ;
                                    $query->orWhere('data_content', 'like', '%' . $search . '%') ;
                                    $query->orWhere('failed', 'like', '%' . $search . '%') ;
                                }
                            })
                            // ->withoutAppends()
                            ->orderBy('id', 'desc');

            if($status == ""){

            }elseif($status=="null") {
                $netsuite = $netsuite->where('status', NULL);
            }else{
                $netsuite = $netsuite->where('status', $status);
            }

            if($type!=""){
                if($type=="wo"){
                    $netsuite = $netsuite->whereIn('record_type', ['work_order','wo_build']);
                }

                if($type=="itemfulfill"){
                    $netsuite = $netsuite->whereIn('record_type', ['item_fulfill']);
                }

                if($type=="itemreceipt"){
                    $netsuite = $netsuite->whereIn('record_type', ['itemreceipt']);
                }
                if($type=="return"){
                    $netsuite = $netsuite->whereIn('record_type', ['return_authorization', 'receipt_return']);
                }
                if($type=="transfer_inventory"){
                    $netsuite = $netsuite->whereIn('record_type', ['transfer_inventory']);
                }

                if ($type == 'wo1') {
                    $netsuite = $netsuite->whereIn('label', ['wo-1','wo-1-build']);
                }
                if ($type == 'wo2') {
                    $netsuite = $netsuite->whereIn('label', ['wo-2', 'wo-2-build','wo-2-marinasi', 'wo-2-build-marinasi','wo-2-whole', 'wo-2-build-whole','wo-2-parting', 'wo-2-build-parting', 'wo-2-frozen', 'wo-2-build-frozen', 'wo-2-boneless', 'wo-2-build-boneless','wo-2-byproduct', 'wo-2-build-byproduct']);
                }
                if ($type == 'wo3') {
                    $netsuite = $netsuite->whereIn('label', ['wo-3','wo-3-build', 'wo-3-build-abf-cs', 'wo-3-abf-cs']);
                }
                if ($type == 'wo4') {
                    $netsuite = $netsuite->whereIn('label', ['wo-4', 'wo-4-build', 'wo-4-thawing', 'wo-4-build-thawing']);
                }
                if ($type == 'wo6') {
                    $netsuite = $netsuite->whereIn('label', ['wo-6','wo-6-build']);
                }
                if ($type == 'wo7') {
                    $netsuite = $netsuite->whereIn('label', ['wo-7','wo-7-build']);
                }
            }

            // $netsuite   = $netsuite->paginate(30);

            // $semua      = Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])->get()->count();
            // $gagal      = Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])->where('status', '0')->get()->count();
            // $sukses     = Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])->where('status', '1')->get()->count();
            // $pending    = Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])->where('status', '2')->get()->count();
            // $batal      = Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])->where('status', '3')->get()->count();
            // $antrian    = Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])->where('status', '4')->get()->count();
            // $approval   = Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])->where('status', '5')->get()->count();
            // $hold       = Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])->where('status', '6')->get()->count();
            // $null       = Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])->whereNull('status')->get()->count();

            // $hitung['semua']            = $semua - $null;
            // $hitung['approval']         = $approval;
            // $hitung['gagal']            = $gagal;
            // $hitung['pending']          = $pending;
            // $hitung['batal']            = $batal;
            // $hitung['antrian']          = $antrian;
            // $hitung['sukses']           = $sukses;
            // $hitung['hold']             = $hold;
            // $hitung['totalOGP']         = $pending + $antrian;
            // $hitung['integrasi']        = ($semua - $null) - ($hold + $approval + $batal);

            $netsuite   = $netsuite->paginate(15);

            $monthawal      = self::bulan(date("m",strtotime($tanggal_awal)));
            $monthakhir     = self::bulan(date("m",strtotime($tanggal_akhir)));
            $yearawal       = date("Y",strtotime($tanggal_awal));
            $yearakhir      = date("Y",strtotime($tanggal_akhir));
            $partition      = $yearawal == $yearakhir ? "p".$yearawal : "p".$yearawal.",p".$yearakhir;
            $query          = DB::select("select
                                        SUM(CASE WHEN `status` = 0 THEN 1 ELSE 0 END) AS gagal,
                                        SUM(CASE WHEN `status` = 1 THEN 1 ELSE 0 END) AS sukses,
                                        SUM(CASE WHEN `status` = 2 THEN 1 ELSE 0 END) AS pending,
                                        SUM(CASE WHEN `status` = 3 THEN 1 ELSE 0 END) AS batal,
                                        SUM(CASE WHEN `status` = 4 THEN 1 ELSE 0 END) AS antrian,
                                        SUM(CASE WHEN `status` = 5 THEN 1 ELSE 0 END) AS approval,
                                        SUM(CASE WHEN `status` = 6 THEN 1 ELSE 0 END) AS hold,
                                        SUM(CASE WHEN `status` IS NULL THEN 1 ELSE 0 END) AS nulldata
                                    from netsuite where trans_date between '".$tanggal_awal."' AND '".$tanggal_akhir."' AND deleted_at IS NULL");
            foreach($query as $item){
                    $gagal      = $item->gagal ?? 0;
                    $sukses     = $item->sukses ?? 0;
                    $pending    = $item->pending ?? 0;
                    $batal      = $item->batal ?? 0;
                    $antrian    = $item->antrian ?? 0;
                    $approval   = $item->approval ?? 0;
                    $hold       = $item->hold ?? 0;
                    $nulldata   = $item->nulldata ?? 0;
            }


            $semua                      = $gagal+$sukses+$pending+$batal+$antrian+$approval+$hold;
            $hitung['semua']            = $semua - $nulldata;
            $hitung['approval']         = $approval;
            $hitung['gagal']            = $gagal;
            $hitung['pending']          = $pending;
            $hitung['batal']            = $batal;
            $hitung['antrian']          = $antrian;
            $hitung['sukses']           = $sukses;
            $hitung['hold']             = $hold;
            $hitung['totalOGP']         = $pending + $antrian;
            $hitung['integrasi']        = ($semua - $nulldata) - ($hold + $approval + $batal);
            return view('admin.pages.log.netsuite_data', compact('tanggal_awal', 'tanggal_akhir', 'netsuite', 'search', 'status', 'type', 'page', 'hitung'));
        } else {
            return view('admin.pages.log.netsuite', compact('tanggal_awal', 'tanggal_akhir', 'search', 'status', 'type', 'page'));
        }

    }

    public function bulan($bln)
    {
        switch ($bln)
        {
            case 1:
                return "jan";
                break;
            case 2:
                return "feb";
                break;
            case 3:
                return "mar";
                break;
            case 4:
                return "apr";
                break;
            case 5:
                return "may";
                break;
            case 6:
                return "jun";
                break;
            case 7:
                return "jul";
                break;
            case 8:
                return "aug";
                break;
            case 9:
                return "sep";
                break;
            case 10:
                return "oct";
                break;
            case 11:
                return "nov";
                break;
            case 12:
                return "des";
                break;
        }
    }

    public function wo_control(Request $request)
    {
        $tanggal_awal   = $request->tanggal_awal;
        $tanggal_akhir  = $request->tanggal_akhir;
        $netsuite       =   Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])
                        ->whereIn('record_type', ['work_order'])
                        ->get() ;

        return view('admin.pages.log.wo_control', compact('request', 'netsuite', 'tanggal_awal', 'tanggal_akhir')) ;
        
    }


    public function wo_total(Request $request)
    {
        $tanggal_awal   = $request->tanggal_awal;
        $tanggal_akhir  = $request->tanggal_akhir;
        $netsuite       =   Netsuite::whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])
                        ->whereIn('record_type', ['work_order'])
                        ->get() ;

        return view('admin.pages.log.wo_total', compact('request', 'netsuite', 'tanggal_awal', 'tanggal_akhir')) ;
    }

    public function streamFunction(Request $request){
        $response = new StreamedResponse();
        
        $fields = ['id', 'name', 'email'];

        $response->setCallback(function () use ($fields, $request) {
            $handle = fopen('php://output', 'w');
            
            // set CSV header
            fputcsv($handle, $fields);
            
            // fill the data, using chunk by 1000
            User::select($fields)
                ->where($request->all())
                ->chunk(1000, function($users) use (&$handle) {
                    $users->each(function($user) use (&$handle) {
                        fputcsv($handle, $user->toArray());
                    });
                });
                
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    public function showSync($id)
    {
        $data   =   Netsuite::find($id);

        if ($data) {
            return view('admin.pages.log.netsuite_show', compact('data'));
        }
        return redirect()->route('sync.index');
    }

    public function detail($id)
    {
        $data   =   Netsuite::find($id);

        if ($data) {
            return view('admin.pages.log.netsuite_detail', compact('data'));
        }
        return redirect()->route('sync.index');
    }


    public function postSync(Request $request, $id)
    {
        $data   =   Netsuite::find($id);

        if ($data) {
            $data->record_type      =   $request->record_type ;
            $data->label            =   $request->label ;
            $data->trans_date       =   $request->trans_date ;
            $data->tabel            =   $request->tabel ;
            $data->tabel_id         =   $request->tabel_id ;
            $data->location         =   $request->location ;
            $data->id_location      =   $request->id_location ;
            $data->subsidiary       =   $request->subsidiary ;
            $data->subsidiary_id    =   $request->subsidiary_id ;
            $data->script           =   $request->script ;
            $data->paket_id         =   $request->paket_id ;
            $data->deploy           =   $request->deploy ;
            $data->data_content     =   $request->data_content ;
            $data->response         =   $request->response ;
            $data->failed           =   $request->failed ;
            $data->response_id      =   $request->response_id ;
            $data->status           =   $request->status ;
            $data->save();

            return back()->with('status', 1)->with('message', 'Ubah data berhasil') ;
        }
        return redirect()->route('sync.index');
    }


    public function customDownloadSync(Request $request){

        $tanggal_awal   =   $request->awal ?? date('Y-m-d');
        $tanggal_akhir  =   $request->akhir ?? date('Y-m-d');
        $status         =   $request->status ?? "";
        $type           =   $request->type ?? "";
        $netsuite       =   Netsuite::orderBy('id', 'asc')->whereBetween('trans_date', [$tanggal_awal, $tanggal_akhir])
                            ->where(function($query) use($status,$type){
                                if($status!=""){
                                    $query->where('status', $status);
                                }
                                if ($type != "") {
                                    if ($type == "wo") {
                                        $query->whereIn('record_type', ['work_order', 'wo_build']);
                                    }

                                    if ($type == "itemfulfill") {
                                        $query->whereIn('record_type', ['item_fulfill']);
                                    }

                                    if ($type == "itemreceipt") {
                                        $query->whereIn('record_type', ['itemreceipt']);
                                    }
                                    if ($type == "return") {
                                        $query->whereIn('record_type', ['return_authorization', 'receipt_return']);
                                    }
                                    if ($type == "transfer_inventory") {
                                        $query->whereIn('record_type', ['transfer_inventory']);
                                    }
                                    if ($type == "transfer_inventory_do") {
                                        $query->whereIn('record_type', ['transfer_inventory', 'item_fulfill']);
                                        $query->where('document_code', 'like', '%SO%');
                                        $query->orderBy('created_at', 'asc');
                                        $query->orderBy('document_code', 'asc');
                                    }
                                    if ($type == "gudang_retur") {
                                        $query->wherein('label', ['return_authorization', 'receipt_return', 'ti_retur_fg', 'ti_retur_chillerbb', 'ti_retur_abf', 'wo-6', 'ti_retur_storage_susut']);
                                        $query->orderBy('created_at', 'asc');
                                    }
                                    if ($type == "gudang_lb") {
                                        $query->whereIn('label', ['wo-1', 'wo-1-build', 'ti_livebird_bahanbaku']);
                                    }
                                    if ($type == 'wo1') {
                                        $query->whereIn('label', ['wo-1', 'wo-1-build']);
                                    }
                                    if ($type == 'wo2') {
                                        $query->whereIn('label', ['wo-2', 'wo-2-build','wo-2-marinasi', 'wo-2-build-marinasi','wo-2-whole', 'wo-2-build-whole','wo-2-parting', 'wo-2-build-parting', 'wo-2-frozen', 'wo-2-build-frozen', 'wo-2-boneless', 'wo-2-build-boneless','wo-2-byproduct', 'wo-2-build-byproduct']);
                                    }
                                    if ($type == 'wo3') {
                                        $query->whereIn('label', ['wo-3', 'wo-3-build', 'wo-3-build-abf-cs', 'wo-3-abf-cs']);
                                    }
                                    if ($type == 'wo4') {
                                        $query->whereIn('label', ['wo-4', 'wo-4-build', 'wo-4-thawing', 'wo-4-build-thawing']);
                                    }
                                    if ($type == 'wo6') {
                                        $query->whereIn('label', ['wo-6', 'wo-6-build']);
                                    }
                                    if ($type == 'wo7') {
                                        $query->whereIn('label', ['wo-7', 'wo-7-build']);
                                    }
                                }
                            })
                            ->get();

        if ($request->key == 'view') {
            return view('admin.pages.log.show_dataexport', compact('netsuite', 'request', 'type'));
        } else {
            $tasks = $netsuite;

            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=NS_".$type.' - '.$tanggal_awal. ' - '.$tanggal_akhir.".csv");
            $file = fopen('php://output', 'w');
            fputcsv($file,["sep=,"]);

            if($type=="itemreceipt"){
                $columns = array("No", "ID", "Label","Tanggal", "Activity","LocID","Location","IntID","Paket","DocumentNo", "Internal Id","Status","Timestamp","Timestamp Update", "No PO","Tanggal Nota","Item","Nama Item","Berat","Qty","Location", "Response", "Failed", "Data");
                fputcsv($file, $columns);
            }
            if($type=="itemfulfill"){
                $columns = array("No", "ID", "Label","Tanggal", "Activity","LocID","Location","IntID","Paket","DocumentNo", "Internal Id","Status","Timestamp","Timestamp Update", "No SO","Tanggal DO","Memo", "Item","Berat","Qty","Location", "Response", "Failed", "Data");
                fputcsv($file, $columns);
            }
            if($type=="transfer_inventory"){
                $columns = array("No", "ID", "Label","Tanggal", "Activity","LocID","Location","IntID","Paket","DocumentNo", "Internal Id","Status","Timestamp","Timestamp Update","Tanggal","Memo", "From", "To", "Item","Nama Item","Berat","Response", "Failed", "Data");
                fputcsv($file, $columns);
            }
            if($type=="transfer_inventory_do"){
                $columns = array('Tanggal', 'Document No', 'Item', 'From', 'To', 'Masuk', 'Keluar', 'NO SO');
                fputcsv($file, $columns);
            }
            if($type=="gudang_lb"){
                $columns = array('Tanggal', 'Document Terkait', 'Item', 'From', 'To', 'Masuk', 'Keluar');
                fputcsv($file, $columns);
            }

            if($type=="gudang_retur"){
                $columns = array('Tanggal', 'Document No', 'Item', 'From', 'To', 'Masuk', 'Keluar', 'Dokumen Terkait');
                fputcsv($file, $columns);
            }

            if ($type == "return") {
                $columns = array("ID", "Label","Tanggal", "Activity","LocID","Location","IntID","Paket","DocumentNo", "Internal Id","Status","Timestamp","Timestamp Update","Tanggal RA", "Memo", "Item","Berat","Qty","Location", "Response", "Failed", "Data");
                fputcsv($file, $columns);
            }

            if($type==""){
                $columns = array("No", "ID", "Label","Tanggal", "Activity","IDLoc","Location","IntID","Paket","DocumentNo", "Item", "Data","Response", "Internal Id","Failed","Status");
                fputcsv($file, $columns);
            }

            if($type=="wo" || $type=="wo1" || $type=="wo2" || $type=="wo3" || $type=="wo4" || $type=="wo5" || $type=="wo6" || $type =="wo7"){
                $columns = array("No", "ID", "Label","Tanggal", "Activity","LocID","Location","IntID","Paket","DocumentNo", "Internal Id","Status","Timestamp","Timestamp Update","Tanggal","Assembly ID", "Assembly", "Location", "Type","Internal ID Item","Item", "Description", "Berat","Response", "Failed");
                fputcsv($file, $columns);
            }

            foreach ($tasks as $no => $task) {
                $row['No']          = ++$no;
                $row['ID']          = $task->id;
                $row['Label']       = $task->label;
                $row['trans_date']  = $task->trans_date;
                $row['Activity']    = $task->record_type;
                $row['DocumentCode'] = $task->document_code;
                $row['IDLoc']       = $task->id_location;
                $row['Location']    = $task->location;
                $row['IntID']       = $task->tabel_id;
                $row['Paket']       = $task->paket_id;
                $row['DocumentNo']  = $task->document_no ?? $task->id;
                $row['Data']        = $task->data_content;
                $row['ResponseId']  = $task->response_id;
                $row['Response']    = $task->response;
                $row['Failed']      = $task->failed;
                $row['Status']      = $task->status;
                $row['Timestamp']   = $task->created_at;
                $row['Update']      = $task->updated_at;

                $ext = json_decode($task->data_content);

                if($type=="itemreceipt"){

                    try {
                        //code...
                        $ext = json_decode($task->data_content);

                        for($l=0; $l<count($ext->data[0]->line); $l++){
                            fputcsv($file, array(
                                $row['No'],
                                $row['ID'],
                                $row['Label'],
                                $row['trans_date'],
                                $row['Activity'],
                                $row['IDLoc'],
                                $row['Location'],
                                $row['IntID'],
                                $row['Paket'],
                                $row['DocumentNo'],
                                $row['ResponseId'],
                                $row['Status'],
                                $row['Timestamp'],
                                $row['Update'],
                                $ext->data[0]->po_number,
                                $ext->data[0]->tanggal_nota,
                                $ext->data[0]->line[$l]->item_code,
                                Item::where('sku',$ext->data[0]->line[$l]->item_code)->first()->nama,
                                number_format($ext->data[0]->line[$l]->qty,1,',', '.'),
                                // str_replace(".", ",", $ext->data[0]->line[$l]->qty),
                                $ext->data[0]->line[$l]->qty_in_ekor,
                                $ext->data[0]->line[$l]->gudang,
                                $row['Response'],
                                $row['Failed'],
                                $row['Data'])
                            );
                        }

                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                }
                if($type=="itemfulfill"){

                    try {
                        //code...
                        $ext = json_decode($task->data_content);

                        if (!empty($ext->data[0]->items)) {
                            foreach ($ext->data[0]->items as $it) {
                                fputcsv($file, array(
                                    $row['No'],
                                    $row['ID'],
                                    $row['Label'],
                                    $row['trans_date'],
                                    $row['Activity'],
                                    $row['IDLoc'],
                                    $row['Location'],
                                    $row['IntID'],
                                    $row['Paket'],
                                    $row['DocumentNo'],
                                    $row['ResponseId'],
                                    $row['Status'],
                                    $row['Timestamp'],
                                    $row['Update'],
                                    $ext->data[0]->so_number,
                                    $ext->data[0]->date_so,
                                    $ext->data[0]->memo,
                                    $it->item,
                                    number_format($it->qty, 1, ',', '.'),
                                    $it->qty_in_ekr_pcs_pack,
                                    $it->gudang,
                                    $row['Response'],
                                    $row['Failed'],
                                    $row['Data']
                                ));
                            }
                        } else {
                            // Jika $ext->data[0]->items kosong, tetap catat baris kosong atau pesan lainnya
                            fputcsv($file, array('Data Kosong'));
                        }
                            

                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                }

                if($type=="transfer_inventory"){

                    try {
                        //code...
                        $ext = json_decode($task->data_content);

                        foreach($ext->data[0]->line as $it):
                            fputcsv($file, array(
                                $row['No'],
                                $row['ID'],
                                $row['Label'],
                                $row['trans_date'],
                                $row['Activity'],
                                $row['IDLoc'],
                                $row['Location'],
                                $row['IntID'],
                                $row['Paket'],
                                $row['DocumentNo'],
                                $row['ResponseId'],
                                $row['Status'],
                                $row['Timestamp'],
                                $row['Update'],
                                $ext->data[0]->transaction_date,
                                $ext->data[0]->memo,
                                $ext->data[0]->from_gudang,
                                $ext->data[0]->to_gudang,
                                $it->item,
                                Item::where('sku',$it->item)->first()->nama,
                                number_format($it->qty_to_transfer,1,',', '.'),
                                // str_replace(".", ",", $it->qty_to_transfer),
                                $row['Response'],
                                $row['Failed'],
                                $row['Data'])
                            );
                        endforeach;

                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                }
                if($type=="gudang_lb"){
                    try {
                        //code...

                        $ext = json_decode($task->data_content);
                        if (isset($ext->data[0]->items)) {
                                foreach ($ext->data[0]->items as $it) {
                                    if($it->item !== '7000000001' && $it->item !== '7000000002' && $it->item !== '1310000001' && $it->item !== '1100000011') {
                                        fputcsv($file, array(
                                            $row['trans_date'],
                                            $row['DocumentCode'],
                                            Item::where('sku', $it->item)->first()->nama,
                                            '',
                                            Gudang::gudang_code($ext->data[0]->id_location),
                                            number_format($it->qty,1,',', '.'),
                                            // str_replace(".", ",", $it->qty),
                                            '',
                                        ));
                                    }
                                }

                        } else {
                            if(isset($ext->data[0]->line)) {
                                foreach($ext->data[0]->line as $it):
    
                                    fputcsv($file, array(
                                        $row['trans_date'],
                                        $row['DocumentCode'],
                                        Item::where('sku', $it->item)->first()->nama,
                                        Gudang::gudang_code($ext->data[0]->from_gudang),
                                        Gudang::gudang_code($ext->data[0]->to_gudang),
                                        '',
                                        '-'.number_format($it->qty_to_transfer,1,',', '.'),
                                        )
                                    );
                                endforeach;
                            }
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                }
                if($type=="transfer_inventory_do"){
                    try {
                        //code...

                        $ext = json_decode($task->data_content);
                        if (isset($ext->data[0]->line)) {

                            foreach($ext->data[0]->line as $it):
    
                                fputcsv($file, array(
                                    $row['trans_date'],
                                    $row['DocumentNo'],
                                    Item::where('sku', $it->item)->first()->nama,
                                    Gudang::gudang_code($ext->data[0]->from_gudang),
                                    Gudang::gudang_code($ext->data[0]->to_gudang),
                                    number_format($it->qty_to_transfer,1,',', '.'),
                                    '',
                                    $ext->data[0]->memo
                                    )
                                );
                            endforeach;
                            } else {
                                if(isset($ext->data[0]->items)) {
                                    foreach ($ext->data[0]->items as $it) {
                                        if(($it->item != 'AY - S' && $it->item != 'AY - SF')) {
                                            fputcsv($file, array(
                                                $row['trans_date'],
                                                $row['DocumentNo'],
                                                $it->item,
                                                $it->gudang,
                                                '',
                                                '',
                                                '-'.number_format($it->qty,1,',', '.'),
                                                $ext->data[0]->so_number,
                                            ));
                                        }
                                    }
                                }
                            }
                        
                    } catch (\Throwable $th) {
                    }
                }
                if($type=="gudang_retur"){
                    try {
                        //code...

                        $ext = json_decode($task->data_content);
                        if ($ext->record_type == "transfer_inventory") {
                            foreach($ext->data[0]->line as $it):

                                fputcsv($file, array(
                                    $row['trans_date'],
                                    $row['DocumentNo'],
                                    Item::where('sku', $it->item)->first()->nama,
                                    Gudang::gudang_code($ext->data[0]->from_gudang),
                                    Gudang::gudang_code($ext->data[0]->to_gudang),
                                    '',
                                    '-'.number_format($it->qty_to_transfer,1,',', '.'),
                                    $row['DocumentCode']
                                    )
                                );
                            endforeach;
                        } else if ($ext->record_type == "receipt_return") {
                            foreach ($ext->data[0]->line as $it) {
                                fputcsv($file, array(
                                    $row['trans_date'],
                                    $row['DocumentNo'],
                                    Item::where('sku', $it->item_code)->first()->nama,
                                    '',
                                    Gudang::gudang_code($it->internal_id_location),
                                    number_format($it->qty,1,',', '.'),
                                    '',
                                    $row['DocumentCode']
                                ));
                            }
                        } else if ($ext->record_type == "work_order") {
                            foreach ($ext->data[0]->items as $it) {
                                if($it->type == "Component") {
                                    fputcsv($file, array(
                                        $row['trans_date'],
                                        $row['DocumentNo'],
                                        $it->description,
                                        '',
                                        '',
                                        number_format($it->qty,1,',', '.'),
                                        '',
                                        $row['DocumentCode']
                                    ));
                                } else {
                                    fputcsv($file, array(
                                        $row['trans_date'],
                                        $row['DocumentNo'],
                                        $it->description,
                                        '',
                                        '',
                                        '',
                                        '-'.number_format($it->qty,1,',', '.'),
                                        $row['DocumentCode']
                                    ));
                                }
                            }
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                }

                if ($type == 'return') {
                    try {
                        //code...
                        $ext = json_decode($task->data_content);
                        if ($ext->data[0]->items ?? false) {
                            foreach($ext->data[0]->items as $it):
                                fputcsv($file, array(
                                    $row['ID'],
                                    $row['Label'],
                                    $row['trans_date'],
                                    $row['Activity'],
                                    $row['IDLoc'],
                                    $row['Location'],
                                    $row['IntID'],
                                    'Parent',
                                    $row['DocumentNo'],
                                    $row['ResponseId'],
                                    $row['Status'],
                                    $row['Timestamp'],
                                    $row['Update'],
                                    $ext->data[0]->tanggal_ra,
                                    $ext->data[0]->memo,
                                    Item::where('sku', $it->sku)->first()->nama,
                                    $it->qty,
                                    $it->qty_in_ekr_pcs_pack,
                                    Gudang::where('netsuite_internal_id', $it->internal_id_gudang)->first()->code ?? '#',
                                    $row['Response'],
                                    $row['Failed'],
                                    $row['Data'])
                                );
                            endforeach;
                        } else {
                            foreach($ext->data[0]->line as $it):
                                fputcsv($file, array(
                                    $row['ID'],
                                    $row['Label'],
                                    $row['trans_date'],
                                    $row['Activity'],
                                    $row['IDLoc'],
                                    $row['Location'],
                                    $row['IntID'],
                                    $row['Paket'],
                                    $row['DocumentNo'],
                                    $row['ResponseId'],
                                    $row['Status'],
                                    $row['Timestamp'],
                                    $row['Update'],
                                    $ext->data[0]->date,
                                    $ext->data[0]->memo,
                                    Item::where('sku', $it->item_code)->first()->nama,
                                    $it->qty,
                                    $it->qty_in_ekor,
                                    $it->gudang,
                                    $row['Response'],
                                    $row['Failed'],
                                    $row['Data'])
                                );
                            endforeach;
                        }

                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }

                if($type=="wo" || $type=="wo1" || $type=="wo2" || $type=="wo3" || $type=="wo4" || $type=="wo5" || $type=="wo6" || $type=="wo7"){

                    try {
                        //code...
                        $ext = json_decode($task->data_content);


                        foreach($ext->data[0]->items as $it):

                            if($task->record_type=="work_order"){
                                fputcsv($file, array(
                                    $row['No'],
                                    $row['ID'],
                                    $row['Label'],
                                    $row['trans_date'],
                                    $row['Activity'],
                                    $row['IDLoc'],
                                    $row['Location'],
                                    $row['IntID'],
                                    $row['Paket'],
                                    $row['DocumentNo'],
                                    $row['ResponseId'],
                                    $row['Status'],
                                    $row['Timestamp'],
                                    $row['Update'],
                                    $ext->data[0]->transaction_date,
                                    $ext->data[0]->id_item_assembly,
                                    $ext->data[0]->item_assembly,
                                    $ext->data[0]->location,
                                    $it->type,
                                    $it->internal_id_item,
                                    $it->item,
                                    $it->description,
                                    number_format($it->qty,1,',', '.'),
                                    $row['Response'],
                                    $row['Failed'])
                                );
                            } else
                            if($task->record_type=="wo_build"){
                                fputcsv($file, array(
                                    $row['No'],
                                    $row['ID'],
                                    $row['Label'],
                                    $row['trans_date'],
                                    $row['Activity'],
                                    $row['IDLoc'],
                                    $row['Location'],
                                    $row['IntID'],
                                    $row['Paket'],
                                    $row['DocumentNo'],
                                    $row['ResponseId'],
                                    $row['Status'],
                                    $row['Timestamp'],
                                    $row['Update'],
                                    $ext->data[0]->transaction_date,
                                    "-",
                                    "-",
                                    $ext->data[0]->created_from_wo,
                                    $it->type,
                                    $it->internal_id_item,
                                    $it->item,
                                    $it->description,
                                    number_format($it->qty,1,',', '.'),
                                    $row['Response'],
                                    $row['Failed'])
                                );
                            }

                        endforeach;

                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                }


                if($type==""){
                    $data = array(
                        $row['No'], 
                        $row['ID'],
                        $row['Label'],
                        $row['trans_date'],
                        $row['Activity'],
                        $row['IDLoc'],
                        $row['Location'],
                        $row['IntID'],
                        $row['Paket'],
                        $row['DocumentNo'] ?? "NULL",
                    );
                    
                    // data array per type record
                    if (!empty($ext->data[0]->items) || !empty($ext->data[0]->line)) {
                        $item = [];
                    
                        if ($task->record_type == "work_order" || $task->record_type == "wo_build" || $task->record_type == "gudang_lb") {
                            foreach ($ext->data[0]->items as $it) {
                                $item[] = Item::where('sku', $it->item)->first()->nama;
                            }
                        } elseif ($task->record_type == "transfer_inventory" || $task->record_type == "transfer_inventory_do" && !empty($ext->data[0]->line)) {
                            foreach ($ext->data[0]->line as $it) {
                                $item[] = Item::where('sku', $it->item)->first()->nama;
                            }
                        } else if ($task->record_type == "itemreceipt" || $task->record_type == "receipt_return") {
                            foreach ($ext->data[0]->line as $it) {
                                $item[] = Item::where('sku', $it->item_code)->first()->nama;
                            }
                        } else if ($task->record_type == "item_fulfill"){
                            foreach ($ext->data[0]->items as $it) {
                                $item[] = $it->item;
                            }
                        } else if ($task->record_type == "return_authorization"){
                            foreach ($ext->data[0]->items as $it) {
                                $item[] = Item::where('sku',$it->sku)->first()->nama;
                            }
                        }
                    
                        if (count($item) > 1) {
                            $data[] = implode("\n", $item);
                        } else {
                            $data[] = implode("", $item);
                        }
                    }
                    
                    $data[] =    $row['Data'] ?? "NULL";
                    $data[] =    $row['Response'] ?? "NULL";
                    $data[] =    $row['ResponseId'] ?? "NULL"; 
                    $data[] =    $row['Failed'] ?? "NULL";
                    $data[] =    $row['Status'] ?? "NULL";

                   
                
                    // Tulis data ke dalam file CSV
                    fputcsv($file, $data);
                        
                }
            }

            fclose($file);
        }
    }

    public function exportSync(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $netsuite = Netsuite::orderBy('id', 'asc')->where('trans_date', $tanggal)->get();

        $tasks = $netsuite;

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=ns-".date('Y-m-d-H:i:s').".csv");
        $file = fopen('php://output', 'w');
        fputcsv($file,["sep=,"]);

        $columns = array("No", "Label","Activity","IDLoc","Location","IntID","Paket","DocumentNo", "Data","Response","Failed","Status");
        fputcsv($file, $columns);

        foreach ($tasks as $no => $task) {
            $row['No']          = ++$no;
            $row['Label']       = $task->label;
            $row['Activity']    = $task->activity;
            $row['IDLoc']       = $task->id_location;
            $row['Location']    = $task->location;
            $row['IntID']       = $task->tabel_id;
            $row['Paket']       = $task->paket_id;
            $row['DocumentNo']  = $task->document_no;
            $row['Data']        = $task->data_content;
            $row['Response']    = $task->response;
            $row['Failed']      = $task->failed;
            $row['Status']      = $task->status;

            fputcsv($file, array($row['No'], $row['Label'], $row['Activity'], $row['IDLoc'], $row['Location'], $row['IntID'], $row['Paket'],$row['DocumentNo'], $row['Data'], $row['Response'], $row['Status']));
        }

        fclose($file);

    }

    public function indexSyncShow(){

        $logs = Log::orderBy('id', 'desc')->paginate(30);
        return view('admin.pages.log.sync-show')
                ->with('logs', $logs);
    }

    public function notification(){

        $logs = Log::orderBy('id', 'desc')->paginate(30);
        return view('admin.pages.log.notification')
                ->with('logs', $logs);
    }

    public function showNotification(){

        $logs = Log::orderBy('id', 'desc')->paginate(30);
        return view('admin.pages.log.index')
                ->with('logs', $logs);

    }

    public function countNotification(){

        $logs = Log::orderBy('id', 'desc')->where('admin_read', null)->count();
        return $logs;

    }

    public function syncStatus(){

        $tanggal = date('Y-m-d');

        $gagal      = Netsuite::orderBy('id', 'desc')->where('created_at', 'like', '%'.$tanggal.'%')->where('status', '0')->count();
        $pending    = Netsuite::orderBy('id', 'desc')->where('created_at', 'like', '%'.$tanggal.'%')->where('status', '2')->count();
        $selesai    = Netsuite::orderBy('id', 'desc')->where('created_at', 'like', '%'.$tanggal.'%')->where('status', '1')->count();
        $batal      = Netsuite::orderBy('id', 'desc')->where('created_at', 'like', '%'.$tanggal.'%')->where('status', '3')->count();
        $queue      = Netsuite::orderBy('id', 'desc')->where('created_at', 'like', '%'.$tanggal.'%')->where('status', '4')->count();
        $approval      = Netsuite::orderBy('id', 'desc')->where('created_at', 'like', '%'.$tanggal.'%')->where('status', '5')->count();

        $user_id    = Auth::user()->id;
        $chat       = Chat::where('receiver_id', $user_id)->where('status', '1')->with('sender')->with('receiver')->get();

        $return = array(
            'status' => 1,
            'message' => 'get data sync',
            'data' => array(
                'pending' => $pending,
                'gagal' => $gagal,
                'selesai' => $selesai,
                'batal' => $batal,
                'queue' => $queue,
                'approval' => $approval,
                'chat' => $chat
            )
        );
        return $return;

    }

    public function cancelSync(Request $request){

        if($request->status=="completed"){

            if(count((array)$request->selected_id)>0){
                $selected = [];
                foreach($request->selected_id as $s):
                    $selected[] = $s;
                endforeach;

                Netsuite::whereIn('id', $selected)->whereIn('status', [0,3,4])->update(['status'=>'1']);
            }
            return back()->with('status', 1)->with('message', 'Integrasi berhasil diselesaikan');
        }

        if($request->status=="retry"){

            if(count((array)$request->selected_id)>0){
                $selected = [];
                foreach($request->selected_id as $s):
                    $selected[] = $s;
                endforeach;

                Netsuite::whereIn('id', $selected)->whereIn('status', [0,3,4,6])->update(['status'=>'2']);
            }
            return back()->with('status', 1)->with('message', 'Integrasi berhasil dijalankan ulang');
        }

        if($request->status=="cancel"){

            if(count((array)$request->selected_id)>0){
                $selected = [];
                foreach($request->selected_id as $s):
                    $selected[] = $s;
                endforeach;

                Netsuite::whereIn('id', $selected)->whereIn('status', [0,2,5])->update(['status'=>'3']);
            }
            return back()->with('status', 1)->with('message', 'Integrasi berhasil dibatalkan');
        }

        if($request->status=="approve"){
            if(count((array)$request->selected_id)>0){
                $selected = [];
                foreach($request->selected_id as $s):
                    $selected[] = $s;
                endforeach;

                Netsuite::whereIn('id', $selected)->whereIn('status', [5])->update(['status'=>'2']);
            }

            return back()->with('status', 1)->with('message', 'Integrasi berhasil diapprove');
        }
        if($request->status=="hold"){
            if(count((array)$request->selected_id)>0){
                $selected = [];
                foreach($request->selected_id as $s):
                    $selected[] = $s;
                endforeach;

                Netsuite::whereIn('id', $selected)->whereIn('status', [0,2,5])->update(['status'=>'6']);
            }

            return back()->with('status', 1)->with('message', 'Integrasi berhasil dihold sementara');
        }
    }

    public function approveSync(Request $request){

        if(count((array)$request->selected_id)>0){
            $selected = [];
            foreach($request->selected_id as $s):
                $selected[] = $s;
            endforeach;

            Netsuite::whereIn('id', $selected)->whereIn('status', [5])->update(['status'=>'']);
        }


        return back()->with('status', 1)->with('message', 'Integrasi berhasil diapprove');
    }

    public function syncProcessApproval(Request $request){

        $tanggal        = $request->tanggal ?? date('Y-m-d');
        $dari           = $request->dari ?? 0;
        $sampai         = $request->sampai ?? 0;

        if($dari == 0 && $sampai == 0){
            DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND trans_date='".$tanggal."'");
            return back()->with('status', 1)->with('message', 'Integrasi berhasil diapprove');
        }else{
            DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND id BETWEEN ".$dari." AND ".$sampai);
            return back()->with('status', 1)->with('message', 'Integrasi dari '.$dari." - ".$sampai.' berhasil diapprove');
        }


    }

    public function deleteNetsuite(Request $request, $id){

        $netsuite   = Netsuite::find($id);
        $production = Production::where('id',$netsuite->tabel_id)->first();
        try {
            if ($netsuite) {

                $freestock = Freestock::where('netsuite_id', $netsuite->id)->update(['netsuite_id'=> NULL ]);

                if ($production) {
                    if ($netsuite->record_type == "itemreceipt") {
                        $production->lpah_netsuite_status = NULL;
                    }elseif($netsuite->record_type == "work_order" || $netsuite->record_type == "wo_build"){
                        $production->wo_netsuite_status = NULL;
                    }
                    
                    $production->save();
                    $netsuite->delete();
                    return back()->with('status', 1)->with('message', 'Integrasi berhasil dihapus');
                }else{
                    $netsuite->delete();
                    return back()->with('status', 1)->with('message', 'Integrasi berhasil dihapus');
                }
            }else{
                return back()->with('status', 2)->with('message', 'Data tidak ditemukan');
            }
            
        } catch (\Exception $err) {
            // return $err->getMessage();
            return back()->with('status', 2)->with('message', 'Error:'.json_encode($err->getMessage(), true));
        }
    }

    public function deleteNetsuiteArray(Request $request){

        // return response()->json($request->data);
        if (count($request->data) > 0) {
            foreach ($request->data as $data) {
                $netsuite   = Netsuite::find($data);
                $production = Production::where('id',$netsuite->tabel_id)->first();
                try {
                    if ($netsuite) {

                        $freestock = Freestock::where('netsuite_id', $netsuite->id)->update(['netsuite_id'=> NULL ]);

                        if ($production) {
                            if ($netsuite->record_type == "itemreceipt") {
                                $production->lpah_netsuite_status = NULL;
                            } elseif ($netsuite->record_type == "work_order" || $netsuite->record_type == "wo_build") {
                                $production->wo_netsuite_status = NULL;
                            }
                            
                            $production->save();
                            $netsuite->delete();

                        } else {
                            $netsuite->delete();
                        }
                    }
                    
                } catch (\Exception $err) {
                    return response()->json([
                        'status'    => 'error',
                        'message'   => json_encode($err->getMessage(), true)
                    ]);
                }
            }

            return response()->json([
                'status'    => 'success',
                'message'   => 'Data berhasil dihapus'
            ]);

        } else {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Silahkan pilih data yang ingin dihapus'
            ]);
        }


    }


    public function inject_netsuite(Request $request){

        // inject no ra to retur table
        $ns = Netsuite::where('record_type', 'return_authorization')->get();

        foreach($ns as $n):

            $rt = Retur::find($n->tabel_id);
            if($rt){
                $rt->no_ra = $n->document_no;
                $rt->save();
            }
        endforeach;

        echo "selesai";

    }

}

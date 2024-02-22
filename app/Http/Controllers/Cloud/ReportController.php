<?php

namespace App\Http\Controllers\Cloud;

use App\Http\Controllers\Controller;
use App\Models\Evis;
use App\Models\LaporanEvis;
use App\Models\LaporanRendemen;
use App\Models\LaporanSebarankarkas;
use App\Models\NetsuiteLog;
use App\Models\NetsuiteBom;
use App\Models\NetsuiteLocation;
use App\Models\NetsuitePOItemReceipt;
use App\Models\NetsuitePurchasing;
use App\Models\NetsuiteSalesOrder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function report_dashboard(Request $request)
    {

        if ($request->key == 'rendemen') {
            $mulai  =   $request->mulai ?? date('Y-m-d') ;
            $akhir  =   $request->akhir ?? date('Y-m-d') ;

            $rendemen   =   LaporanRendemen::where('subsidiary_id', $request->subsidiary)
                            ->whereBetween('tanggal', [$mulai, $akhir])
                            ->orderBy('tanggal', 'ASC')
                            ->get() ;

            $tangkap    =   '' ;
            $kirim      =   '' ;
            $total      =   '' ;
            $tgl_rendem =   '[' ;
            foreach ($rendemen as $row) {
                $tangkap    .=  $row->rendemen_tangkap . ",";
                $kirim      .=  $row->rendemen_kirim . ",";
                $total      .=  $row->rendemen_total . ",";
                $tgl_rendem .=  "'" . (string)date('d-M', strtotime($row->tanggal)) . "'," ;
            }
            $tgl_rendem     .=  ']';

            $list_rendem    =   "[" ;
            $list_rendem    .=  "{name: 'Rendemen Tangkap',data: [" . $tangkap . "]},";
            $list_rendem    .=  "{name: 'Rendemen Kirim',data: [" . $kirim . "]},";
            $list_rendem    .=  "{name: 'Total Rendemen',data: [" . $total . "]},";
            $list_rendem    .=  "]";

            return view('cloudreport.laporan.report_rendemen', compact('tgl_rendem', 'list_rendem')) ;
        } else

        if ($request->key == 'evis') {
            $mulai  =   $request->mulai ?? date('Y-m-d');
            $akhir  =   $request->akhir ?? date('Y-m-d');

            $evis   =   LaporanEvis::where('subsidiary_id', $request->subsidiary)
                        ->whereBetween('tanggal', [$mulai, $akhir])
                        ->orderBy('tanggal', 'ASC')
                        ->get() ;

            $hati   =   '' ;
            $usus   =   '' ;
            $kaki   =   '' ;
            $kepala =   '' ;
            $tgl_evis   =   '[' ;
            foreach ($evis as $row) {
                if ($row->sku == '1211810005') {
                    $tgl_evis .=  "'" . (string)date('d-M', strtotime($row->tanggal)) . "'," ;
                    $hati   .=  $row->berat . ',' ;
                }
                if ($row->sku == '1211820005') {
                    $usus   .=  $row->berat . ',' ;
                }
                if ($row->sku == '1211830001') {
                    $kaki   .=  $row->berat . ',' ;
                }
                if ($row->sku == '1211840002') {
                    $kepala .=  $row->berat . ',' ;
                }
            }
            $tgl_evis   .=  ']';

            $list_evis  =   "[" ;
            $list_evis  .=  "{name: 'HATI AMPELA KOTOR BROILER',data: [" . $hati . "]},";
            $list_evis  .=  "{name: 'USUS BROILER',data: [" . $usus . "]},";
            $list_evis  .=  "{name: 'KAKI KOTOR BROILER',data: [" . $kaki . "]},";
            $list_evis  .=  "{name: 'KEPALA LEHER BROILER',data: [" . $kepala . "]}," ;
            $list_evis  .=  "]" ;

            return view('cloudreport.laporan.report_evis', compact('list_evis', 'tgl_evis'));
        } else

        if ($request->key == 'karkas') {
            $mulai  =   $request->mulai ?? date('Y-m-d');
            $akhir  =   $request->akhir ?? date('Y-m-d');

            $karkas   =   LaporanSebarankarkas::where('subsidiary_id', $request->subsidiary)
                        ->whereBetween('tanggal', [$mulai. " 00:00:01", $akhir." 23:59:59"])
                        ->orderBy('tanggal', 'ASC')
                        ->get() ;

            $karkas_group   =   LaporanSebarankarkas::select('nama as name')
                        ->where('subsidiary_id', $request->subsidiary)
                        ->whereBetween('tanggal', [$mulai. " 00:00:01", $akhir." 23:59:59"])
                        ->groupBy('item_id')
                        ->get() ;

            $tgl_karkas = $this->date_range($mulai, $akhir);

            return view('cloudreport.laporan.report_karkas', compact('karkas', 'tgl_karkas', 'karkas_group'));
        } else

        if ($request->key == 'do') {
            $mulai  =   $request->mulai ?? date('Y-m-d') ;
            $akhir  =   $request->akhir ?? date('Y-m-d') ;

            $rendemen   =   LaporanRendemen::where('subsidiary_id', $request->subsidiary)
                            ->whereBetween('tanggal', [$mulai, $akhir])
                            ->orderBy('tanggal', 'ASC')
                            ->get() ;

            $ekor       =   '' ;
            $seckel     =   '' ;
            $tgl_do     =   '[' ;
            foreach ($rendemen as $row) {
                $ekor       .=  (float)($row->ekor_do ?? 0) . ",";
                $seckel     .=  (float)($row->ekoran_seckel ?? 0) . ",";
                $tgl_do     .=  "'" . (string)date('d-M', strtotime($row->tanggal)) . "'," ;
            }
            $tgl_do         .=  ']';

            $list_do    =   "[" ;
            $list_do    .=  "{name: 'Ekor DO',data: [" . $ekor . "]},";
            $list_do    .=  "{name: 'Ekoran Seckel',data: [" . $seckel . "]},";
            $list_do    .=  "]";

            return view('cloudreport.laporan.report_do', compact('list_do', 'tgl_do'));
        }else if ($request->key == 'rendemen_table') {
            $mulai  =   $request->mulai ?? date('Y-m-d') ;
            $akhir  =   $request->akhir ?? date('Y-m-d') ;

            $rendemen   =   LaporanRendemen::where('subsidiary_id', $request->subsidiary)
                            ->whereBetween('tanggal', [$mulai, $akhir])
                            ->orderBy('tanggal', 'ASC')
                            ->get() ;

            return view('cloudreport.laporan.report_rendemen_table', compact('rendemen')) ;
        }else {
            return view('cloudreport.report_dashboard');
        }

    }

    function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public function report_produksi()
    {
        return view('cloudreport.report_produksi');
    }

    public function ns_index(Request $request){
        $type   = $request->type;
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        $search = $request->search ?? "";
        return view('cloudreport.netsuite.index', compact('type', 'mulai', 'sampai', 'search'));
    }

    public function ns_list(Request $request){

        $type   = $request->type;
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        $search = $request->search ?? "";

        $data = NetsuiteLog::select('netsuite_log.*')->with(['data_purchasing:document_number,vendor_name,type_po', 'data_po_item_receipt:document_number,type_po,vendor_name', 'data_salesorder', 'data_bom:bom_name', 'data_location:nama_location', 'data_vendor:nama_vendor', 'data_customer:internal_id_customer,nama_customer,subsidiary', 'data_item:nama_item'])
                ->orderBy('id', 'desc')
                ->where(function($query) use ($type, $search) {
                    if($type!=""){
                        $query->where('activity', $type);
                    }
            
                    if($search!=""){
                        $query->where('table_data', 'like', '%'.$search.'%');
                    }
                    
                })
                ->whereBetween('created_at', [$mulai." 00:00:01", $sampai." 23:59:59"])
                ->paginate(15);


        return view('cloudreport.netsuite.list', compact('type', 'mulai', 'sampai', 'data', 'search'));

    }

    public function ns_location(Request $request){
        $type   = $request->record_type;
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        $data = NetsuiteLocation::get();
        return view('cloudreport.netsuite.ns_location', compact('type', 'mulai', 'sampai', 'data'));
    }

    public function ns_po(Request $request){
        $type   = $request->record_type;
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        $data = NetsuitePurchasing::where('netsuite_log_id', $request->id)->get();
        return view('cloudreport.netsuite.ns_po', compact('type', 'mulai', 'sampai', 'data'));
    }

    public function ns_so(Request $request){
        $type   = $request->record_type;
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        $data = NetsuiteSalesOrder::where('netsuite_log_id', $request->id)->get();
        return view('cloudreport.netsuite.ns_so', compact('type', 'mulai', 'sampai', 'data'));
    }

    public function ns_bom(Request $request){
        $type   = $request->record_type;
        $mulai  = $request->mulai ?? date('Y-m-d');
        $sampai = $request->sampai ?? date('Y-m-d');
        $data = NetsuiteBom::get();
        return view('cloudreport.netsuite.ns_bom', compact('type', 'mulai', 'sampai', 'data'));
    }

    public function ns_raw(Request $request){
        $id     = $request->id;
        $net    = NetsuiteLog::find($id);
        if($net){

            $folder = "";
            if($net->activity=='location'):
                $folder = "location";
            endif;
            if($net->activity=='bom'):
                $folder = "bom";
            endif;
            if($net->activity=='sales-order'):
                $folder = "so";
            endif;
            if($net->activity=='purchase-order'):
                $folder = "po";
            endif;
            if($net->activity=='po-item-receipt'):
                $folder = "po_item_receipt";
            endif;
            if($net->activity=='vendor'):
                $folder = "vendor";
            endif;
            if($net->activity=='customer'):
                $folder = "customer";
            endif;
            if($net->activity=='item'):
                $folder = "item";
            endif;

            try {
                //code...
                $json = file_get_contents("netsuite/".$folder."/netsuite_".$id.".json");
                $data = $net;

                return view('cloudreport.netsuite.raw', compact('json', 'data'));
            } catch (\Throwable $th) {
                //throw $th;
                return $th->getMessage();
                // return redirect(route('index.netsuite'));
            }


        }
        // return redirect(route('index.netsuite'));

    }
}

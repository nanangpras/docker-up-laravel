<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Netsuite;
use App\Models\NetsuiteBom;
use App\Models\NetsuiteLocation;
use App\Models\NetsuiteLog;
use App\Models\NetsuitePOItemReceipt;
use App\Models\NetsuitePurchasing;
use App\Models\NetsuiteSalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NetsuiteController extends Controller
{
    //
    public function notifEmail($netsuite_id){

        // email : report@citragunalestari.co.id
        // pass  : report12345

            $ns = Netsuite::find($netsuite_id);
            
            $data = array(
                'name'      => env('NET_SUBSIDIARY', 'CGL')." INTEGRASI",
                'email'     => "report@citragunalestari.co.id",
                'subject'   => "NOTIFIKASI INTEGRASI",
                'type'      => 'message',
                'message'   => 'Kosong'
            );

            Mail::send('email.contact-email', ['data' => $data], function ($message) use($data) {
                $message->from('report@citragunalestari.co.id',env('NET_SUBSIDIARY').' Form');
                $message->to($data['email'])->subject($data['subject']);
            });

            if (Mail::failures()) {
                // return response showing failed emails
                echo "Error";
            }
    }

    public function raw(Request $request){
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

                return view('admin.pages.netsuite.raw', compact('json', 'data'));
            } catch (\Throwable $th) {
                //throw $th;
                return $th->getMessage();
                return redirect(route('index.netsuite'));
            }

            
        }
        return redirect(route('index.netsuite'));
       
    }

    public function index(Request $request) {

        $tanggalAwal    = $request->tanggal_awal ?? date('Y-m-d');
        $tanggalAkhir   = $request->tanggal_akhir ?? date('Y-m-d');
        
        $netsuites      = Netsuite::whereBetween('trans_date', [$tanggalAwal, $tanggalAkhir])
                            ->withTrashed()
                            ->whereNotNull('deleted_at')
                            ->get();

        if ($request->key == 'loadPageNS') {
            return view('admin.pages.netsuite.detailJSONNS', compact('netsuites', 'tanggalAwal', 'tanggalAkhir'));

        } else if ($request->key == 'restoreData') {
            $findDeletedData = Netsuite::where('id', $request->id)->withTrashed()->first();
            $findDeletedData->restore();

            return response()->json([
                'status' => '200',
                'msg'    => 'success'
            ]);




        } else {
            return view('admin.pages.netsuite.indexJSONNS', compact('netsuites', 'tanggalAwal', 'tanggalAkhir'));
            
        }
    }

    
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\Bahanbaku;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product_gudang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Excel;
use Illuminate\Support\Facades\Auth;

class SalesOrderController extends Controller
{
    public function laporan(Request $request)
    {

        $mulai      =   $request->mulai ?? date('Y-m-d');
        $selesai    =   $request->selesai ?? date('Y-m-d');

        $awal       = $mulai;
        $akhir      = $selesai;

        if($request->download == "download"){

            // header("Content-type: application/csv");
            // header("Content-Disposition: attachment; filename=salesorder-" . $awal . "-" .$akhir . ".csv");
            // $fp = fopen('php://output', 'w');
            // fputcsv($fp, ["sep=,"]);

            $html = '<style>.text {
                mso-number-format:"\@";
                border:thin solid black;
                }</style>';

            $html .= '
                    <table class="table default-table" id="export-table">
                        <thead>
                            <tr class="text-center">
                                <th class="text" rowspan="2">TANGGAL</th>
                                <th class="text" rowspan="2">NAMA MARKETING</th>
                                <th class="text" rowspan="2">NO SO</th>
                                <th class="text" rowspan="2">NAMA CUSTOMER</th>
                                <th class="text" rowspan="2">FRESH/FROZEN</th>
                                <th class="text" rowspan="2">NAMA ITEM</th>
                                <th class="text" rowspan="2">GROUP PRODUK</th>
                                <th class="text" colspan="4">JUMLAH</th>
                                <th class="text" rowspan="2">SO KG</th>
                                <th class="text" rowspan="2">AKTUAL KG</th>
                            </tr>
                            <tr class="text-center">
                                <th class="text" >CUT</th>
                                <th class="text" >Ekor/Pcs/Pack</th>
                                <th class="text" >Package</th>
                                <th class="text" >KG</th>
                            </tr>
                        </thead>';


            $order = Order::whereBetween('tanggal_kirim', [$awal." 00:00:00", $akhir." 23:59:59"])->get();

            $urut = 0;
            $html .= "<tbody>";
            foreach($order as $no => $o):

                foreach(OrderItem::where('order_id', $o->id)->get() as $sub_no => $item):
                    $jenis = "FRESH";

                    if (str_contains($item->nama_detail, 'FROZEN')) {
                        $jenis = "FROZEN";
                    }

                    $data = array(
                        $o->tanggal_kirim,
                        $o->ordercustomer->nama_marketing ?? '#',
                        $o->no_so ?? '#',
                        $o->nama,
                        $jenis,
                        $item->nama_detail,
                        $item->item->itemkat->nama,
                        $item->part,
                        $item->qty ?? "0",
                        0,
                        $item->berat ?? "0",
                        $item->berat ?? "0",
                        $item->fulfillment_berat ?? "0",
                    );

                    $urut++;
                    

                    $html .= '<tr class="text-center">
                                <td class="text">'.$data[0].'</td>
                                <td class="text">'.$data[1].'</td>
                                <td class="text">'.$data[2].'</td>
                                <td class="text">'.$data[3].'</td>
                                <td class="text">'.$data[4].'</td>
                                <td class="text">'.$data[5].'</td>
                                <td class="text">'.$data[6].'</td>
                                <td class="text">'.$data[7].'</td>
                                <td class="text">'.$data[8].'</td>
                                <td class="text">'.$data[9].'</td>
                                <td class="text">'.$data[10].'</td>
                                <td class="text">'.$data[11].'</td>
                                <td class="text">'.$data[12].'</td>
                            </tr>';
                            
                endforeach;
                
            endforeach;
            $html .= "</tbody>";

            $html .= '  <tfoot>
                                <tr>
                                    <td colspan="7" class="text-center"><b>Total</b></td>
                                </tr>
                            </tfoot>
                        </table>
                        ';

                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=salesorder-" . $awal . "-" .$akhir . ".xls");
                echo $html;

        }else{
            $data       =   Order::whereBetween('tanggal_kirim', [$mulai, $selesai])
                            ->orderBy('id', 'desc')
                            ->get();
            
            return view('admin.pages.laporan.laporan-salesorder', compact('mulai', 'selesai', 'data'));
        }

        // return redirect()->route('index')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut');
    }

    public function detail($id)
    {
        if (User::setIjin(23)) {
            $data   =   Order::where('id', $id)->first();

            if ($data) {
                $list   =   OrderItem::where('order_id', $data->id)->get();
                return view('admin.pages.laporan.detail-salesorder', compact('data', 'list'));
            }
            return redirect()->route('salesorder.laporan')->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route('index')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut');
    }

    public function editSo($id, Request $request){
        $data   =   Order::where('id', $id)->first();
        $datalog   =   Order::where('id', $id)->first();


        DB::beginTransaction();
        if ($data) {
            $list   =   OrderItem::where('order_id', $data->id)->get();
            $ceklog = Adminedit::where('table_id', $id)->where('table_name', 'orders')->where('type', 'edit')->count();

            $data->no_do            = $request->no_do;
            $data->tanggal_kirim    = $request->tanggal_kirim;
            $data->tanggal_so       = $request->tanggal_so;

            if($request->status=="0" || $request->status==""){
                $data->status       = NULL;
                $data->status_so    = NULL;
            }else{
                $data->status_so    = "Closed";
                $data->status       = $request->status;
            }

            $data->save();

            // Log activity
            // Item awal/original
            if($ceklog < 1){
                $log                =   new Adminedit ;
                $log->user_id       =   Auth::user()->id ;
                $log->table_name    =   'orders' ;
                $log->table_id      =   $id ;
                $log->type          =   'edit' ;
                $log->activity      =   'sales_order' ;
                $log->content       =   'Data Awal (Original)';
                $log->data          =   json_encode([
                        'header' => $datalog,
                        'list' => []
                ]) ;
                if (!$log->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }

            }
                $banyaklog                  =   Adminedit::where('table_name', 'orders')->where('table_id',$id)->where('type', 'edit')->where('content','!=','Data Awal (Original)')->count() ;
                $logseteelah                =   new Adminedit ;
                $logseteelah->user_id       =   Auth::user()->id ;
                $logseteelah->table_name    =   'orders' ;
                $logseteelah->table_id      =   $id ;
                $logseteelah->type          =   'edit' ;
                $logseteelah->activity      =   'sales_order' ;
                $logseteelah->content       =   'Data Edit ke-'. ($banyaklog+1);
                $logseteelah->data          =   json_encode([
                        'header' => $data,
                        'list' => []
                ]) ;
                if (!$logseteelah->save()) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }
            
            DB::commit();
            return redirect()->back()->with('status', 1)->with('message', 'SO telah diedit');
            
        }
        DB::rollBack() ;
        return redirect()->back()->with('status', 2)->with('message', 'Data tidak ditemukan');
    }

    public function retur($id)
    {
        if (User::setIjin(23)) {
            $data   =   Order::where('id', $id)->first();

            if ($data) {
                $list   =   OrderItem::where('order_id', $data->id)->where('retur_tujuan', null)->get();
                return view('admin.pages.laporan.retur', compact('data', 'list'));
            }
            return redirect()->route('salesorder.laporan')->with('status', 2)->with('message', 'Data tidak ditemukan');
        }
        return redirect()->route('index')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut');
    }

    public function returadd(Request $request)
    {
        if (User::setIjin(23)) {
            $data                   =   OrderItem::find($request->item);

            $order                  =   new OrderItem;
            $order->order_id        =   $data->order_id;
            $order->item_id         =   $data->item_id;
            $order->nama_detail     =   $data->nama_detail;
            $order->retur_qty       =   $request->qty;
            $order->retur_berat     =   $request->berat;
            $order->retur_tujuan    =   $request->tujuan;
            $order->retur_notes     =   $request->alasan;
            $order->retur_status    =   1;
            $order->save();


            $data->fulfillment_qty              =   $data->fulfillment_qty - $request->qty;
            $data->fulfillment_berat            =   $data->fulfillment_berat - $request->berat;
            $data->save();

            $neworder               =   OrderItem::latest()->first();

            if ($request->tujuan == 'gudang' or $request->tujuan == 'frozen') {
                $gudang                   =   new Product_gudang;
                $gudang->table_name       =   'order_item';
                $gudang->table_id         =   $neworder->id;
                $gudang->order_id         =   $neworder->order_id;
                $gudang->order_item_id    =   $neworder->item_id;
                $gudang->qty_awal         =   $neworder->retur_qty;
                $gudang->berat_awal       =   $neworder->retur_berat;
                $gudang->qty              =   $neworder->retur_qty;
                $gudang->berat            =   $neworder->retur_berat;
                $gudang->notes            =   $neworder->retur_notes;
                $gudang->type             =   'retur';
                $gudang->save();
            } elseif ($request->tujuan == 'chiller') {
                $chiller                  =   new Chiller;
                $chiller->table_name      =   'order_item';
                $chiller->table_id        =   $neworder->id;
                $chiller->asal_tujuan     =   'retur';
                $chiller->tanggal_produksi=   Carbon::now();
                $chiller->item_id         =   $neworder->item_id;
                $chiller->item_name       =   $neworder->nama_detail;
                $chiller->jenis           =   'masuk';
                $chiller->type            =   'retur';
                $chiller->qty_item        =   $neworder->retur_qty;
                $chiller->berat_item      =   $neworder->retur_berat;
                $chiller->stock_item      =   $neworder->retur_qty;
                $chiller->stock_berat     =   $neworder->retur_berat;
                $chiller->status          =   1;
                $chiller->save();
            }
        }
        return redirect()->route('index')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut');
    }


    public function uploadSOExcel(Request $request)
    {
        if (User::setIjin(23)) {

            $cstmr = $request->customer;

            if ($request->hasFile('file')) {

                $path = $request->file('file');

                try {
                    //code...
                    $prod_import = Excel::toArray([],$path);
                } catch (\Throwable $th) {
                    //throw $th;
                    return "Format Tidak didukung, ulangi lagi dengan format excel";
                }


                if($cstmr=="sampingan"){

                    // $file           =   "https://docs.google.com/spreadsheets/d/e/2PACX-1vRHsz4jAd0dqSUR-A2gKOx79x_W2YYkh2iEKun2PqRqblnxYQAA0fqWEB-N1rRVoVWCQ_hVl7fk0Ush/pub?output=csv";
                    // $fileData       =   fopen($file, 'r');

                    // $no             =   0;
                    // $update_data    =   0;
                    // $insert_data    =   0;

                    // $map_item_index       = array();

                    // while (($line = fgetcsv($fileData)) !== FALSE) {
                    //         // $map_item_index[]   = explode(" : ", $line[1])[1] ?? "-";
                    //         $map_item_index[]   = $line[0];
                    //         $map_item[]         = $line;
                    //         $no++;
                    // }

                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=cgl-so-sampingan-".date('Y-m-d-H:i:s').".csv");
                    $fp = fopen('php://output', 'w');

                    $data = array(
                        "Order #",
                        "SO Accurate #",
                        "PO #",
                        "Customer (Req)",
                        "Date (Req)",
                        "Memo",
                        "Tanggal Kirim *",
                        "Status (Req)",
                        "Sales Rep",
                        "Customer/Partner",
                        "Alamat Customer/Partner",
                        "Wilayah",
                        "SUBSIDIARY",
                        "Department *",
                        "Cabang *",
                        "Gudang *",
                        "Sales Channel",
                        "Terms",
                        "Line Id",
                        "Item",
                        "Description",
                        "Quantity",
                        "Units",
                        "Price Level",
                        "Unit Price",
                        "Amount",
                        "Department (line) *",
                        "Cabang (Line) *",
                        "Gudang (Line) *",
                        "Sales Channel (Line) *",
                        "Harga Cetakan",
                        "Qty EKR/PCS/PACK",
                        "Harga per EKR/PCS/PACK",
                        "Part",
                        "Plastik",
                        "Bumbu",
                        "Memo"
                    );

                    fputcsv($fp,$data);

                    $no = 0;
                    $nama_cust = "";

                    foreach ($prod_import[0] as $urut => $row) {

                        try {

                        // $hasil_mapping_index    = array_search($row[2], $map_item_index);
                        // $hasil_mapping          = $map_item[$hasil_mapping_index];

                        // $produk = Item::where('nama', (explode(" : ", $hasil_mapping[1])[1] ?? ""))->first();

                        $produk = Item::where('nama', $row[2])->first();

                        if($row[1]!=""){
                            $cs = Customer::where('nama', $row[1])->first();
                            $nama_cust = ($cs->kode ?? " ")." - ".($cs->nama ?? "N/A");
                            $no_so = "SO".($cs->kode ?? " ").$urut;
                        }

                        if($row[2] !="" && $urut > 3){

                                $nama_produk = "N/A";

                                if($produk){
                                    $nama_produk = $produk->sku." ".$produk->nama;
                                }

                                $qty    = 1;
                                $berat  = 1;


                                $data = array(
                                    $no_so,
                                    " ",
                                    " ",
                                    $nama_cust,
                                    date('d-M-Y'),
                                    "",
                                    date('d-M-Y'),
                                    "Pending Fulfillment",
                                    " ",
                                    " ",
                                    " ",
                                    " ",
                                    "CGL",
                                    "NONE",
                                    "NONE",
                                    "",
                                    "By Product - Paket",
                                    "",
                                    $no+1,
                                    $nama_produk,
                                    $nama_produk,
                                    str_replace(".",".",$berat),
                                    "Kg",
                                    "0",
                                    "0",
                                    "0",
                                    "NONE",
                                    "NONE",
                                    "CGL - Storage Expedisi",
                                    "By Product - Paket",
                                    "",
                                    $qty,
                                    "0",
                                    " ",
                                    " ",
                                    " ",
                                    " "
                                );

                                fputcsv($fp,$data);
                                $no++;

                        }

                            //code...
                        } catch (\Throwable $th) {
                            //throw $th;

                            return $th->getMessage();
                        }
                    }

                    fclose($fp);


                }else{

                    $file           =   "https://docs.google.com/spreadsheets/d/e/2PACX-1vTok5v5wO2BpYoGeXQUrSsr_hYWgb1B1eQ6JLMll9fqFkrw1K-jwWZdsFl8qyF8e16E4mOUUI6jfJnB/pub?gid=2080604544&single=true&output=csv";
                    $fileData       =   fopen($file, 'r');

                    $no             =   0;
                    $update_data    =   0;
                    $insert_data    =   0;

                    $map_item_index       = array();

                    while (($line = fgetcsv($fileData)) !== FALSE) {
                        if ($no != 0) {
                            $map_item_index[]   = $line[1];
                            $map_item[]         = $line;
                        }
                        $no++;
                    }

                    $no_saved = 0;
                    $no_unsaved = 0;
                    $proceed = false;

                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=cgl-so-meyer-".date('Y-m-d-H:i:s').".csv");
                    $fp = fopen('php://output', 'w');

                    $data = array(
                        "Order #",
                        "SO Accurate #",
                        "PO #",
                        "Customer (Req)",
                        "Date (Req)",
                        "Memo",
                        "Tanggal Kirim *",
                        "Status (Req)",
                        "Sales Rep",
                        "Customer/Partner",
                        "Alamat Customer/Partner",
                        "Wilayah",
                        "SUBSIDIARY",
                        "Department *",
                        "Cabang *",
                        "Gudang *",
                        "Sales Channel",
                        "Terms",
                        "Line Id",
                        "Item",
                        "Description",
                        "Quantity",
                        "Units",
                        "Price Level",
                        "Unit Price",
                        "Amount",
                        "Department (line) *",
                        "Cabang (Line) *",
                        "Gudang (Line) *",
                        "Sales Channel (Line) *",
                        "Harga Cetakan",
                        "Qty EKR/PCS/PACK",
                        "Harga per EKR/PCS/PACK",
                        "Part",
                        "Plastik",
                        "Bumbu",
                        "Memo"
                    );

                    fputcsv($fp,$data);

                    $no = 0;
                    $no_po = "";
                    foreach ($prod_import[0] as $urut => $row) {


                        try {

                        $hasil_mapping_index    = array_search($row[3], $map_item_index);
                        $hasil_mapping          = $map_item[$hasil_mapping_index];

                        $produk = Item::where('sku', $hasil_mapping[3])->first();

                        if($row[0]!=""){
                            $no_po = $row[0] ?? "-";
                        }

                        if($row[1] !="" && $row[2] !="" && $row[3] !="" && $row[4] !="" && $row[5] !="" && $urut>5){

                                $nama_produk = "N/A";
                                $nama_produk_2 = $row[3];

                                if($produk && $row[3]!="" && $hasil_mapping_index!=""){
                                    $nama_produk = $produk->sku." ".$produk->nama;
                                    $nama_produk_2 = $produk->sku." ".$produk->nama;
                                }

                                $unit = "";

                                if($row[5]=="Packs" || $row[5]=="Ekor"){
                                    $qty          = (int)str_replace(",",".",$row[4]);

                                    if($hasil_mapping[6]!=""){
                                        $berat          = round((double)str_replace(",",".",$row[4])*$hasil_mapping[6]);
                                    }else{
                                        $berat          = 1;
                                    }

                                    $unit = "Ekor/Pcs/Pack";

                                }else if($row[5]=="Kg"){
                                    $berat        = (double)str_replace(",",".",$row[4]);

                                    if($hasil_mapping[6]!=""){
                                        $qty          = (int)round((double)str_replace(",",".",$row[4])/$hasil_mapping[6]);
                                    }else{
                                        $qty          = 1;
                                    }

                                    $unit = "Kg";

                                }else{
                                    $qty          = (int)str_replace(",",".",$row[4]);
                                    $berat        = (double)str_replace(",",".",$row[4]);

                                    $unit = "Kg";
                                }


                                $data = array(
                                    "SO".str_replace("PO.MPP.2021.", "CGL",$no_po),
                                    " ",
                                    $no_po,
                                    "CCGL000447 - MEYER FOOD : CCGL000448 - MEYER PROTEINDO PRAKARSA. PT",
                                    date('d-M-Y'),
                                    " ",
                                    date('d-M-Y',strtotime('tomorrow')),
                                    "Pending Fulfillment",
                                    " ",
                                    $row[1],
                                    $row[2],
                                    " ",
                                    "CGL",
                                    "NONE",
                                    "NONE",
                                    "",
                                    "E-Commerce",
                                    " ",
                                    $no+1,
                                    $nama_produk,
                                    $nama_produk_2,
                                    str_replace(".",".",$berat),
                                    "kg",
                                    "0",
                                    "0",
                                    "0",
                                    "NONE",
                                    "NONE",
                                    "CGL - Storage Expedisi",
                                    "E-Commerce",
                                    $unit,
                                    $qty,
                                    "0",
                                    $hasil_mapping[7],
                                    "Meyer",
                                    $hasil_mapping[8],
                                    $hasil_mapping[9]
                                );

                                fputcsv($fp,$data);
                                $no++;

                        }

                            //code...
                        } catch (\Throwable $th) {
                            //throw $th;

                            return $th->getMessage();
                        }
                    }
                    fclose($fp);

                }
            }
        }

    }

    public function uploadSOExcelMeyerGlobal(Request $request)
    {
        if (User::setIjin(23)) {

            $cstmr      = $request->customer;
            $tanggal    = $request->tanggal ?? date('d-M-y');

            if ($request->hasFile('file')) {

                $path = $request->file('file');

                try {
                    //code...
                    $prod_import = Excel::toArray([],$path);
                } catch (\Throwable $th) {
                    //throw $th;
                    return "Format Tidak didukung, ulangi lagi dengan format excel";
                }

                    $file           =   "https://docs.google.com/spreadsheets/d/e/2PACX-1vTok5v5wO2BpYoGeXQUrSsr_hYWgb1B1eQ6JLMll9fqFkrw1K-jwWZdsFl8qyF8e16E4mOUUI6jfJnB/pub?gid=2080604544&single=true&output=csv";
                    $fileData       =   fopen($file, 'r');

                    $no             =   0;
                    $update_data    =   0;
                    $insert_data    =   0;

                    $map_item_index       = array();

                    while (($line = fgetcsv($fileData)) !== FALSE) {
                        if ($no != 0) {
                            $map_item_index[]   = $line[1];
                            $map_item[]         = $line;
                        }
                        $no++;
                    }

                    $no_saved = 0;
                    $no_unsaved = 0;
                    $proceed = false;

                    header("Content-type: application/csv");
                    header("Content-Disposition: attachment; filename=cgl-so-meyer-".date('Y-m-d-H:i:s').".csv");
                    $fp = fopen('php://output', 'w');

                    $data = array(
                        "Order #",
                        "SO Accurate #",
                        "PO #",
                        "Customer (Req)",
                        "Date (Req)",
                        "Memo",
                        "Tanggal Kirim *",
                        "Status (Req)",
                        "Sales Rep",
                        "Customer/Partner",
                        "Alamat Customer/Partner",
                        "Wilayah",
                        "SUBSIDIARY",
                        "Department *",
                        "Cabang *",
                        "Gudang *",
                        "Sales Channel",
                        "Terms",
                        "Line Id",
                        "Item",
                        "Description",
                        "Quantity",
                        "Units",
                        "Price Level",
                        "Unit Price",
                        "Amount",
                        "Department (line) *",
                        "Cabang (Line) *",
                        "Gudang (Line) *",
                        "Sales Channel (Line) *",
                        "Harga Cetakan",
                        "Qty EKR/PCS/PACK",
                        "Harga per EKR/PCS/PACK",
                        "Part",
                        "Plastik",
                        "Bumbu",
                        "Memo"
                    );

                    fputcsv($fp,$data);

                    $no = 0;
                    $no_po = "";
                    foreach ($prod_import[0] as $urut => $row) {

                        try {

                        $hasil_mapping_index    = array_search($row[3], $map_item_index);
                        $hasil_mapping          = $map_item[$hasil_mapping_index];



                        $produk = Item::where('sku', $hasil_mapping[3])->first();

                        if($row[0]!=""){
                            $no_po = $row[0] ?? "-";
                        }

                        if($row[0] !="#PO" && $row[3] !="" && $row[4] !="" && $row[5] !="" && $urut>5){

                                $nama_produk = "N/A";
                                $nama_produk_2 = $row[3];

                                if($produk){
                                    $nama_produk = $produk->sku." ".$produk->nama;
                                    $nama_produk_2 = $produk->sku." ".$produk->nama;
                                }

                                $unit = "";

                                if($row[5]=="Packs" || $row[5]=="Ekor"){
                                    $qty          = (int)str_replace(",",".",$row[4]);

                                    if($hasil_mapping[6]!=""){
                                        $berat          = round((double)str_replace(",",".",$row[4])*$hasil_mapping[6]);
                                    }else{
                                        $berat          = 1;
                                    }

                                    $unit = "Ekor/Pcs/Pack";

                                }else if($row[5]=="Kg"){
                                    $berat        = (double)str_replace(",",".",$row[4]);

                                    if($hasil_mapping[6]!=""){
                                        $qty          = (int)round((double)str_replace(",",".",$row[4])/$hasil_mapping[6]);
                                    }else{
                                        $qty          = 1;
                                    }

                                    $unit = "Kg";

                                }else{
                                    $qty          = (int)str_replace(",",".",$row[4]);
                                    $berat        = (double)str_replace(",",".",$row[4]);

                                    $unit = "Kg";
                                }


                                $data = array(
                                    "1",
                                    " ",
                                    " ",
                                    "CCGL000447 - MEYER FOOD : CCGL000448 - MEYER PROTEINDO PRAKARSA. PT",
                                    date('d-M-Y', strtotime($tanggal)),
                                    " ",
                                    date('d-M-Y',strtotime('+1 days', strtotime($tanggal))),
                                    "Pending Fulfillment",
                                    "CGL - 0124 - TIAS PRATIWI",
                                    $row[1] ?? "",
                                    $row[2] ?? "JL.GREENLAKE RUKO WALLSTREET NO A29",
                                    "TANGERANG",
                                    "CGL",
                                    "NONE",
                                    "NONE",
                                    "CGL NONE",
                                    "E-Commerce",
                                    " ",
                                    $no+1,
                                    $nama_produk,
                                    $nama_produk_2,
                                    str_replace(".",".",$berat),
                                    "kg",
                                    "0",
                                    "0",
                                    "0",
                                    "NONE",
                                    "NONE",
                                    "CGL - Storage Expedisi",
                                    "E-Commerce",
                                    $unit,
                                    $qty,
                                    "0",
                                    $hasil_mapping[7] ?? "",
                                    "Meyer",
                                    $hasil_mapping[8] ?? "",
                                    $hasil_mapping[9] ?? ""
                                );

                                fputcsv($fp,$data);
                                $no++;

                        }

                            //code...
                        } catch (\Throwable $th) {
                            //throw $th;

                            return $th->getMessage();
                        }
                    }
                    fclose($fp);
            }
        }

    }

    public function salesadd(){
        return view('admin.pages.laporan.salesorder-add');
    }

    public function so_export($id_so){

        $so  =   Order::find($id_so);

        if($so){
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=cgl-so-".$so->nama."-".date('Y-m-d-H:i:s').".csv");
            $fp = fopen('php://output', 'w');
            fputcsv($fp,["sep=,"]);

            $data = array(
                "No",
                "Nama",
                "Qty",
                "Berat",
                "Part",
                "Bumbu",
                "Memo",
                "Customer"
            );
            fputcsv($fp,$data);

            foreach($so->daftar_order_full as $no => $row):

                $data = array(
                    ++$no,
                    $row->nama_detail,
                    $row->qty,
                    str_replace(".",".",$row->berat),
                    $row->part,
                    $row->bumbu,
                    $row->memo,
                    $row->keterangan,
                );
                fputcsv($fp,$data);
            endforeach;

            fclose($fp);
        }
    }

    public function upload_line_idso(Request $request){

        DB::beginTransaction();

        if ($request->hasFile('file')) {

            $path = $request->file('file');

            try {
                //code...
                $prod_import = Excel::toArray([],$path);
            } catch (\Throwable $th) {
                //throw $th;
                DB::rollBack() ;
                return "Format Tidak didukung, ulangi lagi dengan format excel";
            }

            $resp = [];
            foreach ($prod_import[0] as $urut => $line) {

                if ($urut != 0) {

                    $order                       =   Order::where('no_so', $line[5])->first();

                    if ($order) {

                        $proceed    = true;
                        $order_item = OrderItem::where('sku', $line[8])->where('order_id', $order->id)->where('qty', $line[13])->where('berat', $line[10])->first();

                        if($order_item){

                            $item                   =   Item::where('nama', $line[9])->first();

                            if($item){

                                $order_item->line_id         =   $line[6];
                                $order_item->save();

                                $resp[] = $order_item;

                            }
                        }
                    }
                }
            }

            DB::commit();

            return $resp;
        }

    }

}

<?php

namespace App\Console\Commands;

use App\Models\Adminedit;
use App\Models\Bahanbaku;
use App\Models\DataOption;
use App\Models\Item;
use App\Models\Log;
use App\Models\MarketingSO;
use App\Models\MarketingSOList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\Pembelian;
use App\Models\Retur;
use App\Models\Pembelianheader;
use App\Models\Pembelianlist;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Sync:process  {--tanggal=} {--log=} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synd data to cloud';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $tanggal = $this->option('tanggal') ?? date('Y-m-d');
        $log = $this->option('log') ?? "off";
        $id = $this->option('id') ?? "";

        if(env('NET_SUBSIDIARY', 'EBA')=='EBA'){
            $yesterday = date('Y-m-d', strtotime('-90 day'));
        }else{
            $yesterday = date('Y-m-d', strtotime('-60 day')); 
        }


        // CEK KIRIMAN SO UNTUK HARI H DAN H+1
        // $tanggalBesok               = date('Y-m-d',strtotime("+1 days"));
        // $tanggalHariIni             = date('Y-m-d');
        // $setStatusKirimanSO         = MarketingSO::where('netsuite_id', NULL)->where('status', NULL)->get();


        // if ($setStatusKirimanSO) {
        //     foreach ($setStatusKirimanSO as $appsToNS) {
        //         $ns                           = Netsuite::sales_order("marketing_so", $appsToNS->id, $appsToNS->app_po, NULL, $appsToNS->tanggal_so);

        //         $appsToNS->netsuite_status    =   1;
        //         $appsToNS->netsuite_id        =   $ns->id ;
        //         $appsToNS->status             =   1 ;
    
        //         $appsToNS->save();
        //     }
        // }


        $prev_process =  Netsuite::whereIn('status', [4])->whereBetween('created_at', [$yesterday." 00:00:01", $tanggal." 23:59:59"])->count();
        if($prev_process>0){
            echo "Pending Queue ".$prev_process."\n";
            return false;
        }

        $data = [];

        if($id!=""){
            $data   =   Netsuite::where('id', $id)->update(array('status' => '4'));
            $data   =   Netsuite::where('id', $id)->get();
        }else{
            $data   =   Netsuite::whereIn('status', [0, 2])->whereIn('record_type', ["item_fulfill",
                                                                    "transfer_inventory",
                                                                    "return_authorization",
                                                                    "receipt_return",
                                                                    "work_order",
                                                                    "wo_build",
                                                                    "itemreceipt",
                                                                    "sales_order", 
                                                                    "purchase_order"])->whereBetween('created_at', [$yesterday." 00:00:01", $tanggal." 23:59:59"])->limit(30)->update(array('status' => '4'));

            $data   =   Netsuite::whereIn('status', [0, 2])->whereIn('record_type', ["item_fulfill",
                                                                    "transfer_inventory",
                                                                    "return_authorization",
                                                                    "receipt_return",
                                                                    "work_order",
                                                                    "wo_build",
                                                                    "itemreceipt",
                                                                    "sales_order", 
                                                                    "purchase_order"])->whereBetween('created_at', [$tanggal." 00:00:01", $tanggal." 23:59:59"])->limit(5)->update(array('status' => '4'));

            $data   =   Netsuite::whereIn('status', [4])->whereIn('record_type', ["item_fulfill",
                                                                    "transfer_inventory",
                                                                    "return_authorization",
                                                                    "receipt_return",
                                                                    "work_order",
                                                                    "wo_build",
                                                                    "itemreceipt",
                                                                    "sales_order", 
                                                                    "purchase_order"])->whereBetween('created_at', [$yesterday." 00:00:01", $tanggal." 23:59:59"])->get();
                                                                    
        }
        
        echo "Process Queue ".count($data)."\n";

        foreach ($data as $row) {

            try {
            
                if ($row->record_type == 'wo_build') {

                    if($row->paket_id!=""){
                        $get_net    =   Netsuite::find($row->paket_id) ;
                    }else{
                        $get_net    =   Netsuite::where('tabel', $row->tabel)
                                        ->where('tabel_id', $row->tabel_id)
                                        ->where('record_type', 'work_order')
                                        ->where('status', 1)
                                        ->orderBy('id', 'DESC')
                                        ->first() ;
                    }


                                    // echo json_encode($get_net);
                                    // return false;


                    if($get_net){

                        $wo_response    = json_decode($get_net->response, TRUE);
                        $wo_content     = json_decode($get_net->data_content, TRUE);
                        $get_line       = $wo_response[0]['message'] ?? [];
                        $new_line_item  = [];

                        if(count($get_line)>0){
                            
                            $new_line_item                      = [];
                            
                            if(count($get_line)>0){
                                $new_line_item                  = $get_line;
                            }

                            $json   =   [
                                "record_type"   =>  "wo_build",
                                "data"          =>  [
                                    [
                                        "appsid"            =>  env('NET_SUBSIDIARY', "CGL")."-".$row->id,
                                        "transaction_date"  =>  $wo_content['data'][0]['transaction_date'],
                                        "qty_to_build"      =>  $wo_content['data'][0]['plan_qty'],
                                        "created_from_wo"   =>  "$get_net->response_id",
                                        "items"             =>  $new_line_item
                                    ]
                                ]
                            ] ;
        
                            $row->data_content  =   json_encode($json) ;
                            $row->save() ;
                        }

                    }else{
                        if($log=="on"){
                            echo "paket sebelumnya belum sukses\n";
                        }
                        $row->status = 0;
                        $row->save();
                    }
                    
                }

                if ($row->record_type == 'receipt_return') {
                    $get_net    =   Netsuite::where('tabel', $row->tabel)
                                    ->where('tabel_id', $row->tabel_id)
                                    ->where('record_type', 'return_authorization')
                                    ->where('status', 1)
                                    ->orderBy('id', 'DESC')
                                    ->first() ;


                    $exp            = json_decode($row->data_content) ;
                    $ra_response    = json_decode($get_net->response, TRUE);

                    $get_line       = $ra_response[0]['message']['items'] ?? [];

                    $new_line_item = [];
                    foreach($get_line as $ln):

                        $new_line_item[] = [
                            "line"                        => $ln['line'],
                            "internal_id_item"            => $ln['internal_id_item'],
                            "item_code"                   => $ln['SKU'],
                            "qty"                         => $ln['qty'],
                            "qty_in_ekor"                 => $ln['qty_in_ekr_pcs_pack'],
                            "internal_id_location"        => $ln['internal_id_gudang'],
                            "gudang"                      => env('NET_SUBSIDIARY', 'CGL').' - Storage Retur'
                        ];

                    endforeach;

                    $json   =   [
                        "record_type" 	=> 	"receipt_return",
                        "data"          =>  [
                            [

                                "appsid"                      =>  $exp->data[0]->appsid,
                                "internal_id_ra"              =>  "$get_net->response_id",
                                "ra_number"                   =>  "",
                                "date"                        =>  $exp->data[0]->date,
                                "memo"                        =>  $exp->data[0]->memo ,
                                "no_nota"                     =>  $exp->data[0]->no_nota,
                                "tanggal_nota"                =>  $exp->data[0]->tanggal_nota,
                                "line"                        =>  $new_line_item
                                
                            ]
                        ]
                    ] ;

                    $row->data_content  =   json_encode($json) ;
                    $row->save() ;
                }

                $script =   $row->script;
                $deploy =   $row->deploy;
                $content=   $row->data_content;

                $nonce  =   md5(mt_rand());
                $url    =   env("NET_LINK", "https://6484226-sb1.restlets.api.netsuite.com/app/site/hosting/restlet.nl") . '?&script=' . $script . '&deploy=' . $deploy . '&realm=' . env("NET_ACCOUNT", "6484226_SB1") ;

                $prev_activity  = false;
                $execute        = true;

                if($row->paket_id!="0"){
                    $prev   = Netsuite::find($row->paket_id);

                    if(!$prev){
                        if($log=="on"){
                            echo "PAKET ID TIDAK TERSEDIA\n";
                        }

                        $row->status = 0;
                        $row->save();

                        $execute        = false;
                        
                    }else if($prev->status=="1"){

                        $execute        = true;
                        
                    }else{
                        if($log=="on"){
                            echo $row->id." SKIP PROCESS\n";
                        }
                        $row->status = 0;
                        $row->save();
                        
                        $execute        = false;
                    }
                    
                }else{
                    $execute        = true;
                }

                if($execute){

                    echo "Execute ID : $row->id || ";

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 45000);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: ' . Netsuite::header_netsuite($nonce, $script, $deploy),
                        'Content-Type: application/json'
                    ]);
    
                    $server_output  =   curl_exec($ch);

                    $curl_errno = curl_errno($ch);
                    $curl_error = curl_error($ch);
                    curl_close($ch);

                    $err = [];
                    if ($curl_errno > 0) {

                        if($log=="on"){
                            echo "cURL Error ($curl_errno): $curl_error\n";
                        }

                        $err[] = array(
                            'status'=>'failed',
                            'message'=> "cURL Error ($curl_errno): $curl_error\n"
                        );
                        $netsave                =   Netsuite::find($row->id) ;
                        $netsave->failed        =   json_encode($err) ;
                        $netsave->response_id   =   NULL ;
                        $netsave->status        =   0 ;
                        $netsave->save() ;

                    } else {
                        

                        try {
                            $exp            =   json_decode($server_output) ;
                            //code...
                            if ($exp[0]->status == 'success') {
                                $netsave                =   Netsuite::find($row->id) ;
                                $netsave->response      =   $server_output ;
                                $netsave->respon_time   =   date('Y-m-d H:i:s') ;
                                $netsave->response_id   =   $exp[0]->internalid ;
                                $netsave->document_no   =   $exp[0]->documentno ?? "";
                                $netsave->status        =   1 ;
                                $netsave->count         =   $netsave->count+1 ;
                                $netsave->save() ;
    
                                try {
                                    //code...
                                    if($netsave->record_type=="item_fulfill"){
                                        $sales_order            = Order::where('id', $netsave->tabel_id)->first();
                                        if($sales_order){

                                            if($sales_order->no_do!=""){
                                                $sales_order->no_do = $sales_order->no_do." - ".$exp[0]->documentno;
                                                $sales_order->save();
                                            }else{
                                                $sales_order->no_do = $exp[0]->documentno;
                                                $sales_order->save();
                                            }

                                            $order_bahan_baku_list = Bahanbaku::where('type', 'order-fulfillment')
                                                                        ->where('order_id', $sales_order->id)
                                                                        ->whereNull('no_do')->get();

                                            $order_bahan_baku = Bahanbaku::where('type', 'order-fulfillment')
                                                                        ->where('order_id', $sales_order->id)
                                                                        ->whereNull('no_do')->update(array('no_do' => $exp[0]->documentno));

                                            try {
                                                //code...
                                                foreach($order_bahan_baku_list as $ob){
                                                    Netsuite::update_no_do($ob->netsuite_id);
                                                }
                                            } catch (\Throwable $th) {
                                                //throw $th;
                                            }
                                        }
                                    }

                                    if($netsave->record_type=="return_authorization"){
                                        $rt = Retur::find($netsave->tabel_id);
                                        if($rt){
                                            $rt->no_ra = $exp[0]->documentno;
                                            $rt->save();
                                        }
                                    }

                                    if($netsave->record_type=="sales_order"){
                                        $so = MarketingSO::find($netsave->tabel_id);
                                        if($so){
                                            $so->netsuite_status    = 1;
                                            $so->no_so              = $exp[0]->documentno;
                                            $so->save();
                                        }

                                        $resp_line            = json_decode($netsave->response) ;
                                        foreach($so->listItem as $p){

                                            $item_list = Item::find($p->item_id);

                                            if($p->line_id==""){

                                                foreach($resp_line[0]->message as $n){

                                                    if($n->internal_id_item == $item_list->netsuite_internal_id &&
                                                    $n->quantity == $p->berat  &&
                                                    $n->parting == $p->parting  &&
                                                    $n->bumbu == $p->bumbu  &&
                                                    $n->qty_ekr_pcs_pack == $p->qty){

                                                        if($item_list){

                                                            $order_item             = MarketingSOList::where('id', $p->id)->first();

                                                            if($order_item){
                                                                $order_item->update(
                                                                    [
                                                                        'line_id' => $n->line_id
                                                                    ]
                                                                );
                                                            }

                                                        }

                                                    }else{
                                                    }

                                                }
                                            }

                                        }
                                        
                                    }

                                    if($netsave->record_type=="purchase_order"){
                                        $po = Pembelianheader::find($netsave->tabel_id);
                                        if($po){
                                            $po->netsuite_status    = 3;
                                            $po->document_number    = $exp[0]->documentno;
                                            $po->save();

                                            $resp_line    =   json_decode($netsave->response);

                                            foreach($po->list_pembelian as $p){

                                                $item_list = Item::find($p->item_id);

                                                if($p->line_id==""){
                                                    foreach($exp[0]->message as $n){

                                                        if($po->type_po=="PO LB" || $po->type_po=="PO Non Karkas" || $po->type_po=="PO Karkas" || $po->type_po=="PO Evis" || $po->type_po=="PO Transit"){
                                                            $quantity = (float)$p->berat;
                                                        }else{
                                                            $quantity = (float)$p->qty;
                                                        }
                                                        if($n->internal_id_item == $item_list->netsuite_internal_id
                                                        && (float)$n->quantity == $quantity
                                                        && (float)$n->rate == (float)$p->harga){

                                                            if($item_list){

                                                                $po_item             = Pembelianlist::where('id', $p->id)->first();

                                                                if($po_item){
                                                                    $po_item->update(['line_id' => $n->line_id]);
                                                                }

                                                            }

                                                        }

                                                    }
                                                }

                                            }

                                            // Data PR
                                            $pembelian             = Pembelian::find($po->pembelian_id);
                                            // LOG
                                            if($pembelian){
                                                $log               =   new Adminedit() ;
                                                $log->data         =   json_encode($po);
                                                $log->table_name   =   'pembelian' ;
                                                $log->table_id     =   $pembelian->id ;
                                                $log->activity     =   'pr' ;
                                                $log->status       =   1 ;
                                                $log->content      =   $exp[0]->documentno . " Terbentuk oleh : ".((User::find($netsave->user_id)->name) ?? "#");
                                                $log->type         =   "input" ;
                                                $log->save();
                                            }
                                            
                                        }
                                    }
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }
                            }
            
                            if ($exp[0]->status == 'failed') {
                                $netsave                =   Netsuite::find($row->id) ;
                                $netsave->failed        =   $server_output ;
                                $netsave->failed_time        =   date('Y-m-d H:i:s') ;
                                $netsave->status        =   6 ;
                                $netsave->save() ;

                                if($netsave->record_type=="sales_order"){
                                    $so = MarketingSO::find($netsave->tabel_id);
                                    if($so){
                                        $so->netsuite_status    = 4;
                                        $so->save();

                                        $netsave->status        =   6 ;
                                        $netsave->save() ;
                                    }
                                }
                                if($netsave->record_type=="purchase_order"){
                                    $po = Pembelianheader::find($netsave->tabel_id);
                                    if($po){
                                        $po->netsuite_status    = 4;
                                        $po->save();
                                    }
                                }
                                if($netsave->record_type=="itemreceipt"){
                                    $netsave->status        =   6 ;
                                    $netsave->save() ;
                                    // Modul kirim email ke bu renny
                                    
                                }
                            }

                            if ($exp[0]->status == 'Update success') {
                                $netsave                     =   Netsuite::find($row->id) ;
                                $netsave->resp_update        =   $server_output ;
                                $netsave->status             =   1 ;
                                $netsave->update_time        =   date('Y-m-d H:i:s') ;
                                $netsave->count              =   $netsave->count+1 ;
                                $netsave->save() ;

                                if($netsave->record_type=="purchase_order"){
                                    $po = Pembelianheader::find($netsave->tabel_id);
                                    if($po){
                                        $po->netsuite_status    = 3;
                                        $po->document_number    = $exp[0]->documentno;
                                        $po->save();
                                    }

                                    $resp_line    =   json_decode($netsave->resp_update);

                                    foreach($po->list_pembelian as $p){

                                        $item_list = Item::find($p->item_id);

                                        if($p->line_id==""){
                                            foreach($exp[0]->message as $n){

                                                if($po->type_po=="PO LB" || $po->type_po=="PO Non Karkas" || $po->type_po=="PO Karkas" || $po->type_po=="PO Evis" || $po->type_po=="PO Transit"){
                                                    $quantity = (float)$p->berat;
                                                }else{
                                                    $quantity = (float)$p->qty;
                                                }
                                                if($n->internal_id_item == $item_list->netsuite_internal_id
                                                        && (float)$n->quantity == $quantity
                                                        && (float)$n->rate == (float)$p->harga){

                                                    if($item_list){

                                                        $po_item             = Pembelianlist::where('id', $p->id)->first();

                                                        if($po_item){
                                                            $po_item->update(['line_id' => $n->line_id]);
                                                        }

                                                    }

                                                }

                                            }
                                        }

                                    }
                                    
                                }

                                if($netsave->record_type=="sales_order"){
                                    $so = MarketingSO::find($netsave->tabel_id);
                                    if($so){
                                        $so->netsuite_status    = 1;
                                        $so->no_so              = $exp[0]->documentno;
                                        $so->save();
                                    }

                                    $resp_line            = json_decode($netsave->resp_update) ;
                                    foreach($so->listItem as $p){

                                        $item_list = Item::find($p->item_id);

                                        if($p->line_id==""){

                                            foreach($resp_line[0]->message as $n){

                                                if($n->internal_id_item == $item_list->netsuite_internal_id &&
                                                $n->quantity == $p->berat  &&
                                                $n->parting == $p->parting  &&
                                                $n->bumbu == $p->bumbu  &&
                                                $n->qty_ekr_pcs_pack == $p->qty){

                                                    if($item_list){

                                                        $order_item             = MarketingSOList::where('id', $p->id)->first();

                                                        if($order_item){
                                                            $order_item->update(
                                                                [
                                                                    'line_id' => $n->line_id
                                                                ]
                                                            );
                                                        }

                                                    }

                                                }else{
                                                }

                                            }
                                        }

                                    }
                                    
                                }

                            }

                            if ($exp[0]->status == 'Update Request Already Sent before') {
                                $netsave                     =   Netsuite::find($row->id) ;
                                $netsave->resp_update        =   $server_output ;
                                $netsave->status             =   1 ;
                                $netsave->count              =   $netsave->count+1 ;
                                $netsave->save() ;

                                if($netsave->record_type=="purchase_order"){
                                    $po = Pembelianheader::find($netsave->tabel_id);
                                    if($po){
                                        $po->netsuite_status    = 3;
                                        $po->document_number    = $exp[0]->documentno;
                                        $po->save();
                                    }
                                }

                            }
            
                            echo($exp[0]->status) . "\n";
                            echo($server_output) . "\n";
                        } catch (\Throwable $th) {
                            //throw $th;
                            $exp            =   $server_output ;
    
                            echo $th->getMessage()."\n";
                            echo $exp."\n";
    
                            $netsave                =   Netsuite::find($row->id) ;
                            $netsave->failed        =   $server_output ;
                            $netsave->status        =   0 ;
                            $netsave->save() ;
    
                        }

                    }
  
                }


                //code...
            } catch (\Throwable $th) {
                //throw $th;

                $row->status = 2;
                $row->save();

                if($log=="on"){
                    echo "FAILED ".$th->getMessage()."\n";
                }
            }
        }

    }
}

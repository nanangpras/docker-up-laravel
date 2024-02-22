<?php

namespace App\Http\Controllers;

use App\Models\Chiller;
use App\Models\Client;
use App\Models\DataOption;
use App\Models\FreestockList;
use App\Models\Grading;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\Production;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function netsuiteServer()
    {
        $url = DataOption::getOption('netsuite_url') ?? 'https://6484226-sb1.restlets.api.netsuite.com/';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $health = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($health) {
            $json = json_encode(['health' => $health, 'status' => '1']);
            return $json;
        } else {
            $json = json_encode(['health' => $health, 'status' => '0']);
            return $json;
        }
        
    }

    public function localServer()
    {
        $url = DataOption::getOption('local_url') ?? 'http://localhost/';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $health = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($health) {
            $json = json_encode(['health' => $health, 'status' => '1']);
            return $json;
        } else {
            $json = json_encode(['health' => $health, 'status' => '0']);
            return $json;
        }
        
    }

    public function cloudServer()
    {

        $api_url = env('APP_CODE', '');
        if($api_url=='clg_local'){

            $url = DataOption::getOption('cloud_url') ?? 'https://muhhusniaziz.com/';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $health = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($health) {
                $json = json_encode(['health' => $health, 'status' => '1']);
                return $json;
            } else {
                $json = json_encode(['health' => $health, 'status' => '0']);
                return $json;
            }

        }else{

            $json = json_encode(['health' => '', 'status' => '0']);
            return $json;
            
        }

        
    }

    public function inject_tanggal_so(){
        $so = Order::get();

        $resp = [];
        foreach($so as $s):
            $resp[] = array(
                'awal' => $s->created_at,
                'akhir' => date("Y-m-d H:i:s", strtotime($s->created_at. " -7 hour"))
            );
        endforeach;
        return $resp;
    }

    public function inject_tanggal_potong(){
        $prod = Production::all();

        foreach($prod as $row):
            $row->prod_tanggal_potong = $row->lpah_tanggal_potong;
            $row->save();
        endforeach;

        return "sukses";
    }
    
    public function inject_regu_kondisibb(){
        $fsl = FreestockList::orderBy('id', 'desc')->limit(1500)->get();

        // return $fsl->pluck('id');

        foreach($fsl as $row):

            if($row->chiller){

                if($row->free_stock){

                    if($row->chiller->asal_tujuan=="gradinggabungan"){
                        if(date('Y-m-d', strtotime($row->chiller->tanggal_produksi))>=date('Y-m-d',strtotime($row->free_stock->tanggal))){
                            $row->bb_kondisi = "baru";
                        }else{
                            $row->bb_kondisi = "lama";
                        }
                    }else{
                        $row->bb_kondisi = $row->chiller->asal_tujuan;
                    }
                    
                    $row->save();
                    
                }
            }

        endforeach;

        return "sukses";
    }

    public function inject_regu_kondisibb_evis(){
        $fsl = FreestockList::where('regu', 'byproduct')->where('bb_kondisi', NULL)->orderBy('id', 'desc')->limit(1500)->get();

        // return $fsl->pluck('id');

        foreach($fsl as $row):

            if($row->chiller){

                if($row->free_stock){

                    if($row->chiller->asal_tujuan=="evisgabungan"){
                        if(date('Y-m-d', strtotime($row->chiller->tanggal_produksi))>=date('Y-m-d',strtotime($row->free_stock->tanggal))){
                            $row->bb_kondisi = "baru";
                        }else{
                            $row->bb_kondisi = "lama";
                        }
                    }else{
                        $row->bb_kondisi = $row->chiller->asal_tujuan;
                    }
                    
                    $row->save();
                    
                }
            }

        endforeach;

        return "sukses";
    }


    public function custom_function(Request $request){

        $tanggal = $request->tanggal ??  date('Y-m-d');
        $grad_gabung = Chiller::where('asal_tujuan', 'gradinggabungan')->where('tanggal_produksi', $tanggal)->get();

        foreach($grad_gabung as $gg):
            echo $gg->item_name." - ".$gg->berat_item."<br>";
            $grading = Grading::where('item_id', $gg->item_id)->where('tanggal_potong',  $tanggal)->get();

            $total = 0;
            foreach($grading as $g):
                echo $gg->item_name." // ".$g->berat_item." //" .$g->total_item." // ".$g->created_at." // ".$g->updated_at."-- ";
                if($g->created_at == $g->updated_at){
                    echo "tidak diedit";
                }else{
                    echo "<span style='color: red'>diedit</span>";
                }

                echo "<br>";
                
                $total = $total + $g->berat_item;
            endforeach;
            echo "total = ".$total."< =====> selisih : ".(((float)$total-(float)$gg->berat_item))."<br>";
            echo "<br>";
            echo "<br>";
            echo "<br>";
        endforeach;
    }


    public function crawlSoEba(Request $request){
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $exe = Artisan::call("CrawlSalesOrder:process", ['--tanggal' => $tanggal]);

        return $exe;
    }

    
}

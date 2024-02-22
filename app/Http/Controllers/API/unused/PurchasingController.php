<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Antemortem;
use App\Models\AppKey;
use App\Models\Company;
use App\Models\Evis;
use App\Models\Grading;
use App\Models\Log;
use App\Models\Postmortem;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Supplier;
use App\Models\Unifomity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchasingController extends Controller
{

    public function crawlPurchasing(Request $req){
        $file = "https://docs.google.com/spreadsheets/d/e/2PACX-1vTz1F4bGJHEUHQN5U4gSc_EcKayHQC1C0_KAVeMvGVU7IPUuC4OYTkLgq18_GmsqqETu8MpO-Od8LVw/pub?output=csv";

        $fileData=fopen($file,'r');

        $ukuran = [
        "<1,1",
        ">2,0",
        "1,1-1,3",
        "1,2-1,4",
        "1,3-1,5",
        "1,4-1,6",
        "1,5-1,7",
        "1,6-1,8",
        "1,8-2,0",
        "1,9-2,1",
        "2,0-2,2",
        "Parent",
        "Pejantan"
        ];

        $jumlah = [
                2500,
                1500,
                2500,
                2200,
                2000,
                1920,
                1920,
                1920,
                1500,
                1500,
                1500,
                2000,
                2500
            ];

        while (($line = fgetcsv($fileData)) !== FALSE) {

        //    $s[] = $line;
           $timestamp = substr($line[0],0,10);
           $now = date('d/m/Y');

            if($now==$timestamp){

                    $status = 2;
                    if($line[5]!=null){
                        if($line[5]=="Deal"){
                            $status=1;

                            $selected_ukuran = "";
                    $selected_jumlah = "";
                    if($line[2]!=null){
                        $pos = array_search($line[2], $ukuran);
                        $selected_ukuran = $ukuran[$pos];
                        $selected_jumlah = $jumlah[$pos];
                    }

                    $supp = Supplier::where('id', $line[14])->first();
                    if(!$supp){
                        $supp = Supplier::where('kode', $line[1])->first();
                    }

                    $tanggal_potong = date('Y-m-d',strtotime($line[8]));

                    if($tanggal_potong=="1970-01-01" || $line[8]==""){
                        $tanggal_potong = date('Y-m-d', strtotime('tomorrow'));
                    }

                    $eksepedisi = "";
                    $sc_wilayah = "";
                    if($line[3]=="Kirim"){
                        $eksepedisi = "Kirim";
                    }else{
                        if($line[3]=="kosong"){
                            $eksepedisi = "Kosong";
                        }else{
                            $eksepedisi = "Tangkap";
                            $sc_wilayah = $line[3];
                        }
                    }

                    $jumlah_po = 0;
                    if($line[7]!=null){
                        $jumlah_po=$line[7];
                    }

                    $company_id = 1;
                    if($line[20]!=null){
                        $c = Company::where('code', $line[20])->first();
                        if($c){
                            $company_id=$c->id;
                        }
                    }

                    if($supp){

                        $data = array(
                            "user_id"           => 1,
                            "harga_penawaran"   => $line[4],
                            "harga_deal"        => $line[6],
                            "company_id"        => $company_id,
                            "supplier_id"       => $supp->id,
                            "ukuran_ayam"       => $selected_ukuran,
                            "sc_wilayah"        => $sc_wilayah,
                            "jumlah_per_mobil"  => $selected_jumlah,
                            "type_ekspedisi"    => strtolower($eksepedisi),
                            "status"            => $status,
                            "tanggal_potong"    => $tanggal_potong,
                            "jumlah_po"         => $jumlah_po
                        );

                        $request = new Request($data);

                        $purchasing = Purchasing::where('tanggal_potong', $tanggal_potong)->where('supplier_id', $supp->id)->first();

                        if($purchasing){

                            $purchasing->user_id            = 1;
                            $purchasing->harga_penawaran    = $line[4];
                            $purchasing->harga_deal         = $line[6];
                            $purchasing->company_id         = $company_id;
                            $purchasing->supplier_id        = $supp->id;
                            $purchasing->ukuran_ayam        = $selected_ukuran;
                            $purchasing->jumlah_per_mobil   = $selected_jumlah;
                            $purchasing->type_ekspedisi     = $eksepedisi;
                            $purchasing->status             = $status;
                            $purchasing->tanggal_potong     = $tanggal_potong;
                            $purchasing->jumlah_po          = $jumlah_po;

                            $purchasing->save();

                        }else{
                            $purchasing = $this->store($request);
                        }

                        }
                    }

                    }
            }
        }

        if($req->next=="purchasing"){
            return redirect('admin/purchasing');
        }else{
            $response['meta']["status"]     =   200;
            $response['meta']["message"]    =   "OK";

            return response()->json($response, $response['meta']["status"]);
        }

    }

    public function store(Request $request)
    {

        $proceed = false;
        DB::beginTransaction(); // <-- first line

        $purchasing                     =   new Purchasing;
        $purchasing->no_po              =   $request->no_po;
        $purchasing->jenis_po           =   $request->jenis_po;
        $purchasing->item_po            =   $request->item_po;
        $purchasing->harga_deal         =   $request->harga_deal;
        $purchasing->supplier_id        =   $request->supplier_id;
        $purchasing->ukuran_ayam        =   $request->ukuran_ayam;
        $purchasing->jumlah_ayam        =   $request->total_qty_order;
        $purchasing->type_ekspedisi     =   $request->type_ekspedisi;
        $purchasing->jenis_po           =   $request->jenis_ayam;
        $purchasing->tanggal_potong     =   $request->tanggal_potong;
        $purchasing->jumlah_po          =   $request->jumlah_po;


        // $purchasing->harga_penawaran    =   $request->harga_penawaran;
        // $purchasing->company_id         =   $request->company_id;
        // $purchasing->ukuran_ayam        =   $request->ukuran_ayam;
        // $purchasing->jumlah_per_mobil   =   $request->jumlah_per_mobil;
        // $purchasing->status             =   $request->status;
        $purchasing->key                =   AppKey::generate() ;

        if($purchasing->save()){
            $proceed = true;
        }

        for ($x=0; $x < $request->jumlah_po; $x++) {
            $produksi                           =   new Production ;
            $produksi->po_jenis_ekspedisi       =   $request->type_ekspedisi ;
            $produksi->purchasing_id            =   $purchasing->id ;
            $produksi->sc_wilayah               =   $request->sc_wilayah ;
            $produksi->key                      =   AppKey::generate() ;
            $produksi->save();
            if($produksi->save()){
                $proceed = true;
            }else{
                $proceed = false;
            }
        }

        if($proceed==true){

            $response['meta']["status"]     =   200;
            $response['meta']["message"]    =   "OK";
            $response["response"] =
            [
                "user_id"           =>  $request->user_id,
                "harga_penawaran"   =>  $request->harga_penawaran,
                "harga_deal"        =>  $request->harga_deal,
                "company_id"        =>  $request->company_id,
                "supplier_id"       =>  $request->supplier_id,
                "ukuran_ayam"       =>  $request->ukuran_ayam,
                "jumlah_per_mobil"  =>  $request->jumlah_per_mobil,
                "type_ekspedisi"    =>  $request->type_ekspedisi,
                "wilayah"           =>  $request->wilayah,
                "status"            =>  $request->status,
                "tanggal_potong"    =>  $request->tanggal_potong,
                "jumlah_po"         =>  $request->jumlah_po,
            ];

            DB::commit();
        }else{

            $response['meta']["status"]     =   400;
            $response['meta']["message"]    =   "Server Error";
            $response["response"] =
            [
            ];

            DB::rollBack();
        }

        return response()->json($response, $response['meta']["status"]);
    }

    public function getData(Request $request)
    {
        $data   =   Purchasing::where(function ($query) use ($request) {
                        if ($request->nomor_po) {
                            $query->where('no_po', $request->nomor_po);
                        }

                        if ($request->tanggal_potong) {
                            $query->where('tanggal_potong', $request->tanggal_potong);
                        }else{
                            $query->where('tanggal_potong', date('Y-m-d'));
                        }

                    })->get();

        $response['meta']["status"]     =   200;
        $response['meta']["message"]    =   "OK";

        $row    =   [];
        foreach ($data as $item) {
            $row[]  =   [
                'data_purchase' =>  [
                    'nomor_po'          =>  $item->no_po,
                    'harga_penawaran'   =>  $item->harga_penawaran,
                    'harga_deal'        =>  $item->harga_deal,
                    'supplier'          =>  $item->purcsupp->nama,
                    'wilayah'           =>  $item->sc_wilayah,
                    'ukuran_ayam'       =>  $item->ukuran_ayam,
                    'jumlah_per_mobil'  =>  $item->jumlah_per_mobil,
                    'tipe_ekspedisi'    =>  $item->type_ekspedisi,
                    'jenis_ayam'        =>  $item->jenis_ayam,
                    'berat_ayam'        =>  $item->berat_ayam,
                    'jumlah_ayam'       =>  $item->jumlah_ayam,
                    'tanggal_potong'    =>  $item->tanggal_potong,
                    'jumlah_po'         =>  $item->jumlah_po,
                    'data_produksi'     =>  $this->supir($item->id)
                ],
            ];
        }

        $response["response"] = $row;

        return response()->json($response, $response['meta']["status"]);
    }


    private function supir($id)
    {
        $data   =   Production::where('purchasing_id', $id)
                    ->get();

        $row    =   [];
        foreach ($data as $item) {
            $row[]  =   [
                'data_security' =>  [
                    'nomor_urut'    =>  $item->no_urut,
                    'hari_masuk'    =>  $item->sc_hari,
                    'tanggal_masuk' =>  $item->sc_tanggal_masuk,
                    'jam_masuk'     =>  $item->sc_jam_masuk,
                    'nama_supir'    =>  $item->sc_pengemudi,
                    'plat_nomor'    =>  $item->sc_no_polisi,
                    'jumlah_ayam'   =>  $item->sc_ekor_do,
                    'berat_ayam'    =>  $item->sc_berat_do,
                    'kandang'       =>  $item->sc_nama_kandang,
                    'alamat_kandang'=>  $item->sc_alamat_kandang,
                    'target'        =>  $item->sc_pengemudi_target,
                ],
                'data_lpah'     =>  [
                    'jam_bongkar'       =>  $item->lpah_jam_bongkar,
                    'tanggal_potong'    =>  $item->lpah_tanggal_potong,
                    'jam_potong'        =>  $item->lpah_jam_potong,
                    'berat_kotor'       =>  $item->lpah_berat_kotor,
                    'berat_susut'       =>  $item->lpah_berat_susut,
                    'persen_susut'      =>  $item->lpah_persen_susut,
                    'berat_diterima'    =>  $item->lpah_berat_terima,
                    'jumlah_keranjang'  =>  $item->lpah_jumlah_keranjang,
                    'berat_keranjang'   =>  $item->lpah_berat_keranjang,
                ],
                'data_evis'     =>  $this->evis($item->id),
                'data_grading'  =>  $this->grading($item->id),
                'data_qc'       =>  [
                    'ekor_ayam_mati'    =>  $item->qc_ekor_ayam_mati,
                    'persen_ayam_mati'  =>  $item->qc_persen_ayam_mati,
                    'berat_ayam_mati'   =>  $item->qc_berat_ayam_mati,
                    'ekor_ayam_merah'   =>  $item->qc_ekor_ayam_merah,
                    'persen_ayam_merah' =>  $item->qc_persen_ayam_merah,
                    'berat_ayam_merah'  =>  $item->qc_berat_ayam_merah,
                    'antemortem'        =>  $this->antemortem($item->id),
                    'postmortem'        =>  $this->postmortem($item->id),
                ],
            ];
        }

        return $row;
    }

    private function evis($id)
    {
        $data   =   Evis::where('production_id', $id)->get();

        $row    =   [];
        foreach ($data as $item) {
            $row[]  =   [
                'item'              =>  $item->eviitem->nama,
                'total_item'        =>  $item->total_item,
                'berat_item'        =>  $item->berat_item,
                'keranjang'         =>  $item->keranjang,
                'berat_keranjang'   =>  $item->berat_keranjang,
                'peruntukan'        =>  $item->jenis_peruntukan,
                'stock_item'        =>  $item->stock_item,
                'stock_berat'       =>  $item->berat_stock
            ];
        }

        return $row;
    }

    private function grading($id)
    {
        $data   =   Grading::where('trans_id', $id)->get();

        $row    =   [];
        foreach ($data as $item) {
            $row[]  =   [
                'item'              =>  $item->graditem->nama,
                'total_item'        =>  $item->total_item,
                'berat_item'        =>  $item->berat_item,
                'keranjang'         =>  $item->keranjang,
                'berat_keranjang'   =>  $item->berat_keranjang,
                'jenis_karkas'      =>  $item->jenis_karkas,
                'stock_item'        =>  $item->stock_item,
                'stock_berat'       =>  $item->berat_stock
            ];
        }

        return $row;
    }

    private function antemortem($id)
    {
        $data   =   Antemortem::where('production_id', $id)->get();
        $row    =   [];
        foreach ($data as $item) {
            $row[]  =   [
                'basah_bulu'    =>  $item->basah_bulu,
                'keaktifan'     =>  $item->keaktifan,
                'cairan'        =>  $item->cairan,
                'ayam_mati'     =>  $item->ayam_mati,
                'ayam_mati_kg'  =>  $item->ayam_mati_kg,
                'ayam_sakit'    =>  $item->ayam_sakit,
                'memar_dada'    =>  $item->memar_dada,
                'memar_paha'    =>  $item->memar_paha,
                'memar_sayap'   =>  $item->memar_sayap,
                'patah_sayap'   =>  $item->patah_sayap,
                'patah_kaki'    =>  $item->patah_kaki,
                'keropeng_kaki' =>  $item->keropeng_kaki,
                'keropeng_sayap'=>  $item->keropeng_sayap,
                'keropeng_dada' =>  $item->keropeng_dada,
                'keropeng_pg'   =>  $item->keropeng_pg,
            ];
        }

        return $row;
    }

    private function postmortem($id)
    {
        $data   =   Postmortem::where('production_id', $id)->get();
        $row    =   [];
        foreach ($data as $item) {
            $row[]  =   [
                'tembolok_kondisi'  =>  $item->tembolok_kondisi,
                'tembolok_jumlah'   =>  $item->tembolok_jumlah,
                'ayam_merah'        =>  $item->ayam_merah,
                'kehijauan'         =>  $item->kehijauan,
                'jeroan_hati'       =>  $item->jeroan_hati,
                'jeroan_jantung'    =>  $item->jeroan_jantung,
                'jeroan_ampela'     =>  $item->jeroan_ampela,
                'jeroan_usus'       =>  $item->jeroan_usus,
                'rerata'            =>  $item->rerata,
                'catatan'           =>  $item->catatan,
            ];
        }

        return $row;
    }


    public function itemReceipt(Request $request){
        $tanggal        = $request->tanggal ?? date('Y-m-d');
        $id             = $request->id ?? "";
        $purchasing     = Purchasing::where('tanggal_potong', $tanggal)->where('status','1');

        if($id!=""){
            $purchasing = $purchasing->where('id', $id);
        }

        $purchasing = $purchasing->get();

        $response = [];

        foreach($purchasing as $row):

            $production = Production::where('purchasing_id', $row->id)->get();

            $line_prod = [];
            $line_qc = [];
            foreach($production as $prod):


                $qc = array(
                    'antemorthem'   => Antemortem::where('production_id', $row->id)->first(),
                    'postmorthem'   => Postmortem::where('production_id', $row->id)->first(),
                    'uniformity'    => Unifomity::where('production_id', $row->id)->first()
                );

                $line = array(
                    'item_po'           => $row->item_po,
                    'qty'               => $prod->jumlah_ayam,
                    'berat'             => $prod->berat_ayam,
                );

                $data = array(
                    'id_po'             => $row->id,
                    'id_prod'           => $prod->id,
                    'nomor_po'          => $row->no_po,
                    'tanggal_potong'    => $row->tanggal_potong,
                    'data_qc'           => $qc,
                    'line'              => $line,
                    'no_do'             => $prod->no_do,
                    'tanggal_do'        => $prod->tanggal_do,
                    'gudang'            => "CGL-Live-Birds",
                );

                $response[] = $data;

            endforeach;



        endforeach;

        return $response;
    }
}

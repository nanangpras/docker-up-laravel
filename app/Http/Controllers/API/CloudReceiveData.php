<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LaporanEvis;
use App\Models\LaporanRendemen;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CloudReceiveData extends Controller
{
    //
    public function receive_laporan_local(Request $request){

        if(file_get_contents('php://input')!=""){
            
            try {
                //code...
                $json  = file_get_contents('php://input');
                $data  = json_decode($json, TRUE);

                $subsidiary = $data['subsidiary'];

                $rendemen                           =   LaporanRendemen::where('tanggal', $data['tanggal'])
                                                        ->where('subsidiary', $subsidiary)
                                                        ->first() ?? new LaporanRendemen ;

                $rendemen->tanggal                  =   $data['tanggal'] ;
                $rendemen->subsidiary_id            =   $data['laporan_rendemen'][0]['subsidiary_id'] ;
                $rendemen->subsidiary               =   $data['laporan_rendemen'][0]['subsidiary'] ;
                $rendemen->rendemen_total           =   $data['laporan_rendemen'][0]['rendemen_total'] ;
                $rendemen->rendemen_tangkap         =   $data['laporan_rendemen'][0]['rendemen_tangkap'] ;
                $rendemen->rendemen_kirim           =   $data['laporan_rendemen'][0]['rendemen_kirim'];
                $rendemen->berat_rpa                =   $data['laporan_rendemen'][0]['berat_rpa'] ;
                $rendemen->berat_grading            =   $data['laporan_rendemen'][0]['berat_grading'] ;
                $rendemen->berat_evis               =   $data['laporan_rendemen'][0]['berat_evis'] ;
                $rendemen->darah_bulu               =   $data['laporan_rendemen'][0]['darah_bulu'] ;
                $rendemen->ekor_rpa                 =   $data['laporan_rendemen'][0]['ekor_rpa'] ;
                $rendemen->ekor_grading             =   $data['laporan_rendemen'][0]['ekor_grading'] ;
                $rendemen->selisih_ekor             =   $data['laporan_rendemen'][0]['selisih_ekor'] ;
                $rendemen->jumlah_supplier          =   $data['laporan_rendemen'][0]['jumlah_supplier'] ;
                $rendemen->jumlah_po_mobil          =   $data['laporan_rendemen'][0]['jumlah_po_mobil'] ;
                $rendemen->selesai_potong            =   $data['laporan_rendemen'][0]['selesai_potong'] ;
                $rendemen->ekor_do                  =   $data['laporan_rendemen'][0]['ekor_do'] ;
                $rendemen->berat_do                 =   $data['laporan_rendemen'][0]['berat_do'] ;
                $rendemen->rerata_do                =   $data['laporan_rendemen'][0]['rerata_do'] ;
                $rendemen->ekoran_seckel            =   $data['laporan_rendemen'][0]['ekoran_seckel'] ;
                $rendemen->kg_terima                =   $data['laporan_rendemen'][0]['kg_terima'] ;
                $rendemen->rerata_terima_lb         =   $data['laporan_rendemen'][0]['rerata_terima_lb'] ;
                $rendemen->susut_tangkap            =   $data['laporan_rendemen'][0]['susut_tangkap'] ;
                $rendemen->susut_kirim              =   $data['laporan_rendemen'][0]['susut_kirim'] ;
                $rendemen->susut_seckel             =   $data['laporan_rendemen'][0]['susut_seckel'] ;
                $rendemen->ekoran_grading           =   $data['laporan_rendemen'][0]['ekoran_grading'] ;
                $rendemen->selisih_seckel_grading   =   $data['laporan_rendemen'][0]['selisih_seckel_grading'] ;
                $rendemen->rerata_grading           =   $data['laporan_rendemen'][0]['rerata_grading'] ;
                $rendemen->save();
                

                $data_evis = $data['laporan_evis'];

                foreach($data_evis as $ev):

                    $sebaran                    =   LaporanEvis::where('item_id', $ev['item_id'])
                                                ->where('tanggal', $ev['tanggal'])
                                                ->where('subsidiary_id', $ev['subsidiary_id'])
                                                ->where('subsidiary', $subsidiary)
                                                ->first() ?? new LaporanEvis();

                    $sebaran->tanggal           =   $ev['tanggal'] ;
                    $sebaran->subsidiary_id     =   $ev['subsidiary_id'] ;
                    $sebaran->subsidiary        =   $subsidiary ;
                    $sebaran->item_id           =   $ev['item_id'] ;
                    $sebaran->sku               =   $ev['sku'] ;
                    $sebaran->nama              =   $ev['nama'] ;
                    $sebaran->qty               =   $ev['qty'] ?? NULL;
                    $sebaran->berat             =   $ev['berat'] ;
                    $sebaran->save();

                endforeach;

                $data_grading = $data['laporan_grading'];

                foreach($data_grading as $gd):

                    $sebaran                    =   LaporanSebarankarkas::where('item_id', $gd['item_id'])
                                                ->where('tanggal', $gd['tanggal'])
                                                ->where('subsidiary_id', $gd['subsidiary_id'])
                                                ->where('subsidiary', $subsidiary)
                                                ->first() ?? new LaporanSebarankarkas() ;

                    $sebaran->tanggal           =   $gd['tanggal'] ;
                    $sebaran->subsidiary_id     =   $gd['subsidiary_id'] ;
                    $sebaran->subsidiary        =   $subsidiary ;
                    $sebaran->item_id           =   $gd['item_id'] ;
                    $sebaran->sku               =   $gd['sku'] ;
                    $sebaran->nama              =   $gd['nama'] ;
                    $sebaran->qty               =   $gd['qty'] ?? NULL ;
                    $sebaran->berat             =   $gd['berat'] ;
                    $sebaran->save();

                endforeach;

                return $data;
                
            } catch (\Throwable $th) {
                //throw $th;

                return $th->getMessage();
            }
        }
    }
}

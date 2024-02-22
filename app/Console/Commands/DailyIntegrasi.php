<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyIntegrasi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DailyIntegrasi:process  {--tanggal=} {--log=} {--wo=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily approve proses integrasi';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $wo             =  $this->option('wo') ?? "off";


        if ($wo == 'so') {
            $tanggal    = date('Y-m-d', strtotime('-2 days'));
            DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND document_code like 'SO.%' AND trans_date = '".$tanggal."'");
            echo "Integrasi diapprove - ".$tanggal;


        } else if ($wo == 'musnahkan') {
            $tanggal    = date('Y-m-d', strtotime('-2 days'));
            DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND tabel = 'musnahkan' AND DATE(created_at) = '".$tanggal."'");
            echo "Integrasi diapprove - ".$tanggal;

        } else if ($wo == '1' || $wo == '2') {
            

            $hari      = date('D');
            
            if (env('NET_SUBSIDIARY')=='CGL') {
                // CGL
                // Senin H-2 (Kiriman hari Minggu)
                // Selasa H-2 (Kiriman hari senin)
                // Rabu H-2 (Kiriman hari selasa)
                // Kamis H-2 (Kiriman hari rabu)
                // Jumat H-2 (Kiriman hari kamis)
                // Sabtu TIDAK ADA KIRIMAN
                // Minggu H-3 (Kiriman hari Jumat)

                if ($hari=="Mon"){
                    $tanggal    = date('Y-m-d', strtotime("-2 day")); 

                } elseif ($hari=="Tue") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day")); 

                } elseif ($hari=="Wed") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day"));

                } elseif ($hari=="Thu") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day"));

                } elseif ($hari=="Fri") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day"));

                } elseif ($hari=="Sat") {
                    // TIDAK ADA KIRIMAN
                    echo "Sabtu tidak ada WO";
                    return false;
                } elseif ($hari=="Sun") {
                    $tanggal    = date('Y-m-d', strtotime("-3 day"));

                }

                // $tanggal    = date('Y-m-d', strtotime("-4 day")); 
            } else {
                // EBA
                // Senin H-3 (Kiriman hari Sabtu)
                // Selasa H-2 (Kiriman hari senin)
                // Rabu H-2 (Kiriman hari selasa)
                // Kamis H-2 (Kiriman hari rabu)
                // Jumat H-2 (Kiriman hari kamis)
                // Sabtu H-2 (Kiriman hari Jumat)
                // Minggu TIDAK ADA KIRIMAN

                if($hari=="Mon"){
                    $tanggal    = date('Y-m-d', strtotime("-3 day"));
                    
                } elseif ($hari=="Tue") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day")); 

                } elseif ($hari=="Wed") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day"));

                } elseif ($hari=="Thu") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day"));

                } elseif ($hari=="Fri") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day"));

                } elseif ($hari=="Sat") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day"));

                } elseif ($hari=="Sun") {
                    //TIDAK ADA KIRIMAN
                    echo "Minggu tidak ada WO";
                    return false;
                }
            }

            $log        = $this->option('log') ?? "off";

            if (env('NET_SUBSIDIARY')=='CGL' && $hari == 'Sun') {

                DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND document_code like 'wo-2%' AND trans_date <='".$tanggal."'");
                DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND tabel = 'productions' AND trans_date  <='".$tanggal."'");
                echo "Integrasi diapprove sampai dengan tanggal - ".$tanggal;

            } elseif (env('NET_SUBSIDIARY')=='EBA' && $hari == 'Mon') {

                DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND document_code like 'wo-2%' AND trans_date  <='".$tanggal."'");
                DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND tabel = 'productions' AND trans_date  <='".$tanggal."'");
                echo "Integrasi diapprove - ".$tanggal;

            } else {
                DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND document_code like 'wo-2%' AND trans_date = '".$tanggal."'");
                DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND tabel = 'productions' AND trans_date = '".$tanggal."'");
                echo "Integrasi diapprove - ".$tanggal;
            }

        } else if ($wo == '3') {

            $tanggal    = date('Y-m-d', strtotime('-2 days'));
            $log        = $this->option('log') ?? "off";

            DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND document_code like 'ABF-%' AND trans_date = '".$tanggal."'");
            echo "Integrasi diapprove - ".$tanggal;

        } else if ($wo == '4' || $wo == '5' || $wo == '6') {
            $tanggal    = date('Y-m-d', strtotime('-2 days'));
            $log        = $this->option('log') ?? "off";
    
            DB::select("UPDATE netsuite SET status = 2 WHERE (document_code like 'TW-%' or label like 'wo-5%' or document_code like 'RETUR-%') and status = 5 AND trans_date = '".$tanggal."'");
            echo "Integrasi diapprove - ".$tanggal;

        } else {
            $tanggal    = $this->option('tanggal') ?? date('Y-m-d', strtotime('-5 days'));
            $log        = $this->option('log') ?? "off";
    
            DB::select("UPDATE netsuite SET status = 2 WHERE status = 5 AND trans_date<='".$tanggal."'");
            echo "Integrasi diapprove - ".$tanggal;
        }
        

    }
}
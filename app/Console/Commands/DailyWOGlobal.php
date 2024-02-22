<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\WOController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class DailyWOGlobal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DailyWOGlobal:process  {--tanggal=} {--log=} {--regu=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activity WO Global process';

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

        // echo $this->option('tanggal');
        // return false;
        $tanggal    = $this->option('tanggal') ?? date('Y-m-d');
        $log        = $this->option('log') ?? "off";
        
        // if ($this->option('tanggal')!="") {
        if ($this->option('log') == 'on') {
            $tanggal   =  $this->option('tanggal') ?? date('Y-m-d');
            $timestamp = strtotime($tanggal);

            // $hari      = date('D', $timestamp);
            $hari      = date('D', $timestamp);
            
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
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal))); 

                } elseif ($hari=="Tue") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal))); 

                } elseif ($hari=="Wed") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal)));

                } elseif ($hari=="Thu") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal)));

                } elseif ($hari=="Fri") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal)));

                } elseif ($hari=="Sat") {
                    // TIDAK ADA KIRIMAN
                    echo "Sabtu tidak ada WO";
                    return false;
                } elseif ($hari=="Sun") {
                    $tanggal1    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal)));
                    $tanggal2    = date('Y-m-d', strtotime("-3 day", strtotime($tanggal)));

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
                    $tanggal1    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal)));
                    $tanggal2    = date('Y-m-d', strtotime("-3 day", strtotime($tanggal)));
                    // $tanggal    = '2024-01-07'; 
                    
                } elseif ($hari=="Tue") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal))); 

                } elseif ($hari=="Wed") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal)));

                } elseif ($hari=="Thu") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal)));

                } elseif ($hari=="Fri") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal)));

                } elseif ($hari=="Sat") {
                    $tanggal    = date('Y-m-d', strtotime("-2 day", strtotime($tanggal)));

                } elseif ($hari=="Sun") {
                    //TIDAK ADA KIRIMAN
                    echo "Minggu tidak ada WO";
                    return false;
                }
            }
        } 


        // [NEW REVISI 2024-01-03]

        if (env('NET_SUBSIDIARY')=='CGL' && $hari == 'Sun') {
            if($this->option('regu')!=""){
                
                $regu           = $this->option('regu');
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
            } else {
                
                //Boneless
                $regu           = 'boneless';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'parting';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'marinasi';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'whole';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'frozen';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
            }

            

        } elseif (env('NET_SUBSIDIARY')=='EBA' && $hari == 'Mon') {
            if($this->option('regu')!=""){
                
                $regu           = $this->option('regu');
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
            } else {
                
                //Boneless
                $regu           = 'boneless';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'parting';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'marinasi';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'whole';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'frozen';
                $wo_controller  = new WOController();
                $console        = 'true';
                echo "WO Process - ".$tanggal1." - ".$tanggal2." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal1, $regu, $console)."\n";
                echo $wo_controller->generateWOGlobal($tanggal2, $regu, $console)."\n";
    
            }


        } else {
            if($this->option('regu')!=""){
                
                $regu           = $this->option('regu');
                $wo_controller  = new WOController();
                $tanggal        = $tanggal;
                $console        = 'true';
                echo "WO Process - ".$tanggal." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal, $regu, $console)."\n";
    
            } else {
                
                //Boneless
                $regu           = 'boneless';
                $wo_controller  = new WOController();
                $tanggal        = $tanggal;
                $console        = 'true';
                echo "WO Process - ".$tanggal." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'parting';
                $wo_controller  = new WOController();
                $tanggal        = $tanggal;
                $console        = 'true';
                echo "WO Process - ".$tanggal." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'marinasi';
                $wo_controller  = new WOController();
                $tanggal        = $tanggal;
                $console        = 'true';
                echo "WO Process - ".$tanggal." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'whole';
                $wo_controller  = new WOController();
                $tanggal        = $tanggal;
                $console        = 'true';
                echo "WO Process - ".$tanggal." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal, $regu, $console)."\n";
    
                //Boneless
                $regu           = 'frozen';
                $wo_controller  = new WOController();
                $tanggal        = $tanggal;
                $console        = 'true';
                echo "WO Process - ".$tanggal." - ".$regu."\n";
                echo $wo_controller->generateWOGlobal($tanggal, $regu, $console)."\n";
    
            }
        }
    }
}

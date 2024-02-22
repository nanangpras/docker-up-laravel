<?php

namespace App\Console\Commands;

use App\Models\Abf;
use App\Models\Chiller;
use Illuminate\Console\Command;

class AutomaticCutoff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutomaticCutoff:process  {--tanggal=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic Cut Off Type Bahan Baku More Than 3 Day for CGL and 2 Day for EBA';

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
        $tanggal        = $this->option('tanggal') ?? date('Y-m-d');
        $subsidiary     = env('NET_SUBSIDIARY') ?? "";
        
        if($subsidiary == 'CGL'){
            $newtanggal = date('Y-m-d', strtotime('-4 days', strtotime($tanggal)));
        }else{
            $newtanggal     = date('Y-m-d', strtotime('-3 days', strtotime($tanggal)));
            $newtanggalFG   = date('Y-m-d', strtotime('-4 days', strtotime($tanggal)));
        }

        // $dataAbf        = Abf::where('tanggal_masuk',$newtanggal)->get()->count();
        if($subsidiary == 'CGL'){
            Chiller::where('tanggal_produksi',$newtanggal)->where(function($q){
                            $q->whereIn('type',['bahan-baku','hasil-produksi']);
                            $q->where('status',2);
                        })
                        ->whereNotIn('item_id', [4529,11341])
                        ->update(['status_cutoff' => 1 ]);
        }

        if($subsidiary == 'EBA'){

            Chiller::where('tanggal_produksi',$newtanggalFG)->where(function($q){
                            $q->whereIn('type',['hasil-produksi']);
                            $q->where('status',2);
                        })
                        ->whereNotIn('item_id',[8246,11299])
                        ->update(['status_cutoff' => 1 ]);
            
            Chiller::where('tanggal_produksi',$newtanggal)->where(function($q){
                            $q->whereIn('type',['bahan-baku']);
                            $q->where('status',2);
                        })
                        ->whereNotIn('item_id',[8246,11299])
                        ->update(['status_cutoff' => 1 ]);
        }

        $result         = "Data Chiller sudah tutup";
        $this->info($result);
    }
}

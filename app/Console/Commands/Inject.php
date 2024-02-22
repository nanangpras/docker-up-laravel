<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\FreestockTemp;
use App\Models\Item;
use Illuminate\Console\Command;

class Inject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Inject:process  {--tanggal=} {--jenis=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inject data command';

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
        // $data   =   FreestockTemp::whereDate('tanggal_produksi', $this->option('tanggal') ?? date('Y-m-d'))
        //             ->get();

        // $run    =   '';
        // $run    .=  "DATA PLASTIK ----\n";
        // foreach ($data as $row) {
        //     $exp        =   json_decode($row->label);

        //     if ($exp->plastik) {
        //         $run    .=  'FS-' . $row->freestock_id . ' // ' . $row->item->nama . ' // ' . $exp->plastik->sku . ' - ' . $exp->plastik->jenis . ' (' . $exp->plastik->qty . ')' . "\n";
        //         $row->prod_nama         =   Item::find($row->item_id)->nama;
        //         $row->plastik_sku       =   $exp->plastik->sku;
        //         $row->plastik_nama      =   $exp->plastik->jenis;
        //         $row->plastik_qty       =   $exp->plastik->qty;
        //         $row->save();
        //     }
        // }

        // $run    .=  "\n\n";
        // $run    .=  "DATA CUSTOMER---- \n";

        // $data   =   FreestockTemp::whereDate('tanggal_produksi', $this->option('tanggal') ?? date('Y-m-d'))
        //             ->where('regu', '!=', 'byproduct')
        //             ->get();

        // foreach ($data as $row) {
        //     $exp        =   json_decode($row->label);
        //     $sub        =   explode(' || ', $exp->sub_item);

        //     if ($sub[0]) {
        //         $konsumen           =   Customer::where('nama', $sub[0])->first();
        //         $row->customer_id   =   $konsumen->id;
        //         $row->save();

        //         $run   .=  '(' . $row->id . ') - ' . $row->customer_id . ' // ' . $sub[0] . "\n";
        //     }
        // }

        // echo $run;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Purchasing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DailyPOCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DailyPOCheck:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Selesaikan PO dan Production';

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

        DB::beginTransaction();
            $po = Purchasing::whereBetween('created_at', array(date("Y-m-d 00:00:01", strtotime( '-7 days' ) ), date("Y-m-d 23:59:59", strtotime( '-3 days' ) )))
                ->whereIn('status',[2])
                ->get();

            echo "complete order check : ".date("Y-m-d 00:00:01", strtotime( '-7 days' ) )." to ".date("Y-m-d 23:59:59", strtotime( '-3 days' ) )."\n";
            echo "found ".count($po)." data"."\n";

            foreach($po as $row):
                $row->status = 1;
                $row->save();

                echo $row->no_po." Telah diselesaikan otomatis \n";
            endforeach;

            echo "proses selesai";
        DB::commit();
    }
}

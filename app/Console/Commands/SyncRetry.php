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

class SyncRetry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncRetry:process  {--tanggal=} {--log=} {--id=}';

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

        $tanggal    = $this->option('tanggal') ?? date('Y-m-d');
        $log        = $this->option('log') ?? "off";
        $id         = $this->option('id') ?? "";

        $ns_count = Netsuite::whereIn('status', [6])->count();
        Netsuite::whereIn('status', [6])->update(['status'=>'2']);

        echo "Aktifasi ulang ".$ns_count." integrasi";

    }
}

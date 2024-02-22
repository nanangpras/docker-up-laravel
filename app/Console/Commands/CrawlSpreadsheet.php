<?php

namespace App\Console\Commands;

use App\Models\AppKey;
use App\Models\Company;
use App\Models\DataOption;
use App\Models\Item;
use App\Models\Production;
use App\Models\PurchaseItem;
use App\Models\Purchasing;
use App\Models\Supplier;
use App\Models\Temperature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CrawlSpreadsheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CrawlSpreadsheet:process {--tanggal=} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl Spreadsheet Data';

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

        $app_crawl = env("APP_CRAWL", "gsheet");


        $update_data    =   0;
        $insert_data    =   0;
        $commit         = false;

        $tanggal = $this->option('tanggal') ?? date('Y-m-d');
        $log = $this->option('log') ?? "off";
        echo "Process ...".$tanggal."...\n";

            DB::beginTransaction();

            $file   = "https://docs.google.com/spreadsheets/d/e/2PACX-1vSh-TUmH1-kZEd8xyjOscMUgr4SS1mrJ1M6B6V_v5bno978yJa4B-ZH7xMctMfKEXGNPfWshU7km9nW/pub?gid=1353239251&single=true&output=csv";

            $fileData=fopen($file,'r');

                $update_data    =   0;
                $insert_data    =   0;

                $no = 0;
                $data = [];
                while (($line = fgetcsv($fileData)) !== FALSE) {

                    try {
                        //code...

                        if($line[0]!="" && $no>0){
                            $timestamp = date('Y-m-d H:i:s', strtotime($line[0] . ' ' . $line[1]));
                            $data[] = [
                                'time'                  => date('Y-m-d', strtotime($line[0])),
                                'stamp'                 => $line[1],
                                'suhu'                  => $line[2],
                                'lembab'                => $line[3],
                                'lokasi'                => $line[4],
                                'nama_sensor'           => $line[5],
                                'kode_perangkat'        => $line[6],
                            ];

                            $existingData = Temperature::where('timestamp', $timestamp)->first();

                            // dd($existingData, $timestamp);

                            if ($existingData) {
                                $existingData->update([
                                    'timestamp'      => $timestamp,
                                    'suhu'           => $line[2],
                                    'lembab'         => $line[3],
                                    'lokasi'         => $line[4],
                                    'nama_sensor'    => $line[5],
                                    'kode_perangkat' => $line[6],
                                ]);

                                $update_data++;
                            } 
                            else {
                                    $datas = Temperature::create([
                                        'timestamp'      => $timestamp,
                                        'suhu'           => $line[2],
                                        'lembab'         => $line[3],
                                        'lokasi'         => $line[4],
                                        'nama_sensor'    => $line[5],
                                        'kode_perangkat' => $line[6],
                                    ]);
    
                                    $insert_data++;
                            }
                            
                        }
                        
                        
                        DB::commit();
                        $response['meta']["status"]     =   200;
                        $response['meta']["message"]    =   "OK";
                        
                    } catch (\Throwable $th) {
                        //throw $th;
                        DB::rollBack();
                        $response['meta']["status"]     =   200;
                        $response['meta']["message"]    =   "GAGAL";
                        echo $th->getMessage();
                    }

                    $no++;
                        
                }

                // if($commit==true){
                //     DB::commit();
                //     $response['meta']["status"]     =   200;
                //     $response['meta']["message"]    =   "OK";
                // }else{
                //     DB::rollBack();
                //     $response['meta']["status"]     =   200;
                //     $response['meta']["message"]    =   "GAGAL";
                // }

                fclose($fileData);

                $response['response']["data_insert"]    =   $insert_data;
                $response['response']["data_update"]    =   $update_data;


                echo response()->json([
                    'Data Successfully insert : ' => $insert_data,
                    'Data Successfully update : ' => $update_data,
                ]);

        }

}

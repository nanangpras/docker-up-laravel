<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\FreestockList;
use App\Models\FreestockTemp;
use App\Models\Item;
use App\Models\Production;
use App\Models\Purchasing;
use App\Models\Unifomity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanProduksiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal    =   $request->tanggal ?? date('Y-m-d');
        $tanggalend =   $request->tanggalend ?? date('Y-m-d');
        $report     =   $request->report ?? '';
        $data       =   Production::whereIn('purchasing_id', Purchasing::select('id')->whereBetween('tanggal_potong', [$tanggal, $tanggalend])->where(function ($query) use ($report) {
            if ($report) {
                if ($report == 'po_lb') {
                    $query->where('jenis_po', 'PO LB');
                }
                if ($report == 'non_lb') {
                    $query->where('jenis_po', '!=', 'PO LB');
                }
            }
        }))
            ->where('sc_status', '1')
            ->orderBy('prod_tanggal_potong')
            ->orderBy('no_urut', 'ASC')
            ->get();

        $uniformArr =   [];
        $underArr   =   [];
        $overArr    =   [];
        foreach ($data as $dat) {
            $uniformity =   Unifomity::where('production_id', $dat->id)->get();

            $uniform    =   0;
            $under      =   0;
            $over       =   0;
            foreach ($uniformity as $uni) {
                if ($uni->uniprod->prodpur->ukuran_ayam == '8-10') {
                    if ($uni->berat >= '0.8' and $uni->berat <= '1') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '0.8') {
                        $under += 1;
                    }
                    if ($uni->berat > '1') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.0-1.2') {
                    if ($uni->berat >= '1' and $uni->berat <= '1.2') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1') {
                        $under += 1;
                    }
                    if ($uni->berat > '1.2') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.2-1.4') {
                    if ($uni->berat >= '1.2' and $uni->berat <= '1.4') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1.2') {
                        $under += 1;
                    }
                    if ($uni->berat > '1.4') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.4-1.6') {
                    if ($uni->berat >= '1.4' and $uni->berat <= '1.6') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1.4') {
                        $under += 1;
                    }
                    if ($uni->berat > '1.6') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.6-1.8') {
                    if ($uni->berat >= '1.6' and $uni->berat <= '1.8') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1.6') {
                        $under += 1;
                    }
                    if ($uni->berat > '1.8') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '1.8-2.0') {
                    if ($uni->berat >= '1.8' and $uni->berat <= '2') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '1.8') {
                        $under += 1;
                    }
                    if ($uni->berat > '2') {
                        $over += 1;
                    }
                }
                if ($uni->uniprod->prodpur->ukuran_ayam == '2.0-2.2') {
                    if ($uni->berat >= '2.0' and $uni->berat <= '2.2') {
                        $uniform += 1;
                    }
                    if ($uni->berat < '2.0') {
                        $under += 1;
                    }
                    if ($uni->berat > '2.2') {
                        $over += 1;
                    }
                }
            }

            $uniformArr[]   =   $uniform;
            $underArr[]     =   $under;
            $overArr[]      =   $over;
        }

        $arr = [
            'under'     =>  $underArr,
            'over'      =>  $overArr,
            'uni'       =>  $uniformArr,
        ];

        if ($request->key == 'export') {
            $data        =   clone $data;
            $data;
            $tanggal    =   $request->tanggal ?? date('Y-m-d');
            $tanggalend =   $request->tanggalend ?? date('Y-m-d');
            return view('admin.pages.produksi.laporan.laporan-produksi-export', compact('data', 'request', 'tanggal', 'tanggalend', 'arr'));
        }

        return view('admin.pages.produksi.laporan.index', compact('data', 'arr', 'tanggal', 'tanggalend', 'request'));
    }

    public function hasilbbfg(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $bahan_baku     =   FreestockList::select(DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS jumlah"), 'free_stocklist.item_id', 'free_stocklist.freestock_id', 'items.nama', 'items.sku', 'chiller.type')
            ->whereIn('free_stock.regu', ['boneless', 'parting', 'marinasi', 'whole', 'frozen'])
            ->where('free_stock.status', '3')
            ->where('free_stock.tanggal', $tanggal)
            ->leftJoin('items', 'items.id', '=', 'free_stocklist.item_id')
            ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
            ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
            ->orderBy('items.nama')
            ->groupBy('items.nama')
            ->groupBy('chiller.type')
            ->get();
        $hasil_produksi   =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"), 'item_id', 'items.nama', 'free_stocktemp.*')
            ->whereIn('free_stock.regu', ['boneless', 'parting', 'marinasi', 'whole', 'frozen'])
            ->where('free_stock.status', '3')
            ->where('free_stock.tanggal', $tanggal)
            ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
            ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')
            ->orderBy('items.nama')
            ->groupBy('items.nama')
            ->get();
        $env = env("NET_SUBSIDIARY", "CGL");
        $bom   =   Bom::whereIn('bom_name', [$env . " - KARKAS - BONELESS BROILER", $env . " - AYAM PARTING BROILER", $env . " - AYAM PARTING MARINASI BROILER", $env . " - AYAM KARKAS BROILER", $env . " - AYAM KARKAS FROZEN", $env . " - KARKAS - BONELESS BROILER"])->get();
        if ($request->key == 'export') {
            $tanggal = $request->tanggal ?? date('Y-m-d');
            $clonebb = clone $bahan_baku;
            $clonefg = clone $hasil_produksi;
            $clonebb;
            $clonefg;
            return view('admin.pages.produksi.unduh.export_bbfg', compact('tanggal', 'clonebb', 'clonefg', 'bom'));
        }
        return view('admin.pages.produksi.hasil-bbfg', compact('bahan_baku', 'hasil_produksi', 'tanggal', 'bom'));
    }
}

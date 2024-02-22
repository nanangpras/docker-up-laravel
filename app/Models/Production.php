<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Purchasing;
use App\Models\Lpah;
use App\Models\Evis;
use App\Models\Chiller;
use App\Models\Seckle;
use App\Models\Bonus_driver;
use App\Models\Grading;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;

class Production extends Model
{
    //
    protected $table    =   'productions';
    protected $appends  =   ['ppic_status', 'status_lpah', 'status_evis', 'status_grading', 'berat_lpah', 'berat_isi', 'berat_bersih_lpah', 'total_lpah', 'prod_yield_produksi', 'total_bersih_lpah', 'berat_keranjang', 'aksi_qc', 'kondisi_ayam', 'cek_qc','selisih_lpah_grading','qty_grading','berat_item_grading','qty_evis_production','berat_evis_production'];

    public function getCekQcAttribute()
    {
        $data   =   Antemortem::where('production_id', $this->id)->first();
        if ($data) {
            return (($data->basah_bulu != NULL) and ($data->keaktifan != NULL) and ($data->cairan != NULL)) ? TRUE : FALSE;
        } else {
            return FALSE;
        }
    }

    public function getAksiQcAttribute()
    {
        $data   =   Antemortem::where('production_id', $this->id)->count();
        if ($data > 0) {
            return "<span class='status status-success'>Proses</span>";
        } else {
            return "<span class='status status-warning'>Pending</span>";
        }
    }

    public function getKondisiAyamAttribute()
    {
        $data   =   Antemortem::select('basah_bulu')
            ->where('production_id', $this->id)
            ->first();

        if ($data) {
            if ($data->basah_bulu == '0') {
                return 'Kering';
            } else {
                return $data->basah_bulu ? ('Basah Bulu ' . $data->basah_bulu . ' %') : '####';
            }
        } else {
            return '####';
        }
    }

    public static function setNotifSecurity($id) {
        $production = Production::find($id);
        if ($production->sc_status == 1) {
            echo '<span class="status status-success">Sesuai</span>';
        } elseif ($production->sc_status == 2) {
            echo '<span class="status status-danger">Batal</span>';
        } elseif ($production->sc_status == 3) {
            echo '<span class="status status-warning">Kecil</span>';
        } else {
            return false;
        }
    }

    public function getTotalLpahAttribute()
    {
        // return Lpah::where('production_id', $this->id)->where('type', 'isi')->sum('qty');
        return Production::find($this->id)->ekoran_seckle ?? "0";
    }

    public function getBeratLpahAttribute()
    {
        return Lpah::where('production_id', $this->id)->sum('berat');
    }

    public function getBeratIsiAttribute()
    {
        return Lpah::where('production_id', $this->id)->where('type', 'isi')->sum('berat');
    }

    public function getBeratKeranjangAttribute()
    {
        return Lpah::where('production_id', $this->id)->where('type', 'kosong')->sum('berat');
    }

    public function getBeratBersihLpahAttribute()
    {
        $isi                =   Lpah::where('production_id', $this->id)->where('type', 'isi')->sum('berat');
        $kosong             =   Lpah::where('production_id', $this->id)->where('type', 'kosong')->sum('berat');
        $mati               =   $this->qc_berat_ayam_mati ?? 0;
        $tembolok           =   $this->qc_tembolok ?? 0;
        $berat_ayam_merah   =   $this->qc_hitung_ayam_merah == 1 ? $berat_ayam_merah = $this->qc_berat_ayam_merah : 0;

        $basah      =   Antemortem::select('basah_bulu')
                        ->where('production_id', $this->id)
                        ->first();

        // if ($this->po_jenis_ekspedisi == 'tangkap') {
        //     return round(($isi - $kosong - $tembolok - ($basah->basah_bulu ?? 0) - $berat_ayam_merah),1);
        //     $dataBersih = round($isi,1) - round($kosong,1) - round($tembolok,1) - ($basah->basah_bulu ?? 0) - round($berat_ayam_merah,1);
        //     $dataBersih = round($isi,1) - round($kosong,1) - round($tembolok,1) - ($basah->basah_bulu ?? 0) ;
        //     return $dataBersih;

        // } else {
            // return round(($isi - $kosong - $tembolok - $mati - ($basah->basah_bulu ?? 0) - $berat_ayam_merah),1);
            // $dataBersih = round($isi,1) - round($kosong,1) - round($tembolok,1) - round($mati,1) - ($basah->basah_bulu ?? 0) - round($berat_ayam_merah,1);
            $dataBersih = round($isi,1) - round($kosong,1) - round($tembolok,1) - round($mati,1) - ($basah->basah_bulu ?? 0);
            return $dataBersih;
        // }

        // if ($this->po_jenis_ekspedisi == 'tangkap') {
        //     return (round($isi) - round($kosong) - round($tembolok) - round($basah->basah_bulu ?? 0)) - round(($berat_ayam_merah));
        // } else {
        //     return (round($isi) - round($kosong) - round($tembolok) - round($mati) - round(($basah->basah_bulu ?? 0)) - round($berat_ayam_merah));
        // }
    }

    public function getProdYieldProduksiAttribute()
    {
        $isi        =   Lpah::where('production_id', $this->id)->where('type', 'isi')->sum('berat');
        $kosong     =   Lpah::where('production_id', $this->id)->where('type', 'kosong')->sum('berat');
        $mati       =   $this->qc_berat_ayam_mati ?? 0;
        $tembolok   =   $this->qc_tembolok ?? 0;

        $summary    =   Grading::where('trans_id', $this->id)->where('keranjang', 0)->orderBy('id', 'DESC')->get();
        $gradberat   =   0;
        $graditem    =   0;
        foreach ($summary as $row) {
            $gradberat   +=  $row->berat_item;
            $graditem    +=  $row->total_item;
        }

        if($this->lpah_berat_terima>0){

            if ($this->po_jenis_ekspedisi == 'tangkap'){
                // $yield_produksi = ($gradberat / ($this->lpah_berat_terima - $mati)) * 100;
                $yield_produksi = ($gradberat / ($this->lpah_berat_terima)) * 100;
            }else{
                $yield_produksi = ($gradberat / ($this->lpah_berat_terima)) * 100;
            }


        }else{
            $yield_produksi = 0;
        }

        return $yield_produksi ?? 0;
    }

    public static function yieldProduksiHarian($tanggal)
    {
        $tanggal = $tanggal ?? date('Y-m-d');
        $prod = Production::where('prod_tanggal_potong', $tanggal)->get();

        $yield_produksi = 0;
        foreach($prod as $p):
            $yield_produksi = $yield_produksi+$p->prod_yield_produksi;
        endforeach;

        if(count($prod)>0){
            $yield_produksi = $yield_produksi/count($prod);
        }

        return $yield_produksi ?? 0;
    }

    public function getTotalBersihLpahAttribute()
    {
        $data       =   Antemortem::select(DB::raw('(ayam_mati + ayam_sakit) AS susut'))
            ->first();

        return $this->total_lpah - ($data->susut ?? 0);
    }

    public function getStatusLpahAttribute()
    {
        if ($this->lpah_status == NULL) {
            return 'Pending';
        }
        if ($this->lpah_status == 2) {
            return 'Proses';
        }
        if ($this->lpah_status == 1) {
            return 'Selesai';
        }
    }

    public function getStatusEvisAttribute()
    {
        if ($this->evis_status == NULL) {
            return 'Pending';
        }
        if ($this->evis_status == '2') {
            return 'Proses';
        }
        if ($this->evis_status == '1') {
            return 'Selesai';
        }
    }

    public function getStatusGradingAttribute()
    {
        if ($this->grading_status == NULL) {
            return 'Pending';
        }
        if ($this->grading_status == '2') {
            return 'Proses';
        }
        if ($this->grading_status == '1') {
            return 'Selesai';
        }
    }

    public function prodpur()
    {
        return $this->belongsTo(Purchasing::class, 'purchasing_id', 'id');
    }

    public function proddriver()
    {
        return $this->belongsTo(Driver::class, 'sc_pengemudi_id', 'id');
    }

    public function prodlpah()
    {
        return $this->hasMany(Lpah::class, 'production_id', 'id');
    }

    public function prodlpahisi()
    {
        return $this->hasMany(Lpah::class, 'production_id', 'id')->where('type', 'isi');
    }

    public function prodlpahkosong()
    {
        return $this->hasMany(Lpah::class, 'production_id', 'id')->where('type', 'kosong');
    }

    public function prodevis()
    {
        return $this->hasMany(Evis::class, 'production_id', 'id');
    }

    public function prodgrad()
    {
        return $this->hasMany(Grading::class, 'trans_id', 'id');
    }

    public function prodchill()
    {
        return $this->hasMany(Chiller::class, 'id', 'trans_id')->withTrashed();
    }

    public function prodseck()
    {
        return $this->hasMany(Seckle::class, 'id', 'trans_id')->withTrashed();
    }

    public function prodbonus()
    {
        return $this->hasMany(Bonus_driver::class, 'id', 'trans_id')->withTrashed();
    }

    public function prodqcuni()
    {
        return $this->hasMany(Unifomity::class, 'production_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'sc_pengemudi_id', 'id');
    }

    public function antem()
    {
        return $this->belongsTo(Antemortem::class, 'id', 'production_id');
    }
    public function post()
    {
        return $this->belongsTo(Postmortem::class, 'id', 'production_id');
    }

    public function nekrop()
    {
        return $this->belongsTo(Nekropsi::class, 'id', 'production_id');
    }

    public function adminedt()
    {
        return $this->hasMany(Adminedit::class, 'table_id', 'id')->where('table_name', 'productions')->where('type', 'edit-security')->where('content','!=','Data awal security') ;
    }


    public static function nomor_urut($tanggal, $type = false)
    {
        $data   =   Production::select('no_urut')
                    ->whereIn('purchasing_id', Purchasing::select('id')
                        ->whereIn('type_po', ['PO LB', 'PO Maklon'])
                        ->where('prod_tanggal_potong', $tanggal))
                    ->where('sc_status', '1')
                    ->orderBy('no_urut', 'DESC')
                    ->count();

        $do   =   Production::where(function ($query) use ($type) {
                    if ($type == 'pending') {
                        $query->where('no_urut', NULL);
                    }
                    })
                    ->whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO LB', 'PO Maklon'])->whereDate('prod_tanggal_potong', $tanggal))
                    ->count();

        if ($do > 0) {
            return $data ? $data + 1 : 1;
        } else {
            return 0;
        }
    }

    public static function nomor_lpah($id)
    {
        $data   =   Production::select(DB::raw('max(SUBSTRING(no_lpah, -4)) as nom'))
            ->whereMonth('prod_tanggal_potong', date('m'))
            ->orderBy('no_lpah', 'DESC')
            ->limit(1)
            ->first();

        $prod   =   Production::find($id);

        if ($data->nom == null) {
            $nomor =  1;
        } else {
            $nomor =  $data->nom + 1;
        }

        if ($prod->prodpur->jenis_po == 'PO Karkas') {
            return env("NET_SUBSIDIARY", "CGL").'.KRKS.' . date('Ym', strtotime($prod->created_at)) . str_pad((string)$nomor, 4, "0", STR_PAD_LEFT);
        } else if ($prod->prodpur->type_po == 'PO Frozen') {
            return env("NET_SUBSIDIARY", "CGL").'.FRZN.' . date('Ym', strtotime($prod->created_at)) . str_pad((string)$nomor, 4, "0", STR_PAD_LEFT);
        } else if ($prod->prodpur->type_po == 'PO Evis') {
            return env("NET_SUBSIDIARY", "CGL").'.EVIS.' . date('Ym', strtotime($prod->created_at)) . str_pad((string)$nomor, 4, "0", STR_PAD_LEFT);
        } else if ($prod->prodpur->type_po == 'PO Boneless') {
            return env("NET_SUBSIDIARY", "CGL").'.BNLS.' . date('Ym', strtotime($prod->created_at)) . str_pad((string)$nomor, 4, "0", STR_PAD_LEFT);
        } else {
            return env("NET_SUBSIDIARY", "CGL").'.LPAH.' . date('Ym', strtotime($prod->created_at)) . str_pad((string)$nomor, 4, "0", STR_PAD_LEFT);
        }
    }

    public static function hitung_do($type, $tanggal)
    {
        $data   =   Production::where(function ($query) use ($type) {
            if ($type == 'pending') {
                $query->where('no_urut', NULL);
            }
        })
            ->whereIn('purchasing_id', Purchasing::select('id')->whereIn('type_po', ['PO LB', 'PO Maklon'])->whereDate('tanggal_potong', $tanggal))
            ->count();

        return $data;
    }

    public function getPpicStatusAttribute()
    {
        if ($this->prodpur) {
            if ($this->prodpur->type_po == 'PO LB') {
                if ($this->sc_status == 1) {
                    return '<span class="status status-success">Datang [' . $this->sc_jam_masuk . ']</span>';
                } else {
                    return '<span class="status status-warning">Pending</span>';
                }
            } else {
                if ($this->ppic_acc == 1 or $this->ppic_acc == null) {
                    return '<span class="status status-warning">Pending</span>';
                } elseif ($this->ppic_acc == 2) {
                    return "<span class='status status-success'>Terima " . strtoupper($this->ppic_tujuan) . "</span>";
                } elseif ($this->ppic_acc == 3) {
                    return '<span class="status status-info">Selesai</span>';
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }


    public static function hitungYieldHarian($tanggal)
    {

        $grading_rpa =   Grading::whereIn('trans_id', Production::select('id')->whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->where('lpah_tanggal_potong', $tanggal)
            ->where('grading_status', '1')
            ->where('evis_status', '1'))
            ->select(DB::raw("SUM(total_item) AS ekor"), DB::raw("SUM(berat_item) AS berat"), DB::raw("(SUM(berat_item) / SUM(total_item)) AS ratarata"))
            ->first();

        $berat_grading  = 0;
        $ekor_grading  = 0;
        if($grading_rpa){
            $berat_grading      = $grading_rpa->berat;
            $ekor_grading       = $grading_rpa->ekor;
        }


        $terima_rpa   =   Production::whereIn('purchasing_id', Purchasing::select('id')->where('jenis_po', 'PO LB'))->where('lpah_tanggal_potong', $tanggal)
            ->select(
                DB::raw("po_jenis_ekspedisi"),
                DB::raw("SUM(ekoran_seckle) AS seckle"),
                DB::raw("SUM(lpah_berat_terima) AS kg_terima"),
                DB::raw("SUM(qc_berat_ayam_mati) AS kg_mati")
            )
            ->where('grading_status', '1')
            ->where('evis_status', '1')
            ->where('no_urut', '!=', NULL)
            ->first();

        $berat_rpa     = 0;
        $ekor_rpa     = 0;
        $kg_mati     = 0;
        if($terima_rpa){
            $berat_rpa      = $terima_rpa->kg_terima;
            $ekor_rpa       = $terima_rpa->seckle;
            $kg_mati       = $terima_rpa->kg_mati;
        }

        if($berat_rpa>0 && $berat_grading>0){

            if ($terima_rpa->po_jenis_ekspedisi == 'tangkap'){
                $yield_produksi = $berat_grading/($berat_rpa-$kg_mati)*100;
            }else{
                $yield_produksi = $berat_grading/($berat_rpa)*100;
            }

        }else{
            $yield_produksi = 0;
        }

        return $yield_produksi ?? 0;

    }


    public static function hitung_lb($tanggal, $field)
    {
        return Production::whereDate('prod_tanggal_potong', $tanggal)->sum($field);
    }

    public function getSelisihLpahGradingAttribute(){
        $datagrading    =  Grading::where('trans_id', $this->id)->sum('total_item');
        $dataseckle     =  Production::select('ekoran_seckle')->where('id', $this->id)->first();
        
        return $datagrading - $dataseckle->ekoran_seckle;
    }

    public function getQtyGradingAttribute(){
        $grading = Grading::where('trans_id', $this->id)->sum('total_item');
        return $grading;
    }

    public function getBeratItemGradingAttribute(){
        $grading = Grading::where('trans_id', $this->id)->sum('berat_item');
        return $grading;
    }
    public function getQtyEvisProductionAttribute(){
        $evis = Evis::where('production_id', $this->id)->where('item_id','184')->sum('total_item');
        return $evis;
    }
    public function getBeratEvisProductionAttribute(){
        $evis = Evis::where('production_id', $this->id)->where('item_id','184')->sum('berat_item');
        return $evis;
    }

    public static function wordwraptext($text,$length){
        $newtext    = wordwrap($text,$length,"<br/>",true);
        return $newtext;
    }

    public static function hitung_downtime($jamBongkar, $jamSelesai, $ekorSekle) {
        $waktuWajar          = $ekorSekle / DataOption::getOption('lpah_downtime');
        $waktuWajar          = round($waktuWajar * 60) + 10;
        $mulai               = new DateTime(date('Y-m-d'). $jamBongkar);
        $selesai             = new DateTime(date('Y-m-d'). $jamSelesai);
        $diff                = $mulai->diff($selesai);
        $jam                 = $diff->h;
        $menit               = $diff->i;
        $jamKeMenit          = $jam * 60;
        $tambahMenit         = $jamKeMenit + $menit;
        $hasilDowntime       = $tambahMenit - $waktuWajar;

        
        return $hasilDowntime;

    }

    public static function sebaran_lb($id, $tanggal_awal, $tanggal_akhir, $type='sc_ekor_do')
    {
        return  Production::
                            join('purchasing', 'productions.purchasing_id', '=', 'purchasing.id')
                            ->whereBetween('lpah_tanggal_potong', [$tanggal_awal, $tanggal_akhir])
                            ->where('purchasing.ukuran_ayam', $id)
                            ->sum($type);
    }
    public static function new_sebaran_lb_persupplier($id, $supplier_id, $tanggal_awal, $tanggal_akhir, $type='sc_ekor_do')
    {
        return   Production::join('purchasing', 'productions.purchasing_id', '=', 'purchasing.id')
                            ->join('supplier', 'supplier.id', '=', 'purchasing.supplier_id')
                            ->whereBetween('productions.lpah_tanggal_potong',[$tanggal_awal, $tanggal_akhir])
                            ->select('supplier.nama AS nama_supplier', 'supplier.id AS id_supplier',  DB::raw('SUM(productions.sc_ekor_do) AS qty_ekor_lb'))
                            ->groupBy('supplier.id', 'purchasing.ukuran_ayam')
                            ->where('purchasing.supplier_id', $supplier_id)
                            ->where('purchasing.ukuran_ayam', $id)
                            ->sum($type);
    }
    public static function sebaran_lb_all($id, $type='sc_ekor_do')
    {
        return  Production::
                            join('purchasing', 'productions.purchasing_id', '=', 'purchasing.id')
                            ->where('purchasing.ukuran_ayam', $id)
                            ->sum($type);
    }

    public static function sebaran_lb_supplier($id_supplier, $id_item, $tanggal_awal2, $tanggal_akhir2) {
        // dd($id_supplier);
        $datas = [];
        foreach($id_supplier as $supplier) {
            $datas[]   =  Production::
                                    join('purchasing', 'productions.purchasing_id', '=', 'purchasing.id')
                                    ->join('supplier', 'supplier.id', '=', 'purchasing.supplier_id')
                                    ->whereBetween('productions.lpah_tanggal_potong',[$tanggal_awal2, $tanggal_akhir2])
                                    ->select('supplier.nama AS nama_supplier', 'supplier.id AS id_supplier',  DB::raw('SUM(productions.sc_ekor_do) AS qty_ekor_lb'))
                                    ->groupBy('supplier.id', 'purchasing.ukuran_ayam')
                                    ->where('purchasing.supplier_id', $supplier->id_supplier)
                                    ->where('purchasing.ukuran_ayam', $id_item)
                                    ->first();
        }
        return $datas;
    }
    public static function nomorpolisi($search){
        $query          = Production::select('sc_no_polisi')
                                    ->where('sc_no_polisi','!=',null)
                                    ->where('sc_no_polisi','like', '%'.$search.'%')
                                    ->groupBy('sc_no_polisi')
                                    ->orderBy('id','DESC')
                                    ->get();
        $array          = array();
        if($query->count() > 0){
            foreach($query as $key => $value){
                $array[]  = $value->sc_no_polisi;
            }
        }else{
            $array = '';
        }

        return $array;
    }

    public static function cekLpahStatus($id, $tanggal){
        $newtgl         = date('Y-m-d', strtotime('+2 days', strtotime($tanggal)));
        $now            = Carbon::now();
        if($now > $newtgl){
            $query          = Production::find($id);
            $result         = $query->lpah_status;
        }else{
            $result         = "OK";
        }   
        return $result;
    }

    public static function cekProductionStatus($id, $tanggal){
        $newtgl         = date('Y-m-d', strtotime('+2 days', strtotime($tanggal)));
        $now            = Carbon::now();
        if($now > $newtgl){
            $query          = Production::find($id);
            $result         = $query->sc_status;
        }else{
            $result         = "OK";
        }   
        return $result;
    }
}

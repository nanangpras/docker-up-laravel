<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FreestockList;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Freestock extends Model
{
    use SoftDeletes;
    protected $table    =   'free_stock';
    protected $appends  =   ['nomor_freestock'];
    const kirim_wo      = NULL;
    const tidak_kirim_wo = 0;

    public static function get_nomor()
    {
        $data   =   Freestock::select('nomor')
                    ->whereDate('tanggal', date('Y-m-d'))
                    ->orderBy('id', 'DESC')
                    ->first();

        return ($data->nomor ?? 0) + 1 ;
    }

    public function orderitem() {
        return $this->hasOne(OrderItem::class, 'id', 'orderitem_id');
    }

    public function getNomorFreestockAttribute()
    {
        return 'FREESTOCK.CGL.'. date('Ym', strtotime($this->tanggal)) . str_pad((string)$this->nomor, 3, "0", STR_PAD_LEFT) ;
    }

    public function listfreestock()
    {
        return $this->hasMany(FreestockList::class, 'freestock_id', 'id');
    }

    public function freetemp()
    {
        return $this->hasMany(FreestockTemp::class, 'freestock_id', 'id');
    }
    public function freetempcategory()
    {
        return $this->hasMany(FreestockTemp::class, 'freestock_id', 'id');
    }

    public function netsuite()
    {
        return $this->hasOne(Netsuite::class, 'id', 'netsuite_id');
    }

    public function getHistoryDeleteTemp() {
        return $this->belongsTo(FreestockTemp::class, 'id', 'freestock_id')->whereNotNull('deleted_at')->withTrashed();
    }

    public function getHistoryDeleteList() {
        return $this->belongsTo(FreestockList::class, 'id', 'freestock_id')->whereNotNull('deleted_at')->withTrashed();
    }

    public static function getNameData($table,$condition,$data){
        $query                      = DB::table($table)->select($data)->where('id', $condition)->get();
        if ($query->count() > 0) {
            foreach ($query as $h) {
                $hasil = $h->$data;
            }
        } else {
            $hasil = '';
        }
        return $hasil;
    }

    public static function getNetsuiteSend($id){
        $query                      = Freestock::select('netsuite_send')->where('id', $id)->get();
        if ($query->count() > 0) {
            foreach ($query as $h) {
                $hasil  = $h->netsuite_send;
            }
        } else {
            $hasil      = '';
        }
        return $hasil;
    }


    public static function getDataWONONWO($regu, $tanggalAwal, $tanggalAkhir, $getDataWOBB, $getDataWOFG,$getDataNONWOBB, $getDataNONWOFG) {

        $clone_data     =   FreestockList::select(DB::raw("SUM(berat) AS kg"), DB::raw("SUM(qty) AS jumlah"), 'free_stocklist.item_id', 'free_stocklist.freestock_id', 'items.nama', 'items.sku', 'chiller.type')
                                ->where('free_stock.regu', $regu)
                                ->where('free_stock.status', '3')
                                ->whereBetween('free_stock.tanggal', [$tanggalAwal,$tanggalAkhir])
                                ->leftJoin('items', 'items.id', '=', 'free_stocklist.item_id')
                                ->leftJoin('chiller', 'chiller.id', '=', 'free_stocklist.chiller_id')
                                ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocklist.freestock_id')
                                ->orderBy('items.nama')
                                ->groupBy('items.nama')
                                ->groupBy('chiller.type');
                        

        $clone_produksi   =   FreestockTemp::select(DB::raw("SUM(qty) AS jumlah"), DB::raw("SUM(berat) AS kg"), 'free_stocktemp.item_id', 'items.nama','items.sku', 'free_stocktemp.*')
                                ->where('free_stock.regu', $regu)
                                ->where('free_stock.status', '3')
                                ->whereBetween('free_stock.tanggal', [$tanggalAwal,$tanggalAkhir])
                                ->leftJoin('items', 'items.id', '=', 'free_stocktemp.item_id')
                                ->leftJoin('free_stock', 'free_stock.id', '=', 'free_stocktemp.freestock_id')
                                ->orderBy('items.nama')
                                ->groupBy('items.nama');



        if ($clone_data && $clone_produksi) {
            $id_item_bb=[];
            $id_item_prod=[];
            $getDataFG                  = clone $clone_produksi;
            $getDataNONWO               = clone $clone_data;
            $collectionQueryBBWO        = new Collection();
            $collectionQueryFGWO        = new Collection();
            $collectionNetsuiteNullBB   = new Collection();
            $collectionNetsuiteNullFG   = new Collection();
    
    
            $getAllArray                = [];
    
    
    
            $cek_bb    = FreestockList::cek_bb_non_wo($regu, $tanggalAwal, $tanggalAkhir);
            $cek_prod  = FreestockTemp::cek_non_wo_produksi($regu, $tanggalAwal, $tanggalAkhir);
    
    
            foreach ($cek_bb as $item_bb) {
                foreach ($cek_prod as $item_prod) {
                    if ($item_bb->item_id == $item_prod->item_id) {
    
                        // JIKA BB LEBIH BANYAK DARI FG
                        if ($item_bb->berat > $item_prod->berat) {
    
                            // SIMPAN DATA ITEM KE ARRAY
                            $id_item_bb[]   = $item_bb->item_id;
    
                            // $arrayBBWO[] = collect([
                            //     'item_id'   => $item_bb->item_id,
                            //     'berat'     => $item_bb->berat - $item_prod->berat,
                            //     'qty'       => $item_bb->qty,
                            //     'type'      => $item_bb->type
                            // ]);
    
                            $collectionQueryBBWO->push((object)
                            [
                                'item_id'   => $item_bb->item_id,
                                'berat'     => $item_bb->berat - $item_prod->berat,
                                'qty'       => $item_bb->qty - $item_prod->qty,
                                'type'      => $item_bb->type,
                                'sku'       => $item_bb->sku
    
                            ]);
    
    
                            $collectionNetsuiteNullBB->push((object)
                            [
                                'item_id'   => $item_bb->item_id,
                                'berat'     => $item_bb->berat,
                                'qty'       => $item_bb->qty,
                                'type'      => $item_bb->type,
                                'sku'       => $item_bb->sku
    
                            ]);
    
                            $collectionNetsuiteNullFG->push((object)
                            [
                                'item_id'   => $item_bb->item_id,
                                'berat'     => $item_bb->berat,
                                'qty'       => $item_bb->qty,
                                'type'      => $item_bb->type,
                                'sku'       => $item_bb->sku
    
                            ]);
    
                        } else if ($item_prod->berat > $item_bb->berat) {
                            // dd($item_prod->berat);
                            // SIMPAN DATA ITEM KE ARRAY
                            $id_item_prod[]   = $item_prod->item_id;
    
                            $collectionQueryFGWO->push((object)
                            [
                                'item_id'   => $item_prod->item_id,
                                'berat'     => $item_prod->berat - $item_bb->berat,
                                'qty'       => $item_prod->qty - $item_bb->qty,
                                'type'      => $item_prod->type,
                                'sku'       => $item_prod->sku
    
                            ]);
    
    
                            $collectionNetsuiteNullBB->push((object)
                            [
                                'item_id'   => $item_bb->item_id,
                                'berat'     => $item_bb->berat,
                                'qty'       => $item_bb->qty,
                                'type'      => $item_bb->type,
                                'sku'       => $item_prod->sku
    
    
                            ]);
    
                            $collectionNetsuiteNullFG->push((object)
                            [
                                'item_id'   => $item_bb->item_id,
                                'berat'     => $item_bb->berat,
                                'qty'       => $item_bb->qty,
                                'type'      => $item_bb->type,
                                'sku'       => $item_bb->sku
    
                            ]);
    
                            // dd($arrayNONWO);
                        }
                    }
                }
            }
    
            // QUERY BB NON WO
            $queryBBWO               = (clone $clone_data)->whereNull('free_stock.netsuite_send')
                                        ->where(function($query) use ($id_item_prod, $id_item_bb) {
                                            $query
                                            ->whereNotIn('free_stocklist.item_id', $id_item_prod)
                                            ->whereNotIn('free_stocklist.item_id', $id_item_bb)
                                            ->orWhere('chiller.type', '=', 'bahan-baku')
                                            ;
                                        })
                                        ->get();
    
    
            // QUERY HASIL PRODUKSI KIRIM WO
            // $queryFGWO                  = (clone $clone_produksi)->whereNull('free_stock.netsuite_send')->get();
    
    
            // QUERY HASIL PRODUKSI WO
            $queryFGWO               = (clone $getDataFG)->whereNull('free_stock.netsuite_send')
                                            ->where(function($query) use ($id_item_prod, $id_item_bb) {
                                                $query->whereNotIn('free_stocktemp.item_id', $id_item_prod)
                                                ->whereNotIn('free_stocktemp.item_id', $id_item_bb);
                                        })
                                        ->get();
    
    
            $netsuite_null_bb       = (clone $getDataNONWO)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')
                                            ->get();
    
            $netsuite_null_fg       = (clone $getDataFG)->where('free_stock.netsuite_send', '0')->whereNull('free_stock.orderitem_id')
                                            ->get();
    
    
            if ($getDataWOBB == 'getDataWOBB') {
    
                foreach($queryBBWO as $dataBBWO){
                    $collectionQueryBBWO->push((object)
                        [
                        'item_id'   => $dataBBWO->item_id,
                        'berat'     => $dataBBWO->kg,
                        'qty'       => $dataBBWO->jumlah,
                        'type'      => $dataBBWO->type,
                        'sku'       => $dataBBWO->sku
                        ]);
                    }
    
                if ($getDataWOFG == NULL || $getDataNONWOBB == NULL || $getDataNONWOFG == NULL) {
                    return $collectionQueryBBWO;
                } else {
                    $getAllArray[] = $collectionQueryBBWO;
    
                }
            }
    
    
    
            if ($getDataWOFG == 'getDataWOFG') {
    
                foreach($queryFGWO as $dataFGWO){
                    $collectionQueryFGWO->push((object)
                        [
                        'item_id'   => $dataFGWO->item_id,
                        'berat'     => $dataFGWO->kg,
                        'qty'       => $dataFGWO->jumlah,
                        'type'      => $dataFGWO->type,
                        'sku'       => $dataFGWO->sku
                        ]);
                    }
    
    
                if ($getDataWOBB == NULL || $getDataNONWOBB == NULL || $getDataNONWOFG == NULL) {
                    return $collectionQueryFGWO;
                } else {
                    $getAllArray[] = $collectionQueryFGWO;
    
                }
            }
    
            if ($getDataNONWOBB == 'getDataNONWOBB') {
    
                foreach($netsuite_null_bb as $dataNONWOBB){
                    $collectionNetsuiteNullBB->push((object)
                        [
                        'item_id'   => $dataNONWOBB->item_id,
                        'berat'     => $dataNONWOBB->kg,
                        'qty'       => $dataNONWOBB->jumlah,
                        'type'      => $dataNONWOBB->type,
                        'sku'       => $dataNONWOBB->sku
                        ]);
                    }
    
                if ($getDataWOBB == NULL || $getDataWOFG == NULL || $getDataNONWOFG == NULL) {
                    return $collectionNetsuiteNullBB;
    
                } else {
                    $getAllArray[] = $collectionNetsuiteNullBB;
    
                }
    
            }
    
            if ($getDataNONWOFG == 'getDataNONWOFG') {
    
                foreach($netsuite_null_fg as $dataNONWOFG){
                    $collectionNetsuiteNullFG->push((object)
                        [
                        'item_id'   => $dataNONWOFG->item_id,
                        'berat'     => $dataNONWOFG->kg,
                        'qty'       => $dataNONWOFG->jumlah,
                        'type'      => $dataNONWOFG->type,
                        'sku'       => $dataNONWOFG->sku
                        ]);
                    }
    
                if ($getDataWOBB == NULL || $getDataWOFG == NULL || $getDataNONWOBB == NULL) {
                    return $collectionNetsuiteNullFG;
    
                } else {
                    $getAllArray[] = $collectionNetsuiteNullFG;
    
                }
            }
    
            return $getAllArray;
    
        } else {
            return true;
        }

    }

}

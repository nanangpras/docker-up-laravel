<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abf;
use App\Models\Adminedit;
use App\Models\AppKey;
use App\Models\Bahanbaku;
use App\Models\Chiller;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Item;
use App\Models\Netsuite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemLog;
use App\Models\Product_gudang;
use App\Models\Retur;
use App\Models\Returalasan;
use App\Models\ReturItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\Undefined;

class ReturController extends Controller
{
    public function index()
    {
        $customer           =   Customer::where('nama', '!=', '')
            ->where('netsuite_internal_id', '!=', NULL)
            ->orderBy('nama')->get();
        $item       =   Item::all();
        $data       =   Retur::where('status', 1)->first();
        $summary    =   Retur::where('status', 2)->get();
        $driver     =   Driver::orderBy('id', 'DESC')->get();
        $alasan     =   Returalasan::all();
        return view('admin.pages.retur.qc-retur', compact('customer', 'item', 'summary', 'data', 'driver', 'alasan'));
    }

    public function storecustomer(Request $request)
    {
        if ($request->customer) {
            $data   =   new Retur;

            $data->customer_id      =   $request->customer;
            $data->qc_id            =   Auth::user()->id;
            $data->tanggal_retur    =   Carbon::now();
            $data->status           =   1;
            $data->save();

            return redirect()->to(url()->previous() . '#custom-tabs-three-nonso')->with('status', 1)->with('message', 'Berhasil Selesaikan');
        }
        return redirect()->to(url()->previous() . '#custom-tabs-three-nonso')->with('status', 2)->with('message', 'Konsumen belum dipilih');
    }

    public function store(Request $request)
    {
        if ($request->key == 'alasan') {
            $alasan             =   new Returalasan;
            if (($request->jenis == "Bau") || ($request->jenis == "Memar") || ($request->jenis == "Patah") || ($request->jenis == "Warna Tidak Standar") || ($request->jenis == "Kualitas lain-lain")) {
                $alasan->jenis      =   "Kualitas";
            } else {
                $alasan->jenis      =   "Non Kualitas";
            }
            $alasan->kelompok   =   $request->jenis;
            $alasan->nama       =   $request->alasan;
            $alasan->save();

            return back()->with('status', 1)->with('message', 'Tambah alasan berhasil');
        } else {
            $item       =   new ReturItem;
            $cekitem    =   Item::find($request->item);

            $item->retur_id         =   Retur::where('status', 1)->first()->id;
            $item->item_id          =   $request->item;
            $item->orderitem_id     =   $request->orderitem_id;
            $item->sku              =   $cekitem->sku;
            $item->qty              =   $request->qty;
            $item->berat            =   $request->berat;
            $item->unit             =   $request->tujuan;
            $item->rate             =   $request->harga;
            $item->status           =   1;
            $item->save();

            return redirect()->to(url()->previous() . '#custom-tabs-three-nonso')->with('status', 1)->with('message', 'Berhasil Simpan');
        }
    }

    public function edit(Request $request)
    {
        if ($request->key == 'editPenanganan') {
            // return $request->id;
            $data       = ReturItem::find($request->id);
            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'data'   => $data
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                ]);
            }
        } else
            if ($request->key == 'updatePenanganan') {
            DB::beginTransaction();

            $cekReturItem               = ReturItem::find($request->id);
            $dataReturLama              = ReturItem::find($request->id);

            $cloneAbf                   = Abf::where('table_name', 'retur_item')->where('table_id', $cekReturItem->retur_id)
                                        ->where('qty_awal', $cekReturItem->qty)->where('berat_awal', $cekReturItem->berat)
                                        ->where('qty_item', $cekReturItem->qty)->where('berat_item', $cekReturItem->berat)
                                        ->where('label', Customer::where('id', Retur::where('id', $cekReturItem->retur_id)->first()->customer_id)->first()->nama)
                                        ->where('grade_item', $cekReturItem->grade_item)
                                        ->where('jenis', 'masuk')->where('item_id', $cekReturItem->item_id);

            $cloneChiller               = Chiller::where('table_name', 'retur_item')->where('table_id', $cekReturItem->retur_id)
                                        ->where('jenis', 'masuk')->where('label', Customer::where('id', Retur::where('id', $cekReturItem->retur_id)->first()->customer_id)->first()->nama)
                                        ->where('item_id', $cekReturItem->item_id)
                                        ->where('qty_item', $cekReturItem->qty)->where('berat_item', $cekReturItem->berat)
                                        ->where('stock_item', $cekReturItem->qty)->where('stock_berat', $cekReturItem->berat);

            $cloneCekAbf                = clone $cloneAbf;
            $cloneCekChiller            = clone $cloneChiller;

            $cekAbf                     = $cloneCekAbf->first();
            $cekChiller                 = $cloneCekChiller->first();

            // $cloneDeletedItemAbf        = clone $cloneCekAbf;
            $cloneDeletedItemChiller    = clone $cloneChiller;

            // $deletedItemAbf             = $cloneDeletedItemAbf->where('deleted_at', NULL)->first();
            $deletedItemChiller         = $cloneDeletedItemChiller->whereNotNull('deleted_at')->withTrashed()->first();


            if ($cekReturItem) {

                // Nanti disini data Log sebelum edit penanganan
                $cekLogAwal                         = Adminedit::where('table_name', 'retur_item')->where('type', 'retur')->where('content', 'Original Item')->where('activity', '!=', 'Retur Salah Item/Tidak Sesuai Pesanan')
                    ->where('table_id', $dataReturLama->id)->first();
                if (!$cekLogAwal) {

                    $logAwal                        =   new Adminedit;
                    $logAwal->data                  =   json_encode([
                        'data' => $dataReturLama,
                    ]);
                    $logAwal->table_name            =   'retur_item';
                    $logAwal->activity              =   'retur';
                    $logAwal->status                =   1;
                    $logAwal->content               =   'Dawa Awal Retur Item';
                    $logAwal->type                  =   'edit';
                    $logAwal->table_id              =   $dataReturLama->id;
                    $logAwal->user_id               =   Auth::user()->id;

                    if (!$logAwal->save()) {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Gagal simpan Log Awal.',
                            'status'  => 'error'
                        ]);
                        return false;
                    }
                }


                // Cek Data Penanganan
                $cekItem                            = Item::find($cekReturItem->item_id);

                // INSERT CHILLER DARI TABEL SELAIN ASALNYA SAMPINGAN
                $tambahChiller                      =   new Chiller;
                $tambahChiller->table_name          =   'retur_item';
                $tambahChiller->table_id            =   $cekReturItem->retur_id;
                $tambahChiller->asal_tujuan         =   'retur';
                $tambahChiller->item_id             =   $cekReturItem->item_id;
                $tambahChiller->item_name           =   $cekItem->nama;
                $tambahChiller->jenis               =   'masuk';
                $tambahChiller->type                =   $cekItem->category_id == 1 ? 'bahan-baku' : 'hasil-produksi';
                $tambahChiller->label               =   Customer::where('id', Retur::where('id', $cekReturItem->retur_id)->first()->customer_id)->first()->nama;
                $tambahChiller->qty_item            =   $cekReturItem->qty;
                $tambahChiller->tanggal_potong      =   date('Y-m-d', strtotime($cekReturItem->created_at));
                $tambahChiller->berat_item          =   $cekReturItem->berat;
                $tambahChiller->tanggal_produksi    =   date('Y-m-d', strtotime($cekReturItem->created_at));
                $tambahChiller->stock_item          =   $cekReturItem->qty;
                $tambahChiller->stock_berat         =   $cekReturItem->berat;
                $tambahChiller->status              =   2;

                // End insert

                // ----------------------------------------------------------------
                // Insert data baru pada Abf
                $tambahAbf                      =   new Abf;
                $tambahAbf->tanggal_masuk       =   date('Y-m-d', strtotime($cekReturItem->created_at));
                $tambahAbf->label               =   Customer::where('id', Retur::where('id', $cekReturItem->retur_id)->first()->customer_id)->first()->nama;
                $tambahAbf->table_name          =   'retur_item';
                $tambahAbf->table_id            =   $cekReturItem->retur_id;
                $tambahAbf->asal_tujuan         =   'retur';
                $tambahAbf->item_id             =   $cekReturItem->item_id;
                $tambahAbf->item_id_lama        =   $cekReturItem->item_id;
                $tambahAbf->item_name           =   $cekItem->nama;
                $tambahAbf->jenis               =   'masuk';
                $tambahAbf->type                =   'free';
                $tambahAbf->customer_id         =   $cekReturItem->customer_id;
                $tambahAbf->qty_awal            =   $cekReturItem->qty;
                $tambahAbf->berat_awal          =   $cekReturItem->berat;
                $tambahAbf->qty_item            =   $cekReturItem->qty;
                $tambahAbf->berat_item          =   $cekReturItem->berat;
                $tambahAbf->status              =   1;

                // End insert

                //Log Delete Abf
                if ($cekAbf) {
                    $deleteAbfLog                   =   new Adminedit;
                    $deleteAbfLog->data             =   json_encode([
                        'data' => $cekAbf,
                    ]);
                    $deleteAbfLog->table_name       =   'abf';
                    $deleteAbfLog->activity         =   'abf';
                    $deleteAbfLog->status           =   1;
                    $deleteAbfLog->content          =   'Delete item Abf';
                    $deleteAbfLog->type             =   'delete';
                    $deleteAbfLog->table_id         =   $cekAbf->id;
                    $deleteAbfLog->user_id          =   Auth::user()->id;

                    // End Log
                }

                if ($cekChiller) {
                    // Log Delete Chiller
                    $deleteChillerLog              =   new Adminedit;
                    $deleteChillerLog->data        =   json_encode([
                        'data' => $cekChiller,
                    ]);
                    $deleteChillerLog->table_name  =   'chiller';
                    $deleteChillerLog->activity    =   'chiller';
                    $deleteChillerLog->status      =   1;
                    $deleteChillerLog->content     =   'Delete item chiller';
                    $deleteChillerLog->type        =   'Delete';
                    $deleteChillerLog->table_id    =   $cekChiller->id;
                    $deleteChillerLog->user_id     =   Auth::user()->id;
                    // End Log
                }

                // CASE JIKA ALOKASI REPROSES PRODUKSI
                if ($request->data == 'produksi') {

                    // Cek data Asal RETUR ITEM
                    if ($dataReturLama->penanganan == 'Reproses produksi') {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Silahkan pilih penanganan lain.',
                            'status'  => 'error'
                        ]);
                        return false;
                    } else

                        if ($dataReturLama->penanganan == 'Kembali ke Frezeer' || $dataReturLama->penanganan == 'Musnahkan') {


                        if ($dataReturLama->penanganan == 'Kembali ke Frezeer') {
                            if ($cekAbf) {
                                $cekProductGudang           = Product_gudang::where('table_name', 'abf')->where('table_id', $cekAbf->id)->first();
                                if (!$cekProductGudang) {
                                    // Log ABF sebelum dihapus
                                    if (!$deleteAbfLog->save()) {
                                        DB::rollBack();
                                        return response()->json([
                                            'message' => 'Gagal simpan Log delete data Abf.',
                                            'status'  => 'error'
                                        ]);
                                        return false;
                                    }
                                    // End Log

                                    // Try delete ABF
                                    if (!$cekAbf->delete()) {
                                        DB::rollBack();
                                        return response()->json([
                                            'message' => 'Tidak dapat ubah alokasi. Karena data Qty atau Berat sudah berbeda.',
                                            'status'  => 'error'
                                        ]);
                                        return false;
                                    }
                                } else {
                                    DB::rollBack();
                                    return response()->json([
                                        'message' => 'Tidak dapat ubah alokasi. Karena data sudah berada di CS.',
                                        'status'  => 'error'
                                    ]);
                                    return false;
                                }
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Tidak dapat ubah alokasi. Karena data Qty atau Berat sudah berbeda.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                            // ----------------------------------------------------------------
                        }
                        // Log tambah chiller dari retur;


                        if ($deletedItemChiller) {
                            if (!$deletedItemChiller->restore()) {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Data pada Chiller tidak dapat disimpan.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        } else {
                            if (!$tambahChiller->save()) {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Data pada Chiller tidak dapat disimpan.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        }
                    }

                    if ($cekItem) {
                        // Update Retur Item
                        if ($cekItem->category_id == 1) {
                            $cekReturItem->unit               =   "chillerbb";
                            $cekReturItem->tujuan             =   "chillerbb";
                            $cekReturItem->penanganan         =   "Reproses Produksi";
                        } else {
                            $cekReturItem->unit               =   "chillerfg";
                            $cekReturItem->tujuan             =   "chillerfg";
                            $cekReturItem->penanganan         =   "Reproses Produksi";
                        }
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Data Item tidak ditemukan.',
                            'status'  => 'error'
                        ]);
                        return false;
                    }



                    // CASE JIKA ALOKASI CHILLER FG
                } else if ($request->data == 'chillerfg') {

                    // Cek asal Retur
                    if ($dataReturLama->penanganan == 'Jual Sampingan') {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Silahkan pilih penanganan lain.',
                            'status'  => 'error'
                        ]);
                        return false;
                    } else

                        if ($dataReturLama->penanganan == 'Kembali ke Frezeer' || $dataReturLama->penanganan == 'Musnahkan') {
                        if ($dataReturLama->penanganan == 'Kembali ke Frezeer') {

                            if ($cekAbf) {
                                $cekProductGudang           = Product_gudang::where('table_name', 'abf')->where('table_id', $cekAbf->id)->first();
                                if (!$cekProductGudang) {
                                    // Log ABF sebelum dihapus
                                    if (!$deleteAbfLog->save()) {
                                        DB::rollBack();
                                        return response()->json([
                                            'message' => 'Gagal simpan Log delete data Abf.',
                                            'status'  => 'error'
                                        ]);
                                        return false;
                                    }
                                    // End Log

                                    // Try delete ABF
                                    if (!$cekAbf->delete()) {
                                        DB::rollBack();
                                        return response()->json([
                                            'message' => 'Tidak dapat ubah alokasi. Karena data Qty atau Berat sudah berbeda.',
                                            'status'  => 'error'
                                        ]);
                                        return false;
                                    }
                                } else {
                                    DB::rollBack();
                                    return response()->json([
                                        'message' => 'Tidak dapat ubah alokasi. Karena data sudah berada di CS.',
                                        'status'  => 'error'
                                    ]);
                                    return false;
                                }
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Tidak dapat ubah alokasi. Karena data Qty atau Berat sudah berbeda.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        }

                        // Log tambah chiller dari retur;

                        // End Log

                        if ($deletedItemChiller) {
                            if (!$deletedItemChiller->restore()) {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Data pada Chiller tidak dapat disimpan.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        } else {
                            if (!$tambahChiller->save()) {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Data pada Chiller tidak dapat disimpan.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        }
                    }

                    if ($cekItem) {
                        // Update Retur Item
                        $cekReturItem->unit               =   "chillerfg";
                        $cekReturItem->tujuan             =   "chillerfg";
                        $cekReturItem->penanganan         =   "Jual Sampingan";
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Data Item tidak ditemukan.',
                            'status'  => 'error'
                        ]);
                        return false;
                    }



                    // CASE JIKA ALOKASI ABF
                } else if ($request->data == 'gudang') {

                    if ($dataReturLama->penanganan == 'Kembali ke Frezeer') {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Silahkan pilih penanganan lain.',
                            'status'  => 'error'
                        ]);
                        return false;
                    } else if ($dataReturLama->penanganan == 'Reproses Produksi' || $dataReturLama->penanganan == 'Jual Sampingan') {


                        if ($cekChiller) {
                            // Log Chiller sebelum dihapus
                            if (!$deleteChillerLog->save()) {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Gagal simpan Log delete data Chiller.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }

                            // End Log

                            // Try delete Chiller
                            if (!$cekChiller->delete()) {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Data pada Chiller tidak dapat dihapus.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'message' => 'Tidak dapat ubah alokasi. Karena data Qty atau Berat sudah berbeda.',
                                'status'  => 'error'
                            ]);
                            return false;
                        }
                    }

                    if ($dataReturLama !== 'Kembali ke Frezeer') {

                        // Log tambah Abf dari retur;

                        // End log
                        if ($cekAbf) {
                            $cekProductGudang           = Product_gudang::where('table_name', 'abf')->where('table_id', $cekAbf->id)->first();
                            if (!$cekProductGudang) {
                                if (!$cekAbf->restore()) {
                                    DB::rollBack();
                                    return response()->json([
                                        'message' => 'Data pada ABF tidak dapat disimpan.',
                                        'status'  => 'error'
                                    ]);
                                    return false;
                                }
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Tidak dapat ubah alokasi. Karena data sudah berada di CS.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        } else {
                            if (!$tambahAbf->save()) {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Data pada ABF tidak dapat disimpan.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        }
                    }

                    if ($cekItem) {
                        // Update Retur Item
                        $cekReturItem->unit               =   "gudang";
                        $cekReturItem->tujuan             =   "gudang";
                        $cekReturItem->penanganan         =   "Kembali ke Frezeer";
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Data Item tidak ditemukan.',
                            'status'  => 'error'
                        ]);
                        return false;
                    }

                    // CASE JIKA ALOKASI MUSNAHKAN
                } else if ($request->data == 'musnahkan') {
                    if ($dataReturLama->penanganan == 'Musnahkan') {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Silahkan pilih penanganan lain.',
                            'status'  => 'error'
                        ]);
                        return false;
                    } else if ($dataReturLama->penanganan == 'Reproses Produksi' || $dataReturLama->penanganan == 'Jual Sampingan') {

                        if ($cekChiller) {
                            // Log Chiller sebelum dihapus
                            if (!$deleteChillerLog->save()) {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Gagal simpan Log delete data Chiller.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }

                            // End Log

                            // Try delete Chiller
                            if (!$cekChiller->delete()) {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Data pada Chiller tidak dapat dihapus.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'message' => 'Tidak dapat ubah alokasi. Karena data Qty atau Berat sudah berbeda.',
                                'status'  => 'error'
                            ]);
                            return false;
                        }
                    } else {

                        if ($cekAbf) {
                            $cekProductGudang           = Product_gudang::where('table_name', 'abf')->where('table_id', $cekAbf->id)->first();
                            if (!$cekProductGudang) {
                                // Log ABF sebelum dihapus
                                if (!$deleteAbfLog->save()) {
                                    DB::rollBack();
                                    return response()->json([
                                        'message' => 'Gagal simpan Log delete data Abf.',
                                        'status'  => 'error'
                                    ]);
                                    return false;
                                }
                                // End Log

                                // Try delete ABF
                                if (!$cekAbf->delete()) {
                                    DB::rollBack();
                                    return response()->json([
                                        'message' => 'Tidak dapat ubah alokasi. Karena data Qty atau Berat sudah berbeda.',
                                        'status'  => 'error'
                                    ]);
                                    return false;
                                }
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'message' => 'Tidak dapat ubah alokasi. Karena data sudah berada di CS.',
                                    'status'  => 'error'
                                ]);
                                return false;
                            }
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'message' => 'Tidak dapat ubah alokasi. Karena data Qty atau Berat sudah berbeda.',
                                'status'  => 'error'
                            ]);
                            return false;
                        }
                    }

                    if ($cekItem) {
                        // Update Retur Item
                        $cekReturItem->unit               =   "musnahkan";
                        $cekReturItem->tujuan             =   "musnahkan";
                        $cekReturItem->penanganan         =   "Musnahkan";
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'message' => 'Data Item tidak ditemukan.',
                            'status'  => 'error'
                        ]);
                        return false;
                    }
                }

                // Log detail retur item

                // End log

                // Update Retur item sesuai penanganan
                if (!$cekReturItem->save()) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Data tidak dapat diupdate.',
                        'status'  => 'error'
                    ]);
                    return false;
                }


                // Log Pindah Penanganan (Detail Retur Item)
                $penangananLog                  =   new Adminedit;
                $penangananLog->data            =   json_encode([
                    'data' => $cekReturItem,
                ]);
                $penangananLog->table_name      =   'retur_item';
                $penangananLog->activity        =   'retur';
                $penangananLog->status          =   1;
                $penangananLog->content         =   'Edit penanganan';
                $penangananLog->type            =   'edit';
                $penangananLog->table_id        =   $cekReturItem->id;
                $penangananLog->user_id         =   Auth::user()->id;
                // End Log

                if (!$penangananLog->save()) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Gagal simpan Log retur item.',
                        'status'  => 'error'
                    ]);
                    return false;
                }

                DB::commit();
                return response()->json([
                    'message' => 'Data berhasil diupdate.',
                    'data'    => $cekReturItem,
                    'status'  => 'success',
                    'idcust' => $dataReturLama
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Data tidak ditemukan.',
                    'status'  => 'error'
                ]);
                return false;
            }
        }else if($request->key == 'editAlasan'){
            $alasan = Returalasan::where('id', $request->id)->first();
            $alasan->nama = $request->nama;
            $alasan->kelompok = $request->kelompok;
            $alasan->save();
            return back()->with('status', 1)->with('message', 'Edit alasan berhasil');
        }else {

            $item   =   ReturItem::find($request->idcustomer);

            $item->qty              =   $request->qty;
            $item->berat            =   $request->berat;
            $item->unit             =   $request->tujuan;
            $item->rate             =   $request->harga;
            $item->status           =   1;
            $item->save();

            return back()->with('status', 1)->with('message', 'Berhasil Simpan');
        }
    }

    public function tanggal(Request $request)
    {
        return  Order::whereDate('tanggal_kirim', $request->tanggal ?? date('Y-m-d'))->get()->makeHidden(['nomor_invoice', 'status_order']);
    }


    public function returmeyer(Request $request)
    {
        if ($request->key == 'view') {
            $data   =   Order::whereDate('tanggal_kirim', $request->tanggal ?? date('Y-m-d'))
                ->where('nama', 'like', '%meyer%')
                ->get();

            return view('admin.pages.retur.meyer.data', compact('data'));
        } else {
            return view('admin.pages.retur.meyer.index');
        }
    }


    public function returByCustomer(Request $request)
    {
        $data   =   Order::where('id', $request->customer_id)
            ->whereDate('tanggal_kirim', $request->tanggal ?? date('Y-m-d'))
            ->first();
        $month  = date('m', strtotime($data->tanggal_kirim));
        $year   = date('Y', strtotime($data->tanggal_kirim));

        foreach ($data->daftar_order_full as $i => $row) {
            $cekAbf = Abf::where('item_id', $row->item_id)
                            ->whereBetween('tanggal_masuk',[$data->tanggal_kirim,$data->tanggal_kirim])
                            ->where('status_cutoff',1)
                            ->count();

            //Chiller
            $cekChillerAbf = Chiller::where('item_id', $row->item_id)
                                    ->where('kategori',1)
                                    ->where('status_cutoff',1)
                                    ->whereBetween('tanggal_produksi',[$data->tanggal_kirim,$data->tanggal_kirim])
                                    ->count();
            $cekChiller = Chiller::where('item_id', $row->item_id)
                                    ->where('status_cutoff',1)
                                    ->whereBetween('tanggal_produksi',[$data->tanggal_kirim,$data->tanggal_kirim])
                                    ->count();
        }
        // dd($cekAbf);

        if ($data) {
            return view('admin.pages.retur.order-customer', compact('data','cekAbf','cekChillerAbf','cekChiller'));
        } else {
            return "<h3 class='text-center'>Data Tidak Ditemukan</h3>";
        }
    }

    public function customer()
    {
        $data   =   Retur::where('status', 1)->get();
        $customer           =   Customer::where('nama', '!=', '')
            ->where('netsuite_internal_id', '!=', NULL)
            ->orderBy('nama')->get();
        return view('admin.pages.retur.customer', compact('data', 'customer'));
    }

    public function deletecus(Request $request)
    {
        $data   =   Retur::find($request->id);

        foreach (ReturItem::where('retur_id', $data->id)->get() as $row) {
            if ($row->orderitem_id) {
                $orderitem                      =   OrderItem::find($row->orderitem_id);
                $orderitem->fulfillment_qty     =   $orderitem->fulfillment_qty + $row->qty;
                $orderitem->fulfillment_berat   =   $orderitem->fulfillment_berat + $row->berat;
                $orderitem->save();
            }
        }

        $item   =   ReturItem::where('retur_id', $data->id)->delete();

        $data->delete();
    }

    public function returDetail(Request $request, $id)
    {
        $data       =   Retur::find($id);
        if ($data) {
            $returitem  =   ReturItem::where('retur_id', $data->id)->first();
            $orderitem  =   OrderItem::find($returitem->orderitem_id);
            $item       =   Item::whereIn('category_id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20])->get();
            $sopir      =   Driver::where('driver_kirim', 1)->get();
            $alasan     =   Returalasan::get();

            $netsuite   =   '';
            if ($data->status == 2) {
                $netsuite_fulfill   =   Netsuite::where('tabel', 'retur')
                    ->where('tabel_id', $data->id)
                    // ->whereDate('created_at', $data->created_at)
                    ->orderBy('id', 'DESC')
                    ->get();

                $netsuite           =   $netsuite_fulfill->all();
            }

            return view('admin.pages.retur.retur-detail', compact('data', 'item', 'orderitem', 'sopir', 'netsuite', 'alasan'));
        }
        return redirect()->route('retur.index');
    }

    public function deleteitem(Request $request)
    {
        $item   =   ReturItem::find($request->id);
        if ($item->orderitem_id) {
            $orderitem                      =   OrderItem::find($item->orderitem_id);
            $orderitem->fulfillment_qty     =   $orderitem->fulfillment_qty + $item->qty;
            $orderitem->fulfillment_berat   =   $orderitem->fulfillment_berat + $item->berat;
            $orderitem->save();
        }

        $item->delete();
    }

    public function itemretur()
    {
        $retur  =   Retur::where('status', 1)->first();
        $data   =   ReturItem::where('status', 1)->get();
        $item       =   Item::all();
        return view('admin.pages.retur.itemretur', compact('data', 'retur', 'item'));
    }

    public function retursummarylist(Request $request)
    {
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggal_akhir  =   $request->akhir ?? date('Y-m-d');
        $kata           =   $request->kata ?? '';
        $retur_list     =   ReturItem::whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
            ->where(function ($query2) use ($request, $kata) {
                if ($request->tujuan != "") {
                    $query2->orWhere('unit', $request->tujuan);
                    $query2->orWhere('tujuan', $request->tujuan);
                }

                if ($kata != "") {
                    $query2->orWhere('catatan', 'like', "%" . $kata . "%");
                    $query2->orWhere('tujuan', 'like', '%' . $kata . '%');
                    $query2->orWhere('retur_item.kategori', 'like', '%' . $kata . '%');
                    $query2->orWhere('satuan', 'like', '%' . $kata . '%');
                    $query2->orWhere('penanganan', 'like', '%' . $kata . '%');
                    $query2->orWhere('retur.no_so', 'like', "%" . $kata . "%");
                    $query2->orWhere('retur.no_ra', 'like', '%' . $kata . '%');
                    $query2->orWhere('customers.nama', 'like', '%' . $kata . '%');
                }

                if ($request->kategori != "") {
                    $query2->orWhere('kategori', $request->kategori);
                }

                if ($request->satuan != "") {
                    $query2->orWhere('satuan', $request->satuan);
                }

                if ($request->penanganan != "") {
                    $query2->orWhere('penanganan', 'like', '%' . $request->penanganan . '%');
                }
            })
            ->select('retur_item.*','customers.id as customer_id','retur.id as id_retur','retur.status as status_retur','retur.tanggal_retur as tanggalretur','orders.no_so as nomer_so','orders.tanggal_kirim as tanggal_kirim','retur.no_ra')
            ->leftjoin('retur', 'retur.id', '=', 'retur_item.retur_id')
            ->leftjoin('order_items', 'order_items.id', '=', 'retur_item.orderitem_id')
            ->leftjoin('orders','orders.id','=','order_items.order_id')
            ->leftjoin('customers', 'retur.customer_id', '=', 'customers.id')
            ->orderBy('retur.id', 'desc')
            ->whereIn('retur.status', [1, 2]);


        $master                     = clone $retur_list;
        $retur_list                 = $master->get();

        $cloneReturItem             = clone $master;
        $dataCloneMaster            = $cloneReturItem->whereNotNull('orderitem_id')->groupBy('orderitem_id')->get();
        $ReturOrderItems            = [];

        foreach($dataCloneMaster as $valueRetur){
            $ReturOrderItems[]      = $valueRetur->orderitem_id;
        }

        // STORE DATA ORDER ITEM
        $newDataRetur               = $ReturOrderItems;
        $stringDataOrderItem2       = implode(",",$newDataRetur);
        $ReturOrderItem2            = [];
        if($stringDataOrderItem2){
            $ReturOrderItem2        = OrderItem::whereRaw("id IN(".$stringDataOrderItem2.")")->get();
        }
        $totalQty                               = 0;
        $totalBerat                             = 0;
        $totalAllQtyFulFill                     = 0;
        $totalFulFillQty                        = 0;
        $totalPersenQty                         = 0;
        if($ReturOrderItems){
            $totalQty                           = $retur_list->sum('qty');
            $totalBerat                         = $retur_list->sum('berat');
            $totalFulFillQty                    = $ReturOrderItem2->sum('fulfillment_qty');
            $totalPersenQty                     = $totalQty / $totalFulFillQty * 100;
        }
        $totalReturQty                          = number_format($totalQty,2);
        $totalReturBerat                        = number_format($totalBerat,2);
        $totalAllQtyFulFill                     = number_format($totalFulFillQty,2);
        $totalQtyDO                             = number_format($totalFulFillQty, 2);
        $totalqtypercentage                     = number_format($totalPersenQty, 2);

        if ($request->cetak == true) {
            $download = true;
            return view('admin.pages.retur.retur-summary-list', compact('retur_list', 'tanggal', 'download','totalReturQty','totalReturBerat','totalqtypercentage','totalAllQtyFulFill'));
        } else {
            $download = true;
            return view('admin.pages.retur.retur-summary-list', compact('retur_list', 'tanggal', 'download','totalReturQty','totalReturBerat','totalqtypercentage','totalAllQtyFulFill'));
        }
    }

    public function retursummary(Request $request)
    {
        $tanggal        =   $request->tanggal ?? date('Y-m-d');
        $tanggal_akhir  =   $request->akhir ?? date('Y-m-d');
        $akhir          =   $request->akhir ?? date('Y-m-d');
        $kata           =   $request->kata ?? '';
        $alasan         =   $request->alasan ?? '';
        $retur          =   Retur::with(['to_itemretur', 'to_customer', 'to_netsuite', 'getItemTukarRetur', 'to_order'])->select('retur.*')
                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                            ->where(function ($query) use ($request, $alasan) {
                                if ($request->customer) {
                                    if (is_numeric($request->customer)) {
                                        $query->where('customer_id', $request->customer);
                                    }
                                }
                                if ($alasan) {
                                    $query->orWhere('retur_item.catatan', 'like', '%' . $alasan . '%');
                                }
                            })
                            // ->orderBy('tanggal_retur', 'ASC');
                            ->orderBy('created_at', 'ASC');

        if ($request->key == 'customer') {
            return view('admin.pages.retur.customer_select', compact('retur', 'request'));
        } else if ($request->key == 'exportcsv') {
            return view('admin.pages.retur.exportcsv', compact('tanggal', 'tanggal_akhir'));
        } else if ($request->type == "peritem") {
            $dataReturExport = collect();

            $dataReturExportFresh =   Retur::select('retur.*', 'items.nama as nama_item', 'retur_item.item_id as id_item', 'retur_item.berat as berat_item', 'retur.tanggal_retur as tgl_retur', 'items.category_id AS kategori_item')
                                        ->join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                        ->whereIn('retur.status', [1, 2])
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->where('retur.no_ra', '!=', NULL)
                                        ->where('items.nama', 'NOT LIKE', '%FROZEN%')
                                        ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['4']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->groupBy('retur_item.item_id')
                                        ->orderBy('category_id', 'ASC')
                                        ->get();

            $dataReturExportFrozen =   Retur::select('retur.*', 'items.nama as nama_item', 'retur_item.item_id as id_item', 'retur_item.berat as berat_item', 'retur.tanggal_retur as tgl_retur', 'items.category_id AS kategori_item')
                                        ->join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                        ->whereIn('retur.status', [1, 2])
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->where('retur.no_ra', '!=', NULL)
                                        ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->where('items.nama', 'LIKE', '%FROZEN%')
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['10', '16']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->groupBy('retur_item.item_id')
                                        ->orderBy('category_id', 'ASC')
                                        ->get();

            foreach ($dataReturExportFresh as $fresh)
            $dataReturExport->push($fresh);

            foreach ($dataReturExportFrozen as $frozen)
            $dataReturExport->push($frozen);

            $countTotalKualitas     = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                        ->select('catatan')
                                        ->whereIn('retur.status', [1, 2])
                                        ->where('retur.no_ra', '!=', NULL)
                                        ->whereIn('kategori', ['Kualitas', 'kualitas'])
                                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['4', '10', '16']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->groupBy('catatan')
                                        ->get();

            $countTotalNonKualitas  = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')->select('catatan')
                                        ->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)
                                        ->whereIn('kategori', ['Non Kualitas', 'nonkualitas'])
                                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['4', '10', '16']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->groupBy('catatan')
                                        ->get();


            $countPenanganan        = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->select('penanganan')->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)
                                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['4', '10', '16']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->groupBy('penanganan')
                                        ->get();

            $getcatatan             = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                        ->select('catatan')->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['4', '10', '16']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->orderBy('retur_item.kategori')
                                        ->groupBy('catatan')
                                        ->get('catatan');

            $totalPenanganan        = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                        ->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)
                                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['4', '10', '16']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->groupBy('catatan')
                                        ->get('catatan');

            $totalReturKualitas     = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                        ->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)
                                        ->whereIn('kategori', ['Kualitas', 'kualitas'])
                                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['4', '10', '16']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->sum('berat');

            $totalReturNonKualitas  = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                        ->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)
                                        ->whereIn('kategori', ['Non Kualitas', 'nonkualitas'])
                                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['4', '10', '16']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->sum('berat');

            $totalRetur             = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                        ->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)
                                        ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                        ->join('items', 'retur_item.item_id', '=', 'items.id')
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                $query->whereIn('items.category_id', ['4', '10', '16']);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                            }
                                        })
                                        ->sum('berat');

            $totalPengiriman        = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                                        ->join('items', 'order_items.item_id', 'items.id')
                                        ->where('orders.status', 10)
                                        ->where(function($query) use ($request) {
                                            if ($request->jenisitem == 'sampingan') {
                                                // $query->whereIn('items.category_id', ['10', '16']);
                                                $query->whereIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                $query->whereNotIn(
                                                    'nama_detail',
                                                    [
                                                        "AMPELA BERSIH BROILER",
                                                        "AY - S",
                                                        "HATI AMPELA BERSIH BROILER",
                                                        "HATI AMPELA KOTOR BROILER",
                                                        "HATI AMPELA KOTOR BROILER FROZEN",
                                                        "HATI BERSIH BROILER",
                                                        "KAKI BERSIH BROILER",
                                                        "KAKI KOTOR BROILER",
                                                        "KEPALA LEHER BROILER",
                                                        "USUS BROILER",
                                                        "TEMBOLOK"
                                                    ]
                                                );
                                                // $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                $query->whereNotIn('sales_channel', ["By Product - Paket", "By Product - Retail"]);
                                            }
                                        })
                                        ->whereBetween('orders.tanggal_kirim', [$tanggal, $tanggal_akhir])
                                        ->sum('order_items.fulfillment_berat');

            // dd($getcatatan, $countPenanganan);
            // FRESH //
            $totalReturFresh         = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['4']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereIn('retur.status', [1, 2])
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                                ->where('items.nama', 'NOT LIKE', '%FROZEN%')
                                                ->sum('retur_item.berat');

            $totalKualitasFresh     = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['4']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereIn('retur.status', [1, 2])
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->whereIn('kategori', ['Kualitas', 'kualitas'])
                                                ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                                ->where('items.nama', 'NOT LIKE', '%FROZEN%')
                                                ->sum('retur_item.berat');

            $totalNonKualitasFresh  = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['4']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereIn('retur.status', [1, 2])
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->whereIn('kategori', ['Non Kualitas', 'nonkualitas'])
                                                ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                                ->where('items.nama', 'NOT LIKE', '%FROZEN%')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['4']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->sum('retur_item.berat');

            $totalPengirimanFresh   = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                                                ->join('items', 'order_items.item_id', 'items.id')
                                                ->whereBetween('orders.tanggal_kirim', [$tanggal, $tanggal_akhir])
                                                ->where('items.nama', 'NOT LIKE', '%FROZEN%')
                                                ->whereNotIn('items.category_id',[54])
                                                ->where('orders.status', 10)
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                        $query->whereIn('items.category_id',[4,6,10,16]);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id',[4,6,10,16]);
                                                        $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                    }else{
                                                        $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                    }
                                                })
                                                ->sum('order_items.fulfillment_berat');

            $totalPengirimanFrozen   = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                                                ->join('items', 'order_items.item_id', 'items.id')
                                                ->whereBetween('orders.tanggal_kirim', [$tanggal, $tanggal_akhir])
                                                ->where('items.nama', 'LIKE', '%FROZEN%')
                                                ->whereNotIn('items.category_id',[54])
                                                ->where('orders.status', 10)
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        // $query->whereIn('items.category_id', ['10', '16']);
                                                        $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                        $query->whereIn('items.category_id',[4,6,10,16]);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id',[4,6,10,16]);
                                                    }else{
                                                        $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                    }
                                                })
                                                ->sum('order_items.fulfillment_berat');

            // FROZEN //
            $totalKualitasFrozen    = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['10', '16']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereIn('retur.status', [1, 2])
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->whereIn('kategori', ['Kualitas', 'kualitas'])
                                                ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                                ->where('items.nama', 'LIKE', '%FROZEN%')
                                                ->sum('retur_item.berat');

            $totalNonKualitasFrozen = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['10', '16']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereIn('retur.status', [1, 2])
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->whereIn('kategori', ['Non Kualitas', 'nonkualitas'])
                                                ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                                ->where('items.nama', 'LIKE', '%FROZEN%')
                                                // ->select(DB::raw('Sum(retur_item.berat) as berat'))
                                                ->sum('retur_item.berat');

            $totalReturFrozen       = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['10', '16']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereIn('retur.status', [1, 2])
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])
                                                ->where('items.nama', 'LIKE', '%FROZEN%')
                                                ->sum('retur_item.berat');

            
            $dataTotalBawah       =   [
                'returTotKualitasfresh' =>  $totalReturFresh,
                'totKualitasfresh'      =>  $totalKualitasFresh,
                'totNonKualitasfresh'   =>  $totalNonKualitasFresh,
                'totPengirimanfresh'    =>  $totalPengirimanFresh,

                'returTotKualitasfrozen'=>  $totalReturFrozen,
                'totKualitasfrozen'     =>  $totalKualitasFrozen,
                'totNonKualitasfrozen'  =>  $totalNonKualitasFrozen,
                'totPengirimanfrozen'   =>  $totalPengirimanFrozen,

                'totalkualitas'         =>  $totalKualitasFresh + $totalKualitasFrozen,
                'totalNonkualitas'      =>  $totalNonKualitasFrozen + $totalNonKualitasFresh,
                'totalretur'            =>  $totalReturFresh + $totalReturFrozen,
                'totalpengiriman'       =>  $totalPengirimanFresh + $totalPengirimanFrozen,

                'prsReturKualitasfresh' =>  ($totalKualitasFresh / ($totalPengirimanFresh ?:1)) * 100,
                'prsReturKualitasfrozen' =>  ($totalKualitasFrozen / ($totalPengirimanFrozen?:1)) * 100,

                'prsRetrNonKualitasfresh'    => ($totalNonKualitasFresh / ($totalPengirimanFresh?:1)) *100,
                'prsRetrNonKualitasfrozen'   => ($totalNonKualitasFrozen / ($totalPengirimanFrozen?:1)) *100,

                'prsReturFresh'         => $totalReturFresh/($totalPengirimanFresh?:1) *100,
                'prsReturFrozen'        => $totalReturFrozen/($totalPengirimanFrozen?:1) *100,

                'persentaseReturTotalFreshK' => (($totalKualitasFresh + $totalKualitasFrozen) / ($totalPengirimanFresh + $totalPengirimanFrozen)) * 100,
                'persentaseReturTotalFreshNK' => (($totalNonKualitasFrozen + $totalNonKualitasFresh) / ($totalPengirimanFresh + $totalPengirimanFrozen)) * 100,
                'persentaseReturTotal' => (($totalReturFresh + $totalReturFrozen) / ($totalPengirimanFresh + $totalPengirimanFrozen)) * 100,
            ];
            // dd($getcatatan, $countPenanganan);

            // persentase retur
            $data = [];
            for ($no = 0; $no < 4; $no++) {
                $tanggal_awal       = date('Y-m-d', strtotime("-6 Day", strtotime($akhir)));
                // $tanggal_akhir      = $akhir;

                $mulai              = $tanggal_awal;
                // $akhir              = $tanggal_akhir;

                $kualitas                   = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                                ->whereIn('retur.status', [1, 2])
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['4', '10', '16']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereBetween('retur.tanggal_retur', [$mulai, $akhir])
                                                ->where('retur_item.kategori', 'Kualitas')
                                                ->select(DB::raw('Sum(retur_item.berat) as berat'))->first();

                $nonkualitas                = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                                ->whereIn('retur.status', [1, 2])
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['4', '10', '16']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereBetween('retur.tanggal_retur', [$mulai, $akhir])
                                                ->where('retur_item.kategori', 'Non Kualitas')
                                                ->select(DB::raw('Sum(retur_item.berat) as berat'))->first();
                //FRESH
                $kualitasfresh              = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['4']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereBetween('retur.tanggal_retur', [$mulai, $akhir])
                                                ->where('retur_item.kategori', 'Kualitas')
                                                ->where('items.nama', 'NOT LIKE', '%FROZEN%')
                                                ->select(DB::raw('Sum(retur_item.berat) as berat'))
                                                ->first();

                $nonkualitasfresh           = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['4']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereBetween('retur.tanggal_retur', [$mulai, $akhir])
                                                ->where('retur_item.kategori', 'Non Kualitas')
                                                ->where('items.nama', 'NOT LIKE', '%FROZEN%')
                                                ->select(DB::raw('Sum(retur_item.berat) as berat'))
                                                ->first();

                $gtotalpengirimanfresh      =   OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                                                        ->join('items', 'order_items.item_id', 'items.id')
                                                        ->whereBetween('orders.tanggal_kirim', [$mulai, $akhir])
                                                        ->where('items.nama', 'NOT LIKE', '%FROZEN%')
                                                        ->whereNotIn('items.category_id',[54])
                                                        ->where('orders.status', 10)
                                                        ->where(function($query) use ($request) {
                                                            if ($request->jenisitem == 'sampingan') {
                                                                $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                                $query->whereIn('items.category_id',[4,6,10,16]);
                                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                                $query->whereNotIn('items.category_id',[4,6,10,16]);
                                                                $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                            }else{
                                                                $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                            }
                                                        })
                                                        ->sum('order_items.fulfillment_berat');
                $gtotalpengirimanfrozen     =   OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
                                                        ->join('items', 'order_items.item_id', 'items.id')
                                                        ->whereBetween('orders.tanggal_kirim', [$mulai, $akhir])
                                                        ->where('items.nama', 'LIKE', '%FROZEN%')
                                                        ->whereNotIn('items.category_id',[54])
                                                        ->where('orders.status', 10)
                                                        ->where(function($query) use ($request) {
                                                            if ($request->jenisitem == 'sampingan') {
                                                                // $query->whereIn('items.category_id', ['10', '16']);
                                                                $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                                $query->whereIn('items.category_id',[4,6,10,16]);
                                                            } else if ($request->jenisitem == 'nonsampingan') {
                                                                $query->whereNotIn('items.category_id',[4,6,10,16]);
                                                            }else{
                                                                $query->whereNotIn('sales_channel', ["By Product - Paket"]);
                                                            }
                                                        })
                                                        ->sum('order_items.fulfillment_berat');

                //FROZEN
                $kualitasfrozen             = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['10', '16']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereBetween('retur.tanggal_retur', [$mulai, $akhir])
                                                ->where('retur_item.kategori', 'Kualitas')
                                                ->where('items.nama', 'LIKE', '%FROZEN%')
                                                ->select(DB::raw('Sum(retur_item.berat) as berat'))
                                                ->first();


                $nonkualitasfrozen          = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                                ->where('retur.no_ra', '!=', NULL)
                                                ->join('items', 'retur_item.item_id', 'items.id')
                                                ->where(function($query) use ($request) {
                                                    if ($request->jenisitem == 'sampingan') {
                                                        $query->whereIn('items.category_id', ['10', '16']);
                                                    } else if ($request->jenisitem == 'nonsampingan') {
                                                        $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                    }
                                                })
                                                ->whereBetween('retur.tanggal_retur', [$mulai, $akhir])
                                                ->where('retur_item.kategori', 'Non Kualitas')
                                                ->where('items.nama', 'LIKE', '%FROZEN%')
                                                ->select(DB::raw('Sum(retur_item.berat) as berat'))
                                                ->first();


                

                $report             = array(
                    'tanggal'           => date('d M Y', strtotime($mulai)) . "-" . date('d M Y', strtotime($akhir)),
                    'totalretur'        => $totalRetur,
                    'kualitas'          => $kualitas->berat,
                    'nonkualitas'       => $nonkualitas->berat,
                    'kualitasfresh'     => $kualitasfresh->berat,
                    'nonkualitasfresh'  => $nonkualitasfresh->berat,
                    'totkirimfresh'     => $gtotalpengirimanfresh,
                    'kualitasfrozen'    => $kualitasfrozen->berat,
                    'nonkualitasfrozen' => $nonkualitasfrozen->berat,
                    'totkirimfrozen'    => $gtotalpengirimanfrozen,
                    'totalpengiriman'   => $totalPengiriman
                );
                $data[]                 = $report;
                $akhir                  = date('Y-m-d', strtotime("-1 Day", strtotime($tanggal_awal)));
            }
            $date_range             = $data;

            // grafik persentase retur
            $total_kualitas     = [];
            $total_non_kualitas = [];
            $tanggal_per_minggu       = [];
            foreach ($date_range as $row) {
                if ($row['totalpengiriman']) {
                    $total_kualitas[]     =  round(((int)$row['kualitas'] / (int)$row['totalpengiriman']) * 100, 2);
                    $total_non_kualitas[] =  round(((int)$row['nonkualitas'] / (int)$row['totalpengiriman']) * 100, 2);
                    $tanggal_per_minggu[] =  $row['tanggal'];
                }
            }
            $tgl_mingguan   = json_encode($tanggal_per_minggu);
            $alokasi        = "[{name: 'kualitas',data: ";
            $alokasi    .=  json_encode($total_kualitas);
            $alokasi    .=  "}, {name: 'non kualitas',data: ";
            $alokasi    .=  json_encode($total_non_kualitas);
            $alokasi    .=  "}]";


            $jenisitem = $request->jenisitem;
            if ($request->key == 'download') {
                $download = true;
                return view('admin.pages.retur.exportcsv-retur.exportPerItem', compact('dataReturExport', 'tanggal', 'akhir', 'tanggal_akhir', 'download', 'countTotalKualitas', 'countTotalNonKualitas', 'countPenanganan', 'getcatatan','date_range', 'tgl_mingguan', 'alokasi','dataTotalBawah', 'jenisitem'));
            } else {
                $download = false;
                return view('admin.pages.retur.exportcsv-retur.exportPerItem', compact('dataReturExport', 'tanggal', 'akhir', 'tanggal_akhir', 'download', 'countTotalKualitas', 'countTotalNonKualitas', 'countPenanganan', 'getcatatan','date_range', 'tgl_mingguan', 'alokasi','dataTotalBawah', 'jenisitem'));
                // return view('admin.pages.retur.exportcsv-retur.exportPerItem', compact('dataReturExport','tanggal','tanggal_akhir', 'download','total_memar','grand_total_memar','grand_total_warna','grand_total_bau','grand_total_kotor','grand_total_patah','grand_brg_batal','grand_brg_lebih','grand_brg_tdk_sesuai','grand_brg_slh_kemasan','countTotalKualitas','countTotalNonKualitas'));
            }
        } else if ($request->type == "percustomer") {
            $jenisitem                  =   $request->jenisitem;
            $exportPerCustomer          =   Retur::select('retur.*', DB::raw("SUM(retur_item.qty) AS qty"), DB::raw("SUM(retur_item.berat) AS berat"))
                                            ->where('retur.no_ra', '!=', 'NULL')
                                            ->whereBetween('retur_item.created_at', [$tanggal . " 00:00:01", $tanggal_akhir . " 23:59:59"])
                                            ->join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                            ->join('items', 'retur_item.item_id', 'items.id')
                                            ->where(function($query) use ($request) {
                                                if ($request->jenisitem == 'sampingan') {
                                                    $query->whereIn('items.category_id', ['4', '10', '16']);
                                                } else if ($request->jenisitem == 'nonsampingan') {
                                                    $query->whereNotIn('items.category_id', ['4', '10', '16']);
                                                }
                                            })
                                            ->whereIn('retur.status', [1, 2])
                                            ->orderBy('retur_item.created_at', 'ASC')
                                            ->groupBy('customer_id')
                                            ->get();

            if ($request->key == 'download') {
                $download = true;
                return view('admin.pages.retur.exportcsv-retur.exportPerCustomer', compact('tanggal', 'tanggal_akhir', 'download', 'exportPerCustomer', 'jenisitem'));
            } else {
                $download = false;
                return view('admin.pages.retur.exportcsv-retur.exportPerCustomer', compact('tanggal', 'tanggal_akhir', 'download', 'exportPerCustomer', 'jenisitem'));
            }
        } else if ($request->type == "itemfresh") {
            $pengurangan    = 6;
            $data           = [];

            for ($no = 0; $no < 4; $no++) {
                $tanggal_akhir              = $akhir;
                $tanggal_awal               = date('Y-m-d', strtotime("-6 Day", strtotime($akhir)));

                $mulai                      = $tanggal_awal;
                $akhir                      = $tanggal_akhir;

                $kualitas                   = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')->where('retur.no_ra', '!=', NULL)->join('items', 'retur_item.item_id', 'items.id')->whereBetween('retur.tanggal_retur', [$mulai, $akhir])->where('retur_item.kategori', 'Kualitas')->where('items.nama', 'NOT LIKE', '%FROZEN%')->select(DB::raw('Sum(retur_item.berat) as berat'))->first();
                $nonkualitas                = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')->where('retur.no_ra', '!=', NULL)->join('items', 'retur_item.item_id', 'items.id')->whereBetween('retur.tanggal_retur', [$mulai, $akhir])->where('retur_item.kategori', 'Non Kualitas')->where('items.nama', 'NOT LIKE', '%FROZEN%')->select(DB::raw('Sum(retur_item.berat) as berat'))->first();
                $totalpengiriman            = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')->join('items', 'order_items.item_id', 'items.id')->whereBetween('orders.tanggal_kirim', [$mulai, $akhir])->where('items.nama', 'NOT LIKE', '%FROZEN%')->sum('order_items.fulfillment_berat');
                $summary = array(
                    'tanggal'               => date('d M Y', strtotime($mulai)) . " - " . date('d M Y', strtotime($akhir)),
                    'kualitas'              => $kualitas->berat,
                    'nonkualitas'           => $nonkualitas->berat,
                    'totalpengiriman'       => $totalpengiriman
                );

                $data[]                     = $summary;
                $akhir                      = date('Y-m-d', strtotime("-1 Day", strtotime($tanggal_awal)));
            }

            $data_range                     = $data;
            if ($request->key == 'download') {
                $download = true;
                return view('admin.pages.retur.exportcsv-retur.exportItemFresh', compact('download', 'data_range'));
            } else {
                $download = false;
                return view('admin.pages.retur.exportcsv-retur.exportItemFresh', compact('download', 'data_range'));
            }
        } else if ($request->type == "itemfrozen") {
            $pengurangan    = 6;
            $data           = [];

            for ($no = 0; $no < 4; $no++) {
                $tanggal_akhir              = $akhir;
                $tanggal_awal               = date('Y-m-d', strtotime("-6 Day", strtotime($akhir)));

                $mulai                      = $tanggal_awal;
                $akhir                      = $tanggal_akhir;

                $kualitas                   = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')->where('retur.no_ra', '!=', NULL)->join('items', 'retur_item.item_id', 'items.id')->whereBetween('retur.tanggal_retur', [$mulai, $akhir])->where('retur_item.kategori', 'Kualitas')->where('items.nama', 'LIKE', '%FROZEN%')->select(DB::raw('Sum(retur_item.berat) as berat'))->first();
                $nonkualitas                = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')->where('retur.no_ra', '!=', NULL)->join('items', 'retur_item.item_id', 'items.id')->whereBetween('retur.tanggal_retur', [$mulai, $akhir])->where('retur_item.kategori', 'Non Kualitas')->where('items.nama', 'LIKE', '%FROZEN%')->select(DB::raw('Sum(retur_item.berat) as berat'))->first();
                $totalpengiriman            = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')->join('items', 'order_items.item_id', 'items.id')->whereBetween('orders.tanggal_kirim', [$mulai, $akhir])->where('items.nama', 'LIKE', '%FROZEN%')->sum('order_items.fulfillment_berat');
                $summary = array(
                    'tanggal'               => date('d M Y', strtotime($mulai)) . " - " . date('d M Y', strtotime($akhir)),
                    'kualitas'              => $kualitas->berat,
                    'nonkualitas'           => $nonkualitas->berat,
                    'totalpengiriman'       => $totalpengiriman
                );

                $data[]                     = $summary;
                $akhir                      = date('Y-m-d', strtotime("-1 Day", strtotime($tanggal_awal)));
            }

            $data_range                     = $data;

            if ($request->key == 'download') {
                $download = true;
                return view('admin.pages.retur.exportcsv-retur.exportItemFrozen', compact('data_range', 'download'));
            } else {
                $download = false;
                return view('admin.pages.retur.exportcsv-retur.exportItemFrozen', compact('data_range', 'download'));
            }
        }else if ($request->type == "perkategori") {
                $dataReturExport = collect();
                $dataReturExportFresh =   Retur::select('retur.*', 'items.nama as nama_item', 'retur_item.item_id as id_item', 'retur_item.berat as berat_item', 'retur.tanggal_retur as tgl_retur', 'items.category_id AS kategori_item')
                                            ->join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                            ->join('items', 'retur_item.item_id', '=', 'items.id')
                                            ->where('retur.no_ra', '!=', NULL)
                                            ->whereIn('retur.status', [1, 2])
                                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                                            ->where('items.nama', 'NOT LIKE', '%FROZEN%')
                                            ->groupBy('category_id')
                                            ->orderBy('category_id', 'ASC')
                                            ->get();

                $dataReturExportFrozen =   Retur::select('retur.*', 'items.nama as nama_item', 'retur_item.item_id as id_item', 'retur_item.berat as berat_item', 'retur.tanggal_retur as tgl_retur', 'items.category_id AS kategori_item')
                                            ->join('retur_item', 'retur.id', '=', 'retur_item.retur_id')
                                            ->join('items', 'retur_item.item_id', '=', 'items.id')
                                            ->where('retur.no_ra', '!=', NULL)
                                            ->whereIn('retur.status', [1, 2])
                                            ->whereBetween('tanggal_retur', [$tanggal, $tanggal_akhir])
                                            ->where('items.nama', 'LIKE', '%FROZEN%')
                                            ->groupBy('category_id')
                                            ->orderBy('category_id', 'ASC')
                                            ->get();

                foreach ($dataReturExportFresh as $fresh)
                $dataReturExport->push($fresh);

                foreach ($dataReturExportFrozen as $frozen)
                $dataReturExport->push($frozen);

                $countTotalKualitas     = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')->select('catatan')->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)->whereIn('kategori', ['Kualitas', 'kualitas'])->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])->groupBy('catatan')->get();
                $countTotalNonKualitas  = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')->select('catatan')->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)->whereIn('kategori', ['Non Kualitas', 'nonkualitas'])->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])->groupBy('catatan')->get();
                $countPenanganan        = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')->select('penanganan')->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])->groupBy('penanganan')->get();
                $getcatatan             = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')->select('catatan')->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])->orderBy('retur_item.kategori')->groupBy('catatan')->get('catatan');

                $totalReturKualitas     = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')->where('retur.no_ra', '!=', NULL)->whereIn('retur.status', [1, 2])->whereIn('kategori', ['Kualitas', 'kualitas'])->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])->sum('berat');
                $totalReturNonKualitas  = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')->where('retur.no_ra', '!=', NULL)->whereIn('retur.status', [1, 2])->whereIn('kategori', ['Non Kualitas', 'nonkualitas'])->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])->sum('berat');
                $totalRetur             = ReturItem::join('retur', 'retur.id', '=', 'retur_item.retur_id')->where('retur.no_ra', '!=', NULL)->whereIn('retur.status', [1, 2])->whereBetween('retur.tanggal_retur', [$tanggal, $tanggal_akhir])->sum('berat');
                $totalPengiriman        = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')->join('items', 'order_items.item_id', 'items.id')->whereBetween('orders.tanggal_kirim', [$tanggal, $tanggal_akhir])->sum('order_items.fulfillment_berat');



                // end

                  // persentase retur
            $data = [];
            for ($no = 0; $no < 4; $no++) {
                $tanggal_awal       = date('Y-m-d', strtotime("-6 Day", strtotime($akhir)));
                // $tanggal_akhir      = $akhir;

                $mulai              = $tanggal_awal;
                // $akhir              = $tanggal_akhir;

                $kualitas                   = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)->join('items', 'retur_item.item_id', 'items.id')->whereBetween('retur.tanggal_retur', [$mulai, $akhir])->where('retur_item.kategori', 'Kualitas')->select(DB::raw('Sum(retur_item.berat) as berat'))->first();
                $nonkualitas                = Retur::join('retur_item', 'retur.id', '=', 'retur_item.retur_id')->whereIn('retur.status', [1, 2])->where('retur.no_ra', '!=', NULL)->join('items', 'retur_item.item_id', 'items.id')->whereBetween('retur.tanggal_retur', [$mulai, $akhir])->where('retur_item.kategori', 'Non Kualitas')->select(DB::raw('Sum(retur_item.berat) as berat'))->first();

                $report             = array(
                    'tanggal'           => date('d M Y', strtotime($mulai)) . "-" . date('d M Y', strtotime($akhir)),
                    'totalretur'        => $totalRetur,
                    'kualitas'          => $kualitas->berat,
                    'nonkualitas'       => $nonkualitas->berat,
                    'totalpengiriman'   => $totalPengiriman
                );
                $data[]                 = $report;
                $akhir                  = date('Y-m-d', strtotime("-1 Day", strtotime($tanggal_awal)));
            }
            $date_range             = $data;

            // grafik persentase retur
            $total_kualitas     = [];
            $total_non_kualitas = [];
            $tanggal_per_minggu       = [];
            foreach ($date_range as $row) {
                if ($row['totalpengiriman']) {
                    $total_kualitas[]     =  round(((int)$row['kualitas'] / (int)$row['totalpengiriman']) * 100, 2);
                    $total_non_kualitas[] =  round(((int)$row['nonkualitas'] / (int)$row['totalpengiriman']) * 100, 2);
                    $tanggal_per_minggu[] =  $row['tanggal'];
                }
            }
            $tgl_mingguan   = json_encode($tanggal_per_minggu);
            $alokasi        = "[{name: 'kualitas',data: ";
            $alokasi    .=  json_encode($total_kualitas);
            $alokasi    .=  "}, {name: 'non kualitas',data: ";
            $alokasi    .=  json_encode($total_non_kualitas);
            $alokasi    .=  "}]";
            if ($request->key == 'download') {
                $download = true;
                return view('admin.pages.retur.exportcsv-retur.exportitemPerKategori', compact('dataReturExport', 'download',"countTotalKualitas","countTotalNonKualitas","tanggal","tanggal_akhir","countPenanganan","getcatatan","totalPengiriman","totalRetur","totalReturNonKualitas","totalReturKualitas","date_range","tgl_mingguan" ,"alokasi"));
            } else {
                $download = false;
                return view('admin.pages.retur.exportcsv-retur.exportitemPerKategori', compact('dataReturExport', 'download',"countTotalKualitas","countTotalNonKualitas","tanggal","tanggal_akhir","countPenanganan","getcatatan","totalPengiriman","totalRetur","totalReturNonKualitas","totalReturKualitas","date_range" ,"tgl_mingguan" ,"alokasi"));
            }
        }else if ($request->key == 'logedit_retur'){
            $logretur   = Adminedit::where('table_id',$request->retur_id)->where('activity','retur')->where('type','edit')->where('content', '!=', 'Dawa Awal Retur Item')->get();
            $dataAsli   = Adminedit::where('table_id',$request->retur_id)->where('table_name','retur_item')->where('activity', '!=', 'Retur Salah Item/Tidak Sesuai Pesanan')->where('content', '=', 'Dawa Awal Retur Item')->first();
            $content    = NULL;
            if (!$dataAsli) {
                $dataAsli = Adminedit::where('table_id',$request->retur_id)->where('table_name','retur_item')->where('content', '=', 'Original Item')->first();
                $content = 'Original Item';
            }
            return view('admin.pages.retur.log-retur',compact('logretur', 'dataAsli', 'content'));
        }
        else {
            $clone                          = clone $retur;
            $master                         = $clone->groupBy('retur.id')->get();

            $idretur                        = array();
            foreach($master as $val){
                $idretur[] = $val->id;
            }

            $newDataReturItem               = $idretur;
            $stringDataReturItem            = implode(",",$newDataReturItem);
            $ReturItem                      = array();
            if($stringDataReturItem){
                $ReturItem                  = ReturItem::whereRaw("retur_id IN(".$stringDataReturItem.")")->whereNotNull('orderitem_id')->get();
            }

            $OrderItemId                    = array();
            foreach($ReturItem as $val){
                $OrderItemId[]              = $val->orderitem_id;
            }

            $newDataReturOrderItem          = $OrderItemId;
            $stringDataOrderItem            = implode(",",$newDataReturOrderItem);
            $ReturOrderItem                 = array();
            if($stringDataOrderItem){
                $ReturOrderItem             = OrderItem::whereRaw("id IN(".$stringDataOrderItem.")")->get();
            }

            $totalQty                       = 0;
            $totalBerat                     = 0;
            $totalAllQtyFulFill             = 0;
            $totalFulFillQty                = 0;
            $totalPersenQty                 = 0;
            if($ReturItem){
                $totalQty                   = $ReturItem->sum('qty');
                $totalBerat                 = $ReturItem->sum('berat');
                $totalFulFillQty            = $ReturOrderItem->sum('fulfillment_qty');

                $totalPersenQty             = $totalQty / $totalFulFillQty * 100;

            }
            $totalReturQty                  = number_format($totalQty,2);
            $totalReturBerat                = number_format($totalBerat,2);
            $totalAllQtyFulFill             = number_format($totalFulFillQty,2);
            $totalQtyDO                     = number_format($totalFulFillQty, 2);
            $totalqtypercentage             = number_format($totalPersenQty, 2);

            $retur                          = $retur->groupBy('retur.id')->paginate(10);
            
            return view('admin.pages.retur.retur-summary', compact('retur', 'tanggal','totalReturQty','totalReturBerat','totalqtypercentage','totalAllQtyFulFill'));
        }
    }

    public function paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $options = [
            'path'      => LengthAwarePaginator::resolveCurrentPath(),
            'pageName'  => 'page'
        ];
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function returNonSo(Request $request)
    {
        $customer   =   Customer::where('nama', '!=', '')
            ->where('netsuite_internal_id', '!=', NULL)
            ->orderBy('nama')->get();
        $item       =   Item::whereIn('category_id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20])->get();
        $sopir      =   Driver::where('driver_kirim', 1)->get();
        $alasan     =   Returalasan::get();
        if ($request->key == 'parent') {
            $data = Returalasan::where('kelompok', $request->parents)->get();
            return response()->json($data);
        } elseif ($request->key == 'nonnetsuite') {
            $key    = 'nonnetsuite';
            return view('admin.pages.retur.retur-non-so', compact(['customer', 'item', 'sopir', 'alasan', 'key']));
        } else {
            return view('admin.pages.retur.retur-non-so', compact(['customer', 'item', 'sopir', 'alasan']));
        }
    }

    public function returNonSoSubmit(Request $request)
    {
        // if (User::setIjin(19)) {

        DB::beginTransaction();


        if ($request->idedit != null) {

            $item_retur             =   ReturItem::find($request->idedit);
            $dataedit               =   ReturItem::where('id',$request->idedit)->first();
            $item                   =   Item::find($request->item);
            $retur                  =   Retur::find($item_retur->retur_id);

            if ($retur->no_so != "") {
                $so = Order::where('no_so', $retur->no_so)->first();
                if ($so) {
                    $line_so                = OrderItem::where('order_id', $so->id)->where('item_id', $item->id)->first();
                    if ($line_so) {
                        $item_retur->line_request        =   $line_so->line_id;
                    }
                }
            }

            $item_retur->item_id        =   $request->item;
            $item_retur->sku            =   $item->sku;
            $item_retur->qty            =   $request->qty;

            if ($request->tujuan == "produksi") {
                if ($item->category_id == "1") {
                    $item_retur->unit               =   "chillerbb";
                    $item_retur->tujuan             =   "chillerbb";
                    $item_retur->penanganan         =   "Reproses Produksi";
                } else {
                    $item_retur->unit               =   "chillerfg";
                    $item_retur->tujuan             =   "chillerfg";
                    $item_retur->penanganan         =   "Reproses Produksi";
                }
            } else if ($request->tujuan == "chillerfg") {
                $item_retur->unit               =   "chillerfg";
                $item_retur->tujuan             =   "chillerfg";
                $item_retur->penanganan         =   "Jual Sampingan";
            } else if ($request->tujuan == "gudang") {
                $item_retur->unit               =   "gudang";
                $item_retur->tujuan             =   "gudang";
                $item_retur->penanganan         =   "Kembali ke Frezeer";
                $item_retur->grade_item         =   $request->gradeitem ?? NULL;
            } else if ($request->tujuan == "musnahkan") {
                $item_retur->unit               =   "musnahkan";
                $item_retur->tujuan             =   "musnahkan";
                $item_retur->penanganan         =   "Musnahkan";
            }

            $alasan     =   Returalasan::find($request->alasan);

            $item_retur->berat          =   $request->berat;
            $item_retur->kategori       =   $alasan->jenis;
            $item_retur->catatan        =   $alasan->nama;
            $item_retur->satuan         =   $request->satuan;
            $item_retur->rate           =   '1';

            if (!$item_retur->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses Gagal";
                return $result;
            }

            $ceklog                     = Adminedit::where('table_id',$item_retur->id)->where('table_name','retur_item')->where('content', 'Dawa Awal Retur Item')->count();

            if ($ceklog < 1) {
                $logedit_retur              = new Adminedit();
                $logedit_retur->user_id     = Auth::user()->id;
                $logedit_retur->table_name  = 'retur_item';
                $logedit_retur->table_id    = $item_retur->id;
                $logedit_retur->type        = 'edit';
                $logedit_retur->activity    = 'retur';
                $logedit_retur->content     = 'Dawa Awal Retur Item';
                $logedit_retur->data        = json_encode([
                    'data'  => $dataedit
                ]);

                if (!$logedit_retur->save()) {
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }
            }
            $ceklogadmin                = Adminedit::where('table_id',$item_retur->id)->where('table_name','retur_item')->where('content','!=','Dawa Awal Retur Item')->count();
            $logedit_update              = new Adminedit();
            $logedit_update->user_id     = Auth::user()->id;
            $logedit_update->table_name  = 'retur_item';
            $logedit_update->table_id    = $item_retur->id;
            $logedit_update->type        = 'edit';
            $logedit_update->activity    = 'retur';
            $logedit_update->content     = 'Edit retur ke- '. ($ceklogadmin+1);
            $logedit_update->data        = json_encode([
                'data'  => $item_retur
            ]);

            if (!$logedit_update->save()) {
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }

        } else {

            // dd($request->all());
            if ($request->customer_id == null) {
                DB::rollBack();
                return back()->with('status', 2)->with('message', 'Data Customer Tidak Lengkap');
            }

            $retur                  =   new Retur;

            foreach ($request->item as $no => $row) :

                if ($request->returberat[$no] != "" || $request->returberat[$no] != "0") {

                    $retur->customer_id     =   $request->customer_id;
                    $retur->id_so           =   NULL;
                    $retur->qc_id           =   Auth::user()->id;
                    $retur->tanggal_retur   =   Carbon::now();
                    if ($request->nonnetsuite) {
                        $retur->status      =   4;
                    } else {
                        $retur->status      =   1;
                    }
                    if (!$retur->save()) {
                        DB::rollBack();
                        $result['status']   =   400;
                        $result['msg']      =   "Proses Gagal";
                        return $result;
                    }

                    $item = Item::find($request->item[$no]);

                    if ($item) {

                        $item_retur             =   new ReturItem;

                        $item_retur->retur_id   =   $retur->id;
                        $item_retur->item_id    =   $request->item[$no];
                        $item_retur->sku        =   $item->sku;
                        $item_retur->orderitem_id     =   null;
                        $item_retur->qty        =   $request->returqty[$no];

                        if ($request->returto[$no] == "produksi") {
                            if ($item->category_id == "1") {
                                $item_retur->unit               =   "chillerbb";
                                $item_retur->tujuan             =   "chillerbb";
                                $item_retur->penanganan         =   "Reproses Produksi";
                            } else {
                                $item_retur->unit               =   "chillerfg";
                                $item_retur->tujuan             =   "chillerfg";
                                $item_retur->penanganan         =   "Reproses Produksi";
                            }
                        } else if ($request->returto[$no] == "chillerfg") {
                            $item_retur->unit               =   "chillerfg";
                            $item_retur->tujuan             =   "chillerfg";
                            $item_retur->penanganan         =   "Jual Sampingan";
                        } else if ($request->returto[$no] == "gudang") {
                            $item_retur->unit               =   "gudang";
                            $item_retur->tujuan             =   "gudang";
                            $item_retur->penanganan         =   "Kembali ke Frezeer";
                            $item_retur->grade_item         =   $request->gradeitem[$no] ?? NULL;
                        } else if ($request->returto[$no] == "musnahkan") {
                            $item_retur->unit               =   "musnahkan";
                            $item_retur->tujuan             =   "musnahkan";
                            $item_retur->penanganan         =   "Musnahkan";
                        }

                        $alasan     =   Returalasan::find($request->alasan[$no]);

                        $item_retur->berat      =   $request->returberat[$no];
                        $item_retur->kategori   =   $alasan->jenis;
                        $item_retur->catatan    =   $alasan->nama;
                        $item_retur->satuan     =   $request->satuan[$no];
                        $item_retur->rate       =   '1';

                        $item_retur->status     =   1;
                        if (!$item_retur->save()) {
                            DB::rollBack();
                            $result['status']   =   400;
                            $result['msg']      =   "Proses Gagal";
                            return $result;
                        }
                    }
                }

            endforeach;
        }

        DB::commit();
        if ($request->idedit != null) {
            return back()->with('status', 1)->with('message', 'Berhasil Update Retur');
        } else {
            return redirect(url('admin/retur/detail') . "/" . $retur->id)->with('status', '1')->with('message', 'Retur berhasil disimpan!');
        }
        // }
    }

    public function returSoSubmit(Request $request)
    {
        // if (User::setIjin(19)) {

        $order                  =   Order::find($request->order_id);

        DB::beginTransaction();

        $retur                  =   Retur::where('id_so', $order->id_so)->first() ?? new Retur;

        $retur->customer_id     =   $order->customer_id;
        $retur->id_so           =   $order->netsuite_internal_id;
        $retur->no_so           =   $order->no_so;
        $retur->qc_id           =   Auth::user()->id;
        $retur->tanggal_retur   =   Carbon::now();

        $retur->status          =   1;
        if (!$retur->save()) {
            DB::rollBack();
            $result['status']   =   400;
            $result['msg']      =   "Proses Gagal";
            return $result;
        }

        foreach ($request->orderitem_id as $no => $row) :

            if ($request->returberat[$no] > 0) {

                $data                   =   OrderItem::find($row);

                $data->retur_qty        =   $request->returqty[$no];
                $data->retur_berat      =   $request->returberat[$no];
                $data->retur_tujuan     =   $request->returto[$no];
                $data->retur_notes      =   $request->alasan[$no];
                $data->retur_status     =   1;

                $data->save();

                $item_retur             =   ReturItem::where('orderitem_id', $row)
                    ->first() ?? new ReturItem;

                $item = Item::find($data->item_id);

                $item_retur->retur_id           =   $retur->id;
                $item_retur->item_id            =   $data->item_id;
                $item_retur->line_request       =   $data->line_id;
                $item_retur->sku                =   $data->sku;
                $item_retur->orderitem_id       =   $row;
                $item_retur->qty                =   $request->returqty[$no];

                if ($request->returto[$no] == "produksi") {
                    if ($item->category_id == "1") {
                        $item_retur->unit               =   "chillerbb";
                        $item_retur->tujuan             =   "chillerbb";
                        $item_retur->penanganan         =   "Reproses Produksi";
                    } else {
                        $item_retur->unit               =   "chillerfg";
                        $item_retur->tujuan             =   "chillerfg";
                        $item_retur->penanganan         =   "Reproses Produksi";
                    }
                } else if ($request->returto[$no] == "chillerfg") {
                    $item_retur->unit               =   "chillerfg";
                    $item_retur->tujuan             =   "chillerfg";
                    $item_retur->penanganan         =   "Jual Sampingan";
                } else if ($request->returto[$no] == "gudang") {
                    $item_retur->unit               =   "gudang";
                    $item_retur->tujuan             =   "gudang";
                    $item_retur->penanganan         =   "Kembali ke Frezeer";
                    $item_retur->grade_item         =   $request->gradeitem[$no] ?? NULL;
                } else if ($request->returto[$no] == "musnahkan") {
                    $item_retur->unit               =   "musnahkan";
                    $item_retur->tujuan             =   "musnahkan";
                    $item_retur->penanganan         =   "Musnahkan";
                }

                $item_retur->berat      =   $request->returberat[$no];
                $item_retur->kategori   =   $request->kategori[$no];
                $item_retur->catatan    =   $request->alasan[$no];
                $item_retur->rate       =   $data->rate;
                $item_retur->satuan     =   $request->satuan[$no];
                $item_retur->driver     =   $request->driver[$no];

                $item_retur->status     =   1;
                if (!$item_retur->save()) {
                    DB::rollBack();
                    $result['status']   =   400;
                    $result['msg']      =   "Proses Gagal";
                    return $result;
                }
            }

        endforeach;


        DB::commit();

        return redirect(url('admin/retur/detail') . "/" . $retur->id)->with('status', '1')->with('message', 'Retur berhasil disimpan!');
        // }
    }


    public function selesaikan(Request $request)
    {
        $retur                  =   Retur::find($request->retur_id);

        if ($request->key == 'inject') {
            Netsuite::retur($retur->id);
            return redirect()->to(url()->previous() . '#custom-tabs-three-summary')->with('status', 1)->with('message', 'Berhasil Tembak Ulang');
        }

        DB::beginTransaction();

        if ($retur) {
            if ($retur->data_order) {
                if (strtotime($retur->data_order->tanggal_kirim) > strtotime($request->tanggal_input)) {
                    DB::rollBack();
                    return back()->with('status', 2)->with('message', 'Tanggal input salah');
                }
            }
        } else {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Data tidak ditemukan');
        }

         // Jika retur dengan ID yang diberikan tidak ditemukan, buat baru
         if (!$retur) {
            $retur = new Retur;
        }

        $retur->operator        =   $request->operator;
        $retur->tanggal_retur   =   $request->tanggal_input;
        $retur->status          =   $request->nonnetsuite ? 5 : 2;
        $retur->no_so           =   $request->nonnetsuite ? $request->nodo : $request->nodo ?? NULL;
        
        // if ($request->nonnetsuite) {
        //     $retur->status          =   5;
        //     $retur->no_so           = $request->nodo;
        // } else {
            
        //     $retur->status          =   2;
        //     $retur->no_so           = $request->nodo ?? NULL;

        // }
        $retur->save();


        if (!$retur->save()) {
            DB::rollback();
        }

        foreach (ReturItem::where('retur_id', $retur->id)->get() as $row) {
            $row->driver    =   $request->driver;
            if (!$row->save()) {
                DB::rollback();
            }
        }

        $item               =   ReturItem::select(DB::raw('SUM(qty) as qty, SUM(berat) as berat, item_id, unit, grade_item'))->where('retur_id', $retur->id)->groupBy(['item_id', 'unit', 'grade_item'])->get();
        foreach ($item as $val) {

            $item     =   Item::where('id', $val->item_id)->withTrashed()->first();

            if ($item->category_id != "1") {
                if ($val->unit == "chillerbb") {
                    return redirect()->to(url()->previous() . '#custom-tabs-three-summary')->with('status', 2)->with('message', 'Item ' . $item->nama . ' tidak bisa masuk ke chiller BB, silahkan ganti ke Chiller FG');
                }
            }

            if (!$request->nonnetsuite) {

                if ($val->unit == 'gudang') {

                    // $itemfrozen         =   Item::item_fresh_to_frozen($val->item_id, $item->nama);

                    $abf                =   new Abf;
                    $abf->table_name    =   'retur_item';
                    $abf->tanggal_masuk =   date('Y-m-d', strtotime($request->tanggal_input));
                    $abf->table_id      =   $retur->id;
                    $abf->asal_tujuan   =   'retur';
                    $abf->item_id       =   $val->item_id;
                    $abf->item_id_lama  =   $val->item_id;
                    $abf->item_name     =   $item->nama;
                    $abf->label         =   Customer::find($retur->customer_id)->nama ?? "";
                    $abf->customer_id   =   $retur->customer_id ?? "";
                    $abf->jenis         =   'masuk';
                    $abf->type          =   'free';
                    $abf->qty_awal      =   $val->qty;
                    $abf->berat_awal    =   number_format((float)$val->berat,2,'.','');
                    $abf->qty_item      =   $val->qty;
                    $abf->berat_item    =   number_format((float)$val->berat,2,'.','');
                    $abf->status        =   1;
                    $abf->grade_item    =   $val->grade_item ?? NULL;

                    if (!$abf->save()) {
                        DB::rollback();
                    }
                    $order                  =   Order::find($request->order_id);
                } else if ($val->unit == 'chillerfg') {
                    $chiller                    =   new Chiller;
                    $chiller->table_name        =   'retur_item';
                    $chiller->table_id          =   $retur->id;
                    $chiller->asal_tujuan       =   'retur';
                    $chiller->item_id           =   $val->item_id;
                    $chiller->item_name         =   $item->nama;
                    $chiller->jenis             =   'masuk';
                    $chiller->type              =   'hasil-produksi';
                    $chiller->label             =   Customer::find($retur->customer_id)->nama ?? "";
                    $chiller->qty_item          =   $val->qty;
                    $chiller->berat_item        =   number_format((float)$val->berat,2,'.','');
                    $chiller->tanggal_potong    =   date('Y-m-d', strtotime($request->tanggal_input));
                    $chiller->tanggal_produksi  =   date('Y-m-d', strtotime($request->tanggal_input));
                    $chiller->stock_item        =   $val->qty;
                    $chiller->stock_berat       =   number_format((float)$val->berat,2,'.','');
                    $chiller->status            =   2;


                    if (!$chiller->save()) {
                        DB::rollback();
                    }
                } else if ($val->unit == 'chillerbb') {
                    $chiller                    =   new Chiller;
                    $chiller->table_name        =   'retur_item';
                    $chiller->table_id          =   $retur->id;
                    $chiller->asal_tujuan       =   'retur';
                    $chiller->item_id           =   $val->item_id;
                    $chiller->item_name         =   $item->nama;
                    $chiller->jenis             =   'masuk';
                    $chiller->type              =   'bahan-baku';
                    $chiller->label             =   Customer::find($retur->customer_id)->nama ?? "";
                    $chiller->qty_item          =   $val->qty;
                    $chiller->berat_item        =   number_format((float)$val->berat,2,'.','');
                    $chiller->tanggal_potong    =   date('Y-m-d', strtotime($request->tanggal_input));
                    $chiller->tanggal_produksi  =   date('Y-m-d', strtotime($request->tanggal_input));
                    $chiller->stock_item        =   $val->qty;
                    $chiller->stock_berat       =   number_format((float)$val->berat,2,'.','');
                    $chiller->status            =   2;

                    if (!$chiller->save()) {
                        DB::rollback();
                    }
                }
            }
        }


        //update statuss retur item jadi selesai
        $retur_item = ReturItem::where('retur_id', $retur->id)->update(['status' => '2']);
        DB::commit();

        if (!$request->nonnetsuite) {
            Netsuite::retur($retur->id);
        }

        return redirect()->to(url()->previous() . '#custom-tabs-three-summary')->with('status', 1)->with('message', 'Berhasil Selesaikan');
    }

    public function driverretur(Request $request)
    {
        DB::beginTransaction();

        $data                   =   OrderItem::find($request->item);

        $data->retur_qty       =   $request->qty;
        $data->retur_berat     =   $request->berat;
        $data->retur_notes     =   $request->alasan;
        $data->retur_status    =   1;

        $id_so  = $data->order_id;
        $order  = Order::find($id_so);

        $retur  = Retur::where('id_so', $id_so)->first();
        if (!$retur) {
            $retur = new Retur;
            $retur->customer_id      =   $order->customer_id;
            $retur->id_so            =   $order->netsuite_internal_id;
            $retur->qc_id            =   Auth::user()->id;
            $retur->tanggal_retur    =   Carbon::now();
        }

        $retur->status           =   1;
        if (!$retur->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses Gagal');
        }

        $item_retur         =   ReturItem::where('item_id', $data->item_id)
            ->where('retur_id', $retur->id)
            ->first();

        if (!$item_retur) {
            $item_retur     = new ReturItem();
        }

        $item_retur->retur_id         =   $retur->id;
        $item_retur->item_id          =   $data->item_id;
        $item_retur->orderitem_id     =   $id_so;
        $item_retur->sku              =   $data->sku;
        $item_retur->qty              =   $request->qty;
        $item_retur->berat            =   $request->berat;
        $item_retur->status           =   1;
        if (!$item_retur->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses Gagal');
        }



        $log_item                   = new OrderItemLog;
        $log_item->activity         = "retur-proses";
        $log_item->order_item_id    = $data->id;
        $log_item->user_id          = Auth::user()->id;
        $log_item->key              = AppKey::generate();
        if (!$log_item->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses Gagal');
        }

        $data->fulfillment_qty              =   $data->fulfillment_qty - $request->qty;
        $data->fulfillment_berat            =   $data->fulfillment_berat - $request->berat;
        if (!$data->save()) {
            DB::rollBack();
            return back()->with('status', 2)->with('message', 'Proses Gagal');
        }

        DB::commit();
        return back()->with('status', 1)->with('message', 'Retur sukses');
    }


    public function destroy(Request $request)
    {

        if ($request->key == 'cekAlurData') {
            // return response()->json($request->id);
            $cekRetur               = Retur::find($request->id);
            $cekReturItem           = ReturItem::where('retur_id', $request->id)->get();

            if ($cekRetur->status != 1) {
                if ($cekReturItem) {
                    foreach ($cekReturItem as $returItem) {

                        if ($returItem->penanganan == "Reproses Produksi" || $returItem->penanganan == "Jual Sampingan") {
                            $cekChiller                 = Chiller::where('table_name', 'retur_item')->where('table_id', $returItem->retur_id)
                                                            ->where('jenis', 'masuk')->where('label', Customer::where('id', Retur::where('id', $returItem->retur_id)->first()->customer_id)->first()->nama)
                                                            ->where('item_id', $returItem->item_id)
                                                            // ->where('qty_item', $returItem->qty)->where('berat_item', $returItem->berat)
                                                            // ->where('stock_item', $returItem->qty)->where('stock_berat', $returItem->berat)
                                                            ->first();

                            if ($cekChiller) {
                                if ($cekChiller->qty_item == $cekChiller->stock_item && $cekChiller->stock_berat == $cekChiller->berat_item) {
                                    return response()->json([
                                        'message'          => 'success',
                                        'status'           => '200',
                                        'data'             => $cekRetur
                                    ]);
                                }  else {
                                    return response()->json([
                                        'message'          => 'Gagal batal karena item sudah terpakai di Chiller',
                                        'status'           => '400',
                                    ]);
                                    return false;
                                }
                            }
                        } else if ($returItem->penanganan == 'Kembali ke Frezeer') {
                            $cekAbf                     = Abf::where('table_name', 'retur_item')->where('table_id', $returItem->retur_id)
                                                            // ->where('qty_awal', $returItem->qty)->where('berat_awal', $returItem->berat)
                                                            // ->where('qty_item', $returItem->qty)->where('berat_item', $returItem->berat)
                                                            ->where('label', Customer::where('id', Retur::where('id', $returItem->retur_id)->first()->customer_id)->first()->nama)
                                                            ->where('grade_item', $returItem->grade_item)
                                                            ->where('jenis', 'masuk')->where('item_id', $returItem->item_id)->first();

                            if ($cekAbf) {
                                if ($cekAbf->qty_awal == $cekAbf->qty_item && $cekAbf->berat_awal == $cekAbf->berat_item) {
                                    return response()->json([
                                        'message'          => 'success',
                                        'status'           => '200',
                                        'data'             => $cekRetur
                                    ]);
                                }  else {
                                    return response()->json([
                                        'message'          => 'Gagal batal karena item sudah terpakai di ABF',
                                        'status'           => '400',
                                    ]);
                                    return false;
                                }
                            }

                            if ($cekAbf) {
                                $cekProductGudang       = Product_gudang::where('table_name', 'abf')->where('table_id', $cekAbf->id)->first();

                                if ($cekProductGudang) {
                                    return response()->json([
                                        'message'          => 'Gagal batal karena item sudah terpakai di CS',
                                        'status'           => '400',
                                    ]);
                                    return false;
                                }
                            }
                        }
                    }
                    return response()->json([
                        'message'          => 'success',
                        'status'           => '200',
                        'data'             => $cekRetur
                    ]);
                } else {
                    return response()->json([
                        'message'          => 'Data tidak ditemukan',
                        'status'           => '401',
                    ]);
                    return false;
                }
            } else {
                return response()->json([
                    'message'          => 'success',
                    'status'           => '200',
                    'data'             => $cekRetur
                ]);
            }
        } else {

            $data   =   Retur::find($request->id);

            if ($data) {
                $list   =   ReturItem::where('retur_id', $data->id)->get();

                foreach ($list as $row) {
                    $data_chil_masuk    =   Chiller::where('table_name', 'retur_item')
                        ->where('asal_tujuan', 'retur')
                        ->where('table_id', $data->id)
                        ->first();

                    if ($data_chil_masuk) {
                        $data_chil_masuk->delete();
                    }

                    $storage    =   Abf::where("table_name", 'retur_item')
                                    ->where('table_id', $data->id)
                                    ->first();

                    if ($storage) {
                        $storage->delete();
                    }

                    // $row->delete();
                }

                $data->status = 3;

                $dataNetsuite = Netsuite::where('tabel', 'retur')->where('tabel_id', $data->id)->get();

                if ($dataNetsuite) {
                    foreach ($dataNetsuite as $deleteNS) {
                        $deleteNS->delete();
                    }
                }

                $data->save();

                return back()->with('status', 1)->with('message', 'Retur berhasil dibatalkan');
            }
        }

        return back()->with('status', 2)->with('message', 'Data tidak ditemukan');
    }


    public function returdo(Request $request)
    {
        $data       =   Order::find($request->id);
        $orderitem  =   OrderItem::where('order_id', $data->id)->where('fulfillment_berat', '>', 0)->get();
        $alasan     =   Returalasan::get();
        return view('admin.pages.retur.retur-do', compact('data', 'orderitem', 'alasan'));
    }

    public function returDoSoSubmit(Request $request)
    {
        $order                  =   Order::find($request->order_id);

        DB::beginTransaction();

        if ($request->idedit != null) {
            // dd($request->all());
            $item_retur             =   ReturItem::find($request->idedit);
            $dataedit               =   ReturItem::where('id',$request->idedit)->first();
            $item                   =   Item::find($request->item);
            $retur                  =   Retur::find($item_retur->retur_id);
            $chiller                = false;

            if ($request->alasan != '29' || $request->alasan != '28') {

                if ($item_retur->tujuan == "chillerfg") {
                    $chiller    =   Chiller::where('table_name', 'retur_item')->where('table_id', $item_retur->retur_id)->where('type', 'hasil-produksi')->where('item_id', $item_retur->item_id)->first();
                } elseif ($item_retur->tujuan == "chillerbb") {
                    $chiller    =   Chiller::where('table_name', 'retur_item')->where('table_id', $item_retur->retur_id)->where('type', 'bahan-baku')->where('item_id', $item_retur->item_id)->first();
                } elseif ($item_retur->tujuan == "gudang") {

                    $abf                =   Abf::where('table_id', $item_retur->id)->where('grade_item', $item_retur->grade_item)->first();

                    if ($abf) {

                        $abf->qty_awal      =   ($abf->qty_awal - $item_retur->qty) + $request->qty;
                        $abf->berat_awal    =   ($abf->berat_awal - $item_retur->berat) + $request->berat;
                        $abf->qty_item      =   ($abf->qty_item - $item_retur->qty) + $request->qty;;
                        $abf->berat_item    =   ($abf->berat_item  - $item_retur->berat) + $request->berat;
                        $abf->grade_item    =   $request->gradeitem ?? NULL;

                        $abf->save();
                    }
                } else {
                    $chiller    = false;
                }

                $selisih_berat  = 0;
                $selisih_qty    = 0;

                if ($chiller) {

                    $chiller->item_id       =   $item_retur->item_id;
                    $chiller->item_name     =   $item->nama;
                    $chiller->qty_item      =   ($chiller->qty_item - $item_retur->qty) + $request->qty;
                    $chiller->berat_item    =   ($chiller->berat_item - $item_retur->berat) + $request->berat;
                    $chiller->stock_item    =   ($chiller->stock_item - $item_retur->qty) + $request->qty;;
                    $chiller->stock_berat   =   ($chiller->stock_berat - $item_retur->berat) + $request->berat;

                    if (!$chiller->save()) {
                        DB::rollBack();
                        $result['status']   =   400;
                        $result['msg']      =   "Proses Gagal";
                        return $result;
                    }
                }
            }
            $alasan     =   Returalasan::find($request->alasan);

            $item_retur->kategori       =   $alasan->jenis;
            $item_retur->catatan        =   $alasan->nama;
            $item_retur->satuan         =   $request->satuan;
            $item_retur->driver         =   $request->driver;
            $item_retur->rate           =   '1';

            if ($retur->no_so != "") {
                $so = Order::where('no_so', $retur->no_so)->first();
                if ($so) {
                    $line_so                = OrderItem::where('order_id', $so->id)->where('item_id', $item->id)->first();
                    if ($line_so) {
                        $item_retur->line_request        =   $line_so->line_id;
                        // 29 atau 28 adalah id alasan retur
                        // if($request->alasan == '29' || $request->alasan == '28'){
                        //     $item_retur->qty            =   $line_so->qty;
                        //     $item_retur->berat          =   $line_so->berat;
                        // }
                    }
                }
            }

            $item_retur->qty            =   $request->qty;
            $item_retur->berat          =   $request->berat;

            $item_retur->item_id        =   $request->item;
            $item_retur->sku            =   $item->sku;

            if ($request->tujuan == "produksi") {
                if ($item->category_id == "1") {
                    $item_retur->unit               =   "chillerbb";
                    $item_retur->tujuan             =   "chillerbb";
                    $item_retur->penanganan         =   "Reproses Produksi";
                } else {
                    $item_retur->unit               =   "chillerfg";
                    $item_retur->tujuan             =   "chillerfg";
                    $item_retur->penanganan         =   "Reproses Produksi";
                }
            } else if ($request->tujuan == "chillerfg") {
                $item_retur->unit               =   "chillerfg";
                $item_retur->tujuan             =   "chillerfg";
                $item_retur->penanganan         =   "Jual Sampingan";
            } else if ($request->tujuan == "gudang") {
                $item_retur->unit               =   "gudang";
                $item_retur->tujuan             =   "gudang";
                $item_retur->penanganan         =   "Kembali ke Frezeer";
                $item_retur->grade_item         =   $request->gradeitem ?? NULL;
            } else if ($request->tujuan == "musnahkan") {
                $item_retur->unit               =   "musnahkan";
                $item_retur->tujuan             =   "musnahkan";
                $item_retur->penanganan         =   "Musnahkan";
            }

            if (!$item_retur->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses Gagal";
                return $result;
            }
            $ceklog                     = Adminedit::where('table_id',$item_retur->id)->where('table_name','retur_item')->where('content', 'Dawa Awal Retur Item')->count();

            if ($ceklog < 1) {
                $logedit_retur              = new Adminedit();
                $logedit_retur->user_id     = Auth::user()->id;
                $logedit_retur->table_name  = 'retur_item';
                $logedit_retur->table_id    = $item_retur->id;
                $logedit_retur->type        = 'edit';
                $logedit_retur->activity    = 'retur';
                $logedit_retur->content     = 'Dawa Awal Retur Item';
                $logedit_retur->data        = json_encode([
                    'data'  => $dataedit
                ]);

                if (!$logedit_retur->save()) {
                    return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
                }
            }
            $ceklogadmin                = Adminedit::where('table_id',$item_retur->id)->where('table_name','retur_item')->where('content','!=','Dawa Awal Retur Item')->count();
            $logedit_update              = new Adminedit();
            $logedit_update->user_id     = Auth::user()->id;
            $logedit_update->table_name  = 'retur_item';
            $logedit_update->table_id    = $item_retur->id;
            $logedit_update->type        = 'edit';
            $logedit_update->activity    = 'retur';
            $logedit_update->content     = 'Edit retur ke- '. ($ceklogadmin+1);
            $logedit_update->data        = json_encode([
                'data'  => $item_retur
            ]);

            if (!$logedit_update->save()) {
                return back()->with('status', 2)->with('message', 'Proses simpan log gagal');
            }


        } else {
            // dd($request->all());

            $retur                  =   new Retur;

            $retur->customer_id     =   $order->customer_id;
            $retur->id_so           =   $order->netsuite_internal_id;
            $retur->no_so           =   $order->no_so;
            $retur->qc_id           =   Auth::user()->id;
            $retur->tanggal_retur   =   Carbon::now();

            $retur->status          =   1;
            if (!$retur->save()) {
                DB::rollBack();
                $result['status']   =   400;
                $result['msg']      =   "Proses Gagal";
                return $result;
            }

            foreach ($request->item as $no => $row) :
                $orderitem              =   OrderItem::where('id', $row)->first();

                if ($request->returberat[$no] != "" || $request->returberat[$no] != "0" || $request->gradeitem[$no] != "") {

                    $item = $request->itemTukar[$no] == null ? Item::find($orderitem->item_id) : Item::find($request->itemTukar[$no]);

                    if ($item) {


                        $item_retur                     =   new ReturItem;


                        $item_retur->line_request       =   $orderitem->line_id;
                        $item_retur->retur_id           =   $retur->id;
                        $item_retur->orderitem_id       =   $orderitem->id;
                        if ($request->itemTukar[$no] == null) {
                            $item_retur->item_id            =   $orderitem->item_id;
                            $item_retur->sku                =   $orderitem->sku;
                        } else {
                            $item_retur->item_id            =   $item->id;
                            $item_retur->sku                =   $item->sku;
                        }
                        $item_retur->qty                =   $request->returqty[$no];

                        if ($request->returto[$no] == "produksi") {
                            if ($item->category_id == "1") {
                                $item_retur->unit               =   "chillerbb";
                                $item_retur->tujuan             =   "chillerbb";
                                $item_retur->penanganan         =   "Reproses Produksi";
                            } else {
                                $item_retur->unit               =   "chillerfg";
                                $item_retur->tujuan             =   "chillerfg";
                                $item_retur->penanganan         =   "Reproses Produksi";
                            }
                        } else if ($request->returto[$no] == "chillerfg") {
                            $item_retur->unit               =   "chillerfg";
                            $item_retur->tujuan             =   "chillerfg";
                            $item_retur->penanganan         =   "Jual Sampingan";
                        } else if ($request->returto[$no] == "gudang") {
                            $item_retur->unit               =   "gudang";
                            $item_retur->tujuan             =   "gudang";
                            $item_retur->penanganan         =   "Kembali ke Frezeer";
                            $item_retur->grade_item         =   $request->gradeitem[$no] ?? NULL;
                        } else if ($request->returto[$no] == "musnahkan") {
                            $item_retur->unit               =   "musnahkan";
                            $item_retur->tujuan             =   "musnahkan";
                            $item_retur->penanganan         =   "Musnahkan";
                        }

                        $alasan     =   Returalasan::find($request->alasan[$no]);

                        $item_retur->berat              =   $request->returberat[$no];
                        $item_retur->satuan             =   $request->satuan[$no];
                        // if($request->itemTukar[$no] == null){
                        // } else {
                        //     $item_retur->berat              =   $orderitem->berat;
                        // }
                        $item_retur->kategori           =   $alasan->jenis;
                        $item_retur->catatan            =   $alasan->nama;
                        $item_retur->rate               =   $orderitem->rate;

                        $item_retur->status             =   1;
                        if (!$item_retur->save()) {
                            DB::rollBack();
                            $result['status']   =   400;
                            $result['msg']      =   "Proses Gagal";
                            return $result;
                        }

                        if ($request->itemTukar[$no] == null) {
                            $orderitem->retur_qty        =   $orderitem->retur_qty + $request->returqty[$no];
                            $orderitem->retur_berat      =   $orderitem->retur_berat + $request->returberat[$no];
                            // $orderitem->retur_tujuan     =   $request->returto[$no];
                        } else {
                            // Cek data log sudah ada atau belum
                            $cekLog            = Adminedit::where('table_name', 'retur_item')->where('type', 'retur')->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan')
                                ->where('table_id', $item_retur->id)->first();
                            if (!$cekLog) {
                                // Log setelah
                                $log               =   new Adminedit;
                                $log->data         =   $orderitem->nama_detail;
                                $log->table_name   =   'retur_item';
                                $log->activity     =   'Retur Salah Item/Tidak Sesuai Pesanan';
                                $log->status       =   1;
                                $log->content      =   'Original Item';
                                $log->type         =   'retur';
                                $log->table_id     =   $item_retur->id;
                                $log->save();
                            }
                        }
                        // $orderitem->retur_notes      =   $request->alasan[$no];
                        $orderitem->retur_status     =   1;

                        $orderitem->save();
                    }
                }

            endforeach;
        }

        DB::commit();
        try {
            Chiller::recalculate_chiller($chiller->id);
        } catch (\Throwable $th) {
        }
        if ($request->idedit != null) {
            return back()->with('status', 1)->with('message', 'Berhasil Update Retur');
        } else {
            return redirect(url('admin/retur/detail') . "/" . $retur->id)->with('status', '1')->with('message', 'Retur berhasil disimpan!');
        }
    }


    public function custom_selesaikan(Request $request)
    {
        DB::beginTransaction();
        $retur                  =   Retur::find($request->retur_id);

        $item               =   ReturItem::select(DB::raw('SUM(qty) as qty, SUM(berat) as berat, item_id, unit'))->where('retur_id', $retur->id)->groupBy(['item_id', 'unit'])->get();

        foreach ($item as $val) {

            $item     =   Item::find($val->item_id);

            if ($val->unit == 'gudang') {
                $replace = str_replace(' FROZEN', '', $item->nama);

                $itemfrozen         =   Item::where('nama', $replace . ' FROZEN')->first();
                $abf                =   new Abf;
                $abf->table_name    =   'retur_item';
                $abf->tanggal_masuk     =   date('Y-m-d', strtotime($request->tanggal_retur));
                $abf->table_id      =   $retur->id;
                $abf->asal_tujuan   =   'retur';
                $abf->item_id       =   $item->id;
                $abf->item_id_lama  =   $val->item_id;
                $abf->item_name     =   $item->nama;
                $abf->label         =   Customer::find($retur->customer_id)->nama ?? "";
                $abf->customer_id   =   $retur->customer_id ?? "";
                $abf->jenis         =   'masuk';
                $abf->type          =   'free';
                $abf->qty_awal      =   $val->qty;
                $abf->berat_awal    =   number_format($val->berat,2);
                $abf->qty_item      =   $val->qty;
                $abf->berat_item    =   number_format($val->berat,2);
                $abf->status        =   1;

                if (!$abf->save()) {
                    DB::rollback();
                }
            } else if ($val->unit == 'chillerfg') {
                $chiller                    =   new Chiller;
                $chiller->table_name        =   'retur_item';
                $chiller->table_id          =   $retur->id;
                $chiller->asal_tujuan       =   'retur';
                $chiller->item_id           =   $val->item_id;
                $chiller->item_name         =   $item->nama;
                $chiller->jenis             =   'masuk';
                $chiller->type              =   'hasil-produksi';
                $chiller->label             =   Customer::find($retur->customer_id)->nama ?? "";
                $chiller->qty_item          =   $val->qty;
                $chiller->berat_item        =   number_format($val->berat,2);
                $chiller->tanggal_potong    =   date('Y-m-d', strtotime($request->tanggal_retur));
                $chiller->tanggal_produksi  =   date('Y-m-d', strtotime($request->tanggal_retur));
                $chiller->stock_item        =   $val->qty;
                $chiller->stock_berat       =   number_format($val->berat,2);
                $chiller->status            =   2;

                if (!$chiller->save()) {
                    DB::rollback();
                }
            } else if ($val->unit == 'chillerbb') {
                $chiller                    =   new Chiller;
                $chiller->table_name        =   'retur_item';
                $chiller->table_id          =   $retur->id;
                $chiller->asal_tujuan       =   'retur';
                $chiller->item_id           =   $val->item_id;
                $chiller->item_name         =   $item->nama;
                $chiller->jenis             =   'masuk';
                $chiller->type              =   'bahan-baku';
                $chiller->label             =   Customer::find($retur->customer_id)->nama ?? "";
                $chiller->qty_item          =   $val->qty;
                $chiller->berat_item        =   number_format($val->berat,2);
                $chiller->tanggal_potong    =   date('Y-m-d', strtotime($request->tanggal_retur));
                $chiller->tanggal_produksi  =   date('Y-m-d', strtotime($request->tanggal_retur));
                $chiller->stock_item        =   $val->qty;
                $chiller->stock_berat       =   number_format($val->berat,2);
                $chiller->status            =   2;

                if (!$chiller->save()) {
                    DB::rollback();
                }
            }
        }

        //update statuss retur item jadi selesai
        $retur_item = ReturItem::where('retur_id', $retur->id)->update(['status' => '2']);
        DB::commit();

        return $retur;
    }

    public function alasanRetur(Request $request)
    {
        $data = Returalasan::orderBy('nama','desc')
                            ->where(function ($query) use ($request) {
                                if ($request->kelompok != '') {
                                    $query->where('kelompok','=', $request->kelompok);
                                }
                            })
                            ->get();
        if ($request->key == 'edit') {
            $edit = Returalasan::where('id', $request->id)->first();
            // dd($edit);
            return view('admin.pages.retur.alasan.edit',compact('edit'));
        }
        return view('admin.pages.retur.alasan.index',compact('data'));
    }
}

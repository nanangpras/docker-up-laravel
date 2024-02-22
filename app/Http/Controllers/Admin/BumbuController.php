<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bumbu;
use App\Models\BumbuDetail;
use App\Models\Customer;
use App\Models\CustomerBumbu;
use Carbon\Carbon;
use Facade\FlareClient\View;
use Illuminate\Http\Request;

class BumbuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data       = Bumbu::orderBy('bumbu.id','desc');
        $bumbu_list = Bumbu::all();
        $customer   = Customer::all();

        if ($request->key == 'gudang') {
            $bumbu_gudang   = Bumbu::select('id')->get();
            $list_detail    = Bumbu::with('freetemp', 'customer_bumbu','customer_bumbu.customers')->get();
            $customer_bumbu = CustomerBumbu::where('status_bumbu', 1)->get();
            $search         = $request->cari_item ?? NULL;
            $status         = $request->cari_status ?? 'semua';

            $tgl_awal       = $request->tanggal_awal ?? date('Y-m-d');
            $tgl_akhir      = $request->tanggal_akhir ?? date('Y-m-d');


            if ($request->subkey == 'search') {

                if ($request->subSubKey == true) {
                    $searchBumbu = true;
                    
                } else {
                    $searchBumbu = false;
                    
                }


                $data =     $data->where(function($query) use ($search, $tgl_awal, $tgl_akhir, $status) {

                            if ($search != NULL) {

                                // $query->orWhereHas('bumbu_detail', function($query) use ($tgl_awal, $tgl_akhir, $status, $search) {
                                //     $query->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]);
                                //     if ($status != 'semua') {
                                //         $query->where('status', $status);
                                //     }
                                // });
                                $query->where('id', $search);
                                
                                
                            } 

                            if ($status != 'semua') {
                                $query->whereHas('bumbu_detail', function($query) use ($tgl_awal, $tgl_akhir, $status, $search) {
                                    $query->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]);
                                    if ($status != 'semua') {
                                        $query->where('status', $status);
                                    }
                                });
                            }

                            
                            

                            $query->whereBetween('created_at', [$tgl_awal . ' 00:00:00', $tgl_akhir . ' 23:59:59']);

                    })->get();


                return view('admin.pages.bumbu.gudang.cari-bumbu',compact('data','bumbu_list','list_detail','customer_bumbu', 'searchBumbu', 'search', 'tgl_awal', 'tgl_akhir', 'status'));

            }

            
            return view('admin.pages.bumbu.gudang.list',compact('bumbu_list','list_detail','customer_bumbu'));
        } 

        
        if($request->key == 'unduh') {
            $nama = $request->input('nama');
            $bumbu_gudang = Bumbu::where('nama', 'LIKE', $nama)->get();

            return view('admin.pages.bumbu.gudang.list', compact('bumbu_gudang'));
        }
        $data       = $data->get();


        return view('admin.pages.bumbu.index',compact('data','customer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->key == 'customer'){
            $id = $request->input('bumbuId');
            $existCustomer = CustomerBumbu::where('bumbu_id', $id)->get();
            $customer = Customer::all();
            return view('admin.pages.bumbu.customer.tambah', compact('customer', 'existCustomer','id'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        if ($request->key == 'bumnbu_admin') {
            $bumbu          = new Bumbu;
            $bumbu->nama    = $request->nama;
            $bumbu->stock   = NULL;
            $bumbu->berat   = NULL;
            $bumbu->save();
            
            // cek kondisi simpan bumbu
            if($bumbu){
                $selectCustomer = $request->input('customer_id',[]);
                foreach ($selectCustomer as $customer) {
                    $customer_bumbu = new CustomerBumbu();
                    $customer_bumbu->status_bumbu    = 0;
                    $customer_bumbu->bumbu_id       = $bumbu->id;
                    $customer_bumbu->customer_id    = $customer;
                    $customer_bumbu->save();
                }
            }
        }

        if($request->key == 'tambah_customer')
        {
            $selectCustomer = $request->input('customer_id',[]);
            foreach($selectCustomer as $customer)
            {
                $customer_bumbu = new CustomerBumbu();
                $customer_bumbu->status_bumbu    = 0;
                $customer_bumbu->bumbu_id       = $request->bumbu_id;
                $customer_bumbu->customer_id    = $customer;
                $customer_bumbu->save();
            }
        }

        if ($request->key == 'bumbu_gudang') {
            // dd($request->all*);
            $bumbu_detail                       = new BumbuDetail();
            $bumbu_detail->bumbu_id             = $request->bumbu_id;
            $bumbu_detail->status               = $request->status;
            // $bumbu_detail->stock    = $request->stock;
            $bumbu_detail->berat                = $request->berat;
            $bumbu_detail->regu                 = ($request->status == 'masuk') ? null : $request->regu;
            $bumbu_detail->tanggal              = $request->tanggal;
            $bumbu_detail->bumbu_customer_id    = ($request->status == 'masuk') ? null : $request->customer_bumbu_id;
            $bumbu_detail->save();

            
                $add_bumbu = Bumbu::find($request->bumbu_id);
                if ($add_bumbu) {
                    if ($request->status == 'masuk') {
                        // $add_bumbu->stock = $add_bumbu->stock + $request->stock;
                        $add_bumbu->berat = $add_bumbu->berat + $request->berat;
                    } else {
                        // $add_bumbu->stock = $add_bumbu->stock - $request->stock;
                        $add_bumbu->berat = $add_bumbu->berat - $request->berat;
                    }
    
                    $add_bumbu->save();
                }
            


        }

        return back()->with('status', 1)->with('message', 'Bumbu berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->key == 'bumbu_admin') {
            $data = Bumbu::find($id);
            $customer = Customer::all();
            return view('admin.pages.bumbu.delete',compact('data','customer'));
        }
        if ($request->key == 'bumbu_gudang') {
            $bgudang = BumbuDetail::find($id);
            return view('admin.pages.bumbu.gudang.delete',compact('bgudang'));
        }
        if ($request->key == 'tambah_bumbu') {
            $bumbu = Bumbu::find($id);
            return $data = json_encode($bumbu);
        }
        if($request->key == 'delete_customer')
        {
            $customer = CustomerBumbu::with('customers')->find($id);
            return view('admin.pages.bumbu.customer.delete', compact('customer'));
        }

        if($request->key == 'tambah_customer'){
            $customer = Customer::all();
            return $data = json_encode($customer);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->key == 'edit_admin') {
            $edit = Bumbu::find($id);
            return view('admin.pages.bumbu.edit',compact('edit'));
        }
        if ($request->key == 'edit_gudang') {
            $edit = BumbuDetail::where('id',$id)->first();
            return view('admin.pages.bumbu.gudang.edit',compact('edit'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->key == 'bumbu_admin') {
            $update = Bumbu::find($id);
            $update->nama = $request->nama;
            $update->save();
            return back()->with('status', 1)->with('message', 'Bumbu berhasil diupdate');
        }
        if ($request->key == 'bumbu_gudang') {
            // dd($request->all());
            $record         = BumbuDetail::find($id);
            $hasil_berat    = $request->berat - $record->berat;
            $hasil_stock    = $request->stock - $record->stock;

                $update_master        = Bumbu::find($record->bumbu_id);
                if($record->status == "masuk"){
                    $update_master->stock = $update_master->stock + ($hasil_stock);
                    $update_master->berat = $update_master->berat + ($hasil_berat);
                    $update_master->save();
                } else {
                    $update_master->stock = $update_master->stock - ($hasil_stock);
                    $update_master->berat = $update_master->berat - ($hasil_berat);
                    $update_master->save();
                }

            $record->stock  = $request->stock;
            $record->berat  = $request->berat;
            $record->status = $request->status;
            $record->save();

            
            return back()->with('status', 1)->with('message', 'Bumbu Record berhasil diupdate');

        }

        if($request->key == "status_bumbu"){
            $status = CustomerBumbu::find($id);
            $status->status_bumbu = $request->status_bumbu;
            $status->save();

            return response()->json(["success"=>true, "message" => "Status Update", "data"=>$status]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->key == 'delete_admin') {
            $data = Bumbu::findOrFail($id);
            $data->delete();
            return back()->with('status', 1)->with('message', 'Bumbu berhasil dihapus');
        }
        if ($request->key == 'delete_gudang') {
            $record = BumbuDetail::findOrFail($id);
            $master = Bumbu::find($record->bumbu_id);
            if ($record->status == 'masuk') {
                $master->stock = $master->stock - $record->stock;
                $master->berat = $master->berat - $record->berat;
            }
            if ($record->status == 'keluar') {
                $master->stock = $master->stock + $record->stock;
                $master->berat = $master->berat + $record->berat;    
            }
            $master->save();
            $record->delete();
            return back()->with('status', 1)->with('message', 'Record Bumbu berhasil dihapus');
        }

        if($request->key == 'delete_customer'){
            $data = CustomerBumbu::findOrFail($id);
            $data->delete();
            return back()->with('status', 1)->with('message', 'Customer berhasil dihapus');
        }
        
    }

    public function getBumbu($customer_id)
    {
        $bumbu = Bumbu::whereHas('customer_bumbu', function ($query) use ($customer_id) {
            $query->where('customer_id', $customer_id)
            ->where('status_bumbu',1);
        })->get();

        return response()->json($bumbu);
    }

    public function download(Request $request)
    {
        

       
            if ($request->key == "unduh") {

                $search = $request->cari_item;

                $query = BumbuDetail::with(['bumbu', 'customer_bumbu.customers'])
                    ->whereNull('bumbu_details.deleted_at')
                    ->where(function ($sub) use ($search) {
                        $sub->whereHas('customer_bumbu.customers', function ($q) use ($search) {
                            $q->where('nama', 'like', '%' . $search . '%');
                        });
                        $sub->orWhereHas('bumbu', function ($q) use ($search) {
                            $q->where('nama', 'like', '%' . $search . '%');
                        });
                    });

                if (!empty($request->tanggal_awal) && !empty($request->tanggal_akhir)) {
                    $tgl_awal = $request->tanggal_awal;
                    $tgl_akhir = $request->tanggal_akhir;
                    
                    $query->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]);
                    $filename = "download-bumbu ".$tgl_awal."-".$tgl_akhir;
                }
                
                $data = $query->get();
                $filename = "download-bumbu ".Carbon::now()->format('Y-m-d');

                return view('admin.pages.bumbu.gudang.download-bumbu',compact('data','filename'));
            }

            // if($request->key == "cari_bumbu")
            // {
            //     $search = $request->cari_item;
    
            //     $query = BumbuDetail::with(['bumbu', 'customer_bumbu.customers'])
            //         ->whereNull('bumbu_details.deleted_at')
            //         ->where(function ($sub) use ($search) {
            //             $sub->whereHas('customer_bumbu.customers', function ($q) use ($search) {
            //                 $q->where('nama', 'like', '%' . $search . '%');
            //             });
            //             $sub->orWhereHas('bumbu', function ($q) use ($search) {
            //                 $q->where('nama', 'like', '%' . $search . '%');
            //             });
            //         });

            //     if (!empty($request->tanggal_awal) && !empty($request->tanggal_akhir)) {
            //         $tgl_awal = $request->tanggal_awal;
            //         $tgl_akhir = $request->tanggal_akhir;
                    
            //         $query->whereBetween('tanggal', [$tgl_awal, $tgl_akhir]);
            //     }

            //     $data = $query->get();

            //         return view('admin.pages.bumbu.gudang.cari-bumbu',compact('data'));   
            // }
    }
}

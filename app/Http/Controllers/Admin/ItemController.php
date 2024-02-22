<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Applib;
use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\BomItem;
use App\Models\Category;
use App\Models\DataOption;
use App\Models\Item;
use App\Models\PurchaseItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Excel;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(User::setIjin(17)){
            $q      =   $request->q ?? '';
            $category = Category::select('id','nama','slug')->whereNotNull('nama')->orderBy('nama','ASC')->get();
            $category_item = $request->category_item ?? '';
            $cari = $request->cari ?? '';
            $option = Applib::getIdOption('options','item_akses','option_value');
            $dataOption = explode(',',$option);
            $option = Applib::getIdOption('options','item_akses','option_value');
            $dataOption = explode(',',$option);
            if ($request->key == 'filter') {
                $data   =   Item::where(function($q) use ($request){
                                    if ($request->cari) {
                                        $q->orWhere('nama', 'like', '%'.$request->cari.'%');
                                        $q->orWhere('sku', 'like','%'.$request->cari.'%');
                                    }
                                    if ($request->category_item) {
                                        $q->orWhere('category_id',$request->category_item);
                                    }
                                })
                                ->orderBy('nama', 'ASC')->withTrashed()->paginate(15);
                
                
                    return view('admin.pages.items.item_data',compact('data','category','category_item','cari','dataOption'));
                
                
            } else if ($request->key == 'editItem') {
                $data   = Item::where('id', $request->id)->withTrashed()->first();
                return view('admin.pages.items.item_edit',compact('data','category'));

            } else if ($request->key == 'akses') {
                $id     = $request->id;
                $akses  = Category::whereIn('id', $dataOption)->get();
                $item   = Item::with('itemkat')->find($id);
                return view('admin.pages.items.item_akses',compact('akses','id','item'));
            } else if ($request->key == 'editYield') {
                
                // dd($request->all());

                if ($request->ukuranAyam == '11') {
                    $dataYield = '&lt; 1.1';
                } 
                else if ($request->ukuranAyam == '1113') {
                    $dataYield = '1.1 - 1.3';
                }
                else if ($request->ukuranAyam == '1214') {
                    $dataYield = '1.2 - 1.4';
                }
                else if ($request->ukuranAyam == '1215') {
                    $dataYield = '1.2 - 1.5';
                }
                else if ($request->ukuranAyam == '1315') {
                    $dataYield = '1.3 - 1.5';
                }
                else if ($request->ukuranAyam == '1316') {
                    $dataYield = '1.3 - 1.6';
                }
                else if ($request->ukuranAyam == '1416') {
                    $dataYield = '1.4 - 1.6';
                }
                else if ($request->ukuranAyam == '1417') {
                    $dataYield = '1.4 - 1.7';
                }
                else if ($request->ukuranAyam == '1517') {
                    $dataYield = '1.5 - 1.7';
                }
                else if ($request->ukuranAyam == '1518') {
                    $dataYield = '1.5 - 1.8';
                }
                else if ($request->ukuranAyam == '1618') {
                    $dataYield = '1.6 - 1.8';
                }
                else if ($request->ukuranAyam == '1719') {
                    $dataYield = '1.7 - 1.9';
                }
                else if ($request->ukuranAyam == '1820') {
                    $dataYield = '1.8 - 2.0';
                }
                else if ($request->ukuranAyam == '1921') {
                    $dataYield = '1.9 - 2.1';
                }
                else if ($request->ukuranAyam == '2022') {
                    $dataYield = '2.0 - 2.2';
                }
                else if ($request->ukuranAyam == '22') {
                    $dataYield = '2.2 up';
                }
                else if ($request->ukuranAyam == '2025') {
                    $dataYield = '2.0 - 2.5';
                }
                // else if ($request->ukuranAyam == '2025') {
                //     $dataYield = '2.0-2.5';
                // }
                // else if ($request->ukuranAyam == '2530') {
                //     $dataYield = '2.5 - 3.0';
                // }
                else if ($request->ukuranAyam == '2530') {
                    $dataYield = '2.5-3.0';
                }
                else if ($request->ukuranAyam == '30') {
                    $dataYield = '3.0 up';
                }
                else if ($request->ukuranAyam == '40') {
                    $dataYield = '4.0 up';
                }

                
                $data = Adminedit::where('activity', 'input_yield')
                            ->where('type', $dataYield)
                            ->where('content',  $request->jenisAyam)
                            ->first();

                            
                if ($data) {
                    $dataAvailable = true;
                    return view('admin.pages.items.item_yield',compact('data', 'dataAvailable'));
                } else {

                    $data   = PurchaseItem::where('ukuran_ayam', $dataYield)->where('jenis_ayam', $request->jenisAyam)->withTrashed()->first();
                    $dataAvailable = false;
                    return view('admin.pages.items.item_yield',compact('data', 'dataAvailable'));

                }


            } else if ($request->key == 'updateYield') {

                if ($request->ukuranAyam == '11') {
                    $dataYield = '&lt; 1.1';
                } 
                else if ($request->ukuranAyam == '1113') {
                    $dataYield = '1.1 - 1.3';
                }
                else if ($request->ukuranAyam == '1214') {
                    $dataYield = '1.2 - 1.4';
                }
                else if ($request->ukuranAyam == '1215') {
                    $dataYield = '1.2 - 1.5';
                }
                else if ($request->ukuranAyam == '1315') {
                    $dataYield = '1.3 - 1.5';
                }
                else if ($request->ukuranAyam == '1316') {
                    $dataYield = '1.3 - 1.6';
                }
                else if ($request->ukuranAyam == '1416') {
                    $dataYield = '1.4 - 1.6';
                }
                else if ($request->ukuranAyam == '1417') {
                    $dataYield = '1.4 - 1.7';
                }
                else if ($request->ukuranAyam == '1517') {
                    $dataYield = '1.5 - 1.7';
                }
                else if ($request->ukuranAyam == '1518') {
                    $dataYield = '1.5 - 1.8';
                }
                else if ($request->ukuranAyam == '1618') {
                    $dataYield = '1.6 - 1.8';
                }
                else if ($request->ukuranAyam == '1719') {
                    $dataYield = '1.7 - 1.9';
                }
                else if ($request->ukuranAyam == '1820') {
                    $dataYield = '1.8 - 2.0';
                }
                else if ($request->ukuranAyam == '1921') {
                    $dataYield = '1.9 - 2.1';
                }
                else if ($request->ukuranAyam == '2022') {
                    $dataYield = '2.0 - 2.2';
                }
                else if ($request->ukuranAyam == '22') {
                    $dataYield = '2.2 up';
                }

                else if ($request->ukuranAyam == '2025') {
                    $dataYield = '2.0 - 2.5';
                }
                else if ($request->ukuranAyam == '2530') {
                    $dataYield = '2.5-3.0';
                }

                else if ($request->ukuranAyam == '30') {
                    $dataYield = '3.0 up';
                }
                else if ($request->ukuranAyam == '40') {
                    $dataYield = '4.0 up';
                }
                
        
                // $cekData = Adminedit::where('type', 'ukuranAyam')->where('activity', 'input_yield')
                //             ->where('content',  $dataYield)
                //             ->first();

                $cekData = Adminedit::where('activity', 'input_yield')
                            ->where('type', $dataYield)
                            ->where('content',  $request->jenisAyam)
                            ->first();

                // UPDATE DATA
                if ($cekData) {
                    $cekData->data = json_encode([
                        'yield_karkas'   => $request->yield_karkas,
                        'yield_evis'     => $request->yield_evis
                    ]) ;
                    $cekData->save();

                } else {

                    // BUAT FIELD BARU JIKA TIDAK ADA DATA
                    $newData            = new Adminedit;
                    $newData->type      = $dataYield;
                    $newData->user_id   = Auth::user()->id;
                    $newData->activity  = 'input_yield';
                    $newData->content   = $request->jenisAyam;
                    $newData->data      = json_encode([
                        'yield_karkas'  => $request->yield_karkas,
                        'yield_evis'    => $request->yield_evis
                    ]) ;

                    $newData->save();
                }

    
                $return['status']   =   200;
                $return['data']     =   $request->all();
                $return['msg']      =   "Update berhasil";
                return $return;



            }else
            if ($request->key == 'download') {
                // return 'ok';
                $dataunduh = Item::where(function ($q) use ($request){
                                        if ($request->category_item) {
                                            $q->orWhere('category_id',$request->category_item);
                                        }
                                    })
                                    ->orderBy('nama','ASC')->withTrashed()->get();

                // $category_item = $request->category_item ?? '';
                return view('admin.pages.items.item_download',compact('dataunduh','category_item'));


            }else
            if ($request->key == 'yield_benchmark') {
                $category               = Category::where('id', '<=', 20)->get();
                $category_item          = $request->category_item ?? '';
                $cari                   = $request->cari ?? '';

                $sql                   =   PurchaseItem::select('ukuran_ayam', 'jenis_ayam')->groupBy('jenis_ayam')->groupBy('ukuran_ayam')->where('ukuran_ayam', '!=', 'NONE')->orderByRaw('jenis_ayam ,ukuran_ayam ASC')->get();
                $dataArray              = array();
                $dataArray2             = array();
                foreach($sql as $row){
                    if($row->jenis_ayam == 'Live Bird'){
                        $dataArray[] = array(
                            'ukuran_ayam'   => $row->ukuran_ayam,
                            'jenis_ayam'    => $row->jenis_ayam 
                        );
                    }else{
                        $dataArray2[] = array(
                            'ukuran_ayam'   => $row->ukuran_ayam,
                            'jenis_ayam'    => $row->jenis_ayam 
                        );
                    }
                    
                }
                $gabung = array_merge($dataArray,$dataArray2);
                $data   = $gabung ?? [];
                // dd($data);

                return view('admin.pages.items.yield_benchmark',compact('data','category','category_item','cari'));

                // $category_item = $request->category_item ?? '';
                // return view('admin.pages.items.yield_benchmark',compact('dataunduh','category_item'));
            }else {
                return view('admin.pages.items.item',compact('category','category_item'));
            }
            

            // $data   =   $data->filter(function($item) use($q){
            //                 $res = true;
            //                 if ($q != "") {
            //                     $res =  (false !== stripos($item->nama, $q)) ||
            //                             (false !== stripos($item->alamat, $q)) ||
            //                             (false !== stripos($item->summary_route, $q)) ||
            //                             (false !== stripos($item->summary_ekor, $q)) ||
            //                             (false !== stripos(($item->summary_berat ?? ''), $q));
            //                 }
            //                 return $res;
            //             });


            // return view('admin/pages/item', compact('data', 'q', 'category'));
        }
        return redirect()->route("index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(User::setIjin(17)){
            //
        }
        return redirect()->route("index");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(User::setIjin(17)){
            //
        }
        return redirect()->route("index");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(User::setIjin(17)){
            //
        }
        return redirect()->route("index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(User::setIjin(17)){
            //
        }
        return redirect()->route("index");
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
        if(User::setIjin(17)){

            // if ($request->key == 'update_yield') {
                // dd($request->all());

                // if ($request->ukuranAyam == '11') {
                //     $dataYield = '&lt; 1.1';
                // } 
                // else if ($request->ukuranAyam == '1113') {
                //     $dataYield = '1.1 - 1.3';
                // }
                // else if ($request->ukuranAyam == '1214') {
                //     $dataYield = '1.2 - 1.4';
                // }
                // else if ($request->ukuranAyam == '1315') {
                //     $dataYield = '1.3 - 1.5';
                // }
                // else if ($request->ukuranAyam == '1416') {
                //     $dataYield = '1.4 - 1.6';
                // }
                // else if ($request->ukuranAyam == '1417') {
                //     $dataYield = '1.4 - 1.7';
                // }
                // else if ($request->ukuranAyam == '1517') {
                //     $dataYield = '1.5 - 1.7';
                // }
                // else if ($request->ukuranAyam == '1518') {
                //     $dataYield = '1.5 - 1.8';
                // }
                // else if ($request->ukuranAyam == '1618') {
                //     $dataYield = '1.6 - 1.8';
                // }
                // else if ($request->ukuranAyam == '1719') {
                //     $dataYield = '1.7 - 1.9';
                // }
                // else if ($request->ukuranAyam == '1820') {
                //     $dataYield = '11.8 - 2.0';
                // }
                // else if ($request->ukuranAyam == '2022') {
                //     $dataYield = '2.0 - 2.2';
                // }

                // $cekData = Adminedit::where('type', 'ukuranAyam')->where('activity', 'input_yield')
                //             ->where('content',  $dataYield)
                //             ->first();
                
                // $dataJsonYield = [
                //     'yield_karkas' => $request->yield_karkas,
                //     'yield_evis'   => $request->yield_evis,
                // ];
                
                // // UPDATE DATA
                // if ($cekData) {
                //     $cekData->data = $dataJsonYield;
                //     $cekData->save();

                // } else {

                //     // BUAT FIELD BARU JIKA TIDAK ADA DATA
                //     $newData            = new Adminedit;
                //     $newData->type      = 'ukuranAyam';
                //     $newData->user_id   = Auth::user()->id;
                //     $newData->activity  = 'input_yield';
                //     $newData->content   = $dataYield;
                //     $newData->data      = $dataJsonYield;
                //     $newData->save();
                // }

    
                // $return['status']   =   200;
                // $return['msg']      =   "Update berhasil";
                // return $return;



            // } else {
                try {
                    // BOM ITEM QTY
                    $qtyAssembly     = $request->qty_assembly ?? NULL;
                    
                    $old  = Item::withTrashed()->find($id);
                    $data = Item::withTrashed()->find($id);
                    
                    $data->category_id              = $request->category_id;
                    $data->status                   = $request->status;
                    $data->sku                      = $request->sku;
                    $data->nama                     = $request->nama;
                    $data->subsidiary               = $request->subsidiary;
                    $data->code_item                = $request->code_item;
                    $data->netsuite_internal_id     = $request->netsuiteId;
                    $data->slug                     = Str::slug($request->nama);
    
    
                    if ($request->status == 0) {
                        $data->deleted_at = date('Y-m-d H:i:s');
                    } 
    
                    if ($request->status == 1) {
                        $data->deleted_at = NULL;
                    }
                    if ($qtyAssembly != NULL) {
                        $updateBomItem       = BomItem::where('sku', $request->sku)->get();
                        if ($updateBomItem) {
                            foreach ($updateBomItem as $bomItem) {
                                $bomItem->bom_qty_per_assembly        = $qtyAssembly;
                                $bomItem->qty_per_assembly            = $qtyAssembly;
                                $bomItem->qty_per_top_level_assembly  = $qtyAssembly;
                                $bomItem->save();
                            }
                        }
                    }
    
                    $data->save();
                    
                    $data_edit = [
                        'item_lama' => $old,
                        'item_baru' => $data,
                    ];
    
                    $editlog                = new Adminedit();
                    $editlog->user_id       = Auth::user()->id;
                    $editlog->table_name    = 'items';
                    $editlog->table_id      = $data->id;
                    $editlog->activity      = 'admin';
                    $editlog->content       = 'Edit Item';
                    $editlog->type          = 'edit';
                    $editlog->data          = json_encode($data_edit);
                    $editlog->status        = 1;
                    $editlog->save();
    
                    $return['status']   =   200;
                    $return['msg']      =   "Update berhasil";
                    return $return;
                } catch (\Throwable $th) {
                    $return['status']   =   400;
                    $return['msg']      =   $th->getMessage();
                    return $return;
                }
            // }
            
        }
        return redirect()->route("index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(User::setIjin(17)){
            //
        }
        return redirect()->route("index");
    }

    public function uploadItemExcel(Request $request)
    {
        if(User::setIjin(17)){
            if ($request->hasFile('file')) {
                $path = $request->file('file');

                $prod_import = Excel::toArray([],$path);

                $no_saved = 0;
                $no_unsaved = 0;
                $no_updated = 0;

                foreach ($prod_import[0] as $no => $row) {

                    
                        try{
                            
                            $data = Item::where('sku', $row[6])->first();

                            if($data){

                                $data->nama             = $row[1];
                                $data->sku              = $row[6];
                                $data->jenis            = $row[4];
                                
                                $category               = Category::where('nama', $row[10])->first();

                                $data->category_id      = $category->id ?? "22";
                                $data->slug             = strtolower(str_replace(" ", "-", preg_replace('/[^A-Za-z0-9 !@#$%^&*()-.]/u','', strip_tags(strtolower(str_replace(" ", "-", $row[1]))))));
                                $data->status           = '1';
    
                                $data->save();
                                $no_updated++;

                            }else{

                                if($no!="0"){

                                    $data = Item::where('nama', $row[1])->first();

                                    if($data){

                                        $data->nama             = $row[1];
                                        $data->sku              = $row[6];
                                        $data->jenis            = $row[4];
                                        
                                        $category               = Category::where('nama', $row[10])->first();
        
                                        $data->category_id      = $category->id ?? "22";
                                        $data->slug             = strtolower(str_replace(" ", "-", preg_replace('/[^A-Za-z0-9 !@#$%^&*()-.]/u','', strip_tags(strtolower(str_replace(" ", "-", $row[1]))))));
                                        $data->status           = '1';
            
                                        $data->save();
                                        $no_updated++;

                                    }else{
                                        
                                        $data = new Item;
                                        $data->nama             = $row[1];
                                        $data->sku              = $row[6];
                                        $data->jenis            = $row[4];
                                        
                                        $category               = Category::where('nama', $row[10])->first();
                                        $data->category_id      = $category->id ?? "22";

                                        $data->slug             = strtolower(str_replace(" ", "-", preg_replace('/[^A-Za-z0-9 !@#$%^&*()-.]/u','', strip_tags(strtolower(str_replace(" ", "-", $row[1]))))));
                                        $data->status           = '1';
            
                                        $data->save();
                                        $no_saved++;

                                    }

                                }
                                
                            }

                        }catch(\Throwable $th){
                            $no_unsaved++;
                        }


                }

                    return redirect($request->input('url'))
                    ->with('no_saved', $no_saved)
                    ->with('no_unsaved', $no_unsaved)
                    ->with('no_updated', $no_updated)
                    ->with('data', $prod_import[0])
                    ->with('status', 1)
                    ->with('message', "Data Saved!");


            }
        }
        return redirect()->route("index");
    }

    public function updateAccess(Request $request, $id)
    {
        $access = $request->input('akses',[]);
        $item = Item::findOrFail($id);

        if(empty($access)){
            $item->access = NULL;
        } else {
            $data = implode(",", $access);
            $item->access = $data;
        }
        $item->save();

        return response()->json([
            'message'   => "Data berhasil disimpan",
            'data'      => $item->access,
        ],201);
    }
}

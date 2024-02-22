<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\AppKey;
use App\Models\Chiller;
use App\Models\Evis;
use App\Models\Grading;
use App\Models\Item;
use App\Models\Lpah;
use App\Models\Netsuite;
use App\Models\Production;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TracingController extends Controller
{
    //
    public function index(Request $request){

        $item       = Item::where('category_id', '<=', 20)->get();
        $item_id    = $request->item_id;
        $bulan      = $request->bulan;
        return view('admin.pages.tracing.index', compact(['item', 'bulan', 'item_id']));
    }
}


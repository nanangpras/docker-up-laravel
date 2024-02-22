<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adminedit;
use App\Models\AppKey;
use App\Models\Category;
use App\Models\Hakakses;
use App\Models\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Session;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index(Request $request)
    {
        if(User::setIjin(21)){
            $mulai  = $request->mulai ?? date('Y-m-d');
            $akhir  = $request->akhir ?? date('Y-m-d');
            $data   =   User::all();
            $akses  =   UserRole::get();
            $logadmin = Adminedit::where('data','!=',null)
                                ->whereBetween(DB::raw('DATE(created_at)'), [$mulai,$akhir])
                                ->orderBy('created_at','desc')
                                ->paginate(15);
                                // ->get();
            // dd($logadmin);
            if ($request->key == "detail") {
                $id         = $request->id;
                $log_admin  = Adminedit::find($id);
                $category   = Category::all();
                // dd(json_decode($log_admin->data,true));
                return view('admin.pages.log.log-admin-detail',compact('log_admin','category'));

            }
            else if($request->key == 'filter'){
                return view('admin.pages.log.log-admin-filter',compact('logadmin'));
            }
            else{
                return view('admin.pages.users', compact('akses','logadmin','mulai','akhir'))->with('user', $data);
            }
        }
        return redirect()->route("index");
    }

    public function filterLogAdmin(Request $request)
    {
        $mulai  = $request->mulai;
        $akhir  = $request->akhir;
    }

    public function store(Request $request)
    {
        if(User::setIjin(21)){
            $request->validate([
                "nama"          =>  'required|string',
                "email"         =>  'required|email',
                "password"      =>  'required',
                "role"          =>  'required|in:superadmin,admin',
                "subsidiary"    =>  'required'
            ]);

            $user                   =   new User ;
            $user->name             =   $request->nama ;
            $user->email            =   $request->email ;
            $user->company_id       =   $request->subsidiary;
            $user->password         =   Hash::make($request->password) ;
            $user->account_role     =   $request->role ;
            $user->account_type     =   1 ;
            $user->status           =   1 ;
            $user->save() ;

            return back()->with('status', 1)->with('message', 'Tambah user berhasil') ;
        }
        return redirect()->route("index");
    }

    public function storehakakses(Request $request)
    {
        if(User::setIjin(21)){

            $user                   =   new Hakakses() ;
            $user->id               =   $request->id ;
            $user->function_name    =   $request->function ;
            $user->function_desc    =   $request->nama ;
            $user->status     =  1;
            $user->save() ;

            return back()->with('status', 1)->with('message', 'Tambah user berhasil') ;
        }
        return redirect()->route("index");
    }

    public function update(Request $request)
    {
        // dd($request->all());
        if(User::setIjin(21)){
            $request->validate([
                "x_code"    =>  ['required', Rule::exists('users', 'id')],
                "nama"      =>  'required|string',
                "email"     =>  'required|email',
                "role"      =>  'required'
            ]);

            $user                           =   User::find($request->x_code);
            $user->name                     =   $request->nama;
            $user->email                    =   $request->email;
            $user->password                 =   $request->password ? Hash::make($request->password) : $user->password ;
            $user->account_role             =   $request->role;
            $user->netsuite_internal_id     =   $request->netsuite_internal_id;
            $user->company_id               =   $request->subsidiary;
            $user->account_type             =   1 ;
            $user->save();

            return back()->with('status', 1)->with('message', 'Ubah user berhasil');
        }
        return redirect()->route("index");
    }

    public function akses(Request $request)
    {
        if(User::setIjin(21)){
            $user                   =   User::find($request->x_code) ;
            $user->group_role       =   collect($request->chk)->implode(',');
            $user->save();

            return back()->with('status', 1)->with('message', 'Ubah hak akses berhasil');
        }
        return redirect()->route("index");
    }

    public function destroy($id)
    {
        if(User::setIjin(21)){
            //
        }
        return redirect()->route("index");
    }

    public function switchcolor()
    {
        $color = Session::get('color');
        if(empty($color)){
            Session::put('color', 'dark');
            return redirect(url()->previous());
        }else{
            if($color=='light'){
                Session::put('color', 'dark');
                return redirect(url()->previous());
            }else{
                Session::put('color', 'light');
                return redirect(url()->previous());
            }
        }

    }

    public function userOnlineStatus()
    {
        $users = User::all();
        foreach ($users as $user) {
            if (Cache::has('user-is-online-' . $user->id))
                echo $user->name . " is online. Last seen: " . Carbon::parse($user->last_seen)->diffForHumans() . " <br>";
            else
                echo $user->name . " is offline. Last seen: " . Carbon::parse($user->last_seen)->diffForHumans() . " <br>";
        }
    }

    public function profile (Request $request){
        if($request->x_code){
            $data                   =   User::find($request->x_code);
            $data->name             =   $request->nama ;
            $data->email            =   $request->email ;
            if($request->password !== ''){
                $data->password     =   Hash::make($request->password) ;
            }

            $data->save();
            return back()->with('status', 1)->with('message', 'Ubah data berhasil');

        } else {
            $data   =   User::find(Auth::user()->id);
            return view('admin.pages.profile', compact('data'));
        }
    }
}

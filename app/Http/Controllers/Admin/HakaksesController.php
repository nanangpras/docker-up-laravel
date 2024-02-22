<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hakakses;
use App\Models\User;
use Illuminate\Http\Request;

class HakaksesController extends Controller
{

    public function index()
    {
        if(User::setIjin(21)){
            $data   =   Hakakses::paginate(30);

            return view('admin/pages/hakakses',compact('data'));
        }
        return redirect()->route("index");
    }


    public function create()
    {
        if(User::setIjin(21)){
            //
        }
        return redirect()->route("index");
    }

    public function store(Request $request)
    {
        if(User::setIjin(21)){
            //
        }
        return redirect()->route("index");
    }

    public function show($id)
    {
        if(User::setIjin(21)){
            //
        }
        return redirect()->route("index");
    }

    public function edit($id)
    {
        if(User::setIjin(21)){
            //
        }
        return redirect()->route("index");
    }

    public function update(Request $request, $id)
    {
        if(User::setIjin(21)){
            //
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
}

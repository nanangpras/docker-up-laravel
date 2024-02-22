<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use Illuminate\Http\Request;

class BomController extends Controller
{
    public function index()
    {
        $data   =   Bom::get();
        return view('admin.pages.bom.index', compact('data'));
    }
}

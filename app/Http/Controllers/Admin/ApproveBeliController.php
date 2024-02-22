<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApproveBeliController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pages.pembelian_barang.approve.index');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nekropsi;
use App\Models\Production;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    public function index()
    {
        $pdf    = PDF::loadView('admin.pages.pdf.pdfone');

        return $pdf->stream('pdf');
    }

    public function pdfnekropsi($id)
    {
        $nekropsi   =   Production::find($id);
        $isi        =   Nekropsi::where('production_id', $nekropsi->id)->first();

        $pdf    = PDF::loadView('admin.pages.pdf.nekropsi',compact('nekropsi','isi'));

        return $pdf->stream('pdf');
    }
}

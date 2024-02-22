<div class="row">

    <div class="col-6 mb-3">
        @if ($data->prodpur->type_po == 'PO LB')
            <div class="small"><b>SELISIH GRADING - LPAH</b></div>
            {{ number_format($total['ekor'] - $data->total_bersih_lpah) }} EKOR
        @else
            <div class="small"><b>JUMLAH AYAM</b></div>
            {{ number_format($total['ekor']) }} EKOR
        @endif
    </div>

</div>

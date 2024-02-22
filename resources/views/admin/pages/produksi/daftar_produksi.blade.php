@php
    $berat = 0;
@endphp
@foreach ($freestock as $item)
@php
    $berat += $item->qty;
@endphp
@php
    $exp    =   json_decode($item->label) ;
@endphp
<div class="border rounded p-1 mb-2">
    <div class="row">
        <div class="col-8 col-md-7 pr-1">
            {{ $item->item->nama }}
        </div>
        <div class="col-md col-4 text-right text-md-left px-md-1 pl-1">
            ({{ $item->qty }} Kg)
        </div>
        <div class="col-auto pl-1">
            <i class="fa fa-trash text-danger ml-3 hapus_produksi float-right p-1" data-id="{{ $item->id }}"></i>
        </div>
        <div class="col-12">
            <div class="border-top pt-1 mt-1"></div>
            
             <div class="status status-success">
                <div class="row">
                    <div class="col pr-1">
                        {{ $item->plastik_nama }}
                    </div>
                    <div class="col-auto pl-1">
                        <span class="float-right">// {{ $item->plastik_qty }} Pcs</span>
                    </div>
                </div>
            </div>
                    
                            <br>
            @if ($exp->parting->qty) Parting : {{ $exp->parting->qty }} <br> @endif
            @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Lemak, ' : '' }} {{ $exp->additional->maras ? 'Maras' : '' }} @endif
        </div>
    </div>
</div>
@endforeach
<div class="border rounded p-1 mb-2">
    <div class="row">
        <div class="col-8 col-md-7 pr-1">
            <b class="text-center">TOTAL</b>
        </div>
        <input type="hidden" name="beratprod" id="beratprod" value="{{ $berat }}">
        <div class="col-md col-4 text-right text-md-left px-md-1 pl-1">
           <b>{{ $berat }} Kg</b>
        </div>
    </div>
</div>

@if (COUNT($freestock))
<button type="submit" data-jenis="{{ $regu }}" class="btn btn-primary selesaikan btn-lg btn-block mt-3">
    Selesaikan
</button>
@endif

@if (count($freestock)>0)
@php
    $berat = 0;
@endphp
<table class="table default-table table-small tabel-hp">
    <thead>
        <th>Nama</th>
        <th>Ekor/Pcs/Pack</th>
        <th>Berat</th>
        <th>#</th>
    </thead>
    <tbody>
        @php
            $qty = 0;
            $berat = 0;
        @endphp
        @foreach ($freestock as $item)
            @php
                $qty += $item->qty;
                $berat += $item->berat;
                $exp    =   json_decode($item->label) ;
            @endphp
            <tr>
                <td>
                    @if($item->kategori=="1")
                    <span class="status status-danger">[ABF]</span>
                    @elseif($item->kategori=="2")
                    <span class="status status-warning">[EKSPEDISI]</span>
                    @elseif($item->kategori=="3")
                    <span class="status status-warning">[TITIP CS]</span>
                    @else
                    <span class="status status-info">[CHILLER]</span>
                    @endif
                    {{ $item->item->nama ?? ''}}</td>
                <td>{{ $item->qty }}</td>
                <td>{{ $item->berat }} Kg</td>
                <td> <i class="fa fa-trash ml-2 hapus_produksi text-danger" style="cursor:pointer;" data-id="{{ $item->id }}"></i> <input type="hidden" class="id_hp" value="{{$item->id}}"></td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="row">
                        <div class="col pr-1">
                            @if ($item->kode_produksi)
                                <div>Kode Produksi : {{ $item->kode_produksi }}</div>
                            @endif
                            @if ($item->keranjang)
                                <div>{{ $item->keranjang }} Keranjang</div>
                            @endif
                        </div>
                        <div class="col pl-1 text-right">
                            @if ($item->unit)
                                Unit : {{ $item->unit }}
                            @endif
                        </div>
                    </div>

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
            

                    @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                    <div class="row pt-1 text-info">
                        <div class="col pr-1">
                            @if ($item->customer_id) <div>Customer : {{ $item->konsumen->nama ?? '#' }}</div> @endif
                            @if ($exp->sub_item) <div>Keterangan : {{ $exp->sub_item }}</div> @endif
                        </div>
                        <div class="col pl-1 text-right">
                            @if ($item->selonjor) <div class="text-danger font-weight-bold">SELONJOR</div> @endif
                            @if ($exp->parting->qty) Parting : {{ $exp->parting->qty }} @endif
                        </div>
                    </div>

                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <th>{{ $qty }} Ekor/Pcs/Pack</th>
            <th>{{ $berat }} Kg</th>
            <th><input id="beratprod" value="{{$berat}}" type="hidden"></th>
        </tr>
    </tfoot>
</table>
@else
<div class="alert alert-danger">Item hasil produksi belum dipilih</div>
@endif

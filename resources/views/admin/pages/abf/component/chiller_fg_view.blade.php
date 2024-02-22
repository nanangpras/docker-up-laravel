<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total QTY</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ number_format($sumqty) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total Berat</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ number_format($sumberat, 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="bg-warning p-2 text-center text-light font-weight-bold text-uppercase">Sisa QTY</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ number_format($sisaqty) }}</h5>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <div class="bg-warning p-2 text-center text-light font-weight-bold text-uppercase">Sisa Berat</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ number_format($sisaberat, 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="bg-danger p-2 text-center text-light font-weight-bold text-uppercase">Masuk ABF QTY
                    </div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ $inboundqtyabf }}</h5>
                        {{-- <h5 class="mb-0">{{ number_format($sumqty - $sisaqty) }}</h5> --}}
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="bg-danger p-2 text-center text-light font-weight-bold text-uppercase">Masuk ABF Berat
                    </div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ number_format($inboundberatabf,2) }}</h5>
                        {{-- <h5 class="mb-0">{{ number_format(($sumberat - $sisaberat),2) }}</h5> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<table class="table default-table" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Asal</th>
            <th>Ekor/Pcs/Pack Awal</th>
            <th>Berat Awal (Kg)</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat (Kg)</th>
            <th>Tanggal Bahan Baku</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
       
        @foreach ($chiller_fg as $i => $chill)
            @php 
                $sisaQtyChiller        = $chill->sisaQty;
                // $sisaBeratChiller      = $chill->sisaBerat;  
                $sisaBeratChiller      = number_format((float)$chill->sisaBerat, 2, '.', '');  
                
            @endphp
            <tr>
                <td>{{$loop->iteration+($chiller_fg->currentpage() - 1) * $chiller_fg->perPage()}}</td>
                <td>
                    <div class="float-right text-secondary small">CH-{{ $chill->id }}</div>
                    {{ $chill->item_name }}
                    @if($chill->kategori=="1")
                    <span class="status status-danger">[ABF]</span>
                    @elseif($chill->kategori=="2")
                    <span class="status status-warning">[EKSPEDISI]</span>
                    @elseif($chill->kategori=="3")
                    <span class="status status-warning">[TITIP CS]</span>
                    @else
                    <span class="status status-info">[CHILLER]</span>
                    @endif
                    @if ($chill->selonjor)
                    <br><span class="text-danger font-weight-bold">SELONJOR</span>
                    @endif
                    @php
                    $exp = json_decode($chill->label ?? false);
                    $expByProduct = json_decode($chill->chillertofreestocktemp->label ?? false);
                    @endphp


                    @if ($chill->customer_id)
                    <br><span class="text-info">Customer : {{ $chill->customer_name }}</span>
                    @endif


                    @if($chill->regu == 'byproduct')

                    @if ($expByProduct)<br>
                    <div class="status status-success">
                        <div class="row">
                            <div class="col pr-1">
                                {{ $chill->chillertofreestocktemp->plastik_nama ?? "" }}
                            </div>
                            <div class="col-auto pl-1">
                                <span class="float-right">// {{ $chill->chillertofreestocktemp->plastik_qty ?? "" }}
                                    Pcs</span>
                            </div>
                        </div>
                    </div>
                    @if (isset($expByProduct->additional)) {{ $expByProduct->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                    {{ $expByProduct->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $expByProduct->additional->maras ?
                    'Tanpa Maras' : '' }} @endif
                    <div class="row mt-1 text-info">
                        <div class="col pr-1">@if (isset($expByProduct->sub_item)) Keterangan : {{ $expByProduct->sub_item }} @endif</div>
                        <div class="col-auto pl-1">@if (isset($expByProduct->parting->qty)) Parting : {{ $expByProduct->parting->qty }} @endif</div>
                    </div>
                    @endif
                    @else
                    @if ($exp)<br>
                    <div class="status status-success">
                        <div class="row">
                            <div class="col pr-1">
                                {{ $chill->plastik_nama ?? "" }}
                            </div>
                            <div class="col-auto pl-1">
                                <span class="float-right">// {{ $chill->plastik_qty ?? "" }} Pcs</span>
                            </div>
                        </div>
                    </div>
                    @if (isset($exp->additional)) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                    @endif
                    <div class="row mt-1 text-info">
                        <div class="col pr-1">@if (isset($exp->sub_item)) Keterangan : {{ $exp->sub_item }} @endif</div>
                        <div class="col-auto pl-1">@if (isset($exp->parting->qty)) Parting : {{ $exp->parting->qty }} @endif
                        </div>
                    </div>
                    @endif
                    @endif

                </td>
                <td>
                    @if($chill->regu)
                    @if($chill->regu=="frozen")
                    <span class="status status-danger">
                        {{$chill->regu}}
                    </span>
                    @elseif($chill->regu=="marinasi")
                    <span class="status status-warning">
                        {{$chill->regu}}
                    </span>
                    @elseif($chill->regu=="parting")
                    <span class="status status-info">
                        {{$chill->regu}}
                    </span>
                    @elseif($chill->regu=="whole")
                    <span class="status status-success">
                        {{$chill->regu}}
                    </span>
                    @elseif($chill->regu=="boneless")
                    <span class="status status-brown">
                        {{$chill->regu}}
                    </span>
                    @else
                    <span class="status status-other">
                        {{$chill->regu}}</span>
                    @endif
                    @endif
                </td>
                <td>{{ number_format($chill->qty_item) }}</td>
                <td class="text-right">{{ number_format($chill->berat_item, 2) }}
                <td>{{ $sisaQtyChiller }}</td>
                <td class="text-right">{{ $sisaBeratChiller }}
                </td>
                <td>{{ date('d/m/y', strtotime($chill->tanggal_produksi)) }}</td>
                <td>
                    @if($sisaBeratChiller <= 0) <br><span class="status status-danger">Dipindahkan</span>
                        @else
                        <br><span class="status status-info">Pending</span>
                        @endif
                        @if($chill->ambil_abf > 0)
                        <br><span class="status status-success mt-2">{{ $chill->ambil_abf ?? '' }}x Kirim ke ABF</span>
                        @endif
                        {{-- @if ($chill->status_cutoff == 1)
                        <br><span class="status status-danger">Transaksi sudah ditutup</span>
                        @endif --}}
                </td>
                <td>
                    @if ($chill->status_cutoff == 1)
                    <span class="status status-danger">Transaksi sudah ditutup</span>
                    <br>
                    @else
                    @if ($chill->status == 2)
                    @if($sisaBeratChiller > 0)
                    <div style="width:160px!important">
                        <div class="row mb-1">
                            <div class="col-6 pr-1">
                                <input type="number" id="kirim_jumlah{{ $chill->id }}" style="width:75px" placeholder="QTY"
                                    value="{{ $sisaQtyChiller }}"
                                    onkeyup="return changeInputQtyAbf({{ $chill->id }}, {{ $sisaQtyChiller }}, {{$sisaBeratChiller}})">
                            </div>
                            <div class="col-6 pl-1">
                                <input type="number" id="kirim_berat{{ $chill->id }}" step="0.01" style="width:75px"
                                    placeholder="Berat" value="{{ $sisaBeratChiller }}">
                            </div>
                        </div>
                        <div class="small">Tanggal Diterima</div>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_terima{{ $chill->id }}"
                            value="{{ $chill->tanggal_produksi }}">
                        <div><button type="submit" class="btn btn-primary btn-block toabf_fg mt-1"
                                data-chiller="{{ $chill->id }}" data-nama="{{ $chill->item_name }}">Kirim ke ABF</button>
                        </div>
                    </div>
                    @endif
                    @else
                    <button class="btn btn-success" disabled>Selesai</button>
                    @endif
                    @endif

                    <a href="{{ route('chiller.show', $chill->id) }}" class="btn btn-info mt-2">Detail</a>

                </td>
            </tr>
        @endforeach

    </tbody>
</table>

<div id="paginate_chiller_fg">
    {{ $chiller_fg->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    function changeInputQtyAbf(c_id, c_qty, c_berat){

    var berat_ratio = 0;
    var qty_input   = 0;
    if(c_qty!=0){


        qty_input = $('#kirim_jumlah'+c_id).val();

        berat_ratio = qty_input/c_qty*c_berat;

        $('#kirim_berat'+c_id).val(berat_ratio.toFixed(2));
    }
    console.log(berat_ratio.toFixed(2));
}

$('#paginate_chiller_fg .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#chiller_fg').html(response);
        }

    });
});
</script>
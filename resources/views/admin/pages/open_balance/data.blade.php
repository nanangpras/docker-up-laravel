@extends('admin.layout.template')

@section('title', 'DATA OPEN BALANCE')

@section('content')
<div class="my-4 text-center text-uppercase"><b>DATA OPEN BALANCE</b></div>

<section class="panel">
    <div class="card-body">
        <form action="{{ route('openbalance.data') }}" method="get">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-6">
                    Tanggal Awal
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date" name="tanggalawal" id="tanggalawal"
                        value="{{ $tanggalawal }}" placeholder="Cari...">
                </div>
                <div class="col-md-4 col-sm-4 col-6">
                    Tanggal Akhir
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date" name="tanggalakhir" id="tanggalakhir"
                        value="{{ $tanggalakhir }}" placeholder="Cari...">
                </div>
            </div>
        </form>
    </div>
</section>
<div class="card mt-4">
    <div class="card-body">
        <table width="100%" id="opbal" class="table default-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Ekor/Pcs/Pack</th>
                    <th>Berat (Kg)</th>
                    <th>Type Item</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                $qty = 0;
                $berat = 0;
                @endphp
                @foreach ($opbal as $i => $item)
                @php
                $qty += $item->qty;
                $berat += $item->berat;
                @endphp
                <tr>
                    <td>{{ ++$i }}</td>
                    <td> {{ $item->item->nama ?? "" }}
                    </td>
                    <td>{{ $item->gudang }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ number_format($item->qty) }}</td>
                    <td class="text-right">{{ number_format($item->berat, 2) }}</td>
                    <td>{{ $item->tipe_item }}</td>
                    <td>
                        @if($item->gudang == "chiller")
                        @php
                        $chiller = App\Models\Chiller::where('table_name', 'openbalance')->where('table_id',
                        $item->id)->first();
                        @endphp

                        @if($chiller)
                        {{ $chiller->label }}
                        @endif

                        @elseif ($item->gudang == "cold1" || $item->gudang == "cold2" || $item->gudang == "cold3" ||
                        $item->gudang == "cold4")
                        @php
                        $gudang = App\Models\Product_gudang::where('table_name', 'openbalance')->where('table_id',
                        $item->id)->first();
                        @endphp

                        @if($gudang)
                        {{ $gudang->label }}
                        @endif

                        @elseif ($item->gudang == 'abf')

                        @php
                        $abf = App\Models\Product_gudang::where('table_name', 'openbalance')->where('table_id',
                        $item->id)->first();
                        @endphp

                        @if($abf)
                        {{ $abf->label }}
                        @endif
                        @endif
                    </td>
                    <td>
                        @if($item->gudang=="chiller")
                        @php
                        $chiller = App\Models\Chiller::where('table_name', 'openbalance')->where('table_id',
                        $item->id)->first();
                        @endphp
                        @if($chiller)
                        <a href="{{ route('chiller.show', $chiller->id) }}" class="btn btn-info"
                            target="_blank">Detail</a>
                        @endif
                        @endif
                        <button class="btn btn-primary btn-sm edit-opbal" data-toggle="modal" data-target="#opbal-edit"
                            data-qty="{{$item->qty}}" data-id="{{$item->id}}" data-berat="{{$item->berat}}">edit
                        </button>
                        <form action="{{ route('openbalance.delete',$item->id) }}" method="POST"
                            style="display: inline-block;">
                            @method('delete')
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm" value="hapus"
                                onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data {{ $item->item->nama ?? '' }} ?')">
                                hapus
                            </button>
                        </form>
                        {{-- <a href="{{ route('openbalance.update',['key'=>'delete_opbal']) }}&idopen={{$item->id}}"
                            class="btn btn-danger btn-sm">hapus</a> --}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $('.edit-opbal').click(function () { 
        var id = $(this).attr('data-id');
        var qty = $(this).attr('data-qty');
        var berat = $(this).attr('data-berat');

        $('#id-opbal-edit').val(id);
        $('#form-edit-qty-opbal').val(qty);
        $('#form-edit-berat-opbal').val(berat);
        
    });
</script>
<div class="modal fade" id="opbal-edit" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="hasilLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hasilLabel">Edit Open Balance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('openbalance.update') }}" method="post">
                @csrf @method('patch')
                <input type="hidden" name="idopen" value="" id="id-opbal-edit">
                <div class="modal-body">

                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Ekor/Qty
                                <input type="number" name="qty" value="" class="form-control" id="form-edit-qty-opbal">
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                Berat
                                <input type="number" name="berat" value="" step="0.01" class="form-control"
                                    id="form-edit-berat-opbal">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.change-date').change(function() {
            var tanggalawal     = $('#tanggalawal').val();
            var tanggalakhir    = $('#tanggalakhir').val();
            $(this).closest("form").submit();
        });
</script>


@stop
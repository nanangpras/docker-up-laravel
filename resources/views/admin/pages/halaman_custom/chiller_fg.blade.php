@extends('admin.layout.template')

@section('title', 'WO-3 Custom')

@section('footer')
<script>
    $('.tanggal').change(function() {
            var form = $(this).closest("form");
            var hash = window.location.hash;
            form.attr("action", "{{ route('wo.chiller_fg') }}"+hash);
            form.submit();
        });
</script>
@endsection


@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col-6 py-1 text-center">
        <b>WO Chiller FG</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">

        <div class="card">
            <div class="card-body">
                <form action="{{ route('wo.chiller_fg') }}" method="GET">
                    <div class="form-group">
                        <label for="">Filter</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggal" class="form-control tanggal" id="tanggal"
                            value="{{ $tanggal }}" autocomplete="off">
                    </div>
                </form>
            </div>
        </div>

        <form method="post" action="{{route('wo.create_wo3')}}">
            @csrf
            <div class="form-group card-body">
                <label for="">Tanggal WO</label>
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif name="tanggal" class="form-control tanggal" id="tanggal" value="{{ $tanggal }}"
                    autocomplete="off">
            </div>

            <table class="table default-table" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat (Kg)</th>
                        <th>Tanggal Bahan Baku</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chiller_fg as $i => $chill)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $chill->item_name }}
                            @php
                            $exp = json_decode($chill->label);
                            @endphp

                            <div class="status status-success">
                                <div class="row">
                                    <div class="col pr-1">
                                        {{ $chill->plastik_nama }}
                                    </div>
                                    <div class="col-auto pl-1">
                                        <span class="float-right">// {{ $chill->plastik_qty }} Pcs</span>
                                    </div>
                                </div>
                            </div>


                            @if ($exp)<br>

                            @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">@if ($exp->sub_item ?? '') Customer : {{ $exp->sub_item }} @endif
                                </div>
                                <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif</div>
                            </div>
                            @endif
                        </td>
                        <td>{{ number_format($chill->qty_item) }} ekor</td>
                        <td class="text-right">{{ number_format($chill->berat_item, 2) }}</td>
                        <td>{{ $chill->tanggal_produksi }}</td>
                        <td>
                            <input type="hidden" name="chiller_id[]" value="{{$chill->id}}">
                            <input type="hidden" name="item_id[]" value="{{$chill->item_id}}">
                            <input type="hidden" name="item_name[]" value="{{$chill->item_name}}">
                            <input type="number" name="chiller_qty[]" id="kirim_jumlah{{ $chill->id }}"
                                class="form-input-table" placeholder="QTY" value="{{$chill->qty_item}}">
                            <input type="number" name="chiller_berat[]" id="kirim_berat{{ $chill->id }}" step="0.01"
                                class="form-input-table" placeholder="Berat" value="{{$chill->berat_item}}">
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>

            <button type="submit" class="btn btn-info">Simpan WO-3</button>
        </form>
    </div>
</section>
@stop

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
            $('#chillerfbtable').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY:        500,
                scrollX:        true,
                scrollCollapse: true,
                paging:         false,
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        } );
</script>
@stop
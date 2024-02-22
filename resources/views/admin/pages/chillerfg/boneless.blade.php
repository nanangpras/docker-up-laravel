<a href="{{ route('hasilproduksi.index', ['key' => 'unduh']) }}&tipe=boneless&tanggal={{ $tanggal }}" class="btn btn-success">Unduh</a>

<div class="table-responsive">
    <table width="100%" id="kategori" class="table default-table dataTable-boneless">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Asal Tujuan</th>
                <th>Berat Awal</th>
                <th>Berat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bonless as $i => $part)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $part->chillitem->nama }}
                        @php
                            $exp = json_decode($part->label);
                        @endphp

                        @if ($part->customer_id)<br><span class="text-info">Customer : {{ $part->konsumen->nama ?? '' }}</span> @endif

                        <div class="status status-success">
                            <div class="row">
                                <div class="col pr-1">
                                    {{ $part->plastik_nama }}
                                </div>
                                <div class="col-auto pl-1">
                                    <span class="float-right">// {{ $part->plastik_qty }} Pcs</span>
                                </div>
                            </div>
                        </div>
                        
                        @if($exp)<br>
                            @if ($exp->additional ?? FALSE) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                            <div class="row text-info">
                                <div class="col pr-1">@if ($exp->sub_item) Keterangan : {{ $exp->sub_item }} @endif</div>
                                <div class="col-auto pl-1 text-right">@if ($exp->parting->qty ?? "") Parting : {{ $exp->parting->qty }} @endif</div>
                            </div>
                        @endif
                    </td>
                    <td>{{ $part->tujuan }}</td>
                    <td>{{ number_format($part->berat_item, 2) }}</td>
                    <td>{{ number_format($part->stock_berat, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('.dataTable-boneless')) {
                $('.dataTable-boneless').DataTable().destroy();
            }
            $('.dataTable-boneless').DataTable({
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
@stop
<table width="100%" id="ppicsiaptable" class="table default-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
            <th>Tanggal BB</th>
            <th>Status</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($chiller_penyiapan as $i => $chill)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $chill->item_name }}

                @if($chill->kategori=="1")
                    <span class="status status-danger">[ABF]</span>
                    @elseif($chill->kategori=="2")
                    <span class="status status-warning">[EKSPEDISI]</span>
                    @elseif($chill->kategori=="3")
                    <span class="status status-warning">[TITIP CS]</span>
                    @else
                    <span class="status status-info">[CHILLER]</span>
                    @endif

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
                    
                    @if (isset($exp->additional)) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                    <div class="row mt-1 text-info">
                        <div class="col pr-1">@if ($exp->sub_item ?? '') Customer : {{ $exp->sub_item }} @endif</div>
                        <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif</div>
                    </div>
                @endif
            </td>
            <td>{{ number_format($chill->stock_item) }} ekor</td>
            <td class="text-right">{{ number_format($chill->stock_berat, 2) }}
            </td>
            <td>{{ $chill->tanggal_produksi }}</td>
            <td>
                @if($chill->stock_berat<=0)
                    <br><span class="status status-danger">Dipindahkan</span>
                @endif
            </td>
            <td>

                <a href="{{ route('chiller.show', $chill->id) }}" class="btn btn-info mt-1">Detail</a>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#ppicsiaptable').DataTable({
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

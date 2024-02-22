<form method="get" action="{{route('abf.stock')}}" id="filter-form-submit">
    <div class="row">
        <div class="col-md-3 col-6 mb-3">
            <label>Mulai</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control change-filter" name="mulai" value="{{$mulai}}">
        </div>
        <div class="col-md-3 col-6 mb-3">
            <label>Sampai</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control change-filter" name="sampai" value="{{$sampai}}">
        </div>
    </div>
</form>

<table class="table default-table" width="100%" id="LBabfTable">
    <thead>
        <tr>
            <th width="10px">No</th>
            <th>Nama</th>
            <th>Item</th>
            <th>Packaging</th>
            <th>Asal</th>
            <th>Tanggal</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>
                <div class="float-right text-secondary small">AF-{{ $row->id }}</div>
                {{ $row->item_name }}
                @if ($row->selonjor)
                <br><span class="text-danger font-weight-bold">SELONJOR</span>
                @endif
                @if ($row->table_name == 'chiller')
                @php
                $exp = json_decode($row->abf_chiller->label);
                @endphp

                @if ($row->customer_id)<br><span class="text-info">Customer : {{ $row->konsumen->nama ?? '' }}</span>
                @endif

                <div class="status status-success">
                    <div class="row">
                        <div class="col pr-1">
                            {{ $row->abf_chiller->plastik_nama }}
                        </div>
                        <div class="col-auto pl-1">
                            <span class="float-right">// {{ $row->abf_chiller->plastik_qty }} Pcs</span>
                        </div>
                    </div>
                </div>

                @if ($exp)<br>

                @if ($exp)
                @if (isset($exp->additional))
                {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                @endif
                @endif
                <div class="row mt-1 text-info">
                    <div class="col pr-1">@if ($exp->sub_item ?? '') Keterangan : {{ $exp->sub_item }} @endif</div>
                    <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif
                    </div>
                </div>
                @endif
                @endif

                @if ($row->table_name == 'free_stocktemp')
                @php
                $exp = json_decode($row->abf_freetemp->label ?? false);
                @endphp

                @if ($row->customer_id)<br><span class="text-info">Customer : {{ $row->konsumen->nama ?? '' }}</span>
                @endif

                <div class="status status-success">
                    <div class="row">
                        <div class="col pr-1">
                            {{ $row->plastik_nama }}
                        </div>
                        <div class="col-auto pl-1">
                            <span class="float-right">// {{ $row->plastik_qty }} Pcs</span>
                        </div>
                    </div>
                </div>
                @if ($exp)<br>

                @if ($exp->additional ?? FALSE) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                @endif
                <div class="row mt-1 text-info">
                    <div class="col pr-1">@if ($exp->sub_item ?? FALSE) @if ($exp->sub_item ?? '') Keterangan : {{ $exp->sub_item }} @endif @endif</div>
                    <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif
                    </div>
                </div>
                @endif
                @endif
            </td>
            <td>
                @if (strpos($row->item_name, 'FROZEN') !== false)
                <span class="status status-danger">FROZEN</span>
                @else
                <span class="status status-info">FRESH</span>
                @endif
            </td>
            <td>{{ $row->packaging }}</td>
            <td>
                @if($row->asal_tujuan=="kepala_produksi")
                <span class="status status-warning">Produksi</span>
                @elseif($row->asal_tujuan=="free_stock")
                <span class="status status-danger">ReguFrozen</span>
                @else
                <span class="status status-info">{{$row->asal_tujuan}}</span>
                @endif
            </td>
            <td>{{ date('d/m/Y', strtotime($row->tanggal_masuk)) }}</td>
            <td>{{ number_format($row->qty_item > 0 ? $row->qty_item : '0') }}</td>
            <td class="text-right">{{ number_format(($row->berat_item > 0 ? $row->berat_item : '0'), 2) }}</td>
            <td>
                @if($row->berat_awal!=$row->berat_item && $row->berat_item > 0)
                <span class="status status-success">Ditimbang Sebagian</span>
                @endif

                @if($row->berat_item <= 0) <span class="status status-danger">Selesai</span>
                    @endif

                    @if ($row->status == 3)
                    <span class="status status-other">Approval</span>
                    @endif

            </td>
            <td>
                @if ($row->status == 1)
                <a class="btn btn-primary btn-sm mb-1" href="{{ route('abf.timbang', $row->id) }}">Timbang</a>
                @if($row->berat_awal==$row->berat_item)
                <a class="red" href="{{ route('abf.batalkan', $row->id) }}">Batalkan</a>
                @endif

                @elseif ($row->status == 3)
                <a class="btn btn-danger btn-sm mb-1"
                    href="{{ route('abf.chiller_kirim_abf_acc', $row->id) }}">Approve</a>
                <a class="red" href="{{ route('abf.batalkan', $row->id) }}">Batalkan</a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    var url = "{{route('abf.stock')}}";

    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        url = $(this).attr('href');
        filterAbf();
    });

    $('#filter-form-submit').on('submit', function(e){
        e.preventDefault();
        url = $(this).attr('action')+"?"+$(this).serialize();
        console.log(url);
        filterAbf();
    })

    $('.change-filter').on('change', function(){
        $('#filter-form-submit').submit();
        filterAbf();
    })

    var abfFilterTimeout = null;  

    $('#search-filter').on('keyup', function(){

        if (abfFilterTimeout != null) {
            clearTimeout(abfFilterTimeout);
        }

        abfFilterTimeout = setTimeout(function() {
            abfFilterTimeout = null;  
            //ajax code
            $('#filter-form-submit').submit();
            filterAbf();
        }, 1000);  
    })


    function filterAbf(){
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#abf-stock').html(response);
            }

        });
    }

</script>

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#LBabfTable')) {
            $('#LBabfTable').DataTable().destroy();
        }
        $('#LBabfTable').DataTable({
            "bInfo": false,
            responsive: true,
            scrollY:        500,
            scrollX:        true,
            scrollCollapse: true,
            paging:         false,
        });
    });
</script>
@stop
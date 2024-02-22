<a href="{{route('warehouse.thawing',array_merge(['key' => 'unduh_thawing'],$_GET))}}" class="btn btn-primary d-inline float-left btn-sm"> <i class="fa fa-download"></i> unduh</a>
<table width="100%" class="table default-table" id="warehouseThawing">
    <thead>
        <tr>
            <th>No</th>
            <th>GudangID</th>
            <th>ThawingID</th>
            <th>Tanggal Request</th>
            <th>Tanggal Outbound</th>
            <th>Nama</th>
            <th>Sub</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat </th>
            <th width="150px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_qty = 0;
            $total_berat = 0;
        @endphp
        @foreach ($thawing as $i => $val)
            <tr>
                <td>{{ ++$i }}</td>
                <td>ID-{{ $val->item_id }}</td>
                <td>@if ($val->thawing_id) TH-{{ $val->thawing_id }} @endif</td>
                <td>{{ \App\Models\Thawinglist::tanggal_request_thawing($val->thawing_id)}}</td>
                {{-- <td>{{ $val->relasi_thawing->tanggal_request }}</td> --}}
                <td>{{ $val->created_at }}</td>
                <td>{{ $val->gudang->nama }}</td>
                <td>{{ $val->gudang->sub_item ?? 'Free Stock' }}</td>
                <td>{{ number_format($val->qty) ?: '0' }} ekor</td>
                <td>{{ number_format($val->berat, 2) ?: '0' }} Kg</td>
                <td>
                    @if ($val->status == 3)
                        <a href="{{ route('warehouse.edit', $val->item_id) }}" class="btn btn-success btn-sm  timbangkeluargudang">Konfirmasi</a>
                    @else
                        <a href="{{ url('admin/warehouse/tracing', $val->item_id) }}" class="btn btn-success btn-sm ">Detail</a>
                        
                    @endif
                </td>
            </tr>
            @php
                $total_qty += $val->qty;
                $total_berat += $val->berat;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">Total</td>
            <td>{{$total_qty}} ekor</td>
            <td>{{$total_berat}} Kg</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>

        var url = "{{route('warehouse.thawing')}}";

        $('.pagination a').on('click', function(e) {
            e.preventDefault();
            url = $(this).attr('href');
            filterWarehouseThawing();
        });

        $('#filter-form-submit-thawing').on('submit', function(e){
            e.preventDefault();
            url = $(this).attr('action')+"?"+$(this).serialize();
            console.log(url);
            filterWarehouseThawing();
        })

        $('.change-filter-thawing').on('change', function(){
            $('#filter-form-submit-thawing').submit();
            filterWarehouseThawing();
        })

        var searchThawingTimeout = null;  

        $('#search-filter-thawing').on('keyup', function(){
            if (searchThawingTimeout != null) {
                clearTimeout(searchThawingTimeout);
            }
            searchThawingTimeout = setTimeout(function() {
                searchThawingTimeout = null;  
                //ajax code
                $('#filter-form-submit-thawing').submit();
                filterWarehouseThawing();
            }, 1000);
        })

        function filterWarehouseThawing(){
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#warehouse-thawing').html(response);
                }

            });
        }

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#warehouseThawing')) {
                $('#warehouseThawing').DataTable().destroy();
            }
            $('#warehouseThawing').DataTable({
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


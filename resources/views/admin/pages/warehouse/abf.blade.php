<div class="">
    <form method="get" action="{{ route('warehouse.abf') }}" id="filter-form-submit-abf">
        <div class="row">
            <div class="col-md-3 col-6 mb-3">
                <label>Mulai</label>
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control change-filter-abf" name="mulai" value="{{ $mulai }}">
            </div>
            <div class="col-md-3 col-6 mb-3">
                <label>Sampai</label>
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control change-filter-abf" name="sampai" value="{{ $sampai }}">
            </div>
        </div>
    </form>
</div>

<table width="100%" class="table default-table" id="warehouseAbf">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Packaging</th>
            <th>Tanggal</th>
            <th>Ekor </th>
            <th>Berat</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($abf as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $row->item_name }}</td>
            <td>{{ $row->packaging }}</td>
            <td>{{ date('d/m/Y', strtotime($row->created_at)) }}</td>
            <td>{{ $row->qty_item ?: '0' }}</td>
            <td>{{ $row->berat_item ?: '0' }}</td>
            <td class="text-center">{{ $row->status_abf }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    var url = "{{ route('warehouse.abf') }}";

        $('.pagination a').on('click', function(e) {
            e.preventDefault();
            url = $(this).attr('href');
            filterWarehouseAbf();
        });

        $('#filter-form-submit-abf').on('submit', function(e) {
            e.preventDefault();
            url = $(this).attr('action') + "?" + $(this).serialize();
            console.log(url);
            filterWarehouseAbf();
        })

        $('.change-filter-abf').on('change', function() {
            $('#filter-form-submit-abf').submit();
            filterWarehouseAbf();
        })

        var searchFilterAbfTimeout = null;  

        $('#search-filter-abf').on('keyup', function() {
            if (searchFilterAbfTimeout != null) {
                clearTimeout(searchFilterAbfTimeout);
            }
            searchFilterAbfTimeout = setTimeout(function() {
                searchFilterAbfTimeout = null;  
                //ajax code
                $('#filter-form-submit-abf').submit();
                filterWarehouseAbf();
            }, 1000);  
        })

        function filterWarehouseAbf() {
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#warehouse-abf').html(response);
                }

            });
        }

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#warehouseAbf')) {
                $('#warehouseAbf').DataTable().destroy();
            }
            $('#warehouseAbf').DataTable({
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
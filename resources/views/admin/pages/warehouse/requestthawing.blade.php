<table width="100%" class="table default-table" id="warehouseRequestThawing">
    <thead>
        <tr>
            <th>No</th> 
            <th>ThawingID</th>
            <th>Tanggal Request</th>
            <th>Tanggal Input Request</th>
            <th>Item</th>
            <th>Item Thawing</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_qty = 0;
            $total_berat = 0;
            $total_item_setuju = 0;  
            $total_berat_setuju = 0; 
        @endphp
        @foreach ($thawing as $i => $row)
           
            <tr>
                <td>{{ ++$i }}</td>
                <td>TH-{{ $row->id }}</td>
                <td>{{ $row->tanggal_request ?? '' }}</td>
                <td>{{ $row->created_at }}</td>
                <td>
                    @foreach (json_decode($row->item) as $i => $item)
                        @php
                            $total_qty = 0; // Inisialisasi total_qty untuk setiap iterasi item
                            $total_berat = 0; // Inisialisasi total_berat untuk setiap iterasi item
                            if (!empty($row->thawing_list)) {
                                foreach ($row->thawing_list as $list) {
                                    $total_qty += $list->qty;
                                    $total_berat += $list->berat;
                                }
                            }
                    
                            if ($item->berat < $total_berat) {
                                $background_color   = '#ffff8d';
                                $color              = '#e65100';
                            } else {
                                $background_color   = '#dbeefd';
                                $color              = '#2196F3';
                            }
                        @endphp
                        <div class="border-bottom p-1">
                            {{ ++$i }}. {{ App\Models\Item::find($item->item)->nama }}
                                <span class="status status-success">{{ number_format($item->qty) }} Pcs</span>
                                <span class="status status" style="background-color: {{$background_color}}; color: {{$color}}">{{ number_format($item->berat, 2) }} kg</span>
                                @if ($item->keterangan )<span class="status status-warning">{{$item->keterangan ?? ''}}</span> @endif
                        </div>
                        
                        @php
                            $total_qty += $item->qty;
                            $total_berat += $item->berat;
                        @endphp
                    @endforeach
                </td>
                <td>
                    @if ($row->status != 1)
                        <div style="font-size: x-small">
                            @foreach ($row->thawing_list as $list)
                                <div class="border-bottom p-1">
                                    <div>{{ $list->gudang->nama ?? 'ITEM TIDAK ADA' }}</div>
                                    <br>
                                    <span class="p-1 status status-success">{{ number_format($list->qty) }} pcs</span> 
                                    
                                    @if($item->berat > $list->berat)
                                        <span class="p-1 status status-info">{{ number_format($list->berat, 2) }} kg</span>
                                    @else
                                        <span class="p-1 status" style="background-color: {{$background_color}}; color: {{$color}}">{{ number_format($list->berat, 2) }} kg</span>
                                    @endif
                                    <br>
                                    <br>
                                    Tanggal Pemenuhan: {{ $list->created_at }}
                                </div>
                                @php
                                    $total_item_setuju += $list->qty;
                                    $total_berat_setuju += $list->berat;
                                @endphp
                            @endforeach
                        </div>
                        <button class="status status-success mt-1">Selesai</button>
                    @endif
                </td>
                <td>
                    @if ($row->status == 1)
                        <a href="{{ route('warehouse.request_thawing', $row->id) }}" class="btn btn-warning">Ambil Stock</a>
                    @else
                        <a href="{{ route('warehouse.request_thawing', $row->id) }}?key=editThawing" class="btn btn-info">Reset / Edit</a>
                    @endif
                </td>
            </tr>

        @endforeach
    </tbody>
    
    <tfoot>
        <tr>
            <td colspan="4">Total</td>
            <td>
                <span class="status status-success">Total pcs : {{$total_qty}} Pcs</span>
                <span class="status status-info">Total berat : {{$total_berat}} kg</span>
            </td>
            <td>
                <span class="status status-success">Total pcs : {{$total_item_setuju}} Pcs</span>
                <span class="status status-info">Total berat : {{$total_berat_setuju}} kg</span>
            </td>
        </tr>
    </tfoot>
</table>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        var url = "{{ route('warehouse.requestthawing') }}";

        $('.pagination a').on('click', function(e) {
            e.preventDefault();
            url = $(this).attr('href');
            filterWarehouseRequestThawing();
        });

        $('#filter-form-submit-requestthawing').on('submit', function(e) {
            e.preventDefault();
            url = $(this).attr('action') + "?" + $(this).serialize();
            console.log(url);
            filterWarehouseRequestThawing();
        })

        $('.change-filter-requestthawing').on('change', function() {
            $('#filter-form-submit-requestthawing').submit();
            filterWarehouseRequestThawing();
        })

        var requestThawingTimeout = null;  

        $('#search-filter-requestthawing').on('keyup', function() {
            if (requestThawingTimeout != null) {
                clearTimeout(requestThawingTimeout);
            }
            requestThawingTimeout = setTimeout(function() {
                requestThawingTimeout = null;  
                //ajax code
                $('#filter-form-submit-requestthawing').submit();
                filterWarehouseRequestThawing();
            }, 1000);  
        })

        function filterWarehouseRequestThawing() {
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#warehouse-requestthawing').html(response);
                }

            });
        }

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#warehouseRequestThawing')) {
                $('#warehouseRequestThawing').DataTable().destroy();
            }
            $('#warehouseRequestThawing').DataTable({
                "bInfo"         :   false,
                responsive      :   true,
                scrollY         :   500,
                scrollX         :   true,
                scrollCollapse  :   true,
                paging          :   false,
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });

        $('.edit-bb-open').on('click', function() {
            var id      =   $(this).attr('data-id');
            var nama    =   $(this).attr('data-nama');
            var qty     =   $(this).attr('data-qty');
            var berat   =   $(this).attr('data-berat');

            $('#form-edit-id').val(id);
            $('#form-edit-qty').val(qty);
            $('#form-edit-berat').val(berat);
            $('#selected_item').val(nama).change();

            $("#selected_item option[value='" + nama + "']").prop('selected', true).trigger('change');

        })

        $('.select2').select2({
            theme: 'bootstrap4',
            dropdownParent: "#edit-thawing"
        });
    </script>
@stop

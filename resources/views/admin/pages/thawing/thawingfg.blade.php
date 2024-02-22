<table width="100%" class="table default-table" id="thawingfg">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Tanggal (Inbound)</th>
            <th rowspan="2">Konsumen / Sub Item</th>
            <th rowspan="2">Item</th>
            <th class="text-center" colspan="2">Kemasan</th>
            <th rowspan="2">Qty</th>
            <th rowspan="2">Berat</th>
            <th rowspan="2">Aksi</th>
        </tr>
        <tr>
            <th>Packaging</th>
            <th>SubPack</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock as $i => $row)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $row->production_date }}</td>
                <td>{{ $row->konsumen->nama ?? "" }}<br>@if ($row->sub_item) Keterangan : {{ $row->sub_item }} @endif</td>
                <td>
                    {{ $row->productitems->nama ?? '' }}
                    @if ($row->selonjor)
                    <div class="font-weight-bold text-danger">SELONJOR</div>
                    @endif
                    @if ($row->barang_titipan)
                    <div class="font-weight-bold text-primary">BARANG TITIPAN</div>
                    @endif
                </td>
                <td>{{ $row->packaging }}</td>
                <td>{{ $row->subpack }}</td>
                <td class="text-right">{{ number_format($row->qty) }}</td>
                <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal" data-target="#modal{{ $row->id }}">
                        <i class="fa fa-edit"></i>
                    </button>
                </td>
            </tr>

        @endforeach
    </tbody>
</table>

@foreach ($stock as $i => $row)
    <div class="modal fade" id="modal{{ $row->id }}" tabindex="-1"
        aria-labelledby="modal{{ $row->id }}Label" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('warehouse.postthawingfg') }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal{{ $row->id }}Label">THAWING KELUAR</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="idgudang" value="{{ $row->id }}">
                        <div class="form-group">
                            <label for="item_label{{ $row->id }}">Produk Item</label>
                            <div id="item_label{{ $row->id }}">
                                <b>{{ $row->productitems->nama ?? '' }}</b>
                                @if ($row->selonjor)
                                <span class="font-weight-bold text-danger">SELONJOR</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col pr-1">
                                    <label for="package{{ $row->id }}">Packaging</label>
                                    <div class="font-weight-bold" id="package{{ $row->id }}">{{ $row->packaging }}</div>
                                </div>
                                <div class="col pl-1">
                                    <label for="subpackage{{ $row->id }}">Sub Packaging</label>
                                    <div class="font-weight-bold" id="subpackage{{ $row->id }}">{{ $row->subpack ?? "TIDAK ADA" }}</div>
                                </div>
                            </div>
                        </div>
                        {{-- <label for="">Packaging : {{ $row->packaging }}</label><br> --}}
                        {{-- <label for="">Sub Packaging : {{ $row->subpack }}</label><br> --}}
                        <div class="border p-2 mt-3">
                            <h6>Jumlah Thawaing</h6>
                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        <div class="bg-light small text-center py-1">QTY</div>
                                        <input type="number" placeholder="Qty Item" name="qty" class="rounded-0 form-control" autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="col pl-1">
                                    <div class="form-group">
                                        <div class="bg-light small text-center py-1">BERAT</div>
                                        <input type="number" placeholder="Berat Item" name="berat" class="rounded-0 form-control" autocomplete="off" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">OK</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        var url = "{{ route('warehouse.thawingfg') }}";

        $('.pagination a').on('click', function(e) {
            e.preventDefault();
            url = $(this).attr('href');
            filterWarehouseThawingfg();
        });

        $('#filter-form-submit-thawingfg').on('submit', function(e) {
            e.preventDefault();
            url = $(this).attr('action') + "?" + $(this).serialize();
            console.log(url);
            filterWarehouseThawingfg();
        })

        $('.change-filter-thawingfg').on('change', function() {
            $('#filter-form-submit-thawingfg').submit();
            filterWarehouseThawingfg();
        })

        var thawingTimeout = null;  

        $('#search-filter-thawingfg').on('keyup', function() {
            if (thawingTimeout != null) {
                clearTimeout(thawingTimeout);
            }
            thawingTimeout = setTimeout(function() {
                thawingTimeout = null;  
                //ajax code
                $('#filter-form-submit-thawingfg').submit();
                filterWarehouseThawingfg();
            }, 1000); 
        })

        function filterWarehouseThawingfg() {
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#warehouse-thawingfg').html(response);
                }

            });
        }

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#thawingfg')) {
                $('#thawingfg').DataTable().destroy();
            }
            $('#thawingfg').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY: 500,
                scrollX: true,
                scrollCollapse: true,
                paging: false,
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
@stop

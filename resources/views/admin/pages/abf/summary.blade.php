<section class="panel">
    <div class="card-body">
        <div class="float-right mb-3">
            <a href="{{ route('abf.index', array_merge(['key' => 'summary', 'get' => 'unduh'], $_GET)) }}" class="btn btn-success">Unduh</a>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total QTY</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ number_format($qty) }}</h5>
                    </div>
                </div>
            </div>
        
            <div class="col">
                <div class="form-group">
                    <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total Berat</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ number_format($berat, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div style="overflow-x:auto;">
    <table class="table default-table" width="100%">
        <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">ID ABF</th>
                <th rowspan="2">Item</th>
                <th rowspan="2">Customer</th>
                <th rowspan="2">Tanggal Bongkar</th>
                <th rowspan="2">Tanggal Produksi</th>
                <th rowspan="2">Gudang</th>
                <th rowspan="2">SubItem</th>
                <th rowspan="2">Packaging</th>
                <th class="text-center" colspan="2">Karung</th>
                <th class="text-center" colspan="2">Tanggal Kemasan</th>
                <th rowspan="2">Kode Produksi</th>
                <th rowspan="2">Qty Awal</th>
                <th rowspan="2">Berat Awal</th>
                <th rowspan="2">Qty</th>
                <th rowspan="2">Berat</th>
                <th rowspan="2">Pallete</th>
                <th rowspan="2">Expired</th>
                <th rowspan="2">Stock</th>
                <th rowspan="2">#</th>
            </tr>
            <tr>
                <th>Nama</th>
                <th>Jumlah</th>
                <th>Isi</th>
                <th>Kemasan</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($summary as $i => $row)
            @php
                $totalGradingUlang = App\Models\Product_gudang::where('notes', 'grading_ulang')->where('table_id', $row->id)->where('table_name', 'product_gudang')->count();
            @endphp
            <tr>
                <td>{{ $loop->iteration + ($summary->currentpage() - 1) * $summary->perPage() }}</td>
                <td>@if ($row->gudangabf)<a href="{{ route('abf.timbang', $row->gudangabf->id ?? '#') }}">{{ $row->gudangabf->id ?? "#" }} </a> @else <a href="{{ route('abf.timbang', $row->table_id ?? '#') }}">{{ $row->table_id ?? "#" }} </a>   @endif </td>
                <td><a href="{{route('warehouse.tracing', ['id' => $row->id])}}" target="_blank">{{ $row->productitems->nama ?? ""}} @if($row->gudangabf) @if($row->gudangabf->grade_item != NULL) <br> <span class="text-primary font-weight-bold uppercase"> // Grade B @endif @endif</span></a></td>
                <td>{{ $row->konsumen->nama ?? ""}}</td>
                <td>{{ $row->production_date ?? "" }}</td>
                <td>{{ $row->gudangabf->tanggal_masuk ?? "" }}</td>
                <td>{{ $row->productgudang->code ?? "" }}</td>
                <td>{{ $row->sub_item ?? "" }}</td>
                <td>{{ $row->packaging }}</td>
                <td>{{ App\Models\Item::item_sku($row->karung)->nama ?? "#" }} || {{ $row->karung_qty }}</td>
                <td>{{ $row->karung_qty }}</td>
                <td>{{ $row->karung_isi }}</td>
                <td>{{ $row->tanggal_kemasan ? date('d/m/y', strtotime($row->tanggal_kemasan)) : '###' }}</td>
                <td>{{ $row->production_code }}</td>
                <td>{{ number_format($row->gudangabf->qty_awal) }}</td>
                <td>{{ number_format($row->gudangabf->berat_awal, 2) }}</td>
                <td>{{ number_format($row->qty_awal) }}</td>
                <td>{{ number_format($row->berat_awal, 2) }}</td>
                <td>{{ number_format($row->palete) }}</td>
                <td>{{ number_format($row->expired) }} Bulan</td>
                <td>{{ $row->stock_type }}</td>
                {{-- @if ($row->gudangabf) --}}
                <td>
                    <a href="{{route('warehouse.tracing', $row->id)}}" class="btn btn-sm btn-blue" target="_blank">Detail</a>
                    {{-- @if ($row->berat > 0)
                    <br>
                    <a href="{{ route('abf.index', ['key' => 'warehouseGrading']) }}&id={{ $row->id }}" class="btn bnt-sm btn-info mt-2 mb-2"></a>
                    @endif

                    @if (isset($totalGradingUlang))
                        @if ($totalGradingUlang > 0)
                        <br>
                        <span class="status status-info mt-2">{{ $totalGradingUlang }}  x </span>
                        @endif
                    @endif --}}

                </td>
                {{-- @else
                <td>
                    <a href="{{route('abf.tracing', $row->table_id) }}" class="btn btn-sm btn-blue" target="_blank">Detail</a>
                    @if ($row->berat > 0)
                    <br>
                    <a href="{{ route('abf.index', ['key' => 'warehouseGrading']) }}&id={{ $row->id }}" class="btn bnt-sm btn-info mt-2 mb-2"></a>
                    @endif

                    @if (isset($totalGradingUlang))
                        @if ($totalGradingUlang > 0)
                        <br>
                        <span class="status status-info mt-2">{{ $totalGradingUlang }} x </span>
                        @endif
                    @endif
                </td>
                
                @endif --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="paginate_summary">
    {{ $summary->appends($_GET)->links() }}
</div>

<script>
$('#paginate_summary .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#summary_data').html(response);
        }

    });
});
</script>

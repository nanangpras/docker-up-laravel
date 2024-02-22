<section class="panel">
    <div class="card-body">
        <div class="float-right mb-3">
            <a href="{{ route('abf.index', array_merge(['key' => 'summaryGradingUlang', 'get' => 'unduh'], $_GET)) }}" class="btn btn-success">Unduh</a>
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
<section class="panel">
    <div class="card-body table-responsive">
        <div style="overflow-x:auto;">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">Item</th>
                        <th rowspan="2">Customer</th>
                        <th rowspan="2">Tanggal Produksi</th>
                        <th rowspan="2">Gudang</th>
                        <th rowspan="2">SubItem</th>
                        <th rowspan="2">Packaging</th>
                        <th class="text-center" colspan="2">Karung</th>
                        <th class="text-center" colspan="2">Tanggal Kemasan</th>
                        <th rowspan="2">Qty Awal</th>
                        <th rowspan="2">Berat Awal</th>
                        <th rowspan="2">Qty</th>
                        <th rowspan="2">Berat</th>
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
                <tbody class="accordion" id="accordionSummaryGradingUlang" style="border-bottom: none;">
                    @foreach ($summary as $i => $row)
                    @php
                        $getProductGudangAsal   = App\Models\Product_gudang::where('id', $row->table_id)->first();   
                        $ns                     = App\Models\Netsuite::where('tabel_id', $row->id)
                                                    ->where(function($query) {
                                                        $query->where('tabel', 'reGrading');
                                                    })
                                                    ->get();
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration + ($summary->currentpage() - 1) * $summary->perPage() }}</td>
                        <td>{{ $row->productitems->nama ?? ""}} @if($row->gudangabf) @if($row->gudangabf->grade_item != NULL) <br> <span class="text-primary font-weight-bold uppercase"> // Grade B @endif @endif</span></td>
                        <td>{{ $row->konsumen->nama ?? ""}}</td>
                        <td>{{ $row->production_date ?? "" }}</td>
                        <td>{{ $row->productgudang->code ?? "" }}</td>
                        <td>{{ $row->sub_item ?? "" }}</td>
                        <td>{{ $row->packaging }}</td>
                        <td>{{ App\Models\Item::item_sku($row->karung)->nama ?? "Curah" }} || {{ $row->karung_qty }}</td>
                        <td>{{ $row->karung_qty }}</td>
                        <td>{{ $row->karung_isi }}</td>
                        <td>{{ $row->tanggal_kemasan ? date('d/m/y', strtotime($row->tanggal_kemasan)) : '###' }}</td>
                        <td>{{ number_format($row->qty_awal) }}</td>
                        <td>{{ number_format($row->berat_awal, 2) }}</td>
                        <td>{{ number_format($row->qty) }}</td>
                        <td>{{ number_format($row->berat, 2) }}</td>
                        <td>{{ number_format($row->expired) }} Bulan</td>
                        <td>{{ $row->stock_type }}</td>
                        <td>
                            @if (count($ns) > 0) 
                            <button class="btn btn-primary" data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true" aria-controls="collapse{{ $row->id }}">Expand Detail</button>
                            @else
                            <button class="btn btn-success sendNSGradingUlang" data-id="{{ $row->id }}" aria-expanded="true">Create JSON</button>
                            @endif
                            {{-- <a href="{{route('abf.tracing', $row->id) }}" class="btn btn-sm btn-blue" target="_blank">Detail</a> --}}
                        </td>
                        
                    </tr>
                    @if (count($ns) > 0) 
                    <td colspan="18">
                        <div id="collapse{{ $row->id }}" class="collapse" aria-labelledby="headingOne"
                            data-parent="#accordionSummaryGradingUlang">
                            <div class="card card-body px-2 mb-1">
                                <b>Item yang digunakan untuk grading ulang:</b>
                                <div class="table-responsive">
                                    <table class="table default-table">
                                        <thead>
                                            <tr>
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
                                                {{-- <th rowspan="2">#</th> --}}
                                            </tr>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Jumlah</th>
                                                <th>Isi</th>
                                                <th>Kemasan</th>
                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>@if ($getProductGudangAsal->gudangabf)<a href="{{ route('abf.timbang', $getProductGudangAsal->gudangabf->id ?? '#') }}">{{ $getProductGudangAsal->gudangabf->id ?? "#" }} </a> @else <a href="{{ route('abf.timbang', $getProductGudangAsal->table_id ?? '#') }}">{{ $getProductGudangAsal->table_id ?? "#" }} </a>   @endif </td>
                                                <td>{{ $getProductGudangAsal->productitems->nama ?? ""}} @if($getProductGudangAsal->gudangabf) @if($getProductGudangAsal->gudangabf->grade_item != NULL) <br> <span class="text-primary font-weight-bold uppercase"> // Grade B @endif @endif</span></td>
                                                <td>{{ $getProductGudangAsal->konsumen->nama ?? ""}}</td>
                                                <td>{{ $getProductGudangAsal->production_date ?? "" }}</td>
                                                <td>{{ $getProductGudangAsal->gudangabf->tanggal_masuk ?? "" }}</td>
                                                <td>{{ $getProductGudangAsal->productgudang->code ?? "" }}</td>
                                                <td>{{ $getProductGudangAsal->sub_item ?? "" }}</td>
                                                <td>{{ $getProductGudangAsal->packaging }}</td>
                                                <td>{{ App\Models\Item::item_sku($getProductGudangAsal->karung)->nama ?? "#" }} || {{ $getProductGudangAsal->karung_qty }}</td>
                                                <td>{{ $getProductGudangAsal->karung_qty }}</td>
                                                <td>{{ $getProductGudangAsal->karung_isi }}</td>
                                                <td>{{ $getProductGudangAsal->tanggal_kemasan ? date('d/m/y', strtotime($getProductGudangAsal->tanggal_kemasan)) : '###' }}</td>
                                                <td>{{ $getProductGudangAsal->production_code }}</td>
                                                <td>{{ number_format($getProductGudangAsal->qty_awal) }}</td>
                                                <td>{{ number_format($getProductGudangAsal->berat_awal, 2) }}</td>
                                                <td>{{ number_format($getProductGudangAsal->qty) }}</td>
                                                <td>{{ number_format($getProductGudangAsal->berat, 2) }}</td>
                                                <td>{{ number_format($getProductGudangAsal->palete) }}</td>
                                                <td>{{ number_format($getProductGudangAsal->expired) }} Bulan</td>
                                                <td>{{ $getProductGudangAsal->stock_type }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-body p-1 mt-2">
                                <b>Netsuite Status</b> <br>
                                @if($ns)
                                    @if(User::setIjin('superadmin'))
                                    <hr>
                                    <div class="card card-body px-2 mb-1">
                                        <table class="table default-table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="ns-checkall">
                                                    </th>
                                                    <th>ID</th>
                                                    <th>C&U Date</th>
                                                    <th>TransDate</th>
                                                    <th>Label</th>
                                                    <th>Activity</th>
                                                    <th>Location</th>
                                                    <th>IntID</th>
                                                    <th>Paket</th>
                                                    <th width="100px">Data</th>
                                                    <th width="100px">Action</th>
                                                    <th>Response</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($ns ?? false)
                                                @foreach($ns as $dataNS)
                                                    @include('admin.pages.log.netsuite_one', ($netsuite = $dataNS))
                                                @endforeach
                                                @endif
        
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </td>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

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

$('.sendNSGradingUlang').on('click', function () {
    const id          =   $(this).attr('data-id');
    const key         =   'injectGradingUlang' ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $.ajax({
        url: "{{ route('abf.togudang') }}",
        data: {
            key,
            id
        },
        method: 'POST',
        success: function(data){
            // console.log(data)
            if (data.status == '200') {
                showNotif(data.msg)
                window.location.reload();
            } else {
                showAlert(data.msg)
            }
        }
    })


})
</script>

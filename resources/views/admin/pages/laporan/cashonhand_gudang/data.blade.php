@foreach ($data as $row)
<section class="panel">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th class="table-info" colspan="12">HASIL PRODUKSI</th>
                    </tr>
                    <tr>
                        <th class="text-center" rowspan="2">ID</th>
                        <th class="text-center" rowspan="2">Jenis</th>
                        <th class="text-center" rowspan="2">Tanggal</th>
                        <th class="text-center" colspan="2">Item</th>
                        <th class="text-center" rowspan="2">Plastik</th>
                        <th class="text-center" colspan="2">Masuk</th>
                        <th class="text-center" colspan="2">Sisa</th>
                    </tr>
                    <tr>
                        <th class="text-center">SKU</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Berat</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Berat</th>
                        <th class="text-center">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="#" target="_blank">{{ $row->id }}</a>
                        </td>
                        <td class="text-uppercase">{{ $row->stock_type }}</td>
                        <td>{{ $row->production_date }}</td>
                        <td>{{ $row->productitems->sku }}</td>
                        <td>{{ $row->productitems->nama }}</td>
                        <td>{{ $row->packaging }}</td>
                        <td>{{ number_format($row->qty_awal) }}</td>
                        <td>{{ number_format($row->berat_awal, 2) }}</td>
                        <td>{{ number_format($row->qty) }}</td>
                        <td>{{ number_format($row->berat, 2) }}</td>
                    </tr>
                </tbody>
                {{-- <tfoot>
                    <tr>
                        <tr>
                            <td colspan="12">
                                @if ($exp->parting->qty)
                                <div><b>Parting : {{ $exp->parting->qty }}</b></div>
                                @endif
                                @if ($sub[0]) <div><span class="status status-info">Sub Item : {{ $sub[0] }}</span></div> @endif
                                <div>Keterangan : {{ $sub[1] ?? '' }}</div>
                            </td>
                        </tr>
                    </tr>
                </tfoot> --}}
            </table>
        </div>


        {{-- <div class="table-responsive">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th colspan="10">DATA KONSUMEN ORDER</th>
                    </tr>
                    <tr>
                        <th class="text-center" colspan="3">Sales Order</th>
                        <th class="text-center" colspan="3">Delivery Order</th>
                        <th class="text-center" colspan="4">Pengiriman</th>
                    </tr>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nomor</th>
                        <th class="text-center">TanggalSO</th>
                        <th class="text-center">ID</th>
                        <th class="text-center">Nomor</th>
                        <th class="text-center">TanggalKirim</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Alamat</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($row->order_item as $item)
                    <tr>
                        <td>
                            <a href="{{ route('salesorder.detail', $item->order_id) }}" target="_blank">{{ $item->order_id }}</a>
                        </td>
                        <td>{{ $item->bahanbborder->no_so }}</td>
                        <td>{{ $item->bahanbborder->tanggal_so }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->bahanbborder->no_do }}</td>
                        <td>{{ $item->bahanbborder->tanggal_kirim }}</td>
                        <td>{{ $item->bahanbborder->nama }}</td>
                        <td>{{ $item->bahanbborder->alamat_kirim }}</td>
                        <td>{{ $item->bb_item ?? 0 }}</td>
                        <td>{{ $item->bb_berat ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}
    </div>
</section>
@endforeach

<div id="daftar_paginate">
    {{ $data->appends($_GET)->links() }}
</div>

<script>
$('#daftar_paginate .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_view').html(response);
        }

    });
});
</script>


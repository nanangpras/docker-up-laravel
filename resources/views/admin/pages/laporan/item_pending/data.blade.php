<table class="table default-table">
    <tbody>
        <tr>
            <th style="width:150px">SKU</th>
            <td>{{ $item->sku }}</td>
        </tr>
        <tr>
            <th>Nama Item</th>
            <td>{{ $item->nama }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td>{{ $item->itemkat->nama }}</td>
        </tr>
        <tr>
            <th>Tanggal Order</th>
            <td>{{ $request->tanggal_awal . ($request->tanggal_awal == $request->tanggal_akhir ? '' : ' - '. $request->tanggal_akhir) }}</td>
        </tr>
        @php
            $qty    =   0 ;
            $berat  =   0 ;
        @endphp
        @foreach ($data2 as $row)
        @php
            $qty    +=  $row->qty ;
            $berat  +=  $row->berat ;
        @endphp
        @endforeach
        <tr>
            <th>Qty (Ekor/Pcs)</th>
            <td>{{ $qty }}</td>
        </tr>
        <tr>
            <th>Berat (Kg)</th>
            <td>{{ $berat }}</td>
        </tr>
    </tbody>
</table>

<div class="table-responsive">
    <table class="table default-table">
        <thead>
            <tr>
                <th class="text-center" colspan="2">Sales Order</th>
                <th class="text-center" rowspan="2">OrderID</th>
                <th class="text-center" rowspan="2">Nama</th>
                <th class="text-center" rowspan="2">Alamat Kirim</th>
                <th class="text-center" rowspan="2">Sales Channel</th>
                <th class="text-center" rowspan="2">Qty (Ekor/Item)</th>
                <th class="text-center" rowspan="2">Berat (Kg)</th>
                <th class="text-center" rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th class="text-center">Nomor</th>
                <th class="text-center">Tanggal</th>
            </tr>
        </thead>
        <tbody>
        </div>
        @foreach ($data as $row)
            <tr>
                <td><a href="{{ route('salesorder.detail', $row->order_id) }}" target="_blank">{{ $row->no_so }}</a></td>
                <td>{{ $row->tanggal_so }}</td>
                <td><a href="{{ route('editso.index', $row->order_id) }}" target="_blank">{{ $row->order_id }}</a></td>
                <td>{{ $row->nama }}</td>
                <td>{{ $row->alamat_kirim }}</td>
                <td>{{ $row->sales_channel }}</td>
                <td>{{ $row->qty ?? 0 }}</td>
                <td>{{ $row->berat ?? 0 }}</td>
                <td>
                    @if ($row->part) Parting : {{ $row->part }}<br> @endif
                    @if ($row->bumbu) Bumbu : {{ $row->bumbu }}<br> @endif
                    @if ($row->memo) Memo : {{ $row->memo }}<br> @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

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

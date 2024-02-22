<table class="table default-table">
    <tbody>
        <tr>
            <th style="width:150px">Tanggal Pencarian</th>
            <td>{{ $request->tanggal_awal }} {{ $request->tanggal_awal == $request->tanggal_akhir ? '' : ' - ' . $request->tanggal_akhir }}</td>
        </tr>
        <tr>
            <th>Kepala Regu</th>
            <td class="text-uppercase">{{ $request->regu }}</td>
        </tr>
        @php
            $qty        =   0 ;
            $plastik    =   0 ;
        @endphp
        @foreach ($data2 as $row)
        @php
            $qty        +=  $row->qty ;
            $plastik    +=  $row->plastik_qty ;
        @endphp
        @endforeach
        <tr>
            <th>Qty Produksi</th>
            <td class="text-uppercase">{{ number_format($qty) }}</td>
        </tr>
        <tr>
            <th>Qty Plastik</th>
            <td class="text-uppercase">{{ number_format($plastik) }}</td>
        </tr>
        <tr>
            <th>Download</th>
            <td><a href="{{ route('dashboard.produksiplastik', ['key'=>'unduh'] ) }}&regu={{$request->regu}}&tanggal_awal={{$request->tanggal_awal}}&tanggal_akhir={{$request->tanggal_akhir}}" class="btn btn-outline-primary"><i class="fa fa-download"></i> Unduh</a></td>
        </tr>
    </tbody>
</table>

<div class="table-responsive">
    <table class="table default-table">
        <thead>
            <tr>
                <th colspan="8">HASIL PRODUKSI</th>
            </tr>
            <tr>
                <th class="text-center" rowspan="2">ID</th>
                <th class="text-center" colspan="3">Produksi</th>
                <th class="text-center" colspan="2">Hasil Produksi</th>
                <th class="text-center" colspan="2">Plastik</th>
            </tr>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Chiller</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Qty</th>
                <th class="text-center">Nama</th>
                <th class="text-center">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>
                    <a href="{{ route('regu.index', ['kategori' => $request->regu, 'produksi' => $row->freestock_id]) }}" target="_blank">{{ $row->freestock_id }}</a>
                </td>
                <td>{{ $row->free_stock->tanggal }}</td>
                <td><a href="{{ route('chiller.show', $row->tempchiller->id) }}" target="_blank">{{ $row->tempchiller->id }}</a></td>
                <td>{{ $row->prod_nama }}</td>
                <td>{{ $row->qty ?? 0 }}</td>
                <td>{{ $row->plastik_nama ?? '-' }}</td>
                <td>{{ $row->plastik_qty ?? 0 }}</td>
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

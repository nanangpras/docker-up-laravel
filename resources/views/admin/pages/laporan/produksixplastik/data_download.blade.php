@php
    header('Content-Transfer-Encoding: none');
    header('Content-type: application/vnd-ms-excel');
    header('Content-type: application/x-msexcel');
    header('Content-Disposition: attachment; filename=Data-Produksi-Plastik-Download.xls');
@endphp

<table class="table default-table table-small table-hover" border="1">
    <thead>
        <tr>
            <th colspan="9">HASIL PRODUKSI</th>
        </tr>
        <tr>
            <th class="text-center" rowspan="2">No</th>
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
        @foreach ($data_unduh as $row)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $row->id }}</td>
            <td><a href="{{ route('regu.index', ['kategori' => $request->regu, 'produksi' => $row->freestock_id]) }}" target="_blank">{{ $row->freestock_id }}</a></td>
            <td>{{ $row->tanggal }}</td>
            <td><a href="{{ route('chiller.show', $row->chillerid) }}" target="_blank">{{ $row->chillerid }}</a></td>
            <td>{{ $row->prod_nama }}</td>
            <td>{{ $row->qty }}</td>
            <td>{{ $row->plastik_nama }}</td>
            <td>{{ $row->plastik_qty }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

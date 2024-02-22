@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=WO1-Download-".$tanggal.".xls");
@endphp

<table class="table default-table table-small table-hover"  border="1">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Tanggal</th>
            <th rowspan="2">No LPAH/DO</th>
            <th rowspan="2">Supir</th>
            <th rowspan="2">PO</th>
            <th rowspan="2">Jam Masuk</th>
            <th rowspan="2">Operator</th>
            <th colspan="3">DO</th>
            <th colspan="3">LPAH</th>
        </tr>
        <tr>
            <th>Ekor</th>
            <th>Berat</th>
            <th>Rata-rata</th>
            <th>Ekor</th>
            <th>Kg</th>
            <th>Rata</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $row)
            <tr>
                <td>{{++$i}}</td>
                <td>{{$row->prod_tanggal_potong}}</td>
                <td>
                    {{ $row->prodpur->purcsupp->nama ?? '####' }}<br>{{ $row->no_lpah }}<br>NoDO :
                    {{ $row->no_do }}<br>
                    {{ $row->prodpur->no_po ?? '####' }}
                </td>
                <td>{{ $row->sc_pengemudi }}<br>{{ $row->sc_no_polisi }}</td>
                <td>
                    @if ($row->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $row->prodpur->ukuran_ayam }} @endif<br>
                    <span class="text-capitalize">{{ $row->po_jenis_ekspedisi }}</span> <br>
                    {{ $row->prodpur->type_po }}
                </td>
                <td>{{ date('d/m/y', strtotime($row->sc_tanggal_masuk ?? '')) }}
                    <br>{{ date('H:i', strtotime($row->sc_jam_masuk ?? '00:00')) }} WIB
                </td>
                <td>{{ $row->lpah_user_nama }}</td>
                <td>{{ number_format($row->sc_ekor_do) }} ekor</td>
                <td>{{ number_format($row->sc_berat_do,1) }} kg</td>
                <td>{{ number_format($row->sc_rerata_do,) }} kg</td>
                <td>{{ number_format($row->ekoran_seckle) }} ekor</td>
                <td>{{ number_format($row->lpah_berat_terima, 1) }} Kg</td>
                <td>
                    @if ($row->ekoran_seckle > 0) {{ number_format($row->lpah_rerata_terima, 1) }} Kg @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
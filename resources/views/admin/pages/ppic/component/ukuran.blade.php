<div class="table-responsive mt-3">
    <table class="table table-sm default-table dataTable">
        <thead>
            <tr>
                <th>No</th>
                <th>No. LPAH</th>
                <th>Supir</th>
                <th>No. Polisi</th>
                <th>Ukuran Ayam</th>
                <th>Kandang</th>
                <th>Ekspedisi</th>
                <th>Jam Masuk</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
                <th>Rerata</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ukuran as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->no_lpah }}</td>
                    <td>{{ $row->sc_pengemudi }}</td>
                    <td>{{ $row->sc_no_polisi }}</td>
                    <td>@if ($row->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $row->prodpur->ukuran_ayam }} @endif</td>
                    <td>{{ $row->sc_nama_kandang }}</td>
                    <td>{{ $row->po_jenis_ekspedisi }}</td>
                    <td>{{ date('H:i', strtotime($row->sc_jam_masuk ?? '00:00')) }} WIB
                    </td>
                    <td>{{ number_format($row->sc_ekor_do) }} ekor</td>
                    <td>{{ number_format($row->sc_berat_do, 2) }} Kg</td>
                    <td>{{ $row->sc_rerata_do }} Kg</td>
                    <td>{{ $row->status_lpah }}</td>
                    <td>
                        @if ($row->sc_status == 3)
                            <button type="submit" class="btn btn-primary btn-sm prosesukuran" data-id="{{ $row->id }}" data-purchse="{{ $row->prodpur->id }}">Proses</button>
                            <button type="submit" class="btn btn-danger btn-sm batalkanukuran" data-id="{{ $row->id }}" data-purchse="{{ $row->prodpur->id }}">Batalkan</button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
@stop
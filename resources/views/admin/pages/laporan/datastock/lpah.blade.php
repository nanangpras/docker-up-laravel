<div class="table-responsive">
    <a href="{{ route('datastock.laporan', ['key' => 'lpah']) }}&mulai={{ $mulai }}&akhir={{ $akhir }}" class="btn btn-success float-left">Unduh</a>
    <table class="table default-table"  id="lpahTable" width="100%">
        <thead>
            <tr>
                <th rowspan="2">Nomor PO</th>
                <th rowspan="2">Tanggal Potong</th>
                <th rowspan="2">Tipe PO</th>
                <th colspan="6">Security</th>
                <th colspan="13">LPAH</th>
            </tr>
            <tr>
                <th>Tanggal Masuk</th>
                <th>Jam</th>
                <th>Nomor Polisi</th>
                <th>Supir</th>
                <th>Alamat Kandang</th>
                <th>Nama Kandang</th>
                <th>Nomor Urut</th>
                <th>Nomor LPAH</th>
                <th>Jam Bongkar</th>
                <th>Tanggal Potong</th>
                <th>Berat Susut</th>
                <th>Berat Terima</th>
                <th>Rerata Terima</th>
                <th>Jumlah Keranjang</th>
                <th>Berat Keranjang</th>
                <th>Jam Potong</th>
                <th>Berat Kotor</th>
                <th>Ekoran Seckle</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lpah as $row)
            <tr>
                <td>{{ $row->no_po }}</td>
                <td>{{ $row->tanggal_potong }}</td>
                <td>{{ $row->type_po }}</td>
                <td>{{ $row->sc_tanggal_masuk }}</td>
                <td>{{ $row->sc_jam_masuk }}</td>
                <td>{{ $row->sc_no_polisi }}</td>
                <td>{{ $row->sc_pengemudi }}</td>
                <td>{{ $row->sc_alamat_kandang }}</td>
                <td>{{ $row->sc_nama_kandang }}</td>
                <td>{{ $row->no_urut }}</td>
                <td>{{ $row->no_lpah }}</td>
                <td>{{ $row->lpah_jam_bongkar }}</td>
                <td>{{ $row->lpah_tanggal_potong }}</td>
                <td class="text-right">{{ number_format($row->lpah_berat_susut, 2) }}</td>
                <td class="text-right">{{ number_format($row->lpah_berat_terima, 2) }}</td>
                <td>{{ $row->lpah_rerata_terima }}</td>
                <td>{{ number_format($row->lpah_jumlah_keranjang) }}</td>
                <td class="text-right">{{ number_format($row->lpah_berat_keranjang, 2) }}</td>
                <td>{{ $row->lpah_jam_potong }}</td>
                <td class="text-right">{{ number_format($row->lpah_berat_kotor, 2) }}</td>
                <td>{{ number_format($row->ekoran_seckle) }}</td>
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
    <script>
        $(document).ready(function() {
            $('#lpahTable').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY:        500,
                scrollX:        true,
                scrollCollapse: true,
                paging:         false,
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        } );
    </script>
@stop

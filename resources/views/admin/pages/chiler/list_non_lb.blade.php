
<section class="panel">
    <div class="card-body">

    <div class="table-responsive mt-4">
        <table class="table table-sm default-table dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>No. DO</th>
                    <th>Supir</th>
                    <th>Kandang</th>
                    <th>Jam Masuk</th>
                    <th>DO Ekor/Berat</th>
                    <th>Ekor/Berat</th>
                    <th>Status</th>
                    <th>Tujuan</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i => $row)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->no_lpah ?? '###' }}<br>DO : {{ $row->no_do ?? '###' }}</td>
                        <td>{{ $row->sc_pengemudi ?? '###' }}<br>{{ $row->sc_no_polisi ?? '###' }}</td>
                        <td>{{ $row->sc_nama_kandang ?? '###' }}<br>@if ($row->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $row->prodpur->ukuran_ayam }} @endif<br><span class="text-capitalize">{{ $row->po_jenis_ekspedisi ?? '###' }}</span></td>
                        <td>{{ date('d/m/y', strtotime($row->sc_tanggal_masuk ?? '')) }} <br>{{ date('H:i', strtotime($row->sc_jam_masuk ?? '00:00')) }} WIB</td>
                        <td>{{ number_format($row->sc_ekor_do) }} ekor<br>{{ number_format($row->sc_berat_do, 2) }} Kg <br> Rata : {{ $row->sc_rerata_do }} Kg</td>
                        <td>{{ number_format($row->ekoran_seckle) }} ekor <br> {{ number_format($row->lpah_berat_terima) }} Kg <br> Rata : {{number_format($row->lpah_berat_terima/($row->ekoran_seckle ?? '1'), 2)}} Kg</td>
                        <td>{{ $row->ppic_acc == 2 ? 'Proses' : 'Selesai' }}</td>
                        <td>{{ $row->ppic_tujuan }}</td>
                        <td>
                            @if ($row->ppic_acc == 2)
                                <a href="{{ route('nonkarkas.show', $row->id) }}" class="btn btn-sm btn-primary btn-rounded">Proses</a>
                            @else
                            <a href="{{ route('nonkarkas.show', $row->id) }}" class="btn btn-sm btn-warning btn-rounded">Detail</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@section('header')
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')
    <!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.dataTable').DataTable({
                "bInfo"         : false,
                responsive      : true,
                scrollY         : 500,
                scrollX         : true,
                scrollCollapse  : true,
                paging          : false,
            });
        });
    </script> -->
@stop
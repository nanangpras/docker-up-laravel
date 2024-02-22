<table class="table table-sm default-table dataTable">
    <thead>
        <tr>
            <th>Tanggal</th>
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
    @php
        $totalweighttransaction     = 0;
        $totalqtytransaction        = 0;
    @endphp
        @foreach ($data as $i => $row)
            <tr>
                <td>{{ $row->prod_tanggal_potong }}</td>
                <td> {{ $row->no_po ?? '###' }}<br>{{ $row->no_lpah ?? '' }}<br>DO :
                    {{ $row->no_do ?? '' }}</td>
                <td>{{ $row->sc_pengemudi ?? '###' }}<br>{{ $row->sc_no_polisi ?? '###' }}</td>
                <td>{{ $row->sc_nama_kandang ?? '###' }}<br>@if ($row->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $row->prodpur->ukuran_ayam }} @endif<br><span
                        class="text-capitalize">{{ $row->po_jenis_ekspedisi ?? '###' }}</span></td>
                <td>{{ date('d/m/y', strtotime($row->sc_tanggal_masuk ?? '')) }}
                    <br>{{ date('H:i', strtotime($row->sc_jam_masuk ?? '00:00')) }} WIB</td>
                <td>{{ number_format($row->sc_ekor_do) }}
                    ekor<br>{{ number_format($row->sc_berat_do, 2) }} Kg <br> Rata :
                    {{ number_format($row->sc_rerata_do, 2) }} Kg</td>
                <td>
                    Item : <br>
                    <table class="table default-table">
                        @foreach ($row->prodpur->purchasing_item as $no => $itm)
                            @php
                                $item = \App\Models\Item::item_sku($itm->item_po);
                            @endphp
                            <tr>
                                <td class="list-record">{{ $no + 1 }}</td>
                                <td class="list-record">{{ $item->nama }}</td>
                                <td class="list-record">Rp {{ number_format($itm->harga) }}</td>
                                <td class="list-record">{{ $itm->berat_ayam ?? '###' }} kg</td>
                                <td class="list-record">{{ $itm->jumlah_ayam ?? '###' }} Pcs/Ekr</td>
                                <td class="list-record">{{ $item->sku }}</td>
                            </tr>
                            @php
                                $totalweighttransaction     += $itm->berat_ayam;
                                $totalqtytransaction        += $itm->jumlah_ayam;
                            @endphp
                        @endforeach
                    </table>
                </td>
                <td>{{ $row->ppic_acc == 2 ? 'Proses' : 'Selesai' }}</td>
                <td>{{ $row->ppic_tujuan }}</td>
                <td>
                    @if ($row->ppic_acc == 2)
                        <a href="{{ route('nonkarkas.show', $row->id) }}"
                            class="btn btn-sm btn-primary btn-rounded">Proses</a>
                    @else
                        <a href="{{ route('nonkarkas.show', $row->id) }}"
                            class="btn btn-sm btn-warning btn-rounded">Detail</a>
                    @endif
                </td>
            </tr>
           
        @endforeach
    </tbody>
</table>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#abfNonLB').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY:        500,
                scrollX:        true,
                scrollCollapse: true,
                paging:         false,
            });
        } );
    </script>
@stop

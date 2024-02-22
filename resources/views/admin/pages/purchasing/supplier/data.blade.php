<section class="panel">
    <div class="card-body p-2">
        @php
            $jumlah_po  =   0 ;
            $unloading  =   0 ;
            $tt_ekor    =   0 ;
            $tt_berat   =   0 ;
            $tt_rerata  =   0 ;
        @endphp
        @foreach ($data2 as $row)
        @php
            $jumlah_po  +=  $row->jumlah_po ;
            $unloading  +=  count($row->unload_data) ;
        @endphp
            @foreach ($row->unload_data as $item)
                @php
                    $tt_ekor    +=  $item->sc_ekor_do ;
                    $tt_berat   +=  $item->sc_berat_do ;
                    $tt_rerata  +=  $item->sc_rerata_do ;
                @endphp
            @endforeach
        @endforeach
        <table class="table default-table">
            <tbody>
                <tr>
                    <th style="width: 170px">Total Purchase</th>
                    <td>{{ count($data2) }}</td>
                </tr>
                <tr>
                    <th>Jumlah PO</th>
                    <td>{{ $jumlah_po }}</td>
                </tr>
                <tr>
                    <th>Jumlah Unloading</th>
                    <td>{{ $unloading }}</td>
                </tr>
                <tr>
                    <th>Total Ekor</th>
                    <td>{{ number_format($tt_ekor) }}</td>
                </tr>
                <tr>
                    <th>Total Berat</th>
                    <td>{{ number_format($tt_berat, 2) }}</td>
                </tr>
                <tr>
                    <th>Rerata</th>
                    <td>@if($tt_rerata > 0 && $unloading > 0) {{ number_format($tt_rerata / $unloading, 2) }} @endif</td>
                </tr>
                <tr>
                    <th colspan="2">
                        <a href="{{ route('bukubesar.exportlpah', ['tanggal_mulai' => $awal, 'tanggal_selesai' => $akhir, 'supplier' => $request->supplier]) }}" class="btn btn-success btn-sm">Unduh Data</a>
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
</section>
@foreach ($data as $row)
<section class="panel">
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th class="text-center" colspan="7">Purchase</th>
                        <th class="text-center" colspan="3">Item</th>
                        <th class="text-center" colspan="2">Harga</th>
                    </tr>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">Internal Netsuite ID</th>
                        <th class="text-center">Tanggal Potong</th>
                        <th class="text-center">Nomor PO</th>
                        <th class="text-center">Tipe</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Unloading</th>
                        <th class="text-center">SKU</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center">Ukuran</th>
                        <th class="text-center">Penawaran</th>
                        <th class="text-center">Deal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->internal_id_po }}</td>
                        <td>{{ $row->tanggal_potong }}</td>
                        <td>{{ $row->no_po }}</td>
                        <td>{{ $row->type_po }}</td>
                        <td class="text-center">{{ $row->jumlah_po }}</td>
                        <td class="text-center">{{ count($row->unload_data) }}</td>
                        <td>{{ $row->item_po }}</td>
                        <td>{{ $row->purcitem->nama }}</td>
                        <td>{{ $row->ukuran_ayam }}</td>
                        <td class="text-right">{{ number_format($row->harga_penawaran) }}</td>
                        <td class="text-right">{{ number_format($row->harga_deal) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="table-responsive">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th colspan="9">Data Unloading</th>
                    </tr>
                    <tr>
                        <th class="text-center">ID</th>
                        <th class="text-center">No DO</th>
                        <th class="text-center">Tanggal Potong</th>
                        <th class="text-center">Pengemudi</th>
                        <th class="text-center">Alamat</th>
                        <th class="text-center">Jenis</th>
                        <th class="text-center">Ekor/Pcs/Pack</th>
                        <th class="text-center">Berat</th>
                        <th class="text-center">Rerata</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $ekor   =   0 ;
                        $berat  =   0 ;
                        $rerata =   0 ;
                    @endphp
                    @foreach ($row->unload_data as $item)
                    @php
                        $ekor   +=  $item->sc_ekor_do ;
                        $berat  +=  $item->sc_berat_do ;
                        $rerata +=  $item->sc_rerata_do ;
                    @endphp
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->no_do }}</td>
                        <td>{{ $item->prod_tanggal_potong }}</td>
                        <td>{{ $item->sc_pengemudi }}</td>
                        <td>{{ $item->sc_alamat_kandang }}</td>
                        <td>{{ $item->po_jenis_ekspedisi }}</td>
                        <td class="text-right">{{ number_format($item->sc_ekor_do) }}</td>
                        <td class="text-right">{{ number_format($item->sc_berat_do, 2) }}</td>
                        <td class="text-right">{{ number_format($item->sc_rerata_do, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6"></th>
                        <th class="text-right">{{ number_format($ekor) }}</th>
                        <th class="text-right">{{ number_format($berat, 2) }}</th>
                        <th class="text-right">{{ number_format($rerata / count($row->unload_data), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>
@endforeach

<div id="paginate_data">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('#paginate_data .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#view_data').html(response);
        }

    });
});
</script>



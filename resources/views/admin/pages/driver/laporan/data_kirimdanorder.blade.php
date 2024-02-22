<section class="panel">
    <div class="card-body">
        <div class="text-center mb-3">
            <b>SUMMARY PENGIRIMAN AYAM HIDUP</b>

            <table class="table default-table">
                <tbody>
                    <tr>
                        <th style="width: 140px">Nama Driver</th>
                        <td style="text-align: left">{{ $driver->nama }}</td>
                    </tr>
                    @if ($driver->kernek)
                    <tr>
                        <th>Nama Kernek</th>
                        <td style="text-align: left">{{ $driver->kernek }}</td>
                    </tr>
                    @endif
                    @if($driver->no_polisi)
                    <tr>
                        <th>Nomor Polisi</th>
                        <td style="text-align: left">{{ $driver->no_polisi }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="table-responsive">
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th>Nomor PO</th>
                            <th>Wilayah</th>
                            <th>Tanggal</th>
                            <th>Nomor Polisi</th>
                            <th>Target</th>
                            <th>Susut</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($delivery as $i => $row)
                        @php
                        $target = App\Models\Target::where('alamat', 'like', '%' . $row->sc_wilayah .
                        '%')->first()->target
                        ?? 0 ;
                        @endphp
                        <tr>
                            <td>{{ $row->no_po ?? '###' }}</td>
                            <td>{{ $row->sc_wilayah }}</td>
                            <td>{{ $row->sc_tanggal_masuk }}</td>
                            <td>{{ $row->sc_no_polisi }}</td>
                            <td>{{ $target }} %</td>
                            <td>{{ $row->lpah_persen_susut }} %</td>
                            <th>{{ ($target <= $row->lpah_persen_susut) ? '' : 'Sesuai' }}</th>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $delivery->appends($_GET)->onEachSide(1)->links() }}

            <script>
                $('.pagination a').on('click', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                console.log(url);
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(response) {
                        $('#summarypengiriman').html(response);
                    }
                });
            });
        
            </script>
        </div>
    </div>
</section>


<div class="text-center mb-3">
    <b>SUMMARY PENGIRIMAN ORDER</b>
</div>

<div class="card mb-3">
    <div class="card-header">Summary Pengiriman</div>
    <div class="card-body p-2">
        <div class="row mb-1">
            @foreach ($ekspedisi as $ekspedisi)
            <div class="col ">
                <div class="border text-center">
                    <div class="small">{{ $ekspedisi->wilayah->nama }}</div>
                    <div class="font-weight-bold"> {{ $ekspedisi->jumlah }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@foreach ($order as $row)
<section class="panel">
    <div class="card-header font-weight-bold">
        <div class="float-right status status-success">No Urut {{ $row->no_urut }}</div>
        Ekspedisi {{ $row->tanggal }} @if ($row->status == 4) <span class="status status-success">Selesai</span>
        @endif
    </div>
    <div class="card-body p-2">
        <table class="table default-table">
            <tbody>
                <tr>
                    <th style="width: 140px">Nama Driver</th>
                    <td>{{ $row->nama }}</td>
                </tr>
                @if ($row->kernek)
                <tr>
                    <th>Nama Kernek</th>
                    <td>{{ $row->kernek }}</td>
                </tr>
                @endif
                <tr>
                    <th>Nomor Polisi</th>
                    <td>{{ $row->no_polisi }}</td>
                </tr>
                <tr>
                    <th>Wilayah</th>
                    <td>{{ $row->wilayah->nama }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table default-table">
            <thead>
                <tr>
                    <th>Nomor SO</th>
                    <th>Tanggal Kirim</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($row->eksrute as $item)
                <tr>
                    <td>{{ $item->no_so }}</td>
                    <td>{{ $item->order_so->tanggal_kirim ?? '' }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">{{ number_format($item->berat, 2) }}</td>
                    <td>
                        @if ($item->status == 2) Proses Loading @endif
                        @if ($item->status == 3) Dalam Perjalanan @endif
                        @if ($item->status == 4) Terkirim @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endforeach

{{ $order->appends($_GET)->onEachSide(1)->links() }}

<script>
    $('.pagination a').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        console.log(url);
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#summarypengirimanorder').html(response);
            }
        });
    });

</script>
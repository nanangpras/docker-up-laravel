<div class="card mb-3">
    <div class="card-header">Summary Pengiriman</div>
    <div class="card-body p-2">
        <div class="row mb-1">
            @foreach ($ekspedisi as $ekspedisi)
            <div class="col ">
                <div class="border text-center">
                    <div class="small"> {{ $ekspedisi->wilayah->nama }}</div>
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
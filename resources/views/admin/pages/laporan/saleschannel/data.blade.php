<section class="panel">
    <div class="card-body p-2">
        <table class="table default-table">
            <tbody>
                <tr>
                    <th style="width: 170px">Sales Channel</th>
                    <td colspan="3">{{ $request->channel }}</td>
                </tr>
                <tr>
                    <th>Tanggal Kirim</th>
                    <td colspan="3">{{ $request->tanggal_awal . ($request->tanggal_awal != $request->tanggal_akhir ? ' - '. $request->tanggal_akhir : '') }}</td>
                </tr>
                @php
                    $ord_qty    =   0 ;
                    $alc_qty    =   0 ;
                    $ord_bb     =   0 ;
                    $alc_bb     =   0 ;
                @endphp
                @foreach ($data2 as $row)
                @foreach ($row->list_order as $list)
                @php
                    $ord_qty    +=  $list->qty ;
                    $ord_bb     +=  $list->berat ;
                    $alc_qty    +=  $list->fulfillment_qty ;
                    $alc_bb     +=  $list->fulfillment_berat ;
                @endphp
                @endforeach
                @endforeach
                @php
                    $selisih_qty    =   $ord_qty - $alc_qty ;
                    $selisih_bb     =   $ord_bb - $alc_bb ;
                @endphp
                <tr>
                    <th></th>
                    <th>Order</th>
                    <th>Alokasi</th>
                    <th>Selisih</th>
                </tr>
                <tr>
                    <th>Qty</th>
                    <td>{{ number_format($ord_qty) }}</td>
                    <td>{{ number_format($alc_qty) }}</td>
                    <td class="{{ ($selisih_qty) < 0 ? 'text-success' : ($ord_qty == $alc_qty ? 'text-primary' : 'text-danger') }}">
                        {{ number_format($selisih_qty) }}
                        @if ($selisih_qty) ({{ number_format((100 - (($selisih_qty / $ord_qty) * 100)), 2) }}%) @endif
                    </td>
                </tr>
                <tr>
                    <th>Berat</th>
                    <td>{{ number_format($ord_bb, 2) }}</td>
                    <td>{{ number_format($alc_bb, 2) }}</td>
                    <td class="{{ ($selisih_bb) < 0 ? 'text-success' : ($ord_bb == $alc_bb ? 'text-primary' : 'text-danger') }}">
                        {{ number_format($selisih_bb) }}
                        @if ($selisih_bb) ({{ number_format((100 - (($selisih_bb / $ord_bb) * 100)), 2) }}%) @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

@foreach ($data as $row)
<section class="panel">
    <div class="card-body p-2">
        <table class="table default-table">
            <thead>
                <tr>
                    <th colspan="4">
                        <div class="float-right">{!! $row->status_order !!}</div>
                        <a href="{{ route('salesorder.detail', $row->id) }}" target="_blank">{{ $row->id }}</a> - {{ $row->no_so }}
                    </th>
                </tr>
                <tr>
                    <th>Nama</th>
                    <th>Tanggal SO</th>
                    <th>Tanggal Kirim</th>
                    <th>Alamat Kirim</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->tanggal_so }}</td>
                    <td>{{ $row->tanggal_kirim }}</td>
                    <td>{{ $row->alamat_kirim }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table default-table">
            <thead>
                <tr>
                    <th colspan="6">ALOKASI ORDER</th>
                </tr>
                <tr>
                    <th class="text-center" rowspan="2">ID</th>
                    <th class="text-center" rowspan="2">Item</th>
                    <th class="text-center" colspan="2">Order</th>
                    <th class="text-center" colspan="2">Alokasi</th>
                </tr>
                <tr>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Berat</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Berat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($row->list_order as $list)
                <tr>
                    <td>{{ $list->id }}</td>
                    <td>
                        {{ $list->nama_detail }}
                        @if ($list->part) <div class="text-warning">PARTING {{ $list->part }}</div> @endif
                        @if ($list->bumbu) <div class="text-success">BUMBU {{ $list->bumbu }}</div> @endif
                    </td>
                    <td class="text-right">{{ $list->qty ?? 0 }}</td>
                    <td class="text-right">{{ $list->berat ?? 0 }}</td>
                    <td class="text-right {{ $list->qty > $list->fulfillment_qty ? 'text-danger' : ($list->qty == $list->fulfillment_qty ? 'text-primary' : 'text-success') }}">{{ $list->fulfillment_qty ?? 0 }}</td>
                    <td class="text-right {{ $list->berat > $list->fulfillment_berat ? 'text-danger' : ($list->berat == $list->fulfillment_berat ? 'text-primary' : 'text-success') }}">{{ $list->fulfillment_berat ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endforeach

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


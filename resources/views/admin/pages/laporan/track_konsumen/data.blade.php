@foreach ($data as $row)
<section class="panel">
    <div class="card-body">
        <table class="table default-table">
            <thead>
                <tr>
                    <th class="table-success" colspan="3">
                        <div class="float-right">{{ $row->sales_channel }}</div>
                        {{ $row->no_so }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <b>NS Internal ID :</b> {{ $row->netsuite_internal_id }}<br>
                        <b>Order ID : </b> <a href="{{ route('salesorder.detail', $row->id) }}" target="_blank">{{ $row->id }}</a><br>
                        <b>Tanggal SO : </b> {{ $row->tanggal_so }}<br>
                        <b>Tanggal Kirim : </b> {{ $row->tanggal_kirim }}
                    </td>
                    <td>
                        <b>Konsumen :</b> {{ $row->ordercustomer->nama }}<br>
                        <b>Alamat Kirim :</b>
                        <div style="max-width: 400px" class="status status-warning">{{ $row->alamat_kirim }}</div>
                    </td>
                    <td>
                        @php
                            $qty        =   0 ;
                            $berat      =   0 ;
                            $ff_qty     =   0 ;
                            $ff_berat   =   0 ;
                            $r_qty      =   0 ;
                            $r_berat    =   0 ;
                        @endphp
                        @foreach ($row->list_order as $list)
                        @php
                            $qty        +=  $list->qty ;
                            $berat      +=  $list->berat ;
                            $ff_qty     +=  $list->fulfillment_qty ;
                            $ff_berat   +=  $list->fulfillment_berat ;
                            $r_qty      +=  $list->retur_qty ;
                            $r_berat    +=  $list->retur_berat ;
                        @endphp

                        @endforeach
                        <div class="row">
                            <div class="col pr-lg-1">
                                <div>Order</div>
                                <div class="rounded-0 status status-info">{{ number_format($qty) }} Ekor/Pcs</div>
                                <div class="rounded-0 status status-success">{{ number_format($berat, 2) }} kg</div>
                            </div>
                            <div class="col px-lg-1">
                                <div>Fulfillment</div>
                                <div class="rounded-0 status status-info">{{ number_format($ff_qty) }} Ekor/Pcs</div>
                                <div class="rounded-0 status status-success">{{ number_format($ff_berat, 2) }} kg</div>
                            </div>
                            <div class="col pl-lg-1">
                                <div>Prosentase</div>
                                @php
                                    $jumlah     =   ((($ff_qty ? $ff_qty : 1) / ($qty ? $qty : 1)) * 100) ;
                                    $kilogram   =   ((($ff_berat ? $ff_berat : 1) / ($berat ? $berat : 1)) * 100) ;
                                @endphp
                                <div class="text-right rounded-0 status status-info">{{ number_format($ff_qty ? ($qty ? $jumlah : ($jumlah > 100 ? 100 : $jumlah)) : 0, 2) }} %</div>
                                <div class="text-right rounded-0 status status-success">{{ number_format($ff_berat ? ($berat ? $kilogram : ($kilogram > 100 ? 100 : $kilogram)) : 0, 2) }} %</div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="p-0" colspan="3">
                        <table class="table m-0 default-table">
                            <thead>
                                <tr>
                                    <th colspan="8">DAFTAR ORDER</th>
                                </tr>
                                <tr>
                                    <th class="text-center" rowspan="2">SKU</th>
                                    <th class="text-center" rowspan="2">Item</th>
                                    <th class="text-center" colspan="2">Order</th>
                                    <th class="text-center" colspan="2">Fulfill</th>
                                    <th class="text-center" colspan="2">Retur</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Berat</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Berat</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $qty        =   0 ;
                                    $berat      =   0 ;
                                    $ff_qty     =   0 ;
                                    $ff_berat   =   0 ;
                                    $r_qty      =   0 ;
                                    $r_berat    =   0 ;
                                @endphp
                                @foreach ($row->list_order as $list)
                                @php
                                    $qty        +=  $list->qty ;
                                    $berat      +=  $list->berat ;
                                    $ff_qty     +=  $list->fulfillment_qty ;
                                    $ff_berat   +=  $list->fulfillment_berat ;
                                    $r_qty      +=  $list->retur_qty ;
                                    $r_berat    +=  $list->retur_berat ;
                                @endphp
                                <tr>
                                    <td>{{ $list->sku }}</td>
                                    <td>{{ $list->nama_detail }}</td>
                                    <td class="text-right">{{ number_format($list->qty ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($list->berat, 2) }}</td>
                                    <td class="text-right {{ $list->fulfillment_qty > $list->qty ? 'text-success' : ($list->fulfillment_qty == $list->qty ? 'text-primary' : 'text-danger') }}">{{ number_format($list->fulfillment_qty ?? 0) }}</td>
                                    <td class="text-right {{ $list->fulfillment_berat > $list->berat ? 'text-success' : ($list->fulfillment_berat == $list->berat ? 'text-primary' : 'text-danger') }}">{{ number_format($list->fulfillment_berat, 2) }}</td>
                                    <td class="text-right">{{ number_format($list->retur_qty ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($list->retur_berat, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">TOTAL</th>
                                    <th class="text-right">{{ number_format($qty) }}</th>
                                    <th class="text-right">{{ number_format($berat, 2) }}</th>
                                    <th class="text-right">{{ number_format($ff_qty) }}</th>
                                    <th class="text-right">{{ number_format($ff_berat, 2) }}</th>
                                    <th class="text-right">{{ number_format($r_qty) }}</th>
                                    <th class="text-right">{{ number_format($r_berat, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                @if (COUNT($row->list_retur))
                <tr>
                    <td class="p-0 pt-3" colspan="3">
                        <table class="table m-0 default-table">
                            <thead>
                                <tr>
                                    <th colspan="4">DAFTAR RETUR</th>
                                </tr>
                                <tr>
                                    <th>ID</th>
                                    <th>Tanggal</th>
                                    <th>Operator</th>
                                    <th>Nomor RA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($row->list_retur as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('retur.detail', $item->id) }}" target="_blank">
                                            {{ $item->id }}
                                        </a>
                                    </td>
                                    <td>{{ $item->tanggal_retur }}</td>
                                    <td>{{ $item->operator }}</td>
                                    <td>{{ $item->no_ra }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <table class="table default-table mt-1">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Penanganan</th>
                                                    <th class="text-center">Catatan</th>
                                                    <th class="text-center">Satuan</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-center">Berat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $qty_retur  =   0 ;
                                                    $bb_retur   =   0 ;
                                                @endphp
                                                @foreach ($item->to_itemretur as $list)
                                                @php
                                                    $qty_retur  +=  $list->qty ;
                                                    $bb_retur   +=  $list->berat ;
                                                @endphp
                                                <tr>
                                                    <td>{{ $list->penanganan }}</td>
                                                    <td>{{ $list->catatan }}</td>
                                                    <td>{{ $list->satuan }}</td>
                                                    <td class="text-right">{{ number_format($list->qty) }}</td>
                                                    <td class="text-right">{{ number_format($list->berat, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3">Total</th>
                                                    <th class="text-right">{{ $qty_retur }}</th>
                                                    <th class="text-right">{{ $bb_retur }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
                @endif
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


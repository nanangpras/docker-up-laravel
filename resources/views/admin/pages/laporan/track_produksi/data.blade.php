@foreach ($data as $list)
<section class="panel">
    <div class="card-body">
        @php
            $exp    =   json_decode($list->label) ;
        @endphp

        <table class="table default-table">
            <thead>
                <tr>
                    <th class="table-success" colspan="2">DATA HASIL PRODUKSI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>ID Produksi</th>
                    <td>{{ $list->id }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $list->tanggal_produksi }}</td>
                </tr>
                <tr>
                    <th>Kepala Regu</th>
                    <td class="text-uppercase">{{ $list->regu }}</td>
                </tr>
                <tr>
                    <th>Nama Item</th>
                    <td>{{ $list->item->nama }}</td>
                </tr>
                <tr>
                    <th>Qty // Berat</th>
                    <td>{{ $list->qty }} pcs // {{ $list->berat }} kg</td>
                </tr>
                 <div class="status status-success">
                        <div class="row">
                            <div class="col pr-1">
                                {{ $list->plastik_nama }}
                            </div>
                            <div class="col-auto pl-1">
                                <span class="float-right">// {{ $list->plastik_qty }} Pcs</span>
                            </div>
                        </div>
                    </div>
            
                    
                @if ($exp)
                    
                    @if ($exp->sub_item)
                    <tr>
                        <th>Sub Item</th>
                        <td>{{ explode(' || ', $exp->sub_item)[0] ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Keterangan</th>
                        <td>{{ explode(' || ', $exp->sub_item)[1] ?? '' }}</td>
                    </tr>
                    @endif
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <div class="mb-1"><b>ALOKASI ORDER</b></div>
                        @foreach ($list->tempchiller->alokasi_order as $row)
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th colspan="3">
                                        NS Internal ID : {{ $row->bahanbborder->netsuite_internal_id }} ~ Order ID : <a href="{{ route('salesorder.detail', $row->order_id) }}" target="_blank">{{ $row->order_id }}</a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <b>ID SO :</b> {{ $row->bahanbborder->id_so }}<br>
                                        <b>Nomor SO :</b> {{ $row->bahanbborder->no_so }}<br>
                                        <b>Tanggal SO :</b> {{ $row->bahanbborder->tanggal_so }}<br>
                                    </td>
                                    <td>
                                        <b>Customer :</b> {{ $row->bahanbborder->nama }}<br>
                                        <b>Tanggal Kirim :</b> {{ $row->bahanbborder->tanggal_kirim }}<br>
                                        <b>Sales Channel :</b> {{ $row->bahanbborder->sales_channel }}<br>
                                        <b>Alamat Kirim :</b> <div class="status status-warning">{{ $row->bahanbborder->alamat_kirim }}</div>
                                    </td>
                                    <td>
                                        <a href="{{ route('chiller.show', $row->chiller_out) }}" target="_blank">{{ $row->chiller_out }}</a>. {{ $row->nama }}<br>
                                        <span class="status status-info">{{ $row->bb_item }}pcs</span> <span class="status status-success">{{ $row->bb_berat }}kg</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @endforeach
                    </td>
                </tr>
            </tfoot>
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

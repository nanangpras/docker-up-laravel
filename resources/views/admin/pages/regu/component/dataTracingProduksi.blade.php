@if ($download == true)
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=DATA TRACING PRODUKSI.xls');
    @endphp
@endif
<style>
    th,
    td {
        border: 1px solid #ddd;
    }
</style>
<div class="card-body p-2">
    <div class="table-responsive">
        <table class="default-table" width="1600px">
            <thead>
                <tr>
                    <th class="text-center text" rowspan="2">No</th>
                    <th class="text-center text" rowspan="2" width="200px">ITEM</th>
                    <th class="text-center text" rowspan="2" width="200px">REGU</th>
                    <th class="text-center text" rowspan="2">TYPE</th>
                    <th class="text-center text" rowspan="2" width="180px">CUSTOMER</th>
                    <th class="text-center text" rowspan="2">KETERANGAN</th>
                    <th class="text-center text" rowspan="2">TANGGAL PRODUKSI</th>
                    <th class="text-center text" rowspan="2">ASAL / TUJUAN</th>
                    <th class="text-center text" colspan="4">DATA STOCK CHILLER</th>
                    <th class="text-center text" colspan="4">ABF</th>
                    <th class="text-center text" colspan="4">GUDANG</th>
                    <th class="text-center text" colspan="2">AMBIL BB</th>
                    <th class="text-center text" colspan="2">ALOKASI / EKSPEDISI</th>
                </tr>
                <tr>
                    <th class="text-center text">QTY AWAL</th>
                    <th class="text-center text">BERAT AWAL</th>
                    <th class="text-center text">SISA QTY</th>
                    <th class="text-center text">SISA BERAT</th>
                    <th class="text-center text">QTY AWAL</th>
                    <th class="text-center text">QTY BERAT</th>
                    <th class="text-center text">SISA QTY</th>
                    <th class="text-center text">SISA BERAT</th>
                    <th class="text-center text">QTY AWAL</th>
                    <th class="text-center text">BERAT AWAL</th>
                    <th class="text-center text">QTY</th>
                    <th class="text-center text">BERAT</th>
                    <th class="text-center text">QTY</th>
                    <th class="text-center text">BERAT</th>
                    <th class="text-center text">QTY</th>
                    <th class="text-center text">BERAT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataChiller as $key => $data)
                <tr>
                    @php 
                        $qtyabfawal                 = 0;
                        $beratabfawal               = 0;
                        $qtyabfitem                 = 0;
                        $beratabfitem               = 0;
                        $qtyAwalGudang              = 0;
                        $beratAwalGudang            = 0;
                        $SisaQtyGudang              = 0;
                        $SisaBeratGudang            = 0;
                        $qtyBahanBaku               = 0;
                        $beratBahanBaku             = 0;
                        $qtyAlokasi                 = 0;
                        $beratAlokasi               = 0;
                    @endphp
                    @foreach($dataABF as $abf)
                        @if($abf->table_id_abf == $data->id)
                            @php
                                $qtyabfawal         += $abf->Qty_Awal;
                                $beratabfawal       += $abf->Berat_Awal;
                                $qtyabfitem         += $abf->Sisa_Qty;
                                $beratabfitem       += $abf->Sisa_Berat;
                            @endphp
                        @endif
                    @endforeach

                    @foreach($dataABFGudang as $gudang)
                        @if($gudang->table_id == $data->id)
                            @php
                                $qtyAwalGudang          += $gudang->Qty_Awal_Gudang;
                                $beratAwalGudang        += $gudang->Berat_Awal_Gudang;
                                $SisaQtyGudang          += $gudang->Sisa_Qty_Gudang;
                                $SisaBeratGudang        += $gudang->Sisa_Berat_Gudang;
                            @endphp
                        @endif
                    @endforeach

                    @foreach ($dataOrderBahanBaku as $orderbb)
                        @if($orderbb->chiller_id == $data->id)
                            @php 
                                $qtyBahanBaku               += $orderbb->qty;
                                $beratBahanBaku             += $orderbb->berat;
                            @endphp
                        @endif
                    @endforeach
                    
                    @foreach ($dataAlokasiOrder as $alokasi)
                        @if($alokasi->chiller_out == $data->id)
                            @php 
                                $qtyAlokasi               += $alokasi->bb_item;
                                $beratAlokasi             += $alokasi->bb_berat;
                            @endphp
                        @endif
                    @endforeach
                    
                    @php 
                        $exp    = json_decode($data->label);
                    @endphp
                    <td>{{ $loop->iteration }}</td>
                    <td><a href="{{ route('chiller.show', $data->id) }}" target="_blank">{{ $data->item_name }}</a></td>
                    <td>{{ $data->regu }}</td>
                    <td>{{ $data->type == 'bahan-baku' ? 'BAHAN BAKU' : 'HASIL PRODUKSI' }}</td>
                    <td>{{ $data->konsumen->nama ?? '#' }}</td>
                    <td>{{ $exp->sub_item ?? '#' }}</td>
                    <td>{{ $data->tanggal_produksi }}</td>
                    <td>{{ $data->asal_tujuan }}</td>
                    <td class="center">{{ number_format($data->qty_item,2) }}</td>
                    <td class="center">{{ number_format($data->berat_item, 2) }}</td>
                    <td class="center">{{ number_format($data->stock_item,2) }}</td>
                    <td class="center">{{ number_format($data->stock_berat,2) }}</td>
                    <td class="center">{{ number_format($qtyabfawal, 2) }}</td>
                    <td class="center">{{ number_format($beratabfawal,2) }}</td>
                    <td class="center">{{ number_format($qtyabfitem, 2) }}</td>
                    <td class="center">{{ number_format($beratabfitem, 2) }}</td>
                    <td class="center">{{ number_format($qtyAwalGudang,2) }}</td>
                    <td class="center">{{ number_format($beratAwalGudang,2) }}</td>
                    <td class="center">{{ number_format($SisaQtyGudang,2) }}</td>
                    <td class="center">{{ number_format($SisaBeratGudang,2) }}</td>
                    <td class="center">{{ number_format($qtyBahanBaku,2) }}</td>
                    <td class="center">{{ number_format($beratBahanBaku,2) }}</td>
                    <td class="center">{{ number_format($qtyAlokasi,2) }}</td>
                    <td class="center">{{ number_format($beratAlokasi,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="paginateData" class="mt-4">
        {{ $dataChiller->appends($_GET)->onEachSide(1)->links() }}
    </div>
</div>
<style>
    .center{
        text-align: center;
    }
    .table-responsive{
        overflow-x: auto;
    }
    .color-light-blue{
        background-color: #D9E1F5;
    }
    .color-light-green{
        background-color: #C7DFB2;
    }
    .color-light-yellow{
        background-color: #EDEF6D;
    }
    .color-light-red{
        background-color: #F4CEAF;
    }
</style>
<script>
    $('#paginateData .pagination a').on('click', function(e) {
        e.preventDefault();
        $('#text-notif').html('Menunggu...');
        $('#topbar-notification').fadeIn();

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#dataTracing').html(response).after($('#topbar-notification').fadeOut());
            }
        });
    });
</script>

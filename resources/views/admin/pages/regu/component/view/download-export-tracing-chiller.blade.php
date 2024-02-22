
@php
    header('Content-Transfer-Encoding: none');
    header('Content-type: application/vnd-ms-excel');
    header('Content-type: application/x-msexcel');
    header('Content-Disposition: attachment; filename=DATA TRACING PRODUKSI.xls');
@endphp

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
                @foreach ($arrayData as $data)
                <tr>
                    
                    <td>{{ $data['no'] }}</td>
                    <td>{{ $data['nama_item'] }}</td>
                    <td>{{ $data['regu'] }}</td>
                    <td>{{ $data['type'] == 'bahan-baku' ? 'BAHAN BAKU' : 'HASIL PRODUKSI' }}</td>
                    <td>{{ $data['konsumen'] ?? '#' }}</td>
                    <td>{{ $exp['sub_item'] ?? '#' }}</td>
                    <td>{{ $data['tanggal'] }}</td>
                    <td>{{ $data['asal_tujuan'] }}</td>
                    <td class="center">{{ $data['qty_item'] }}</td>
                    <td class="center">{{ $data['berat_item'] }}</td>
                    <td class="center">{{ $data['stock_item'] }}</td>
                    <td class="center">{{ $data['stock_berat'] }}</td>
                    <td class="center">{{ $data['qty_abf_awal'] }}</td>
                    <td class="center">{{ $data['berat_abf_awal'] }}</td>
                    <td class="center">{{ $data['qty_abf_item'] }}</td>
                    <td class="center">{{ $data['berat_abf_item'] }}</td>
                    <td class="center">{{ $data['gudang_qty_awal'] }}</td>
                    <td class="center">{{ $data['gudang_berat_awal'] }}</td>
                    <td class="center">{{ $data['gudang_qty_akhir'] }}</td>
                    <td class="center">{{ $data['gudang_berat_akhir'] }}</td>
                    <td class="center">{{ $data['qtyBahanBaku'] }}</td>
                    <td class="center">{{ $data['beratBahanBaku'] }}</td>
                    <td class="center">{{ $data['qtyAlokasi'] }}</td>
                    <td class="center">{{ $data['beratAlokasi'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
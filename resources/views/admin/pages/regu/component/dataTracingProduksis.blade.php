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
                    <th class="text-center text" rowspan="2">TYPE</th>
                    <th class="text-center text" rowspan="2" width="180px">CUSTOMER</th>
                    <th class="text-center text" rowspan="2">KETERANGAN</th>
                    <th class="text-center text" rowspan="2">TANGGAL</th>
                    <th class="text-center text" colspan="4">DATA STOCK AWAL</th>
                    <th class="text-center text" colspan="4">ABF</th>
                    <th class="text-center text" colspan="4">GUDANG</th>
                    {{-- <th class="text-center text" colspan="2">EKSPEDISI</th> --}}
                </tr>
                <tr>
                    <th class="text-center text">QTY AWAL</th>
                    <th class="text-center text">BERAT AWAL</th>
                    <th class="text-center text">QTY</th>
                    <th class="text-center text">BERAT</th>
                    <th class="text-center text">QTY AWAL</th>
                    <th class="text-center text">QTY BERAT</th>
                    <th class="text-center text">QTY</th>
                    <th class="text-center text">BERAT</th>
                    <th class="text-center text">QTY AWAL</th>
                    <th class="text-center text">BERAT AWAL</th>
                    <th class="text-center text">QTY</th>
                    <th class="text-center text">BERAT</th>
                    {{-- 
                        <th class="text-center text">QTY</th>
                        <th class="text-center text">BERAT</th>
                    --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($newArrayData as $key => $data)
                    <td>{{ $data['no'] }}</td>
                    {{-- <td><a href="{{ route('chiller.show', $data['id']) }}" target="_blank">{{ $data['nama_item'] }}</a></td> --}}
                    <td>{{ $data['nama_item'] }}</td>
                    <td>{{ $data['type'] == 'bahan-baku' ? 'BAHAN BAKU' : 'HASIL PRODUKSI' }}</td>
                    <td>{{ $data['konsumen'] }}</td>
                    <td>{{ $data['sub_item'] }}</td>
                    <td>{{ $data['tanggal'] }}</td>
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
                    {{-- 
                        <td class="center">{{ $data['qtyEkspedisi'] }}</td>
                        <td class="center">{{ $data['beratEkspedisi'] }}</td>
                    --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div id="paginateData" class="mt-4">
        {{ $newArrayData->appends($_GET)->onEachSide(1)->links() }}
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

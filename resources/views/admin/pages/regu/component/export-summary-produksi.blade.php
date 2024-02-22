@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=$filename");
@endphp
<style>
    .text-center{
        vertical-align: middle; 
        text-align: center;
    }
    .hidden{
        display:none !important;
        visibility: hidden;
    }
    .text-left{
        text-align: left;
    }
    .float-right{
        float: right;
    }
</style>
<table class="table default-table" id="filterdata" border="1" width="100%">
    <thead>
        <th>No</th>
        <th>Item</th>
        <th>Customer</th>
        <th>Keterangan</th>
        <th>Tanggal</th>
        <th>#</th>
        {{-- <th>Kode</th> --}}
        <th>Packaging</th>
        <th>Ekor/Pcs/Pack</th>
        <th>Berat</th>
    </thead>
    <tbody>
        @foreach ($arrayData as $item)
            <tr class="filter-name">
                <td>{{ $item['no'] }}</td>
                <td>
                    {{ $item['nama_item'] }}
                    <span> </span>
                    {{ $item['diedit'] }}
                    <div>
                    {{ $item['additional'] }}
                    </div>
                    <div class="row mt-1 text-info">
                        <div class="col-auto pl-1 text-right">
                            {{ $item['parting'] }}
                        </div>
                    </div>
                </td>
                <td>{{$item['konsumen'] }}</td>
                <td class="text-center">{{ $item['sub_item'] }}</td>
                <td>{{ $item['tanggal'] }}</td>
                <td>
                    @if ($item['kategori'] == '1')
                        <span class="status status-danger">ABF </span>
                    @elseif($item['kategori'] == '2')
                        <span class="status status-warning">EKSPEDISI </span>
                    @else
                        <span class="status status-info">CHILLER </span>
                    @endif
                </td>
                {{-- <td>
                    {!! $item['kode_abf'] !!}
                </td> --}}
                <td>
                    <div class="status status-success">
                        <div class="row">
                            <div class="col pr-1">
                                {{ $item['plastik_name'] }}
                            </div>
                            <div class="col-auto pl-1">
                                <span class="float-right">// {{ $item['plastik_qty'] }} Pcs</span>
                            </div>
                        </div>
                    </div>
            
                </td>
                <td class="text-center">{{ $item['qty'] }}</td>
                <td class="text-center">{{ $item['berat'] }} Kg</td>
            </tr>
        @endforeach
    </tbody>
</table>
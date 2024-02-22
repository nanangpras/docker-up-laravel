@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename={$filename}.xls");
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
<script>
    let clonedTable = $("#filterdata").clone();
    clonedTable.find('[style*="display:none"]').remove();
</script>
<table class="table default-table table-small table-hover" id="filterdata" border="1" width="100%">
    <thead>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nama Bumbu</th>
        {{-- <th>Stock</th> --}}
        <th>Berat</th>
        <th>Status</th>
        <th>Tujuan</th>
        <th>Customer</th>
    </thead>
    <tbody>
        @foreach ($data as $no => $item)
            <tr>
                <td>{{ ++$no }}</td>
                <td>{{ $item->tanggal }}</td>
                <td>{{ $item->bumbu->nama }}</td>
                {{-- <td>{{ $item->stock }}</td> --}}
                <td>{{ $item->berat }}</td>
                <td>{{ $item->status }}</td>
                <td>
                    @if($item->regu !== NULL)
                    {{ $item->regu }}
                    @else
                    Gudang
                    @endif
                </td>
                <td>
                    @if ($item->customer_bumbu)
                        @if($item->regu == "marinasi")
                            {{ $item->customer_bumbu->customers->nama }}
                        @else
                            Adjustment
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
        
    </tbody>
</table>
<br>
<div class="table-outer">
    <div class="table-inner">
        Pengalokasian dari Gudang Frozen
        <input type="hidden" name="lokasi_asal" value="frozen">
        <table class="table default-table tableFixHead table-small">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Tanggal </th>
                    <th>Lokasi</th>
                    <th>Qty (Ekor)</th>
                    <th>Berat (Kg)</th>
                    <th>Pengambilan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stock as $i => $val)
                    @php 
                        $sisaQty     = $val->sisaQty;  
                        $sisaBerat   = number_format((float)$val->sisaBerat, 2, '.', ''); 
                    @endphp
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $val->nama ?? '#' }}</td>
                        <td>{{ date('d/m/y', strtotime($val->production_date)) }}</td>
                        <td>{{ $val->kode_gudang }}</td>
                        <td>{{ $sisaQty }}</td>
                        <td>{{ $sisaBerat }}</td>
                        <td>
                            <input type="hidden" name="x_code[]" value="{{ $val->id }}">
                            <div style="max-width: 200px!important">
                                <div class="row">
                                    <div class="col pr-1"><input type="number" name="qty[]" style="max-width: 100px" class="p-1 form-control form-control-sm" placeholder="Ekor" min="0"></div>
                                    <div class="col pl-1"><input type="number" name="berat[]" style="max-width: 100px" class="p-1 form-control form-control-sm" step="0.01" placeholder="Berat" min="0" max="{{ $sisaBerat }}"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <div class="float-right text-secondary small">ID-{{ $val->id }}</div>
                            @if ($val->packaging)
                            <div class="text-info">{{ $val->packaging }}</div>
                            @endif
                            @if ($val->sub_item || $val->customer_id)
                            <div class="text-success">{{ $val->customer_id ? $val->customer_name . ' - ' : '' }} {{ $val->sub_item }}</div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>

<style>
    /* Fix table head */
    .tableFixHead    {
        overflow: auto;
        height: 100px;
    }
    .tableFixHead th {
        position: sticky;
        top: 0;
        z-index: 2000;
    }

    /* Just common table stuff. */
    table  {
        border-collapse: collapse;
        width: 100%; }
    th, td {
        padding: 8px 16px;
    }
    th     {
        background:#eee;
    }
    .table-outer{
        max-height: 500px;
        overflow-y: scroll;
    }
</style>


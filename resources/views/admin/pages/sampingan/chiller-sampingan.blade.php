<br>
<div class="table-outer">
    <div class="table-inner">
        Pengalokasian dari Chiller Bahan Baku
        <input type="hidden" name="lokasi_asal" value="sampingan">
        <table class="table default-table tableFixHead table-small">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Qty (Ekor)</th>
                    <th>Berat (kg)</th>
                    <th>Pengambilan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $berat  = 0;
                    $qty    = 0;
                @endphp
                @foreach ($produk as $row)
                    @php 
                        $sisaQty        = $row->sisaQty;
                        $sisaBerat      = number_format((float)$row->sisaBerat, 2, '.', '');
                    @endphp
                    <tr>
                        <td> {{ $row->item_name }}
                            @php
                                $exp = json_decode($row->label);
                            @endphp
                            <div class="status status-success">
                                <div class="row">
                                    <div class="col pr-1">
                                        {{ $row->plastik_nama }}
                                    </div>
                                    <div class="col-auto pl-1">
                                        <span class="float-right">// {{ $row->plastik_qty }} Pcs</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($exp)
                                
                                @if ($exp->parting->qty ?? "") Parting : {{ $exp->parting->qty }} <br> @endif
                                @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                                <div class="status status-success">
                                    @if ($exp->sub_item ?? "") Customer : {{ $exp->sub_item }} @endif
                                </div>
                            @endif

                        </td>
                        <td>{{ $sisaQty }}</td>
                        <td>{{ $sisaBerat }}</td>
                        <td>
                            <input type="hidden" name="x_code[]" value="{{ $row->id }}">
                            <div style="max-width: 200px!important">
                                <div class="row">
                                    <div class="col pr-1"><input type="number"  name="qty[]" style="max-width: 100px" class="p-1 form-control qty_ambil form-control-sm" placeholder="Ekor" min="0"></div>
                                    <div class="col pl-1"><input type="number"  name="berat[]" style="max-width: 100px" class="p-1 form-control berat_ambil form-control-sm" step="0.01" placeholder="Berat" min="0" max="{{ $sisaBerat }}"></div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    @php
                        $berat  = $berat + $row->stock_berat;
                        $qty    = $qty + $row->stock_item;
                    @endphp

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


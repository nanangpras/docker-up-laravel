<div class="table-responsive">
    <div  id="table-bb-beli">

<table class="default-table table">
    <style>
        .text {
            mso-number-format:"\@";
            border:thin solid black;
        }
    </style>
    <thead>
        <tr>
            <th class="text" rowspan="3">Tanggal</th>
            <th class="text" colspan="2" rowspan="2">Jumlah Pembelian</th>
            <th class="text" colspan="8">Hasil Produksi Bahan Baku Beli</th>
            <th class="text" colspan="2" rowspan="2">Total</th>
            <th class="text" colspan="2" rowspan="2">Sisa</th>
        </tr>
        <tr>
            <th colspan="2" class="text">WHOLE CHICKEN</th>
            <th colspan="2" class="text">PARTING</th>
            <th colspan="2" class="text">PARTING M</th>
            <th colspan="2" class="text">BONELESS</th>
        </tr>
        <tr>
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
            <th class="text">Ekor/Pcs/Pack</th>
            <th class="text">Kg</th>
        </tr>
    </thead>
    <tbody>
        @php
            $yield      = 0;
            $qty_beli   = 0;
            $bb_beli    = 0;
            $qty_wc     = 0;
            $bb_wc      = 0;
            $qty_pt     = 0;
            $bb_pt      = 0;
            $qty_mr     = 0;
            $bb_mr      = 0;
            $qty_bn     = 0;
            $bb_bn      = 0;
            $qty_tt     = 0;
            $bb_tt      = 0;
            $qty_sisa   = 0;
            $bb_sisa    = 0;
        @endphp

        @foreach ($collection as $row)
            @php
                $qty_beli   +=  $row['qtybeli'];
                $bb_beli    +=  $row['bbbeli'];
                $qty_wc     +=  $row['qtywc'];
                $bb_wc      +=  $row['bbwc'];
                $qty_pt     +=  $row['qtypt'];
                $bb_pt      +=  $row['bbpt'];
                $qty_mr     +=  $row['qtymr'];
                $bb_mr      +=  $row['bbmr'];
                $qty_bn     +=  $row['qtybn'];
                $bb_bn      +=  $row['bbbn'];
                $qty_tt     +=  $row['qtytt'];
                $bb_tt      +=  $row['bbtt'];
                $qty_sisa   +=  $row['qtysisa'];
                $bb_sisa    +=  $row['bbsisa'];
            @endphp
            <tr>
                <td class="text">{{ date('d/m/Y', strtotime($row['lpah_tanggal_potong'])) }}</td>
                <td class="text">{{ number_format($row['qtybeli']) }}</td>
                <td class="text">{{ number_format($row['bbbeli']) }}</td>
                <td class="text">{{ number_format($row['qtywc']) }}</td>
                <td class="text">{{ number_format($row['bbwc'], 1) }}</td>
                <td class="text">{{ number_format($row['qtypt']) }}</td>
                <td class="text">{{ number_format($row['bbpt'], 1) }}</td>
                <td class="text">{{ number_format($row['qtymr']) }}</td>
                <td class="text">{{ number_format($row['bbmr'], 1) }}</td>
                <td class="text">{{ number_format($row['qtybn']) }}</td>
                <td class="text">{{ number_format($row['bbbn'], 1) }}</td>
                <td class="text">{{ number_format($row['qtytt']) }}</td>
                <td class="text">{{ number_format($row['bbtt'], 1) }}</td>
                <td class="text">{{ number_format($row['qtysisa']) }}</td>
                <td class="text">{{ number_format($row['bbsisa'], 1) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th class="text">TOTAL</th>
            <th class="text">{{ number_format($qty_beli) }}</th>
            <th class="text">{{ number_format($bb_beli) }}</th>
            <th class="text">{{ number_format($qty_wc) }}</th>
            <th class="text">{{ number_format($bb_wc, 1) }}</th>
            <th class="text">{{ number_format($qty_pt) }}</th>
            <th class="text">{{ number_format($bb_pt, 1) }}</th>
            <th class="text">{{ number_format($qty_mr) }}</th>
            <th class="text">{{ number_format($bb_mr, 1) }}</th>
            <th class="text">{{ number_format($qty_bn) }}</th>
            <th class="text">{{ number_format($bb_bn, 1) }}</th>
            <th class="text">{{ number_format($qty_tt) }}</th>
            <th class="text">{{ number_format($bb_tt, 1) }}</th>
            <th class="text">{{ number_format($qty_sisa) }}</th>
            <th class="text">{{ number_format($bb_sisa, 1) }}</th>
        </tr>
    </tfoot>
</table>

</div>

<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="export-weekly-bb-beli.xls">
    <textarea name="html" style="display: none" id="html-bb-beli"></textarea>
    <button type="submit" id="export-bb-beli" class="btn btn-blue">Export</button>
</form>

<script>
    $(document).ready(function(){
        var html  = $('#table-bb-beli').html();
        $('#html-bb-beli').val(html);
    })
</script>

</div>

<div class="table-responsive">
    <div  id="table-bb-fresh">

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
                    <th class="text" colspan="3" rowspan="2">Jumlah Pemotongan</th>
                    <th class="text" rowspan="3">Yield</th>
                    <th class="text" colspan="2" rowspan="2">Stock Fresh</th>
                    <th class="text" colspan="12">Hasil Produksi Bahan Baku</th>
                    <th class="text" colspan="2" rowspan="2">Total</th>
                </tr>
                <tr>
                    <th colspan="2" class="text">WHOLE CHICKEN</th>
                    <th colspan="2" class="text">PARTING</th>
                    <th colspan="2" class="text">PARTING M</th>
                    <th colspan="2" class="text">BONELESS</th>
                    <th colspan="2" class="text">Stock Frozen</th>
                    <th colspan="2" class="text">SAMPINGAN</th>
                </tr>
                <tr>
                    <th class="text">Jumlah Mobil</th>
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
                    <th class="text">Ekor/Pcs/Pack</th>
                    <th class="text">Kg</th>
                    <th class="text">Ekor/Pcs/Pack</th>
                    <th class="text">Kg</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $yield      = 0;
                    $prod_yield = 0;
                    $mobil      = 0;
                    $ekor       = 0;
                    $berat      = 0;

                    $qty_fs     = 0;
                    $bb_fs      = 0;

                    $qty_wc     = 0;
                    $bb_wc      = 0;
                    $qty_pt     = 0;
                    $bb_pt      = 0;
                    $qty_mr     = 0;
                    $bb_mr      = 0;
                    $qty_bn     = 0;
                    $bb_bn      = 0;
                    $qty_fz     = 0;
                    $bb_fz      = 0;
                    $qty_sp     = 0;
                    $bb_sp      = 0;
                    $bb_tt      = 0;
                    $qty_tt     = 0;
                @endphp
                @foreach($collection as $row)
                    @php
                        $ekor       +=  $row['qty_gr'];
                        $berat      +=  $row['bb_gr'];
                        $mobil      +=  $row['jumlah_mobil'];
                        $prod_yield +=  $row['prod_yield'];
                        $qty_fs     =   $qty_fs + $row['qtyfs'];
                        $bb_fs      =   $bb_fs + $row['bbfs'];

                        $qty_wc     +=  $row['qtywc'];
                        $bb_wc      +=  $row['bbwc'];
                        $qty_pt     +=  $row['qtypt'];
                        $bb_pt      +=  $row['bbpt'];
                        $qty_mr     +=  $row['qtymr'];
                        $bb_mr      +=  $row['bbmr'];
                        $qty_bn     +=  $row['qtybn'];
                        $bb_bn      +=  $row['bbbn'];
                        $qty_fz     +=  $row['qtyfz'];
                        $bb_fz      +=  $row['bbfz'];
                        $qty_sp     +=  $row['qtysp'];
                        $bb_sp      +=  $row['bbsp'];
                        $qty_tt     +=  $row['qtytt'];
                        $bb_tt      +=  $row['bbtt'];

                    @endphp
                    <tr>
                        <td class="text">{{ date('d/m/Y', strtotime($row['lpah_tanggal_potong'])) }}</td>
                        <td class="text">{{ $row['jumlah_mobil'] }}</td>
                        <td class="text">{{ number_format($row['qty_gr']) }}</td>
                        <td class="text">{{ number_format($row['bb_gr'], 1) }}</td>
                        <td class="text">{{ number_format($row['prod_yield'], 1) }}%</td>
                        <td class="text">{{ number_format($row['qtyfs']) }}</td>
                        <td class="text">{{ number_format($row['bbfs'], 1) }}</td>
                        <td class="text">{{ number_format($row['qtywc']) }}</td>
                        <td class="text">{{ number_format($row['bbwc'], 1) }}</td>
                        <td class="text">{{ number_format($row['qtypt']) }}</td>
                        <td class="text">{{ number_format($row['bbpt'], 1) }}</td>
                        <td class="text">{{ number_format($row['qtymr']) }}</td>
                        <td class="text">{{ number_format($row['bbmr'], 1) }}</td>
                        <td class="text">{{ number_format($row['qtybn']) }}</td>
                        <td class="text">{{ number_format($row['bbbn'], 1) }}</td>
                        <td class="text">{{ number_format($row['qtyfz']) }}</td>
                        <td class="text">{{ number_format($row['bbfz'], 1) }}</td>
                        <td class="text">{{ number_format($row['qtysp']) }}</td>
                        <td class="text">{{ number_format($row['bbsp'], 1) }}</td>
                        <td class="text">{{ number_format($row['qtyfs'] + $row['qtytt']) }}</td>
                        <td class="text">{{ number_format($row['bbfs'] + $row['bbtt'], 1) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text">TOTAL</th>
                    <th class="text">{{ $mobil }}</th>
                    <th class="text">{{ number_format($ekor) }}</th>
                    <th class="text">{{ number_format($berat, 1) }}</th>
                    <th class="text">{{ number_format($prod_yield, 1) }}%</th>
                    <th class="text">{{ number_format($qty_fs) }}</th>
                    <th class="text">{{ number_format($bb_fs, 1) }}</th>
                    <th class="text">{{ number_format($qty_wc) }}</th>
                    <th class="text">{{ number_format($bb_wc, 1) }}</th>
                    <th class="text">{{ number_format($qty_pt) }}</th>
                    <th class="text">{{ number_format($bb_pt, 1) }}</th>
                    <th class="text">{{ number_format($qty_mr) }}</th>
                    <th class="text">{{ number_format($bb_mr, 1) }}</th>
                    <th class="text">{{ number_format($qty_bn) }}</th>
                    <th class="text">{{ number_format($bb_bn, 1) }}</th>
                    <th class="text">{{ number_format($qty_fz) }}</th>
                    <th class="text">{{ number_format($bb_fz, 1) }}</th>
                    <th class="text">{{ number_format($qty_sp) }}</th>
                    <th class="text">{{ number_format($bb_sp, 1) }}</th>
                    <th class="text">{{ number_format($qty_fs + $qty_tt) }}</th>
                    <th class="text">{{ number_format($bb_fs + $bb_tt, 1) }}</th>
                </tr>
            </tfoot>
        </table>

    </div>
</div>

<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="export-weekly-bahanbaku-fresh.xls">
    <textarea name="html" style="display: none" id="html-bb-fresh"></textarea>
    <button type="submit" id="export-bb-fresh mt-3" class="btn btn-blue">Export</button>
</form>

<script>
    $(document).ready(function(){
        var html  = $('#table-bb-fresh').html();
        $('#html-bb-fresh').val(html);
    })
</script>

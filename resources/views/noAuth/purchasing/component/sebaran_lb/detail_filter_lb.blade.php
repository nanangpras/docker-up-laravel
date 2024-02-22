<div class="col-lg-12">
    <div class="mt-5">
        <div class="table-responsive">
            <div class="mb-3 center bold">
                TABEL DETAIL PRODUKSI LIVE BIRD TO GRADING
            </div>
            <table class="default-table table-bordered" width="5000">
                <thead>
                    <tr>
                        <th class="center" width="50"  rowspan="2">No</th>
                        <th class="center" width="100" rowspan="2">Tanggal</th>
                        <th class="center" width="100" rowspan="2">Supplier</th>
                        <th class="center" width="100" rowspan="2">Kandang</th>
                        <th class="center" width="100" rowspan="2">Ukuran</th>
                        <th class="center" width="80"  rowspan="2">Rerata Terima</th>
                        <th class="center" width="80"  rowspan="2">Persen Susut</th>
                        <th class="center" width="80"  rowspan="2">Rerata Ekor Mati</th>
                        @foreach($DataItem as $item)
                        <th class="center" width="150" colspan="2">Broiler {{ substr($item->nama,-5) }}</th>
                        @endforeach
                        <th class="center" width="100" rowspan="2"> TOTAL </th>
                    </tr>
                    <tr>
                        @for($x=1; $x<=23; $x++)
                        <th class="center"> Kg </th>
                        <th class="center"> % </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $total_0304       = 0;
                        $total_0405       = 0;
                        $total_0506       = 0;
                        $total_0607       = 0;
                        $total_0708       = 0;
                        $total_0809       = 0;
                        $total_0910       = 0;
                        $total_1011       = 0;
                        $total_1112       = 0;
                        $total_1213       = 0;
                        $total_1314       = 0;
                        $total_1415       = 0;
                        $total_1516       = 0;
                        $total_1617       = 0;
                        $total_1718       = 0;
                        $total_1819       = 0;
                        $total_1920       = 0;
                        $total_2021       = 0;
                        $total_2122       = 0;
                        $total_2223       = 0;
                        $total_2324       = 0;
                        $total_2425       = 0;
                        $total_25UP       = 0;

                        $t1                 = 0;
                        $t2                 = 0;
                        $t3                 = 0;
                        $t4                 = 0;
                        $t5                 = 0;
                        $t6                 = 0;
                        $t7                 = 0;
                        $t8                 = 0;
                        $t9                 = 0;
                        $t10                = 0;
                        $t11                = 0;
                        $t12                = 0;
                        $t13                = 0;
                        $t14                = 0;
                        $t15                = 0;
                        $t16                = 0;
                        $t17                = 0;
                        $t18                = 0;
                        $t19                = 0;
                        $t20                = 0;
                        $t21                = 0;
                        $t22                = 0;
                        $t23                = 0;

                        $countdata        = count($arrayItem);

                        $rerataterima     = 0;
                        $susut            = 0;
                        $mati             = 0;
                    @endphp
                    @if(count($arrayItem) > 0)
                        @foreach($arrayItem as $row)
                            <tr>
                                @php 
                                    $totalmendatar  = $row['uk03_04'] + $row['uk04_05'] + $row['uk05_06'] + $row['uk06_07'] + $row['uk07_08'] + $row['uk08_09'] + $row['uk09_10'] + $row['uk10_11'] + $row['uk11_12'] + $row['uk12_13'] + $row['uk13_14'] + $row['uk14_15'] + $row['uk15_16'] + $row['uk16_17'] + $row['uk17_18'] + $row['uk18_19'] + $row['uk19_20'] + $row['uk20_21'] + $row['uk21_22'] + $row['uk22_23'] + $row['uk23_24'] + $row['uk24_25'] + $row['uk25_UP'];

                                    if ($row['uk03_04'] > 0) {
                                        $satu           = number_format(($row['uk03_04'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $satu           = 0;
                                    }

                                    if ($row['uk04_05'] > 0) {
                                        $dua            = number_format(($row['uk04_05'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $dua            = 0;
                                    }

                                    if ($row['uk05_06'] > 0) {
                                        $tiga           = number_format(($row['uk05_06'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $tiga           = 0;
                                    }

                                    if ($row['uk06_07'] > 0) {
                                        $empat          = number_format(($row['uk06_07'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $empat          = 0;
                                    }

                                    if ($row['uk07_08'] > 0) {
                                        $lima           = number_format(($row['uk07_08'] * 100 ) / ($totalmendatar),2);

                                    } else {
                                        $lima           = 0;
                                    }

                                    if ($row['uk08_09'] > 0) {
                                        $enam           = number_format(($row['uk08_09'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $enam           = 0;
                                    }

                                    if ($row['uk09_10'] > 0) {
                                        $tujuh          = number_format(($row['uk09_10'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $tujuh          = 0;
                                    }

                                    if ($row['uk10_11'] > 0) {
                                        $delapan        = number_format(($row['uk10_11'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $delapan        = 0;
                                    }

                                    if ($row['uk11_12'] > 0) {
                                        $sembilan       = number_format(($row['uk11_12'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $sembilan       = 0;
                                    }

                                    if ($row['uk12_13'] > 0) {
                                        $sepuluh        = number_format(($row['uk12_13'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $sepuluh        = 0;
                                    }

                                    if ($row['uk13_14'] > 0) {
                                        $sebelas        = number_format(($row['uk13_14'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $sebelas        = 0;
                                    }

                                    if ($row['uk14_15'] > 0) {
                                        $duabelas       = number_format(($row['uk14_15'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $duabelas       = 0;
                                    }

                                    if ($row['uk15_16'] > 0) {
                                        $tigabelas      = number_format(($row['uk15_16'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $tigabelas      = 0;
                                    }

                                    if ($row['uk16_17'] > 0) {
                                        $empatbelas     = number_format(($row['uk16_17'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $empatbelas     = 0;
                                    }

                                    if ($row['uk17_18'] > 0) {
                                        $limabelas      = number_format(($row['uk17_18'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $limabelas       = 0;
                                    }

                                    if ($row['uk18_19'] > 0) {
                                        $enambelas      = number_format(($row['uk18_19'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $enambelas       = 0;
                                    }
                                    
                                    if ($row['uk19_20'] > 0) {
                                        $tujuhbelas     = number_format(($row['uk19_20'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $tujuhbelas       = 0;
                                    }

                                    if ($row['uk20_21'] > 0) {
                                        $delapanbelas   = number_format(($row['uk20_21'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $delapanbelas   = 0;
                                    }

                                    if ($row['uk21_22'] > 0) {
                                        $sembilanbelas  = number_format(($row['uk21_22'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $sembilanbelas  = 0;
                                    }

                                    if ($row['uk22_23'] > 0) {
                                        $duapuluh       = number_format(($row['uk22_23'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $duapuluh       = 0;
                                    }

                                    if ($row['uk23_24'] > 0) {
                                        $duasatu        = number_format(($row['uk23_24'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $duasatu       = 0;
                                    }

                                    if ($row['uk24_25'] > 0) {
                                        $duadua         = number_format(($row['uk24_25'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $duadua       = 0;
                                    }

                                    if ($row['uk25_UP'] > 0) {
                                        $duatiga        = number_format(($row['uk25_UP'] * 100 ) / ($totalmendatar),2);
                                    } else {
                                        $duatiga       = 0;
                                    }



                                @endphp

                                <td class="center">{{ $loop->iteration }}</td>
                                <td class="center">{{ $row['tanggal'] }}</td>
                                <td class="center">{{ $row['supplier'] }}</td>
                                <td class="center">{{ $row['kandang'] }}</td>
                                <td class="center">{{ $row['ukuran'] }}</td>
                                <td class="center">{{ $row['rerata_terima'] }}</td>
                                <td class="center">{{ $row['persen_susut'] }} %</td>
                                <td class="center">{{ $row['ekor_mati'] }}</td>
                                <td class="center">{{ $row['uk03_04'] }} Kg</td>
                                <td class="center">{{ $satu }} %</td>
                                <td class="center">{{ $row['uk04_05'] }}</td>
                                <td class="center">{{ $dua }} %</td>
                                <td class="center">{{ $row['uk05_06'] }} Kg</td>
                                <td class="center">{{ $tiga }} %</td>
                                <td class="center">{{ $row['uk06_07'] }} Kg</td>
                                <td class="center">{{ $empat }} %</td>
                                <td class="center">{{ $row['uk07_08'] }} Kg</td>
                                <td class="center">{{ $lima }} %</td>
                                <td class="center">{{ $row['uk08_09'] }} Kg</td>
                                <td class="center">{{ $enam }} %</td>
                                <td class="center">{{ $row['uk09_10'] }} Kg</td>
                                <td class="center">{{ $tujuh }} %</td>
                                <td class="center">{{ $row['uk10_11'] }} Kg</td>
                                <td class="center">{{ $delapan }} %</td>
                                <td class="center">{{ $row['uk11_12'] }} Kg</td>
                                <td class="center">{{ $sembilan }} %</td>
                                <td class="center">{{ $row['uk12_13'] }} Kg</td>
                                <td class="center">{{ $sepuluh }} %</td>
                                <td class="center">{{ $row['uk13_14'] }} Kg</td>
                                <td class="center">{{ $sebelas }} %</td>
                                <td class="center">{{ $row['uk14_15'] }} Kg</td>
                                <td class="center">{{ $duabelas }} %</td>
                                <td class="center">{{ $row['uk15_16'] }} Kg</td>
                                <td class="center">{{ $tigabelas }} %</td>
                                <td class="center">{{ $row['uk16_17'] }} Kg</td>
                                <td class="center">{{ $empatbelas }} %</td>
                                <td class="center">{{ $row['uk17_18'] }} Kg</td>
                                <td class="center">{{ $limabelas }} %</td>
                                <td class="center">{{ $row['uk18_19'] }} Kg</td>
                                <td class="center">{{ $enambelas }} %</td>
                                <td class="center">{{ $row['uk19_20'] }} Kg</td>
                                <td class="center">{{ $tujuhbelas }} %</td>
                                <td class="center">{{ $row['uk20_21'] }} Kg</td>
                                <td class="center">{{ $delapanbelas }} %</td>
                                <td class="center">{{ $row['uk21_22'] }} Kg</td>
                                <td class="center">{{ $sembilanbelas }} %</td>
                                <td class="center">{{ $row['uk22_23'] }} Kg</td>
                                <td class="center">{{ $duapuluh }} %</td>
                                <td class="center">{{ $row['uk23_24'] }} Kg</td>
                                <td class="center">{{ $duasatu }} %</td>
                                <td class="center">{{ $row['uk24_25'] }} Kg</td>
                                <td class="center">{{ $duadua }} %</td>
                                <td class="center">{{ $row['uk25_UP'] }} Kg</td>
                                <td class="center">{{ $duatiga }} %</td>
                                <td class="center kolom bold">
                                    {{ number_format($totalmendatar,2,',','.') }} Kg
                                </td>
                            </tr>
                            @php 
                                $total_0304       = $total_0304 + $row['uk03_04'];
                                $total_0405       = $total_0405 + $row['uk04_05'];
                                $total_0506       = $total_0506 + $row['uk05_06'];
                                $total_0607       = $total_0607 + $row['uk06_07'];
                                $total_0708       = $total_0708 + $row['uk07_08'];
                                $total_0809       = $total_0809 + $row['uk08_09'];
                                $total_0910       = $total_0910 + $row['uk09_10'];
                                $total_1011       = $total_1011 + $row['uk10_11'];
                                $total_1112       = $total_1112 + $row['uk11_12'];
                                $total_1213       = $total_1213 + $row['uk12_13'];
                                $total_1314       = $total_1314 + $row['uk13_14'];
                                $total_1415       = $total_1415 + $row['uk14_15'];
                                $total_1516       = $total_1516 + $row['uk15_16'];
                                $total_1617       = $total_1617 + $row['uk16_17'];
                                $total_1718       = $total_1718 + $row['uk17_18'];
                                $total_1819       = $total_1819 + $row['uk18_19'];
                                $total_1920       = $total_1920 + $row['uk19_20'];
                                $total_2021       = $total_2021 + $row['uk20_21'];
                                $total_2122       = $total_2122 + $row['uk21_22'];
                                $total_2223       = $total_2223 + $row['uk22_23'];
                                $total_2324       = $total_2324 + $row['uk23_24'];
                                $total_2425       = $total_2425 + $row['uk24_25'];
                                $total_25UP       = $total_25UP + $row['uk25_UP'];

                                $t1                 = $t1 + $satu;
                                $t2                 = $t2 + $dua;
                                $t3                 = $t3 + $tiga;
                                $t4                 = $t4 + $empat;
                                $t5                 = $t5 + $lima;
                                $t6                 = $t6 + $enam;
                                $t7                 = $t7 + $tujuh;
                                $t8                 = $t8 + $delapan;
                                $t9                 = $t9 + $sembilan;
                                $t10                = $t10 + $sepuluh;
                                $t11                = $t11 + $sebelas;
                                $t12                = $t12 + $duabelas;
                                $t13                = $t13 + $tigabelas;
                                $t14                = $t14 + $empatbelas;
                                $t15                = $t15 + $limabelas;
                                $t16                = $t16 + $enambelas;
                                $t17                = $t17 + $tujuhbelas;
                                $t18                = $t18 + $delapanbelas;
                                $t19                = $t19 + $sembilanbelas;
                                $t20                = $t20 + $duapuluh;
                                $t21                = $t21 + $duasatu;
                                $t22                = $t22 + $duadua;
                                $t23                = $t23 + $duatiga;
                            
                                $totalsemuanya    = $total_0304 + $total_0405 + 
                                                    $total_0506 + $total_0607 + 
                                                    $total_0708 + $total_0809 + 
                                                    $total_0910 + $total_1011 +
                                                    $total_1112 + $total_1213 + 
                                                    $total_1314 + $total_1415 + 
                                                    $total_1516 + $total_1617 + 
                                                    $total_1718 + $total_1819 + 
                                                    $total_1920 + $total_2021 + 
                                                    $total_2122 + $total_2223 + 
                                                    $total_2324 + $total_2425 + 
                                                    $total_25UP;
                                $rerataterima     = $rerataterima + $row['rerata_terima'];
                                $susut            = $susut + $row['persen_susut'];
                                $mati             = $mati + $row['ekor_mati'];

                                $a  = ($t1 / $countdata);
                                $b  = ($t2 / $countdata);
                                $c  = ($t3 / $countdata);
                                $d  = ($t4 / $countdata);
                                $e  = ($t5 / $countdata);
                                $f  = ($t6 / $countdata);
                                $g  = ($t7 / $countdata);
                                $h  = ($t8 / $countdata);
                                $i  = ($t9 / $countdata);
                                $j  = ($t10 / $countdata); 
                                $k  = ($t11 / $countdata); 
                                $l  = ($t12 / $countdata); 
                                $m  = ($t13 / $countdata); 
                                $n  = ($t14 / $countdata); 
                                $o  = ($t15 / $countdata); 
                                $p  = ($t16 / $countdata); 
                                $q  = ($t17 / $countdata); 
                                $r  = ($t18 / $countdata); 
                                $s  = ($t19 / $countdata); 
                                $t  = ($t20 / $countdata); 
                                $u  = ($t21 / $countdata); 
                                $v  = ($t22 / $countdata); 
                                $w  = ($t23 / $countdata);

                                $persentase     = [ $a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l, $m, $n, $o, $p, $q, $r, $s, $t, $u, $v, $w ];
                                $dataPersen     = $persentase;
                            @endphp
                        @endforeach
                    @else
                    <tr>
                        <td colspan="32" class="center"> Tidak Ditemukan Data</td>
                    </tr>
                    @endif
                </tbody>
                @if(count($arrayItem) > 0)
                <tfoot>
                    <tr>
                        <td class="center subtotal bold" colspan="8"> Sub Total</td>
                        <td class="center subtotal bold"> {{ number_format($total_0304,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_0405,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_0506,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_0607,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_0708,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_0809,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_0910,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1011,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1112,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1213,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1314,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1415,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1516,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1617,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1718,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1819,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_1920,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_2021,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_2122,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_2223,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_2324,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_2425,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                        <td class="center subtotal bold"> {{ number_format($total_25UP,2,',','.') }} Kg</td>
                        <td class="center subtotal bold"></td>
                    </tr>
                    <tr>
                        <td colspan="8" class="center baris bold large-size"> Total</td>
                        <td colspan="46" class="center baris bold large-size"> {{ number_format($totalsemuanya,2,',','.') ?? '0' }} Kg</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
<div class="card-body">
    <div class="col-12">
        <div class="row">
            <div class="col-lg-12 mt-5">
                <div class="table-responsive">
                    <table class="table default-table">
                        <tr style="height: 50px;">
                            <th width="10%" class="bold large-medium">No</th>
                            <th width="60%" class="bold large-medium">Indikator</th>
                            <th width="30%" class="bold large-medium">Nilai</th>
                        </tr>
                        <tr style="height: 50px;">
                            <td class="bold large-medium">
                                1
                            </td>
                            <td class="bold large-medium">
                                Rerata Terima
                            </td>
                            <td class="bold large-medium">
                                @if($countdata)
                                    {{ number_format($rerataterima / $countdata,2,',','.') }} Kg
                                @else
                                    0 Kg
                                @endif
                            </td>
                        </tr>
                        <tr style="height: 50px;">
                            <td class="bold large-medium">
                                2
                            </td>
                            <td class="bold large-medium">
                                Persen Susut Perjalanan + Produksi
                            </td>
                            <td class="bold large-medium">
                                @if($countdata)
                                    {{ number_format($susut / $countdata,2,',','.') }} % 
                                @else
                                    0 %
                                @endif
                            </td>
                        </tr>
                        <tr style="height: 50px;">
                            <td class="bold large-medium">
                                3
                            </td>
                            <td class="bold large-medium">
                                Rerata Ekor Mati
                            </td>
                            <td class="bold large-medium">
                                @if($countdata)
                                    {{ number_format($mati / $countdata,1,',','.') }} Ekor
                                @else
                                    0 Ekor
                                @endif
                            </td>
                        </tr>
                        <tr style="height: 50px;">
                            <td class="bold large-medium">
                                4
                            </td>
                            <td class="bold large-medium">
                                Total Pengambilan
                            </td>
                            <td class="bold large-medium">
                                @if($countdata)
                                    {{ $countdata }}
                                @else
                                    0
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card-body">
    <div class="col-12">
        <div class="row">
            <div class="col-lg-6">
                <div class="mt-5">
                    <figure id="container-grafik"></figure>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mt-5">
                    <figure class="highcharts-figure">
                        <div id="container-grading"></div>
                    </figure>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .center{
        text-align: center;
    }
    .bold{
        font-weight: bold;
    }
    .right{
        float: right;
    }
    .kolom{
        background-color: #FDF4F7;
    }
    .subtotal{
        background-color: #efefef;
    }
    .baris{
        background-color: #F4FCFD;
    }
    .large-medium{
        font-size: medium;
    }
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 320px;
        max-width: 700px;
        margin: 1em auto;
    }
    .mt-150{
        margin-top:150px;
    }
    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }

    #container-grading,
    #container-grafik {
        width: 500px;
        height: 500px;
    }

</style>
    @php
        if($countdata){
            $persentase     = json_encode($dataPersen);
        }else{
            $datapersentase = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            $persentase     = json_encode($datapersentase);
        }
    @endphp
<script>
    // var Broiler =  arrayBroiler
    // var Persen  =  $persentase

    Highcharts.chart('container-grafik', {
        chart: {
            polar: true,
            type: 'area'
        },

        accessibility: {
            description: 'Grafik Persentase Live Bird sampai Grading'
        },

        title: {
            text: 'Grafik Persentase Grading Per Supplier',
            x: 0,
            y: 5
        },

        pane: {
            size: '75%'
        },

        xAxis: {
            categories: <?php echo $arrayBroiler; ?>,
            tickmarkPlacement: 'on',
            lineWidth: 0,
            labels:{
                style:{
                    fontSize: '9px'
                }
            }
        },

        yAxis: {
            gridLineInterpolation: 'polygon',
            lineWidth: 0,
            min: 0
        },

        tooltip: {
            shared: true,
            pointFormat: '<span style="color:{series.color}"><b>{point.y:.2f} %</b><br/>'
        },

        legend: {
            align: 'right',
            verticalAlign: 'middle',
            layout: 'vertical'
        },

        series: [
            {
                name: 'Persentase LB to Grading',
                data: <?php echo $persentase; ?>,
                pointPlacement: 'off'
            }
        ],

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 700
                },
                chartOptions: {
                    legend: {
                        align: 'center',
                        verticalAlign: 'bottom',
                        layout: 'horizontal'
                    },
                    pane: {
                        size: '90%'
                    }
                }
            }]
        }

    });


    Highcharts.chart('container-grading', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Grafik Persentase Live Bird sampai Grading'
        },
        subtitle: {
            text: null
        },
        xAxis: {
            categories: <?php echo $arrayBroiler; ?>,
            title: {
                text: 'Grading'
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Population (Percent)',
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ' Percent',
            pointFormat: '<span style="color:{series.color}"><b>{point.y:.2f} %</b><br/>'
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.y:.2f} %</b>'
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -40,
            y: 80,
            floating: true,
            borderWidth: 1,
            backgroundColor:
            Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
            shadow: true
        },
        credits: {
            enabled: true
        },
        series: [{
            name:'Grading',
            data : <?php echo $persentase;?>
        }]
    });
</script>

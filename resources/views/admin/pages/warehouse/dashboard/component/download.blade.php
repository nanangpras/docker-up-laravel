@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=download-supplier-live-bird.xls");
@endphp
<style>
    .center{
        mso-number-format:"\@";
        border:thin solid black;
        text-align: center;
    }
</style>
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
            <th class="center" width="150" colspan="3">Broiler {{ substr($item->nama,-5) }}</th>
            @endforeach
            <th class="center" width="100" colspan="2"> TOTAL </th>
        </tr>
        <tr>
            @for($x=1; $x<=23; $x++)
            <th class="center"> Ekor </th>
            <th class="center"> Kg </th>
            <th class="center"> % </th>
            @endfor
            <th class="center"> Ekor </th>
            <th class="center"> Kg </th>
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

            $total_0304_ekor    = 0;
            $total_0405_ekor    = 0;
            $total_0506_ekor    = 0;
            $total_0607_ekor    = 0;
            $total_0708_ekor    = 0;
            $total_0809_ekor    = 0;
            $total_0910_ekor    = 0;
            $total_1011_ekor    = 0;
            $total_1112_ekor    = 0;
            $total_1213_ekor    = 0;
            $total_1314_ekor    = 0;
            $total_1415_ekor    = 0;
            $total_1516_ekor    = 0;
            $total_1617_ekor    = 0;
            $total_1718_ekor    = 0;
            $total_1819_ekor    = 0;
            $total_1920_ekor    = 0;
            $total_2021_ekor    = 0;
            $total_2122_ekor    = 0;
            $total_2223_ekor    = 0;
            $total_2324_ekor    = 0;
            $total_2425_ekor    = 0;
            $total_25UP_ekor    = 0;

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

            // $countdata        = count($arrayItem);
            $countdata        = count($GradingSelesaiNotNull);

            $rerataterima     = 0;
            $susut            = 0;
            $mati             = 0;
        @endphp
        @if(count($GradingSelesaiNotNull) > 0)
            @foreach($GradingSelesaiNotNull as $row)
                <tr>
                    @php
                        $totalmendatar      = $row['uk03_04'] + $row['uk04_05'] + $row['uk05_06'] + $row['uk06_07'] + $row['uk07_08'] + $row['uk08_09'] + $row['uk09_10'] + $row['uk10_11'] + $row['uk11_12'] + $row['uk12_13'] + $row['uk13_14'] + $row['uk14_15'] + $row['uk15_16'] + $row['uk16_17'] + $row['uk17_18'] + $row['uk18_19'] + $row['uk19_20'] + $row['uk20_21'] + $row['uk21_22'] + $row['uk22_23'] + $row['uk23_24'] + $row['uk24_25'] + $row['uk25_UP'];
                        $totalmendatar_ekor = $row['uk03_04_ekor'] + $row['uk04_05_ekor'] + $row['uk05_06_ekor'] + $row['uk06_07_ekor'] + $row['uk07_08_ekor'] + $row['uk08_09_ekor'] + $row['uk09_10_ekor'] + $row['uk10_11_ekor'] + $row['uk11_12_ekor'] + $row['uk12_13_ekor'] + $row['uk13_14_ekor'] + $row['uk14_15_ekor'] + $row['uk15_16_ekor'] + $row['uk16_17_ekor'] + $row['uk17_18_ekor'] + $row['uk18_19_ekor'] + $row['uk19_20_ekor'] + $row['uk20_21_ekor'] + $row['uk21_22_ekor'] + $row['uk22_23_ekor'] + $row['uk23_24_ekor'] + $row['uk24_25_ekor'] + $row['uk25_UP_ekor'];

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
                    <td class="center">{{ $row['uk03_04_ekor'] }}</td>
                    <td class="center">{{ $row['uk03_04'] }} </td>
                    <td class="center">{{ $satu }} %</td>
                    <td class="center">{{ $row['uk04_05_ekor'] }} </td>
                    <td class="center">{{ $row['uk04_05'] }} </td>
                    <td class="center">{{ $dua }} %</td>
                    <td class="center">{{ $row['uk05_06_ekor'] }} </td>
                    <td class="center">{{ $row['uk05_06'] }} </td>
                    <td class="center">{{ $tiga }} %</td>
                    <td class="center">{{ $row['uk06_07_ekor'] }} </td>
                    <td class="center">{{ $row['uk06_07'] }} </td>
                    <td class="center">{{ $empat }} %</td>
                    <td class="center">{{ $row['uk07_08_ekor'] }} </td>
                    <td class="center">{{ $row['uk07_08'] }} </td>
                    <td class="center">{{ $lima }} %</td>
                    <td class="center">{{ $row['uk08_09_ekor'] }} </td>
                    <td class="center">{{ $row['uk08_09'] }} </td>
                    <td class="center">{{ $enam }} %</td>
                    <td class="center">{{ $row['uk09_10_ekor'] }} </td>
                    <td class="center">{{ $row['uk09_10'] }} </td>
                    <td class="center">{{ $tujuh }} %</td>
                    <td class="center">{{ $row['uk10_11_ekor'] }} </td>
                    <td class="center">{{ $row['uk10_11'] }} </td>
                    <td class="center">{{ $delapan }} %</td>
                    <td class="center">{{ $row['uk11_12_ekor'] }} </td>
                    <td class="center">{{ $row['uk11_12'] }} </td>
                    <td class="center">{{ $sembilan }} %</td>
                    <td class="center">{{ $row['uk12_13_ekor'] }} </td>
                    <td class="center">{{ $row['uk12_13'] }} </td>
                    <td class="center">{{ $sepuluh }} %</td>
                    <td class="center">{{ $row['uk13_14_ekor'] }} </td>
                    <td class="center">{{ $row['uk13_14'] }} </td>
                    <td class="center">{{ $sebelas }} %</td>
                    <td class="center">{{ $row['uk14_15_ekor'] }} </td>
                    <td class="center">{{ $row['uk14_15'] }} </td>
                    <td class="center">{{ $duabelas }} %</td>
                    <td class="center">{{ $row['uk15_16_ekor'] }} </td>
                    <td class="center">{{ $row['uk15_16'] }} </td>
                    <td class="center">{{ $tigabelas }} %</td>
                    <td class="center">{{ $row['uk16_17_ekor'] }} </td>
                    <td class="center">{{ $row['uk16_17'] }} </td>
                    <td class="center">{{ $empatbelas }} %</td>
                    <td class="center">{{ $row['uk17_18_ekor'] }} </td>
                    <td class="center">{{ $row['uk17_18'] }} </td>
                    <td class="center">{{ $limabelas }} %</td>
                    <td class="center">{{ $row['uk18_19_ekor'] }} </td>
                    <td class="center">{{ $row['uk18_19'] }} </td>
                    <td class="center">{{ $enambelas }} %</td>
                    <td class="center">{{ $row['uk19_20_ekor'] }} </td>
                    <td class="center">{{ $row['uk19_20'] }} </td>
                    <td class="center">{{ $tujuhbelas }} %</td>
                    <td class="center">{{ $row['uk20_21_ekor'] }} </td>
                    <td class="center">{{ $row['uk20_21'] }} </td>
                    <td class="center">{{ $delapanbelas }} %</td>
                    <td class="center">{{ $row['uk21_22_ekor'] }} </td>
                    <td class="center">{{ $row['uk21_22'] }} </td>
                    <td class="center">{{ $sembilanbelas }} %</td>
                    <td class="center">{{ $row['uk22_23_ekor'] }} </td>
                    <td class="center">{{ $row['uk22_23'] }} </td>
                    <td class="center">{{ $duapuluh }} %</td>
                    <td class="center">{{ $row['uk23_24_ekor'] }} </td>
                    <td class="center">{{ $row['uk23_24'] }} </td>
                    <td class="center">{{ $duasatu }} %</td>
                    <td class="center">{{ $row['uk24_25_ekor'] }} </td>
                    <td class="center">{{ $row['uk24_25'] }} </td>
                    <td class="center">{{ $duadua }} %</td>
                    <td class="center">{{ $row['uk25_UP_ekor'] }} </td>
                    <td class="center">{{ $row['uk25_UP'] }} </td>
                    <td class="center">{{ $duatiga }} %</td>
                    <td class="center">
                        {{ number_format($totalmendatar_ekor) }}
                    </td>
                    <td class="center">
                        {{ number_format($totalmendatar,2,',','.') }}
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

                    $total_0304_ekor       = $total_0304_ekor + $row['uk03_04_ekor'];
                    $total_0405_ekor       = $total_0405_ekor + $row['uk04_05_ekor'];
                    $total_0506_ekor       = $total_0506_ekor + $row['uk05_06_ekor'];
                    $total_0607_ekor       = $total_0607_ekor + $row['uk06_07_ekor'];
                    $total_0708_ekor       = $total_0708_ekor + $row['uk07_08_ekor'];
                    $total_0809_ekor       = $total_0809_ekor + $row['uk08_09_ekor'];
                    $total_0910_ekor       = $total_0910_ekor + $row['uk09_10_ekor'];
                    $total_1011_ekor       = $total_1011_ekor + $row['uk10_11_ekor'];
                    $total_1112_ekor       = $total_1112_ekor + $row['uk11_12_ekor'];
                    $total_1213_ekor       = $total_1213_ekor + $row['uk12_13_ekor'];
                    $total_1314_ekor       = $total_1314_ekor + $row['uk13_14_ekor'];
                    $total_1415_ekor       = $total_1415_ekor + $row['uk14_15_ekor'];
                    $total_1516_ekor       = $total_1516_ekor + $row['uk15_16_ekor'];
                    $total_1617_ekor       = $total_1617_ekor + $row['uk16_17_ekor'];
                    $total_1718_ekor       = $total_1718_ekor + $row['uk17_18_ekor'];
                    $total_1819_ekor       = $total_1819_ekor + $row['uk18_19_ekor'];
                    $total_1920_ekor       = $total_1920_ekor + $row['uk19_20_ekor'];
                    $total_2021_ekor       = $total_2021_ekor + $row['uk20_21_ekor'];
                    $total_2122_ekor       = $total_2122_ekor + $row['uk21_22_ekor'];
                    $total_2223_ekor       = $total_2223_ekor + $row['uk22_23_ekor'];
                    $total_2324_ekor       = $total_2324_ekor + $row['uk23_24_ekor'];
                    $total_2425_ekor       = $total_2425_ekor + $row['uk24_25_ekor'];
                    $total_25UP_ekor       = $total_25UP_ekor + $row['uk25_UP_ekor'];

                    $t1                 = $total_0304;
                    $t2                 = $total_0405;
                    $t3                 = $total_0506;
                    $t4                 = $total_0607;
                    $t5                 = $total_0708;
                    $t6                 = $total_0809;
                    $t7                 = $total_0910;
                    $t8                 = $total_1011;
                    $t9                 = $total_1112;
                    $t10                = $total_1213;
                    $t11                = $total_1314;
                    $t12                = $total_1415;
                    $t13                = $total_1516;
                    $t14                = $total_1617;
                    $t15                = $total_1718;
                    $t16                = $total_1819;
                    $t17                = $total_1920;
                    $t18                = $total_2021;
                    $t19                = $total_2122;
                    $t20                = $total_2223;
                    $t21                = $total_2324;
                    $t22                = $total_2425;
                    $t23                = $total_25UP;

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

                    $totalsemuanya_ekor = $total_0304_ekor + $total_0405_ekor +
                                        $total_0506_ekor + $total_0607_ekor +
                                        $total_0708_ekor + $total_0809_ekor +
                                        $total_0910_ekor + $total_1011_ekor +
                                        $total_1112_ekor + $total_1213_ekor +
                                        $total_1314_ekor + $total_1415_ekor +
                                        $total_1516_ekor + $total_1617_ekor +
                                        $total_1718_ekor + $total_1819_ekor +
                                        $total_1920_ekor + $total_2021_ekor +
                                        $total_2122_ekor + $total_2223_ekor +
                                        $total_2324_ekor + $total_2425_ekor +
                                        $total_25UP_ekor;

                    $rerataterima     = $rerataterima + $row['rerata_terima'];
                    $susut            = $susut + $row['persen_susut'];
                    $mati             = $mati + $row['ekor_mati'];

                    $a  = $totalsemuanya != '0' ? ($t1 / $totalsemuanya) * 100 : '0';
                    $b  = $totalsemuanya != '0' ? ($t2 / $totalsemuanya) * 100 : '0';
                    $c  = $totalsemuanya != '0' ? ($t3 / $totalsemuanya) * 100 : '0';
                    $d  = $totalsemuanya != '0' ? ($t4 / $totalsemuanya) * 100 : '0';
                    $e  = $totalsemuanya != '0' ? ($t5 / $totalsemuanya) * 100 : '0';
                    $f  = $totalsemuanya != '0' ? ($t6 / $totalsemuanya) * 100 : '0';
                    $g  = $totalsemuanya != '0' ? ($t7 / $totalsemuanya) * 100 : '0';
                    $h  = $totalsemuanya != '0' ? ($t8 / $totalsemuanya) * 100 : '0';
                    $i  = $totalsemuanya != '0' ? ($t9 / $totalsemuanya) * 100 : '0';
                    $j  = $totalsemuanya != '0' ? ($t10 / $totalsemuanya) * 100 : '0';
                    $k  = $totalsemuanya != '0' ? ($t11 / $totalsemuanya) * 100 : '0';
                    $l  = $totalsemuanya != '0' ? ($t12 / $totalsemuanya) * 100 : '0';
                    $m  = $totalsemuanya != '0' ? ($t13 / $totalsemuanya) * 100 : '0';
                    $n  = $totalsemuanya != '0' ? ($t14 / $totalsemuanya) * 100 : '0';
                    $o  = $totalsemuanya != '0' ? ($t15 / $totalsemuanya) * 100 : '0';
                    $p  = $totalsemuanya != '0' ? ($t16 / $totalsemuanya) * 100 : '0';
                    $q  = $totalsemuanya != '0' ? ($t17 / $totalsemuanya) * 100 : '0';
                    $r  = $totalsemuanya != '0' ? ($t18 / $totalsemuanya) * 100 : '0';
                    $s  = $totalsemuanya != '0' ? ($t19 / $totalsemuanya) * 100 : '0';
                    $t  = $totalsemuanya != '0' ? ($t20 / $totalsemuanya) * 100 : '0';
                    $u  = $totalsemuanya != '0' ? ($t21 / $totalsemuanya) * 100 : '0';
                    $v  = $totalsemuanya != '0' ? ($t22 / $totalsemuanya) * 100 : '0';
                    $w  = $totalsemuanya != '0' ? ($t23 / $totalsemuanya) * 100 : '0';

                    $persentase     = [ $a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l, $m, $n, $o, $p, $q, $r, $s, $t, $u, $v, $w ];
                    $persenKG       = [$total_0304, $total_0405,$total_0506,$total_0607,$total_0708,$total_0809,$total_0910,$total_1011,$total_1112,$total_1213,$total_1314,$total_1415,$total_1516,$total_1617,$total_1718,$total_1819,$total_1920,$total_2021,$total_2122,$total_2223,$total_2324,$total_2425,$total_25UP];
                    $persenEkor     = [$total_0304_ekor, $total_0405_ekor,$total_0506_ekor,$total_0607_ekor,$total_0708_ekor,$total_0809_ekor,$total_0910_ekor,$total_1011_ekor,$total_1112_ekor,$total_1213_ekor,$total_1314_ekor,$total_1415_ekor,$total_1516_ekor,$total_1617_ekor,$total_1718_ekor,$total_1819_ekor,$total_1920_ekor,$total_2021_ekor,$total_2122_ekor,$total_2223_ekor,$total_2324_ekor,$total_2425_ekor,$total_25UP_ekor];
                @endphp
            @endforeach
        @else
        <tr>
            <td colspan="32" class="center"> Tidak Ditemukan Data</td>
        </tr>
        @endif
    </tbody>
    @if(count($GradingSelesaiNotNull) > 0)
    <tfoot>
        <tr>
            <td class="center" colspan="8"> Total</td>
            <td class="center">{{ $total_0304_ekor }}</td>
            <td class="center"> {{ number_format($total_0304,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_0405_ekor }}</td>
            <td class="center"> {{ number_format($total_0405,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_0506_ekor }}</td>
            <td class="center"> {{ number_format($total_0506,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_0607_ekor }}</td>
            <td class="center"> {{ number_format($total_0607,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_0708_ekor }}</td>
            <td class="center"> {{ number_format($total_0708,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_0809_ekor }}</td>
            <td class="center"> {{ number_format($total_0809,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_0910_ekor }}</td>
            <td class="center"> {{ number_format($total_0910,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1011_ekor }}</td>
            <td class="center"> {{ number_format($total_1011,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1112_ekor }}</td>
            <td class="center"> {{ number_format($total_1112,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1213_ekor }}</td>
            <td class="center"> {{ number_format($total_1213,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1314_ekor }}</td>
            <td class="center"> {{ number_format($total_1314,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1415_ekor }}</td>
            <td class="center"> {{ number_format($total_1415,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1516_ekor }}</td>
            <td class="center"> {{ number_format($total_1516,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1617_ekor }}</td>
            <td class="center"> {{ number_format($total_1617,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1718_ekor }}</td>
            <td class="center"> {{ number_format($total_1718,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1819_ekor }}</td>
            <td class="center"> {{ number_format($total_1819,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_1920_ekor }}</td>
            <td class="center"> {{ number_format($total_1920,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_2021_ekor }}</td>
            <td class="center"> {{ number_format($total_2021,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_2122_ekor }}</td>
            <td class="center"> {{ number_format($total_2122,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_2223_ekor }}</td>
            <td class="center"> {{ number_format($total_2223,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_2324_ekor }}</td>
            <td class="center"> {{ number_format($total_2324,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_2425_ekor }}</td>
            <td class="center"> {{ number_format($total_2425,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center">{{ $total_25UP_ekor }}</td>
            <td class="center"> {{ number_format($total_25UP,2,',','.') }} Kg</td>
            <td class="center"></td>
            <td class="center"> {{ $totalsemuanya_ekor ?? '0' }} Ekor</td>
            <td class="center"> {{ number_format($totalsemuanya,2,',','.') ?? '0' }} Kg</td>
        </tr>
    </tfoot>
    @endif
</table>
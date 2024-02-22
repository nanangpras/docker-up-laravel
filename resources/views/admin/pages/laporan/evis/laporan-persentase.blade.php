@if ($download == true)
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=Evis-Persentase.xls');
    @endphp
@endif
<style>
    th,
    td {
        border: 1px solid #ddd;
    }
</style>
<section class="panel">
    <div class="col">
        <div class="form-group">
            @if ($download == false)
            <button type="submit" class="btn btn-blue float-right mb-2 downloadLaporanPersentase"><i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Download</span></button>
            @endif
        </div>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="center">Item</th>
                        <th colspan="3" class="center">Hasil Produksi</th>
                        <th rowspan="2" class="center">Total Paket & Eceran</th>
                        <th rowspan="2" class="center">Stock Frozen</th>
                        <th rowspan="2" class="center">Sisa</th>
                        <th rowspan="2" class="center">Stock Chiller</th>
                        <th rowspan="2" class="center">Selisih Susut</th>
                        <th rowspan="2" class="center">%</th>
                    </tr>
                    <tr>
                        <th class="center">Baru</th>
                        <th class="center">Lama</th>
                        <th class="center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataAtas as $it)
                        <tr>
                            <td class="center">Ati/Ampela</td>
                            <td class="center">{{number_format($it['ati_ampela_baru'],2,',', '.')  }}</td>
                            <td class="center">{{number_format($it['ati_ampela_lama'],2,',', '.')  }}</td>
                            <td class="center">{{number_format($it['total_produksi_ati_ampela'],2,',', '.')  }}</td>
                            <td class="center">{{number_format($it['ecer_retail_atiampela'],2,',', '.') }}</td>
                            <td class="center">{{number_format($it['stock_frz_ampela'],2,',', '.')}}</td>
                            <td class="center">{{number_format($it['sisa_ati_ampela'],2,',', '.')}}</td>
                            <td class="center">{{number_format($it['stock_chiller_atiampela'],2,',', '.')}}</td>
                            <td class="center">{{number_format($it['sisa_ati_ampela'] - $it['stock_chiller_atiampela'],2,',', '.')}}</td>
                            @if (($it['ati_ampela_baru'] + $it['ati_ampela_lama']) == 0)
                                <td class="center">0</td>
                            @else
                                <td class="center">{{number_format(($it['sisa_ati_ampela'] - $it['stock_chiller_atiampela']) / ($it['ati_ampela_baru'] + $it['ati_ampela_lama']) * 100 ,2,',', '.') }} %</td>
                            @endif
                        </tr>
                    @endforeach
                    @foreach ($dataAtas as $it)
                        <tr>
                            <td class="center">Kepala</td>
                            <td class="center">{{ number_format($it['kepala_baru'],2,',', '.')  }}</td>
                            <td class="center">{{ number_format($it['kepala_lama'],2,',', '.')  }}</td>
                            <td class="center">{{ number_format($it['total_produksi_kepala'],2,',', '.')  }}</td>
                            <td class="center">{{ number_format($it['ecer_retail_kepala'],2,',', '.') }}</td>
                            <td class="center">{{ number_format($it['stock_frz_kepala'],2,',', '.')}}</td>
                            <td class="center">{{ number_format($it['sisa_kepala'],2,',', '.')}}</td>
                            <td class="center">{{ number_format($it['stock_chiller_kepala'],2,',', '.')}}</td>
                            <td class="center">{{ number_format($it['sisa_kepala'] - $it['stock_chiller_kepala'],2,',', '.')}}</td>
                            @if (($it['kepala_baru'] + $it['kepala_lama']) == 0)
                                <td class="center">0</td>
                            @else
                                <td class="center">{{number_format(($it['sisa_kepala'] - $it['stock_chiller_kepala']) / ($it['kepala_baru'] + $it['kepala_lama']) * 100 ,2,',', '.') }} %</td>
                            @endif
                        </tr>
                    @endforeach
                    @foreach ($dataAtas as $it)
                        <tr>
                            <td class="center">Kaki</td>
                            <td class="center">{{number_format($it['kaki_baru'],2,',', '.')  }}</td>
                            <td class="center">{{number_format($it['kaki_lama'],2,',', '.')  }}</td>
                            <td class="center">{{number_format($it['total_produksi_kaki'],2,',', '.')  }}</td>
                            <td class="center">{{number_format($it['ecer_retail_kaki'],2,',', '.') }}</td>
                            <td class="center">{{number_format($it['stock_frz_kaki'],2,',', '.')}}</td>
                            <td class="center">{{number_format($it['sisa_kaki'],2,',', '.')}}</td>
                            <td class="center">{{number_format($it['stock_chiller_kaki'],2,',', '.')}}</td>
                            <td class="center">{{number_format($it['sisa_kaki'] - $it['stock_chiller_kaki'],2,',', '.')}}</td>
                            @if (($it['kaki_baru'] + $it['kaki_lama']) == 0)
                                <td class="center">0</td>
                            @else
                                <td class="center">{{number_format(($it['sisa_kaki'] - $it['stock_chiller_kaki']) / ($it['kaki_baru'] + $it['kaki_lama']) * 100 ,2,',', '.') }} %</td>
                            @endif
                        </tr>
                    @endforeach
                    
                    @foreach ($dataAtas as $it)
                        <tr>
                            <td class="center">Usus</td>
                            <td class="center">{{number_format($it['usus_baru'],2,',', '.')  }}</td>
                            <td class="center">{{number_format($it['usus_lama'],2,',', '.')  }}</td>
                            <td class="center">{{number_format($it['total_produksi_usus'],2,',', '.')  }}</td>
                            <td class="center">{{number_format($it['ecer_retail_usus'],2,',', '.') }}</td>
                            <td class="center">{{number_format($it['stock_frz_usus'],2,',', '.')}}</td>
                            <td class="center">{{number_format($it['sisa_usus'],2,',', '.')}}</td>
                            <td class="center">{{number_format($it['stock_chiller_usus'],2,',', '.') }}</td>
                            <td class="center">{{number_format($it['sisa_usus'] - $it['stock_chiller_usus'],2,',', '.')}}</td>
                            @if (($it['usus_baru'] + $it['usus_lama']) == 0)
                                <td class="center">0</td>
                            @else
                                <td class="center">{{number_format(($it['sisa_usus'] - $it['stock_chiller_usus']) / ($it['usus_baru'] + $it['usus_lama']) * 100 ,2,',', '.') }} %</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <br />
    <br />
    <div class="card-body mt-2">
        <div class="table-responsive">
            <table class="default-table" width="1200px">
                <thead>
                    <tr>
                        <th rowspan="2" class="center" width="100px">Tanggal</th>
                        <th colspan="4" class="center">Hasil Produksi</th>
                        <th rowspan="2" class="center">Ati / Ampela Bersih (Kg) </th>
                        <th colspan="4" class="center">Penjualan Barang Baru</th>
                        <th colspan="4" class="center">Sisa / Stok</th>
                    </tr>
                    <tr>
                        <th class="center" width="80px">Ati / Ampela (Kg)</th>
                        <th class="center" width="80px">Kepala (Kg)</th>
                        <th class="center" width="80px">Kaki (Kg)</th>
                        <th class="center" width="80px">Usus (Kg)</th>

                        <th class="center" width="80px">Ati / Ampela (Kg)</th>
                        <th class="center" width="80px">Kepala (Kg)</th>
                        <th class="center" width="80px">Kaki (Kg)</th>
                        <th class="center" width="80px">Usus (Kg)</th>
                    
                        <th class="center" width="80px">Ati / Ampela (Kg)</th>
                        <th class="center" width="80px">Kepala (Kg)</th>
                        <th class="center" width="80px">Kaki (Kg)</th>
                        <th class="center" width="80px">Usus (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_prod_ati_ampela  = 0;
                        $total_prod_kepala      = 0;
                        $total_prod_kaki        = 0;
                        $total_prod_usus        = 0;
                        $total_prod_hatibersih  = 0;
                        
                        $total_sell_ati_ampela  = 0;
                        $total_sell_kepala      = 0;
                        $total_sell_kaki        = 0;
                        $total_sell_usus        = 0;

                        $total_stok_ati_ampela  = 0;
                        $total_stok_kepala      = 0;
                        $total_stok_kaki        = 0;
                        $total_stok_usus        = 0;

                    @endphp
                    @foreach ($dataPersentase as $dt)
                        <tr>
                            <td class="center">{{ $dt['tanggal'] }}</td>
                            <td class="center">{{ number_format($dt['bb_ati_ampela'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['bb_kepala'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['bb_kaki'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['bb_usus'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['hatibersih'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['sell_ati_ampela'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['sell_kepala'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['sell_kaki'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['sell_usus'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['stok_ati_ampela'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['stok_kepala'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['stok_kaki'], 2,',', '.') }}</td>
                            <td class="center">{{ number_format($dt['stok_usus'], 2,',', '.') }}</td>
                        </tr>
                        @php
                            $total_prod_ati_ampela += $dt['bb_ati_ampela'];
                            $total_prod_kepala     += $dt['bb_kepala'];
                            $total_prod_kaki       += $dt['bb_kaki'];
                            $total_prod_usus       += $dt['bb_usus'];
                            $total_prod_hatibersih += $dt['hatibersih'];

                            $total_sell_ati_ampela += $dt['sell_ati_ampela'];
                            $total_sell_kepala     += $dt['sell_kepala'];
                            $total_sell_kaki       += $dt['sell_kaki'];
                            $total_sell_usus       += $dt['sell_usus'];

                            $total_stok_ati_ampela += $dt['stok_ati_ampela'];
                            $total_stok_kepala     += $dt['stok_kepala'];
                            $total_stok_kaki       += $dt['stok_kaki'];
                            $total_stok_usus       += $dt['stok_usus'];

                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="center" style="font-weight:bold;"> TOTAL </td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_prod_ati_ampela , 2,',', '.')}}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_prod_kepala, 2,',', '.') }}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_prod_kaki, 2,',', '.') }}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_prod_usus, 2,',', '.') }}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_prod_hatibersih, 2,',', '.') }}</td>

                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_sell_ati_ampela, 2,',', '.') }}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_sell_kepala, 2,',', '.') }}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_sell_kaki, 2,',', '.') }}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_sell_usus, 2,',', '.') }}</td>

                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_stok_ati_ampela, 2,',', '.') }}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_stok_kepala, 2,',', '.') }}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_stok_kaki, 2,',', '.') }}</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($total_stok_usus, 2,',', '.') }}</td>

                    </tr>
                    @php 
                    if ($total_prod_ati_ampela) {
                        $persen_ati_ampela      = ($total_sell_ati_ampela + $total_prod_hatibersih) / $total_prod_ati_ampela * 100;
                    }else{
                        $persen_ati_ampela =0;
                    }
                    if ($total_prod_kepala) {
                        $persen_kepala          = $total_sell_kepala / $total_prod_kepala * 100;
                    }else{
                        $persen_kepala=0;
                    }
                    if ($total_prod_kaki) {
                        $persen_kaki            = $total_sell_kaki / $total_prod_kaki * 100;
                    }else{
                        $persen_kaki=0;
                    }
                    if ($total_prod_usus) {
                        $persen_usus            = $total_sell_usus / $total_prod_usus * 100;
                    }else{
                        $persen_usus=0;
                    }

                    @endphp
                    <tr>
                        <td class="center"> % </td>
                        <td class="center" colspan="5"></td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($persen_ati_ampela ?? '0', 2,',', '.') }} %</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($persen_kepala ?? '0', 2,',', '.')}} %</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($persen_kaki ?? '0', 2,',', '.')}} %</td>
                        <td class="center" style="background-color:#EDEF6D; font-weight:bold;">{{ number_format($persen_usus ?? '0', 2,',', '.')}} %</td>
                        <td class="center" colspan="5"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

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
    $(".downloadLaporanPersentase").on('click', () => {
        var tanggalMulai      = $("#tanggalMulai").val();
        var tanggalSelesai    = $("#tanggalSelesai").val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('evis.laporan', ['key' => 'laporanPersentase']) }}&subkey=download" + "&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai,
            method: "GET",
            beforeSend: function() {
                $('.downloadLaporanPersentase').attr('disabled');
                $(".spinerloading").show(); 
                $("#text").text('Downloading...');
            },
            success: function(data) {
                window.location = "{{ route('evis.laporan', ['key' => 'laporanPersentase']) }}&subkey=download" + "&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai
                $("#text").text('Download');
                $(".spinerloading").hide();
            }
        });
    })
</script>
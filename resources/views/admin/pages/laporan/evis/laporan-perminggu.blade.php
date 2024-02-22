@if ($download == true)
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=Evis-Perminggu.xls');
    @endphp
@endif
<style>
    th,
    td {
        border: 1px solid #ddd;
    }
</style>
<section class="panel">
    <div class="card-body">
        <div class="form-group">
            @if ($download == false)
            <button type="submit" class="btn btn-blue float-right mb-2 downloadLaporanPerminggu"><i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Download</span></button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-striped table-bordered table-small">
                <thead>
                    <tr class="center">
                        <th rowspan="2" style="text-align:center">Tanggal</th>
                        <th rowspan="2" style="text-align:center">Jumlah Mobil</th>
                        <th rowspan="2" style="background-color:#b6d9ac;">Jumlah Pemotongan LPAH</th>
                        <th colspan="4" style="text-align:center">Hasil Produksi</th>
                        <th rowspan="2" style="background-color:#d9efd3;">Ati Ampela Bersih</th>
                        <th colspan="3" style="background-color:#eae78f; text-align:center">Lama</th>
                        <th colspan="4" style="background-color:#fea7a7; text-align:center">Penjualan</th>
                        <th colspan="3" style="background-color:#e1e1e1; text-align:center">Frozen</th>
                        <th colspan="4" style="background-color:#95ed7d; text-align:center">Sisa/Stok</th>
                    </tr>
                    <tr class="center">
                        <th style="text-align:center">Ati/Ampela</th>
                        <th style="text-align:center">Kepala</th>
                        <th style="text-align:center">Kaki</th>
                        <th style="text-align:center">Usus</th>
                        <th style="background-color:#eae78f; text-align:center">Ati/Ampela</th>
                        <th style="background-color:#eae78f; text-align:center">Kepala</th>
                        <th style="background-color:#eae78f; text-align:center">Kaki</th>
                        <th style="background-color:#fea7a7; text-align:center">Ati/Ampela</th>
                        <th style="background-color:#fea7a7; text-align:center">Kepala</th>
                        <th style="background-color:#fea7a7; text-align:center">Kaki</th>
                        <th style="background-color:#fea7a7; text-align:center">Usus</th>
                        <th style="background-color:#e1e1e1; text-align:center">Ati/Ampela</th>
                        <th style="background-color:#e1e1e1; text-align:center">Kepala</th>
                        <th style="background-color:#e1e1e1; text-align:center">Kaki</th>
                        <th style="background-color:#95ed7d;">Ati/Ampela</th>
                        <th style="background-color:#95ed7d;">Kepala</th>
                        <th style="background-color:#95ed7d;">Kaki</th>
                        <th style="background-color:#95ed7d;">Usus</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_jml_mobil        = 0;
                        $total_jml_potonglpah   = 0;
                        $total_prod_atiampela   = 0;
                        $total_prod_kepala      = 0;
                        $total_prod_kaki        = 0;
                        $total_prod_usus        = 0;
                        $total_atibersih        = 0;
                        $total_penj_atiampela   = 0;
                        $total_penj_kepala      = 0;
                        $total_penj_kaki        = 0;
                        $total_penj_usus        = 0;
                        $total_frz_atiampela    = 0;
                        $total_frz_kepala       = 0;
                        $total_frz_kaki         = 0;
                    @endphp
                    @foreach ($dataMingguan as $item)
                    <tr class="center">
                        <td>{{$item['tanggal']}}</td>
                        <td>{{$item['jml_mobil']}}</td>
                        <td>{{number_format($item['jml_potong_lpah'],2,',', '.')}}</td>
                        <td>{{number_format($item['bb_ati_ampela'],2,',', '.')}}</td>
                        <td>{{number_format($item['bb_prod_kepala'],2,',', '.')}}</td>
                        <td>{{number_format($item['bb_prod_kaki'],2,',', '.')}}</td>
                        <td>{{number_format($item['bb_prod_usus'],2,',', '.')}}</td>
                        <td>{{number_format($item['hati_bersih'],2,',', '.')}}</td>
                        <td>{{number_format($item['ati_ampela_lama'],2,',', '.')}}</td>
                        <td>{{number_format($item['kepala_lama'],2,',', '.')}}</td>
                        <td>{{number_format($item['kaki_lama'],2,',', '.')}}</td>
                        <td>{{number_format($item['penjualan_atiampela'],2,',', '.')}}</td>
                        <td>{{number_format($item['penjualan_kepala'],2,',', '.')}}</td>
                        <td>{{number_format($item['penjualan_kaki'],2,',', '.')}}</td>
                        <td>{{number_format($item['penjualan_usus'],2,',', '.')}}</td>
                        <td>{{number_format($item['frz_ati_ampela'],2,',', '.')}}</td>
                        <td>{{number_format($item['frz_kepala'],2,',', '.')}}</td>
                        <td>{{number_format($item['frz_kaki'],2,',', '.')}}</td>
                        <td>{{ number_format($item['bb_ati_ampela'] - $item['hati_bersih'] - $item['penjualan_atiampela'],2,',', '.') }}</td>
                        <td>{{ number_format($item['bb_prod_kepala'] - $item['penjualan_kepala'],2,',', '.')}}</td>
                        <td>{{ number_format($item['bb_prod_kaki'] - $item['penjualan_kaki'],2,',', '.')}}</td>
                        <td>{{ number_format($item['bb_prod_usus'] - $item['penjualan_usus'],2,',', '.')}}</td>
                    </tr>
                        @php
                            $total_jml_mobil        += $item['jml_mobil'];
                            $total_jml_potonglpah   += $item['jml_potong_lpah'];
                            $total_prod_atiampela   += $item['bb_ati_ampela'];
                            $total_prod_kepala      += $item['bb_prod_kepala'];
                            $total_prod_kaki        += $item['bb_prod_kaki'];
                            $total_prod_usus        += $item['bb_prod_usus'];
                            $total_atibersih        += $item['hati_bersih'];
                            $total_penj_atiampela   += $item['penjualan_atiampela'];
                            $total_penj_kepala      += $item['penjualan_kepala'];
                            $total_penj_kaki        += $item['penjualan_kaki'];
                            $total_penj_usus        += $item['penjualan_usus'];
                            $total_frz_atiampela    += $item['frz_ati_ampela'];
                            $total_frz_kepala       += $item['frz_kepala'];
                            $total_frz_kaki         += $item['frz_kaki'];
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color:#EDEF6D; font-weight:bold;" class="center">
                        <td>TOTAL</td>
                        <td>{{ number_format($total_jml_mobil,2,',', '.')  }}</td>
                        <td>{{ number_format($total_jml_potonglpah,2,',', '.') ?? 0 }}</td>
                        <td>{{ number_format($total_prod_atiampela,2,',', '.') }}</td>
                        <td>{{ number_format($total_prod_kepala,2,',', '.') }}</td>
                        <td>{{ number_format($total_prod_kaki,2,',', '.') }}</td>
                        <td>{{ number_format($total_prod_usus,2,',', '.') }}</td>
                        <td>{{ number_format($total_atibersih,2,',', '.') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($total_penj_atiampela,2,',', '.') }}</td>
                        <td>{{ number_format($total_penj_kepala,2,',', '.') }}</td>
                        <td>{{ number_format($total_penj_kaki,2,',', '.') }}</td>
                        <td>{{ number_format($total_penj_usus,2,',', '.') }}</td>
                        <td>{{ number_format($total_frz_atiampela,2,',', '.') }}</td>
                        <td>{{ number_format($total_frz_kepala,2,',', '.') }}</td>
                        <td>{{ number_format($total_frz_kaki,2,',', '.') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @php
                        if ($total_jml_potonglpah) {
                            $persen_prod_atiampela  = $total_prod_atiampela/$total_jml_potonglpah * 100;
                            $persen_prod_kepala     = $total_prod_kepala/$total_jml_potonglpah * 100;
                            $persen_prod_kaki       = $total_prod_kaki/$total_jml_potonglpah * 100;
                            $persen_prod_usus       = $total_prod_usus/$total_jml_potonglpah * 100;
                        }else{
                            $persen_prod_atiampela=0;
                            $persen_prod_kepala=0;
                            $persen_prod_kaki=0;
                            $persen_prod_usus=0;
                        }

                        if($total_prod_atiampela){
                            $persen_penj_atiampela  = $total_penj_atiampela/$total_prod_atiampela * 100;
                        }else{
                            $persen_penj_atiampela  = 0;
                        }

                        if($total_prod_kepala){
                            $persen_penj_kepala     = $total_penj_kepala/$total_prod_kepala * 100;
                        }else{
                            $persen_penj_kepala     = 0;
                        }

                        if($total_prod_kaki){
                            $persen_penj_kaki       = $total_penj_kaki/$total_prod_kaki * 100;
                        }else{
                            $persen_penj_kaki       = 0;
                        }

                        if($total_prod_usus){
                            $persen_penj_usus       = $total_penj_usus/$total_prod_usus * 100;
                        }else{
                            $persen_penj_usus       = 0;
                        }
                    @endphp
                    <tr>
                        <td colspan="3"></td>
                        <td class="center" style="background-color:#9fcfe2; font-weight:bold;">{{number_format($persen_prod_atiampela,2,',', '.')}} %</td>
                        <td class="center" style="background-color:#9fcfe2; font-weight:bold;">{{number_format($persen_prod_kepala,2,',', '.')}} %</td>
                        <td class="center" style="background-color:#9fcfe2; font-weight:bold;">{{number_format($persen_prod_kaki,2,',', '.')}} %</td>
                        <td class="center" style="background-color:#9fcfe2; font-weight:bold;">{{number_format($persen_prod_usus,2,',', '.')}} %</td>
                        <td colspan="4"></td>
                        <td class="center" style="background-color:#9fcfe2; font-weight:bold;">{{number_format($persen_penj_atiampela,2,',', '.')}} %</td>
                        <td class="center" style="background-color:#9fcfe2; font-weight:bold;">{{number_format($persen_penj_kepala,2,',', '.')}} %</td>
                        <td class="center" style="background-color:#9fcfe2; font-weight:bold;">{{number_format($persen_penj_kaki,2,',', '.')}} %</td>
                        <td class="center" style="background-color:#9fcfe2; font-weight:bold;">{{number_format($persen_penj_usus,2,',', '.')}} %</td>
                        <td colspan="7"></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="4" class="text-center" style="background-color:#e9b381; font-weight:bold;">{{number_format($persen_prod_atiampela+$persen_prod_kepala+$persen_prod_kaki+$persen_prod_usus,2,',', '.')}}%</td>
                        <td colspan="15"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(".downloadLaporanPerminggu").on('click', () => {
    var tanggalMulai      = $("#tanggalMulai").val();
    var tanggalSelesai    = $("#tanggalSelesai").val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('evis.laporan', ['key' => 'laporanPerminggu']) }}&subkey=download" + "&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai,
        method: "GET",
        beforeSend: function() {
            $('.downloadLaporanPerminggu').attr('disabled');
            $(".spinerloading").show(); 
            $("#text").text('Downloading...');
        },
        success: function(data) {
            window.location = "{{ route('evis.laporan', ['key' => 'laporanPerminggu']) }}&subkey=download" + "&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai
            $("#text").text('Download');
            $(".spinerloading").hide();
        }
    });
})
</script>
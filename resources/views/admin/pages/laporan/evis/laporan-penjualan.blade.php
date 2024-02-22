@if ($download == true)
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=Evis-Perbandingan-Hasil-Produksi-X-Penjualan.xls');
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
            <button type="submit" class="btn btn-blue float-right mb-2 downloadlaporanPenjualan"><i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Download</span></button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-default">
                <thead>
                    <tr class="center">
                        <th rowspan="2" style="background-color:#dbe3fa; text-align:center; ">Tanggal</th>
                        <th rowspan="2" style="background-color:#dbe3fa; text-align:center; ">Jumlah Mobil</th>
                        <th rowspan="2" style="background-color:#dbe3fa;">Jumlah Pemotongan LPAH</th>
                        <th colspan="4" style="background-color:#dbe3fa; text-align:center;">Hasil Produksi</th>
                        <th rowspan="2" style="background-color:#dbe3fa;">Ati Ampela Bersih</th>
                        <th colspan="4" style="background-color:#dbe3fa; text-align:center">Penjualan</th>
                        <th colspan="3" style="background-color:#dbe3fa; text-align:center">Sisa/Stok</th>
                    </tr>
                    <tr class="center">
                        <th style="text-align:center">Ati/Ampela</th>
                        <th style="text-align:center">Kepala</th>
                        <th style="text-align:center">Kaki</th>
                        <th style="text-align:center">Usus</th>
                        <th style=" text-align:center">Ati/Ampela</th>
                        <th style=" text-align:center">Kepala</th>
                        <th style=" text-align:center">Kaki</th>
                        <th style=" text-align:center">Usus</th>
                        <th style=" text-align:center">Ati/Ampela</th>
                        <th style=" text-align:center">Kepala</th>
                        <th style=" text-align:center">Kaki</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        //mobil
                        $val_mobil0 = 0;
                        $val_mobil1 = 0;
                        $hasil_index_jml_Mobil =0;
                        $hasil_persen_jml_mobil=0;
                        //potong lpah
                        $val_potong_lpah0 = 0;
                        $val_potong_lpah1 = 0;
                        $hasil_index_jml_potong =0;
                        $hasil_persen_jml_potong=0;
                        //hasilproduksi ati ampela
                        $val_ati_ampela0 = 0;
                        $val_ati_ampela1 = 0;
                        $hasil_index_ati_ampela =0;
                        $hasil_persen_ati_ampela=0;
                        //hasilproduksi kepala
                        $val_kepala0 = 0;
                        $val_kepala1 = 0;
                        $hasil_index_kepala =0;
                        $hasil_persen_kepala=0;
                        //hasilproduksi kaki
                        $val_kaki0 = 0;
                        $val_kaki1 = 0;
                        $hasil_index_kaki =0;
                        $hasil_persen_kaki =0;
                        //hasilproduksi usus
                        $val_usus0 = 0;
                        $val_usus1 = 0;
                        $hasil_index_usus =0;
                        $hasil_persen_usus=0;
                        //hati bersih
                        $val_hati_bersih0 = 0;
                        $val_hati_bersih1 = 0;
                        $hasil_index_hati_bersih =0;
                        $hasil_persen_hati_bersih=0;
                        //penjualan ati ampela
                        $val_penj_atiampela0 = 0;
                        $val_penj_atiampela1 = 0;
                        $hasil_index_penj_atiampela =0;
                        $hasil_persen_penj_atiampela=0;
                        //penjualan kepala
                        $val_penj_kepala0 = 0;
                        $val_penj_kepala1 = 0;
                        $hasil_index_penj_kepala =0;
                        $hasil_persen_penj_kepala=0;
                        //penjualan kaki
                        $val_penj_kaki0 = 0;
                        $val_penj_kaki1 = 0;
                        $hasil_index_penj_kaki =0;
                        $hasil_persen_penj_kaki=0;
                        //penjualan usus
                        $val_penj_usus0 = 0;
                        $val_penj_usus1 = 0;
                        $hasil_index_penj_usus =0;
                        $hasil_persen_penj_usus=0;
                    @endphp
                    @foreach ($date_range as $i => $item)
                    @php
                        //get value index array
                        if ($i == 0) {
                            $val_mobil0         = $item['jml_mobil'];
                            $val_potong_lpah0   = $item['jml_potong'];
                            $val_ati_ampela0    = $item['hp_ati_ampela'];
                            $val_kepala0        = $item['hp_kepala'];
                            $val_kaki0          = $item['hp_kaki'];
                            $val_usus0          = $item['hp_usus'];
                            $val_hati_bersih0   = $item['hp_hati_berish'];
                            $val_penj_atiampela0 = $item['penj_ati_ampela'];
                            $val_penj_kepala0   = $item['penj_kepala'];
                            $val_penj_kaki0     = $item['penj_kaki'];
                            $val_penj_usus0     = $item['penj_usus'];
                        }
                        if ($i == 1) {
                            $val_mobil1         = $item['jml_mobil'];
                            $val_potong_lpah1   = $item['jml_potong'];
                            $val_ati_ampela1    = $item['hp_ati_ampela'];
                            $val_kepala1        = $item['hp_kepala'];
                            $val_kaki1          = $item['hp_kaki'];
                            $val_usus1          = $item['hp_usus'];
                            $val_hati_bersih1   = $item['hp_hati_berish'];
                            $val_penj_atiampela1 = $item['penj_ati_ampela'];
                            $val_penj_kepala1   = $item['penj_kepala'];
                            $val_penj_kaki1     = $item['penj_kaki'];
                            $val_penj_usus1     = $item['penj_usus'];
                        }

                        //perhitungan jumlah index value
                        $hasil_index_jml_Mobil  = $val_mobil0 - $val_mobil1;
                        $hasil_index_jml_potong = $val_potong_lpah0 - $val_potong_lpah1;
                        $hasil_index_ati_ampela = $val_ati_ampela0 - $val_ati_ampela1;
                        $hasil_index_kepala     = $val_kepala0 - $val_kepala1;
                        $hasil_index_kaki       = $val_kaki0 - $val_kaki1;
                        $hasil_index_usus       = $val_usus0 - $val_usus1;
                        $hasil_index_hati_bersih = $val_hati_bersih0 - $val_hati_bersih1;
                        $hasil_index_penj_atiampela = $val_penj_atiampela0 - $val_penj_atiampela1;
                        $hasil_index_penj_kepala = $val_penj_kepala0 - $val_penj_kepala1;
                        $hasil_index_penj_kaki   = $val_penj_kaki0 - $val_penj_kaki1;
                        $hasil_index_penj_usus   = $val_penj_usus0 - $val_penj_usus1;

                        
                        //perhitungan jumlah persen
                        if ($val_mobil1 != 0 || $val_potong_lpah1 != 0 || $val_ati_ampela1 != 0 || $val_usus1 !=0 || $val_penj_atiampela1 != 0) {
                            $hasil_persen_jml_mobil  = $hasil_index_jml_Mobil/$val_mobil1 * 100;
                            $hasil_persen_jml_potong = $hasil_index_jml_potong/$val_potong_lpah1 * 100;
                            $hasil_persen_ati_ampela = $hasil_index_ati_ampela/$val_ati_ampela1 *100;
                            $hasil_persen_kepala     = $hasil_index_kepala/$val_kepala1 * 100;
                            $hasil_persen_kaki       = $hasil_index_kaki/$val_kaki1 * 100;
                            $hasil_persen_usus       = $hasil_index_usus/$val_usus1 * 100;
                            $hasil_persen_penj_atiampela = $hasil_index_penj_atiampela/$val_penj_atiampela1*100;
                            $hasil_persen_penj_kepala = $hasil_index_penj_kepala/$val_penj_kepala1*100;
                            $hasil_persen_penj_kaki   = $hasil_index_penj_kaki/$val_penj_kaki1*100;
                            $hasil_persen_penj_usus   = $hasil_index_penj_usus/$val_penj_usus1*100;
                        } else {
                            $hasil_persen_jml_mobil  = 0;
                            $hasil_persen_jml_potong = 0;
                            $hasil_persen_ati_ampela = 0;
                            $hasil_persen_kepala     = 0;
                            $hasil_persen_kaki       = 0;
                            $hasil_persen_usus       = 0;
                            $hasil_persen_penj_atiampela = 0;
                            $hasil_persen_penj_kepala =0; 
                            $hasil_persen_penj_kaki   = 0;
                            $hasil_persen_penj_usus   = 0;
                        }

                        if ($val_hati_bersih1 !=0) {
                            $hasil_persen_hati_bersih = $hasil_index_hati_bersih/$val_hati_bersih1 * 100;
                        } else {
                            $hasil_persen_hati_bersih = 0;
                        }
                        

                        
                    @endphp
                        <tr>
                            <td>{{$item['tanggal']}}</td>
                            <td>{{$item['jml_mobil']}}</td>
                            <td>{{$item['jml_potong']}}</td>
                            <td>{{$item['hp_ati_ampela']}}</td>
                            <td>{{$item['hp_kepala']}}</td>
                            <td>{{$item['hp_kaki']}}</td>
                            <td>{{$item['hp_usus']}}</td>
                            <td>{{$item['hp_hati_berish']}}</td>
                            <td>{{$item['penj_ati_ampela']}}</td>
                            <td>{{$item['penj_kepala']}}</td>
                            <td>{{$item['penj_kaki']}}</td>
                            <td>{{$item['penj_usus']}}</td>
                            <td>{{$item['sisa_atiampela']}}</td>
                            <td>{{$item['sisa_kepala']}}</td>
                            <td>{{$item['sisa_kaki']}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td rowspan="3"></td>
                        <td style="color: red">{{number_format($hasil_index_jml_Mobil,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_jml_potong,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_ati_ampela,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_kepala,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_kaki,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_usus,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_hati_bersih,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_penj_atiampela,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_penj_kepala,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_penj_kaki,1)}}</td>
                        <td style="color: red">{{number_format($hasil_index_penj_usus,1)}}</td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td>
                            @if ($hasil_index_jml_Mobil > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_jml_potong > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_ati_ampela > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_kepala > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_kaki > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_usus > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_hati_bersih > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_penj_atiampela > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_penj_kepala > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_penj_kaki > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td>
                            @if ($hasil_index_penj_usus > 0)
                                NAIK
                            @else
                                TURUN
                            @endif
                        </td>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td style="color: red">{{ number_format($hasil_persen_jml_mobil, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_jml_potong, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_ati_ampela, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_kepala, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_kaki, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_usus, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_hati_bersih, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_penj_atiampela, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_penj_kepala, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_penj_kaki, 2)}}%</td>
                        <td style="color: red">{{ number_format($hasil_persen_penj_usus, 2)}}%</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br>
        {{-- BENCHMARK --}}
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align: center">Periode</th>
                        <th rowspan="2" style="text-align: center">LPAH</th>
                        <th colspan="4" style="text-align: center">Hasil Produksi</th>
                    </tr>
                    <tr>
                        <th>Ati/Ampela</th>
                        <th>Kepala</th>
                        <th>Kaki</th>
                        <th>Usus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data_benchmark as $item)
                        <tr>
                            <td rowspan="2">{{$item['tgl_awal']}} - {{$item['tgl_akhir']}}</td>
                            <td rowspan="2">{{$item['total_potong_lpah']}}</td>
                            <td>{{$item['hp_total_atiampela']}}</td>
                            <td>{{$item['hp_total_kepala']}}</td>
                            <td>{{$item['hp_total_kaki']}}</td>
                            <td>{{$item['hp_total_usus']}}</td>
                        </tr>
                        <tr>
                            <td>{{number_format($item['persen_atiampela'],2)}} % </td>
                            <td>{{number_format($item['persen_kepala'],2)}} % </td>
                            <td>{{number_format($item['persen_kaki'],2)}} % </td>
                            <td>{{number_format($item['persen_usus'],2)}} % </td>
                        </tr>
                        <tr style="background-color: rgb(243, 220, 72)">
                            <td colspan="2">BENCHMARK</td>
                            <td>{{number_format($item['bm_bawah_atiampela'],2 )}}%</td>
                            <td>{{number_format($item['bm_bawah_kepala'],2)}}%</td>
                            <td>{{number_format($item['bm_bawah_kaki'],2)}}%</td>
                            <td>{{number_format($item['bm_bawah_usus'],2)}}%</td>
                        </tr>
                        <tr style="background-color: bisque">  
                            <td colspan="2"></td>
                            <td>{{number_format($item['persenan_bawah_ati'],2)}}%</td>
                            <td>{{number_format($item['persenan_bawah_kepala'],2)}}%</td>
                            <td>{{number_format($item['persenan_bawah_kaki'],2)}}%</td>
                            <td>{{number_format($item['persenan_bawah_usus'],2)}}%</td>
                        </tr>
                        <tr style="background-color: white">
                            <td colspan="2"></td>
                            <td>
                                @if ($item['persenan_bawah_ati'] > 0)
                                    NAIK
                                @else
                                    TURUN
                                @endif
                            </td>
                            <td>
                                @if ($item['persenan_bawah_kepala'] > 0)
                                    NAIK
                                @else
                                    TURUN
                                @endif
                            </td>
                            <td>
                                @if ($item['persenan_bawah_kaki'] > 0)
                                    NAIK
                                @else
                                    TURUN
                                @endif
                            </td>
                            <td>
                                @if ($item['persenan_bawah_usus'] > 0)
                                    NAIK
                                @else
                                    TURUN
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{-- END BENCHMARK --}}
    </div>
</section>
<script type="text/javascript">
    $(".downloadlaporanPenjualan").on('click', () => {
    var tanggalMulai      = $("#tanggalMulai").val();
    var tanggalSelesai    = $("#tanggalSelesai").val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('evis.laporan', ['key' => 'laporanPenjualan']) }}&subkey=download" + "&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai,
        method: "GET",
        beforeSend: function() {
            $('.downloadlaporanPenjualan').attr('disabled');
            $(".spinerloading").show(); 
            $("#text").text('Downloading...');
        },
        success: function(data) {
            window.location = "{{ route('evis.laporan', ['key' => 'laporanPenjualan']) }}&subkey=download" + "&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai
            $("#text").text('Download');
            $(".spinerloading").hide();
        }
    });
})
</script>
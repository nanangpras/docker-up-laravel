@if ($download == true)
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=Laporan Evis Perbandingan.xls');
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
            <button type="submit" class="btn btn-blue float-right mb-2 downloadLaporanPerbandingan"><i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Download</span></button>
            @endif
        </div>
    </div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th class="text-center text" rowspan="2">Tanggal</th>
                        <th class="text-center text" style="background-color:rgb(140,170,222)" colspan="4">ECERAN</th>
                        <th class="text-center text" style="background-color:rgb(244,176,129)" colspan="4">PAKET</th>
                        <th class="text-center text" style="background-color:rgb(190,192,191)" colspan="3">KIRIMAN</th>
                        <th class="text-center text" style="background-color:rgb(250,218,97)" colspan="1">KUPAS</th>
                        <th class="text-center text" style="background-color:rgb(191,191,191)" colspan="3">STOK FROZEN</th>
                    </tr>
                    <tr>
                        <th class="text-center text" style="background-color:rgb(175,199,235)">Ati/Ampela</th>
                        <th class="text-center text" style="background-color:rgb(200,223,179)">Kepala</th>
                        <th class="text-center text" style="background-color:rgb(219,219,217)">Kaki</th>
                        <th class="text-center text" style="background-color:rgb(148,206,85)">Usus</th>
                        <th class="text-center text" style="background-color:rgb(175,199,235)">Ati/Ampela</th>
                        <th class="text-center text" style="background-color:rgb(200,223,179)">Kepala</th>
                        <th class="text-center text" style="background-color:rgb(219,219,217)">Kaki</th>
                        <th class="text-center text" style="background-color:rgb(148,206,85)">Usus</th>
                        <th class="text-center text" style="background-color:rgb(175,199,235)">Ati/Ampela</th>
                        <th class="text-center text" style="background-color:rgb(200,223,179)">Kepala</th>
                        <th class="text-center text" style="background-color:rgb(219,219,217)">Kaki</th>
                        <th class="text-center text" style="background-color:rgb(175,199,235)">Ampela</th>
                        <th class="text-center text" style="background-color:rgb(175,199,235)">Ati/Ampela</th>
                        <th class="text-center text" style="background-color:rgb(200,223,179)">Kepala</th>
                        <th class="text-center text" style="background-color:rgb(219,219,217)">Kaki</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                            $totalHatiAmpelaEceran  = 0;
                            $totalKepalaEceran      = 0;
                            $totalKakiEceran        = 0;
                            $totalUsusEceran        = 0;
                            // -----------------------
                            $totalHatiAmpelaPaket   = 0;
                            $totalKepalaPaket       = 0;
                            $totalKakiPaket         = 0;
                            $totalUsusPaket         = 0;
                            // -----------------------
                            $totalHatiAmpelaKiriman = 0;
                            $totalKepalaKiriman     = 0;
                            $totalKakiKiriman       = 0;
                            // -----------------------
                            $totalKupas             = 0;
                            // -----------------------
                            $totalHatiAmpelaFrozen  = 0;
                            $totalKepalaFrozen      = 0;
                            $totalKakiFrozen        = 0;
                            // -----------------------
                        @endphp
                    @foreach ($dataEvis as $no => $data)
                    <tr class="text-right">
                        <td>{{ $no }}</td>
                        @foreach ($data as $key => $value)
                        @php
                            $totalHatiAmpelaEceran  += $value->HATIAMPELAECERAN;
                            $totalKepalaEceran      += $value->KEPALAECERAN;
                            $totalKakiEceran        += $value->KAKIECERAN;
                            $totalUsusEceran        += $value->USUSECERAN;
                            // -----------------------
                            $totalHatiAmpelaPaket   += $value->HATIAMPELAPAKET;
                            $totalKepalaPaket       += $value->KEPALAPAKET;
                            $totalKakiPaket         += $value->KAKIPAKET;
                            $totalUsusPaket         += $value->USUSPAKET;
                            // -----------------------
                            $totalHatiAmpelaKiriman += $value->HATIAMPELAKIRIMAN;
                            $totalKepalaKiriman     += $value->KEPALAKIRIMAN;
                            $totalKakiKiriman       += $value->KAKIKIRIMAN;
                            // -----------------------
                            $totalKupas             += $value->KUPAS;
                            // -----------------------
                            $totalHatiAmpelaFrozen  += $value->HATIAMPELAFROZEN;
                            $totalKepalaFrozen      += $value->KEPALAFROZEN;
                            $totalKakiFrozen        += $value->KAKIFROZEN;
                            // -----------------------
                        @endphp
                            <td @if ($value->HATIAMPELAECERAN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->HATIAMPELAECERAN ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->KEPALAECERAN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->KEPALAECERAN ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->KAKIECERAN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->KAKIECERAN ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->USUSECERAN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->USUSECERAN ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->HATIAMPELAPAKET == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->HATIAMPELAPAKET ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->KEPALAPAKET == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->KEPALAPAKET ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->KAKIPAKET == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->KAKIPAKET ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->USUSPAKET == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->USUSPAKET ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->HATIAMPELAKIRIMAN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->HATIAMPELAKIRIMAN ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->KEPALAKIRIMAN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->KEPALAKIRIMAN ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->KAKIKIRIMAN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->KAKIKIRIMAN ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->KUPAS == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->KUPAS ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->HATIAMPELAFROZEN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->HATIAMPELAFROZEN ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->KEPALAFROZEN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->KEPALAFROZEN ?? '0', 2,',', '.') }} </td>
                            <td @if ($value->KAKIFROZEN == 0) style="background-color:rgb(253,1,0)" @endif> {{ number_format($value->KAKIFROZEN ?? '0', 2,',', '.') }} </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="text-right">
                    <tr>
                        <td>Total</td>
                        <td class="text-danger">{{ number_format($totalHatiAmpelaEceran, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalKepalaEceran, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalKakiEceran, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalUsusEceran, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalHatiAmpelaPaket, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalKepalaPaket, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalKakiPaket, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalUsusPaket, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalHatiAmpelaKiriman, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalKepalaKiriman, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalKakiKiriman, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalKupas, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalHatiAmpelaFrozen, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalKepalaFrozen, 2,',', '.') }}</td>
                        <td class="text-danger">{{ number_format($totalKakiFrozen, 2,',', '.') }}</td>
                    </tr>
                    <tr>
                        {{-- YANG BAWAH GAJADI KEPAKE KARENA SALAH RUMUSAN DARI EXCEL PAK HERI KOAWKOAWKOWAKOWA --}}

                        @php
                            $totalPersentaseHatiAmpelaEceran  = 0;
                            $totalPersentaseKepalaEceran      = 0;
                            $totalPersentaseKakiEceran        = 0;
                            $totalPersentaseUsusEceran        = 0;
                            // -----------------------
                            $totalPersentaseHatiAmpelaEceran  =  $totalHatiAmpelaEceran / ($totalHatiAmpelaEceran + $totalHatiAmpelaPaket + $totalHatiAmpelaKiriman + $totalKupas + $totalHatiAmpelaFrozen) * 100;
                            $totalPersentaseKepalaEceran      =  $totalKepalaEceran / ($totalKepalaEceran + $totalKepalaPaket + $totalKepalaKiriman + $totalKepalaFrozen) * 100;
                            $totalPersentaseKakiEceran        =  $totalKakiEceran / ($totalKakiEceran + $totalKakiPaket + $totalKakiKiriman + $totalKakiFrozen) * 100;
                            $totalPersentaseUsusEceran        =   $totalUsusEceran / ($totalUsusEceran + $totalUsusPaket)* 100;
                            // -----------------------
                            


                            // -----------------------
                            $totalPersentaseHatiAmpelaPaket   = 0;
                            $totalPersentaseKepalaPaket       = 0;
                            $totalPersentaseKakiPaket         = 0;
                            $totalPersentaseUsusPaket         = 0;
                            // -----------------------
                            $totalPersentaseHatiAmpelaPaket  =  $totalHatiAmpelaPaket / ($totalHatiAmpelaEceran + $totalHatiAmpelaPaket + $totalHatiAmpelaKiriman + $totalKupas + $totalHatiAmpelaFrozen) * 100;
                            $totalPersentaseKepalaPaket      =  $totalKepalaPaket / ($totalKepalaEceran + $totalKepalaPaket + $totalKepalaKiriman + $totalKepalaFrozen) * 100;
                            $totalPersentaseKakiPaket        =  $totalKakiPaket / ($totalKakiEceran + $totalKakiPaket + $totalKakiKiriman + $totalKakiFrozen) * 100;
                            $totalPersentaseUsusPaket        =  $totalUsusPaket / ($totalUsusEceran + $totalUsusPaket) * 100;
                            // -----------------------



                            // -----------------------
                            $totalPersentaseHatiAmpelaKiriman = 0;
                            $totalPersentaseKepalaKiriman     = 0;
                            $totalPersentaseKakiKiriman       = 0;
                            // -----------------------
                            $totalPersentaseHatiAmpelaKiriman  =  $totalHatiAmpelaKiriman / ($totalHatiAmpelaEceran + $totalHatiAmpelaPaket + $totalHatiAmpelaKiriman + $totalKupas + $totalHatiAmpelaFrozen) * 100;
                            $totalPersentaseKepalaKiriman      =  $totalKepalaKiriman / ($totalKepalaEceran + $totalKepalaPaket + $totalKepalaKiriman + $totalKepalaFrozen) * 100;
                            $totalPersentaseKakiKiriman        =  $totalKakiKiriman / ($totalKakiEceran + $totalKakiPaket + $totalKakiKiriman + $totalKakiFrozen) * 100;
                            // -----------------------



                            // -----------------------
                            $totalPersentaseKupas             = 0;
                            // -----------------------
                            $totalPersentaseKupas             =  $totalKupas / ($totalHatiAmpelaEceran + $totalHatiAmpelaPaket + $totalHatiAmpelaKiriman + $totalKupas + $totalHatiAmpelaFrozen) * 100;


                            
                            // -----------------------
                            $totalPersentaseHatiAmpelaFrozen  = 0;
                            $totalPersentaseKepalaFrozen      = 0;
                            $totalPersentaseKakiFrozen        = 0;
                            // -----------------------
                            $totalPersentaseHatiAmpelaFrozen =   $totalHatiAmpelaFrozen / ($totalHatiAmpelaEceran + $totalHatiAmpelaPaket + $totalHatiAmpelaKiriman + $totalKupas + $totalHatiAmpelaFrozen)* 100;
                            $totalPersentaseKepalaFrozen      =  $totalKepalaFrozen / ($totalKepalaEceran + $totalKepalaPaket + $totalKepalaKiriman + $totalKepalaFrozen) * 100;
                            $totalPersentaseKakiFrozen        =  $totalKakiFrozen / ($totalKakiEceran + $totalKakiPaket + $totalKakiKiriman + $totalKakiFrozen) * 100;
                            // -----------------------


                        @endphp

                        {{-- <td></td>
                        <td style="background-color:rgb(140,170,222)">{{ number_format(round($totalHatiAmpelaEceran / $ati_ampela * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(140,170,222)">{{ number_format(round($totalKepalaEceran / $kepala_baru_mgg * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(140,170,222)">{{ number_format(round($totalKakiEceran / $kaki_baru_mgg * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(140,170,222)">{{ number_format(round($totalUsusEceran / $usus_baru_mgg * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(244,176,129)">{{ number_format(round($totalHatiAmpelaPaket / $ati_ampela * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(244,176,129)">{{ number_format(round($totalKepalaPaket / $kepala_baru_mgg * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(244,176,129)">{{ number_format(round($totalKakiPaket / $kaki_baru_mgg * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(244,176,129)">{{ number_format(round($totalUsusPaket / $usus_baru_mgg * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(190,192,191)">{{ number_format(round($totalHatiAmpelaKiriman / $ati_ampela * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(190,192,191)">{{ number_format(round($totalKepalaKiriman / $kepala_baru_mgg * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(190,192,191)">{{ number_format(round($totalKakiKiriman / $kaki_baru_mgg * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(250,218,97)">{{ number_format(round($totalKupas / $ati_ampela * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(191,191,191)">{{ number_format(round($totalHatiAmpelaFrozen / $ati_ampela * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(191,191,191)">{{ number_format(round($totalKepalaFrozen / $kepala_baru_mgg * 100, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(191,191,191)">{{ number_format(round($totalKakiFrozen / $kaki_baru_mgg * 100, 2), 2,',', '.') }}%</td> --}}


                        {{-- YANG BAWAH GAJADI KEPAKE KARENA SALAH RUMUSAN DARI EXCEL PAK HERI KOAWKOAWKOWAKOWA --}}

                        <td></td>
                        <td style="background-color:rgb(140,170,222)">{{ number_format(round($totalPersentaseHatiAmpelaEceran, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(140,170,222)">{{ number_format(round($totalPersentaseKepalaEceran, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(140,170,222)">{{ number_format(round($totalPersentaseKakiEceran, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(140,170,222)">{{ number_format(round($totalPersentaseUsusEceran, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(244,176,129)">{{ number_format(round($totalPersentaseHatiAmpelaPaket, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(244,176,129)">{{ number_format(round($totalPersentaseKepalaPaket, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(244,176,129)">{{ number_format(round($totalPersentaseKakiPaket, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(244,176,129)">{{ number_format(round($totalPersentaseUsusPaket, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(190,192,191)">{{ number_format(round($totalPersentaseHatiAmpelaKiriman, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(190,192,191)">{{ number_format(round($totalPersentaseKepalaKiriman, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(190,192,191)">{{ number_format(round($totalPersentaseKakiKiriman, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(250,218,97)">{{ number_format(round($totalPersentaseKupas, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(191,191,191)">{{ number_format(round($totalPersentaseHatiAmpelaFrozen, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(191,191,191)">{{ number_format(round($totalPersentaseKepalaFrozen, 2), 2,',', '.') }}%</td>
                        <td style="background-color:rgb(191,191,191)">{{ number_format(round($totalPersentaseKakiFrozen, 2), 2,',', '.') }}%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>

<script>
        $(".downloadLaporanPerbandingan").on('click', () => {
        var tanggalMulai      = $("#tanggalMulai").val();
        var tanggalSelesai    = $("#tanggalSelesai").val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('evis.laporan', ['key' => 'laporanPerbandingan']) }}&subkey=download" + "&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai,
            method: "GET",
            beforeSend: function() {
                $('.downloadLaporanPerbandingan').attr('disabled');
                $(".spinerloading").show(); 
                $("#text").text('Downloading...');
            },
            success: function(data) {
                window.location = "{{ route('evis.laporan', ['key' => 'laporanPerbandingan']) }}&subkey=download" + "&tanggalMulai=" + tanggalMulai + "&tanggalSelesai=" + tanggalSelesai
                $("#text").text('Download');
                $(".spinerloading").hide();
            }
        });
    })
</script>
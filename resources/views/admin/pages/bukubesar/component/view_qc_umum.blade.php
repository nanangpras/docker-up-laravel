<p class="text-center mt-4"><strong>Laporan Umum</strong></p>
<section class="panel mt-4">
    <div class="table-responsive" id="export-qc">
        <style>
            .text {
                mso-number-format:"\@";
                border:thin solid black;
            }
            .default-table td {
                min-width: 100px;
            }
        </style>

        <table class="table table-sm default-table">
            <thead>
                <tr class="text-center">
                    <th class="text" rowspan="3">No</th>
                    <th class="text" rowspan="3">Supplier</th>
                    <th class="text" rowspan="3">Tanggal Pemotongan</th>
                    <th class="text" rowspan="3">No Urut Potong</th>
                    <th class="text" rowspan="3">Jam Kedatangan</th>
                    <th class="text" rowspan="3">Jam Bongkar</th>
                    <th class="text" rowspan="3">Ekor DO</th>
                    <th class="text" rowspan="3">Ukuran Ayam</th>
                    {{-- <th class="text" rowspan="3">Susut</th> --}}
                    <th class="text" rowspan="3">Sopir</th>
                    <th class="text" rowspan="3">Jumlah Ayam Merah</th>
                    <th class="text" rowspan="3">Berat Ayam Merah</th>
                    <th class="text" rowspan="3">Basah Bulu</th>
                    {{-- <th class="text" rowspan="3">Kisaran DO</th>
                    <th class="text" rowspan="3">Sampling Uniformity</th> --}}
                    <th class="text" rowspan="3">Ayam Mati</th>
                    {{-- <th class="text" rowspan="3">Kondisi Ayam</th>
                    <th class="text" rowspan="3">Diagnosis</th> --}}
                    <th class="text" rowspan="3">Diagnosa</th>
                    <th class="text" rowspan="3">Nama Penyakit</th>
                    <th class="text" colspan="20">Hasil Sampling QC</th>
                </tr>
                <tr class="text-center">
                    <th class="text" colspan="3">Memar</th>
                    <th class="text" colspan="2">Patah</th>
                    <th class="text" colspan="5">Keropeng</th>
                    <th class="text" rowspan="2">Dengkul Hijau</th>
                    <th class="text" colspan="2">Tembolok</th>
                    <th class="text" rowspan="2">Hati</th>
                    <th class="text" rowspan="2">Jantung</th>
                    <th class="text" rowspan="2">Usus</th>
                    <th class="text" colspan="3">Uniformity</th>
                </tr>
                <tr>
                    <th>Dada</th>
                    <th>Paha</th>
                    <th>Sayap</th>
                    <th>Sayap</th>
                    <th>Kaki</th>
                    <th>Kaki</th>
                    <th>Dada</th>
                    <th>Sayap</th>
                    <th>Punggung</th>
                    <th>Dengkul</th>
                    <th>Prosentase</th>
                    <th>Berat</th>
                    <th>Under</th>
                    <th>Uniform</th>
                    <th>Over</th>
                </tr>
            </thead>
            <tbody>
            @if(count($produksi) > 0)
                @foreach ($produksi as $i => $val)
                @php 
                    $JeroanHati             = $val->post->jeroan_hati ?? '0';
                    if($JeroanHati != "0"){
                        if($JeroanHati != 'null'){
                            $decoded            = json_decode($JeroanHati);
                            $b                  = array();
                            foreach($decoded as $key => $a){
                                for($x=0;$x < count($decoded);$x++){
                                    $b[$key]      = $a;
                                }
                            }
                            $JeroanHati         = implode(",",$b);
                        }
                        else{
                            $JeroanHati         = "0"; 
                        }
                    }
                    else{
                        $JeroanHati         = "0";
                    }

                    $JeroanJantung             = $val->post->jeroan_jantung ?? '0';
                    if($JeroanJantung != "0"){
                        if($JeroanJantung != 'null'){
                            $decodedJantung     = json_decode($JeroanJantung);
                            $j                  = array();
                            foreach($decodedJantung as $key => $k){
                                for($x=0;$x < count($decodedJantung);$x++){
                                    $j[$key]      = $k;
                                }
                            }
                            $JeroanJantung         = implode(",",$j);
                        }
                        else{
                            $JeroanJantung         = "0";
                        }
                    }
                    else{
                        $JeroanJantung         = "0";
                    }

                    $JeroanUsus             = $val->post->jeroan_usus ?? '0';
                    if($JeroanUsus != "0"){
                        if($JeroanUsus != 'null'){
                            $decodedUsus     = json_decode($JeroanUsus);
                            $l                  = array();
                            foreach($decodedUsus as $key => $u){
                                for($x=0;$x < count($decodedUsus);$x++){
                                    $l[$key]      = $u;
                                }
                            }
                            $JeroanUsus         = implode(",",$l);
                        }
                        else{
                            $JeroanUsus         = "0";
                        }
                    }
                    else{
                        $JeroanUsus         = "0";
                    }
                @endphp
                    <tr>
                        <td class="text">{{ ++$i }}</td>
                        <td class="text">{{ ($val->prodpur->purcsupp->nama ?? 'VENDOR TIDAK DITEMUKAN') }}</td>
                        <td class="text">{{ date('d-m-Y', strtotime($val->prodpur->tanggal_potong)) }}</td>
                        <td class="text">{{ $val->no_urut }}</td>
                        <td class="text">{{ $val->sc_jam_masuk }}</td>
                        <td class="text">{{ $val->lpah_jam_bongkar }}</td>
                        <td class="text">{{ number_format($val->sc_ekor_do, 0) }}</td>
                        <td class="text">@if ($val->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $val->prodpur->ukuran_ayam }} @endif</td>
                        {{-- <td class="text"></td> --}}
                        <td class="text">{{ $val->sc_pengemudi }}</td>
                        <td class="text">{{ $val->post->ayam_merah ?? '0' }}</td>
                        <td class="text">{{ $val->qc_berat_ayam_merah ?? '0' }}</td>
                        <td class="text">{{ $val->antem->basah_bulu ?? '0' }}</td>
                        {{-- <td class="text"></td>
                        <td class="text"></td> --}}
                        <td class="text">{{ $val->antem->ayam_mati ?? '0' }}</td>
                        <td class="text">{{ $val->nekrop->diagnosa ?? '-' }}</td>
                        <td class="text">{{ $val->antem->ayam_sakit_nama ?? '-' }}</td>
                        {{-- <td class="text"></td>
                        <td class="text"></td> --}}
                        <td class="text">{{ $val->post->memar_dada ?? '0' }}</td>
                        <td class="text">{{ $val->post->memar_paha ?? '0' }}</td>
                        <td class="text">{{ $val->post->memar_sayap ?? '0' }}</td>
                        <td class="text">{{ $val->post->patah_sayap ?? '0' }}</td>
                        <td class="text">{{ $val->post->patah_kaki ?? '0' }}</td>
                        <td class="text">{{ $val->post->keropeng_kaki ?? '0' }}</td>
                        <td class="text">{{ $val->post->keropeng_sayap ?? '0' }}</td>
                        <td class="text">{{ $val->post->keropeng_dada ?? '0' }}</td>
                        <td class="text">{{ $val->post->keropeng_pg ?? '0' }}</td>
                        <td class="text">{{ $val->post->keropeng_dengkul ?? '0' }}</td>
                        <td class="text">{{ $val->post->kehijauan ?? '0' }}</td>
                        <td class="text">{{ $val->post->tembolok_kondisi ?? '0' }}</td>
                        <td class="text">{{ $val->qc_tembolok ?? '0' }}</td>
                        <td class="text">{{ $JeroanHati }}</td>
                        <td class="text">{{ $JeroanJantung }}</td>
                        <td class="text">{{ $JeroanUsus }}</td>
                        <td class="text">{{ $val->qc_under }}</td>
                        <td class="text">{{ $val->qc_uniform }}</td>
                        <td class="text">{{ $val->qc_over }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text text-center" colspan="35"> Tidak ada data </td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</section>

<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="export-qc-{{$tanggal}}_{{$tanggalend}}.xls">
    <textarea name="html" style="display: none" id="html-export-qc"></textarea>
    <button type="submit" class="btn btn-blue">Export</button>
</form>

<script>
    $(document).ready(function(){
        var html  = $('#export-qc').html();
        $('#html-export-qc').val(html);
    })
</script>
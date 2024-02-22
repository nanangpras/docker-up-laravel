<p class="text-center mt-4"><strong>Kematian Ayam {{ $tanggalawal }} - {{ $tanggalakhir }}</strong></p>
<section class="panel mt-4">
    <div class="table-responsive" id="export-qc-kematianayam">
        <style>
            .text {
                mso-number-format:"\@";
                border:thin solid black;
            }
            .table thead th{
                vertical-align: center !important;
            }
            .table {
                margin-bottom: 0 !important;
            }
        </style>
        <table class="table table-sm default-table">
            <thead>
                <tr class="text-center">
                    <th class="text" rowspan="2">No</th>
                    <th class="text" rowspan="2">Tanggal</th>
                    <th class="text" rowspan="2">Farm</th>
                    <th class="text" rowspan="2">No Urut Potong</th>
                    <th class="text" rowspan="2">Jam Kedatangan</th>
                    <th class="text" rowspan="2">Jam Bongkar</th>
                    <th class="text" rowspan="2">Jumlah (Ekor)</th>
                    <th class="text" rowspan="2">Ukuran (Kg)</th>
                    <th class="text" rowspan="2">Susut (%)</th>
                    <th class="text" colspan="2">Kematian Ayam</th>
                    <th class="text" rowspan="2">Sopir</th>
                </tr>
                <tr class="text-center">
                    <th class="text" >Jumlah(e)</th>
                    <th class="text" >Persentase</th>
                </tr>
            </thead>
            <tbody>
            @if(count($kematianayam) > 0)
                @foreach ($kematianayam as $i => $val)
                    <tr>
                        <td class="text">{{ ++$i }}</td>
                        <td class="text">{{ date('d-m-Y', strtotime($val->prodpur->tanggal_potong)) }}</td>
                        <td class="text">{{ ($val->prodpur->purcsupp->nama ?? 'VENDOR TIDAK DITEMUKAN') }}</td>
                        <td class="text">{{ $val->no_urut }}</td>
                        <td class="text">{{ $val->sc_jam_masuk }}</td>
                        <td class="text">{{ $val->lpah_jam_bongkar }}</td>
                        <td class="text">{{ number_format($val->sc_ekor_do, 0) }}</td>
                        <td class="text">@if ($val->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $val->prodpur->ukuran_ayam }} @endif</td>
                        <td class="text">{{ $val->lpah_persen_susut }} %</td>
                        <td class="text">{{ $val->qc_ekor_ayam_mati }}</td>
                        <td class="text">{{ $val->qc_persen_ayam_mati }} %</td>
                        <td class="text">{{ $val->sc_pengemudi }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text text-center" colspan="12"> Tidak ada data kematian diatas 1%</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</section>

<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="export-qc-ayam-mati-{{$tanggalawal}}_{{$tanggalakhir}}.xls">
    <textarea name="html" style="display: none" id="html-export-qckematianayam"></textarea>
    <button type="submit" class="btn btn-blue">Export</button>
</form>


<script>
    $(document).ready(function(){
        var html  = $('#export-qc-kematianayam').html();
        $('#html-export-qckematianayam').val(html);
    })
</script>
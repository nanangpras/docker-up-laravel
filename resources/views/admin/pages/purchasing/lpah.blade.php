<section class="panel">
    <div class="card-body">
        <div class="table-responsive" id="table-lpah">
            <table class="table table-sm default-table">
                <style>
                    .text {
                        mso-number-format:"\@";
                        border:thin solid black;
                    }
                </style>
                <thead>
                    <tr class="text-center">
                        <th class="text" rowspan="2">No</th>
                        <th class="text" rowspan="2">Tanggal Potong</th>
                        <th class="text" rowspan="2">FARM</th>
                        <th class="text" rowspan="2">WILAYAH</th>
                        <th class="text" rowspan="2">MOBIL</th>
                        <th class="text" rowspan="2">No. DO</th>
                        <th class="text" rowspan="2">Jam Datang</th>
                        <th class="text" rowspan="2">Jam Bongkar</th>
                        <th class="text" rowspan="2">DRIVER</th>
                        <th class="text" rowspan="2">No. MOBIL</th>
                        <th class="text" colspan="3">TIMBANG KANDANG</th>
                        <th class="text" colspan="3">KENYATAAN TERIMA</th>
                        <th class="text" colspan="2">SUSUT TIMBANG</th>
                        <th class="text" colspan="2">MATI</th>
                        <th class="text" colspan="2">PROSENTASE MATI (%)</th>
                    </tr>
                    <tr class="text-center">
                        <th class="text">Ekor</th>
                        <th class="text">Kg</th>
                        <th class="text">Rata2 Kg</th>
                        <th class="text">Ekor</th>
                        <th class="text">Kg</th>
                        <th class="text">Rata2 Kg</th>
                        <th class="text">Kg</th>
                        <th class="text">%</th>
                        <th class="text">Ekor</th>
                        <th class="text">Kg</th>
                        <th class="text">Ekor</th>
                        <th class="text">Kg</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produksi as $i => $val)
                        <tr>
                            <td class="text">{{ ++$i }}</td>
                            <td class="text">{{ $val->prod_tanggal_potong }}</td>
                            <td class="text">{{ $val->sc_nama_kandang }}</td>
                            <td class="text">{{ $val->sc_wilayah }}</td>
                            <td class="text">{{ $val->no_urut }}</td>
                            <td class="text">{{ $val->no_do }}</td>
                            <td class="text">{{ $val->sc_jam_masuk }}</td>
                            <td class="text">{{ $val->lpah_jam_bongkar }}</td>
                            <td class="text">{{ $val->sc_pengemudi }}</td>
                            <td class="text">{{ $val->sc_no_polisi }}</td>
                            <td class="text">{{ number_format($val->sc_ekor_do, 0) }}</td>
                            <td class="text">{{ number_format($val->sc_berat_do, 2) }}</td>
                            <td class="text">{{ $val->sc_rerata_do }}</td>
                            <td class="text">{{ number_format($val->ekoran_seckle, 0) }}</td>
                            <td class="text">{{ number_format($val->lpah_berat_terima, 2) }}</td>
                            <td class="text">{{ $val->lpah_rerata_terima }}</td>
                            <td class="text">{{ number_format($val->lpah_berat_susut, 2) }}</td>
                            <td class="text">{{ $val->lpah_persen_susut }}</td>
                            <td class="text">{{ number_format($val->qc_ekor_ayam_mati, 0) }}</td>
                            <td class="text">{{ $val->qc_berat_ayam_mati }}</td>
                            <td class="text">{{ number_format($val->sc_ekor_do != 0 ? ($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 : 0, 2) }}</td>
                            <td class="text">{{ number_format($val->sc_berat_do != 0 ? ($val->qc_berat_ayam_mati / $val->sc_berat_do) * 100 : 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <form method="post" action="{{route('weekly.export')}}">
                @csrf
                <input name="filename" type="hidden" value="export-purchasing-{{date('dmyHis')}}.xls">
                <textarea name="html" style="display: none" id="html-export"></textarea>
                <button type="submit" id="export-bb-fresh" class="btn btn-blue">Export</button>
            </form>
        </div>

        <script>
            $(document).ready(function(){
                var html  = $('#table-lpah').html();
                $('#html-export').val(html);
            })
        </script>
    </div>
</section>

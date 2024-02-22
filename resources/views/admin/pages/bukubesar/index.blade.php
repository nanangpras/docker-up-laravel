@extends('admin.layout.template')

@section('title', 'Buku Besar')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>RENDEMEN</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">

        <form action="{{ route('bukubesar.index') }}" method="get">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <label for="awal">Pencarian Tanggal Awal</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date" id="awal" name="tanggal" value="{{ $tanggal }}"
                        placeholder="Cari...">
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <label for="akhir">Pencarian Tanggal Akhir</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date-end" id="akhir" name="tanggalend"
                        value="{{ $tanggalend }}" placeholder="Cari...">
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <label for="jenis_report">Jenis Report</label>
                    <select name="report" id="jenis_report" class="form-control">
                        <option value="all" {{ $request->report == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="po_lb" {{ $request->report == 'po_lb' ? 'selected' : '' }}>PO LB</option>
                        <option value="non_lb" {{ $request->report == 'non_lb' ? 'selected' : '' }}>PO Non LB</option>
                    </select>
                </div>
            </div>
        </form>
        <br>
        <div id="loading"><img src="{{ asset('loading.gif') }}" style="width: 18px"> Loading ...</div>
        <br>
        {{-- <section class="panel"> --}}
            <div class="table-responsive" id="table-rendemen">
                <table class="table default-table" id="export-table">
                    <style>
                        .text {
                            mso-number-format: "\@";
                            border: thin solid black;
                        }
                    </style>
                    <thead>
                        <tr class="text-center">
                            <th class="text" rowspan="2">NO</th>
                            <th class="text" rowspan="2">TANGGAL POTONG</th>
                            <th class="text" rowspan="2">NO URUT</th>
                            <th class="text" rowspan="2">JENIS</th>
                            <th class="text" rowspan="2">NO PO</th>
                            <th class="text" rowspan="2">UKURAN</th>
                            <th class="text" rowspan="2">NAMA ITEM</th>
                            <th class="text" rowspan="2">SUPPLIER</th>
                            <th class="text" rowspan="2">FARM</th>
                            <th class="text" rowspan="2">WILAYAH</th>
                            <th class="text" rowspan="2">No. DO</th>
                            <th class="text" rowspan="2">DRIVER</th>
                            <th class="text" rowspan="2">No. MOBIL</th>
                            <th class="text" rowspan="2">OPERATOR</th>
                            <th class="text" colspan="3">DO</th>
                            <th class="text" colspan="4">TIMBANG LPAH</th>
                            <th class="text" rowspan="2">SELISIH EKOR DO</th>
                            <th class="text" colspan="2">SUSUT DO-TIMBANG</th>
                            <th class="text" colspan="3">MATI</th>
                            <th class="text" rowspan="2">PROSENTASE MATI (%)</th>
                            <th class="text" colspan="2">MERAH</th>
                            <th class="text" rowspan="2">KONDISI AYAM</th>
                            <th class="text" colspan="3">GRADING</th>
                            <th class="text" rowspan="2">YEILD PRODUKSI</th>
                            <th class="text" rowspan="2">RENDEMEN</th>
                            <th class="text" colspan="3">EVIS</th>
                            <th class="text" rowspan="2">YIELD EVIS</th>
                            <th class="text" rowspan="2">ALASAN BENCHMARK</th>
                            <th class="text" rowspan="2">YIELD BENCHMARK KARKAS</th>
                            <th class="text" rowspan="2">YIELD BENCHMARK EVIS</th>
                        </tr>
                        <tr class="text-center">

                            <th class="text">Ekor/Pcs/Pack</th>
                            <th class="text">Kg</th>
                            <th class="text">Rata2 Kg</th>
                            <th class="text">Ekor/Pcs/Pack</th>
                            <th class="text">Kg</th>
                            <th class="text">Rata2 Kg</th>
                            <th class="text">Tembolok</th>
                            <th class="text">Kg</th>
                            <th class="text">%</th>
                            <th class="text">Ekor/Pcs/Pack</th>
                            <th class="text">Kg</th>
                            <th class="text">Rata2 Kg</th>
                            <th class="text">Ekor/Pcs/Pack</th>
                            <th class="text">Kg</th>
                            <th class="text">Ekor/Pcs/Pack</th>
                            <th class="text">Kg</th>
                            <th class="text">Rata2</th>
                            <th class="text">Ekor/Pcs/Pack</th>
                            <th class="text">Kg</th>
                            <th class="text">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $total_DO = 0;
                        $total_BeratDO = 0;
                        $total_RerataDO = 0;
                        $total_EkorSeckle = 0;

                        $total_BeratLPAH = 0;
                        $total_RerataLPAH = 0;
                        $total_Tembolok = 0;

                        $total_SelisihDO = 0;

                        $total_SusutDO = 0;
                        $total_PersenDO = 0;

                        $total_EkorMati = 0;
                        $total_BeratMati = 0;
                        $total_RerataMati = 0;
                        $total_PersenMati = 0;

                        $total_EkorMerah = 0;
                        $total_BeratMerah = 0;

                        $total_EkorGrading = 0;
                        $total_BeratGrading = 0;
                        $total_RerataGrading = 0;
                        $total_PersenProduksi = 0;

                        $total_EkorEvis = 0;
                        $total_BeratEvis = 0;
                        $total_RerataEvis = 0;
                        $total_PersenEvis = 0;
                        $total_YieldEvis = 0;

                        $total_RendemenProduksi = 0;
                        @endphp
                        @foreach ($newData as $i => $val)
                        @php
                        $gradberat = 0;
                        $graditem = 0;
                        $evisberat = 0;
                        $evisekor = 0;
                        @endphp
                        @php
                        $summary = \App\Models\Grading::where('trans_id', $val->id)
                        ->where('keranjang', 0)
                        ->orderBy('id', 'DESC')
                        ->get();
                        $gradberat = 0;
                        $graditem = 0;
                        foreach ($summary as $row) {
                        $gradberat += $row->berat_item;
                        $graditem += $row->total_item;
                        }
                        @endphp
                        @php
                        $evis = \App\Models\Evis::where('production_id', $val->id)->get();
                        $evisberat = 0;
                        $evisekor = 0;
                        foreach ($evis as $key) {
                        $evisekor += $key->stock_item;
                        $evisberat += $key->berat_stock;
                        }
                        @endphp
                        <tr>
                            <td class="text">{{ ++$i }}</td>
                            <td class="text">{{ $val->prodpur->tanggal_potong }}</td>
                            <td class="text">{{ $val->no_urut }}</td>
                            <td class="text">{{ $val->prodpur->type_ekspedisi }}</td>
                            <td class="text">{{ $val->no_po }}</td>
                            <td class="text">@if ($val->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $val->prodpur->ukuran_ayam }} @endif</td>
                            <td class="text">{{ $val->prodpur->type_po }}</td>
                            <td class="text">{{ ($val->prodpur->purcsupp->nama ?? 'VENDOR TIDAK DITEMUKAN') }}</td>
                            <td class="text">{{ $val->sc_nama_kandang }}</td>
                            <td class="text">{{ $val->sc_wilayah }}</td>
                            <td class="text">{{ $val->no_do }}</td>
                            <td class="text">{{ $val->sc_pengemudi }}</td>
                            <td class="text">{{ $val->sc_no_polisi }}</td>
                            <td class="text">{{ $val->lpah_user_nama }}</td>
                            <td class="text">{{ number_format($val->sc_ekor_do, 0) }}</td>
                            <td class="text">{{ number_format($val->sc_berat_do, 1) }}</td>
                            <td class="text">{{ number_format($val->sc_rerata_do, 1) }}</td>
                            <td class="text">{{ number_format($val->ekoran_seckle, 0) }}</td>
                            <td class="text">{{ number_format($val->lpah_berat_terima, 1) }}</td>
                            <td class="text">
                                @if ($val->ekoran_seckle != 0)
                                {{ number_format($val->lpah_berat_terima / $val->ekoran_seckle, 1) }}
                                @else
                                {{ number_format(0,1) }}
                                @endif
                            </td>
                            <td class="text">{{ ($val->qc_tembolok) }}</td>
                            <td class="text">{{ ($val->sc_ekor_do - $val->ekoran_seckle) }}</td>
                            <td class="text">{{ number_format($val->lpah_berat_susut, 1) }}</td>
                            <td class="text">{{ number_format($val->lpah_persen_susut, 2) }} %</td>
                            <td class="text">{{ $val->qc_ekor_ayam_mati }}</td>
                            <td class="text">{{ $val->qc_berat_ayam_mati }}</td>
                            <td class="text">@if($val->qc_ekor_ayam_mati>0){{ number_format($val->qc_berat_ayam_mati/$val->qc_ekor_ayam_mati, 1) }} @else 0 @endif
                            </td>
                            <td class="text">{{ number_format($val->qc_ekor_ayam_mati != 0 ? ($val->qc_ekor_ayam_mati /
                                $val->sc_ekor_do) * 100 : 0, 2) }}
                                %
                            </td>
                            <td class="text">{{ $val->qc_ekor_ayam_merah }}</td>
                            <td class="text">{{ $val->qc_berat_ayam_merah }}</td>
                            <td class="text">{{ $val->kondisi_ayam }}</td>
                            <td class="text">{{ number_format($graditem, 0) }}</td>
                            <td class="text">{{ number_format($gradberat, 1) }}</td>
                            <td class="text">{{ number_format($graditem != 0 ? $gradberat / $graditem : '0', 1) }}</td>
                            <td class="text">
                                @php
                                if ($val->lpah_berat_terima != 0) {
                                $yield_produksi = $val->prod_yield_produksi;
                                } else {
                                $yield_produksi = 0;
                                }
                                @endphp
                                {{ number_format($yield_produksi, 2) }} %
                            </td>
                            <td class="text">
                                @if ($val->prodpur->type_ekspedisi == 'tangkap')
                                {{ number_format($val->sc_berat_do != 0 ? ($gradberat / $val->sc_berat_do) * 100 : '0',
                                2) }}
                                @else
                                {{ number_format($val->lpah_berat_terima != 0 ? ($gradberat / $val->lpah_berat_terima) *
                                100 : '0', 2) }}
                                @endif

                                %
                            </td>
                            <td class="text">{{ number_format($evisekor, 0) }}</td>
                            <td class="text">{{ number_format($evisberat, 1) }}</td>
                            <td class="text">{{ number_format($val->lpah_berat_terima != 0 ? ($evisberat /
                                $val->lpah_berat_terima) * 100 : '0', 2) }} %
                            </td>
                            <td class="text">
                                @php
                                if ($val->lpah_berat_terima != 0) {
                                $yield_evis = ($evisberat / $val->lpah_berat_terima) * 100;
                                } else {
                                $yield_evis = 0;
                                }
                                @endphp
                                {{ number_format($yield_evis, 2) }} %
                            </td>

                            @php
                            $getDataYield = App\Models\Adminedit::where('activity', 'input_yield')->where('content',
                            $val->prodpur->purchasing_item[0]->jenis_ayam )->where('type',
                            $val->prodpur->ukuran_ayam)->first();
                            if ($getDataYield) {
                            $decodeYield = json_decode($getDataYield->data);
                            }

                            @endphp

                            <td> {{ $val->keterangan_benchmark ?? '-' }}</td>
                            <td> {{ $decodeYield->yield_karkas ?? '-' }}</td>
                            <td> {{ $decodeYield->yield_evis ?? '-' }}</td>

                        </tr>
                        @php
                        if ($val->ekoran_seckle != 0){
                        $RataRataLPAH = number_format($val->lpah_berat_terima / $val->ekoran_seckle, 1);
                        }else{
                        $RataRataLPAH = number_format(0,1);
                        }
                        if($val->qc_ekor_ayam_mati > 0){
                        $RataRataMati = number_format($val->qc_berat_ayam_mati / $val->qc_ekor_ayam_mati, 1);
                        }else{
                        $RataRataMati = number_format(0,1);
                        }
                        if($val->qc_ekor_ayam_mati != 0) {
                        $RataRataPersenMati = number_format(($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 , 1);
                        }else{
                        $RataRataPersenMati = number_format(0,1);
                        }
                        if($graditem != 0){
                        $RataRataGrading = number_format(($gradberat / $graditem),1);
                        }else{
                        $RataRataGrading = number_format(0,1);
                        }
                        if($val->lpah_berat_terima != 0){
                        $RataRataProduksi = number_format($val->prod_yield_produksi,1);
                        }else{
                        $RataRataProduksi = number_format(0,1);
                        }
                        if ($val->prodpur->type_ekspedisi == 'tangkap'){
                        if($val->sc_berat_do != 0){
                        $RataRataRendemen = number_format(($gradberat / $val->sc_berat_do) * 100, 1);
                        }else{
                        $RataRataRendemen = number_format(0,1);
                        }
                        }else {
                        if($val->lpah_berat_terima != 0){
                        $RataRataRendemen = number_format(($gradberat / $val->lpah_berat_terima) * 100, 1);
                        }else{
                        $RataRataRendemen = number_format(0,1);
                        }
                        }

                        if($val->lpah_berat_terima != 0){
                        $PersenEvis = number_format(($evisberat / $val->lpah_berat_terima) * 100,1);
                        }else{
                        $PersenEvis = number_format(0,1);
                        }

                        $total_DO += $val->sc_ekor_do;
                        $total_BeratDO += $val->sc_berat_do;
                        $total_RerataDO += $val->sc_rerata_do;
                        $total_EkorSeckle += $val->ekoran_seckle;

                        $total_BeratLPAH += $val->lpah_berat_terima;
                        $total_RerataLPAH += $RataRataLPAH;
                        $total_Tembolok += $val->qc_tembolok;

                        $total_SelisihDO += ($val->sc_ekor_do - $val->ekoran_seckle);

                        $total_SusutDO += $val->lpah_berat_susut;
                        $total_PersenDO += $val->lpah_persen_susut;

                        $total_EkorMati += $val->qc_ekor_ayam_mati;
                        $total_BeratMati += $val->qc_berat_ayam_mati;
                        $total_RerataMati += $RataRataMati;

                        $total_PersenMati += $RataRataPersenMati;

                        $total_EkorMerah += $val->qc_ekor_ayam_merah;
                        $total_BeratMerah += $val->qc_berat_ayam_merah;

                        $total_EkorGrading += $graditem;
                        $total_BeratGrading += $gradberat;
                        $total_RerataGrading += $RataRataGrading;
                        $total_PersenProduksi += $RataRataProduksi;

                        $total_EkorEvis += $evisekor;
                        $total_BeratEvis += $evisberat;
                        $total_PersenEvis += $PersenEvis;
                        $total_YieldEvis += $PersenEvis;

                        $total_RendemenProduksi += $RataRataRendemen;

                        @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="14" class="text text-center"><b>Total</b></td>
                            <td class="text text-center"><b>{{ number_format($total_DO,0) }}</b></td>
                            <td class="text text-center"><b>{{ number_format($total_BeratDO,1) }}</b></td>
                            <td class="text text-center"><b>@if ($total_RerataDO > 0 && $NewArray['jumlahdata'] > 0){{ number_format(($total_RerataDO / $NewArray['jumlahdata']),1) }} @endif</b></td>
                            <td class="text text-center"><b>{{ number_format($total_EkorSeckle,1) }}</b></td>
                            <td class="text text-center"><b>{{ number_format($total_BeratLPAH,1) }}</b></td>
                            <td class="text text-center"><b>@if($total_RerataLPAH > 0 && $NewArray['jumlahtimbanglpah'] > 0 ){{ number_format(($total_RerataLPAH / $NewArray['jumlahtimbanglpah']),1) }} @endif</b></td>
                            <td class="text text-center"><b>{{ number_format($total_Tembolok,1) }}</b></td>
                            <td class="text text-center"><b>{{ number_format($total_SelisihDO,1) }}</b></td>
                            <td class="text text-center"><b>{{ number_format($total_SusutDO,1) }}</b></td>
                            <td class="text text-center"><b>@if($total_PersenDO > 0 && $NewArray['jumlahDataSusut'] > 0 ) {{ number_format(($total_PersenDO / $NewArray['jumlahDataSusut']),2) }} % @endif</b></td>
                            <td class="text text-center"><b>{{ number_format($total_EkorMati,1) }}</b></td>
                            <td class="text text-center"><b>{{ number_format($total_BeratMati,1) }}</b></td>
                            <td class="text text-center"><b>@if($total_RerataMati > 0 && $NewArray['jumlahDataMati'] > 0 ){{ number_format(($total_RerataMati / $NewArray['jumlahDataMati']),1) }} kg @endif</b></td>
                            <td class="text text-center"><b>@if($total_PersenMati > 0 && $NewArray['jumlahDataMati'] > 0 ){{ number_format(($total_PersenMati / $NewArray['jumlahDataMati']),2) }} % @endif</b></td>
                            <td class="text text-center"><b>{{ number_format($total_EkorMerah,1) }}</b></td>
                            <td class="text text-center"><b>{{ number_format($total_BeratMerah,1) }}</b></td>
                            <td class="text text-center"></td>
                            <td class="text text-center"><b>{{ number_format($total_EkorGrading,1) }}</b></td>
                            <td class="text text-center"><b>{{ number_format($total_BeratGrading,1) }}</b></td>
                            <td class="text text-center"><b>@if($total_RerataGrading > 0 && $NewArray['jumlahDataGrading'] > 0 ){{ number_format(($total_RerataGrading / $NewArray['jumlahDataGrading']),1) }} @endif</b></td>
                            <td class="text text-center"><b>@if($total_PersenProduksi > 0 && $NewArray['jumlahDataGrading'] > 0 ){{ number_format(($total_PersenProduksi / $NewArray['jumlahDataGrading']),2) }} % @endif</b></td>
                            <td class="text text-center"><b>@if($total_RendemenProduksi > 0 && $NewArray['jumlahDataGrading'] > 0 ){{ number_format(($total_RendemenProduksi / $NewArray['jumlahDataGrading']),2) }} % @endif</b></td>
                            <td class="text text-center"><b>{{ number_format($total_EkorEvis,1) }}</b></td>
                            <td class="text text-center"><b>{{ number_format($total_BeratEvis,1) }}</b></td>
                            <td class="text text-center"><b>@if($total_PersenEvis > 0 && $NewArray['jumlahdataevis'] > 0 ){{ number_format(($total_PersenEvis / $NewArray['jumlahdataevis']),2) }} % @endif</b></td>
                            <td class="text text-center"><b>@if($total_YieldEvis > 0 && $NewArray['jumlahdataevis'] > 0 ){{ number_format(($total_YieldEvis / $NewArray['jumlahdataevis']),2) }} % @endif</b></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
    </div>
</section>

<style>
    .default-table td {
        min-width: 100px;
    }
</style>

<a href="{{ route('bukubesar.index',array_merge(['key'=>'export'],$_GET)) }}" class="btn btn-blue">Export Excel</a>

{{-- <form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="laporan-rendemen-{{date('dmyHis')}}.xls">
    <textarea name="html" style="display: none" id="html-export"></textarea>
    <button type="submit" id="export-bb-fresh" class="btn btn-blue">Export</button>
</form> --}}

<script>
    $(document).ready(function(){
            var html  = $('#table-rendemen').html();
            $('#html-export').val(html);
        })
</script>

{{-- <a href="{{ route('bukubesar.export', ['tanggal' => $tanggal]) }}&tanggalend={{ $tanggalend . ($request->report ? "
    &report=" . $request->report : "") }}" class="btn btn-blue">Export Excel</a> --}}
{{-- </section> --}}
@stop

@section('footer')
<script>
    $('#loading').hide();
        $('.change-date').change(function() {
            $(this).closest("form").submit();
            $('#loading').show();
        });

        $('.change-date-end').change(function() {
            $(this).closest("form").submit();
            $('#loading').show();
        });

        $('#jenis_report').change(function() {
            $(this).closest("form").submit();
            $('#loading').show();
        });


        $('#export').click(function() {
            var titles = [];
            var data = [];

            /*
             * Get the table headers, this will be CSV headers
             * The count of headers will be CSV string separator
             */
            $('#export-table th').each(function() {
                titles.push($(this).text());
            });

            /*
             * Get the actual data, this will contain all the data, in 1 array
             */
            $('#export-table td').each(function() {
                data.push($(this).text());
            });

            /*
             * Convert our data to CSV string
             */
            var CSVString = prepCSVRow(titles, titles.length, '');
            CSVString = prepCSVRow(data, titles.length, CSVString);

            /*
             * Make CSV downloadable
             */
            var downloadLink = document.createElement("a");
            var blob = new Blob(["\ufeff", CSVString]);
            var url = URL.createObjectURL(blob);
            downloadLink.href = url;
            downloadLink.download = "data.csv";

            /*
             * Actually download CSV
             */
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        });

        /*
         * Convert data array to CSV string
         * @param arr {Array} - the actual data
         * @param columnCount {Number} - the amount to split the data into columns
         * @param initial {String} - initial string to append to CSV string
         * return {String} - ready CSV string
         */
        function prepCSVRow(arr, columnCount, initial) {
            var row = ''; // this will hold data
            var delimeter = ','; // data slice separator, in excel it's `;`, in usual CSv it's `,`
            var newLine = '\r\n'; // newline separator for CSV row

            /*
             * Convert [1,2,3,4] into [[1,2], [3,4]] while count is 2
             * @param _arr {Array} - the actual array to split
             * @param _count {Number} - the amount to split
             * return {Array} - splitted array
             */
            function splitArray(_arr, _count) {
                var splitted = [];
                var result = [];
                _arr.forEach(function(item, idx) {
                    if ((idx + 1) % _count === 0) {
                        splitted.push(item);
                        result.push(splitted);
                        splitted = [];
                    } else {
                        splitted.push(item);
                    }
                });
                return result;
            }
            var plainArr = splitArray(arr, columnCount);
            // don't know how to explain this
            // you just have to like follow the code
            // and you understand, it's pretty simple
            // it converts `['a', 'b', 'c']` to `a,b,c` string
            plainArr.forEach(function(arrItem) {
                arrItem.forEach(function(item, idx) {
                    row += item + ((idx + 1) === arrItem.length ? '' : delimeter);
                });
                row += newLine;
            });
            return initial + row;
        }
</script>
@stop
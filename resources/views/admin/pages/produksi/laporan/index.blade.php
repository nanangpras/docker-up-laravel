@extends('admin.layout.template')

@section('title', 'Laporan Produksi')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>LAPORAN PRODUKSI</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">

        <form action="{{ route('laporanproduksi.index') }}" method="get">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <label for="awal">Pencarian Tanggal Awal</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date" name="tanggal" id="awal" value="{{ $tanggal }}"
                        placeholder="Cari...">
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <label for="akhir">Pencarian Tanggal Akhir</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date-end" name="tanggalend" id="akhir"
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
        <div id="loading" class="text-center"><img src="{{ asset('loading.gif') }}" style="width: 18px"> Loading ...
        </div>
        <br>
        {{-- <section class="panel"> --}}
            <div class="table-responsive" id="table-laporan-produksi">
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
                            <th class="text" rowspan="2">NO PO</th>
                            <th class="text" rowspan="2">UKURAN</th>
                            <th class="text" rowspan="2">VENDOR</th>
                            <th class="text" rowspan="2">No. DO</th>
                            <th class="text" rowspan="2">DRIVER</th>
                            <th class="text" rowspan="2">JENIS</th>
                            <th class="text" rowspan="2">% TOLERANSI</th>
                            <th class="text" rowspan="2">No. MOBIL</th>
                            <th class="text" colspan="3">DO</th>
                            <th class="text" colspan="4">TIMBANG LPAH</th>
                            <th class="text" rowspan="2">SELISIH EKOR DO</th>
                            <th class="text" rowspan="2">SELISIH BERAT DO</th>
                            <th class="text" colspan="2">SUSUT DO-TIMBANG</th>
                            <th class="text" colspan="3">MATI</th>
                            <th class="text" rowspan="2">PROSENTASE MATI (%)</th>
                            <th class="text" colspan="3">AYAM MERAH</th>
                            <th class="text" rowspan="2">PROSENTASE AYAM MERAH (%)</th>
                            <th class="text" rowspan="2">KONDISI AYAM</th>
                            <th class="text" colspan="3">GRADING</th>
                            <th class="text" rowspan="2">YEILD PRODUKSI</th>
                            <th class="text" rowspan="2">RENDEMEN</th>
                            <th class="text" colspan="3">EVIS</th>
                            <th class="text" rowspan="2">YEILD EVIS</th>
                            <th class="text" rowspan="2">NOTE</th>
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
                            <th class="text">Rata2 Kg</th>
                            <th class="text">Ekor/Pcs/Pack</th>
                            <th class="text">Kg</th>
                            <th class="text">Rata2</th>
                            <th class="text">Ekor/Pcs/Pack</th>
                            <th class="text">Kg</th>
                            <th class="text">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $i => $val)
                        @php
                        $toleransi = App\Models\Target::where('alamat', 'like', '%' . preg_replace('/\s+/', '',
                        $val->sc_wilayah) . '%')->orderBy('id', 'DESC')->first()->target ?? 0 ;
                        @endphp
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
                            <td class="text">{{ $val->no_po }}</td>
                            <td class="text">@if ($val->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $val->prodpur->ukuran_ayam }} @endif</td>
                            <td class="text">{{ ($val->prodpur->purcsupp->nama ?? 'VENDOR TIDAK DITEMUKAN') }}</td>
                            <td class="text">{{ $val->no_do }}</td>
                            <td class="text">{{ $val->sc_pengemudi }}</td>
                            <td class="text">{{ $val->prodpur->type_ekspedisi }}</td>
                            <td class="text">{{ number_format($val->lpah_persen_susut ? $toleransi : 0, 2) }}</td>
                            <td class="text">{{ $val->sc_no_polisi }}</td>
                            <td class="text">{{ number_format($val->sc_ekor_do, 0) }}</td>
                            <td class="text">{{ number_format($val->sc_berat_do, 2) }}</td>
                            <td class="text">{{ number_format($val->sc_rerata_do, 2) }}</td>
                            <td class="text">{{ number_format($val->ekoran_seckle, 0) }}</td>
                            <td class="text">{{ number_format($val->lpah_berat_terima, 2) }}</td>
                            <td class="text">
                                @if ($val->ekoran_seckle != 0)
                                {{ number_format($val->lpah_berat_terima / $val->ekoran_seckle, 2) ?? '###' }}
                                @endif
                            </td>
                            <td class="text">{{ ($val->qc_tembolok) }}</td>
                            <td class="text">{{ ($val->sc_ekor_do - $val->ekoran_seckle) }}</td>
                            <td class="text">{{ ($val->sc_berat_do - $val->lpah_berat_terima) }}</td>
                            <td class="text">{{ number_format($val->lpah_berat_susut, 2) }}</td>
                            <td class="text">{{ $val->lpah_persen_susut }} %</td>
                            <td class="text">{{ $val->qc_ekor_ayam_mati }}</td>
                            <td class="text">{{ $val->qc_berat_ayam_mati }}</td>
                            <td class="text">@if($val->qc_ekor_ayam_mati>0){{ number_format($val->qc_berat_ayam_mati/$val->qc_ekor_ayam_mati, 2) }} @else 0 @endif
                            </td>
                            <td class="text">{{ number_format($val->qc_ekor_ayam_mati != 0 ? ($val->qc_ekor_ayam_mati / $val->sc_ekor_do) * 100 : 0, 2) }}
                                %
                            </td>
                            <td class="text">{{ $val->qc_ekor_ayam_merah ?? '0' }}</td>
                            <td class="text">{{ $val->qc_berat_ayam_merah ?? '0' }}</td>
                            <td class="text">{{ $val->qc_hitung_ayam_merah ?? '0' }}</td>
                            <td class="text">{{ $val->qc_persen_ayam_merah ?? '0' }} %</td>
                            <td class="text">{{ $val->kondisi_ayam }}</td>
                            <td class="text">{{ number_format($graditem, 0) }}</td>
                            <td class="text">{{ number_format($gradberat, 2) }}</td>
                            <td class="text">{{ number_format($graditem != 0 ? $gradberat / $graditem : '0', 2) }}</td>
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
                            <td class="text">{{ number_format($evisberat, 2) }}</td>
                            <td class="text">{{ number_format($val->lpah_berat_terima != 0 ? ($evisberat /
                                $val->lpah_berat_terima) * 100 : '0', 2) }}
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
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9" class="text text-center"><b>Total</b></td>
                            <td class="text text-center"><b></b></td>
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

<a href="{{ route('laporanproduksi.index',array_merge(['key'=>'export'],$_GET)) }}" class="btn btn-blue">Export
    Excel</a>

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

{{-- <a
    href="{{ route('laporanproduksi.export', ['tanggal' => $tanggal]) }}&tanggalend={{ $tanggalend . ($request->report ? "
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
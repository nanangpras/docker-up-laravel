@extends('admin.layout.template')

@section('title', 'Laporan QC')

@section('content')
<div class="row mb-2">
    <div class="col">
    </div>
    <div class="col-6 py-1 text-center">
        <b>Laporan QC</b>
    </div>
    <div class="col"></div>
</div>


<section class="panel">
    <div class="card-body">
        <form action="{{ route('laporan.qc') }}" method="get">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-6">
                    Pencarian
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date" name="tanggal" value="{{ $tanggal }}"
                        placeholder="Cari...">
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6">
                    <label for=""></label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control change-date-end" name="tanggalend"
                        value="{{ $tanggalend }}" placeholder="Cari...">
                </div>
                <div class="col-md-4 col-sm-4 col-xs-6">
                    Jenis Report
                    <select name="report" id="jenis_report" class="form-control">
                        <option value="all" {{ $request->report == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="po_lb" {{ $request->report == 'po_lb' ? 'selected' : '' }}>PO LB</option>
                        <option value="non_lb" {{ $request->report == 'non_lb' ? 'selected' : '' }}>PO Non LB</option>
                    </select>
                </div>
                <div id="loading"><img src="{{ asset('loading.gif') }}" style="width: 18px"> Loading ...</div>
            </div>
        </form>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="table-responsive" id="export-qc">
            <style>
                .text {
                    mso-number-format: "\@";
                    border: thin solid black;
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
                        <th class="text" rowspan="3">Diagnosis</th>
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
                    @foreach ($produksi as $i => $val)
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
                        <td class="text">{{ $val->post->kehijauan ?? 'ok' }}</td>
                        <td class="text">{{ $val->post->tembolok_kondisi ?? '0' }}</td>
                        <td class="text">{{ $val->qc_tembolok ?? '0' }}</td>
                        <td class="text">{{ $val->post->jeroan_hati ?? '0' }}</td>
                        <td class="text">{{ $val->post->jeroan_jantung ?? '0' }}</td>
                        <td class="text">{{ $val->post->jeroan_usus ?? '0' }}</td>
                        <td class="text">{{ $val->qc_under }}</td>
                        <td class="text">{{ $val->qc_uniform }}</td>
                        <td class="text">{{ $val->qc_over }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
<style>
    .default-table td {
        min-width: 100px;
    }
</style>

{{-- <a href="{{ route('bukubesar.exportqc', ['tanggal' => $tanggal]) }}&tanggalend={{ $tanggalend }}"
    class="btn btn-blue">Export Excel</a> --}}


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
</script>
@stop
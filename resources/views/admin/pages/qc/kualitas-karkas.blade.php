@extends('admin.layout.template')

@section('title', 'Laporan QC Kualitas Karkas')

@section('content')
<div class="row mb-2">
    <div class="col">
    </div>
    <div class="col-6 py-1 text-center">
        <b>Laporan QC Kualitas Karkas</b>
    </div>
    <div class="col"></div>
</div>


<section class="panel">
    <div class="card-body">
        <form action="{{ route('laporan.qc-kualitas-karkas') }}" method="get">
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

            <table class="table table-sm default-table border">
                <thead>
                    <tr class="text-center">
                        <th class="text" rowspan="3">No</th>
                        <th class="text" rowspan="3">Farm</th>
                        <th class="text" rowspan="3">Jumlah Mobil</th>
                        <th class="text" colspan="12">Defect (%)</th>
                    </tr>
                    <tr class="text-center">
                        <th class="text" colspan="3">Memar</th>
                        <th class="text" colspan="2">Patah</th>
                        <th class="text" colspan="4">Keropeng/Kapalan</th>
                        <th class="text" colspan="3">Defect Lain</th>

                    </tr>
                    <tr>
                        {{-- memear --}}
                        <th class="text">Dada</th>
                        <th class="text">Paha</th>
                        <th class="text">Sayap</th>
                        {{-- patah --}}
                        <th class="text">Sayap</th>
                        <th class="text">Kaki</th>
                        {{-- keropeng --}}
                        <th class="text">Kaki</th>
                        <th class="text">Dada</th>
                        <th class="text">Sayap</th>
                        <th class="text">Punggung</th>
                        {{-- defect lain --}}
                        <th class="text">Dengkul hijau</th>
                        <th class="text">Tembolok</th>
                        <th class="text">Keropeng dengkul</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produksi as $i => $item)
                    <tr>
                        <td class="text">{{++$i}}</td>
                        <td class="text">{{ ($item->sup_nama ?? 'VENDOR TIDAK DITEMUKAN') }}</td>
                        <td class="text">{{$item->jml_mobil}}</td>
                        <td class="text">{{number_format($item->count_memar_dada/$item->jml_mobil ,1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_memar_paha/$item->jml_mobil,1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_memar_sayap/$item->jml_mobil,1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_patah_sayap/$item->jml_mobil, 1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_patah_kaki/$item->jml_mobil,1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_keropeng_kaki/$item->jml_mobil, 1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_keropeng_dada/$item->jml_mobil ,1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_keropeng_sayap/$item->jml_mobil, 1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_keropeng_pg/$item->jml_mobil, 1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_kehijauan/$item->jml_mobil, 1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_tembolok_jumlah/$item->jml_mobil, 1) ?? '0'}}</td>
                        <td class="text">{{number_format($item->count_keropeng_dengkul/$item->jml_mobil, 1) ?? '0'}}
                        </td>
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
    <input name="filename" type="hidden" value="export-qc-karkas_{{$tanggal}}_{{$tanggalend}}.xls">
    <textarea name="html" style="display: none;" id="html-export-qc"></textarea>
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
@extends('admin.layout.template')

@section('title', 'Data Bonus')


@section('content')
<div class="row mb-4">
    <div class="col text-primary"><a href="{{ route('purchasing.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="col text-center"><b>Data Bonus</b></div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                PERIODE LAPORAN

                <div class="panel">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="laporan_mulai">Mulai</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="laporan_mulai" name="laporan_mulai"
                                    value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="laporan_sampai">Sampai</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="laporan_sampai" name="laporan_sampai"
                                    value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="supir">Supir</label>
                                <select id="supir" class="form-control select2" name="supir"
                                    data-placeholder="Pilih Supir" data-width="100%">
                                    <!-- <option value=""></option> -->
                                    <option value="all">Semua Supir</option>
                                    @foreach ($supir as $id => $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <h6>Wilayah</h6>
                                <div id="wilayah_select"></div>
                                {{-- <select name="wilayah" id="wilayahfilter" class="form-control select2">
                                    <option value="">Semua</option>
                                    @foreach ($wilayah as $rowdata )
                                    <option value="{{ $rowdata->sc_wilayah }}" {{ ($rowdata->sc_wilayah == $data_wilayah
                                        ? 'selected' : '') }}>{{ $rowdata->sc_wilayah }}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="ekspedisi">Jenis Ekspedisi</label>
                                <select id="ekspedisi" class="form-control select2" name="ekspedisi"
                                    data-placeholder="Pilih Ekspedisi" data-width="100%">
                                    <option value="all">Semua Jenis</option>
                                    <option value="kirim">Kirim</option>
                                    <option value="tangkap">Tangkap</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="loading-bonus" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</div>
                </div>


                <div class="col-md-12">
                    <div id="resume_data"></div>
                </div>
            </div>

        </div>
</section>
</section>
<section class="panel">
    <div class="card-body">

        <div id="table-export">
            <div id="view_header">
            </div>
            <div id="view_bonus"></div>
        </div>
    </div>
</section>

<form method="post" action="{{route('weekly.export')}}">
    @csrf
    <input name="filename" type="hidden" value="export-bonus-supir.xls">
    <textarea name="html" style="display: none" id="html-export-form"></textarea>
    <button type="submit" id="export-button" class="btn btn-blue">Export</button>
</form>

@endsection


@section('footer')
<script>
    $('.select2').select2({
            theme: 'bootstrap4',
        })
</script>
<link rel="stylesheet" type="text/css" href="{{ asset('') }}highcharts/highcharts-style.css" />
<script src="{{ asset('highcharts/highcharts.js') }}"></script>
<script src="{{ asset('highcharts/highcharts-more.js') }}"></script>
<script src="{{ asset('highcharts/exporting.js') }}"></script>
<script src="{{ asset('highcharts/export-data.js') }}"></script>
<script src="{{ asset('highcharts/accessibility.js') }}"></script>

<script>
    var bonusTimeout = null; 
    $("#laporan_mulai,#laporan_sampai,#supir,#ekspedisi").on('change', function(){
        if (bonusTimeout != null) {
            clearTimeout(bonusTimeout);
        }
        bonusTimeout = setTimeout(function() {
            bonusTimeout = null;  
            //ajax code
            reloadData()

        }, 1000);  
    }) 

    function wilayahfilter() {
        if (bonusTimeout != null) {
            clearTimeout(bonusTimeout);
        }
        bonusTimeout = setTimeout(function() {
            bonusTimeout = null;  
            //ajax code
            reloadData()

        }, 1000);  
    }

    reloadData()
    
    
    function reloadData(){
        $("#loading-bonus").show();
        var mulai       = $("#laporan_mulai").val();
        var akhir       = $("#laporan_sampai").val();
        var supir       = encodeURIComponent($("#supir").val());
        var wilayah     = encodeURIComponent($("#wilayahfilter").val());
        var ekspedisi     = $("#ekspedisi").val();
        
        $('#view_header').html('')
        if (supir !== '' && supir !== 'all') {
            $("#view_header").append(`
            <style>
                .text{
                    mso-number-format:"\@";
                    border:thin solid black;
                }
            </style>
            <div class="row">
                <div class="col">
                    <table class="table default-table">
                        <tbody>
                            <tr>
                                <th class="text">Mulai Dari</th>
                                <td class="text">`+ mulai +`</td>
                                <th class="text">Susut</th>
                                <td class="text"><div id="susut"/></td>
                            </tr>
                            <tr>
                                <th class="text">Sampai</th>
                                <td class="text">`+ akhir +`</td>
                                <th class="text">Toleransi</th>
                                <td class="text"><div id="toleransi"/></td>
                            </tr>
                            <tr>
                                <th class="text">Nama Sopir</th>
                                <td class="text">
                                    `+ supir +`
                                </td>
                                <th class="text">Hasil</th>
                                <td class="text">
                                    <div id="hasil" />
                                </td>
                            </tr>
                            <tr>
                                <th class="text">Nomor Polisi</th>
                                <td class="text"><div id="select-no-polisi"></div></td>
                                <th class="text"></th>
                                <td class="text">

                                </td>
                            </tr>
                            <tr>
                                <th class="text"></th>
                                <td class="text"></td>
                                <th class="text"></th>
                                <td class="text">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>`)
        }

        $("#wilayah_select").load("{{ route('purchasing.bonus', ['key' => 'wilayah']) }}&mulai=" + mulai + "&selesai=" + akhir + "&supir=" + supir + "&wilayah=" +wilayah + "&ekspedisi=" + ekspedisi)
        
        $("#select-no-polisi").load("{{ route('purchasing.bonus', ['key' => 'supir']) }}&id=" + supir, function(){
            var html  = $('#table-export').html();
            $('#html-export-form').val(html);
        });
        $("#resume_data").load("{{ route('purchasing.bonus', ['key' => 'resume']) }}&mulai=" + mulai + "&selesai=" + akhir + "&supir=" + supir + "&wilayah=" +wilayah + "&ekspedisi=" + ekspedisi, function(){
            var html  = $('#table-export').html();
            $('#html-export-form').val(html);
        });
        $("#view_bonus").load("{{ route('purchasing.bonus', ['key' => 'view']) }}&mulai=" + mulai + "&selesai=" + akhir + "&supir=" + supir + "&wilayah=" +wilayah + "&ekspedisi=" + ekspedisi, function(){
            var html  = $('#table-export').html();
            $('#html-export-form').val(html);
            $("#loading-bonus").hide();
        });
    }
</script>
@endsection
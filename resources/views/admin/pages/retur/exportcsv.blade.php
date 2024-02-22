@extends('admin.layout.template')

@section('title', 'Data Retur')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('retur.index') }}/#custom-tabs-three-summary" class="btn btn-outline btn-sm btn-back"> <i
                class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col text-center">
        <b>Data Retur</b>
    </div>
    <div class="col"></div>
</div>
<section class="panel">
    <div class="card-body">
        <div class="row mt-2">
            <div class="col">
                <div class="form-group">
                    <div class="form-group">
                        Tanggal Awal
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_awal" name="awal" value="{{ date('Y-m-d') }}"
                            class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="form-group">
                        Tanggal Akhir
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_akhir" name="akhir" value="{{ date('Y-m-d') }}"
                            class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <b>Jenis</b>
                    <select class="form-control" name="type" id="type">
                        <option value="peritem">Per Item</option>
                        <option value="percustomer">Per Customer</option>
                        <option value="itemfresh">Item Fresh</option>
                        <option value="itemfrozen">Item Frozen</option>
                        <option value="perkategori">Per Kategori</option>
                    </select>
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <b>Action</b><br>
                    <button type="submit" class="btn btn-blue downloadRetur"><i
                            class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span
                            id="text">Download</span></button>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <h5 class="text-center loading-exportcsv"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
        <div id="data_retur"></div>
    </div>
</section>

<script>
    var type = $("#type").val();
        var tanggal = $("#tanggal_awal").val();
        var akhir = $("#tanggal_akhir").val();

        $("#type, #tanggal_awal, #tanggal_akhir").on('change', () => {
            loadExportRetur()
        })


        loadExportRetur();

        function loadExportRetur() {
            $(".loading-exportcsv").show();
            type = $("#type").val();
            tanggal = $("#tanggal_awal").val();
            akhir = $("#tanggal_akhir").val();
            var jenisitem = $("#jenisitem").val();

            $("#data_retur").load("{{ route('retur.summary') }}?type=" + type + "&tanggal=" + tanggal + "&akhir=" + akhir + "&jenisitem=" + jenisitem,
            () => {
                $(".loading-exportcsv").hide();
            })
        }


        $(".downloadRetur").on('click', () => {
            type = $("#type").val();
            tanggal = $("#tanggal_awal").val();
            akhir = $("#tanggal_akhir").val();
            var jenisitem = $("#jenisitem").val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('retur.summary') }}",
                method: "GET",
                data: {
                    key: 'download',
                    tanggal,
                    akhir,
                    type,
                    jenisitem,
                },
                beforeSend: function() {
                    $('.downloadRetur').attr('disabled');
                    $(".spinerloading").show();
                    $("#text").text('Downloading...');
                },
                success: function(data) {
                    window.location = "{{ route('retur.summary', ['key' => 'download']) }}&type=" +
                        type + "&tanggal=" + tanggal + "&akhir=" + akhir + "&jenisitem=" + jenisitem
                    $("#text").text('Download');
                    $(".spinerloading").hide();
                }
            });
        })
</script>
<link rel="stylesheet" type="text/css" href="{{ asset('') }}highcharts/highcharts-style.css" />
<script src="{{ asset('highcharts/highcharts.js') }}"></script>
<script src="{{ asset('highcharts/highcharts-more.js') }}"></script>
<script src="{{ asset('highcharts/exporting.js') }}"></script>
<script src="{{ asset('highcharts/export-data.js') }}"></script>
<script src="{{ asset('highcharts/accessibility.js') }}"></script>
@stop
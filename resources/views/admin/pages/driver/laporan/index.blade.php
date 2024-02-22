@extends('admin.layout.template')

@section('title', 'Driver Summary Report')

@section('content')
<div class="row mb-4">
    <div class="col">
    </div>
    <div class="col text-center">
        <b>DRIVER SUMMARY REPORT</b>
    </div>
    <div class="col"></div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('driver.laporan') }}" method="GET">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Pencarian
                        <input type="text" name="q" class="form-control" value="" id="pencarian" placeholder="Cari...."
                            autocomplete="off">
                    </div>
                </div>
                <div class="col">
                    Jenis Driver
                    <div class="form-group">
                        <select name="jenisdriver" id="jenisdriver" class="form-control">
                            <option value="semua" @if(request()->jenisdriver == 'semua') selected @endif>Semua</option>
                            <option value="tangkap" @if(request()->jenisdriver == 'tangkap') selected @endif>Tangkap
                            </option>
                            <option value="kirim" @if(request()->jenisdriver == 'kirim') selected @endif>Kirim</option>
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <span class="d-none d-sm-block">&nbsp;</span>
                </div>
            </div>
        </form>
        <a href="{{ route('purchasing.bonus') }}" class="btn btn-primary" target="_blank">DATA BONUS DRIVER</a>
        <div id="loading-driver-summary" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</div>
    </div>
</div>


<div id="loadindexlaporandriver"></div>

<script>
    
    $("#loadindexlaporandriver").load("{{ route('driver.loadlaporan') }}", function() {
        $("#loading-driver-summary").hide();
    })
</script>

<script>
    $("#pencarian").keyup(function(){
        $('#loading-driver-summary').show();
        let pencarian = encodeURIComponent($(this).val());
        let jenisdriver = $('#jenisdriver').val();
        $("#loadindexlaporandriver").load("{{ route('driver.loadlaporan') }}?pencarian=" + pencarian + "&jenisdriver=" + jenisdriver + "&key=cari", function() {
            $('#loading-driver-summary').hide();
        })
    })

    $('#jenisdriver').change(function(){
        $('#loading-driver-summary').show();
        let pencarian = encodeURIComponent($('#pencarian').val());
        let jenisdriver = $('#jenisdriver').val();
        $("#loadindexlaporandriver").load("{{ route('driver.loadlaporan') }}?pencarian=" + pencarian + "&jenisdriver=" + jenisdriver + "&key=cari", function() {
            $('#loading-driver-summary').hide();
        })
    })
</script>
                    
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });
</script>

@stop
@extends('admin.layout.template')

@section('title', 'SIAP KIRIM FROZEN')

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col-6 py-1 text-center">
        <b>SIAP KIRIM FROZEN</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div class="form-group">
            Pencarian Tanggal SO
            <input type="hidden" name="customer" class="form-control" value="{{ $customer ?? '' }}" id="customer">
            <div class="row">
                <div class="col">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggal" class="form-control" value="{{ $tanggal }}"
                        id="pencarian" placeholder="Cari...." autocomplete="off">
                </div>
                <div class="col">
                    <input type="text" name="search" class="form-control" value="" id="search" placeholder="Cari...."
                        autocomplete="off">
                </div>
            </div>
        </div>

        <div id="loading" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" width="20px">
        </div>

        <div id="show"></div>
    </div>
</section>
@stop

@section('footer')

<script>
    var customer = $('#customer').val();
        var search = $('#search').val();
        var tanggal = "{{ $tanggal }}";

        $('#loading').show();
        $("#show").load("{{ route('penyiapanfrozen.order') }}?tanggal={{ $tanggal }}&customer=" + customer +
            "&search=" + search,
            function() {
                $('#loading').hide();
            });

        $('#pencarian').on('change', function() {
            tanggal = $(this).val();
            $('#loading').show();
            $("#show").load("{{ route('penyiapanfrozen.order') }}?tanggal=" + tanggal + "&customer=" + customer +
                "&search=" + search,
                function() {
                    $('#loading').hide();
                });
        })

        $('#search').on('keyup', function() {
            $('#loading').show();
            search = $(this).val();
            tanggal = $('#pencarian').val();
            console.log("{{ route('penyiapanfrozen.order') }}?tanggal=" + tanggal + "&customer=" + customer +
                "&search=" + search)
            $("#show").load("{{ route('penyiapanfrozen.order') }}?tanggal=" + tanggal + "&customer=" + customer +
                "&search=" + search,
                function() {
                    $('#loading').hide();
                });

        })
</script>

@endsection
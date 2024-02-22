@extends('admin.layout.template')

@section('title', 'Penyiapan')

@section('content')
<div class="mb-4 text-center">
    <b>SIAP KIRIM {{ strtoupper($search) ?? '' }} {{ \App\Models\Customer::find($customer)->nama ?? '' }}</b>
</div>

<section class="panel">
    <div class="card-body">
        <div class="form-group">
            Pencarian Tanggal SO
            <input type="hidden" name="customer" class="form-control" value="{{ $customer ?? '' }}" id="customer">
            <div class="row">
                <div class="col pr-1">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggal" class="form-control" value="{{ $tanggal }}"
                        id="pencarian" placeholder="Cari...." autocomplete="off">
                </div>
                <div class="col pl-1">
                    <input type="text" name="search" class="form-control" value="{{ $search }}" id="search"
                        placeholder="Cari...." autocomplete="off">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col pr-1">
                <button type="submit" class="btn btn-outline-primary btn-block proses" data-data="">Semua</button>
            </div>
            <div class="mb-3 col px-1">
                <button type="submit" class="btn btn-outline-success btn-block proses"
                    data-data="selesai">Selesai</button>
            </div>
            <div class="mb-3 col px-1">
                <button type="submit" class="btn btn-outline-info btn-block proses" data-data="proses">Pending</button>
            </div>
            <div class="mb-3 col px-1">
                <button type="submit" class="btn btn-outline-danger btn-block proses" data-data="gagal">Gagal</button>
            </div>
            <div class="mb-3 col pl-1">
                <button type="submit" class="btn btn-outline-warning btn-block proses" data-data="batal">Batal</button>
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="loading" class="text-center" style="display: none">
            <img src="{{ asset('loading.gif') }}" width="20px">
        </div>
        <div id="show"></div>
    </div>
</section>
@stop

@section('footer')
<script>
    var customer    =   $('#customer').val();
        var search      =   encodeURIComponent($('#search').val());
        var tanggal     =   "{{ $tanggal }}";
        var key         =   "{{$key ?? ''}}";
        var url         =   "";
        var delayTimer ;

        $('#loading').show();
        filterOrder();

        $('#pencarian').on('change', function() {
            tanggal =   $(this).val();
            $('#loading').show();
            filterOrder();
        })

        $('.proses').click(function() {
            key     =   $(this).data('data');
            tanggal =   $('#pencarian').val();
            $('#loading').show();

            filterOrder();
        })

        $('#search').on('keyup', function() {
            $('#loading').show();
            key     =   $('.proses').data('data');
            search  =   encodeURIComponent($(this).val());
            tanggal =   $('#pencarian').val();

            filterOrder();
        })

        function filterOrder()
        {
            clearTimeout(delayTimer);
            url =   "{{ route('penyiapan.index') }}?tanggal=" + tanggal + "&customer=" + customer + "&search=" + search + "&key=" + key;

            window.history.pushState('Siap kirim', 'Siap kirim', url);
            delayTimer = setTimeout(function() {
                
                $("#show").load("{{ route('penyiapan.order') }}?tanggal=" + tanggal + "&customer=" + customer + "&search=" + search + "&key=" + key, function() {
                    $('#loading').hide();
                });
            }, 1000);
        }
</script>

@endsection
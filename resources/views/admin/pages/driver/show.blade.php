@extends('admin.layout.template')

@section('title', 'Ekspedisi Pengiriman')

@section('footer')
<script>
    $(document).ready(function() {
    $('#customer_order').load("{{ route('driver.order', $data->id) }}");
    $('#delivery_route').load("{{ route('driver.route', $data->id) }}");
    $('#result').load("{{ route('driver.result', $data->id) }}");
});

$(document).ready(function() {
    $('#input_ekspedisi').click(function() {
        var tanggal     =   $('#tanggal').val();
        var wilayah     =   $('#wilayah').val();
        var no_polisi   =   $('#no_polisi').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('driver.addekspedisi', $data->id) }}",
            method: "POST",
            data: { tanggal: tanggal, wilayah: wilayah, no_polisi:no_polisi },
            success: function(data) {
                $('#customer_order').load("{{ route('driver.order', $data->id) }}");
                $('#delivery_route').load("{{ route('driver.route', $data->id) }}");
                $('#result').load("{{ route('driver.result', $data->id) }}");
            }
        });
    });
});

$(document).ready(function() {
    $(document).on('click', '.add_route', function() {
        var row_id = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('driver.addorder', $data->id) }}",
            method: "POST",
            data: { row_id: row_id },
            success: function(data) {
                $('#customer_order').load("{{ route('driver.order', $data->id) }}");
                $('#delivery_route').load("{{ route('driver.route', $data->id) }}");
                $('#result').load("{{ route('driver.result', $data->id) }}");
            }
        });
    });
});

$(document).ready(function() {
    $(document).on('click', '.batal_route', function() {
        var row_id = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('driver.batalroute', $data->id) }}",
            method: "POST",
            data: { row_id: row_id },
            success: function(data) {
                $('#customer_order').load("{{ route('driver.order', $data->id) }}");
                $('#delivery_route').load("{{ route('driver.route', $data->id) }}");
                $('#result').load("{{ route('driver.result', $data->id) }}");
            }
        });
    });
});
</script>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('driver.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>EKSPEDISI PENGIRIMAN</b>
    </div>
    <div class="col"></div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    Nama Driver
                    <input type="text" class="form-control bg-white" readonly value="{{ $data->nama }}">
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    No Polisi
                    <input type="text" class="form-control bg-white" autocomplete="off" id="no_polisi"
                        value="{{ $ekspedisi->no_polisi ?? '' }}">
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    Tanggal Kirim
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggal"
                        value="{{ date('Y-m-d', strtotime($ekspedisi->keluar ?? now())) }}" id='tanggal'
                        class="form-control">
                </div>
            </div>

            <div class="col">
                Wilayah
                <select name="wilayah" id='wilayah' class="form-control">
                    {{-- <option value="" disabled hidden selected>Pilih Wilayah</option> --}}
                    <option value="1" selected>Jadetabek</option>
                    {{-- @foreach ($wilayah as $id => $value)
                    <option {{ (($ekspedisi->wilayah_id ?? '') == $id) ? 'selected' : '' }} value="{{ $id }}">{{ $value
                        }}</option>
                    @endforeach --}}
                </select>
            </div>

            <div class="col">
                &nbsp;
                <button type="submit" class="btn btn-primary btn-block" id='input_ekspedisi'>
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-3">
                    <b>CUSTOMER ORDER</b>
                </div>
                <div id="customer_order"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-6">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-3">
                    <b>DELIVERY ROUTE</b>
                </div>
                <div id="delivery_route"></div>
            </div>
        </div>
    </div>
</div>

<div id="result"></div>
@stop
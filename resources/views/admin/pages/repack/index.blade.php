@extends('admin.layout.template')

@section('title', 'Repack')

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center py-2">
        <b>REPACK</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        Pencarian
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            class="form-control" name="q" value="{{date('Y-m-d')}}" id="tanggal">
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-2">
                <div class="form-group radio-toolbar">
                    <div class="row">
                        @foreach ($cold as $i => $item)
                        <div class="col-lg-12 col-4">
                            <input type="radio" name="cold" class="cold" value="{{ $item->id }}" id="{{ $item->id }}">
                            <label for="{{ $item->id }}">{{ $item->code }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-10">
                <div id="show"></div>
            </div>
        </div>
    </div>
</section>
<script>
    var id = "";
    var tanggal = "";

    $('#tanggal').change(function(){
        tanggal = $('#tanggal').val();
        $("#show").load("{{ route('repack.index', ['key' => 'show']) }}&id=" + id + "&tanggal="+tanggal);
    })

    $('.cold').change(function() {
        id = $(this).val();
        tanggal = $('#tanggal').val();
        $("#show").load("{{ route('repack.index', ['key' => 'show']) }}&id=" + id + "&tanggal="+tanggal);
    })

</script>
@endsection
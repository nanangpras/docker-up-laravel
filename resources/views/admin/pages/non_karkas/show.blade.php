@extends('admin.layout.template')

@section('title', 'Detail Penerimaan Non LB')

@section('content')
<div class="row mb-3">
    <div class="col pt-2">
        <a href="{{ route('nonkarkas.index') }}"><span class="fa fa-arrow-left"></span> Back</a>
    </div>
    <div class="col py-2 text-center"><b>Detail Penerimaan Non LB</b></div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div id="loading-detailnonlb" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</div>
        <div id="input_data"></div>

    </div>

</section>

@endsection

@section('footer')
<script>
    $("#input_data").load("{{ route('nonkarkas.show', [$data->id, 'key' => 'input_data']) }}", () => {
        $("#loading-detailnonlb").hide();
    })
</script>


@endsection

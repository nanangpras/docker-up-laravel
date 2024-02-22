@extends('admin.layout.template')

@section('title', 'Detail Customer Report')

@section('footer')
<script>
$("#summary").load("{{ route('customer.show', [$id, 'key' => 'summary']) }}");
$("#detail_view").load("{{ route('customer.show', [$id, 'key' => 'detail_view']) }}");
</script>
@endsection

@section('content')
<div class="mb-4 row">
    <div class="col"><a href="{{ route('customer.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col font-weight-bold text-center">Detail Customer Report</div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-header"><b>Summary Customer Report</b></div>
    <div class="card-body p-2">
        <div id="summary"></div>
    </div>
</section>

<div id="detail_view"></div>
@endsection

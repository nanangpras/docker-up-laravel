@extends('admin.layout.template')

@section('title', 'Pindah Gudang')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Pindah Gudang</b>
    </div>
    <div class="col"></div>
</div>
@php
$thawing = App\Models\Thawing::where('status', 1)->where('tanggal_request', date('Y-m-d'))->count() ;
@endphp

@if ($thawing)
<div class="alert alert-danger text-center mb-3">
    {{ $thawing }} Request Thawing Pending
</div>
@endif

<ul class="nav nav-tabs" role="tablist" id="custom-tabs-three-tab">
    <li class="nav-item">
        <a class="nav-link tab-link" id="pindah-gudang-tab" data-toggle="pill" href="#pindah-gudang" role="tab"
            aria-controls="pindah-gudang" aria-selected="true">
            PINDAH GUDANG
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link tab-link" id="summary-pindah-gudang-tab" data-toggle="pill" href="#summary-pindah-gudang"
            role="tab" aria-controls="summary-pindah-gudang" aria-selected="true">
            SUMMARY
        </a>
    </li>
</ul>

<section class="panel">
    <div class="card-body card-tabs">
        <div class="tab-content" id="pindah">
            <div class="tab-pane fade" id="pindah-gudang" role="tabpanel" aria-labelledby="pindah-gudang-tab">
                <div class="row mb-4">
                    <div class="col">
                        Pencarian
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif class="form-control" name="q" value="{{date('Y-m-d')}}"
                            id="tanggal-pindah">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2 mt-2">
                        <div class="form-group radio-toolbar">
                            @foreach ($cold as $i => $item)
                            <div class="">
                                <input type="radio" name="cold" class="cold" value="{{ $item->id }}"
                                    id="{{ $item->id }}">
                                <label for="{{ $item->id }}">{{ $item->code }}</label>
                            </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="col-lg-10 mt-2">
                        <div id="show"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="summary-pindah-gudang" role="tabpanel"
                aria-labelledby="summary-pindah-gudang-tab">
                <p>summary pindah gudang</p>
                @include('admin.pages.pindahgudang.summary.summary-pindah')
            </div>
        </div>
    </div>
</section>
{{-- <section class="panel">
    <div class="card-body">

    </div>
</section> --}}
<script>
    var hash = window.location.hash.substr(1);
        console.log(hash);
        defaultPage();

        function defaultPage() {
        if (hash == undefined || hash == "") {
                hash = "pindah-gudang";
            }

            $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');

        }

        $('.tab-link').click(function(e) {
            e.preventDefault();
            status = $(this).attr('aria-controls');
            window.location.hash = status;
            href = window.location.href;
        });

        if (hash === "pindah-gudang") {
            var id = "";
            var tanggal_pindah = "";
    
            $('#tanggal-pindah').change(function(){
                tanggal_pindah = $('#tanggal-pindah').val();
                console.log("{{ url('admin/gudang/show?id=') }}" + id + "&tanggal="+tanggal_pindah);
                $("#show").load("{{ url('admin/gudang/show?id=') }}" + id + "&tanggal="+tanggal_pindah);
            })
    
            $('.cold').change(function() {
                id = $(this).val();
                tanggal_pindah = $('#tanggal-pindah').val();
    
                console.log("{{ url('admin/gudang/show?id=') }}" + id + "&tanggal="+tanggal_pindah);
                $("#show").load("{{ url('admin/gudang/show?id=') }}" + id + "&tanggal="+tanggal_pindah);
            })
            
        }



</script>
@stop
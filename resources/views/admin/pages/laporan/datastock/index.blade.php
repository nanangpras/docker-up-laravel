@extends('admin.layout.template')

@section('title', 'Laporan Data Stock')

@section('footer')
<script>
    $('.change-date').change(function() {
    $(this).closest("form").submit();
});
</script>
@endsection

@section('content')
<div class="my-3 text-center">
    <b>Laporan Data Stock</b>
</div>
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('datastock.laporan') }}" method="get">
            <b>Pencarian Rentang Waktu</b>
            <div class="row">
                <div class="col">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="mulai" class="form-control change-date" value="{{ $mulai }}"
                        id="mulai" autocomplete="off">
                </div>
                <div class="col">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="akhir" class="form-control change-date" value="{{ $akhir }}"
                        id="mulai" autocomplete="off">
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-link active" id="alokasi-tab" data-toggle="tab" href="#alokasi" role="tab"
                aria-controls="alokasi" aria-selected="true">Alokasi</a>
            <a class="nav-link" id="ambil_bb-tab" data-toggle="tab" href="#ambil_bb" role="tab" aria-controls="ambil_bb"
                aria-selected="false">Ambil Bahan Baku</a>
            <a class="nav-link" id="bahanbaku-tab" data-toggle="tab" href="#bahanbaku" role="tab"
                aria-controls="bahanbaku" aria-selected="false">Bahan Baku</a>
            <a class="nav-link" id="produksi_masuk-tab" data-toggle="tab" href="#produksi_masuk" role="tab"
                aria-controls="produksi_masuk" aria-selected="false">Produksi Masuk</a>
            <a class="nav-link" id="lpah-tab" data-toggle="tab" href="#lpah" role="tab" aria-controls="lpah"
                aria-selected="false">LPAH</a>
            <a class="nav-link" id="open_balance-tab" data-toggle="tab" href="#open_balance" role="tab"
                aria-controls="open_balance" aria-selected="false">Open Balance</a>
        </div>
    </nav>
    <div class="card-body">
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="alokasi" role="tabpanel" aria-labelledby="alokasi-tab">
                @include('admin.pages.laporan.datastock.alokasi')
            </div>
            <div class="tab-pane fade" id="ambil_bb" role="tabpanel" aria-labelledby="ambil_bb-tab">
                @include('admin.pages.laporan.datastock.ambil_bb')
            </div>
            <div class="tab-pane fade" id="bahanbaku" role="tabpanel" aria-labelledby="bahanbaku-tab">
                @include('admin.pages.laporan.datastock.bahan_baku')
            </div>
            <div class="tab-pane fade" id="produksi_masuk" role="tabpanel" aria-labelledby="produksi_masuk-tab">
                @include('admin.pages.laporan.datastock.produksi_masuk')
            </div>
            <div class="tab-pane fade" id="lpah" role="tabpanel" aria-labelledby="lpah-tab">
                @include('admin.pages.laporan.datastock.lpah')
            </div>
            <div class="tab-pane fade" id="open_balance" role="tabpanel" aria-labelledby="open_balance-tab">
                @include('admin.pages.laporan.datastock.open_balance')
            </div>
        </div>
    </div>
</div>
@endsection
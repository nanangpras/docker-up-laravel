@extends('admin.layout.template')

@section('title', 'Pembelian Barang')

@section('footer')

@php
$unit_measure = [
"Piece",
"Roll",
"Lembar",
"Rim",
"Unit",
"Balok",
"Pack",
"Galon",
"Sachet",
"Tabung",
"Kaleng",
"Botol",
"Box",
"Buku",
"Drg",
"Dus",
"Kotak",
"Pasang",
"Slop",
"Tablet",
"Tube",
"Batang",
"Lusin",
"Set",
"Sak",
"Lot",
"Zak",
"Keranjang",
"Ekor",
"Meter",
"Centimeter",
"Liter",
"Mililiter",
"Kilogram",
"Gram",
"Ton",
"Jam",
"Dump",
"Rit",
"Menit",
"Detik"
]
@endphp

<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>

<script>
    $("#listPendingPR").load("{{ route('pembelian.index', ['key' => 'listPendingPR']) }}");
$("#listitempr").load("{{ route('pembelian.index', ['key' => 'listitempr']) }}");
// $("#riwayat_pembelian").load("{{ route('pembelian.riwayat') }}");
</script>

<script>
    $('#selesaikan').click(function() {
        var keterangan  =   $("#keterangan").val() ;
        var tanggal     =   $("#tanggal").val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#selesaikan').hide() ;

        $.ajax({
            url: "{{ route('pembelian.store') }}",
            method: "POST",
            data: {
                keterangan  :   keterangan ,
                tanggal     :   tanggal ,
                key         :   'selesaikan'
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    $("#data_list").load("{{ route('pembelian.index', ['key' => 'list']) }}");
                    $(".item").val(null).trigger('change') ;
                    $("#keterangan").val(null).trigger('change') ;
                    $("#tanggal").val('') ;
                }
                $('#selesaikan').show() ;
            }
        });
    })
</script>

<script>
    var x = 1;
function addRow(){
    var row = '';
        row +=  '<div class="mb-3 row-'+(x)+'">' ;
        row +=  '<div class="bg-light text-right"><span onclick="deleteRow('+(x)+')" class="cursor text-danger"><i class="fa fa-trash"></i> Hapus</span></div>' ;
        row +=  '<div class="row mb-2">' ;
        row +=  '    <div class="col-12">' ;
        row +=  '        Item' ;
        row +=  '        <select class="form-control select2" required name="item[]" data-placeholder="Pilih Item" data-width="100%">' ;
        row +=  '            <option value=""></option>' ;
        row +=  '            @foreach ($item as $row)' ;
        row +=  '            <option value="{{ $row->id }}">{{ $row->sku }}. {{ $row->nama }}</option>' ;
        row +=  '            @endforeach' ;
        row +=  '        </select>' ;
        row +=  '    </div>' ;

        row +=  '</div>' ;
        row +=  '<div class="row">' ;


        row +=  '    <div class="col-12 mt-3">' ;
        row +=  '        Kuantiti' ;
        row +=  '        <input type="number" required name="qty[]" step="0.01" class="form-control px-2" placeholder="Kuantiti (Sesuai Unit)" autocomplete="off">' ;
        row +=  '    </div>' ;

        row +=  '    <div class="col-12 mt-3">' ;
        row +=  '        Unit' ;
        row +=  '        <select required name="unit[]" class="form-control select2">' ;
        row +=  '                            @foreach($unit_measure as $u)';
        row +=  '                           <option value="{{$u}}">{{$u}}</option>';
        row +=  '                           @endforeach';
        row +=  '        </select>' ;
        row +=  '    </div>' ;
        row +=  '    </div>' ;


        row +=  '<div class="row">' ;
        row +=  '    <div class="col">' ;
        row +=  '        <div class="form-group">' ;
        row +=  '            Keterangan' ;
        row +=  '            <input type="text" name="keterangan[]" placeholder="Tulis keterangan" class="form-control" autocomplete="off">' ;
        row +=  '        </div>' ;
        row +=  '    </div>' ;
        row +=  '</div>' ;

    $('.data-loop').append(row);
    $('.select2').select2({
        theme: 'bootstrap4'
    })

    x++;
}

function deleteRow(rowid){
    $('.row-'+rowid).remove();
}
</script>
<style>
    .select2 {
        width: 100% !important;
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col font-weight-bold text-center text-uppercase">Permintaan / Pembelian Barang</div>
    {{-- <div class="col text-right"><a href="{{ route('pembelian.riwayat') }}"
            class="btn btn-success btn-sm">Riwayat</a></div> --}}
    <div class="col text-right">
        @if (User::setIjin(36))
        <a href="{{ route('pembelian.purchase') }}" class="btn btn-sm btn-success">Purchase</a>
        @endif
    </div>
</div>
<div class="card mt-4">
    <ul class="nav nav-tabs" id="tabs-tab" role="tablist">
        @if(User::setIjin(35))
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-pembelian-tab" data-toggle="pill" href="#tabs-pembelian" role="tab"
                aria-controls="tabs-pembelian" aria-selected="true">
                Pembelian Barang
            </a>
        </li>
        @endif
        @if(User::setIjin(35))
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-riwayat-tab" data-toggle="pill" href="#tabs-riwayat" role="tab"
                aria-controls="tabs-riwayat" aria-selected="false">
                Summary
            </a>
        </li>
        @endif
        @if(User::setIjin(43))
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-summarypr-tab" data-toggle="pill" href="#tabs-summarypr" role="tab"
                aria-controls="tabs-summarypr" aria-selected="false">
                Summary PR
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a class="nav-link tab-link" id="tabs-listitempr-tab" data-toggle="pill" href="#tabs-listitempr" role="tab"
                aria-controls="tabs-listitempr" aria-selected="false">
                List Item
            </a>
        </li>
    </ul>
    <div class="card-body">
        <div class="tab-content" id="tabs-tabContent">
            <div class="tab-pane fade " id="tabs-pembelian" role="tabpanel" aria-labelledby="tabs-pembelian-tab">
                <div id="pembelian_barang">
                    <div class="row">
                        <div class="col col-sm-12">
                            <section class="panel">
                                <form action="{{ route('pembelian.store',['id' => $id]) }}"
                                    enctype="multipart/form-data" method="post">
                                    <div class="card-body p-2">

                                        <div class="form-group">
                                            No PR : ID {{ $id ?? '#' }}
                                            @csrf
                                            <input type="text" name="no_pr" class="form-control" value=""
                                                autocomplete="off" placeholder="Nomor form PR" required>
                                            <input type="hidden" name="key" value="buat_pembelian" required>
                                        </div>

                                        <div class="form-group">
                                            Divisi
                                            <select name="keterangan" id="keterangan" class="form-control select2"
                                                data-placeholder="Pilih Divisi" data-width="100%" required>
                                                <option value="">- Pilih Divisi -</option>
                                                <option value="gudang">Gudang</option>
                                                <option value="produksi">Produksi</option>
                                                <option value="pembangunan">Pembangunan</option>
                                                <option value="accounting">Accounting</option>
                                                <option value="purchasing">Purchasing</option>
                                                <option value="marketing">Marketing</option>
                                                <option value="engineering">Engineering</option>
                                                <option value="hrga">HRGA</option>
                                                <option value="direktur">Direktur</option>
                                                <option value="lainnya">Lain-lain</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            Tanggal
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif id="tanggal" name="tanggal" class="form-control"
                                                value="{{ date("Y-m-d") }}">
                                        </div>

                                        <div class="form-group">
                                            <div>Foto Form *Jika ada</div>
                                            <input type="file" name="file" value="">
                                        </div>

                                    </div>
                                    <div class="card-body p-2">
                                        <button class="btn btn-primary btn-block">Buat Dokumen PR</button>
                                    </div>
                                </form>
                            </section>
                        </div>
                        <div class="col col-sm-12">
                            <section class="panel">
                                <div class="card-body text-center">
                                    <h6 class="my-4">
                                        Buat Dokumen PR Terlebih Dahulu
                                    </h6>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div id="listPendingPR">
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tabs-riwayat" role="tabpanel" aria-labelledby="tabs-riwayat">
                <section class="panel">
                    <div class="card-body">
                        <div class="row">
                            <div class="col pr-1">
                                <label for="">Tanggal Mulai</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggal_awal" value="{{ date("Y-m-d") }}"
                                    class="form-control">
                            </div>
                            <div class="col pl-1">
                                <label for="">Tanggal Akhir</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggal_akhir" value="{{ date("Y-m-d") }}"
                                    class="form-control">
                            </div>
                            <div class="col pl-1">
                                <label for="">Pencarian</label>
                                <input type="text" id="filterRiwayat" class="form-control"
                                    placeholder="Cari Nomor PR/Item/Nomor PO...">
                            </div>
                        </div>
                    </div>
                </section>
                <div id="loading-summaryriwayatpembelian" class="text-center mb-2">
                    <img src="{{ asset('loading.gif') }}" style="width: 30px">
                </div>
                <div id="riwayat_pembelian"></div>
            </div>
            <div class="tab-pane fade" id="tabs-summarypr" role="tabpanel" aria-labelledby="tabs-summarypr">
                <section class="panel">
                    <div class="card-body">
                        <div class="row">
                            <div class="col pr-1">
                                <label for="">Tanggal Mulai</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggal_awalSummaryPR" value="{{ date("Y-m-d") }}"
                                    class="form-control">
                            </div>
                            <div class="col pl-1">
                                <label for="">Tanggal Akhir</label>
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="tanggal_akhirSummaryPR" value="{{ date("Y-m-d") }}"
                                    class="form-control">
                            </div>
                            <div class="col pl-1">
                                <label for="">Pencarian</label>
                                <input type="text" id="filterSummaryPR" class="form-control"
                                    placeholder="Cari Nomor PR/Item/Nomor PO...">
                            </div>
                        </div>
                    </div>
                </section>
                <div id="loading-summarypr" class="text-center mb-2">
                    <img src="{{ asset('loading.gif') }}" style="width: 30px">
                </div>
                <div id="summaryPR"></div>
            </div>
            <div class="tab-pane fade" id="tabs-listitempr" role="tabpanel" aria-labelledby="tabs-listitempr">
                <div class="col pl-1">
                    <label for="">Pencarian</label>
                    <input type="text" id="filterlistitempr" class="form-control mb-3" placeholder="Cari...">
                </div>
                <div id="listitempr"></div>
            </div>
        </div>
    </div>
</div>

<script>
    loadDataRiwayatPR()
$("#tanggal_awal").on('change', function() {
    loadDataRiwayatPR()
})

$("#tanggal_akhir").on('change', function() {
    loadDataRiwayatPR()
})

$("#filterRiwayat").on('keyup', function() {
    loadDataRiwayatPR()
})

function loadDataRiwayatPR(){
    $('#loading-summarypr').show();
    let awal                =   $("#tanggal_awal").val() ;
    let akhir               =   $("#tanggal_akhir").val() ;
    let filterSummaryPR     =   encodeURIComponent($("#filterRiwayat").val() ?? '') ;
    $("#riwayat_pembelian").load("{{ route('pembelian.riwayat', ['key' => 'view']) }}&awal=" + awal + "&akhir=" + akhir + "&filterSummaryPR=" + filterSummaryPR, function() {
        $('#loading-summaryriwayatpembelian').hide();
    });
}

loadDataSummaryPR()
$("#tanggal_awalSummaryPR").on('change', function() {
    setTimeout(() => {
        loadDataSummaryPR()
    }, 1000);
})

$("#tanggal_akhirSummaryPR").on('change', function() {
    setTimeout(() => {
        loadDataSummaryPR()
    }, 1000);
})

$("#filterSummaryPR").on('keyup', function() {
    setTimeout(() => {
        loadDataSummaryPR()
    }, 1000);
})

function loadDataSummaryPR(){
    $('#loading-summarypr').show();
    let awal                =   $("#tanggal_awalSummaryPR").val() ;
    let akhir               =   $("#tanggal_akhirSummaryPR").val() ;
    let filterSummaryPR     =   encodeURIComponent($("#filterSummaryPR").val() ?? '') ;
    $("#summaryPR").load("{{ route('pembelian.riwayat', ['key' => 'summaryPR']) }}&awal=" + awal + "&akhir=" + akhir + "&filterSummaryPR=" + filterSummaryPR, function() {
        $('#loading-summarypr').hide();
    });
}


$("#filterlistitempr").on('keyup', function() {
    let pencarian = encodeURIComponent($("#filterlistitempr").val())
    $("#listitempr").load("{{ route('pembelian.index', ['key' => 'listitempr']) }}&pencarian=" + pencarian);
})
</script>

<script>
    var hash = window.location.hash.substr(1);
    var href = window.location.href;

    
    var set_ijin = "{{(User::setIjin(43))}}"
    deafultPage();
    

    function deafultPage() {
        if (hash == undefined || hash == "") {
            
            if(set_ijin==1){
                hash = "tabs-summarypr";
            }else{
                hash = "tabs-pembelian";
            }
        }

        console.log(set_ijin+hash);

        $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');

    }


    $('.tab-link').click(function(e) {
        e.preventDefault();
        status = $(this).attr('aria-controls');
        window.location.hash = status;
        href = window.location.href;

    });
</script>
@endsection
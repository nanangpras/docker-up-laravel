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
    $("#data_list").load("{{ route('pembelian.index', ['key' => 'list']) }}&id_list={{ $id }}");
$("#riwayat_pembelian").load("{{ route('pembelian.riwayat') }}");
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
        row +=  '            <option value="{{ $row->id }}">{{ $row->sku }}. {{ $row->nama }} ({{ $row->type }})</option>' ;
        row +=  '            @endforeach' ;
        row +=  '        </select>' ;
        row +=  '    </div>' ;

        row +=  '</div>' ;
        row +=  '<div class="row">' ;


        row +=  '    <div class="col-12 mt-3">' ;
        row +=  '        Kuantiti' ;
        row +=  '        <input type="number" required name="qty[]" step="0.01" class="form-control px-2" placeholder="Kuantiti (Sesuai Unit)" autocomplete="off">' ;
        row +=  '    </div>' ;

       


        // row +=  '<div class="row">' ;
        row +=  '    <div class="col mt-3">' ;
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

<script>
    // Validate Special Character

function clsAlphaNoOnly (e) {  // Accept only alpha numerics, no special characters 
    var regex = new RegExp("^[a-zA-Z0-9 ]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    // return false;
    showAlert('Tidak dapat memasukkan karakter selain huruf dan angka.');
}

function validatePaste(el, e) {
    var regex = /^[a-z .'-]+$/gi;
    var key = e.clipboardData.getData('text')
    if (!regex.test(key)) {
        e.preventDefault();
        // return false;
        showAlert('Tidak dapat memasukkan karakter selain huruf dan angka.');
    }
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
    <div class="col text-primary"><a href="{{ route('pembelian.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>
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
    <div class="card-body">
        <div class="tab-content" id="tabs-tabContent">
            <div class="tab-pane fade " id="tabs-pembelian" role="tabpanel" aria-labelledby="tabs-pembelian-tab">
                <div id="pembelian_barang">
                    <div class="row">
                        <div class="col col-sm-12">
                            <section class="panel sticky-top">
                                <form action="{{ route('pembelian.store') }}" enctype="multipart/form-data"
                                    method="post">
                                    <div class="card-body p-2">
                                        <input type="hidden" name="id_submit" id="id_submit" value="{{ $id }}">
                                        <div class="form-group">
                                            No PR : ID {{$id ?? "#"}}
                                            @csrf
                                            <input type="text" name="no_pr" class="form-control"
                                                value="{{ $pembelian->no_pr ?? '' }}" autocomplete="off"
                                                placeholder="Nomor form PR" required>
                                            <input type="hidden" name="key"
                                                value="{{ $pembelian ? 'selesaikan' : 'buat_pembelian' }}" required>
                                        </div>

                                        <div class="form-group">
                                            Divisi
                                            <select name="keterangan" id="keterangan" class="form-control select2"
                                                data-placeholder="Pilih Divisi" data-width="100%" required>
                                                <option value="">- Pilih Divisi -</option>
                                                <option value="gudang" {{ $pembelian ? ($pembelian->divisi == 'gudang' ?
                                                    'selected' : '') : '' }}>Gudang</option>
                                                <option value="produksi" {{ $pembelian ? ($pembelian->divisi ==
                                                    'produksi' ? 'selected' : '') : '' }}>Produksi</option>
                                                <option value="pembangunan" {{ $pembelian ? ($pembelian->divisi ==
                                                    'pembangunan' ? 'selected' : '') : '' }}>Pembangunan</option>
                                                <option value="accounting" {{ $pembelian ? ($pembelian->divisi ==
                                                    'accounting' ? 'selected' : '') : '' }}>Accounting</option>
                                                <option value="purchasing" {{ $pembelian ? ($pembelian->divisi ==
                                                    'purchasing' ? 'selected' : '') : '' }}>Purchasing</option>
                                                <option value="marketing" {{ $pembelian ? ($pembelian->divisi ==
                                                    'marketing' ? 'selected' : '') : '' }}>Marketing</option>
                                                <option value="engineering" {{ $pembelian ? ($pembelian->divisi ==
                                                    'engineering' ? 'selected' : '') : '' }}>Engineering</option>
                                                <option value="hrga" {{ $pembelian ? ($pembelian->divisi == 'hrga' ?
                                                    'selected' : '') : '' }}>HRGA</option>
                                                <option value="direktur" {{ $pembelian ? ($pembelian->divisi ==
                                                    'direktur' ? 'selected' : '') : '' }}>Direktur</option>
                                                <option value="lainnya" {{ $pembelian ? ($pembelian->divisi == 'lainnya'
                                                    ? 'selected' : '') : '' }}>Lain-lain</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            Tanggal
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif id="tanggal" name="tanggal" class="form-control"
                                                value="{{ $pembelian->tanggal ?? date('Y-m-d')}}">
                                        </div>

                                        <div class="form-group">
                                            @if(isset($pembelian->foto))
                                            Foto : <a href="{{asset($pembelian->foto)}}" target="_blank"><img
                                                    src="{{asset($pembelian->foto)}}"
                                                    style="width: 50px; height: auto; margin-bottom: 4px"> </a>
                                            <br>
                                            <span class="status status-info mt-2">*Abaikan jika foto sudah sesuai</span>
                                            <br>
                                            <input type="file" name="file" value="" class="mt-2">
                                            @else
                                            <span class="status status-info">Foto Form <b>*Jika ada</b> <br></span>
                                            <input type="file" name="file" value="" class="mt-2">
                                            @endif
                                        </div>

                                    </div>
                                    <div id="data_list"></div>
                                    <div class="card-body p-2">
                                        <button class="btn {{ $pembelian ? " btn-success" : "btn-primary" }}
                                            btn-block">{{ $pembelian ? 'Submit PR' : 'Buat Dokumen PR' }}</button>
                                    </div>
                                </form>
                            </section>
                        </div>
                        <div class="col col-sm-12">
                            @if ($pembelian)
                            <form action="{{ route('pembelian.store') }}" method="post">
                                @csrf <input type="hidden" name="key" value="add_item">
                                <input type="hidden" name="detail_id" value="{{ $id }}">
                                <section class="panel mb-3">
                                    <div class="card-header">
                                        <div class="float-right cursor" onclick="addRow()"><i class="fa fa-plus"></i>
                                            Tambah</div>
                                        Tambah Item
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="data-loop">
                                            <div class="mb-3">
                                                <div class="form-group">
                                                    Item
                                                    <select required name="item[]" class="form-control select2"
                                                        data-placeholder="Pilih Item" data-width="100%">
                                                        <option value=""></option>
                                                        @foreach ($item as $row)
                                                        <option value="{{ $row->id }}">{{ $row->sku }}. {{ $row->nama }}
                                                            ({{ $row->type }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    Kuantiti
                                                    <input type="number" required step="0.01" name="qty[]"
                                                        class="form-control px-2" placeholder="Kuantiti (Sesuai Unit)"
                                                        autocomplete="off">
                                                </div>
                                                <div class="form-group">
                                                    Keterangan
                                                    <input type="text" name="keterangan[]"
                                                        placeholder="Tulis keterangan" class="form-control"
                                                        autocomplete="off" onkeypress="clsAlphaNoOnly(event)"
                                                        onpaste="validatePaste(this, event)">
                                                </div>

                                            </div>
                                        </div>
                                        <button class="btn btn-primary btn-block" type="submit">Simpan Item</button>
                                    </div>
                                </section>
                            </form>
                            @else
                            <section class="panel">
                                <div class="card-body text-center">
                                    <h6 class="my-4">
                                        Buat Dokumen PR Terlebih Dahulu
                                    </h6>
                                </div>
                            </section>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="tabs-riwayat" role="tabpanel" aria-labelledby="tabs-riwayat">
                <div id="riwayat_pembelian"></div>
            </div>
        </div>
    </div>
</div>


<script>
    var hash = window.location.hash.substr(1);
    var href = window.location.href;

    deafultPage();

    function deafultPage() {
        if (hash == undefined || hash == "") {
            hash = "tabs-pembelian";
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
</script>
@endsection
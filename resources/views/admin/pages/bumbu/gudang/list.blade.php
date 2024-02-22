@extends('admin.layout.template')

@section('title', 'Data Bumbu Gudang')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>

        </div>
        <div class="col text-center">
            <b>Data Bumbu Gudang</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <div class="row d-flex">
                <div class="col">
                    <label for="tanggal_awal">Tanggal Awal</label>
                    <input type="date" value="{{ date("Y-m-d") }}" id="tanggal_awal" class="form-control">
                </div>
                <div class="col">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input type="date" value="{{ date("Y-m-d") }}" id="tanggal_akhir" class="form-control">
                </div>

                <div class="col">
                    <label for="cari_item">Search Nama Bumbu</label>
                    {{-- <input type="text" placeholder="Cari Bumbu..." id="cari_item" class="form-control"> --}}

                    <select name="cari_item" id="cari_item" data-width="100%" class="form-control select2">
                        <option value="" selected>Semua</option>
                        @php
                            $getDataBumbu = App\Models\Bumbu::all();
                        @endphp

                        @foreach ($getDataBumbu as $bumbu)
                        <option value="{{ $bumbu->id }}"> {{ $bumbu->nama }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="col">
                    <label for="cari_status">Status</label>
                    <select name="cari_status" id="cari_status" class="form-control">
                        <option value="semua">Semua</option>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                    </select>
                </div>
            </div>

            <div class="row mt-5 text-right">
                <div class="col">
                    <button class="btn btn-primary" id="download-item">
                        <i class="fa fa-download"></i> Download Excel
                    </button>
                    <button class="btn btn-success" id="cari-bumbu">
                        <i class="fa fa-search"></i> Cari Bumbu
                    </button>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3 mt-4">
                </div>
            </div>
            
            <div id="loadTableBumbu">

            </div>
        </div>
    </section>

    {{-- modal tambah --}}
    <div class="modal fade" id="tambahBumbu" aria-labelledby="tambahBumbuLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAlasanLabel">Tambah Data Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('bumbu.store') }}" method="post" id="tmbh_bumbu" onsubmit="return confirm('Pastikan data sudah sesuai!');">
                    @csrf
                    <input type="hidden" value="bumbu_gudang" name="key">
                    <div class="modal-body">
                        <div class="form-group">
                            Bumbu : <h5><b class="title-bumbu"></b></h5>
                            <input id="bumbu_id" name="bumbu_id" type="hidden">
                        </div>
                        <div class="form-group">
                            {{-- Stock Bumbu
                            <input type="number" name="stock" placeholder="Tuliskan stock bumbu" class="form-control" autocomplete="off" required> --}}
                        </div>
                        <div class="form-group">
                            Berat Bumbu
                            <input type="number" name="berat" id="berat" class="form-control form-control-sm" step="0.01" placeholder="Berat" autocomplete="off">
                            {{-- <input type="number" name="qty" placeholder="Tuliskan berat bumbu" class="form-control" autocomplete="off" required> --}}
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                    Tanggal
                                    <input type="date" name="tanggal" class="form-control form-control-sm" value="{{date("Y-m-d")}}">
                                </div>
                                <div class="col">
                                    Status
                                    <select name="status" id="status" class="form-control form-control-sm">
                                        <option value="masuk">Masuk</option>
                                        @if(auth()->user()->account_role == 'superadmin')
                                        <option value="keluar">Keluar</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="display: none" id="regu-keluar">
                            {{-- Tujuan --}}
                            <input type="hidden" name="regu" id="regu" value="adjustment" class="form-control form-control-sm" readonly>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="btnCancel" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end modal tambah --}}

    {{-- modal edit --}}
    <div class="modal fade" id="bumbuRecord" aria-labelledby="bumbuRecordLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="title-modal"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="content-bumbu"></div>
            </div>
        </div>
    </div>
    {{-- end modal edit --}}


@stop

@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('') }}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
    <script type="text/javascript" src="{{ asset('') }}plugin/DataTables/datatables.min.js"></script>

        <script>
                // $("#tanggal_awal").val("mm/dd/yyyy");
                // $("#tanggal_akhir").val("mm/dd/yyyy");

                $('#download-item').on("click", function(){
                    var cari_item       = $('#cari_item').val();
                    var cari_status     = $('#cari_status').val();
                    var tanggal_awal    = $('#tanggal_awal').val();
                    var tanggal_akhir   = $('#tanggal_akhir').val();

                    var url = "{{ route('bumbu.download') }}" +
                        "?key=unduh" +
                        "&tanggal_awal=" + tanggal_awal +
                        "&tanggal_akhir=" + tanggal_akhir +
                        "&cari_item=" + cari_item +
                        "&cari_status=" + cari_status;

                    window.location.href = url;

                });


                $("#loadTableBumbu").load("{{ route('bumbu.index', ['key' => 'gudang']) }}&subkey=search");


                $('#cari-bumbu').on("click", function(){
                    $("#loadTableBumbu").empty();
                    var cari_item        = $('#cari_item').val();
                    var cari_status      = $('#cari_status').val();
                    var tanggal_awal     = $('#tanggal_awal').val();
                    var tanggal_akhir    = $('#tanggal_akhir').val();

                    $.ajax({
                        // type: "GET",
                        url : "{{ route('bumbu.index') }}",
                        data : {
                            key : 'gudang',
                            tanggal_awal    : tanggal_awal,
                            tanggal_akhir   : tanggal_akhir,
                            cari_item       : cari_item,
                            cari_status     : cari_status,
                            subkey          : 'search',
                            subSubKey       : 'true'
                        },
                        success: function (response) {
                            $("#loadTableBumbu").html(response);
                            // console.log('oke')
                        }
                    });

                });
        </script>
@stop

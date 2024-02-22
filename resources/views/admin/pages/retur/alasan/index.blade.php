@extends('admin.layout.template')

@section('title', 'Data Alasan Retur')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>

        </div>
        <div class="col text-center">
            <b>Data Alasan Retur</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <div class="row">
                {{-- <div class="col-md-6 mb-3"> --}}
                    {{-- <label for="">Kelompok</label>
                    <select name="kelompok" data-placeholder="Pilih Kelompok" data-width="100%" class="form-control select2" id="pilihKelompok">
                        <option value=""></option>
                        <option value="Bau">Bau</option>
                        <option value="Memar">Memar</option>
                        <option value="Patah">Patah</option>
                        <option value="Warna Tidak Standar">Warna Tidak Standar</option>
                        <option value="Kualitas lain-lain">Kualitas lain-lain</option>
                        <option value="Non Kualitas">Non Kualitas</option>
                        <option value="Packing bermasalah">Packing bermasalah</option>
                        <option value="Produk tidk sesuai order">Produk tidk sesuai order</option>
                        <option value="Salah order">Salah order</option>
                        <option value="Tidak terkirim">Tidak terkirim</option>
                        <option value="Masalah Internal Konsumen">Masalah Internal Konsumen</option>
                    </select> --}}
                {{-- </div> --}}
                <div class="col-md-4 mb-3 mt-4">
                    <button class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#tambahAlasan">Tambah Alasan</button>
                    {{-- <button class="btn btn-primary" id="download-item"> <i class="fa fa-download"></i> <i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Export Excel</span> </button> --}}
                </div>
            </div>
            {{-- <h5 id="loading" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</h5> --}}
            <div class="table-responsive">
                <table class="table table-sm default-table dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Kelompok</th>
                            <th>Nama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $i => $row)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $row->jenis }}</td>
                                <td>{{ $row->kelompok }}</td>
                                <td>{{ $row->nama }}</td>
                                <td>
                                    <button class="btn btn-outline-warning rounded-0 btn-block" id="btnAlasan" data-toggle="modal" data-target="#editDataAlasan" data-id="{{$row->id}}"> Edit </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </section>
    {{-- modal tambah --}}
    <div class="modal fade" id="tambahAlasan" aria-labelledby="tambahAlasanLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAlasanLabel">Tambah Alasan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('retur.store') }}" method="post">
                    @csrf <input type="hidden" name="key" value="alasan">
                    <div class="modal-body">
                        <div class="form-group">
                            Kelompok
                            <select name="jenis" data-placeholder="Pilih Jenis" data-width="100%"
                                class="form-control select2" required>
                                <option value=""></option>
                                <option value="Bau">Bau</option>
                                <option value="Memar">Memar</option>
                                <option value="Patah">Patah</option>
                                <option value="Warna Tidak Standar">Warna Tidak Standar</option>
                                <option value="Kualitas lain-lain">Kualitas lain-lain</option>
                                <option value="Non Kualitas">Non Kualitas</option>
                                <option value="Packing bermasalah">Packing bermasalah</option>
                                <option value="Produk tidk sesuai order">Produk tidk sesuai order</option>
                                <option value="Salah order">Salah order</option>
                                <option value="Tidak terkirim">Tidak terkirim</option>
                                <option value="Masalah Internal Konsumen">Masalah Internal Konsumen</option>
                            </select>
                        </div>

                        <div class="form-group">
                            Nama Alasan
                            <input type="text" name="alasan" placeholder="Tuliskan nama alasan" class="form-control"
                                autocomplete="off" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal edit --}}
    <div class="modal fade" id="editDataAlasan" aria-labelledby="editDataAlasan" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDataAlasanLabel">Edit Alasan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="content-alasan"></div>
            </div>
        </div>
    </div>

@stop

@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('') }}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
    <script type="text/javascript" src="{{ asset('') }}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('.dataTable').DataTable();

            $(".table tbody").on("click","#btnAlasan", function () {
                var id      = $(this).attr('data-id');
                $.ajax({
                    type: "GET",
                    url: "{{route('retur.alasan')}}",
                    data: {
                        'key' : 'edit',
                        id    : id,
                    },
                    success: function (response) {
                        $("#content-alasan").html(response);
                    }
                });
            });
            
        } );

        

        $('.select2').select2({
            theme: 'bootstrap4',
        })
    </script>
    <script>
        $("#pilihKelompok").change(function () { 
                var kelompok = $(this).val();
                // alert(kelompok);
                $.ajax({
                    type: "GET",
                    url: "{{route('retur.alasan')}}",
                    data: {
                        kelompok: kelompok,
                    },
                    success: function (response) {
                        $('.dataTable').DataTable().ajax.reload();
                    }
                });
                	
            });
    </script>
@stop

@extends('admin.layout.template')

@section('title', 'Data Customer')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>Data Customer</b>
        </div>
        <div class="col"></div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm default-table dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>NS Internal ID</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
                            <th>Nama Marketing</th>
                            <th>Kategori</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $i => $row)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $row->kode }}</td>
                                <td>{{ $row->netsuite_internal_id }}</td>
                                <td>{{ $row->nama }}</td>
                                <td>{{ $row->alamat }}</td>
                                <td>{{ $row->telp }}</td>
                                <td>{{ $row->nama_marketing }}</td>
                                <td>{{ $row->kategori }}</td>
                                <td>
                                    <form action="{{ route('customers.show', $row->id) }}" method="get">
                                        <button class="btn btn-sm btn-primary" type="submit">Detail</button>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Detail Customer</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <div class="small">Kode</div>
                                                {{ $row->kode }}
                                            </div>
                                            <div class="form-group">
                                                <div class="small">Nama</div>
                                                {{ $row->nama }}
                                            </div>
                                            <div class="form-group">
                                                <div class="small">Alamat</div>
                                                {{ $row->alamat }}
                                            </div>
                                            <div class="form-group">
                                                <div class="small">Telepon</div>
                                                {{ $row->telp }}
                                            </div>
                                            <div class="form-group">
                                                <div class="small">Nama Marketing</div>
                                                {{ $row->nama_marketing }}
                                            </div>
                                            <div class="form-group">
                                                <div class="small">Kategori</div>
                                                {{ $row->kategori }}
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

@stop
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $('.dataTable').DataTable({
        "bPaginate": true,
        "bLengthChange": false,
        "bFilter": true,
        "bInfo": false,
        "bAutoWidth": false
    });
</script>
@stop
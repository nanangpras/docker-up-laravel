@extends('admin.layout.template')

@section('title', 'Data Customer')

@section('header')
    <style>
        

    </style>
@endsection

@section('content')
    <section class="panel">
        <header class="panel-heading">
            Hak Akses
        </header>
        <div class="card-body">
            <a href="#" data-toggle="modal" data-target="#tambah" class="btn btn-blue">Tambah Hak Akses</a><br><br>

            <div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="tambahLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahLabel">Tambah Akses</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('users.storehakakses') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                Nama
                                <input type="text" name="nama" class="form-control" id="nama" placeholder="Nama" required autocomplete="off">
                            </div>

                            <div class="form-group">
                                Function
                                <input type="text" name="function" class="form-control" id="function" placeholder="Function" required autocomplete="off">
                            </div>

                            <div class="form-group">
                                ID
                                <input type="text" name="id" class="form-control" id="id" placeholder="ID" required autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            <table class="table default-table">
                <thead>
                    <tr align="center">
                        <td>No</td>
                        <td>ID</td>
                        <td>Function</td>
                        <td>Name</td>
                        <td width="200px">Action</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $no => $u)
                        <tr>
                            <td width="50px">{{$loop->iteration+($data->currentpage() - 1) * $data->perPage()}}</td>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->function_name }}</td>
                            <td>{{ $u->function_desc }}</td>
                            <td>
                                {{-- <button type="submit" class="btn btn-primary">Edit</button>
                                <button type="submit" class="btn btn-danger">Hapus</button> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $data->links() }}
        </div>
    </section>

@stop

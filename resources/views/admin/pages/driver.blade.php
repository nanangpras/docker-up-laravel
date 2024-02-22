@extends('admin.layout.template')
<script>
    function editDriver(id){
        $.ajax({
            method: 'GET',
            url: "{{ route('driver.index') }}",
            data: {
                id:id,
                '_token': $('input[name=_token]').val(),
                key : 'view_data'
            },
            dataType: 'json',
            success: res =>{
                $('#idsopiredit').val(res.data.id)
                $('#namasopiredit').val(res.data.nama)
                $('#notelpedit').val(res.data.telp)
                $('#no_polisiedit').val(res.data.no_polisi)
                if(res.data.driver_kirim == 1){
                    $('#jenis').val('kirim')
                } else {
                    $('#jenis').val('tangkap')
                }
            }
        })
    }
</script>
@section('title', 'Driver List')

@section('content')
<div class="mb-4 font-weight-bold text-center">Driver List</div>

<div class="card mb-4">
    <div class="card-body">
        <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#tambahdriver">Tambah
            Driver</button>

        <div class="table-responsive">
            <table id="" class="table table-sm default-table dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Nomor Polisi</th>
                        <th>Nomor Telepon</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $i => $driver)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $driver->nama }}</td>
                        <td>{{ $driver->no_polisi }}</td>
                        <td>{{ $driver->telp }}</td>
                        <td>{{ $driver->driver_kirim ? 'Driver Kirim' : 'Driver Tangkap' }}</td>
                        <td><button class="btn btn-primary" data-toggle="modal" data-id="{{ $driver->id }}"
                                data-target="#driver" onclick="editDriver($(this).data('id'))">Edit</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="driver" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="driverLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driverLabel">Edit Driver</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('driver.update') }}" method="POST">
                @csrf @method('patch')
                <input type="hidden" name="x_code" id="idsopiredit" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class=form-group>
                                <label for="namasopiredit"> Nama Sopir</label>
                                <input class="form-control" type="text" name="namasopir" value="" id="namasopiredit"
                                    required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="notelpedit">No. Telp</label>
                                <input type="number" name="notelp" id="notelpedit" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class=form-group>
                                <label for="no_polisiedit"> Nomor Polisi</label>
                                <input class="form-control" type="text" name="no_polisi" value="" id="no_polisiedit">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="jenis">Jenis Driver</label>
                                <select name="jenis" id="jenis" class="form-control">
                                    <option value="" selected hidden disabled>Pilih Jenis Driver</option>
                                    <option value="kirim">Kirim</option>
                                    <option value="tangkap">Tangkap</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal" id="tambahdriver" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Driver</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('driver.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class=form-group>
                                <label for="namasopir"> Nama Sopir</label>
                                <input class="form-control" type="text" name="namasopir" id="namasopir" required>
                                @error('namasopir') <div class="small text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="notelp">No. Telp</label>
                                <input type="number" name="notelp" id="notelp" class="form-control" autocomplete="off">
                                @error('notelp') <div class="small text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class=form-group>
                                <label for="no_polisi"> Nomor Polisi</label>
                                <input class="form-control" type="text" name="no_polisi" value="" id="no_polisi"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="jenis_driver">Jenis Driver</label>
                                <select name="jenis" id="jenis_driver" class="form-control">
                                    <option value="" selected hidden disabled>Pilih Jenis Driver</option>
                                    <option value="kirim">Kirim</option>
                                    <option value="tangkap">Tangkap</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary ">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
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
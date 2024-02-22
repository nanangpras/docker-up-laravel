@extends('admin.layout.template')
@section('title', 'Marketing List')

@section('content')
    <div class="mb-4 font-weight-bold text-center">Marketing List</div>

    <div class="card mb-4">
        <div class="card-body">
            <button type="submit" class="btn btn-primary" data-toggle="modal" data-target="#tambahmarketing">Tambah
                Marketing</button>

            <div class="table-responsive">
                <table id="marketing_list_table" class="table table-sm default-table dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Nama Alias</th>
                            <th>Netsuite Internal Id</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $i => $marketing)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $marketing->nama }}</td>
                                <td>{{ $marketing->nama_alias }}</td>
                                <td>{{ $marketing->netsuite_internal_id }}</td>
                                <td><button class="btn btn-primary" data-toggle="modal" data-id="{{ $marketing->id }}"
                                        data-target="#marketing" onclick="editmarketing($(this).data('id'))">Edit</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="marketing" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="marketingLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="marketingLabel">Edit Marketing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- <form action="{{ route('marketing.update') }}" method="POST"> --}}
                @csrf @method('patch')
                <input type="hidden" name="idmarketing" id="idmarketingedit" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class=form-group>
                                <label for="namamarketingedit">Nama Marketing</label>
                                <input class="form-control" type="text" name="namamarketing" value=""
                                    id="namamarketingedit" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="namaaliasedit">Nama Alias</label>
                                <input type="text" name="namaalias" id="namaaliasedit" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="netsuite_internal_id">Netsuite Internal ID</label>
                                <input type="text" name="netsuite_internal_id" id="netsuite_internal_id" class="form-control"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary update_marketing">Update</button>
                </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>


    <div class="modal fade" id="tambahmarketing" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Input Marketing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- <form action="{{ route('marketing.store') }}" method="post"> --}}
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class=form-group>
                                <label for="namamarketing"> Nama Marketing</label>
                                <input class="form-control" type="text" name="namamarketing" id="namamarketing" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="namaalias">Nama Alias</label>
                                <input type="text" name="namaalias" id="namaalias" class="form-control"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary input_marketing">Simpan</button>
                </div>
                {{-- </form> --}}
            </div>
        </div>
    </div>
@endsection

@section('header')
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')
    <!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script> -->
    <script>
        $('.input_marketing').on('click', function() {
            let namamarketing = $('#namamarketing').val();
            let namaalias = $('#namaalias').val();
            if (namamarketing == '') {
                showAlert('Nama Marketing tidak boleh kosong');
                return false;
            }
            if (namaalias == '') {
                showAlert('Nama Alias tidak boleh kosong');
                return false;
            }

            $.ajax({
                url: "{{ route('marketing.store') }}",
                type: "POST",
                data: {
                    namamarketing: namamarketing,
                    namaalias: namaalias,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    // console.log(data)
                    if (data.status == 'success') {
                        showNotif(data.msg);
                        $('#tambahmarketing').modal('hide');
                        $('#namamarketing').val('');
                        $('#namaalias').val('');
                        window.location.reload();

                    } else {
                        showAlert(data.msg);
                    }
                }
            });
        })



        // Edit Data
        function editmarketing(id) {
            $.ajax({
                url: "{{ route('marketing.edit') }}",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    // console.log(data.data.nama)
                    if (data.status == 'success') {
                        $('#idmarketingedit').val(data.data.id);
                        $('#namamarketingedit').val(data.data.nama);
                        $('#namaaliasedit').val(data.data.nama_alias);
                        $('#netsuite_internal_id').val(data.data.netsuite_internal_id)
                        $('#marketing').modal('show');
                    } else {
                        showAlert(data.msg);
                    }
                }
            });
        }


        $('.update_marketing').on('click', function(){
            let id = $('#idmarketingedit').val();
            let namamarketing = $('#namamarketingedit').val();
            let namaalias = $('#namaaliasedit').val();
            const netsuite_internal_id = $('#netsuite_internal_id').val();
            if (namamarketing == '') {
                showAlert('Nama Marketing tidak boleh kosong');
                return false;
            }
            if (namaalias == '') {
                showAlert('Nama Alias tidak boleh kosong');
                return false;
            }

            if(netsuite_internal_id == ''){
                showAlert('Netsuite Internal ID tidak boleh kosong')
                return false;
            }

            $.ajax({
                url: "{{ route('marketing.update') }}",
                type: "POST",
                data: {
                    id: id,
                    namamarketing: namamarketing,
                    namaalias: namaalias,
                    netsuite_internal_id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    // console.log(data)
                    if (data.status == 'success') {
                        showNotif(data.msg);
                        $('#marketing').modal('hide');
                        window.location.reload();
                    } else {
                        showAlert(data.msg);
                    }
                }
            });
        })
    </script>
@stop

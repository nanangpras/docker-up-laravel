@extends('admin.layout.template')

@section('title', 'Daftar User')

@section('content')

    
    <section class="panel">
        <div class="card-body">
            <div class="row">
                @if (User::setIjin('superadmin') or User::setIjin(7)) 
                    <div class="col">
                        <button class="btn btn-primary mb-2 float-right" data-toggle="modal" data-target="#editSubsidiary">Edit Subsidiary User</button>
                    </div>
                @endif
                
                <div class="col-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-outer">
                        <div class="table-inner">
                            <div class="table-responsive">
                                <table class="table default-table">
                                    <thead>
                                    <tr align="center">
                                        <td>Name</td>
                                        <td>Email</td>
                                        <td>Action</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>{{$data->name}}</td>
                                            <td>{{$data->email}}</td>
                                            <td>
                                                <button type='button' class="btn btn-green btn-sm" data-toggle="modal" data-target="#editUser{{ $data->id }}">Edit</button>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="editUser{{ $data->id }}" aria-labelledby="editUser{{ $data->id }}Label" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editUser{{ $data->id }}Label">Update User</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('profile.update') }}" method="POST">
                                                    @csrf @method('patch') 
                                                    <input type="hidden" name="x_code" value="{{ $data->id }}"">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            Nama
                                                            <input type="text" name="nama" class="form-control" id="nama" placeholder="Tuliskan Nama" value="{{ $data->name }}" required autocomplete="off">
                                                        </div>

                                                        <div class="form-group">
                                                            E-Mail
                                                            <input type="email" name="email" class="form-control" id="email" placeholder="Tuliskan E-Mail" value="{{ $data->email }}" required autocomplete="off">
                                                        </div>

                                                        <div class="form-group">
                                                            Password
                                                            <input type="password" name="password" class="form-control" id="password" placeholder="Tuliskan Password" autocomplete="off">
                                                            <div class="small text-muted">Tulis password apabila akan diganti</div>
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="table-inner">
                            <div class="table-responsive">
                                <table class="table default-table">
                                    <thead>
                                    <tr align="center">
                                        <td>Role</td>
                                        <td>Active</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>@if(Session::has('subsidiary_id')) @if(Session::get('subsidiary_id') != 1)<a href="{{ route('dashboard', ['key' => 'setSubsidiary']) }}&value=CGL"> @endif @endif CGL</a></td>
                                            <td>@if(Session::has('subsidiary_id')) @if(Session::get('subsidiary_id') == 1)<div class="form-check">
                                                <input class="form-check-input position-static" type="checkbox" id="blankCheckbox" value="option1" aria-label="..." checked>
                                                </div> @else - @endif @else - @endif</td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>@if(Session::has('subsidiary_id')) @if(Session::get('subsidiary_id') != 2)<a href="{{ route('dashboard', ['key' => 'setSubsidiary']) }}&value=EBA"> @endif @endif EBA</a></td>
                                            <td>@if(Session::has('subsidiary_id')) @if(Session::get('subsidiary_id') == 2) <div class="form-check">
                                                <input class="form-check-input position-static" type="checkbox" id="blankCheckbox" value="option1" aria-label="..." checked>
                                                </div> @else - @endif @else - @endif</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </section>


    {{-- Modal Edit Subsidiary --}}
    @if (User::setIjin('superadmin') or User::setIjin(7)) 
    <div class="modal fade" id="editSubsidiary" aria-labelledby="editSubsidiaryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubsidiaryLabel">Update User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

                <div class="modal-body">
                    <div class="form-group">
                        User
                        <select name="user" id="user" class="form-control select2" data-placeholder="Pilih User">
                            <option value=""></option>
                            @foreach(App\Models\User::where('account_role', '!=', 'superadmin')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                    </select>
                    </div>
                    <div class="form-group">
                        Role
                        <select name="subsidiary" id="subsidiary" class="form-control select2" data-placeholder="Pilih Subsidiary">
                            <option value=""></option>
                            <option value="CGL">CGL</option>
                            <option value="EBA">EBA</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="editUserSubsidiary">Save</button>
                </div>
            </div>
        </div>
    </div>
    @endif


    <style>
        .default-table pre{
            font-family: sans-serif;
            line-height: 18pt;
            background: #fff;
            border: 1px solid #f9f9f9;

        }

        .table-outer{
            overflow: auto;
        }

        .table-inner{
            width: 100%;
        }
    </style>

@stop


@section('footer')
    <script>
        $("input:checkbox").click(function() { return false; });

        $('.select2').select2({
            theme: 'bootstrap4'
        })

        // Button edit
        const btnEditSubsidiary = document.getElementById("editUserSubsidiary")
        btnEditSubsidiary.addEventListener("click", () => {

            const user          = document.getElementById("user").value;
            const subsidiary    = document.getElementById("subsidiary").value;
            // console.log(user, subsidiary)
            if (user == undefined || user == "") {
                showAlert('Silahkan Pilih User')
            }

            if (subsidiary == undefined || subsidiary == "") {
                showAlert('Silahkan Pilih Subsidiary')
            }

            fetch("{{ route('dashboard', ['key' => 'setSubsidiary']) }}&user=" + user + "&subsidiary=" + subsidiary, {
                    headers: {
                        'Content-Type': 'application/json',
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    credentials: "same-origin",
                }).then((res) => {
                    return res.json();
                }).then((data) => {
                    // console.log(data)
                    if (data.status == 200) {
                        showNotif('Berhasil Merubah Subsidiary')
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000)
                    } else {
                        showAlert('Gagal Merubah Subsidiary')
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000)
                    }

            })
        })
    </script>
@endsection
<div class="row">
    <header class="panel-heading">
        User
    </header>
    <div class="col-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-outer">
            <div class="table-inner">
                <button type='button' class="btn btn-blue" data-toggle="modal" data-target="#tambah">New
                    User</button><br><br>
                <div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="tambahLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="tambahLabel">Tambah User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('users.store') }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group">
                                        Nama
                                        <input type="text" name="nama" class="form-control" id="nama"
                                            placeholder="Tuliskan Nama" value="{{ old('nama') }}" required
                                            autocomplete="off">
                                        @error('nama')
                                            <div class="small text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        E-Mail
                                        <input type="email" name="email" class="form-control" id="email"
                                            placeholder="Tuliskan E-Mail" value="{{ old('email') }}" required
                                            autocomplete="off">
                                        @error('email')
                                            <div class="small text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        Password
                                        <input type="password" name="password" class="form-control" id="password"
                                            placeholder="Tuliskan Password" required autocomplete="off">
                                        @error('password')
                                            <div class="small text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        Role
                                        <select name="role" class="form-control" id="role" required>
                                            <option value="" disabled selected hidden>Pilih Role</option>
                                            <option value="superadmin">Super Admin</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                        @error('role')
                                            <div class="small text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        Subsidiary
                                        <select name="subsidiary" class="form-control select2"
                                            data-placeholder="Pilih Subsidiary">
                                            <option value=""></option>
                                            <option value="1">CGL</option>
                                            <option value="2">EBA</option>
                                        </select>
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

                <div class="table-responsive">
                    <table class="table default-table">
                        <thead>
                            <tr align="center">
                                <td>No</td>
                                <td>Name</td>
                                <td>Email</td>
                                <td>Phone</td>
                                <td>Role</td>
                                <td>Netsuite Internal id</td>
                                <td>Created At</td>
                                <td>Last Seen</td>
                                <td>Online</td>
                                <td>Activity</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no=1;@endphp
                            @foreach ($user as $u)
                                <tr>
                                    <td width="50px">{{ $no }}</td>
                                    <td>{{ $u->name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ $u->phone }}</td>
                                    <td>
                                        <div style="overflow: scroll; max-width: 100px">
                                            {{ $u->group_role }}
                                        </div>
                                    </td>
                                    <td>{{ $u->netsuite_internal_id }}</td>
                                    <td>{{ date('d/m/y H:i:s', strtotime($u->created_at)) }}</td>
                                    <td>
                                        @if (isset($u->last_login))
                                            {{ date('d/m/y H:i:s', strtotime($u->last_login)) }}
                                        @else
                                            new account
                                        @endif
                                    </td>
                                    <td>
                                        @if (Cache::has('user-is-online-' . $u->id))
                                            <span class="text-success">Online</span>
                                        @else
                                            <span class="text-secondary">Offline</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ Cache::get('user-is-located-' . $u->id) }}
                                    </td>
                                    <td>
                                        <button type='button' class="btn btn-green btn-sm" data-toggle="modal"
                                            data-target="#editUser{{ $u->id }}">Edit</button>
                                        <button type='button' class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#hakAkses{{ $u->id }}">Hak Akses</button>
                                    </td>
                                </tr>
                                @php $no=$no+1;@endphp
                                <div class="modal fade" id="editUser{{ $u->id }}"
                                    aria-labelledby="editUser{{ $u->id }}Label" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editUser{{ $u->id }}Label">Update
                                                    User</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('users.update') }}" method="POST">
                                                @csrf @method('patch') <input type="hidden" name="x_code"
                                                    value="{{ $u->id }}"">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        Nama
                                                        <input type="text" name="nama" class="form-control"
                                                            id="nama{{ $u->id }}" placeholder="Tuliskan Nama"
                                                            value="{{ $u->name }}" required autocomplete="off">
                                                        @error('nama')
                                                            <div class="small text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        E-Mail
                                                        <input type="email" name="email" class="form-control"
                                                            id="email{{ $u->id }}"
                                                            placeholder="Tuliskan E-Mail" value="{{ $u->email }}"
                                                            required autocomplete="off">
                                                        @error('email')
                                                            <div class="small text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        Password
                                                        <input type="password" name="password" class="form-control"
                                                            id="password{{ $u->id }}"
                                                            placeholder="Tuliskan Password" autocomplete="off">
                                                        @error('password')
                                                            <div class="small text-danger">{{ $message }}</div>
                                                        @enderror
                                                        <div class="small text-muted">Tulis password apabila akan
                                                            diganti</div>
                                                    </div>

                                                    <div class="form-group">
                                                        Role
                                                        <select name="role" class="form-control"
                                                            id="role{{ $u->id }}" required>
                                                            <option value="" disabled selected hidden>Pilih Role
                                                            </option>
                                                            <option value="superadmin"
                                                                {{ $u->account_role == 'superadmin' ? 'selected' : '' }}>
                                                                Super Admin</option>
                                                            <option value="admin"
                                                                {{ $u->account_role == 'admin' ? 'selected' : '' }}>
                                                                Admin</option>
                                                        </select>
                                                        @error('role')
                                                            <div class="small text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        Netsuite Internal Id
                                                        <input type="text" name="netsuite_internal_id"
                                                            class="form-control"
                                                            id="netsuite_internal_id{{ $u->id }}"
                                                            placeholder="Tuliskan Netsuite Internal Id"
                                                            value="{{ $u->netsuite_internal_id }}"
                                                            autocomplete="off">
                                                    </div>
                                                    <div class="form-group">
                                                        Role / Subsidiary
                                                        <select name="subsidiary" class="form-control select2"
                                                            data-placeholder="Pilih Subsidiary">
                                                            <option value=""></option>
                                                            <option value="1"
                                                                {{ $u->company_id == 1 ? ' selected="selected"' : '' }}>
                                                                CGL</option>
                                                            <option value="2"
                                                                {{ $u->company_id == 2 ? ' selected="selected"' : '' }}>
                                                                EBA</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save
                                                        changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="hakAkses{{ $u->id }}" tabindex="-1"
                                    aria-labelledby="hakAkses{{ $u->id }}Label" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="hakAkses{{ $u->id }}Label">Hak
                                                    Akses</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            @php
                                                $ext = explode(',', $u->group_role);
                                            @endphp
                                            <form action="{{ route('users.akses') }}" method="POST">
                                                @csrf @method('put') <input type="hidden" name="x_code"
                                                    value="{{ $u->id }}">
                                                <div class="modal-body">
                                                    <div class="mb-3">Nama User : {{ $u->name }}</div>
                                                    Pilih Hak Akses

                                                    <div class="outer-scroll">
                                                        <div class="inner-scroll">

                                                        </div>
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        @foreach ($akses as $row)
                                                            <div class="border-bottom my-2">
                                                                <div class="row">
                                                                    <div class="col-auto">
                                                                        <input name="chk[]"
                                                                            value="{{ $row->id }}"
                                                                            id="akses_{{$row->id}}-{{$u->name}}"
                                                                            {{ in_array($row->id, $ext) ? 'checked' : '' }}
                                                                            type="checkbox">
                                                                        <label for="akses_{{$row->id}}-{{$u->name}}">
                                                                            {{ $row->function_desc }} || {{ $row->id }}
                                                                        </label>
                                                                    </div>
                                                                    <div class="col">
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save
                                                        changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

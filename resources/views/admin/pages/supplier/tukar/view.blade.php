
<div class="table-responsive">
    <table class="table default-table">
        <thead class="text-center">
            <tr>
                <th>No</th>
                <th>Supplier</th>
                <th>No DO</th>
                <th>Jenis PO</th>
                <th>Item</th>
                <th>Daerah</th>
                <th>Ekspedisi</th>
                <th>No Polisi</th>
                <th>Supir</th>
                <th>Jumlah</th>
                <th>Berat</th>
                <th>Rerata</th>
                <th>Waktu tiba</th>
                <th>Status</th>
                <th>Notif</th>
                <th>NoUrut</th>
                @if (User::setIjin('superadmin'))
                    <th>Edit Logs</th>
                @endif
                <th>Activity</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($diterima as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->prodpur->purcsupp->nama }}</td>
                    <td>{{ $row->no_do }}</td>
                    <td>{{ $row->prodpur->type_po }}</td>
                    <td>@if ($row->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $row->prodpur->ukuran_ayam }} @endif</td>
                    <td class="text-capitalize">{{ $row->sc_wilayah }}</td>
                    <td class="text-capitalize">{{ $row->po_jenis_ekspedisi ?? $row->prodpur->nama_po }}</td>
                    <td>{{ $row->sc_no_polisi }}</td>
                    <td>{{ $row->sc_pengemudi }}</td>
                    <td>{{ number_format($row->sc_ekor_do) }}</td>
                    <td>{{ number_format($row->sc_berat_do, 2) }} Kg</td>
                    <td>{{ number_format($row->sc_rerata_do, 2) }} Kg</td>
                    <td>{{ date('H:i', strtotime($row->sc_jam_masuk)) }}</td>
                    <td class="text-center">
                        <span class="status status-success">Selesai</span>
                        @if($row->prod_pending=="1")
                            <span class="status status-danger">POTunda</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($row->sc_status == '0')
                        <span class="status status-danger">Dibatalkan</span>
                        @else
                        {{ App\Models\Production::setNotifSecurity($row->id) }}
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $row->no_urut }}
                    </td>
                    @if (User::setIjin('superadmin'))
                        <td>
                            @foreach ($row->adminedt as $e)
                                <li>{{ $e->content }}</li>
                            @endforeach
                        </td>
                    @endif
                    <td class="text-center">
                        @if ($row->sc_status != '0')
                        <a href="" class="btn p-0 btn-link blue" data-toggle="modal" data-target="#edit{{ $row->id }}">Edit</a>
                        <form action="{{ route('security.reset') }}" method="post" class="d-inline-block">
                            @csrf @method('delete')
                            <input type="hidden" name="x_code" value="{{ $row->id }}">
                            <button type="submit" class="btn p-0 btn-link text-danger">Reset</button>
                        </form>
                        @endif
                        <button class="btn btn-link text-dark" data-toggle="modal" data-target="#ganti{{ $row->id }}">Tukar Supplier</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


@foreach ($diterima as $i => $row)
    <div class="modal fade" id="ganti{{ $row->id }}" tabindex="-1" aria-labelledby="ganti{{ $row->id }}Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ganti{{ $row->id }}Label">Tukar Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('security.store') }}" method="post">
                    @csrf <input type="hidden" name="key" value="tukar_supplier">
                    <input type="hidden" name="x_code" value="{{ $row->id }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 pr-md-1">
                                <div class="form-group">
                                    Supplier
                                    <input type="text" disabled value="{{ $row->prodpur->purcsupp->nama }}" class="form-control">
                                </div>

                                <div class="form-group">
                                    Nomor Urut
                                    <input type="text" disabled value="{{ $row->no_urut }}" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6 pl-md-1">
                                Tukar Nomor Urut
                                <input type="number" name="no_urut" min="1" autocomplete="off" placeholder="Tulis Nomor Urut" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade popup-edit-sc" id="edit{{ $row->id }}" aria-labelledby="edit{{ $row->id }}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit{{ $row->id }}Label">Edit Pengiriman Masuk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('security.update') }}" method="post">
                    @csrf @method('patch')
                    <input type="hidden" name="x_code" value="{{ $row->id }}">
                    <input type="hidden" name="tanggal" value="{{ $request->tanggal_supplier }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <div>Supplier</div>
                                    {{ $row->prodpur->purcsupp->nama }}
                                </div>

                                <div class="form-group">
                                    Supir/Kernek
                                    <input type="text" name="supir" class="form-control" value="{{ $row->sc_pengemudi }}" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    Nomor Urut
                                    <input type="number" name="no_urut" class="form-control" value="{{ $row->no_urut }}" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    Nomor DO
                                    <input type="text" name="no_do" class="form-control" value="{{ $row->no_do }}" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    Ekor DO
                                    <input type="number" name="ekor_do" class="form-control" value="{{ $row->sc_ekor_do }}" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    Berat DO
                                    <input type="text" name="berat_do" class="form-control" value="{{ $row->sc_berat_do }}" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    Target
                                    <input type="text" name="target" class="form-control" value="{{ $row->sc_pengemudi_target }}" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    Jam Masuk
                                    <input type="text" name="sc_jam_masuk" class="form-control" value="{{ $row->sc_jam_masuk }}" autocomplete="off">
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <div>Ukuran Ayam</div>
                                    {{ $row->prodpur->ukuran_ayam }}
                                </div>

                                <div class="form-group">
                                    No Polisi
                                    <input type="text" name="no_polisi" class="form-control" value="{{ $row->sc_no_polisi }}" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    Nama Kandang
                                    <input type="text" name="nama_kandang" class="form-control background-grey-2" placeholder="Nama Kandang" value="{{ $row->sc_nama_kandang }}" autocomplete="off">
                                </div>

                                <div class="form-group">
                                    Alamat Kandang
                                    <textarea name="alamat_kandang" class="form-control background-grey-2" placeholder="Tulis Alamat Kandang" cols="3">{{ $row->sc_alamat_kandang }}</textarea>
                                </div>

                                <div class="form-group">
                                    Alasan Perubahan
                                    <textarea name="alasan" class="form-control background-grey-2" placeholder="Tulis Alasan Perubahan" required cols="3"></textarea>
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

@endforeach

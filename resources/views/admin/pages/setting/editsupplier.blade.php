@extends('admin.layout.template')

@section('title', 'Data Pengiriman Masuk')

@section('content')

<div class="row mb-4">
    <div class="col">
    </div>
    <div class="col-6 py-1 text-center">
        <b>Data Penerimaan Masuk</b>
    </div>
    <div class="col"></div>
</div>
<section class="panel">
    <div class="card-body">
        <form action="{{ route('supplieredit.index') }}" method="GET">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="">Tanggal</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="tanggal" class="form-control" id="tanggal"
                            placeholder="Tuliskan " value="{{ $tanggal }}" autocomplete="off">
                        @error('tanggal') <div class="small text-danger">{{ message }}</div> @enderror
                    </div>
                </div>
                <div class="col">
                    &nbsp;
                    <div class="form-group mt-2">
                        <button type="submit" class="btn btn-primary btn-block">Cari</button>
                    </div>
                </div>
            </div>
        </form>

    </div>
</section>

<section class="panel">
    <div class="card-body">
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
                        <td class="text-capitalize">
                            {{ $row->po_jenis_ekspedisi ?? $row->prodpur->nama_po }}</td>
                        <td>{{ $row->sc_no_polisi }}</td>
                        <td>{{ $row->sc_pengemudi }}</td>
                        <td>{{ number_format($row->sc_ekor_do) }}</td>
                        <td>{{ number_format($row->sc_berat_do, 2) }} Kg</td>
                        <td>{{ number_format($row->sc_rerata_do, 2) }} Kg</td>
                        <td>{{ date('H:i', strtotime($row->sc_jam_masuk)) }}</td>
                        <td class="text-center"><span class="status status-success">Selesai</span></td>
                        <td class="text-center"> {{ App\Models\Production::setNotifSecurity($row->id) }}</td>
                        <td>{{ $row->no_urut }}</td>
                        <td class="text-center">
                            <button type="submit" class="btn btn-primary" data-toggle="modal"
                                data-target="#editsupplier{{ $row->id }}">Edit</button>
                            {{-- <a href="" class="blue"></a> --}}
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</section>
@foreach ($diterima as $i => $row)
<div class="modal fade" id="editsupplier{{ $row->id }}" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit SUPPLIER</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('supplieredit.store') }}" method="post">
                @csrf
                <input type="hidden" name="x_code" value="{{ $row->id }}">
                <input type="hidden" name="idlama" value="{{ $row->purchasing_id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="">Supplier lama</label>
                                    <input type="text" name="" class="form-control" id="" placeholder="Tuliskan "
                                        value="{{ $row->prodpur->purcsupp->nama }}" autocomplete="off" readonly>
                                    @error('') <div class="small text-danger">{{ message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">No Nurut</label>
                                    <input type="text" name="" class="form-control" id="" placeholder="Tuliskan "
                                        value="{{ $row->no_urut }}" autocomplete="off" readonly>
                                    @error('') <div class="small text-danger">{{ message }}</div>
                                    @enderror
                                </div>


                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">Supplier Baru</label>
                                <select name="supplier" class="form-control" id="supplier">
                                    <option value="" disabled selected hidden>Pilih </option>
                                    @foreach ($purch as $key)
                                    <option value="{{ $key->id }}"> {{ $key->purcsupp->nama }} - {{ $key->ukuran_ayam }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('supplier') <div class="small text-danger">{{ message }}
                                </div> @enderror
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Edit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@stop
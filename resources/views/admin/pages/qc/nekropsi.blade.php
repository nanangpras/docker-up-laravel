@extends('admin.layout.template')

@section('title', 'Nekropsi')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('qc.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Nekropsi</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <form action="{{ route('qc.nekropsi') }}" method="GET">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Pencarian
                        <input type="text" name="q" class="form-control" value="{{ $q }}" id="pencarian" placeholder="Cari...." autocomplete="off">
                    </div>
                </div>
                <div class="col-auto">
                    <span class="d-none d-sm-block">&nbsp;</span>
                    <button type="submit" class="btn btn-block btn-primary">FILTER</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table default-table table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Supplier</th>
                        <th>UK</th>
                        <th>Daerah</th>
                        <th>Ekspedisi</th>
                        <th>Jumlah PO</th>
                        <th>Tanggal Potong</th>
                        <th>Jumlah Ayam</th>
                        <th>Ayam Mati</th>
                        <th>Status</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $i => $row)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->purcsupp->kode }}</td>
                        <td>{{ $row->ukuran_ayam }}</td>
                        <td class="text-capitalize">{{ $row->wilayah_daerah }}</td>
                        <td class="text-capitalize">{{ $row->type_ekspedisi }}</td>
                        <td>{{ number_format($row->jumlah_po) }}</td>
                        <td>{{ date('d/m/y', strtotime($row->tanggal_potong)) }}</td>
                        <td>{{ number_format($row->jumlah_ayam) }}</td>
                        <td>{{ number_format($row->ayam_mati) }}</td>
                        <td>@php echo $row->status_purchase; @endphp</td>
                        <td class="text-center">
                            {{-- @if ($row->ayam_mati >= 20) --}}
                            <a href="{{ route('qc.nekropsi_show', $row->id) }}" class="btn btn-sm btn-{{ $row->nekrop ? 'success' : 'primary' }}">{{ $row->nekrop ? 'Update' : 'Edit'}}</a>
                            {{-- @endif --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $data->appends(compact('q'))->onEachSide(1)->links() }}
    </div>
</section>

@stop

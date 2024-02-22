@extends('admin.layout.template')

@section('title', 'Laporan Sales Order')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Laporan Sales Order</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    {{-- <div class="card-body">
        <h5>Upload SO</h5>
        <form method="POST" action="{{url('admin/upload-so-excel')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{{Request::url()}}" name="url">
            <input type="file" name="file">
            <button class="btn btn-blue">Save</button>
        </form>
    </div>
    <hr> --}}
    <div class="card-body">
        <b>Pencarian Bedasarkan Tanggal</b>
        <form action="{{ route('salesorder.laporan') }}" method="GET">
            <div class="row mt-2">
                <div class="col">
                    <div class="form-group">
                        Dari
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="mulai" value="{{ $mulai }}"
                            class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        Sampai
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="selesai" value="{{ $selesai }}"
                            class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="col">
                    &nbsp;
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
                <div class="col">
                    &nbsp;
                    <button type="submit" name="download" value="download"
                        class="btn btn-green btn-block">Download</button>
                </div>
                <div class="col">
                    &nbsp;
                    <a href="{{route('salesorder.add')}}" class="btn btn-danger btn-block">Tambah Orderan Manual</a>
                </div>

            </div>
        </form>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table default-table dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>LineID</th>
                        <th>Marketing</th>
                        <th>Nama Customer</th>
                        <th>No SO</th>
                        <th>Sales Channel</th>
                        <th>Tanggal SO</th>
                        <th>Tanggal Kirim</th>
                        <th>Item</th>
                        <th>QTY</th>
                        <th>Berat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $i => $row)
                    <tr>
                        {{-- <td>{{$loop->iteration+($data->currentpage() - 1) * $data->perPage()}}</td> --}}
                        <td>{{++$i}}</td>
                        <td>{{ $row->line_id }}</td>
                        <td>{{ $row->ordercustomer->nama_marketing ?? '#'}}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->no_so }}</td>
                        <td>{{ $row->sales_channel }}</td>
                        <td>{{ date('d/m/y H:i:s', strtotime($row->created_at)) }}</td>
                        <td>{{ date('d/m/y', strtotime($row->tanggal_kirim)) }}</td>
                        <td>{{ count($row->daftar_order) }}</td>
                        <td>
                            @php $qty = 0; @endphp
                            @foreach($row->daftar_order as $i)
                            @php $qty = $qty+$i->qty; @endphp
                            @endforeach
                            {{$qty}}
                        </td>
                        <td>
                            @php $berat = 0; @endphp
                            @foreach($row->daftar_order as $i)
                            @php $berat = $berat+$i->berat; @endphp
                            @endforeach
                            {{$berat}} Kg
                        </td>
                        <td>{!!$row->status_order!!}</td>
                        <td>
                            <a href="{{ route('salesorder.detail', $row->id) }}"
                                class="btn btn-sm btn-primary">Lihat</a>
                            {{-- <a href="{{ route('salesorder.retur', $row->id) }}"
                                class="btn btn-sm btn-danger">Retur</a> --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- {{ $data->appends($_GET)->links() }} --}}
    </div>
</section>

@stop

@section('header')
<!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')
<!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script> -->
@stop
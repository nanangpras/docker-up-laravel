@extends('admin.layout.template')

@section('title', 'Penyiapan')

@section('content')
<div class="row mb-4">
    <div class="col py-1">
        <a href="{{ route('sync.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-8 py-1 text-center">
        <b class="text-uppercase">TRACING SO</b>
    </div>
    <div class="col"></div>
</div>


<style>
    .hidden-form {
        display: none;
    }
</style>

<section class="panel">
    <div class="card-body">
        <form method="get" action="{{url('admin/wo/so-list')}}">
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" value="{{ Request::get('tanggal') ?? date('Y-m-d') }}" class="form-control mb-2">
            <button type="submit" class="btn btn-blue">Filter</button>
        </form>
    </div>
    <div class="row">
        <div class="table-responsive card-body">
            <div class="table-responsive mt-4">
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>LineID</th>
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
                        @foreach ($order as $i => $row)
                        <tr>
                            {{-- <td>{{$loop->iteration+($data->currentpage() - 1) * $data->perPage()}}</td> --}}
                            <td>{{++$i}}</td>
                            <td>{{ $row->line_id }}</td>
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
                        <tr>
                            <td colspan="12">
                                <table>
                                    <tbody>
                                        @foreach($row->daftar_order as $no_list => $do)

                                        <tr>
                                            {{-- <td>{{ ++ $no_list }}</td> --}}
                                            <td>LINEID{{ $do->line_id }}</td>
                                            <td>{{ $do->nama_detail }}</td>
                                            <td>{{ $do->qty }}</td>
                                            <td>{{ $do->berat }}</td>
                                            <td>{{ $do->part }}</td>
                                            <td>{{ $do->bumbu }}</td>
                                            <td>{{ $do->memo }}</td>
                                            <td>{{ $do->keterangan }}</td>
                                            <td>{{ $do->fulfillment_qty }}</td>
                                            <td>{{ $do->fulfillment_berat }}</td>
                                            <td>
                                                @if($do->status==2)
                                                <span class="status status-success">Selesai</span>
                                                @endif
                                                @if($do->status==3)
                                                <span class="status status-danger">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="11">
                                                @php
                                                $bb = $do->bahan_baku;
                                                @endphp
                                                @if(count($bb)>0)
                                                Fulfill Chiller Out :
                                                <hr>
                                                @foreach($bb as $bb_out)
                                                {{$bb_out->chiller_out}} || {{$bb_out->bb_item}} pcs -
                                                {{$bb_out->bb_berat}} Kg
                                                <hr>

                                                @php
                                                $ns = \App\Models\Netsuite::find($bb_out->netsuite_id);
                                                @endphp
                                                @if($ns)
                                                @include('admin.pages.log.netsuite_one', ($netsuite = $ns))
                                                @endif

                                                @endforeach
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@stop

@section('footer')

@endsection
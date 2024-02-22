@extends('admin.layout.template')

@section('title', 'Penyiapan')

@section('content')
<div class="row mb-4">
    <div class="col py-1">
        <a href="{{ route('sync.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-8 py-1 text-center">
        <b class="text-uppercase">TRACING RETUR</b>
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
        <form method="get" action="{{url('admin/wo/retur-list')}}">
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" value="{{ Request::get('tanggal') ?? date('Y-m-d') }}" class="form-control mb-2">
            <button type="submit" class="btn btn-blue">Filter</button>
        </form>
    </div>
    <div class="card-body">

        @php
        $netsuite = [];
        @endphp
        @foreach ($retur as $r)
        @php
        $data = \App\Models\Order::where('netsuite_internal_id', $r->id_so)->first();


        $netsuite = App\Models\Netsuite::where('tabel', 'retur')
        ->where('tabel_id', $r->id)
        ->orderBy('id', 'DESC')
        ->get();
        @endphp

        @if ($data && $r->id_so != '')
        <div class="card mb-2">
            <div class="card-body">

                <div class="row">
                    <div class="col">
                        <div class="small">Tanggal Retur</div>
                        {{ $r->tanggal_retur }}
                    </div>

                    <div class="col">
                        <div class="small">Tanggal Input</div>
                        {{ $r->created_at }}
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <div class="small">Nama Customer</div>
                            {{ $data->nama ?? '' }}
                        </div>
                    </div>
                    <div class="col">
                        <div class="small">Doc Number</div>
                        <span class="status status-success mb-1">{{ $data->no_so }}</span>
                        <span class="status status-warning">{{ $data->no_do }}</span>
                    </div>
                    <div class="col">
                        <div class="small">No RA</div>
                        @php
                        $ns =
                        \App\Models\Netsuite::where('tabel_id',$r->id)->where('label','receipt_return')->where('tabel',
                        'retur')->first();
                        if($ns){

                        try {
                        //code...
                        $resp = json_decode($ns->response, TRUE);
                        echo "<span class='status status-info'>".$resp[0]['message']."</span>";
                        } catch (\Throwable $th) {
                        //throw $th;
                        // echo $th->getMessage();
                        }
                        }
                        @endphp
                        @if ($ns)
                        @if (!empty($ns->failed) && $ns->document_no =="")
                        <div class="status status-danger">
                            @php
                            //code...
                            $resp = json_decode($ns->failed);
                            @endphp

                            RA Gagal : {{ $resp[0]->message->message ?? '' }}
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
                <div class="table-responsive">

                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th width=10px>No</th>
                                <th>Nama Item</th>
                                <th>Tujuan</th>
                                <th>Penanganan</th>
                                <th>Retur Qty</th>
                                <th>Retur Berat</th>
                                <th>Alasan</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Sopir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $total = 0;
                            $berat = 0;
                            $rtr = \App\Models\ReturItem::where('retur_id', $r->id)->get();
                            @endphp
                            @foreach ($rtr as $i => $row)
                            {{-- @foreach ($data->to_itemretur as $i => $row) --}}
                            @php
                            $total += $row->qty;
                            $berat += $row->berat;
                            // $retur_item = \App\Models\ReturItem::where('orderitem_id', $row->id)->first();
                            @endphp
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $row->to_item->nama ?? '' }}</td>
                                <td>{{ $row->unit }}</td>
                                <td>{{ $row->penanganan }}</td>
                                <td>{{ $row->qty }}</td>
                                <td>{{ $row->berat }}</td>
                                <td>{{ $row->catatan }}</td>
                                <td>{{ $row->kategori }}</td>
                                <td>{{ $row->satuan }}</td>
                                <td>{{ $row->todriver->nama ?? '' }}</td>
                                <th>
                                    @if ($row->status == 1)
                                    <span class="status status-danger">Belum Selesai</span>
                                    @else
                                    <span class="status status-success">Selesai</span>
                                    @endif
                                </th>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                @if($r->status=="3")
                <div class="status status-danger">RETUR TELAH DIHAPUS || {{$row->updated_at}}</div>
                @else
                @endif

            </div>
        </div>
        @else


        <div class="card mb-2">
            <div class="card-body">

                @php
                $data = \App\Models\Retur::find($r->id);
                @endphp

                <div class="row">
                    <div class="col">
                        <div class="small">Tanggal Retur</div>
                        {{ $r->tanggal_retur }}
                        <br>
                        @if($data->status == '4' || $data->status == '5')
                        <span class="status status-info mt-3">NON INTEGRASI</span>
                        @endif
                    </div>

                    <div class="col">
                        <div class="small">Tanggal Input</div>
                        {{ $data->created_at }}
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <div class="small">Nama Customer</div>
                            <td>{{ $data->to_customer->nama ?? '' }}</td>
                        </div>
                    </div>
                    <div class="col">
                        <div class="small">No SO</div>
                        <span class="status status-danger">NON SO</span>
                    </div>
                    <div class="col">
                        <div class="small">NO RA</div>
                        @php
                        $ns = \App\Models\Netsuite::where('tabel_id',$r->id)->where('label','receipt_return')->where('tabel', 'retur')->first();
                        if($ns){

                        try {
                        //code...
                        $resp = json_decode($ns->response, TRUE);
                        echo "<span class='status status-info'>".$resp[0]['message']."</span>";
                        } catch (\Throwable $th) {
                        //throw $th;
                        // echo $th->getMessage();
                        }
                        }

                        @endphp
                        @if ($ns)
                        @if (!empty($ns->failed) && $ns->document_no =="")
                        <div class="status status-danger">
                            @php
                            //code...
                            $resp = json_decode($ns->failed);
                            @endphp

                            RA Gagal : {{ $resp[0]->message->message ?? '' }}
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">

                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th width=10px>No</th>
                                <th>Customer</th>
                                <th>Tujuan</th>
                                <th>Penanganan</th>
                                <th>Retur Qty</th>
                                <th>Retur Berat</th>
                                <th>Alasan</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Sopir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($data->to_itemretur as $i => $row)
                            @php
                            $retur_item = \App\Models\ReturItem::where('orderitem_id', $row->id)->first();
                            @endphp
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $row->to_item->nama ?? '' }}</td>
                                <td>{{ $row->unit ?? '' }}</td>
                                <td>{{ $row->penanganan ?? '' }}</td>
                                <td>{{ $row->qty ?? '' }}</td>
                                <td>{{ $row->berat ?? '' }}</td>
                                <td>{{ $row->catatan }}</td>
                                <td>{{ $row->kategori }}</td>
                                <td>{{ $row->satuan }}</td>
                                <td>{{ $row->todriver->nama ?? '' }}</td>
                                <th>
                                    @if ($row->status == 1)
                                    <span class="status status-danger">Belum Selesai</span>
                                    @else
                                    <span class="status status-success">Selesai</span>
                                    @endif
                                </th>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

                @if($r->status=="3")
                <div class="status status-danger">RETUR TELAH DIHAPUS || {{$row->updated_at}}</div>
                @else
                <form action="{{ route('retur.destroy') }}" method="post">
                    @csrf @method('delete') <input type="hidden" name="id" value="{{ $r->id }}">
                    <button type="submit" class="btn btn-danger float-right">Batal</button>
                </form>
                <a href="{{ url('admin/retur/detail', $r->id) }}" class="btn btn-blue">Detail</a>
                @endif
            </div>
        </div>
    </div>

    @endif

    <table class="table default-table">
        <tbody>

            @foreach ($netsuite as $i => $n)
            @include('admin.pages.log.netsuite_one', ($netsuite = $n))
            @endforeach
        </tbody>
    </table>

    @endforeach



    </div>
</section>


@stop

@section('footer')

@endsection
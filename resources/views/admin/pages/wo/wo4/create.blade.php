@extends('admin.layout.template')

@section('title', 'Penyiapan')

@section('content')
<div class="row mb-4">
    <div class="col py-1">
        <a href="{{ route('sync.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-8 py-1 text-center">
        <b class="text-uppercase">TRACING WO-4</b>
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
        <form method="get" action="{{url('admin/wo/wo-4-list')}}">
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" value="{{ Request::get('tanggal') ?? date('Y-m-d') }}" class="form-control mb-2">
            <button type="submit" class="btn btn-blue">Filter</button>
            <a href="{{ route('wo.wo_4_list', ['key'=>'unduh_wo4'] ) }}&tanggal={{$tanggal}}"
                class="btn btn-outline-warning"><i class="fa fa-download"></i>Unduh</a>
        </form>
    </div>
    <div class="">
        <div class="table-responsive card-body">
            <table width="100%" class="table default-table" id="warehouseRequestThawing">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($thawing as $i => $row)
                    <tr class="{{ $row->deleted_at ? 'table-danger' : ($row->edited > 0 ? 'table-warning' : '') }}">
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->id }}</td>
                        <td>{{ $row->tanggal_request }}</td>
                        <td>
                            @foreach (json_decode($row->item) as $i => $item)
                            <div class="border-bottom p-1">
                                {{ ++$i }}. {{ App\Models\Item::find($item->item)->nama }}
                                <span class="status status-success">{{ number_format($item->qty) }} Pcs</span>
                                <span class="status status-info">{{ number_format($item->berat, 2) }} kg</span>
                                <span class="status status-warning">{{$item->keterangan ?? ''}}</span>

                            </div>
                            @endforeach
                        </td>
                        <td>
                            @if ($row->deleted_at)
                            <span class="status status-danger">VOID</span>
                            @else
                            @if (COUNT($row->thawing_list) < 1) <button class="btn btn-sm btn-warning"
                                data-toggle="modal" data-target="#editRequest{{ $row->id }}">Edit</button>
                                <button class="btn btn-sm btn-danger batal_thawing"
                                    data-id="{{ $row->id }}">Batal</button>
                                @endif
                                @endif
                        </td>
                    </tr>

                    <tr>
                        <td colspan="5">


                            @foreach ($row->thawing_list as $list)
                            <div class="border-bottom p-1">
                                <div>TW-{{$list->id}}. {{ $list->gudang->nama }}</div>
                                <span class="p-1 status status-success">{{ number_format($list->qty) }} pcs</span> <span
                                    class="p-1 status status-info">{{ number_format($list->berat, 2) }} kg</span>
                            </div>

                            <table>
                                <tbody>
                                    @php
                                    $ns = \App\Models\Netsuite::where('document_code', 'TW-'.$list->id)->get();
                                    @endphp
                                    @foreach ($ns as $i => $n)
                                    @include('admin.pages.log.netsuite_one', ($netsuite = $n))
                                    @endforeach
                                </tbody>
                            </table>

                            @endforeach
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@stop

@section('footer')

@endsection
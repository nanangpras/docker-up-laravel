@extends('admin.layout.template')

@section('title', 'Penyiapan')

@section('content')
<div class="row mb-4">
    <div class="col py-1">
        <a href="{{ route('sync.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-8 py-1 text-center">
        <b class="text-uppercase">TRACING WO 3</b>
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
        <form method="get" action="{{url('admin/wo/wo-3-list')}}">
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" value="{{ Request::get('tanggal') ?? date('Y-m-d') }}" class="form-control mb-2">
            <button type="submit" class="btn btn-blue">Filter</button>
            <a href="{{ route('wo.wo_3_list', ['key'=>'unduh_wo3'] ) }}&tanggal={{$tanggal}}"
                class="btn btn-outline-warning"><i class="fa fa-download"></i>Unduh</a>
        </form>
    </div>
    <div class="row">
        <div class="table-responsive card-body">
            <table class="table default-table">
                <tbody>
                    @foreach ($abf as $i => $row)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->item_name }}</td>
                        <td>{{ date('d/m/Y', strtotime($row->tanggal_masuk)) }}</td>
                        <td>{{ $row->asal }}</td>
                        <td>{{ $row->qty_awal ?: '0' }}</td>
                        <td>{{ $row->berat_awal ?: '0' }}</td>
                        <td class="text-center">
                            @if ($row->status == 1)
                            <a class="btn btn-primary btn-sm" href="{{ route('abf.timbang', $row->id) }}">Detail</a>
                            @else
                            <button type="button" class="btn btn-success btn-sm  togudang" disabled
                                data-kode="{{ $row->id }}">
                                Selesai
                            </button>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <table>
                                <tbody>
                                    @foreach ($row->hasil_timbang_selesai as $i => $row2)
                                    <tr>
                                        <td>{{ $row2->id }}</td>
                                        <td><a href="{{ route('abf.timbang', $row2->gudangabf->id ?? '0') }}">{{ $row2->gudangabf->id ?? "#" }} </a> </td>
                                        <td>{{ $row2->productitems->nama ?? ""}}</td>
                                        <td>{{ $row2->productgudang->code ?? "" }}</td>
                                        <td>{{ $row2->sub_item ?? "" }}</td>
                                        <td>{{ $row2->packaging }}</td>
                                        <td>{{ App\Models\Item::item_sku($row2->karung)->nama ?? "#" }} || {{ $row2->karung_qty }}</td>
                                        <td>{{ $row2->karung_qty }}</td>
                                        <td>{{ date('d/m/y', strtotime($row2->production_date)) }}</td>
                                        <td>{{ $row2->tanggal_kemasan ? date('d/m/y', strtotime($row2->tanggal_kemasan))
                                            : '###' }}</td>
                                        <td>{{ $row2->production_code }}</td>
                                        <td>{{ number_format($row2->qty_awal) }}</td>
                                        <td>{{ number_format($row2->berat_awal, 2) }}</td>
                                        <td>{{ number_format($row2->palete) }}</td>
                                        <td>{{ number_format($row2->expired) }} Bulan</td>
                                        <td>{{ $row2->stock_type }}</td>
                                        <td></td>
                                    </tr>
                                    @endforeach
                                    @php
                                    $ns = \App\Models\Netsuite::where('document_code', 'like',
                                    '%'.$row->id.'%')->where('label', 'like', '%abf%')->get();
                                    @endphp
                                    @foreach ($ns as $i => $n)
                                    @include('admin.pages.log.netsuite_one', ($netsuite = $n))
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
</section>

@stop

@section('footer')

@endsection
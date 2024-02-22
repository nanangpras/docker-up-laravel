@extends('admin.layout.template')

@section('title', 'Penyiapan')

@section('content')
<div class="row mb-4">
    <div class="col py-1">
        <a href="{{ route('sync.index') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-8 py-1 text-center">
        <b class="text-uppercase">BUAT WO {{$regu}}</b>
    </div>
    <div class="col"></div>
</div>

<form action="{{ route('wo.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <section class="panel">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table default-table" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-info" colspan="4">Bahan Baku</th>
                                </tr>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Ekor/Pcs/Pack</th>
                                    <th>Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $ekor = 0;
                                $berat = 0;
                                @endphp
                                @foreach ($bahan_baku as $i => $row)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>
                                        {{ App\Models\Item::find($row->item_id)->nama }}
                                        @if ($row->type == 'hasil-produksi')
                                            <span class="status status-info">FG</span>
                                        @elseif($row->type == 'bahan-baku')
                                            <span class="status status-danger">BB</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($row->qty) }}</td>
                                    <td>{{ number_format($row->berat, 2) }} Kg</td>
                                </tr>
                                @php
                                $ekor = $ekor+$row->qty;
                                $berat = $berat+$row->berat;
                                @endphp
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td>Total</td>
                                    <td>{{$ekor}}</td>
                                    <td>{{ number_format($berat, 2) }} Kg</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table default-table" width="100%">
                            <thead>
                                <tr>
                                    <th class="text-info" colspan="4">Hasil Produksi</th>
                                </tr>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Ekor/Pcs/Pack</th>
                                    <th>Berat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $ekor = 0;
                                $berat = 0;

                                @endphp
                                @foreach ($produksi as $i => $row)

                                @php
                                $item_cat = \App\Models\Item::find($row->item_id);
                                $bom_item = \App\Models\BomItem::where('sku', $item_cat->sku)->where('bom_id',
                                $bom->id)->first();

                                $item_cat = \App\Models\Item::find($row->item_id);

                                $type = (($item_cat->category_id == 4) OR ($item_cat->category_id == 6) OR
                                ($item_cat->category_id == 10) OR ($item_cat->category_id == 16)) ? "By Product" : "Finished Goods";
                                if($bom_item){
                                $type = $bom_item->kategori;
                                }
                                @endphp
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ App\Models\Item::find($row->item_id)->nama }}

                                        @if ($type == 'Finished Goods')
                                            <span class="status status-info">FG</span>
                                        @else
                                            <span class="status status-warning">{{ $type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($row->qty) }}</td>
                                    <td>{{ number_format($row->berat, 2) }} Kg</td>
                                </tr>
                                @php
                                $ekor = $ekor+$row->qty;
                                $berat = $berat+$row->berat;
                                @endphp
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td>Total</td>
                                    <td>{{$ekor}}</td>
                                    <td>{{ number_format($berat, 2) }} Kg</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col pr-1">
                                <input type="text" name="bom_name" class="form-control mb-2" id="bom-form"
                                    value="{{ $bom->bom_name }}" readonly>
                            </div>
                            <div class="col pl-1">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif name="tanggal" class="form-control tanggal mb-2"
                                    id="tanggal-form" value="{{ $tanggal }}" readonly>
                            </div>
                        </div>
                        <input type="hidden" name="regu" class="form-control" id="regu-form" value="{{ $regu }}"
                            autocomplete="off">
                        <button type="submit" class="btn btn-blue form-control" name="type" value="save">Simpan
                            WO</button>
                        <br>
                        <button type="submit" class="btn btn-green form-control" name="type" value="export">Export
                            WO</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>

<style>
    .hidden-form {
        display: none;
    }
</style>

@stop

@section('footer')

@endsection
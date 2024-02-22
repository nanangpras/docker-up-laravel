@extends('admin.layout.template')

@section('title', 'Timbangan Chiller')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>TRACING GUDANG</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <b>Data Item</b>
            <div class="table-responsive mb-4">
                <table width="100%" id="kategori" class="table default-table">
                    <thead>
                        <tr>
                            <th rowspan="2">#</th>
                            <th rowspan="2">Gudang</th>
                            <th rowspan="2">Konsumen / Sub Item</th>
                            <th rowspan="2">Item</th>
                            <th colspan="2" class="text-center">Tanggal</th>
                            <th colspan="2" class="text-center">Kemasan</th>
                            <th rowspan="2">ABF</th>
                            <th rowspan="2">Qty</th>
                            <th rowspan="2">Berat</th>
                            {{-- <th rowspan="2">Qty</th>
                            <th rowspan="2">Berat</th> --}}
                            <th rowspan="2">Pallete</th>
                            <th rowspan="2">Expired</th>
                            <th rowspan="2">Stock</th>
                            <th rowspan="2">#</th>
                        </tr>
                        <tr>
                            <th>Produksi</th>
                            <th>Kemasan</th>
                            <th>Packaging</th>
                            <th>SubPack</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $row = $product_gudang;
                        @endphp
                        <tr>
                            <td>{{ $product_gudang->id ?? "" }}</td>
                            <td>{{ $product_gudang->productgudang->code ?? "" }}</td>
                            <td>{{ $product_gudang->konsumen->nama ?? "" }}<br>@if ($product_gudang->sub_item) Keterangan : {{ $product_gudang->sub_item }} @endif
                                {{ $product_gudang->no_so ?? "" }}
                            </td>
                            <td>
                                <div>{{ $product_gudang->productitems->nama ?? ""}}</div>
                                @if ($product_gudang->selonjor)
                                <div class="font-weight-bold text-danger">SELONJOR</div>
                                @endif
                                @if ($product_gudang->barang_titipan)
                                <div class="font-weight-bold text-primary">BARANG TITIPAN</div>
                                @endif
                            </td>
                            <td>{{ date('Y-m-d', strtotime($product_gudang->production_date)) }}</td>
                            @if ($product_gudang->status == 2)
                            <td>{{ date('Y-m-d', strtotime($product_gudang->tanggal_kemasan)) }}</td>
                            @elseif ($product_gudang->status == 4)
                            <td>{{ date('Y-m-d', strtotime(App\Models\Product_gudang::find($product_gudang->gudang_id_keluar)->tanggal_kemasan ?? $product_gudang->production_date)) }}</td>
                            @endif
                            <td>{{ $product_gudang->packaging }}</td>
                            <td>{{ $product_gudang->subpack }}</td>
                            <td>{{ $product_gudang->asal_abf }}</td>
                            <td>{{ number_format($product_gudang->qty_awal) }}</td>
                            <td>{{ number_format($product_gudang->berat_awal, 2) }}</td>
                            {{-- <td>{{ number_format($product_gudang->qty) }}</td>
                            <td>{{ number_format($product_gudang->berat, 2) }}</td> --}}
                            <td>{{ number_format($product_gudang->palete) }}</td>
                            <td>{{ number_format($product_gudang->expired) }} Bulan</td>
                            <td>{{ $product_gudang->stock_type }}</td>
                            <td><a href="{{route('warehouse.tracing', $product_gudang->id)}}" class="btn btn-sm btn-blue" target="_blank">Detail</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr>
            @if ($product_gudang->table_name != 'open_balance')
                @if($product_gudang->gudangabf)
                <b>Data Asal ABF</b>
                <div class="table-responsive mb-4">
                    <table width="100%" id="kategori" class="table default-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ITEM</th>
                                <th>TANGGAL</th>
                                <th>QTY</th>
                                <th>BERAT</th>
                                {{-- <th>QTY SISA</th>
                                <th>BERAT SISA</th> --}}
                                <th>STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <a href="{{url('admin/abf/timbang/'.$product_gudang->gudangabf->id)}}">
                                    {{ $product_gudang->gudangabf->id }}
                                    </a>
                                </td>
                                <td><a href="" target="_blank"></a>{{$product_gudang->nama}}</td>
                                <td>{{ date('Y-m-d H:i:s', strtotime($product_gudang->gudangabf->created_at)) }}</td>
                                <td>{{ number_format($product_gudang->gudangabf->qty_awal) }}</td>
                                <td>{{ number_format($product_gudang->gudangabf->berat_awal, 2) }} Kg</td>
                                {{-- <td>{{ number_format($product_gudang->gudangabf->qty) }}</td>
                                <td>{{ number_format($product_gudang->gudangabf->berat, 2) }} Kg</td> --}}
                                <td>
                                    <a href="{{url('admin/abf/timbang/'.$product_gudang->gudangabf->id)}}" class="btn btn-blue btn-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @endif
            @endif

            <hr>
            @if($product_gudang->no_so == "")
            <b>GUDANG KELUAR</b>
            <div class="table-responsive mb-4">
                <table width="100%" id="kategori" class="table default-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ITEM</th>
                            <th>TANGGAL</th>
                            <th>QTY</th>
                            <th>BERAT</th>
                            {{-- <th>QTY SISA</th>
                            <th>BERAT SISA</th> --}}
                            <th>NO SO</th>
                            <th>TYPE</th>
                            <th>STATUS</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product_gudang->gudang_keluar as $tw)
                        <tr>
                            <td>{{ $tw->id }}</td>
                            <td><a href="" target="_blank"></a>{{$tw->productitems->nama}}<br>{{$tw->sub_item}}</td>
                            <td>{{ date('Y-m-d H:i:s', strtotime($tw->created_at)) }}</td>
                            <td>{{ number_format($tw->qty_awal) }}</td>
                            <td>{{ number_format($tw->berat_awal, 2) }} Kg</td>
                            {{-- <td>{{ number_format($tw->qty) }}</td>
                            <td>{{ number_format($tw->berat, 2) }} Kg</td> --}}
                            <td>{{ $tw->no_so }}</td>
                            <td>{{ $tw->type }}</td>
                            <td>{!!$tw->status_gudang!!}</td>
                            <td><a href="{{route('warehouse.tracing', $tw->id)}}" class="btn btn-sm btn-blue" target="_blank">Detail</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <hr>
            @else
            <b>GUDANG ASAL</b>
            <div class="table-responsive mb-4">
                <table width="100%" id="kategori" class="table default-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ITEM</th>
                            <th>TANGGAL</th>
                            <th>QTY</th>
                            <th>BERAT</th>
                            {{-- <th>QTY SISA</th>
                            <th>BERAT SISA</th> --}}
                            <th>NO SO</th>
                            <th>TYPE</th>
                            <th>STATUS</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $gudang_asal = App\Models\Product_gudang::find($row->gudang_id_keluar);
                        @endphp
                        @if($gudang_asal)
                        <tr>
                            <td>{{ $gudang_asal->id }}</td>
                            <td><a href="" target="_blank"></a>{{$gudang_asal->productitems->nama}}<br>{{$gudang_asal->sub_item}}</td>
                            <td>{{ date('Y-m-d H:i:s', strtotime($gudang_asal->created_at)) }}</td>
                            <td>{{ number_format($gudang_asal->qty_awal) }}</td>
                            <td>{{ number_format($gudang_asal->berat_awal, 2) }} Kg</td>
                            {{-- <td>{{ number_format($gudang_asal->qty) }}</td>
                            <td>{{ number_format($gudang_asal->berat, 2) }} Kg</td> --}}
                            <td>{{ $gudang_asal->no_so }}</td>
                            <td>{{ $gudang_asal->type }}</td>
                            <td>{!!$gudang_asal->status_gudang!!}</td>
                            <td><a href="{{route('warehouse.tracing', $gudang_asal->id)}}" class="btn btn-sm btn-blue" target="_blank">Detail</a></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <hr>
            @endif
        </div>
    </section>

@stop

<script>
    //function that display value
    function dis(val) {
        document.getElementById("result").value += val
    }

    //function that evaluates the digit and return result
    function solve() {
        let x = document.getElementById("result").value
        let y = eval(x)
        document.getElementById("result").value = y
    }

    //function that clear the display
    function clr() {
        document.getElementById("result").value = ""
    }

    function clrberat() {
        document.getElementById("berat").value = ""
    }
</script>

@extends('admin.layout.template')

@section('title', 'Tracing Data ABF')

@section('content')

<style>
    .px-2{
        padding-top: 5px;
        padding-bottom: 5px;
        line-height: 0.9 !important;
    }
    .table thead th{
        vertical-align: center !important;
    }
</style>
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('abf.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>TRACING DATA ABF</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            @php 
                $searchtrace = \App\Models\Abf::where('parent_abf', $product_gudang->table_id)->get();
                $countdata   = $searchtrace->count();
            @endphp
            @if($countdata == 0)
                <b>Data Asli</b>
                <br />
                <br />
            @else
                <b>Data Gabungan</b>
                <br />
                <br />
            @endif
            <div class="table-responsive mb-4">
                <table class="table default-table table-trace" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center vertical-align-center">#</th>
                            <th rowspan="2" class="text-center vertical-align-center">ID ABF</th>
                            <th rowspan="2" class="text-center vertical-align-center">Item</th>
                            <th rowspan="2" class="text-center vertical-align-center">Gudang</th>
                            <th rowspan="2" class="text-center vertical-align-center">SubItem</th>
                            <th rowspan="2" class="text-center vertical-align-center">Packaging</th>
                            <th class="text-center" colspan="2">Karung</th>
                            <th class="text-center" colspan="3">Tanggal</th>
                            <th rowspan="2" class="text-center vertical-align-center">Qty</th>
                            <th rowspan="2" class="text-center vertical-align-center">Berat</th>
                            <th rowspan="2" class="text-center vertical-align-center">Pallete</th>
                            <th rowspan="2" class="text-center vertical-align-center">Expired</th>
                            <th rowspan="2" class="text-center vertical-align-center">Stock</th>
                            <th rowspan="2">Kode</th>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <th>Jumlah</th>
                            <th>Isi</th>
                            <th>Produksi</th>
                            <th>Kemasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#</td>
                            <td><a href="{{ route('abf.timbang', $product_gudang->gudangabf->id ?? '0') }}">{{ $product_gudang->gudangabf->id ?? "#" }} </a> </td>
                            <td>{{ $product_gudang->productitems->nama ?? ""}}</td>
                            <td>{{ $product_gudang->productgudang->code ?? "" }}</td>
                            <td>{{ $product_gudang->sub_item ?? "" }}</td>
                            <td>{{ $product_gudang->packaging }}</td>
                            <td>{{ App\Models\Item::item_sku($product_gudang->karung)->nama ?? "#" }} || {{ $product_gudang->karung_qty }}</td>
                            <td>{{ $product_gudang->karung_qty }}</td>
                            <td>{{ $product_gudang->karung_isi }}</td>
                            <td>{{ date('d/m/y', strtotime($product_gudang->production_date)) }}</td>
                            <td>{{ $product_gudang->tanggal_kemasan ? date('d/m/y', strtotime($product_gudang->tanggal_kemasan)) : '###' }}</td>
                            <td>{{ $product_gudang->production_code }}</td>
                            <td>{{ number_format($product_gudang->qty) }}</td>
                            <td>{{ number_format($product_gudang->berat, 2) }}</td>
                            <td>{{ number_format($product_gudang->palete) }}</td>
                            <td>{{ number_format($product_gudang->expired) }} Bulan</td>
                            <td>{{ $product_gudang->stock_type }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if($countdata > 0)
                <hr>
                <b>Data Asal ABF</b>
                <br />
                <br />
                <div class="table-responsive mb-4">
                    <table width="100%" id="kategori" class="table default-table">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>ID ABF</th>
                                <th>TANGGAL MASUK</th>
                                <th>TANGGAL DIGABUNGKAN</th>
                                <th>ITEM</th>
                                <th>PACKAGING</th>
                                <th>JUMLAH</th>
                                <th>BERAT</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                            @foreach($searchtrace as $dataabf)
                            <tr>
                                <td>{{ $loop->iteration}}</td>
                                <td>#{{ $dataabf->id}}</td>
                                <td>{{ $dataabf->tanggal_masuk}}</td>
                                <td>{{ $dataabf->tanggal_keluar}}</td>
                                <td>{{ $dataabf->item_name}}</td>
                                <td>{{ $dataabf->packaging}}</td>
                                <td>{{ $dataabf->gabung_qty}}</td>
                                <td>{{ $dataabf->gabung_berat}}</td>
                            </tr>
                            @endforeach
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

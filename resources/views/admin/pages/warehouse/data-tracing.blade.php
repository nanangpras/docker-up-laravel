@extends('admin.layout.template')

@section('title', 'Tracing Gudang')

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
        <div class="card-header font-weight-bold text-uppercase">Data Stock Awal</div>
        <div class="card-body"> 
            <div class="table-responsive mb-4">
                <table width="100%" id="kategori" class="table default-table">
                    <thead>
                        <tr>
                            <th rowspan="2">ID</th>
                            <th rowspan="2">Gudang</th>
                            <th rowspan="2">Status</th>
                            <th rowspan="2">Konsumen / Sub Item</th>
                            <th rowspan="2">Item</th>
                            <th colspan="2" class="text-center">Tanggal</th>
                            <th colspan="2" class="text-center">Kemasan</th>
                            <th rowspan="2">ABF</th>
                            <th rowspan="2">Pallete</th>
                            <th rowspan="2">Expired</th>
                            <th rowspan="2">Stock</th>
                            <th rowspan="2">Qty</th>
                            <th rowspan="2">Berat</th>
                            <!-- <th rowspan="2">#</th> -->
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
                            $qty    = 0;
                            $berat  = 0;
                            $qty    += $product_gudang->qty;
                            $berat  += $product_gudang->berat;
                            $status = $product_gudang->status == 2 ? 'Masuk' : ($product_gudang->status == 4 ? 'Keluar' : null);
                            if($product_gudang->table_name != 'open_balance'){
                                $url = $product_gudang->gudangabf->id;
                            } else {
                                $url = "";
                            }
                        @endphp
                        <tr>
                            <td>
                                @if($product_gudang->table_name != 'open_balance')
                                    <a href="{{url('admin/abf/timbang/'.$url)}}">
                                        {{ $product_gudang->id ?? "" }}
                                    </a>
                                @else
                                        {{ $product_gudang->id ?? "" }}
                                @endif
                            </td>
                            <td>{{ $product_gudang->productgudang->code ?? "" }}</td>
                            <td>{{ $status }}</td>
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
                            <td>{{ number_format($product_gudang->palete) }}</td>
                            <td>{{ number_format($product_gudang->expired) }} Bulan</td>
                            <td>{{ $product_gudang->stock_type }}</td>
                            <td>{{ number_format($product_gudang->qty_awal) }}</td>
                            <td>{{ number_format($product_gudang->berat_awal, 2) }}</td>
                            <!-- <td><a href="{{route('warehouse.tracing', $product_gudang->id)}}" class="btn btn-sm btn-blue" target="_blank">Detail</a></td> -->
                        </tr>
                    </tbody>

                </table>
                @if (App\Models\User::setIjin(33))
                    <button class="btn btn-outline-info" data-toggle="modal" data-target="#edit">Adjustment Qty/Berat</button>
                @endif

                <!-- Modal adjustmen -->
                <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="editLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="post" enctype="multipart/form-data" action="{{route('product.adjust',['id' => $product_gudang->id]) }}">
                        @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLabel">Edit Inbound</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <input type="hidden" name="chiller_id" value="{{ $product_gudang->id }}" class="form-control" id="qty">
                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Qty Awal
                                        <input type="number" value="{{ $product_gudang->qty_awal }}" class="form-control" id="qty" disabled>
                                    </div>
                                </div>
                                <div class="col pl-1">
                                    <div class="form-group">
                                        Berat Awal
                                        <input type="number" value="{{ $product_gudang->berat_awal }}" class="form-control" id="berat" step="0.01" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Qty Akhir
                                        <input type="number" name="ubahQty" value="{{ $product_gudang->qty }}" class="form-control" id="qty">
                                    </div>
                                </div>
                                <div class="col pl-1">
                                    <div class="form-group">
                                        Berat Akhir
                                        <input type="number" name="ubahBerat" value="{{ $product_gudang->berat }}" class="form-control" id="berat" step="0.01">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" data-id="" class="btn btn-primary">Save</button>
                        </div>
                    </div>

                    </form>
                </div>
            </div>

            </div>
        </div>
    </section>

    <!-- <section class="panel">
        <div class="card-header font-weight-bold text-uppercase">Riwayat Ambil Bahan Baku</div>
        <div class="card-body">
            <div class="table-responsive mb-4">
                <table width="100%" id="kategori" class="table default-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Regu</th>
                            <th>Qty</th>
                            <th>Berat</th>
                            <th>Waktu Ambil</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </section> -->

    <section class="panel" id="alokasi_order">
        <div class="card-header font-weight-bold text-uppercase">Alokasi Order</div>
            <div class="card-body">
                <div class="table-responsive mb-4">
                    <table width="100%" id="alokasi_order"" class="table default-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NO SO</th>
                                <th>TANGGAL</th>
                                <th>JAM</th>
                                <th>NAMA ITEM</th>
                                <th>TYPE</th>
                                <th>STATUS</th>
                                <th>QTY</th>
                                <th>BERAT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $qty    = 0;
                                $berat  = 0;    
                            @endphp
                            @foreach($alokasi_order as $ao)
                                    @php
                                        $qty    += $ao->qty_awal;
                                        $berat  += $ao->berat_awal;
                                    @endphp
                            @endforeach
                            @foreach($alokasi_order as $gk)
                                    @php
                                        $status = $gk->status == 2 ? 'Masuk' : ($gk->status == 4 ? 'Keluar' : null);
                                        $url_so = \App\Models\Order::where('no_so', $gk->no_so)->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $gk->id }}</td>
                                        <td>
                                            @if($url_so)
                                                <a href="{{route('editso.index', ['id' => $url_so->id]) }}" target="_blank">{{ $gk->no_so }}</a>
                                            @else 
                                                {{ $gk->no_so }}
                                            @endif
                                        </td>
                                        <td>{{ date('d-M-Y', strtotime($gk->created_at)) }}</td>
                                        <td>{{ date('h:i', strtotime($gk->created_at)) }}</td>
                                        <td>{{ $gk->productitems->nama }}</td>
                                        <td>{{ $gk->type }}</td>
                                        @if ($product_gudang->table_name != 'open_balance')
                                        <td>{{ $status }} - {!! $gk->gudangabf2->getStatusAbfAttribute() !!}</td>

                                        @else
                                        <td>{!! $gk->getStatusKeluarAttribute() !!}</td>

                                        @endif
                                        <td>{{ $gk->qty_awal }}</td>
                                        <td>{{ $gk->berat_awal }}</td>
                                    </tr>
                            @endforeach
                            <tr>
                                <th colspan="7">Total</th>
                                <th>{{ number_format($qty) }}</th>
                                <th>{{ number_format($berat, 1) }} Kg</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    </section>
    
    <section class="panel">
        <div class="card-header font-weight-bold text-uppercase">REQUEST THAWING</div>
        <div class="card-body">
            <div class="table-responsive mb-4">
                <table width="100%" id="request_thawing" class="table default-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Item</th>
                            <th>Waktu Ambil</th>
                            <th>Tanggal Ambil</th>
                            <th>Status</th>
                            <th>Qty</th>
                            <th>Berat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $qty    = 0;
                            $berat  = 0;
                        @endphp
                        @foreach($request_thawing as $rt)
                                @php
                                    $qty    += $rt->qty_awal;
                                    $berat  += $rt->berat_awal;
                                    $status = $rt->status == 2 ? 'Masuk' : ($rt->status == 4 ? 'Keluar' : null);
                                @endphp
                                <tr>
                                    <td><a href="#" class="redirect-summary" data-id="{{ $rt->request_thawing }}" target="_blank">{{ $rt->id }}</a></td>
                                    <td>{{ $rt->productitems->nama }}</td>
                                    <td>{{ date('h:i', strtotime($rt->created_at)) }}</td>
                                    <td>{{ date('d-M-Y', strtotime($rt->created_at)) }}</td>
                                    <td>{{ $status }} - {!! $rt->gudangabf2->getStatusAbfAttribute() !!}</td>
                                    <td>{{ $rt->qty_awal }}</td>
                                    <td>{{ $rt->berat_awal }}</td>
                                </tr>
                        @endforeach
                        <tr>
                            <th colspan="5">Total</th>
                            <th>{{ number_format($qty) }}</th>
                            <th>{{ number_format($berat, 1) }} Kg</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-header font-weight-bold text-uppercase">GRADING ULANG</div>
        <div class="card-body">
            <div class="table-responsive mb-4">
                <table width="100%" id="kategori" class="table default-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Item</th>
                            <th>Jam Grading Ulang</th>
                            <th>Tanggal Grading Ulang</th>
                            <th>Status</th>
                            <th>Qty</th>
                            <th>Berat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $qty    = 0;
                            $berat  = 0;
                        @endphp
                            @foreach($product_gudang->gudang_keluar as $gu)
                                @if($gu->type == 'grading_ulang')
                                    @php
                                        $qty    += $gu->qty_awal;
                                        $berat  += $gu->berat_awal;
                                        $status = $gu->status == 2 ? 'Masuk' : ($gu->status == 4 ? 'Keluar' : null);
                                    @endphp
                                    <tr>
                                        <td><a href="#" class="redirect-gradul" data-id="{{ $gu->id }}" target="_blank">{{ $gu->id }}</a></td>
                                        <td>{{ $gu->productitems->nama }}</td>
                                        <td>{{ date('h:i', strtotime($gu->created_at)) }}</td>
                                        <td>{{ date('d-M-Y', strtotime($gu->created_at)) }}</td>
                                        @if ($product_gudang->table_name != 'open_balance')
                                        <td>{{ $status }} - {!! $gk->gudangabf2->getStatusAbfAttribute() !!}</td>

                                        @else
                                        <td>{!! $gk->getStatusKeluarAttribute() !!}</td>

                                        @endif
                                        <td>{{ $gu->qty_awal }}</td>
                                        <td>{{ $gu->berat_awal }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <th colspan="5">Total</th>
                                <th>{{ number_format($qty) }}</th>
                                <th>{{ number_format($berat, 1) }} Kg</th>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-header font-weight-bold text-uppercase">DIMUSNAHKAN</div>
        <div class="card-body">
            <div class="table-responsive mb-4">
                <table width="100%" id="musnahkanitem" class="table default-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Item</th>
                            <th>Tanggal Musnahkan</th>
                            <th>Keterangan</th>
                            <th>Gudang</th>
                            <th>Status</th>
                            <th>Qty</th>
                            <th>Berat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $qty    = 0;
                            $berat  = 0;
                        @endphp
                            @foreach($product_gudang->countMusnahkan as $mus)
                                @php
                                    $qty    += $mus->qty;
                                    $berat  += $mus->berat;
                                    $status = $mus->musnahkan->status == "1" ? 'Pending' : ($mus->musnahkan->status == "2" ? 'Selesai' : "Selesai");
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $mus->gudang->nama }}</td>
                                    <td>{{ $mus->musnahkan->tanggal }}</td>
                                    <td>{{ $mus->musnahkan->keterangan }}</td>
                                    <td>{{ $mus->warehouse->code }}</td>
                                    {{-- <td>{{ date('h:i', strtotime($mus->created_at)) }}</td> --}}
                                    {{-- <td>{{ date('d-M-Y', strtotime($mus->created_at)) }}</td> --}}
                                    <td>{{ $status }}</td>
                                    <td>{{ $mus->qty }}</td>
                                    <td>{{ $mus->berat }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="6">Total</th>
                                <th>{{ number_format($qty) }}</th>
                                <th>{{ number_format($berat, 1) }} Kg</th>
                            </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-header font-weight-bold text-uppercase">Inventory Adjustment</div>
        <div class="card-body">
            <div class="table-responsive mb-4">
                <table width="100%" id="kategori" class="table default-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Asal</th>
                            <th>Tanggal Ambil</th>
                            <th>Jam Ambil</th>
                            <th>Status</th>
                            <th>Qty</th>
                            <th>Berat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $qty    =   0 ;
                            $berat  =   0 ;
                        @endphp
                            @foreach($data as $no => $row)
                                @php
                                    $qty    +=  $row->qty ;
                                    $berat  +=  $row->berat ;
                                    $status = $row->status == 2 ? 'Masuk' : ($row->status == 4 ? 'Keluar' : null);
                                @endphp
                                <tr>
                                    <td>{{ ++$no }}</td>
                                    <td>{{$row->productgudang->code}}</td>
                                    <td>{{ date('d-M-Y', strtotime($row->created_at)) }}</td>
                                    <td>{{ date('H:i', strtotime($row->created_at)) }}</td>
                                    @if ($product_gudang->table_name != 'open_balance')
                                    <td>{{ $status }} - {!! $row->gudangabf2->getStatusAbfAttribute() !!}</td>

                                    @else
                                    <td>{!! $row->getStatusKeluarAttribute() !!}</td>

                                    @endif
                                    <td>{{ number_format($row->qty) }}</td>
                                    <td>{{ number_format($row->berat, 2) }} Kg</td>
                                </tr>
                            @endforeach
                        <tr>
                            <th colspan="5">Total</th>
                            <th>{{ number_format($qty) }}</th>
                            <th>{{ number_format($berat, 1) }} Kg</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-header font-weight-bold text-uppercase">Sisa Akhir</div>
        <div class="card-body">
            <div class="table-responsive mb-4">
                <table width="100%" id="kategori" class="table default-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Tanggal</th>
                            <th>Qty Akhir</th>
                            <th>Berat Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $product_gudang->productitems->nama }}</td>
                            <td>{{ date('d-M-Y', strtotime($product_gudang->created_at)) }}</td>
                            <td>{{ $product_gudang->qty_awal - $product_gudang->TotalQtyBahanBaku - $product_gudang->total_qty_thawing - $product_gudang->total_qty_regrading - $product_gudang->total_qty_musnahkan  }}</td>
                            <td>{{ $product_gudang->berat_awal - $product_gudang->TotalBeratBahanBaku - $product_gudang->total_berat_thawing - $product_gudang->total_berat_regrading - $product_gudang->total_berat_musnahkan }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@stop

@section('scripts')
<script>
    $(document).ready(function() {
        $(".redirect-summary").click(function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            window.open("{{ route('thawingproses.index') }}?id=" + id + "#custom-tabs-three-thawing", '_blank');
        });
        $(".redirect-gradul").click(function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            window.open("{{ route('abf.index', ['key' => 'grading-ulang']) }}&id=" + id + "#custom-tabs-sumgradul", '_blank');
        });
    });
</script>
@endsection

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

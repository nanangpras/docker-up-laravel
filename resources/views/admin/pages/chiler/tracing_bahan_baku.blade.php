@extends('admin.layout.template')

@section('title', 'Timbangan Chiller')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('chiller.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>TRACING CHILLER</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <h5>Bahan Baku Fresh</h5>
            <table width="100%" id="kategori" class="table default-table">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>TANGGAL</th>
                        <th>QTY GRAD</th>
                        <th>BERAT GRAD</th>
                        <th>QTY AWAL</th>
                        <th>BERAT AWAL</th>
                        <th>QTY USED</th>
                        <th>BERAT USED</th>
                        <th>SISA QTY</th>
                        <th>SISA BERAT</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total_qty          = 0;
                        $total_berat        = 0;
                        $total_qty_used     = 0;
                        $total_berat_used   = 0;
                    @endphp
                    @foreach($gradinggabungan as $g)
                    <tr>
                        <td><a href="{{url('admin/chiller', $g->id)}}" target="_blank">{{$g->item_name}}</a></td>
                        <td>{{$g->tanggal_produksi}}</td>
                        <td class="text-right">{{\App\Models\Grading::count_item_grading($g->item_id, $g->tanggal_produksi, 'total_item')}}</td>
                        <td class="text-right">{{\App\Models\Grading::count_item_grading($g->item_id, $g->tanggal_produksi, 'berat_item')}}</td>
                        <td class="text-right">{{$g->qty_item}}</td>
                        <td class="text-right">{{$g->berat_item}}</td>
                        

                        @php
                            $used_qty       = 0 ;
                            $used_berat     = 0 ;
                            $sisa_qty       = 0 ;
                            $sisa_berat     = 0 ;
                        @endphp
                        
                        @php
                            
                            $used_qty       = $g->ia_qty < 0 ? $g->fs_qty + $g->bb_qty + $g->abf_qty : $g->fs_qty + $g->bb_qty + $g->abf_qty + $g->ia_qty ;
                            $used_berat     = $g->fs_berat + $g->bb_berat + $g->abf_berat + $g->ia_berat ;
                        @endphp

                        @php
                            $sisa_qty       =  $g->qty_item-$used_qty ;
                            $sisa_berat     =  $g->berat_item-$used_berat ;
                        @endphp

                        <td class="text-right">{{$used_qty}}</td>
                        <td class="text-right">{{number_format($used_berat,2)}}</td>
                        
                        <td class="text-right">
                            @if($g->stock_item < 0)
                            <span class="status status-danger">{{$g->stock_item}}</span> 
                            @else 
                            <span class="status status-success">{{$g->stock_item}}</span> 
                            @endif
                        </td>
                        <td class="text-right">
                            @if($g->stock_berat < 0)
                            <span class="status status-danger">{{number_format($g->stock_berat,2)}}</span> 
                            @else
                            <span class="status status-success">{{number_format($g->stock_berat,2)}}</span> 
                            @endif
                        </td>
                    </tr>
                    @php
                        $total_qty          = $total_qty + $g->qty_item;
                        $total_berat        = $total_berat + $g->berat_item;
                        $total_qty_used     = $total_qty_used + $used_qty;
                        $total_berat_used   = $total_berat_used + $used_berat;
                    @endphp
                    @endforeach

                    <tr>
                        <td colspan="4" class="text-center"><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{$total_qty}}</strong></td>
                        <td class="text-right"><strong>{{$total_berat}}</strong></td>
                        <td class="text-right"><strong>{{$total_qty_used}}</strong></td>
                        <td class="text-right"><strong>{{number_format($total_berat_used,2)}}</strong></td>
                        <td class="text-right"><strong>{{$total_qty - $total_qty_used}}</strong></td>
                        <td class="text-right"><strong>{{number_format(($total_berat - $total_berat_used),2)}}</strong></td>
                    </tr>
                </tbody>

                <tbody>
                    @php
                        $total_qty          = 0;
                        $total_berat        = 0;
                        $total_qty_used     = 0;
                        $total_berat_used   = 0;
                    @endphp
                    @foreach($evisgabungan as $g)
                    <tr>
                        <td><a href="{{url('admin/chiller', $g->id)}}" target="_blank">{{$g->item_name}}</a></td>
                        <td>{{$g->tanggal_produksi}}</td>
                        <td class="text-right">{{\App\Models\Grading::count_item_grading($g->item_id, $g->tanggal_produksi, 'total_item')}}</td>
                        <td class="text-right">{{\App\Models\Grading::count_item_grading($g->item_id, $g->tanggal_produksi, 'berat_item')}}</td>
                        <td class="text-right">{{$g->qty_item}}</td>
                        <td class="text-right">{{number_format($g->berat_item,2)}}</td>
                        

                        @php
                            $used_qty       = 0 ;
                            $used_berat     = 0 ;
                            $sisa_qty       = 0 ;
                            $sisa_berat     = 0 ;
                        @endphp
                        
                        @php
                            $used_berat     = $g->fs_berat + $g->bb_berat + $g->abf_berat + $g->ia_berat ;
                            $used_qty       = $g->fs_qty + $g->bb_qty + $g->abf_qty + $g->ia_qty ;
                        @endphp

                        @php
                            $sisa_qty       = $g->qty_item-$used_qty ;
                            $sisa_berat     = $g->berat_item-$used_berat ;
                        @endphp

                        <td class="text-right">{{$used_qty}}</td>
                        <td class="text-right">{{number_format($used_berat,2)}}</td>
                        
                        <td class="text-right">
                            @if($g->stock_item < 0)
                            <span class="status status-danger">{{$g->stock_item}}</span> 
                            @else 
                            <span class="status status-success">{{$g->stock_item}}</span> 
                            @endif
                        </td>
                        <td class="text-right">
                            @if($g->stock_berat < 0)
                            <span class="status status-danger">{{number_format($g->stock_berat,2)}}</span> 
                            @else
                            <span class="status status-success">{{number_format($g->stock_berat,2)}}</span> 
                            @endif
                        </td>
                   </tr>
                    @php
                        $total_qty          = $total_qty + $g->qty_item;
                        $total_berat        = $total_berat + $g->berat_item;
                        $total_qty_used     = $total_qty_used + $used_qty;
                        $total_berat_used   = $total_berat_used + $used_berat;
                    @endphp
                    @endforeach

                    <tr>
                        <td colspan="4" class="text-center"><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{$total_qty}}</strong></td>
                        <td class="text-right"><strong>{{$total_berat}}</strong></td>
                        <td class="text-right"><strong>{{$total_qty_used}}</strong></td>
                        <td class="text-right"><strong>{{number_format($total_berat_used,2)}}</strong></td>
                        <td class="text-right"><strong>{{$total_qty - $total_qty_used}}</strong></td>
                        <td class="text-right"><strong>{{number_format(($total_berat - $total_berat_used),2)}}</strong></td>
                    </tr>
                </tbody>


                <tbody>
                    @php
                        $total_qty          = 0;
                        $total_berat        = 0;
                        $total_qty_used     = 0;
                        $total_berat_used   = 0;
                    @endphp
                    @foreach($stock as $g)
                    <tr>
                        <td><a href="{{url('admin/chiller', $g->id)}}" target="_blank">{{$g->item_name}}</a></td>
                        <td>{{$g->tanggal_produksi}}</td>
                        <td class="text-right">{{$g->asal_tujuan}}</td>
                        <td class="text-right">{{$g->type}}</td>
                        <td class="text-right">{{$g->qty_item}}</td>
                        <td class="text-right">{{number_format($g->berat_item,2)}}</td>
                        
                        @php
                            $used_qty       = 0 ;
                            $used_berat     = 0 ;
                            $sisa_qty       = 0 ;
                            $sisa_berat     = 0 ;
                        @endphp

                        @php
                            $used_qty      = $g->fs_qty + $g->bb_qty + $g->abf_qty + $g->ia_qty ;
                            $used_berat    = $g->fs_berat + $g->bb_berat + $g->abf_berat + $g->ia_berat ;
                        @endphp
                        

                        @php
                            $sisa_qty       =  $g->qty_item-$used_qty ;
                            $sisa_berat     =  $g->berat_item-$used_berat ;
                        @endphp

                        <td class="text-right">{{$used_qty}}</td>
                        <td class="text-right">{{number_format($used_berat,2)}}</td>
                        
                        <td class="text-right">
                            @if($g->stock_item < 0)
                            <span class="status status-danger">{{$g->stock_item}}</span> 
                            @else 
                            <span class="status status-success">{{$g->stock_item}}</span> 
                            @endif
                        </td>
                        <td class="text-right">
                            @if($g->stock_berat < 0)
                            <span class="status status-danger">{{number_format($g->stock_berat,2)}}</span> 
                            @else
                            <span class="status status-success">{{number_format($g->stock_berat,2)}}</span> 
                            @endif
                        </td>
                   </tr>
                    @php
                        $total_qty          = $total_qty + $g->qty_item;
                        $total_berat        = $total_berat + $g->berat_item;
                        $total_qty_used     = $total_qty_used + $used_qty;
                        $total_berat_used   = $total_berat_used + $used_berat;
                    @endphp
                    @endforeach

                    <tr>
                        <td colspan="4" class="text-center"><strong>TOTAL</strong></td>
                        <td class="text-right"><strong>{{$total_qty}}</strong></td>
                        <td class="text-right"><strong>{{$total_berat}}</strong></td>
                        <td class="text-right"><strong>{{$total_qty_used}}</strong></td>
                        <td class="text-right"><strong>{{number_format($total_berat_used,2)}}</strong></td>
                        <td class="text-right"><strong>{{$total_qty - $total_qty_used}}</strong></td>
                        <td class="text-right"><strong>{{number_format(($total_berat - $total_berat_used),2)}}</strong></td>
                    </tr>
                </tbody>

            </table>

            {{-- <hr>
            <h5>Pengambilan BB Fresh</h5>
            <div class="row">
                <div class="col-6">
                    <table width="100%" id="kategori" class="table default-table">
                        <thead>
                            <tr>
                                <th colspan="4">WHOLE CHICKEN</th>
                            </tr>
                            <tr>
                                <th>ITEM</th>
                                <th>QTY</th>
                                <th>BERAT</th>
                                <th>WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $data_whole = \App\Models\Chiller::regu_ambil_bb_fresh($tanggal, 'whole', 'baru');
                            @endphp

                                @php
                                    $total_qty      = 0;
                                    $total_berat    = 0;
                                @endphp
                                @foreach($data_whole as $d)
                                @php
                                    $total_qty      = $total_qty + $d->qty;
                                    $total_berat    = $total_berat + $d->berat;
                                @endphp
                                <tr>
                                    <td>{{$d->nama}}</td>
                                    <td>{{$d->qty}}</td>
                                    <td>{{$d->berat}}</td>
                                    <td>{{$d->created_at}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th>TOTAL</th>
                                    <th>{{$total_qty}}</th>
                                    <th>{{$total_berat}}</th>
                                    <th></th>
                                </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table width="100%" id="kategori" class="table default-table">
                        <thead>
                            <tr>
                                <th colspan="4">PARTING</th>
                            </tr>
                            <tr>
                                <th>ITEM</th>
                                <th>QTY</th>
                                <th>BERAT</th>
                                <th>WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $data_parting = \App\Models\Chiller::regu_ambil_bb_fresh($tanggal, 'parting', 'baru');
                            @endphp

                            @php
                                $total_qty      = 0;
                                $total_berat    = 0;
                            @endphp
                            @foreach($data_parting as $d)
                            @php
                                $total_qty      = $total_qty + $d->qty;
                                $total_berat    = $total_berat + $d->berat;
                            @endphp
                            <tr>
                                <td>{{$d->nama}}</td>
                                <td>{{$d->qty}}</td>
                                <td>{{$d->berat}}</td>
                                <td>{{$d->created_at}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th>TOTAL</th>
                                <th>{{$total_qty}}</th>
                                <th>{{$total_berat}}</th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table width="100%" id="kategori" class="table default-table">
                        <thead>
                            <tr>
                                <th colspan="4">MARINASI</th>
                            </tr>
                            <tr>
                                <th>ITEM</th>
                                <th>QTY</th>
                                <th>BERAT</th>
                                <th>WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $data_marinasi = \App\Models\Chiller::regu_ambil_bb_fresh($tanggal, 'marinasi', 'baru');
                            @endphp

                            @php
                                $total_qty      = 0;
                                $total_berat    = 0;
                            @endphp
                            @foreach($data_marinasi as $d)
                            @php
                                $total_qty      = $total_qty + $d->qty;
                                $total_berat    = $total_berat + $d->berat;
                            @endphp
                            <tr>
                                <td>{{$d->nama}}</td>
                                <td>{{$d->qty}}</td>
                                <td>{{$d->berat}}</td>
                                <td>{{$d->created_at}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th>TOTAL</th>
                                <th>{{$total_qty}}</th>
                                <th>{{$total_berat}}</th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table width="100%" id="kategori" class="table default-table">
                        <thead>
                            <tr>
                                <th colspan="4">BONELESS</th>
                            </tr>
                            <tr>
                                <th>ITEM</th>
                                <th>QTY</th>
                                <th>BERAT</th>
                                <th>WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $boneless = \App\Models\Chiller::regu_ambil_bb_fresh($tanggal, 'boneless', 'baru');
                            @endphp

                            @php
                                $total_qty      = 0;
                                $total_berat    = 0;
                            @endphp
                            @foreach($boneless as $d)
                            @php
                                $total_qty      = $total_qty + $d->qty;
                                $total_berat    = $total_berat + $d->berat;
                            @endphp
                            <tr>
                                <td>{{$d->nama}}</td>
                                <td>{{$d->qty}}</td>
                                <td>{{$d->berat}}</td>
                                <td>{{$d->created_at}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th>TOTAL</th>
                                <th>{{$total_qty}}</th>
                                <th>{{$total_berat}}</th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table width="100%" id="kategori" class="table default-table">
                        <thead>
                            <tr>
                                <th colspan="4">FROZEN</th>
                            </tr>
                            <tr>
                                <th>ITEM</th>
                                <th>QTY</th>
                                <th>BERAT</th>
                                <th>WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $frozen = \App\Models\Chiller::regu_ambil_bb_fresh($tanggal, 'frozen', 'baru');
                            @endphp

                            @php
                                $total_qty      = 0;
                                $total_berat    = 0;
                            @endphp
                            @foreach($frozen as $d)
                            @php
                                $total_qty      = $total_qty + $d->qty;
                                $total_berat    = $total_berat + $d->berat;
                            @endphp
                            <tr>
                                <td>{{$d->nama}}</td>
                                <td>{{$d->qty}}</td>
                                <td>{{$d->berat}}</td>
                                <td>{{$d->created_at}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th>TOTAL</th>
                                <th>{{$total_qty}}</th>
                                <th>{{$total_berat}}</th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table width="100%" id="kategori" class="table default-table">
                        <thead>
                            <tr>
                                <th colspan="4">SAMPINGAN</th>
                            </tr>
                            <tr>
                                <th>ITEM</th>
                                <th>QTY</th>
                                <th>BERAT</th>
                                <th>WAKTU</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $frozen = \App\Models\Chiller::jualsampingan_bb_fresh($tanggal);
                            @endphp

                            @php
                                $total_qty      = 0;
                                $total_berat    = 0;
                            @endphp
                            @foreach($frozen as $d)
                            @php
                                $total_qty      = $total_qty + $d->qty;
                                $total_berat    = $total_berat + $d->berat;

                            @endphp
                            <tr>
                                <td>{{$d->id}}. {{$d->nama}}</td>
                                <td>{{$d->qty}}</td>
                                <td>{{$d->berat}}</td>
                                <td>{{$d->created_at}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th>TOTAL</td>
                                <th>{{$total_qty}}</th>
                                <th>{{$total_berat}}</th>
                                <th>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> --}}
        </div>
    </section>

@stop

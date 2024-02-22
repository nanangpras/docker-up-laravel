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
    <div class="card-header font-weight-bold text-uppercase">Data Stock Awal</div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table default-table mb-0">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>TANGGAL</th>
                        <th>ASAL/TUJUAN</th>
                        <th>QTY AWAL</th>
                        <th>BERAT AWAL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $data->item_name }}</td>
                        <td>{{ $data->tanggal_produksi }}</td>
                        <td>{{ $data->tujuan }}</td>
                        <th class="text-right">{{ $data->qty_item}} Ekor</th>
                        <th class="text-right">{{ $data->berat_item }} Kg</th>
                    </tr>
                    @if($data->type=="hasil-produksi")
                    <tr>
                        <td colspan="5">
                            @php
                                $freestock_temp = \App\Models\FreestockTemp::find($data->table_id);
                            @endphp

                            @if($freestock_temp)
                                @if($data->regu =='byproduct')
                                    <a href="{{url('admin/evis/peruntukan?produksi='.$freestock_temp->freestock_id)}}" target="_blank"><span class="fa fa-share"></span> proses produksi</a>
                                @else
                                    <a href="{{url('admin/produksi-regu?kategori='.$data->regu.'&produksi='.$freestock_temp->freestock_id)}}" target="_blank"><span class="fa fa-share"></span> proses produksi</a>
                                @endif

                            @if($freestock_temp->kategori=="1")
                            <span class="status status-danger">[ABF]</span>
                            @elseif($freestock_temp->kategori=="2")
                            <span class="status status-warning">[EKSPEDISI]</span>
                            @elseif($freestock_temp->kategori=="3")
                            <span class="status status-warning">[TITIP CS]</span>
                            @else
                            <span class="status status-info">[CHILLER]</span>
                            @endif

                            @endif

                            @php
                                $exp = json_decode($data->label);
                            @endphp
                            @if($exp)
                            @if ($exp->additional ?? false) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                            @if ($exp->parting->qty ?? false)
                            <div class="blue">PART : {{ $exp->parting->qty }} </div>
                            @endif
                            @endif
                            @if ($exp->plastik->jenis ?? false)
                            <div class="orange text-uppercase">
                                {{ $exp->plastik->jenis }} @if ($exp->plastik->qty > 0) // {{ $exp->plastik->qty }} Pcs @endif
                            </div>
                            @endif
                            @if ($exp->sub_item ?? false)
                            <div class="green text-uppercase">
                                {{ $exp->sub_item }} <br>
                            </div>
                            @endif
                            @if ($data->konsumen->nama ?? false)
                            <div class="green text-uppercase">
                                CUSTOMER : {{ $data->konsumen->nama ?? '' }} <br>
                            </div>
                            @endif

                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="5">
                            @php
                                $grad_item  = 0;
                                $grad_berat = 0;
                            @endphp
                            @if($data->asal_tujuan=="retur")
                                <span class="status status-info">{{ $data->label }}</span>
                            @endif

                            @if($data->asal_tujuan=="gradinggabungan")
                                @foreach(\App\Models\Grading::where('tanggal_potong', $data->tanggal_potong)->where('item_id', $data->item_id)->get() as $gr)

                                    @if($gr->gradprod->no_urut!="")
                                        <div class="row mb-1 @if ($gr->gradprod->grading_status != 1) text-warning @endif">
                                            <div class="col">
                                                @if ($gr->gradprod->grading_status != 1)
                                                <b>PENDING</b>
                                                @else
                                                DISELESAIKAN
                                                @endif
                                            </div>
                                            <div class="col">
                                                <a href="{{url('admin/produksi/'.$gr->gradprod->id)}}" target="_blank">{{$gr->id}} || {{date('d/m/y || H:i:s',strtotime($gr->created_at))}}</a>
                                            </div>
                                            <div class="col">
                                                Mobil {{$gr->gradprod->no_urut}}
                                            </div>
                                            <div class="col">
                                                {{$gr->graditem->nama}}
                                            </div>
                                            <div class="col text-right">
                                                @if ($gr->gradprod->grading_status != 1) ( @endif
                                                {{$gr->total_item}} Ekor
                                                @if ($gr->gradprod->grading_status != 1) ) @endif
                                            </div>
                                            <div class="col text-right">
                                                @if ($gr->gradprod->grading_status != 1) ( @endif
                                                {{$gr->berat_item}} Kg
                                                @if ($gr->gradprod->grading_status != 1) ) @endif
                                            </div>
                                        </div>
                                        @if ($gr->gradprod->grading_status == 1)
                                        @php
                                            $grad_item  = $grad_item + $gr->total_item;
                                            $grad_berat = $grad_berat + $gr->berat_item;
                                        @endphp
                                        @endif
                                    @endif
                                @endforeach
                            @endif

                            <div class="row mb-1">
                                <div class="col">
                                    <b>Total</b>
                                </div>
                                <div class="col">

                                </div>
                                <div class="col">

                                </div>
                                <div class="col">

                                </div>
                                <div class="col text-right">
                                    @if($data->asal_tujuan=="gradinggabungan")
                                        <b>{{$grad_item ?? 0}} Ekor</b>
                                    @else
                                        <b>{{$data->qty_item ?? 0}} Ekor</b>
                                    @endif
                                </div>
                                <div class="col text-right">
                                    @if($data->asal_tujuan=="gradinggabungan")
                                        <b>{{$grad_berat ?? 0}} Kg</b>
                                    @else
                                        <b>{{$data->berat_item ?? 0}} Kg</b>
                                    @endif
                                </div>
                            </div>

                            @if($data->asal_tujuan=="evisgabungan")
                                @php
                                    $evis = \App\Models\Evis::where('tanggal_potong', $data->tanggal_produksi)->where('item_id', $data->item_id)->get();
                                @endphp
                                @foreach($evis as $ev)
                                <div class="row mb-1">
                                    <div class="col">
                                        <a href="{{url('admin/produksi/'.$ev->eviprod->id)}}" target="_blank"> {{date('d/m/Y H:i:s',strtotime($ev->created_at))}}</a>
                                    </div>
                                    <div class="col">
                                        Mobil {{$ev->eviprod->no_urut}}
                                    </div>
                                    <div class="col">
                                        {{$ev->eviitem->nama}}
                                    </div>
                                    <div class="col text-right">
                                        {{$ev->total_item}} Pcs
                                    </div>
                                    <div class="col text-right">
                                        {{$ev->berat_item}} Kg
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            @if ($data->status_cutoff == '1')
                <button href="#" class="btn btn-block btn-danger">Data Transaksi Sudah Ditutup</button>
            @else
                @if (App\Models\User::setIjin(33))

                <button class="btn btn-outline-info" data-toggle="modal" data-target="#edit">Adjustment Qty/Berat</button>
                @else

                @endif
            @endif
            @if (App\Models\Adminedit::where('table_id', $data->id)->where('activity', 'kepala_regu_hp')->where('type', 'edit')->first())
            <a class="btn btn-primary riwayat_edit_produksi" href="{{ url('admin/history/'.$data->id.'/kepalaregu') }}"
                data-toggle="modal" data-target="#riwayat_produksi"
                data-id="{{ $data->id }}">History Edit Qty/Berat Setelah diselesaikan
            </a>
            @endif
            <div class="modal fade" id="edit" tabindex="-1" aria-labelledby="editLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="post" enctype="multipart/form-data" action="{{route('chiller.adjustment')}}">
                        @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLabel">Edit Inbound</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <input type="hidden" name="chiller_id" value="{{ $data->id }}" class="form-control" id="qty">
                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Qty Awal
                                        <input type="number" value="{{ $data->qty_item }}" class="form-control" id="qty" disabled>
                                    </div>
                                </div>
                                <div class="col pl-1">
                                    <div class="form-group">
                                        Berat Awal
                                        <input type="number" value="{{ $data->berat_item }}" class="form-control" id="berat" step="0.01" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Qty Akhir
                                        <input type="number" name="ubah_qty" value="{{ $data->stock_item }}" class="form-control" id="qty">
                                    </div>
                                </div>
                                <div class="col pl-1">
                                    <div class="form-group">
                                        Berat Akhir
                                        <input type="number" name="ubah_berat" value="{{ $data->stock_berat }}" class="form-control" id="berat" step="0.01">
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

<section class="panel">
    <div class="card-header font-weight-bold text-uppercase">Riwayat Ambil Bahan Baku</div>
    @if (App\Models\Adminedit::where('table_id', $data->id)->where('activity', 'kepala_regu_bb')->where('type', 'edit')->first())
    <a class="btn btn-primary riwayat_edit_bb btn-sm mt-1 ml-1" href="{{ url('admin/history/'.$data->id.'/kepalaregu') }}"
        data-toggle="modal" data-target="#riwayat_produksi"
        data-id="{{ $data->id }}">History Edit Qty/Berat Setelah diselesaikan
    </a>
    @endif
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table default-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Regu</th>
                        <th>QTY</th>
                        <th>Berat</th>
                        <th>Waktu Ambil</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $qty    =   0 ;
                        $berat  =   0 ;
                    @endphp
                    @foreach ($data->ambil_chiller as $i => $row)
                    @php
                        $qty    +=  $row->qty ;
                        $berat  +=  $row->berat ;
                    @endphp
                        <tr>
                            <td>{{ ++$i }}</td>
                            
                            <td>
                                @if($data->regu =='byproduct')
                                    <a href="{{url('admin/evis/peruntukan?produksi='.$row->freestock_id)}}" target="_blank">{{$row->regu }}</a> 
                                @else
                                    <a href="{{url('admin/produksi-regu?kategori='.$row->regu.'&produksi='.$row->freestock_id)}}" target="_blank">{{$row->regu }}</a> 
                                @endif

                            </td>
                            <td>{{ number_format($row->qty) }}</td>
                            <td>{{ number_format($row->berat, 2) }} Kg</td>
                            <td>{{ date('Y-m-d H:i:s', strtotime($row->created_at)) }} <br>{{ date('Y-m-d H:i:s', strtotime($row->updated_at)) }}</td>
                            <td>@if($row->free_stock) {{ $row->free_stock->status == '3' ? 'Selesai' : 'Pending' }} @else Dihapus @endif</td>           
                            <td>
                                @if (App\Models\Adminedit::where('table_id', $row->id)->where('activity', 'kepala_regu_bb')->where('type', 'edit')->first())
                                    <a class="btn btn-primary riwayat_edit_bb btn-sm mt-1 ml-1" href="{{ url('admin/history/'.$row->id.'/kepalaregu') }}"
                                        data-toggle="modal" data-target="#riwayat_produksi"
                                        data-id="{{ $row->id }}">History Edit Qty/Berat Setelah diselesaikan
                                    </a>
                                @endif
                            </td>                 
                        </tr>
                    @endforeach

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Total</th>
                        <th>{{ number_format($qty) }}</th>
                        <th>{{ number_format($berat, 2) }} Kg</th>
                        <th></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>


@if (COUNT($tukar_item))
<section class="panel">
    <div class="card-header font-weight-bold text-uppercase">Tukar Item</div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table default-table mt-1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Item</th>
                        <th>QTY</th>
                        <th>Berat</th>
                        <th>Waktu Ambil</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tukar_item as $i => $tki)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>Tukar Item : {{$tki->jenis}}</td>
                            <td>{{$tki->item_name}}</td>
                            <td>{{ number_format($tki->qty_item) }}</td>
                            <td>{{ number_format($tki->berat_item, 2) }} Kg</td>
                            <td>{{ date('Y-m-d H:i:s', strtotime($tki->created_at)) }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endif

<section class="panel">
    <div class="card-header font-weight-bold text-uppercase">Alokasi Order</div>
    <div class="card-body p-2">
        <table class="table default-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>SO</th>
                    <th>QTY</th>
                    <th>Berat</th>
                    <th>Keterangan</th>
                    <th>Waktu Ambil</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $qty    =   0 ;
                    $berat  =   0 ;
                @endphp
                @foreach ($data->alokasi_order as $i => $row)
                    @if ($row->deleted_at == NULL)
                        @php
                            $qty    +=  $row->bb_item ;
                            $berat  +=  $row->bb_berat ;
                            $order = \App\Models\Order::find($row->order_id);
                        @endphp
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>
                                <a href="{{url('admin/laporan/sales-order/'.$row->bahanbborder->id)}}" target="_blank"> {{$order->no_so ?? ""}} - {{$order->nama ?? ""}}</a><br />
                                {{ $row->nama }}
                            </td>
                            <td>{{ number_format($row->bb_item) }}</td>
                            <td>{{ number_format($row->bb_berat, 2) }} Kg</td>
                            <td>
                                @if($row->chiller_alokasi != '' || $row->chiller_alokasi != NULL)
                                @isset($row->orderitem->part))
                                    @if($row->orderitem->part != '' || $row->orderitem->part != NULL)
                                        {{ "Parting - " .$row->orderitem->part }}
                                    @else
                                        
                                    @endif
                                @endisset
                                @endif
                            </td>
                            <td>{{ date('Y-m-d H:i:s', strtotime($row->created_at)) }} <br>{{ date('Y-m-d H:i:s', strtotime($row->updated_at)) }}</td>
                            <td>@if($row->status =="2") Selesai @else Pending @endif</td>
                        </tr>
                    @endif
                @endforeach
                <tr>
                    <th colspan="2">Total</th>
                    <th>{{ number_format($qty) }}</th>
                    <th>{{ number_format($berat, 2) }} Kg</th>
                    <th colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="card-header font-weight-bold text-uppercase">ACC ABF (INBOUND)</div>
    <div class="card-body p-2">
        <table class="table default-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>QTY Awal</th>
                    <th>Berat Awal</th>
                    <th>QTY</th>
                    <th>Berat</th>
                    <th>Waktu Ambil (ACC ABF)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $qty    =   0 ;
                    $berat  =   0 ;
                @endphp
                @foreach ($data->ambil_abf as $i => $row)
                    @php
                        $qty    +=  $row->qty_awal ;
                        $berat  +=  $row->berat_awal ;
                    @endphp
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->id }}</td>
                        <td><a href="{{url('admin/abf/timbang/'.$row->id)}}" target="_blank">{{$row->item_name}}</a></td>
                        <td>{{ number_format($row->qty_awal) }}</td>
                        <td>{{ number_format($row->berat_awal, 2) }} Kg</td>
                        <td>{{ number_format($row->qty_item) }}</td>
                        <td>{{ number_format($row->berat_item, 2) }} Kg</td>
                        <td>{{ date('Y-m-d H:i:s', strtotime($row->created_at)) }} <br>{{ date('Y-m-d H:i:s', strtotime($row->updated_at)) }}</td>
                        <td>@if($row->status=="2") Selesai @elseif ($row->parent_abf != NULL) Selesai (Digabung) @else Pending @endif</td>
                    </tr>
                    
                
                    {{-- <tr>
                        <th colspan="8">
                            DITERIMA ABF (BONGKAR CS)
                        </th>
                    </tr> --}}

                    @endforeach

                <tr>
                    <th colspan="3">Total ABF</th>
                    <th>{{ number_format($qty) }}</th>
                    <th>{{ number_format($berat, 2) }} Kg</th>
                    <th colspan="4"></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="card-header font-weight-bold text-uppercase">DITERIMA ABF (BONGKAR CS)</div>
        <div class="card-body p-2">
        <table class="table default-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>QTY Awal</th>
                    <th>Berat Awal</th>
                    <th>QTY</th>
                    <th>Berat</th> 
                    <th>Waktu Bongkar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($data->ambil_abf as $i => $row)
                @foreach($row->abf_gudang as $no_gdg => $gdg)

                    @if($gdg->jenis_trans=="masuk")
                    <tr>
                        <td>{{ ++$no_gdg }}</td>
                        <td>{{ $gdg->id }}</a> </td>
                        <td><a href="{{route('warehouse.tracing', ['id' => $gdg->id])}}" target="_blank">{{$gdg->productitems->nama}}<br>{{$gdg->sub_item}}</a></td>
                        <td>{{ number_format($gdg->qty_awal) }}</td>
                        <td>{{ number_format($gdg->berat_awal, 2) }} Kg</td>
                        <td>{{ number_format($gdg->qty) }}</td>
                        <td>{{ number_format($gdg->berat, 2) }} Kg</td>
                        <td>{{ date('Y-m-d H:i:s', strtotime($gdg->created_at)) }} <br>{{ date('Y-m-d H:i:s', strtotime($gdg->updated_at)) }}</td>
                        <td>@if($gdg->status=="2") Selesai @else Pending @endif</td>
                    </tr>
                    @endif
                @endforeach
            @endforeach
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="card-header font-weight-bold text-uppercase">Inventory Adjustment</div>
    <div class="card-body p-2">
        <table class="table default-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>QTY</th>
                    <th>Berat</th>
                    <th>Jenis</th>
                    <th>Waktu Ambil</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $qty    =   0 ;
                    $berat  =   0 ;
                @endphp
                @foreach ($data->inventory_adjustment as $i => $row)
                @php
                    $qty    +=  $row->qty_item ;
                    $berat  +=  $row->berat_item ;
                @endphp
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ number_format($row->qty_item) }}</td>
                        <td>{{ number_format($row->berat_item, 2) }} Kg</td>
                        <td>{{$row->asal_tujuan}}</td>
                        <td>{{ date('Y-m-d H:i:s', strtotime($row->created_at)) }} <br>{{ date('Y-m-d H:i:s', strtotime($row->updated_at)) }}</td>
                        <td>@if($row->status=="2") Masuk @else Keluar @endif</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="2">Total</th>
                    <th>{{ number_format($qty) }}</th>
                    <th>{{ number_format($berat, 2) }} Kg</th>
                    <th colspan="2"></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="card-header font-weight-bold text-uppercase">Sisa Akhir</div>
    <div class="card-body p-2">
        <table class="table default-table mb-0">
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>TANGGAL</th>
                    <th>TIMESTAMP</th>
                    <th>QTY AKHIR</th>
                    <th>BERAT AKHIR</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $data->item_name }}</td>
                    <td>{{ date('Y-m-d H:i:s', strtotime($data->created_at)) }}<br>{{ date('Y-m-d H:i:s', strtotime($data->updated_at)) }}</td>
                    <td>{{ $data->tanggal_produksi }}</td>
                    <td>{{ $data->stock_item }} Ekor</td>
                    <td>{{ number_format($data->stock_berat,2) }} Kg</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>


{{-- modal box --}}
<div class="modal fade" id="riwayat_produksi" aria-labelledby="riwayatLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Riwayat Edit</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="content_history"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- modal box --}}

<a href="{{route('chiller.recalculate', $data->id)}}?key=stockawal" class="red">Recalculate Data Stock Awal</a>
<a href="{{route('chiller.recalculate', $data->id)}}?key=penggunaan" class="red">Recalculate Data Penggunaan</a>
<script>
    $(".riwayat_edit_produksi").click( function(e){
                e.preventDefault();
        var id       = $(this).data('id');
        var href     = $(this).attr('href');
        $.ajax({
            url : href,
            type: "GET",
            data: {
                id          : id,
                key         : "riwayat_edit_produksi",
            },
            success: function(data){
                $('#content_history').html(data);
            }
        });
    });

    $(".riwayat_edit_bb").click( function(e){
        e.preventDefault();
        var id       = $(this).data('id');
        var href     = $(this).attr('href');
        $.ajax({
            url : href,
            type: "GET",
            data: {
                id          : id,
                key         : "riwayat_edit_bb",
            },
            success: function(data){
                $('#content_history').html(data);
            }
        });
    });
</script>
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

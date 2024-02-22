@php
$netsuite = \App\Models\Netsuite::where('label', 'like', '%'.$kategori.'%')->where('label', '!=', 'item_receipt_frozen')->where('trans_date', $tanggal)->get();
@endphp

@if(count($netsuite)>0)
<h6>Netsuite Terbentuk</h6>

<table class="table default-table">
    <thead>
        <tr>
            <th>
                <input type="checkbox" id="ns-checkall">
            </th>
            <th>ID</th>
            <th>C&U Date</th>
            <th>TransDate</th>
            <th>Label</th>
            <th>Activity</th>
            <th>Location</th>
            <th>IntID</th>
            <th>Paket</th>
            <th width="100px">Data</th>
            <th width="100px">Action</th>
            <th>Response</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($netsuite as $no => $field_value)
            @include('admin.pages.log.netsuite_one', ($netsuite = $field_value))
        @endforeach

    </tbody>
</table>

@else
@if (User::setIjin(33))
@if ($progress < 1)

@if(env('NET_SUBSIDIARY', 'CGL')!="EBA")
    <h6>Wo Belum dikirim</h6>
    <div class="alert alert-danger">
    Kirim WO ketika semua produksi sudah selesai, jangan dikirim jika masih proses, WO bersifat global dan sekali kirim.
    </div>

    <form action="{{ route('wo.create') }}" method="GET">
        <div class="form-group">
            <input type="hidden" name="tanggal" class="form-control tanggal" id="tanggal-form" value="{{ $tanggal }}"
                autocomplete="off">
            <input type="hidden" name="regu" class="form-control" id="regu-form" value="{{ $kategori }}"
                autocomplete="off">
            <button type="submit" class="btn btn-blue form-control">Buat WO</button>
        </div>
    </form>
    @endif
    @endif
    @endif

    @endif

    {{-- {{ var_dump(json_encode($ceklogdelete)) }} --}}
    @if ($ceklogdelete)
        <a href="{{ route('regu.index', ['key' => 'hasil_harian', 'subkey' => 'logdelete']) }}&tanggal={{ $request->tanggal }}&regu={{ $request->kat }}"
            class="btn btn-warning form-control mb-2" target="_blank">Riwayat Dibatalkan</a>
    @endif

    <input type="hidden" id="selected_category" value="{{$kategori}}">
    @foreach($freestock as $no => $row)
    @php
    $ns = \App\Models\Netsuite::where('id',$row->netsuite_id)->first();
    $show = ((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33)) ? TRUE : (($row->user_id ==
    Auth::user()->id) ? TRUE : FALSE) ;
    @endphp


    @if(count($row->listfreestock)>0)
    <div class="card mb-2">
        <div class="p-2">
            <div class="float-right">
                @if ($show)
                    @if ($row->status == 2)
                    <button type="button" class="btn btn-sm btn-success approved"
                        data-id="{{ $row->id }}">Selesaikan</button>
                    @endif

                    @if (!$ns)
                    <a href="{{ route('regu.index', ['kategori' => $kategori, 'produksi' => $row->id]) }}" class="btn btn-sm btn-info edit_regu">Detail</a>
                        @if ((Auth::user()->account_role == 'superadmin') || (App\Models\User::setIjin(33)))
                        <button type="button" class="btn btn-sm btn-danger removed" data-id="{{ $row->id }}">Batalkan</button>
                        @endif
                        
                    @endif
                @endif
            </div>
            <a href="{{ route('regu.index', ['kategori' => $kategori, 'produksi' => $row->id]) }}">Produksi
                {{date('d/m/Y', strtotime($row->tanggal))}}</a><br>
            User Input : {{ App\Models\User::find($row->user_id)->name }}
            
            @if($row->netsuite_send=="0")
                &nbsp <span class="status status-danger">TIDAK KIRIM WO</span>
            @endif
            
            @php
            if($ns){

            try {
            echo "<span class='status status-danger'> Document No : ".$ns->document_no."</span>";
            } catch (\Throwable $th) {
            //throw $th;
            }
            }
            @endphp

        </div>
        <div class="card-body p-2">
            <div class="row">
                @php
                    $total_bb = 0;
                    $total_fg = 0;
                @endphp
                <div class="col-sm-6 pr-sm-1">
                    <table class="table default-table table-small">
                        <thead>
                            <th>Bahan Baku</th>
                            <th>Tanggal</th>
                            <th>Asal</th>
                            <th>Ekor/Pcs/Pack</th>
                            <th>Berat</th>
                            @if ((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33))
                            @if (!$ns)
                            <th></th>
                            @endif
                            @endif
                        </thead>
                        <tbody>
                            @php
                            $item = 0;
                            $berat = 0;
                            @endphp
                            @foreach ($row->listfreestock as $no => $rfs)
                            @php
                            $item               += $rfs->qty;
                            $berat              += $rfs->berat;
                            if($rfs->chiller->label ?? FALSE){
                                $exp = json_decode($rfs->chiller->label) ;
                            }else{
                                $exp = [];
                            }
                            @endphp
                            <tr class="filter-name-harian">
                                <td>{{++$no}}. {{ $rfs->chiller->item_name ?? '<span class="status status-danger">ITEM TELAH DIHAPUS</span>'}}
                                    @if($rfs->chiller->label ?? FALSE)
                                    @if($rfs->chiller->label!="" && $rfs->chiller->type=="bahan-baku")
                                    <br><span class="status status-info">{{$rfs->chiller->label ?? ''}}</span>
                                    @endif
                                    @endif
                                    @if($rfs->catatan!="")
                                    <br>Catatan : {{$rfs->catatan}}
                                    @endif

                                </td>
                                <td>{{ $rfs->chiller->tanggal_produksi ?? ''}}
                                    <br>{{$rfs->bb_kondisi}}

                                </td>
                                <td>{{ $rfs->chiller->tujuan ?? ''}}</td>
                                <td>{{ number_format($rfs->qty) }}</td>
                                <td class="text-right">{{ number_format($rfs->berat, 2) }} Kg</td>
                                @if ((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33))
                                @if (!$ns)
                                <td><i class="fa fa-edit text-primary px-1 edit-bb-open" data-toggle="modal"
                                        data-nama="{{$rfs->chiller->item_name ?? ''}}" data-id="{{$rfs->id}}"
                                        data-qty="{{$rfs->qty}}" data-berat="{{$rfs->berat}}" 
                                        data-chillerid="{{ $rfs->chiller_id}}"
                                        data-target="#bb-edit"></i></td>
                                @endif
                                @endif
                            </tr>
                            <tr>
                            <td colspan="5">
                                    <div class="row">
                                        <div class="col pr-1">
                                            @if ($rfs->kode_produksi)
                                                Kode Produksi : {{ $rfs->kode_produksi }}
                                            @endif
                                        </div>
                                        <div class="col pl-1 text-right">
                                            @if ($rfs->unit)
                                                Unit : {{ $rfs->unit }}
                                            @endif
                                        </div>
                                    </div>
                                    @if ($rfs->keranjang)
                                        <div>{{ $rfs->keranjang }} Keranjang</div>
                                    @endif
                                    @if ($exp->plastik->jenis ?? FALSE)
                                    <div class="status status-success">
                                        <div class="row">
                                            <div class="col pr-1">
                                                {{ $exp->plastik->jenis }}
                                            </div>
                                            <div class="col-auto pl-1">
                                                @if ($exp->plastik->qty > 0)
                                                <span class="float-right">// {{ $exp->plastik->qty }} Pcs</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if ($exp->additional ?? FALSE) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                                    <div class="row mt-1 text-info">
                                        <div class="col pr-1">
                                            @if ($rfs->customer_id) <div>Customer : {{ $rfs->konsumen->nama ?? '-' }}</div> @endif
                                            @if ($rfs->bumbu_id) <div>Bumbu : {{ $rfs->bumbu->nama ?? '-' }}</div> @endif
                                            @if ($exp->sub_item ?? FALSE) <div>Keterangan : {{ $exp->sub_item }}</div> @endif
                                        </div>
                                        <div class="col-auto pl-1 text-right">
                                            @if ($rfs->selonjor ?? FALSE) <div class="text-danger font-weight-bold">SELONJOR</div> @endif
                                            @if ($exp->parting->qty ?? FALSE) Parting : {{ $exp->parting->qty }} @endif
                                        </div>
                                    </div>
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-center">Total</th>
                                <th> {{ number_format($item) }}</th>
                                <th class="text-right">{{ number_format($berat, 2) }} Kg</th>
                                @if ((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33))
                                @if (!$ns)
                                <td></td>
                                @endif
                                @endif
                            </tr>
                            @php
                                $cekhistory = App\Models\FreestockList::where('freestock_id', $row->id)->whereNotNull('deleted_at')->withTrashed()->count();
                            @endphp
                            @if ($cekhistory > 0)
                                <tr>
                                    <th colspan="6"><a href="{{route('regu.index',['key' =>'history_delete_bb'])}}&produksi={{ $row->id }}" class="btn btn-sm btn-info" target="_blank">History Delete Bahan Baku</a></th>
                                </tr>
                            @endif
                        </tfoot>
                    </table>

                @php
                    $total_bb = $berat;
                @endphp


                </div>
                <div class="col-sm-6 pl-sm-1">
                    <table class="table default-table table-small">
                        <thead>
                            <th>Hasil Produksi</th>
                            <th>Ekor/Pcs/Pack</th>
                            <th>Berat</th>
                            @if ((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33))
                            @if (!$ns && $row->status != 3)
                            <th></th>
                            @endif
                            @endif
                            @if((Auth::user()->account_role == 'admin') && App\Models\User::setIjin(43))
                                <th></th>
                            @endif
                        </thead>
                        <tbody>
                            @php
                            $qty = 0;
                            $berat = 0;
                            @endphp
                            @foreach ($row->freetemp as $no => $item)
                            @php
                            $qty += $item->qty;
                            $berat += $item->berat;
                            $exp = json_decode($item->label) ;
                            @endphp
                            <tr class="filter-name-harian">
                                <td>{{++$no}}.
                                    @if($item->kategori=="1")
                                    <span class="status status-danger">[ABF]</span>
                                    @elseif($item->kategori=="2")
                                    <span class="status status-warning">[EKSPEDISI]</span>
                                    @elseif($item->kategori=="3")
                                    <span class="status status-warning">[TITIP CS]</span>
                                    @else
                                    <span class="status status-info">[CHILLER]</span>
                                    @endif
                                    {{ $item->item->nama ?? '<span class="status status-danger">ITEM TELAH DIHAPUS</span>'}}
                                </td>
                                <td>{{ number_format($item->qty) }}</td>
                                <td class="text-right">{{ number_format($item->berat, 2) }} Kg</td>
                                @if ((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33))
                                @if ((!$ns) && ($row->status != 3) && (!$item->tempchiller))
                                <td class="text-right">
                                    <i class="fa fa-edit text-primary px-2 edit-hasil-open"
                                        data-toggle="modal"
                                        data-target="#hasil-edit"
                                        data-nama="{{$item->item->nama ?? ''}}"
                                        data-id="{{$item->id}}"
                                        data-itemid="{{$item->item->id ?? ''}}"
                                        data-qty="{{$item->qty}}"
                                        data-berat="{{$item->berat}}"
                                        data-plastik="{{ $item->plastik_nama }}"
                                        data-qtyplastik="{{ $item->plastik_qty }}"
                                        data-customer="{{ $item->customer_id ?? '' }}"
                                        data-parting="{{ $exp->parting->qty ?? '' }}"
                                        data-subitem="{{ $exp->sub_item }}"
                                        data-bumbuid= "{{$item->bumbu_id ?? ''}}"
                                        data-bumbu_berat = "{{ $item->bumbu_berat }}"
                                        data-kategori       ="{{ $item->regu }}"
                                        >
                                    </i>
                                </td>
                                @endif
                                @endif
                                @if((Auth::user()->account_role == 'admin') || App\Models\User::setIjin(33))
                                    @if($ns)
                                        <td class="text-right">
                                            <i class="fa fa-edit text-primary px-2 edit-hasil-open"
                                                data-toggle="modal"
                                                data-target="#hasil-edit"
                                                data-nama="{{$item->item->nama ?? ''}}"
                                                data-id="{{$item->id}}"
                                                data-itemid="{{$item->item->id ?? ''}}"
                                                data-qty="{{$item->qty}}"
                                                data-berat="{{$item->berat}}"
                                                data-plastik="{{ $item->plastik_nama }}"
                                                data-qtyplastik="{{ $item->plastik_qty }}"
                                                data-customer="{{ $item->customer_id ?? '' }}"
                                                data-parting="{{ $exp->parting->qty ?? '' }}"
                                                data-subitem="{{ $exp->sub_item }}"
                                                data-bumbuid= "{{$item->bumbu_id ?? ''}}"
                                                data-bumbu_berat = "{{ $item->bumbu_berat }}"
                                                data-kategori       ="{{ $item->regu }}"
                                                >
                                            </i>
                                        </td>
                                    @endif
                                @endif
                            </tr>
                            <tr class="filter-name-harian">
                                <td colspan="5">
                                    <div class="row">
                                        <div class="col pr-1">
                                            @if ($item->kode_produksi)
                                                Kode Produksi : {{ $item->kode_produksi }}
                                            @endif
                                        </div>
                                        <div class="col pl-1 text-right">
                                            @if ($item->unit)
                                                Unit : {{ $item->unit }}
                                            @endif
                                        </div>
                                    </div>
                                    @if ($item->keranjang)
                                        <div>{{ $item->keranjang }} Keranjang</div>
                                    @endif
                                    @if ($exp->plastik->jenis)
                                    <div class="status status-success">
                                        <div class="row">
                                            <div class="col pr-1">
                                                {{ $exp->plastik->jenis }}
                                            </div>
                                            <div class="col-auto pl-1">
                                                @if ($exp->plastik->qty > 0)
                                                <span class="float-right">// {{ $exp->plastik->qty }} Pcs</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                                    <div class="row mt-1 text-info">
                                        <div class="col pr-1">
                                            @if ($item->customer_id) <div>Customer : {{ $item->konsumen->nama ?? App\Models\Customer::find($item->customer_id)->nama }}</div> @endif
                                            @if ($item->bumbu) <div>Bumbu : {{ $item->bumbu->nama ?? '-' }}</div> @endif
                                            @if ($exp->sub_item) <div>Keterangan : {{ $exp->sub_item }}</div> @endif
                                        </div>
                                        <div class="col-auto pl-1 text-right">
                                            @if ($item->selonjor) <div class="text-danger font-weight-bold">SELONJOR</div> @endif
                                            @if ($exp->parting->qty) Parting : {{ $exp->parting->qty }} @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th> {{ $qty }} Ekor</th>
                                <th class="text-right">{{ $berat }} Kg</th>
                                @if ((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33))
                                @if (!$ns)
                                <td></td>
                                @endif
                                @endif
                            </tr>
                            @php
                            $cekhistoryhasilproduksi = App\Models\FreestockTemp::where('freestock_id', $row->id)->whereNotNull('deleted_at')->withTrashed()->count();
                            @endphp
                            @if ($cekhistoryhasilproduksi > 0)
                                <tr>
                                    <th colspan="6"><a href="{{route('regu.index',['key' =>'history_delete_hp'])}}&produksi={{ $row->id }}" class="btn btn-sm btn-info" target="_blank">History Delete
                                        Hasil Produksi</a></th>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
                    @php
                    $total_fg   = $berat;
                    $selisih    = ($total_bb-$total_fg)*(-1);
                    if($total_bb>0){
                        $presentase = (($total_bb-$total_fg)/$total_bb*100)*(-1);
                    }else{
                        $presentase = 0;
                    }
                    @endphp

                    <div class="col-sm-12">
                    <hr>
                    <div class="row">
                        @if($total_bb>0 && $total_fg>0)
                        <div class="col-2">
                            <div class="px-2">
                                <label>Selisih</label><br>
                                @if($presentase > 5 || $presentase < -5)
                                <b class="red">{{number_format($selisih,2)}} Kg</b>
                                @else
                                <b class="blue">{{number_format($selisih,2)}} Kg</b>
                                @endif
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="px-2">
                                <label>Presentase</label><br>
                                @if($presentase > 5 || $presentase < -5)
                                <b class="red">{{number_format($presentase, 2)}} %</b>
                                @else
                                <b class="blue">{{number_format($presentase, 2)}} %</b>
                                @endif
                            </div>
                        </div>
                        <div class="col-4">
                            <label>Keterangan</label><br>
                            @if($presentase > 5 || $presentase < -5)
                                <div class="status status-warning">Presentasi susut masih diatas atau dibawah benchmark 5%</div>
                            @else
                                <div class="status status-success">Presentasi susut sesuai dengan benchmark 5%</div>
                            @endif
                        </div>

                        @else
                            @if($total_bb>0 && $total_fg==0)
                                <div class="col-4"><span class="status status-warning">Penginputan Bahan Baku</span></div>
                            @endif
                            @if($total_fg>0 && $total_bb==0)
                                <div class="col-4"><span class="status status-info">Penginputan Hasil Produksi</span></div>
                            @endif
                        @endif

                        @if($row->orderitem_id)
                        <div class="col-4">
                            <label>Input By Order</label>
                            @php
                                $order_item = \App\Models\OrderItem::find($row->orderitem_id);
                            @endphp
                            <span class="status status-success">{{$order_item->itemorder->no_so ?? "#"}}</span>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    @else

    <table class="table default-table table-small">
        <thead>
            <th width="30%">
                <a href="{{ route('regu.index', ['kategori' => $kategori, 'produksi' => $row->id]) }}">Produksi
                {{date('d/m/Y', strtotime($row->tanggal))}}

                @if($row->netsuite_send=="0")
                    &nbsp <span class="status status-danger">TIDAK KIRIM WO</span>
                @endif
                
                </a>
            </th>
            <th></th>
            <th></th>
            <th width="7%">Qty</th>
            <th width="7%">Berat</th>
            <th width="10%">Action</th>
        </thead>
        <tbody>
            @php
            $qty = 0;
            $berat = 0;
            @endphp
            @if(count($row->freetemp) == 0 )
                <tr class="filter-name-harian">
                    <td colspan="3"> <span class="label label-default label-inline label-md pl-1"> Tidak ada data</span></td>
                    <td colspan="3" class="text-center"><a href="{{ route('regu.index', ['kategori' => $kategori, 'produksi' => $row->id]) }}" class="btn btn-danger btn-sm px-1 riwayathapus">Lihat Riwayat</a></td>
                </tr>
            @else
                @foreach ($row->freetemp as $no => $item)
                @php
                $qty += $item->qty;
                $berat += $item->berat;
                $exp = json_decode($item->label) ;
                @endphp
                

                    <tr class="filter-name-harian">
                        <td>
                            {{ $item->item->nama ?? '<span class="status status-danger">ITEM TELAH DIHAPUS</span>'}}<br>
                            @if($item->kategori=="1")
                            <span class="status status-danger">[ABF]</span>
                            @elseif($item->kategori=="2")
                            <span class="status status-warning">[EKSPEDISI]</span>
                            @elseif($item->kategori=="3")
                            <span class="status status-warning">[TITIP CS]</span>
                            @else
                            <span class="status status-info">[CHILLER]</span>
                            @endif
                            @php
                                $order_item = \App\Models\OrderItem::find($row->orderitem_id);
                            @endphp
                            <br><span class="status status-success">{{$order_item->itemorder->no_so ?? "#"}}</span>
                            
                        </td>
                        <td>
                            @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{$exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                            <div class="row mt-1">
                                <div class="col pr-1">
                                    @if ($item->customer_id) <div>{{ $item->konsumen->nama ?? '-' }}</div> @endif
                                    @if ($exp->sub_item) <div>Keterangan : {{ $exp->sub_item }}</div> @endif
                                </div>
                                <div class="col-auto pl-1 text-right">
                                    @if ($item->selonjor) <div class="text-danger font-weight-bold">SELONJOR</div> @endif
                                    @if ($exp->parting->qty) Parting : {{ $exp->parting->qty }} @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="status status-success">
                                <div class="row">
                                    <div class="col pr-1">
                                        {{ $item->plastik_nama }}
                                    </div>
                                    <div class="col-auto pl-1">
                                        <span class="float-right">// {{ $item->plastik_qty }} Pcs</span>
                                    </div>
                                </div>
                            </div>
                            @if ($item->bumbu_id)
                            <div class="status status-success">
                                <div class="row">
                                    <div class="col pr-1">
                                        {{ $item->bumbu->nama }}
                                    </div>
                                    <div class="col-auto pl-1">
                                        <span class="float-right">// {{ $item->bumbu_berat }} kg</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                    
                        </td>
                        <td class="text-right">{{ number_format($item->qty) }}</td>
                        <td class="text-right">{{ number_format($item->berat, 2) }} Kg</td>
                        @if ((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33))
                        @if ((!$ns) && ($row->status != 3) && (!$item->tempchiller))
                        <td class="text-right">
                            <i class="fa fa-edit text-primary px-2 edit-hasil-open"
                                data-toggle="modal"
                                data-target="#hasil-edit"
                                data-nama="{{$item->item->nama ?? ''}}"
                                data-id="{{$item->id}}"
                                data-itemid="{{$item->item->id ?? ''}}"
                                data-qty="{{$item->qty}}"
                                data-berat="{{$item->berat}}"
                                data-plastik="{{ $item->plastik_nama }}"
                                data-qtyplastik="{{ $item->plastik_qty }}"
                                data-customer="{{ $item->customer_id ?? '' }}"
                                data-parting="{{ $exp->parting->qty ?? '' }}"
                                data-subitem="{{ $exp->sub_item }}"
                                data-bumbuid= "{{$item->bumbu_id ?? ''}}"
                                data-bumbu_berat = "{{ $item->bumbu_berat }}"
                                >
                            </i>
                        </td>
                        @endif
                        @endif
                        <td class="text-right">
                                @if ($show)
                                @if ($row->status == 2)
                                <button type="button" class="btn btn-sm btn-success approved"
                                    data-id="{{ $row->id }}">Selesaikan</button>
                                @endif

                                @if (!$ns)
                                <a href="{{ route('regu.index', ['kategori' => $kategori, 'produksi' => $row->id]) }}" class="btn btn-sm btn-info edit_regu">Detail</a>
                                @if ((Auth::user()->account_role == 'superadmin') || (App\Models\User::setIjin(33)))
                                <button type="button" class="btn btn-sm btn-danger removed" data-id="{{ $row->id }}">Batalkan</button>
                                @endif
                                @endif
                                @endif
                        </td>
                    </tr>
                @endforeach
            @endif 
        </tbody>
    </table>

    @endif

    
    

    @endforeach



    <script>
    $('.edit-hasil-open').on('click', function(){
        var id          =   $(this).data('id');
        var nama        =   $(this).data('nama');
        var qty         =   $(this).data('qty');
        var berat       =   $(this).data('berat');
        var item        =   $(this).data('itemid');
        var plastik     =   $(this).data('plastik');
        var kategori    =   $(this).data('kategori');
        var qtyplastik  =   $(this).data('qtyplastik');
        var customer    =   $(this).data('customer');
        var subitem     =   $(this).data('subitem');
        var parting     =   $(this).data('parting');
        var bumbu       =   $(this).data('bumbuid');
        var bumbu_berat =   $(this).data('bumbu_berat');
        // console.log(bumbu)id

        $('#form-edit-id-hasil').val(id);
        $('#form-edit-nama-hasil').html(nama);
        $('#form-edit-qty-hasil').val(qty);
        $('#form-edit-berat-hasil').val(berat);
        $('#form-item-id').val(item);
        $('#form-edit-plastik-hasil').val(plastik);
        $('#form-edit-parting-hasil').val(parting);
        $('#form-edit-qtyplastik-hasil').val(qtyplastik);
        $('#form-edit-keterangan-hasil').val(subitem);
        $('#form-edit-beratbumbu-hasil').val(bumbu_berat);
        $('#form-edit-bumbuid-hasil').val(bumbu).trigger("change")

        

        console.log(customer);

        $('.select2').select2({
            theme: 'bootstrap4',
            tags: true,
            dropdownParent: $(".mymodal"),
        })

        $("#customers").val(customer).trigger('change');

        $("#bumbu").val(bumbu).trigger('change');

        if (plastik) {
            document.getElementById('dataplastik').style    =   'display:block' ;
        } else {
            document.getElementById('dataplastik').style    =   'display:none' ;
        }

        
    })

        
    </script>

    <div class="modal fade mymodal" id="hasil-edit" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="hasilLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hasilLabel">Edit Hasil Produksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('regu.editproduksi', ['key' => 'hasil_produksi']) }}" method="post">
                    @csrf @method('put')
                    <input type="hidden" name="x_code" value="" id="form-edit-id-hasil">
                    <div class="modal-body">
                        <div class="form-group">
                            Item
                            <div><b id="form-edit-nama-hasil"></b></div>
                            <div><b id="form-edit-bumbu-hasil"></b></div>
                            <input id="form-item-id" type="hidden" value="" name="item">
                        </div>

                        <div class="row">
                            <div class="col pr-1">
                                <div class="form-group">
                                    Ekor/Qty
                                    <input type="number" name="qty" value="" class="form-control" id="form-edit-qty-hasil">
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    Berat
                                    <input type="number" name="berat" value="" step="0.01" class="form-control" id="form-edit-berat-hasil" {{ isset($ns) && $ns ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div id="dataplastik" style="display: none">
                            <div class="row">
                                <div class="col pr-1">
                                    <div class="form-group">
                                        Plastik
                                        <input type="text" disabled class="form-control" id="form-edit-plastik-hasil" {{ isset($ns) && $ns ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                @if ($kategori == 'parting' || $kategori == 'marinasi')
                                <div class="col-2 px-1">
                                    <div class="form-group">
                                        Parting
                                        <input type="number" name="parting" class="form-control" id="form-edit-parting-hasil" {{ isset($ns) && $ns ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                @endif
                                <div class="col-3 pl-1">
                                    <div class="form-group">
                                        Qty
                                        <input type="number" name="jumlah_plastik" class="form-control" id="form-edit-qtyplastik-hasil" {{ isset($ns) && $ns ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col pr-1">
                                <div class="form-group">
                                    <label for="customers" {{ isset($ns) && $ns ? 'hidden' : '' }}>Nama Customer</label>
                                    <input type="hidden" class="form-control " id="form-edit-customer-hasil">
                                    <select name="customer" id="customers" class="form-control select2" data-width="100%" data-placeholder="Pilih Customer" {{ isset($ns) && $ns ? 'disabled' : '' }}>
                                        <option value=''></option>
                                        @foreach ($customer as $cus)
                                            <option value="{{ $cus->id }}">{{ $cus->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col pl-1" id="nama-bumbu">
                                <div class="form-group">
                                    Nama Bumbu
                                    {{-- <input type="text" name="keterangan" class="form-control" id="form-edit-keterangan-hasil"> --}}
                                    <select name="bumbu_id" class="form-control select2" id="form-edit-bumbuid-hasil" data-width="100%" data-placeholder="Pilih Bumbu" {{ isset($ns) && $ns ? 'disabled' : '' }}>
                                        <option value=""></option>
                                        @foreach ($bumbu as $bmb)
                                        <option value="{{ $bmb->id }}">{{ $bmb->nama }} - ({{$bmb->berat}} Kg)</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col pl-1" id="berat-bumbu">
                                <div class="form-group">
                                    Berat Bumbu
                                    <input type="text" name="bumbu_berat" class="form-control" id="form-edit-beratbumbu-hasil" {{ isset($ns) && $ns ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    Keterangan
                                    <input type="text" name="keterangan" class="form-control" id="form-edit-keterangan-hasil" {{ isset($ns) && $ns ? 'readonly' : '' }}
>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close">Close</button>
                        <button type="submit" class="btn btn-primary">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
      $(document).ready(function() {
        // Mendapatkan URL saat ini
        var currentURL = window.location.href;

        // Membuat objek URLSearchParams dari URL
        let params = new URLSearchParams(document.location.search);

        // Mengambil nilai parameter 'kategori' dari URL
        let kategori = params.get('kategori');

        // Menampilkan nilai kategori di konsol
        console.log('Kategori:', kategori);

        if(kategori == 'marinasi')
        {
            $('#nama-bumbu').show();
            $('#berat-bumbu').show();
        }else 
        {
            $('#nama-bumbu').hide();
            $('#berat-bumbu').hide();
        }
    });


    $('.edit-bb-open').on('click', function(){
        var id          =   $(this).data('id');
        var nama        =   $(this).data('nama');
        var qty         =   $(this).data('qty');
        var berat       =   $(this).data('berat');
        var chillerid   =   $(this).attr('data-chillerid');

        $.ajax({
            url : "{{ route('regu.viewmodaledit', ['key' => 'viewmodaledit']) }}",
            type: "GET",
            data: {
                id          : id,
                nama        : nama,
                qty         : qty,
                berat       : berat,
                chiller_id  : chillerid
            },
            success: function(data){
                $('#content_modal_bb_edit').html(data);
            }
        });
    })
    </script>
    <div class="modal fade" id="bb-edit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalLpahLabel" aria-hidden="false">
        <div class="modal-dialog">
            <div id="content_modal_bb_edit"></div>
        </div>
    </div>

    <style>
        .select2 {
            height: 30px !important
        }

        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2rem + 2px) !important;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: 2rem;
        }

        .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
            line-height: 0;
        }
    </style>

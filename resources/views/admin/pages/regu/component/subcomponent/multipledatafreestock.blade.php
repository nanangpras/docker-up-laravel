<div class="card mb-2">
    <div class="p-2">
        <div class="float-right">
            @if ($show)
                @if ($row->status == 2)
                    <button type="button" class="btn btn-sm btn-success approved" data-id="{{ $row->id }}">Selesaikan</button>
                @endif

                @if (!$row->netsuite)
                    <a href="{{ route('regu.index', ['kategori' => $kategori, 'produksi' => $row->id]) }}" class="btn btn-sm btn-info edit_regu">Detail</a>
                    @if ((Auth::user()->account_role == 'superadmin') || (App\Models\User::setIjin(33)))
                        <button type="button" class="btn btn-sm btn-danger removed" data-id="{{ $row->id }}">Batalkan</button>
                    @endif
                @endif
            @endif
        </div>
        <a href="{{ route('regu.index', ['kategori' => $kategori, 'produksi' => $row->id]) }}">Produksi {{date('d/m/Y', strtotime($row->tanggal))}}</a><br>
        User Input : {{ App\Models\User::find($row->user_id)->name }}
        
        @if($row->netsuite_send=="0")
            &nbsp <span class="status status-danger">TIDAK KIRIM WO</span>
        @endif
        
        @php
            if($row->netsuite){
                try {
                    echo "<span class='status status-danger'> Document No : ".$row->netsuite->document_no."</span>";
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
                            @if (!$row->netsuite)
                            <th></th>
                            @endif
                        @endif
                    </thead>
                    <tbody>
                        @php
                            $item       = 0;
                            $berat      = 0;
                        @endphp
                        @foreach ($row->listfreestock as $no => $rfs)
                        @php
                            $item                   += $rfs->qty;
                            $berat                  += $rfs->berat;
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
                                @if (!$row->netsuite)
                                <td>
                                    <i class="fa fa-edit text-primary px-1 edit-bb-open" style="cursor:pointer;" data-toggle="modal"
                                        data-nama="{{$rfs->chiller->item_name ?? ''}}" data-id="{{$rfs->id}}"
                                        data-qty="{{$rfs->qty}}" data-berat="{{$rfs->berat}}" 
                                        data-chillerid="{{ $rfs->chiller_id }}"
                                        data-target="#bb-edit">
                                    </i>
                                </td>
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
                                @if (!$row->netsuite)
                                <td></td>
                                @endif
                            @endif
                        </tr>
                        @if ($row->getHistoryDeleteList)
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
                            <th></th>
                        @endif
                        @if((Auth::user()->account_role == 'admin') && App\Models\User::setIjin(43))
                            <th></th>
                        @endif
                    </thead>
                    <tbody>
                        @php
                            $qty    = 0;
                            $berat  = 0;
                        @endphp
                        @foreach ($row->freetemp as $no => $item)
                        @php
                        $qty        += $item->qty;
                        $berat      += $item->berat;
                        $exp         = json_decode($item->label) ;
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
                                <td class="text-right">
                                    <i class="fa fa-edit text-primary px-2 edit-hasil-open" style="cursor:pointer;"
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
                            
                            @else

                                @if((Auth::user()->account_role == 'admin') && (App\Models\User::setIjin(33)) && !$row->netsuite)
                                    <td class="text-right">
                                        <i class="fa fa-edit text-primary px-2 edit-hasil-open" style="cursor:pointer;"
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
                            {{-- @if((Auth::user()->account_role == 'superadmin'))
                                    <td class="text-right">
                                        <i class="fa fa-edit text-primary px-2 edit-hasil-open" style="cursor:pointer;"
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
                            @if((Auth::user()->account_role == 'admin') && (App\Models\User::setIjin(33)) && !$row->netsuite)
                                <td class="text-right">
                                    <i class="fa fa-edit text-primary px-2 edit-hasil-open" style="cursor:pointer;"
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
                            @endif --}}
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
                                        @if ($item->customer_id) <div>Customer : {{ $item->konsumen->nama ?? $item->konsumen->nama }}</div> @endif
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
                                @if (!$row->netsuite)
                                    <td></td>
                                @endif
                            @endif
                        </tr>

                        @if ($row->getHistoryDeleteTemp)
                            <tr>
                                <th colspan="6"><a href="{{route('regu.index',['key' =>'history_delete_hp'])}}&produksi={{ $row->id }}" class="btn btn-sm btn-info" target="_blank">History Delete Hasil Produksi</a></th>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
            @php
                $total_fg       = $berat;
                $selisih        = ($total_bb-$total_fg)*(-1);
                if($total_bb > 0){
                    $presentase = (($total_bb-$total_fg)/$total_bb*100)*(-1);
                }else{
                    $presentase = 0;
                }
            @endphp

            <div class="col-sm-12">
                <hr>
                <div class="row">
                    @if($total_bb > 0 && $total_fg > 0)
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

                    @if($row->orderitem)
                        <div class="col-4">
                            <label>Input By Order</label>
                            <span class="status status-success">{{ $row->orderitem->itemorder->no_so ?? "#"}}</span>
                            {{-- <span class="status status-success">{{$row->orderitem ?? "#"}}</span> --}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
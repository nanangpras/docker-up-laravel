<table class="table default-table table-small">
    <thead>
        <th width="30%">
            <a href="{{ route('regu.index', ['kategori' => $kategori, 'produksi' => $row->id]) }}">
                Produksi {{date('d/m/Y', strtotime($row->tanggal))}}
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
            $qty    = 0;
            $berat  = 0;
        @endphp
        @if(count($row->freetemp) == 0 )
            <tr class="filter-name-harian">
                <td colspan="3"> <span class="label label-default label-inline label-md pl-1"> Tidak ada data</span></td>
                <td colspan="3" class="text-center"><a href="{{ route('regu.index', ['kategori' => $kategori, 'produksi' => $row->id]) }}" class="btn btn-danger btn-sm px-1 riwayathapus">Lihat Riwayat</a></td>
            </tr>
        @else
            @foreach ($row->freetemp as $no => $item)
                @php
                    $qty    += $item->qty;
                    $berat  += $item->berat;
                    $exp    = json_decode($item->label) ;
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
                        <br><span class="status status-success">{{ $row->orderitem->itemorder->no_so ?? "#" }}</span>
                        
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
                        @if ((!$row->netsuite) && ($row->status != 3) && (!$item->tempchiller))
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

                            @if (!$row->netsuite)
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
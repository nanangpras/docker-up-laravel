@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=$filename");
@endphp
<style>
    .text-center{
        vertical-align: middle; 
        text-align: center;
    }
    .hidden{
        display:none !important;
        visibility: hidden;
    }
    .text-left{
        text-align: left;
    }
    .float-right{
        float: right;
    }
</style>
<script>
    let clonedTable = $("#filterdata").clone();
    clonedTable.find('[style*="display:none"]').remove();
</script>
<table class="table default-table table-small table-hover" id="filterdata" border="1" width="100%">
    <thead>
        <th>Item</th>
        <th>Customer</th>
        <th>Keterangan</th>
        <th>Tanggal</th>
        <th>#</th>
        <th>Kode</th>
        <th>Packaging</th>
        <th>Ekor/Pcs/Pack</th>
        <th>Berat</th>
    </thead>
    <tbody>
        @php
            $qty = 0;
            $berat = 0;
            $totalekor = 0;
            $totalquantity = 0;
        @endphp
        @foreach ($freestock as $no => $row)
            @foreach ($row->freetemp as $no => $item)
                @php
                
                    $qty += $item->qty;
                    $berat += $item->berat;
                    $exp = json_decode($item->label);
                @endphp
                @if($filterabf == 'true' && $filterchiller == 'false')
                    <tr class="filter-name" @if($item->kategori == '0' || $item->kategori == '3') style="display:none" @endif>
                        <td>
                            {{ $item->item->nama ?? '' }}
                            <span> </span>
                            @if ($item->created_at != $item->updated_at)
                                ( EDIT )
                            @endif
                            @if ($exp->additional)
                                <div>
                                    {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                    {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                    {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                                </div>
                            @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">
                                    @if ($exp->sub_item ?? '')
                                        <div>Customer : {{ $exp->sub_item }}</div>
                                    @endif
                                </div>
                                <div class="col-auto pl-1 text-right">
                                    @if ($exp->parting->qty)
                                        Parting : {{ $exp->parting->qty }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{$item->konsumen->nama ?? ''}}</td>
                        <td class="text-center">@if ($exp->sub_item ?? '') {{ $exp->sub_item }} @endif</td>
                        <td>{{ $row->tanggal }}</td>
                        <td>
                            @if ($item->kategori == '1')
                                <span class="status status-danger">ABF </span>
                            @elseif($item->kategori == '2')
                                <span class="status status-warning">EKSPEDISI </span>
                            @else
                                <span class="status status-info">CHILLER </span>
                            @endif
                        </td>
                        <td>
                            @php 
                                $codeAbf = $item->freetempchiller->ambil_abf ?? '';
                            @endphp
                            @if($codeAbf !== '')
                                @foreach($codeAbf as $kode)
                                <div class="text-center text-secondary small">#ABF-{{ $kode->id }}</div>
                                @endforeach
                            @else
                                <div class="text-center text-secondary small">#ABF-#</div>
                            @endif
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
                    
                        </td>
                        <td class="text-center">{{ number_format($item->qty) }}</td>
                        <td class="text-center">{{ number_format($item->berat, 2) }} Kg</td>
                    </tr>
                @elseif($filterchiller == 'true' && $filterabf == 'false')
                    <tr class="filter-name" @if($item->kategori == '1') style="display:none" @endif >
                        <td >
                            {{ $item->item->nama ?? '' }}
                            <span> </span>
                            @if ($item->created_at != $item->updated_at)
                                ( EDIT )
                            @endif
                            @if ($exp->additional)
                                <div>
                                    {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                    {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                    {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                                </div>
                            @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">
                                    @if ($exp->sub_item ?? '')
                                        <div>Customer : {{ $exp->sub_item }}</div>
                                    @endif
                                </div>
                                <div class="col-auto pl-1 text-right">
                                    @if ($exp->parting->qty)
                                        Parting : {{ $exp->parting->qty }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{$item->konsumen->nama ?? ''}}</td>
                        <td class="text-center">@if ($exp->sub_item ?? '') {{ $exp->sub_item }} @endif</td>
                        <td>{{ $row->tanggal }}</td>
                        <td>
                            @if ($item->kategori == '1')
                            <span class="status status-danger">ABF </span>
                            @elseif($item->kategori == '2')
                            <span class="status status-warning">EKSPEDISI </span>
                            @else
                            <span class="status status-info">CHILLER </span>
                            @endif
                        </td>
                        <td>
                            @if($item->kategori == '0' || $item->kategori == '3' || $item->kategori == '')
                                <div class="text-center text-secondary small">#CHIL-{{ $item->freetempchiller->id ?? '#'}}</div>
                            @elseif($item->kategori == '2')
                                <div class="text-center text-secondary small"></div>
                            @endif
                        </td>
                        <td>
                            @if ($exp->plastik->jenis)
                                {{ $exp->plastik->jenis }}
                                @if ($exp->plastik->qty > 0)
                                    <span class="float-right">// {{ $exp->plastik->qty }} Pcs</span>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->qty) }}</td>
                        <td class="text-center">{{ number_format($item->berat, 2) }} Kg</td>
                    </tr>
                @elseif($filterchiller == 'true' && $filterabf == 'true' && $filterekspedisi == 'false')
                    <tr class="filter-name" @if($item->kategori == '2') style="display:none" @endif >
                        <td>
                            {{ $item->item->nama ?? '' }}
                            <span> </span>
                            @if ($item->created_at != $item->updated_at)
                                ( EDIT )
                            @endif
                            @if ($exp->additional)
                                <div>
                                    {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                    {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                    {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                                </div>
                            @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">
                                    @if ($exp->sub_item ?? '')
                                        <div>Customer : {{ $exp->sub_item }}</div>
                                    @endif
                                </div>
                                <div class="col-auto pl-1 text-right">
                                    @if ($exp->parting->qty)
                                        Parting : {{ $exp->parting->qty }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{$item->konsumen->nama ?? ''}}</td>
                        <td class="text-center">@if ($exp->sub_item ?? '') {{ $exp->sub_item }} @endif</td>
                        <td>{{ $row->tanggal }}</td>
                        <td>
                            @if ($item->kategori == '1')
                                <span class="status status-danger">ABF </span>
                                @elseif($item->kategori == '2')
                                <span class="status status-warning">EKSPEDISI </span>
                                @else
                                <span class="status status-info">CHILLER </span>
                            @endif
                        </td>
                        <td>
                            @if($item->kategori == '1')
                                @php 
                                    $codeAbf = $item->freetempchiller->ambil_abf ?? '';
                                @endphp
                                @if($codeAbf !== '')
                                    @foreach($codeAbf as $kode)
                                    <div class="text-center text-secondary small">#ABF-{{ $kode->id }}</div>
                                    @endforeach
                                @else
                                    <div class="text-center text-secondary small">#ABF-#</div>
                                @endif
                            @elseif($item->kategori == '0' || $item->kategori == '3' || $item->kategori == '')
                                <div class="text-center text-secondary small">#CHIL-{{ $item->freetempchiller->id ?? '#'}}</div>
                            @endif
                        </td>
                        <td>
                            @if ($exp->plastik->jenis)
                                {{ $exp->plastik->jenis }}
                                @if ($exp->plastik->qty > 0)
                                    <span class="float-right">// {{ $exp->plastik->qty }} Pcs</span>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->qty) }}</td>
                        <td class="text-center">{{ number_format($item->berat, 2) }} Kg</td>
                    </tr>
                @else
                    <tr class="filter-name">
                        <td>
                            {{ $item->item->nama ?? '' }}
                            <span> </span>
                            @if ($item->created_at != $item->updated_at)
                                ( EDIT )
                            @endif
                            @if ($exp->additional)
                                <div>{{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                                    {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                                    {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}</div>
                            @endif
                            <div class="row mt-1 text-info">
                                <div class="col pr-1">
                                    @if ($exp->sub_item ?? '')
                                        <div>Customer : {{ $exp->sub_item }}</div>
                                    @endif
                                </div>
                                <div class="col-auto pl-1 text-right">
                                    @if ($exp->parting->qty)
                                        Parting : {{ $exp->parting->qty }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{$item->konsumen->nama ?? ''}}</td>
                        <td class="text-center">@if ($exp->sub_item ?? '') {{ $exp->sub_item }} @endif</td>
                        <td>{{ $row->tanggal }}</td>
                        <td>
                            @if ($item->kategori == '1')
                                <span class="status status-danger">ABF </span>
                                @elseif($item->kategori == '2')
                                <span class="status status-warning">EKSPEDISI </span>
                                @else
                                <span class="status status-info">CHILLER </span>
                            @endif
                        </td>
                        <td>
                            @if($item->kategori == '1')
                                @php 
                                    $codeAbf = $item->freetempchiller->ambil_abf ?? '';
                                @endphp
                                @if($codeAbf !== '')
                                    @foreach($codeAbf as $kode)
                                    <div class="text-center text-secondary small">#ABF-{{ $kode->id }}</div>
                                    @endforeach
                                @else
                                    <div class="text-center text-secondary small">#ABF-#</div>
                                @endif
                            @elseif($item->kategori == '0' || $item->kategori == '3' || $item->kategori == '')
                                <div class="text-center text-secondary small">#CHIL-{{ $item->freetempchiller->id ?? '#'}}</div>
                            @endif
                        </td>
                        <td>
                            @if ($exp->plastik->jenis)
                                {{ $exp->plastik->jenis }}
                                @if ($exp->plastik->qty > 0)
                                    <span class="float-right">// {{ $exp->plastik->qty }} Pcs</span>
                                @endif
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->qty) }}</td>
                        <td class="text-center">{{ number_format($item->berat, 2) }} Kg</td>
                    </tr>
                @endif
                @php  
                    if($filterabf == 'true' && $filterchiller=='true' && $filterekspedisi=='true'){
                        $totalekor      += $item->qty; 
                        $totalquantity  += $item->berat; 
                    }
                    else if($filterabf == 'true' && $filterchiller=='true' && $filterekspedisi=='false'){
                        if($item->kategori == '1' || $item->kategori == '0' || $item->kategori == '3'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat; 
                        }
                    }
                    else if($filterabf == 'true' && $filterchiller == 'false' && $filterekspedisi =='false'){
                        if($item->kategori == '1'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat; 
                        }
                    }
                    else if($filterabf == 'true' && $filterchiller == 'false' && $filterekspedisi =='true'){
                        if($item->kategori == '1' || $item->kategori == '2'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat; 
                        }
                    }
                    else if($filterabf == 'false' && $filterchiller == 'true' && $filterekspedisi =='true'){
                        if($item->kategori == '2' || $item->kategori == '0' || $item->kategori == '3'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat; 
                        }
                    }
                    else if($filterabf == 'false' && $filterchiller == 'true' && $filterekspedisi =='false'){
                        if($item->kategori == '0' || $item->kategori == '3'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat; 
                        }
                    }
                    else if($filterabf == 'false' && $filterchiller == 'false' && $filterekspedisi =='true'){
                        if($item->kategori == '2'){
                            $totalekor      += $item->qty; 
                            $totalquantity  += $item->berat; 
                        }
                    }
                    else{
                        $totalekor      += $item->qty; 
                        $totalquantity  += $item->berat; 
                    }
                    
                @endphp
            @endforeach
        @endforeach
        <tr>
            <td colspan="4" class="text-center"><strong>Total Produksi</strong> </td>
            <td class="text-center"> <strong> {{ $totalekor }} </strong> </td>
            <td class="text-center"> <strong> {{ $totalquantity }} Kg</strong> </td>
        </tr>
    </tbody>
</table>
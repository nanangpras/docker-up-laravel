@php
header('Content-Transfer-Encoding: none');
header('Content-type: application/vnd-ms-excel');
header('Content-type: application/x-msexcel');
header('Content-Disposition: attachment; filename=Riwayat PR.xls');
@endphp

<style>
    th,
    td {
        border: 1px solid #ddd;
    }
</style>
<table class="table table-sm table-hover table-striped table-bordered table-small">
    <thead>
        <tr>
            <th>No</th>
            <th>No PR</th>
            <th>Dept Requestor</th>
            <th>Tanggal PR</th>
            <th>NO PO</th>
            <th>Tanggal PO</th>
            <th>SKU</th>
            <th>Item</th>
            <th>Qty PR</th>
            <th>Qty PO</th>
            <th>Qty Receive</th>
            <th>Sisa Stock</th>
            <th>Keterangan</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataDownload as $no => $row)
            @foreach ($row->list_beliDownload as $l => $list)
                        <tr>
                            <td>
                                @if ($l == 0)
                                    <a href="#{{ $row->id }}"> {{ $no + 1 }}</a>
                                @endif
                            </td>
                            <td>
                                @if ($l == 0)
                                    #{{ $row->no_pr }}
                                @endif
                            </td>
                            <td>
                                @if ($l == 0)   
                                    {{ $row->divisi }}
                                @endif
                            </td>
                            <td>
                                @if ($l == 0)
                                    {{ date('d/m/Y', strtotime($row->tanggal)) }}
                                @endif
                            </td>
                            {{-- <td>
                                @if($l==0)
                                    @if (count($row->pr_po) > 0)
                                    @foreach ($row->pr_po as $i => $po)
                                        {{ $po->document_number }}
                                    @endforeach
                                    @else
                                    -
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($l==0)
                                    @if (count($row->pr_po) > 0)
                                    @foreach ($row->pr_po as $i => $po)
                                    {{$po->tanggal}} <br>
                                    @endforeach
                                    @else
                                    -
                                    @endif
                                @endif
                            </td> --}}
                            <td>
                                @php
                                    $getParent = \App\Models\PembelianList::getParentId($list->pembelian_id, $list->id);
                                @endphp
                                @foreach($row->pr_po as $po)
                                    @if($getParent == $po->id)
                                        {{ $po->document_number }}<br>
                                    @else
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @if($l==0){{ date('d/m/Y', strtotime($row->tanggal)) }}@endif
                            </td>
                            <td>
                                {{ $list->item->sku  ?? "#ITEM DIHAPUS" }}
                            </td>
                            <td>
                                {{ $list->item->nama  ?? "#ITEM DIHAPUS" }}
                            </td>
                            <td>{{$list->qty}}</td> 
                            <td>{{App\Models\Pembelianlist::where('parent',$list->id)->sum('qty')}}</td>
                            @php
                                $pemlists = App\Models\Pembelianlist::where('item_id', $list->item_id)
                                            ->where('parent',$list->id)->get();
                                $total = 0;
                            @endphp
                            <td>
                                @foreach ($pemlists as $pemlis)
                                @php
                                    $total += App\Models\PembelianItemReceipt::where('pembelian_header_id', $pemlis->headbeli_id)->where('line_id', $pemlis->line_id)->where('item_id', $pemlis->item_id)->get()->sum('qty') 
                                @endphp
                                @endforeach
                                {{ $total}}
                            </td>
                            <td>{{$list->sisa}}</td>
                            <td>
                                {{ $list->keterangan }}
                            </td>
                            <td>{{$row->created_at}}</td>
                        </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

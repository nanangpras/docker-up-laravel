@php
header('Content-Transfer-Encoding: none');
header("Content-type: application/vnd-ms-excel");
header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=Riwayat PO.xls");
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
            <th>No PO</th>
            <th>Tanggal PO</th>
            <th>Vendor</th>
            <th>Type PO</th>
            <th>PO Status</th>
            <th>NS Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $n => $row)
        <tr>
            <td>{{ $loop->iteration + ($data->currentpage() - 1) * $data->perPage() }}</td>
            <td>
                @if ($row->no_pr)
                #PR.{{ $row->no_pr }}
                @endif
            </td>
            <td>{{$row->document_number}}</td>
            <td>{{ date('d/m/Y', strtotime($row->tanggal)) }}
            </td>
            <td>{{ $row->supplier->nama ?? '#' }}</td>
            <td>{{ $row->type_po }}</td>
            <td>
                @php
                $ns = App\Models\Netsuite::where('tabel_id', $row->id)
                ->where('record_type', 'purchase_order')
                ->first();
                @endphp

                @if ($ns)
                <a href="https://6484226-sb1.app.netsuite.com/app/accounting/transactions/purchord.nl?id={{$ns->response_id}}&whence="
                    target="_blank">{{$ns->response_id}}</a><br>
                @endif

                {{ $row->keterangan }}
            </td>
            <td>
                @if ($row->netsuite_status == '2')
                <span class="status status-info">Pending
                    Integrasi</span>
                @elseif($row->netsuite_status == '1')
                <span class="status status-warning">Netsuite
                    Terbentuk</span>
                @elseif($row->netsuite_status == '3')
                <span class="status status-success">Netsuite
                    Terkirim</span>
                @endif
            </td>
        </tr>

        <td colspan="10">
            <div>
                <div class="card-body p-1">
                    <div class="row">
                        <div class="col">
                            <b>Type PO : </b>{{ $row->type_po }}<br>
                            <b>Form PO : </b>{{ $row->form_name }}<br>
                            <b>Memo : </b>{{ $row->memo }}
                        </div>
                        <div class="col">
                            <b>Type Expedisi : </b>{{ $row->jenis_ekspedisi }}<br>
                            <b>Created by : </b> {{\App\Models\User::find($row->user_id ?? "")->name ?? ""}}
                            at {{$row->created_at ?? ""}}
                        </div>
                    </div>
                    <hr>
                    <b>PO Item</b>
                    <div class="table-responsive">
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SKU</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Void</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($row->list_pembelian as $no => $list)
                                <tr>
                                    <td>{{ $no + 1 ?? '' }}</td>
                                    <td>{{ $list->item->sku  ?? "#ITEM DIHAPUS" }}</td>
                                    <td>{{ $list->item->nama  ?? "#ITEM DIHAPUS" }}</td>
                                    <td>{{ number_format($list->qty) }}</td>
                                    <td>{{ number_format($list->harga, 2) }}</td>
                                    <td>
                                        @if ($list->deleted_at)
                                        VOID
                                        @else
                                        <span class="text-center">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-body p-1">
                    <b>PO Receipt</b>
                    @if(count($row->list_po_item_receipt)>0)
                    <div class="table-responsive">
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SKU</th>
                                    <th>Item</th>
                                    <th>Receipt Qty</th>
                                    {{-- <th>Receipt Berat</th> --}}
                                    <th>Log</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($row->list_po_item_receipt as $no => $list)
                                <tr>
                                    <td>{{ $no + 1 ?? '' }}</td>
                                    <td>{{ $list->item->sku }}</td>
                                    <td>{{ $list->item->nama }}</td>
                                    <td>{{ $list->qty }}</td>
                                    {{-- <td>{{ number_format($list->berat) }}</td> --}}
                                    <td>
                                        {{date('d/m/Y H:i:s', strtotime($list->created_at))}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="status status-danger">Belum ada Item Receipt</div>
                    @endif
                </div>
                <div class="card-body p-1 mt-2">
                    <b>Netsuite Status</b> <br>

                    @php
                    $ns = App\Models\Netsuite::where('tabel_id', $row->id)
                    ->where('record_type', 'purchase_order')
                    ->first();
                    @endphp

                    @if ($ns)
                    <hr>

                    @if (!empty($ns->failed))
                    <span class="status status-danger">
                        @php
                        //code...
                        $resp = json_decode($ns->failed);
                        @endphp

                        @if ($resp[0] ?? false)
                        Gagal : {{ $resp[0]->message ?? '' }}
                        @endif
                    </span>
                    @endif

                    @if (!empty($ns->resp_update))
                    <span class="status status-success">
                        @php
                        //code...
                        $resp = json_decode($ns->resp_update);
                        @endphp
                        @if ($resp[0] ?? false)
                        Update : {{ $resp[0]->status_document ?? '' }}
                        @endif
                    </span>
                    @endif

                    @if (!empty($ns->response))
                    <span class="status status-success">
                        @php
                        //code...
                        $resp = json_decode($ns->response);
                        @endphp
                        @if ($resp[0] ?? false)
                        Sukses : {{ $resp[0]->documentno ?? '' }}
                        @endif
                    </span>
                    @endif
                    @endif

                </div>
            </div>
        </td>
        @endforeach
    </tbody>
</table>

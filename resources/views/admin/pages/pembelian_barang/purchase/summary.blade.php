<a href="{{ route('pembelian.purchase', array_merge(['get' => 'unduh'], $_GET)) }}" class="btn btn-success btn-sm mb-2">Unduh</a>
{{-- @php
    $ukuran = ['  ', '< 1.1', ' 1.1-1.3', '1.2-1.4', '1.3-1.5', '1.4-1.6', '1.5-1.7', 
    '1.6-1.8', '1.7-1.9', ' 1.8-2.0', '1.9-2.1', '2.0-2.2', ' 2.2 Up', '1.2-1.5', '1.3-1.6', '1.5-1.8', 
    '2.0-2.5', '2.5-3.0', '3.0 Up', '1.4-1.7', '4.0 up'];
@endphp --}}
   <section class="panel">
        <div class="card-body p-2">
            <div class="table-responsive">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No PR</th>
                        <th>No PO</th>
                        <th>Tgl PO</th>
                        <th>Tgl Kirim</th>
                        <th>Vendor</th>
                        <th>Type PO</th>
                        <th>PO Status</th>
                        <th>NS Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="accordion" id="accordionSummaryPR">
                    @foreach ($data as $n => $row)
                        <tr>
                            <td>{{ $loop->iteration + ($data->currentpage() - 1) * $data->perPage() }}</td>
                            <td>
                                @if ($row->no_pr)
                                #PR.{{ $row->no_pr }}
                                @endif
                            </td>
                            <td>{{$row->document_number}}</td>
                            <td>{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
                            <td>{{ date('d/m/Y', strtotime($row->tanggal_kirim)) }}</td>
                            <td>{{ $row->supplier->nama ?? '#' }}</td>
                            <td>{{ $row->type_po }}</td>
                            <td>
                                @php
                                    $ns = App\Models\Netsuite::where('tabel_id', $row->id)
                                        ->where('record_type', 'purchase_order')
                                        ->first();
                                @endphp

                                @if ($ns)
                                    <a href="https://6484226.app.netsuite.com/app/accounting/transactions/purchord.nl?id={{$ns->response_id}}&whence=" target="_blank">{{$ns->response_id}}</a><br>
                                @endif

                                {{ $row->keterangan }}
                            </td>
                            <td>
                                @if($row->status==4)
                                    <span class="status status-danger">PO CLOSED</span>
                                @elseif($row->status==9)
                                    <span class="status status-warning">PO PENDING</span>
                                @else
                                    @if ($row->netsuite_status == '2')
                                        <span class="status status-info">Pending
                                            Integrasi</span>
                                    @elseif($row->netsuite_status == '1')
                                        <span class="status status-warning">Netsuite
                                            Terbentuk</span>
                                    @elseif($row->netsuite_status == '3')
                                        <span class="status status-success">Netsuite
                                            Terkirim</span>
                                    @elseif($row->netsuite_status == '4')
                                        <span class="status status-danger">Netsuite
                                            Gagal</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if ($row->deleted_at)
                                    <span class="status status-danger">VOID</span>
                                    <button class="btn btn-primary" data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true" aria-controls="collapse{{ $row->id }}">Expand Detail</button>
                                @else
                                    {{-- @if($ns && ($ns->status=="2" || $ns->status=="4" || $row->keterangan == "Pending Bill" || $row->keterangan == "Pending Supervisor Approval" 
                                    || $row->keterangan == "Pending Receipt" || $row->keterangan == "Fully Billed" || $row->keterangan == "Pending Billing/Partially Received"))
                                        <button class="btn btn-primary" data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true" aria-controls="collapse{{ $row->id }}">Expand Detail</button>
                                    @else --}}
                                        <button class="btn btn-primary" data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true" aria-controls="collapse{{ $row->id }}">Expand Detail</button>
                                        <a href="{{ route('pembelian.purchase', ['key' => 'detail']) }}&id={{ $row->id }}&type={{ $row->type_po }}" class="btn btn-success btn-sm" style="color: white">Edit</a>
                                    {{-- @endif --}}
                                    @php
                                        $datalog         =  App\Models\Adminedit::where('table_id', $row->id)->where('table_name', 'pembelian')->get();
                                    @endphp
                                    @if(count($datalog) > 0)
                                    <button class="btn btn-sm btn-outline-info" data-id="{{ $row->id }}" data-toggle="modal" data-target="#riwayatpo{{ $row->id }}">Riwayat Edit</button>
                                    <div class="modal fade" id="riwayatpo{{ $row->id }}" aria-labelledby="riwayatLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" style="width: 800px;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Riwayat Edit</h4>
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    @php
                                                        $json = [];
                                                        $dataedit = [];
                                                        $lists = [];
                                                    @endphp
                                                    <div class="">
                                                    <table class="table table-responsive">
                                                    @foreach ($datalog as $key => $logs)
                                                        <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Waktu Edit</th>
                                                                <th>Riwayat</th>
                                                                <th>Vendor</th>
                                                                <th>Type PO</th>
                                                                <th>Form PO</th>
                                                                <th>Jenis Ekspedisi</th>
                                                                <th>Tanggal PO</th>
                                                                <th>Tanggal Kirim</th>
                                                                <th>File</th>
                                                                <th>Franko/Loko</th>
                                                                <th>Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $json[] = json_decode($logs->data, true);
                                                                $dataedit[] = $logs->content;
                                                            @endphp
                                                                <tr>
                                                                    <td>{{ $key+1 }}</td>
                                                                    <td>{{ $logs->created_at }}</td>
                                                                    <td>{{ $logs->content }}</td>
                                                                    <td 
                                                                    > </td>
                                                                    <td 
                                                                    @if($key > 0)
                                                                        @if($json[$key]['header']['type_po'] ?? FALSE && $json[$key-1]['header']['type_po'] ?? FALSE)
                                                                            @if($json[$key]['header']['type_po'] != $json[$key-1]['header']['type_po'])
                                                                                style="background-color: #fde0dd"
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    >{{ $json[$key]['header']['type_po'] ?? "" }} </td>
                                                                    <td 
                                                                    @if($key > 0)
                                                                        @if($json[$key]['header']['form_id'] ?? FALSE && $json[$key-1]['header']['form_id'] ?? FALSE)
                                                                            @if($json[$key]['header']['form_id'] != $json[$key-1]['header']['form_id'])
                                                                                style="background-color: #fde0dd"
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    >{{ $json[$key]['header']['form_name'] ?? "" }} </td>
                                                                    <td 
                                                                    @if($key > 0)
                                                                        @if($json[$key]['header']['jenis_ekspedisi'] ?? FALSE && $json[$key-1]['header']['jenis_ekspedisi'] ?? FALSE)
                                                                            @if($json[$key]['header']['jenis_ekspedisi'] != $json[$key-1]['header']['jenis_ekspedisi'])
                                                                                style="background-color: #fde0dd"
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    >{{ $json[$key]['header']['jenis_ekspedisi'] ?? "" }} </td>
                                                                    <td 
                                                                    @if($key > 0)
                                                                        @if($json[$key]['header']['tanggal'] ?? FALSE && $json[$key-1]['header']['tanggal'] ?? FALSE)
                                                                            @if($json[$key]['header']['tanggal'] != $json[$key-1]['header']['tanggal'])
                                                                                style="background-color: #fde0dd"
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    >{{ $json[$key]['header']['tanggal'] ?? "" }} </td>
                                                                    <td 
                                                                    @if($key > 0)
                                                                        @if($json[$key]['header']['tanggal_kirim'] ?? FALSE && $json[$key-1]['header']['tanggal_kirim'] ?? FALSE)
                                                                            @if($json[$key]['header']['tanggal_kirim'] != $json[$key-1]['header']['tanggal_kirim'])
                                                                                style="background-color: #fde0dd"
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    >{{ $json[$key]['header']['tanggal_kirim'] ?? ""}} </td>
                                                                    <td 
                                                                    @if($key > 0)
                                                                        @if($json[$key]['header']['link_url'] ?? FALSE && $json[$key-1]['header']['link_url'] ?? FALSE)
                                                                            @if($json[$key]['header']['link_url'] != $json[$key-1]['header']['link_url'])
                                                                                style="background-color: #fde0dd"
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    >{{ $json[$key]['header']['link_url'] ?? "-"}} </td>
                                                                    <td 
                                                                    @if($key > 0)
                                                                        @if($json[$key]['header']['franco_loco'] ?? FALSE && $json[$key-1]['header']['franco_loco'] ?? FALSE)
                                                                            @if($json[$key]['header']['franco_loco'] != $json[$key-1]['header']['franco_loco'])
                                                                                style="background-color: #fde0dd"
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    >{{ $json[$key]['header']['franco_loco'] ?? "-"}} </td>
                                                                    
                                                                    <td
                                                                    @if($key > 0)
                                                                        @if($json[$key]['header']['memo'] ?? FALSE && $json[$key-1]['header']['memo'] ?? FALSE)
                                                                            @if($json[$key]['header']['memo'] != $json[$key-1]['header']['memo'])
                                                                                style="background-color: #fde0dd"
                                                                            @endif
                                                                        @endif
                                                                    @endif
                                                                    >{{ $json[$key]['header']['memo'] ?? '-' }} </td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="12">
                                                                        <div>
                                                                            <table class="table default-table">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>SKU</th>
                                                                                    <th>Item</th>
                                                                                    <th>Keterangan</th>
                                                                                    <th>Gudang</th>
                                                                                    <th>Qty</th>
                                                                                    <th>Harga</th>
                                                                                    <th>Total</th>
                                                                                </tr>
                                                                            </thead>
                                                                                <tbody>
                                                                                    @php
                                                                                        $total_hargalog = 0;
                                                                                        $total_beratlog = 0;
                                                                                        $total_qtylog = 0;
                                                                                        $sumtotallog = 0;
                                                                                    @endphp
                                                                                    @for($i = 0; $i < count($json[$key]['list']); $i++)

                                                                                    {{-- @if($json[$key]['list'][$i]['keterangan'] ?? FALSE) --}}
                                                                                    <tr @if($json[$key]['list'][$i]['deleted_at'] && $logs->content !== 'Data Awal (Original)') style="background-color:#fde0dd; color: #f44336" @endif
                                                                                        @if($key > 0)
                                                                                            @if(!isset($json[$key-1]['list'][$i])) style="background-color: #87CEFA" @endif
                                                                                        @endif
                                                                                    >
                                                                                        <td>{{ App\Models\Item::logso('sku',$json[$key]['list'][$i]['item_id'] ?? 0) }}</td>
                                                                                        <td>{{ App\Models\Item::logso('nama',$json[$key]['list'][$i]['item_id'] ?? 0) }}</td>
                                                                                        <td 
                                                                                        @if($json[$key-1]['list'][$i]['keterangan'] ?? FALSE && $json[$key]['list'][$i]['keterangan'] ?? FALSE)
                                                                                            @if($json[$key]['list'][$i]['keterangan'] != $json[$key-1]['list'][$i]['keterangan'])
                                                                                                style="background-color: #fde0dd"
                                                                                            @endif
                                                                                        @endif
                                                                                        >{{ $json[$key]['list'][$i]['keterangan'] ?? "" }}</td>
                                                                                        <td></td>
                                                                                        <td 
                                                                                        @if($json[$key-1]['list'][$i]['qty'] ?? FALSE && $json[$key]['list'][$i]['qty'] ?? FALSE)
                                                                                            @if($json[$key]['list'][$i]['qty'] != $json[$key-1]['list'][$i]['qty'])
                                                                                                style="background-color: #fde0dd"
                                                                                            @endif
                                                                                        @endif
                                                                                        class="text-right">{{ $json[$key]['list'][$i]['qty'] ?? "" }}</td>
                                                                                        <td 
                                                                                        @if($json[$key-1]['list'][$i]['harga'] ?? FALSE && $json[$key]['list'][$i]['harga'] ?? FALSE)
                                                                                            @if($json[$key]['list'][$i]['harga'] != $json[$key-1]['list'][$i]['harga'])
                                                                                                style="background-color: #fde0dd"
                                                                                            @endif
                                                                                        @endif
                                                                                        class="text-right">
                                                                                        @php 
                                                                                            $hargalog =  ($json[$key]['list'][$i]['harga'] ?? 0) * ($json[$key]['list'][$i]['qty'] ?? 0);
                                                                                            
                                                                                            if (!$json[$key]['list'][$i]['deleted_at'] || $logs->content == 'Data Awal (Original)'){
                                                                                                $total_qtylog      += $json[$key]['list'][$i]['qty'] ?? 0;
                                                                                                $total_hargalog    += $json[$key]['list'][$i]['harga'] ?? 0;
                                                                                                $sumtotallog       += ($json[$key]['list'][$i]['harga'] ?? 0) * ($json[$key]['list'][$i]['qty'] ?? 0);
                                                                                            }
                                                                                        @endphp
                                                                                        Rp {{ number_format($json[$key]['list'][$i]['harga']) ?? ""}}
                                                                                        </td>
                                                                                        <td
                                                                                        @if($json[$key-1]['list'][$i]['harga'] ?? FALSE && $json[$key]['list'][$i]['harga'] ?? FALSE && $json[$key]['list'][$i]['qty'] ?? FALSE && $json[$key-1]['list'][$i]['qty'] ?? FALSE )
                                                                                            @if($json[$key]['list'][$i]['harga'] != $json[$key-1]['list'][$i]['harga'] || $json[$key]['list'][$i]['qty'] != $json[$key-1]['list'][$i]['qty'])
                                                                                                style="background-color: #fde0dd"
                                                                                            @endif
                                                                                        @endif
                                                                                        class="text-right">
                                                                                            Rp {{ number_format(($json[$key]['list'][$i]['harga'] ?? 0) * $json[$key]['list'][$i]['qty'] ?? 0) }}
                                                                                        </td>
                                                                                    </tr>
                                                                                    {{-- @endif --}}
                                                                                    @endfor
                                                                                    <tr>
                                                                                        <td>Total</td>
                                                                                        <td colspan="4" class="text-right">{{ $total_qtylog }}</td>
                                                                                        <td colspan="1" class="text-right">Rp {{ number_format($total_hargalog) }}</td>
                                                                                        <td colspan="1" class="text-right">Rp {{ number_format($sumtotallog) }}</td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            @endforeach
                                                        </table>
                                                        </div>
                                                    <hr>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                    @if ($row->document_number)
                                    <button class="btn btn-sm btn-outline-danger batal_header" data-id="{{ $row->id }}">Batal</button>
                                    @endif
                                @endif
                            </td>
                        </tr>

                        <td colspan="10">
                            <div id="collapse{{ $row->id }}" class="collapse" aria-labelledby="headingOne"
                                data-parent="#accordionSummaryPR">
                                <div class="card-body p-1">
                                    <div class="row">
                                        <div class="col">
                                            <b>Type PO : </b>{{ $row->type_po }}<br>
                                            <b>Form PO : </b>{{ $row->form_name }}<br>
                                            <b>Memo : </b>{{ $row->memo }}
                                        </div>
                                        <div class="col">
                                            <b>Type Expedisi : </b>{{ $row->jenis_ekspedisi }}<br>
                                            <b>Created by : </b> {{\App\Models\User::find($row->user_id ?? "")->name ?? ""}} at {{$row->created_at ?? ""}}<br>
                                            <b>Link : </b> <a href="{{ $row->link_url ?? ""}}">{{ $row->link_url ?? ""}}</a><br>
                                            @php
                                            $datalogterakhir =  App\Models\Adminedit::where('table_id', $row->id)->where('table_name', 'pembelian')->latest('created_at')->first();
                                            if($datalogterakhir){
                                                $dataexplode     =  explode(' ', $datalogterakhir->content);
                                            }
                                            @endphp

                                            @if($datalogterakhir)
                                            <b>{{ $datalogterakhir->content }}</b>
                                            @endif
                                            
                                        </div>
                                    </div>
                                    <hr>
                                    {{-- <button class="btn btn-success text-left status_pembatalan mb-2">Edit Status Pembatalan</button> --}}
                                    {{-- <br> --}}
                                    <b>PO Item</b>
                                    <div class="table-responsive">
                                    <table class="table default-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center"><input type="checkbox" class="checkAllItem" data-id="{{ $row->id }}"></th>
                                                <th>No</th>
                                                <th>ID</th>
                                                <th>SKU</th>
                                                <th>Item</th>
                                                <th>Keterangan</th>
                                                <th>Gudang</th>
                                                @if($row->type_po!="PO LB")
                                                <th>Qty</th>
                                                <th>Unit</th>
                                                @endif
                                                <th>Harga</th>
                                                @if($row->type_po!="PO LB")
                                                <th>Total</th>
                                                @endif
                                                @if($row->type_po=="PO LB" || $row->type_po=="PO Non Karkas" || $row->type_po=="PO Karkas" || $row->type_po=="PO Evis")
                                                <th>Ekspedisi</th>
                                                <th>Jumlah DO</th>
                                                <th>Ukuran Ayam</th>
                                                <th>Ekor DO</th>
                                                <th>Berat DO</th>
                                                @endif
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $qty        =   0 ;
                                                $harga      =   0 ;
                                                $jumlah_do  =   0 ;
                                                $ekor_qty   =   0 ;
                                                $ekor_berat =   0 ;
                                                $total_harga =  0 ;
                                            @endphp
                                            @foreach ($row->list_pembelian as $no => $list)
                                            @php
                                                if(!$list->deleted_at):
                                                $qty        +=   $list->qty ;
                                                $harga      +=   $list->harga ;
                                                $jumlah_do  +=   $list->jumlah_do ;
                                                $ekor_qty   +=   $list->qty ;
                                                $ekor_berat +=   $list->berat ;
                                                $total_harga += $list->harga * $list->qty ;
                                                endif;
                                            @endphp
                                                <tr
                                                @if ($list->deleted_at)
                                                    style="background-color:#fde0dd; color: #f44336"
                                                @endif
                                                        >
                                                    @if($list->deleted_at)
                                                    <td class="text-center"><input type="checkbox" class="checkItem{{ $row->id }}" value="{{ $list->id }}" name="idbatal[]"></td>
                                                    @else
                                                    <td></td>
                                                    @endif
                                                    <td>{{ $no + 1 ?? '' }}</td>
                                                    <td>#{{ $list->id }}_{{ $list->line_id }}</td>
                                                    <td>{{ $list->item->sku ?? "#" }}</td>
                                                    <td>{{ $list->item->nama ?? "#" }}</td>
                                                    <td>{{ $list->keterangan }}</td>
                                                    <td>{{ App\Models\Gudang::gudang_code($list->gudang) }}</td>
                                                    @if($row->type_po!="PO LB")
                                                    <td class="text-right">{{ number_format($list->qty) }}</td>
                                                    <td class="text-right">{{ $list->unit }}</td>
                                                    @endif
                                                    <td class="text-right">{{ number_format($list->harga, 2) }}</td>
                                                    @if($row->type_po != "PO LB")
                                                    <td class="text-right">{{ number_format($list->harga*$list->qty, 2) }}</td>
                                                    @endif
                                                    @if($row->type_po=="PO LB" || $row->type_po=="PO Non Karkas" || $row->type_po=="PO Karkas" || $row->type_po=="PO Evis")
                                                    <th>{{$row->jenis_ekspedisi}}</th>
                                                    <th class="text-right">{{$list->jumlah_do}}</th>

                                                    @if ($list->ukuran_ayam == 1)
                                                        
                                                        <th> < 1.1 </th>

                                                    @elseif ($list->ukuran_ayam == 2)

                                                        <th>1.1-1.3</th>
                                                            
                                                    @elseif ($list->ukuran_ayam == 3)
                                                    
                                                        <th>1.2-1.4</th>

                                                    @elseif ($list->ukuran_ayam == 4)
                                                    
                                                        <th>1.3-1.5</th>

                                                    @elseif ($list->ukuran_ayam == 5)
                                                    
                                                        <th>1.4-1.6</th>

                                                    @elseif ($list->ukuran_ayam == 6)
                                                    
                                                        <th>1.5-1.7</th>

                                                    @elseif ($list->ukuran_ayam == 7)
                                                        <th>1.6-1.8</th>

                                                    @elseif ($list->ukuran_ayam == 8)
                                                    
                                                        <th>1.7-1.9</th>

                                                    @elseif ($list->ukuran_ayam == 9)
                                                    
                                                        <th>1.8-2.0</th>

                                                    @elseif ($list->ukuran_ayam == 10)
                                                    
                                                        <th>1.9-2.1</th>

                                                    @elseif ($list->ukuran_ayam == 15)
                                                    
                                                        <th>1.3-1.6</th>

                                                    @elseif ($list->ukuran_ayam == 16)
                                                    
                                                        <th>1.4-1.7</th>
                                                        
                                                    @elseif ($list->ukuran_ayam == 17)
                                                    
                                                        <th>1.5-1.8</th>

                                                    @elseif ($list->ukuran_ayam == 18)

                                                        <th>2.0-2.5</th>

                                                    @elseif ($list->ukuran_ayam == 19)

                                                        <th>2.5-3.0</th>

                                                    @elseif ($list->ukuran_ayam == 20)

                                                        <th>3.0 Up</th>

                                                    @elseif ($list->ukuran_ayam == 21)

                                                        <th>4.0 Up</th>

                                                    @endif
                                                    
                                                    <th class="text-right">{{number_format($list->qty) }}</th>
                                                    <th class="text-right">{{number_format($list->berat, 2) }}</th>
                                                    @endif
                                                    <td>
                                                        @if ($list->deleted_at)
                                                            VOID
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="7"></th>
                                                @if($row->type_po!="PO LB")
                                                <th class="text-right">{{ number_format($qty) }}</th>
                                                <th></th>
                                                @endif
                                                <th class="text-right">{{ number_format($harga, 2) }}</th>
                                                @if($row->type_po!="PO LB")
                                                <th class="text-right">{{ number_format($total_harga, 2) }}</th>
                                                @endif
                                                @if($row->type_po=="PO LB" || $row->type_po=="PO Non Karkas" || $row->type_po=="PO Karkas" || $row->type_po=="PO Evis")
                                                <th></th>
                                                <th class="text-right">{{ number_format($jumlah_do) }}</th>
                                                <th></th>
                                                <th class="text-right">{{number_format($ekor_qty) }}</th>
                                                <th class="text-right">{{number_format($ekor_berat, 2) }}</th>
                                                @endif
                                            </tr>
                                        </tfoot>
                                    </table>
                                    </div>
                                </div>
                                @if($row->type_po!="PO LB")
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
                                                    <td>{{ $list->item->sku ?? "#ITEM DIHAPUS" }}</td>
                                                    <td>{{ $list->item->nama ?? "#ITEM DIHAPUS" }}</td>
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
                                @endif
                                <div class="card-body p-1 mt-2">
                                    <b>Netsuite Status</b> <br>

                                    @if ($ns)
                                        <hr>

                                        @php
                                        //code...
                                            $resp = json_decode($ns->failed);
                                        @endphp

                                        @if(is_array($resp))
                                        {{-- FAILED : {{ $resp[0]->message ?? '' }} <br>    --}}
                                        @if ($resp[0] ?? FALSE)
                                        {{-- FAILED : {{ $resp[0]->message ?? '' }} <br>    --}}
                                        @endif
                                        @else 
                                        FAILED : {{$ns->failed}}
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
                                    @if (empty($row->netsuite_status))
                                        <hr>
                                        <form method="POST" action="{{ route('pembelian.purchasestore') }}">
                                            @csrf
                                            <input name="id" type="hidden" value="{{ $row->id }}">
                                            <input name="key" type="hidden" value="proses_netsuite">
                                            <button type="submit" class="btn btn-green btn-sm">Approve
                                                Integrasi</button>
                                        </form>
                                    @endif

                                    @if ($row->netsuite_status == '2')
                                        <hr>
                                        <form method="POST" action="{{ route('pembelian.purchasestore') }}">
                                            @csrf
                                            <input name="id" type="hidden" value="{{ $row->id }}">
                                            <input name="key" type="hidden" value="proses_netsuite">
                                            <button type="submit" class="btn btn-green btn-sm">Update Integrasi</button>
                                        </form>
                                    @endif


                                    @if($ns)
                                        @if(User::setIjin('superadmin'))
                                        <hr>

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
                                                @if($ns ?? false)
                                                @include('admin.pages.log.netsuite_one', ($netsuite = $ns))
                                                @endif

                                            </tbody>
                                        </table>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </td>
                    @endforeach
                </tbody>
            </table>
            </div>
            <div id="paginateSummaryPO">
                {{ $data->appends($_GET)->onEachSide(1)->links() }}
            </div>
        </div>
    </section>
    {{-- @endforeach --}}



    <div class="modal fade" id="editPurchase" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="row ">
                                <input type="hidden" id="idPurchase">
                                <div class="col-12 mt-3">
                                    <input type="hidden" id="itemPurchase" name="itemPurchase" required>
                                    Qty
                                    <input type="number" required name="qty" id="qtyPurchase" class="form-control px-2"
                                        placeholder="Qty" autocomplete="off">
                                </div>
                                <div class="col-12 mt-3">
                                    Harga
                                    <input type="number" required name="harga" id="hargaPurchase"
                                        class="form-control px-2" placeholder="Harga" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="updatePurchase">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $('.select2').select2({
            theme: 'bootstrap4',
        })
    </script>
    <script>
        $('#paginateSummaryPO .pagination a').on('click', function(e) {
            e.preventDefault();
            showNotif('Menunggu');

            url = $(this).attr('href');
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#data_summary').html(response);
                }

            });
        });
        </script>
    <script>
        function editPurchase(id) {
            // console.log(id)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('pembelian.purchasestore') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    key: 'get_list',
                },
                success: function(data) {
                    console.log(data)
                    $('#idPurchase').val(data.id)
                    $('#itemPurchase').val(data.item_id).trigger('change')
                    $('#qtyPurchase').val(data.qty)
                    $('#hargaPurchase').val(data.harga)
                }
            })
        }

        $('#updatePurchase').on('click', function(e) {
            e.preventDefault()
            $.ajax({
                url: "{{ route('pembelian.purchasestore') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: $('#idPurchase').val(),
                    key: 'updatePurchaseList',
                    item_id: $('#itemPurchase').val(),
                    qty: $('#qtyPurchase').val(),
                    harga: $('#hargaPurchase').val(),
                },
                success: function(data) {
                    console.log(data)
                    if (data.status == '200') {
                        showNotif(data.msg);
                        $('#editPurchase').modal('hide')
                        loadSummary();

                    }
                }
            })
        })

        $(".hapus_item").on('click', function() {
            var id = $(this).data('id');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('pembelian.purchasestore') }}",
                method: "POST",
                data: {
                    id: id,
                    key: 'hapus_item',
                },
                success: function(data) {
                    if (data.status == 400) {
                        showAlert(data.msg);
                    } else {
                        showNotif(data.msg);
                        loadSummary();
                    }
                }
            });
        })

        var tanggal_mulai = $("#tanggal_mulai").val();
        var tanggal_akhir = $("#tanggal_akhir").val();

        function loadSummary() {
            $("#loading_summary").attr('style', 'display: block');
            $("#data_summary").load("{{ route('pembelian.purchase', ['key' => 'summary']) }}&tanggal_mulai=" +
                tanggal_mulai + "&tanggal_akhir=" + tanggal_akhir,
                function() {
                    $("#loading_summary").attr('style', 'display: none');
                });
        }


        $(document).ready(function() {
            $('.checkAllItem').click(function() {
                if ($(this).is(':checked')) {
                    $('.checkItem'+ $(this).attr('data-id')).prop('checked', true);
                } else {
                    $('.checkItem' + $(this).attr('data-id')).prop('checked', false);
                }
            });
        })
        $('.status_pembatalan').on('click', function() {

            let val = [];
            $('input[name="idbatal[]"]:checked').each(function(i){
                val[i] = $(this).val();
            });
            let id = val
            if(id.length > 0) {
                let result = confirm("Apakah yakin membatalkan penghapusan item PO?");
                if (result === true) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('pembelian.purchasestore') }}",
                        method: "POST",
                        data: {
                            id: id,
                            key: 'batal_hapus_po',
                        },
                        success: function(data) {
                            console.log(data)
                            if (data.status == 400) {
                                showAlert(data.message);
                            } else {
                                showNotif(data.message);
                                loadSummary();
                            }
                        }
                    });
                } else {
                    return false;
                }
            } else {
                showAlert('Tidak ada item yang dipilih')
            }

        })
    </script>


<script>
$(".batal_header").on('click', function() {
    let result = confirm("Apakah yakin ingin membatalkan PO?");
    var id  =   $(this).data('id') ;
    if (result === true) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".batal_header").hide() ;

        $.ajax({
            url: "{{ route('pembelian.purchasestore') }}",
            method: "POST",
            data: {
                id  :   id ,
                key :   'batal_header' ,
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    loadSummary();
                }
                $(".batal_header").show() ;
            }
        });
    } else {
        return false;
    }
    
})
</script>

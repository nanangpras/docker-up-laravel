<div class="table-responsive">
    <table class="table default-table" id="tableex">
        <thead>
            @if ($type == 'itemreceipt')
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Label</th>
                    <th>Tanggal</th>
                    <th>Activity</th>
                    <th>LocID</th>
                    <th>Location</th>
                    <th>IntID</th>
                    <th>Paket</th>
                    <th>DocumentNo</th>
                    <th>Internal Id</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                    <th>Update</th>
                    <th>No PO</th>
                    <th>Tanggal Nota</th>
                    <th>Item</th>
                    <th>Berat</th>
                    <th>Qty</th>
                    <th>Location</th>
                    <th>Response</th>
                    <th>Failed</th>
                    <th>Data</th>
                </tr>
            @endif
            @if ($type == 'itemfulfill')
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Label</th>
                    <th>Tanggal</th>
                    <th>Activity</th>
                    <th>LocID</th>
                    <th>Location</th>
                    <th>IntID</th>
                    <th>Paket</th>
                    <th>DocumentNo</th>
                    <th>Internal Id</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                    <th>Update</th>
                    <th>No SO</th>
                    <th>Tanggal DO</th>
                    <th>Memo</th>
                    <th>Item</th>
                    <th>Berat</th>
                    <th>Qty</th>
                    <th>Location</th>
                    <th>Response</th>
                    <th>Failed</th>
                    <th>Data</th>
                </tr>
            @endif
            @if ($type == 'return')
                <tr>
                    {{-- <th>No</th> --}}
                    <th>ID</th>
                    <th>Label</th>
                    <th>Tanggal</th>
                    <th>Activity</th>
                    <th>LocID</th>
                    <th>Location</th>
                    <th>IntID</th>
                    <th>Paket</th>
                    <th>DocumentNo</th>
                    <th>Internal Id</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                    <th>Update</th>
                    {{-- <th>No PO</th> --}}
                    <th>Tanggal RA</th>
                    <th>Memo</th>
                    <th>Item</th>
                    <th>Berat</th>
                    <th>Qty</th>
                    <th>Location</th>
                    <th>Response</th>
                    <th>Failed</th>
                    <th>Data</th>
                </tr>
            @endif
            @if ($type == 'transfer_inventory')
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Label</th>
                    <th>Tanggal</th>
                    <th>Activity</th>
                    <th>LocID</th>
                    <th>Location</th>
                    <th>IntID</th>
                    <th>Paket</th>
                    <th>DocumentNo</th>
                    <th>Internal Id</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                    <th>Update</th>
                    <th>Tanggal</th>
                    <th>Memo</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Item</th>
                    <th>Berat</th>
                    <th>Response</th>
                    <th>Failed</th>
                    <th>Data</th>
                </tr>
            @endif
            @if ($type == 'transfer_inventory_do')
                <tr>
                    <th>Tanggal</th>
                    <th>DocumentNo</th>
                    <th>Item</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>NO SO</th>
                </tr>
            @endif
            @if ($type == 'gudang_lb')
                <tr>
                    <th>Tanggal</th>
                    <th>Dokumen Terkait</th>
                    <th>Item</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                </tr>
            @endif

            @if ($type == 'gudang_retur')
            <tr>
                <th>Tanggal</th>
                <th>DocumentNo</th>
                <th>Item</th>
                <th>From</th>
                <th>To</th>
                <th>Masuk</th>
                <th>Keluar</th> 
                <th>Dokumen Terkait</th>
            </tr>
            @endif
            
            @if ($type == '')
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Label</th>
                    <th>Item</th>
                    <th>Tanggal</th>
                    <th>Activity</th>
                    <th>IDLoc</th>
                    <th>Location</th>
                    <th>IntID</th>
                    <th>Paket</th>
                    <th>DocumentNo</th>
                    <th>Data</th>
                    <th>Response</th>
                    <th>Internal Id</th>
                    <th>Failed</th>
                    <th>Status</th>
                </tr>
            @endif

            @if ($type == 'wo' ||
                $type == 'wo1' ||
                $type == 'wo2' ||
                $type == 'wo3' ||
                $type == 'wo4' ||
                $type == 'wo5' ||
                $type == 'wo6' ||
                $type == 'wo7')
                <tr>
                    <th>No</th>
                    <th>ID</th>
                    <th>Label</th>
                    <th>Tanggal</th>
                    <th>Activity</th>
                    <th>LocID</th>
                    <th>Location</th>
                    <th>IntID</th>
                    <th>Paket</th>
                    <th>DocumentNo</th>
                    <th>Internal Id</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                    <th>Update</th>
                    <th>Tanggal</th>
                    <th>Assembly ID</th>
                    <th>Assembly</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Internal ID Item</th>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Berat</th>
                    <th>Response</th>
                    <th>Failed</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($netsuite as $no => $task)
                @php
                    $row['No'] = ++$no;
                    $row['ID'] = $task->id;
                    $row['Label'] = $task->label;
                    $row['trans_date'] = $task->trans_date;
                    $row['Activity'] = $task->record_type;
                    $row['DocumentCode'] = $task->document_code;
                    $row['IDLoc'] = $task->id_location;
                    $row['Location'] = $task->location;
                    $row['IntID'] = $task->tabel_id;
                    $row['Paket'] = $task->paket_id;
                    $row['DocumentNo'] = $task->document_no;
                    $row['Data'] = $task->data_content;
                    $row['ResponseId'] = $task->response_id;
                    $row['Response'] = $task->response;
                    $row['Failed'] = $task->failed;
                    $row['Status'] = $task->status;
                    $row['Timestamp'] = $task->created_at;
                    $row['Update'] = $task->updated_at;
                    
                    $ext = json_decode($task->data_content);
                       
                @endphp

                @if ($type == "")
                    @php
                        $qty = $ext->data[0]->line[0]->qty ?? "";
                        if($row['Status'] == 1){
                            $status = "Success";
                        } else if ($row['Status'] == 2){
                            $status =  "Pending";
                        } elseif ($row['Status'] == 3) {
                            $status = "Batal";
                        } elseif ($row['Status'] == 4) {
                            $status = "Antrian";
                        } elseif ($row['Status'] == 5) {
                            $status = "Approval";
                        } else {
                            $status = "Gagal";
                        }
                    @endphp
                    <tr>
                        <td>{{ $row['No'] }}</td>
                        <td>{{ $row['ID'] }}</td>
                        <td>{{ $row['DocumentNo'] ?? $row['Label'] }}</td>
                        <td>
                            @if (!empty($ext->data[0]->items))
                                @foreach ($ext->data[0]->items as $item)
                                    @if (count($ext->data[0]->items) > 1)
                                        @if ($ext->record_type == 'work_order')
                                            {{$item->description ?? ""}}
                                            <hr>
                                        @elseif ($ext->record_type == "wo_build")
                                            {{$item->description ?? ""}}
                                                <hr>
                                        @else
                                            {{$item->item ?? ""}}
                                        <hr>

                                        @endif
                                    @else
                                        {{$item->item ?? ""}}
                                    @endif
                                @endforeach
                            @endif
                            @if (!empty($ext->data[0]->line))
                                @foreach ($ext->data[0]->line as $ti_item)
                                    @if ($ext->record_type == 'transfer_inventory')
                                        @if (isset($ti_item->item))
                                            @php
                                                $data_item = App\Models\Item::where('sku', $ti_item->item)->get();
                                            @endphp
                                            @foreach ($data_item as $data)
                                                {{ $data->nama }}
                                            @endforeach
                                        @endif
                                    @endif
                                    @if ($ext->record_type == 'itemreceipt')
                                        @if (isset($ti_item->item_code))
                                            @php
                                                $data_item = App\Models\Item::where('sku', $ti_item->item_code)->get();
                                            @endphp
                                            @foreach ($data_item as $data)
                                                {{ $data->nama }}
                                            @endforeach
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>{{ $row['trans_date'] }}</td>
                        <td>{{ $row['Activity'] }}</td>
                        <td>{{ $row['IDLoc'] }}</td>
                        <td>{{ $row['Location'] }}</td>
                        <td>{{ $row['IntID'] }}</td>
                        <td>{{ $row['Paket'] }}</td>
                        <td>{{ $row['DocumentNo'] }}</td>
                        <td>{{ $row['Data'] }}</td>
                        <td>{{ $row['Response'] }}</td>
                        <td>{{ $row['ResponseId'] }}</td>
                        <td>{{ $row['Failed'] }}</td>
                        <td>{{ $status }}</td>
                    </tr>
                @endif

                @if ($type == 'itemreceipt')
                    @php
                        $qty = $ext->data[0]->line[0]->qty;
                    @endphp
                    <tr>
                        <td>{{ $row['No'] }}</td>
                        <td>{{ $row['ID'] }}</td>
                        <td>{{ $row['DocumentNo'] ?? $row['Label'] }}</td>
                        <td>{{ $row['trans_date'] }}</td>
                        <td>{{ $row['Activity'] }}</td>
                        <td>{{ $row['IDLoc'] }}</td>
                        <td>{{ $row['Location'] }}</td>
                        <td>{{ $row['IntID'] }}</td>
                        <td>{{ $row['Paket'] }}</td>
                        <td>{{ $row['DocumentNo'] }}</td>
                        <td>{{ $row['ResponseId'] }}</td>
                        <td>{{ $row['Status'] }}</td>
                        <td>{{ $row['Timestamp'] }}</td>
                        <td>{{ $row['Update'] }}</td>
                        <td>{{ $ext->data[0]->po_number }}</td>
                        <td>{{ $ext->data[0]->tanggal_nota }}</td>
                        <td>{{ $ext->data[0]->line[0]->item_code }}</td>
                        <td>{{ $qty }}</td>
                        <td>{{ $ext->data[0]->line[0]->qty_in_ekor }}</td>
                        <td>{{ $ext->data[0]->line[0]->gudang }}</td>
                        <td>{{ $row['Response'] }}</td>
                        <td>{{ $row['Failed'] }}</td>
                        <td>{{ $row['Data'] }}</td>
                    </tr>
                @endif

                @if ($type == 'itemfulfill')
                    @if (!empty($ext->data[0]->items))
                        @foreach ($ext->data[0]->items as $it)
                            <tr>
                                <td>{{ $row['No'] }}</td>
                                <td>{{ $row['ID'] }}</td>
                                <td>{{ $row['DocumentNo'] ?? $row['Label'] }}</td>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['Activity'] }}</td>
                                <td>{{ $row['IDLoc'] }}</td>
                                <td>{{ $row['Location'] }}</td>
                                <td>{{ $row['IntID'] }}</td>
                                <td>{{ $row['Paket'] }}</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ $row['ResponseId'] }}</td>
                                <td>{{ $row['Status'] }}</td>
                                <td>{{ $row['Timestamp'] }}</td>
                                <td>{{ $row['Update'] }}</td>
                                <td>{{ $ext->data[0]->so_number }}</td>
                                <td>{{ $ext->data[0]->date_so }}</td>
                                <td>{{ $ext->data[0]->memo }}</td>
                                <td>{{ $it->item }}</td>
                                <td>{{ $it->qty }}</td>
                                <td>{{ $it->qty_in_ekr_pcs_pack }}</td>
                                <td>{{ $it->gudang }}</td>
                                <td>{{ $row['Response'] }}</td>
                                <td>{{ $row['Failed'] }}</td>
                                <td>{{ $row['Data'] }}</td>
                            </tr>
                        @endforeach    
                    @endif
                    
                @endif
                @if ($type == 'return')
                    @if ($ext->data[0]->items ?? false)
                        @foreach ($ext->data[0]->items as $it)
                            <tr>
                                {{-- <td>{{ $row['No'] }}</td> --}}
                                <td>{{ $row['ID'] }}</td>
                                <td>{{ $row['DocumentNo'] ?? $row['Label'] }}</td>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['Activity'] }}</td>
                                <td>{{ $row['IDLoc'] }}</td>
                                <td>{{ $row['Location'] }}</td>
                                <td>{{ $row['IntID'] }}</td>
                                <td>Parent</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ $row['ResponseId'] }}</td>
                                <td>{{ $row['Status'] }}</td>
                                <td>{{ $row['Timestamp'] }}</td>
                                <td>{{ $row['Update'] }}</td>
                                {{-- <td>{{ $ext->data[0]->nomor_po }}</td> --}}
                                <td>{{ $ext->data[0]->tanggal_ra }}</td>
                                <td>{{ $ext->data[0]->memo }}</td>
                                <td>{{ App\Models\Item::where('sku', $it->sku)->first()->nama }}</td>
                                <td>{{ $it->qty }}</td>
                                <td>{{ $it->qty_in_ekr_pcs_pack }}</td>
                                <td>{{ App\Models\Gudang::where('netsuite_internal_id', $it->internal_id_gudang)->first()->code ?? '#' }}
                                </td>
                                <td>{{ $row['Response'] }}</td>
                                <td>{{ $row['Failed'] }}</td>
                                <td>{{ $row['Data'] }}</td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($ext->data[0]->line as $it)
                            <tr>
                                {{-- <td>{{ $row['No'] }}</td> --}}
                                <td>{{ $row['ID'] }}</td>
                                <td>{{ $row['DocumentNo'] ?? $row['Label'] }}</td>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['Activity'] }}</td>
                                <td>{{ $row['IDLoc'] }}</td>
                                <td>{{ $row['Location'] }}</td>
                                <td>{{ $row['IntID'] }}</td>
                                <td>{{ $row['Paket'] }}</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ $row['ResponseId'] }}</td>
                                <td>{{ $row['Status'] }}</td>
                                <td>{{ $row['Timestamp'] }}</td>
                                <td>{{ $row['Update'] }}</td>
                                {{-- <td>{{  '#' }}</td> --}}
                                <td>{{ $ext->data[0]->date }}</td>
                                <td>{{ $ext->data[0]->memo }}</td>
                                <td>{{ App\Models\Item::where('sku', $it->item_code)->first()->nama }}</td>
                                <td>{{ $it->qty }}</td>
                                <td>{{ $it->qty_in_ekor }}</td>
                                <td>{{ $it->gudang }}</td>
                                <td>{{ $row['Response'] }}</td>
                                <td>{{ $row['Failed'] }}</td>
                                <td>{{ $row['Data'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endif

                @if ($type == 'transfer_inventory')
                    @foreach ($ext->data[0]->line as $it)
                        <tr>
                            <td>{{ $row['No'] }}</td>
                            <td>{{ $row['ID'] }}</td>
                            <td>{{ $row['DocumentNo'] ?? $row['Label'] }}</td>
                            <td>{{ $row['trans_date'] }}</td>
                            <td>{{ $row['Activity'] }}</td>
                            <td>{{ $row['IDLoc'] }}</td>
                            <td>{{ $row['Location'] }}</td>
                            <td>{{ $row['IntID'] }}</td>
                            <td>{{ $row['Paket'] }}</td>
                            <td>{{ $row['DocumentNo'] }}</td>
                            <td>{{ $row['ResponseId'] }}</td>
                            <td>{{ $row['Status'] }}</td>
                            <td>{{ $row['Timestamp'] }}</td>
                            <td>{{ $row['Update'] }}</td>
                            <td>{{ $ext->data[0]->transaction_date }}</td>
                            <td>{{ $ext->data[0]->memo }}</td>
                            <td>{{ $ext->data[0]->from_gudang }}</td>
                            <td>{{ $ext->data[0]->to_gudang }}</td>
                            <td>{{ $it->item }}</td>
                            <td>{{ $it->qty_to_transfer }}</td>
                            <td>{{ $row['Response'] }}</td>
                            <td>{{ $row['Failed'] }}</td>
                            <td>{{ $row['Data'] }}</td>
                        </tr>
                    @endforeach
                @endif
                @if ($type == 'transfer_inventory_do')
                    @if (isset($ext->data[0]->line))
                        @foreach ($ext->data[0]->line as $it)
                            <tr>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ App\Models\Item::where('sku', $it->item)->first()->nama }}</td>
                                <td>{{ App\Models\Gudang::gudang_code($ext->data[0]->from_gudang) }}</td>
                                <td>{{ App\Models\Gudang::gudang_code($ext->data[0]->to_gudang) }}</td>
                                <td>{{ $it->qty_to_transfer }}</td>
                                <td></td>
                                <td>{{ $ext->data[0]->memo }}</td>
                            </tr>
                        @endforeach
                    @else
                        @if (isset($ext->data[0]->items))
                            @foreach ($ext->data[0]->items as $it)
                                @if ($it->item != 'AY - S' && $it->item != 'AY - SF')
                                    <tr>
                                        <td>{{ $row['trans_date'] }}</td>
                                        <td>{{ $row['DocumentNo'] }}</td>
                                        <td>{{ $it->item }}</td>
                                        <td>{{ $it->gudang }}</td>
                                        <td></td>
                                        <td></td>
                                        <td>-{{ $it->qty }}</td>
                                        <td>{{ $ext->data[0]->so_number }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    @endif
                @endif
                @if ($type == 'gudang_retur')
                    @if($ext->record_type == "transfer_inventory")
                        @foreach ($ext->data[0]->line as $it)
                            <tr>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ App\Models\Item::where('sku', $it->item)->first()->nama }}</td>
                                <td>{{ App\Models\Gudang::gudang_code($ext->data[0]->from_gudang) }}</td>
                                <td>{{ App\Models\Gudang::gudang_code($ext->data[0]->to_gudang) }}</td>
                                <td></td>
                                <td>-{{ $it->qty_to_transfer }}</td>
                                <td>{{ $row['DocumentCode'] }}</td>
                            </tr> 
                        @endforeach
                    @elseif($ext->record_type == "receipt_return")
                        @foreach ($ext->data[0]->line as $it)
                            <tr>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ App\Models\Item::where('sku', $it->item_code)->first()->nama }}</td>
                                <td></td>
                                <td>{{ App\Models\Gudang::gudang_code($it->internal_id_location) }}</td>
                                <td>{{ $it->qty }}</td>
                                <td></td>
                                <td>{{ $row['DocumentCode'] }}</td>
                            </tr>
                        @endforeach

                    @elseif($ext->record_type == "work_order")
                        @foreach($ext->data[0]->items as $it)
                            @if($it->type == "Component")
                            <tr>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ $it->description }}</td>
                                <td></td>
                                <td></td>
                                <td>{{ $it->qty }}</td>
                                <td></td>
                                <td>{{ $row['DocumentCode'] }}</td>
                            </tr>
                            @else
                            <tr>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ $it->description }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>-{{ $it->qty }}</td>
                                <td>{{ $row['DocumentCode'] }}</td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                @endif

                @if ($type == 'gudang_lb')
                    @if($ext->record_type == "work_order")
                        @foreach ($ext->data[0]->items as $it)
                            @if ($it->item !== '7000000001' && $it->item !== '7000000002' && $it->item !== '1310000001' && $it->item !== '1100000011')
                            
                            <tr>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['DocumentCode'] }}</td>
                                <td>{{ App\Models\Item::where('sku', $it->item)->first()->nama }}</td>
                                <td></td>
                                <td>{{ App\Models\Gudang::gudang_code($ext->data[0]->id_location) }}</td>
                                <td>{{ $it->qty }}</td>
                                <td></td>
                            </tr> 
                            
                            @endif
                        @endforeach
                    @elseif($ext->record_type == "transfer_inventory")
                        @foreach ($ext->data[0]->line as $it)
                            <tr>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['DocumentCode'] }}</td>
                                <td>{{ App\Models\Item::where('sku', $it->item)->first()->nama }}</td>
                                <td>{{ App\Models\Gudang::gudang_code($ext->data[0]->from_gudang) }}</td>
                                <td>{{ App\Models\Gudang::gudang_code($ext->data[0]->to_gudang) }}</td>
                                <td></td>
                                <td>-{{ $it->qty_to_transfer }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endif

                @if ($type == 'wo' ||
                    $type == 'wo1' ||
                    $type == 'wo2' ||
                    $type == 'wo3' ||
                    $type == 'wo4' ||
                    $type == 'wo5' ||
                    $type == 'wo6' ||
                    $type == 'wo7')
                    @foreach ($ext->data[0]->items as $it)
                        @if ($task->record_type == 'work_order')
                            <tr>
                                <td>{{ $row['No'] }}</td>
                                <td>{{ $row['ID'] }}</td>
                                <td>{{ $row['DocumentNo'] ?? $row['Label'] }}</td>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['Activity'] }}</td>
                                <td>{{ $row['IDLoc'] }}</td>
                                <td>{{ $row['Location'] }}</td>
                                <td>{{ $row['IntID'] }}</td>
                                <td>{{ $row['Paket'] }}</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ $row['ResponseId'] }}</td>
                                <td>{{ $row['Status'] }}</td>
                                <td>{{ $row['Timestamp'] }}</td>
                                <td>{{ $row['Update'] }}</td>
                                <td>{{ $ext->data[0]->transaction_date }}</td>
                                <td>{{ $ext->data[0]->id_item_assembly }}</td>
                                <td>{{ $ext->data[0]->item_assembly }}</td>
                                <td>{{ $ext->data[0]->location }}</td>
                                <td>{{ $it->type }}</td>
                                <td>{{ $it->internal_id_item }}</td>
                                <td>{{ $it->item }}</td>
                                <td>{{ $it->description }}</td>
                                <td>{{ $it->qty }}</td>
                                <td>{{ $row['Response'] }}</td>
                                <td>{{ $row['Failed'] }}</td>
                            </tr>
                        @endif
                        @if ($task->record_type == 'wo_build')
                            <tr>
                                <td>{{ $row['No'] }}</td>
                                <td>{{ $row['ID'] }}</td>
                                <td>{{ $row['DocumentNo'] ?? $row['Label'] }}</td>
                                <td>{{ $row['trans_date'] }}</td>
                                <td>{{ $row['Activity'] }}</td>
                                <td>{{ $row['IDLoc'] }}</td>
                                <td>{{ $row['Location'] }}</td>
                                <td>{{ $row['IntID'] }}</td>
                                <td>{{ $row['Paket'] }}</td>
                                <td>{{ $row['DocumentNo'] }}</td>
                                <td>{{ $row['ResponseId'] }}</td>
                                <td>{{ $row['Status'] }}</td>
                                <td>{{ $row['Timestamp'] }}</td>
                                <td>{{ $row['Update'] }}</td>
                                <td>{{ $ext->data[0]->transaction_date }}</td>
                                <td>-</td>
                                <td>-</td>
                                <td>{{ $ext->data[0]->created_from_wo }}</td>
                                <td>{{ $it->type }}</td>
                                <td>{{ $it->internal_id_item }}</td>
                                <td>{{ $it->item }}</td>
                                <td>{{ $it->description }}</td>
                                <td>{{ $it->qty }}</td>
                                <td>{{ $row['Response'] }}</td>
                                <td>{{ $row['Failed'] }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif

            @endforeach

        </tbody>
    </table>
</div>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#tableex')) {
                $('#tableex').DataTable().destroy();
            }
            $('#tableex').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY: 500,
                scrollX: true,
                scrollCollapse: true,
                paging: false,
                // });
            });
        });
    </script>
@stop
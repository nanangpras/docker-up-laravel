@php
    header('Content-Transfer-Encoding: none');
    header("Content-type: application/vnd-ms-excel");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=". $judul. " " . ($request->tanggal_mulai ?? date('Y-m-d')) . " - " . ($request->tanggal_akhir ?? date('Y-m-d')) . ".xls");
@endphp

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kode</th>
            @if ($jenis == 'warehouse_keluar')
                <td class="stuck">Tanggal DO</td>
            @else
                <td class="stuck">Tanggal Bongkar</td>
            @endif
            <th>Karung Isi</th>
            <th>Qty/Pcs/Ekor</th>
            <th>Berat (Kg)</th>
            <th>Parting</th>
            @if ($jenis == 'warehouse_keluar')
                <th>No Document</th>
            @endif
            <th>Stock Customer</th>
            <th>Sub Item</th>
            <th>Packaging</th>
            <th>SubPack</th>
            <th>ABF</th>
            <th>Label</th>
            <th>Status</th>
            <th>Tujuan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stock as $i => $row)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $row->productitems->nama ?? '' }}</td>
            <td>{{ $row->production_code ?? '' }}</td>
            <td>{{ $row->production_date ?? '' }}</td>
            <td>{{ $row->karung_isi ?? '' }}</td>
            <td>{{ number_format($row->total ?? $row->qty_awal,2,'.', ',')  }}</td>
            <td>{{ number_format($row->kg ?? $row->berat_awal,2,'.', ',')  }}</td>
            {{-- <td>{{ $row->kg ?? $row->berat_awal }}</td> --}}
            <td>{{ $row->parting}}</td>
            @if ($jenis == 'warehouse_keluar')
            <td>
                {{-- <small class="text-uppercase">{{ $row->type }}</small> --}}
                @if($row->type=="siapkirim")
                    <small class="text-uppercase">{{$row->no_so}}</small>
                    <br> <small class="text-uppercase">{{ App\Models\Order::where('id', $row->order_id)->first()->no_do ?? '' }}</small>
                    <br>
                    {{-- <small class="text-uppercase">Customer DO :</small> --}}
                
                    <br><span class="status status-success mt-1 small">{{ App\Models\Order::where('id', $row->order_id)->first()->nama ?? ''  }}</span>
                @endif
            </td>
            @endif
            <td>{{$row->konsumen->nama ?? ''}}</td>
            <td>{{$row->sub_item}}</td>
            <td>{{$row->plastik_group}}</td>
            <td>{{ $row->subpack }}</td>
            <td>{{ $row->asal_abf }}</td>
            <td>{{ $row->label }}</td>
            <td>
                @if ($jenis == 'warehouse_masuk')
                    {!! $row->status_gudang ?? '' !!}
                @else
                    @if($row->status == 2)<div class='status status-danger'>Request Keluar</div>@else <div class='status status-warning'>Keluar</div> @endif
                @endif
            </td>
            <td>
                @if ($row->status != 1)
                    <div style="width: 130px">{{ $row->productgudang->code ?? '' }}</div>
                @else
                    <div class="form-group">
                        <select name="waretujuan" class="form-input-table" id="waretujuan">
                            <option value="" disabled selected hidden>Pilih</option>
                            @foreach ($warehouse as $ware)
                                <option value="{{ $ware->id }}" @if ($row->gudang_id == $ware->id) selected @endif>{{ $ware->code }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

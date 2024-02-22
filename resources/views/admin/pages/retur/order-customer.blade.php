<input type="hidden" name="order_id" value="{{ $data->id }}">
<div class="card mb-3">
    <div class="card-header">INFORMASI TRANSAKSI</div>
    <div class="card-body p-2">
        <div class="row">
            <div class="col-lg-6 pr-1">
                <table class="table default-table">
                    <tbody>
                        <tr>
                            <th>Tanggal Kirim</th>
                            <td>{{ $data->tanggal_kirim }}</td>
                        </tr>
                        <tr>
                            <th>Nama Customer</th>
                            <td>{{ $data->nama }}</td>
                        </tr>
                        <tr>
                            <th>No SO</th>
                            <td>{{ $data->no_so }}</td>
                        </tr>
                        @if ($data->keterangan)
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $data->keterangan }}</td>
                        </tr>
                        @endif
                        @if ($data->alamat)
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $data->alamat }}</td>
                        </tr>
                        @endif
                        @if ($data->telp)
                        <tr>
                            <th>Telepon</th>
                            <td>{{ $data->telp }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="col-lg-6 pl-1">
                <table class="table default-table">
                    <thead>
                        <tr>
                            <th width=10px>No</th>
                            <th>Nama Item</th>
                            <th>Qty Order</th>
                            <th>Berat Order</th>
                            <th>Qty Fulfill</th>
                            <th>Berat Fulfill</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                            $berat = 0;
                            $count_retur = 0;

                            $retur = \App\Models\Retur::where('id_so', $data->no_so)->first();
                        @endphp
                        @foreach ($data->daftar_order_full as $i => $row)
                            @php
                                $total += $row->qty;
                                $berat += $row->berat;

                                $retur_item = \App\Models\ReturItem::where('orderitem_id', $row->id)->first();

                            @endphp
                            @if($row->fulfillment_berat > 0)
                            <tr>
                                <td>{{ ++$i }}
                                    <input type="hidden" name="orderitem_id[]" value="{{ $row->id }}"
                                        id="orderitem_id{{ $row->id }}">
                                    <input type="hidden" name="line_id[]" value="{{ $row->line_id }}"
                                        id="line_id{{ $row->line_id }}">
                                </td>
                                <td>{{ $row->nama_detail }}</td>
                                <td class="text-right">{{ number_format($row->qty) }}</td>
                                <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                                <td class="text-right">{{ number_format($row->fulfillment_qty) }}</td>
                                <td class="text-right">{{ number_format($row->fulfillment_berat, 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">TOTAL</th>
                            <th class="text-right">{{ number_format($total) }}</th>
                            <th class="text-right">{{ number_format($berat, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@if (COUNT($data->list_retur))
@foreach ($data->list_retur as $row)
<div class="card mb-3">
    <div class="card-header">RIWAYAT RETUR</div>
    <div class="card-body p-2">
        <table class="table default-table">
            <tbody>
                <tr>
                    <th>Tanggal Retur</th>
                    <td>{{ $row->tanggal_retur }}</td>
                </tr>
                <tr>
                    <th>Tanggal Input</th>
                    <td>{{ $row->created_at }}</td>
                </tr>
                <tr>
                    <th>Doc. Number</th>
                    <td><span class="status status-success">{{ $row->no_so }}</span> | <span class="status status-warning">{{ $row->no_ra }}</span></td>
                </tr>
                <tr>
                    <th>Penginput</th>
                    <td>{{ $row->operator }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table default-table">
            <thead>
                <tr>
                    <th width=10px>No</th>
                    <th>Nama Item</th>
                    <th>Tujuan</th>
                    <th>Penanganan</th>
                    <th>Retur Qty</th>
                    <th>Retur Berat</th>
                    <th>Alasan</th>
                    <th>Kategori</th>
                    <th>Satuan</th>
                    <th>Sopir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                    $berat = 0;
                    $rtr = \App\Models\ReturItem::where('retur_id', $row->id)->get();
                @endphp
                @foreach ($rtr as $i => $list)
                    @php
                        $total += $list->qty;
                        $berat += $list->berat;
                        $cekLog = \App\Models\Adminedit::where('table_name', 'retur_item')->where('type','retur')->where('activity', 'Retur Salah Item/Tidak Sesuai Pesanan')
                                                    ->where('table_id', $list->id)->first();
                    @endphp
                    <tr @if($row->catatan == 'Salah Item' || $row->catatan == 'Barang Tidak Sesuai Pesanan/Order')  style="background-color: #FFFF8F" @endif>
                        <td>{{ ++$i }}</td>
                        <td>{{ $list->to_item->nama ?? '' }}
                            @if($row->catatan == 'Salah Item' || $row->catatan == 'Barang Tidak Sesuai Pesanan/Order')  
                            <div class="row mt-1">
                                <div class="col-auto">
                                    <span class="text-info">
                                    <b>
                                        @if($cekLog) *ITEM DITUKAR DARI: <br> {{ $cekLog->data }}  @else *ITEM TIDAK DITUKAR @endif
                                    </br>
                                    </span>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td>{{ $list->unit }}</td>
                        <td>{{ $list->penanganan }}</td>
                        <td>{{ $list->qty }}</td>
                        <td>{{ $list->berat }}</td>
                        <td>{{ $list->catatan }}</td>
                        <td>{{ $list->kategori }}</td>
                        <td>{{ $list->satuan }}</td>
                        <td>{{ $list->todriver->nama ?? '' }}</td>
                        <th>
                            @if ($list->status == 1)
                                <span class="status status-danger">Belum Selesai</span>
                            @else
                                <span class="status status-success">Selesai</span>
                            @endif
                        </th>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach
@endif
{{-- @if ($cekChiller >= 1 || $cekAbf >= 1 || $cekChillerAbf >= 1)
    <a href="#" class="btn btn-block btn-danger">Data Transaksi Sudah Ditutup</a>
@else --}}
    <a href="{{ route('retur.returdo', ['id' => $data->id]) }}" class="btn btn-block btn-primary">Proses Retur</a>
{{-- @endif --}}



<section class="panel">
    <div class="card-body">
        <div class="row mt-3 justify-content-around">
            <div class="col-lg-2 col-6">
                <div class="form-group">
                    <div class="bg-primary p-2 text-center text-light font-weight-bold text-uppercase">Total Timbang</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0"><div id="totaltransactionlb"></div></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="form-group">
                    <div class="bg-success p-2 text-center text-light font-weight-bold text-uppercase">Selesai</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0"><div id="totalsuccesslb"></div></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="form-group">
                    <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Proses</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0"><div id="totalprocesslb"></div></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="form-group">
                    <div class="bg-danger p-2 text-center text-light font-weight-bold text-uppercase">Total Ekor</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0"><div id="totalqtylb"></div></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="form-group">
                    <div class="bg-dark p-2 text-center text-light font-weight-bold text-uppercase">Berat Total</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0"><div id="totalweightlb"></div></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive mt-4">
            <table class="table default-table table-bordered" style="width:2000px !important;max-width:2000px !important;">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. DO</th>
                        <th>Kandang</th>
                        <th>Jam Masuk</th>
                        <th>Detail PO</th>
                        <th>Detail Penerimaan</th>
                        <th>Status</th>
                        <th>Tujuan</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $totalweighttransaction     = 0;
                    $totalqtytransaction        = 0;
                    $total_berat_terima         = 0;
                    $total_qty_terima           = 0;
                @endphp
                    @foreach ($data as $i => $row)
                        <tr>
                            <td >{{ $row->prod_tanggal_potong }}</td>
                            <td > {{ $row->no_po ?? 0 }}<br>{{ $row->no_lpah ?? '' }}<br>DO :
                                {{ $row->no_do ?? '' }}</td>
                            <td >{{ $row->sc_nama_kandang ?? 0 }}<br>@if ($row->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $row->prodpur->ukuran_ayam }} @endif<br><span
                                    class="text-capitalize">{{ $row->po_jenis_ekspedisi ?? 0 }}</span></td>
                            <td>{{ date('d/m/y', strtotime($row->sc_tanggal_masuk ?? '')) }}
                                <br>{{ date('H:i', strtotime($row->sc_jam_masuk ?? '00:00')) }} WIB</td>
                            <td>
                                <table class="table default-tables">
                                    @php 
                                        $cekDetailPO = \App\Models\PurchaseItem::detailPoItemReceipt($row->purchasing_id);
                                    @endphp
                                    <tr>
                                        <th> No </th>
                                        <th> Nama </th>
                                        <th> Berat PO </th>
                                        <th> Qty PO </th>
                                        <th> Total Berat Terima </th>
                                        <th> Total Qty Terima </th>
                                        <th> Sisa Berat </th>
                                        <th> Sisa Qty </th>
                                    </th>
                                    @foreach($cekDetailPO as $key => $detailPo)
                                    @php 
                                        $cekSisaBerat   = $detailPo->berat_ayam - $detailPo->total_berat_terima;
                                        $cekSisaQty     = $detailPo->jumlah_ayam - $detailPo->total_qty_terima;
                                    @endphp
                                    <tr>
                                        <td class="list-record">{{ $key + 1 }}</td>
                                        <td class="list-record">{{ $detailPo->keterangan }}</td>
                                        <td class="list-record">{{ $detailPo->berat_ayam ?? 0 }} kg</td>
                                        <td class="list-record">{{ $detailPo->jumlah_ayam ?? 0 }} Pcs/Ekr</td>
                                        <td class="list-record">{{ number_format($detailPo->total_berat_terima,2) }} kg</td>
                                        <td class="list-record">{{ $detailPo->total_qty_terima }} Pcs/Ekr</td>
                                        <td class="list-record" @if($cekSisaBerat < 0) style="background: #ffbdbd;" @endif>{{ number_format(($detailPo->berat_ayam - $detailPo->total_berat_terima),2) }} Kg</td>
                                        <td class="list-record" @if($cekSisaQty < 0) style="background: #ffbdbd;" @endif>{{ $detailPo->jumlah_ayam - $detailPo->total_qty_terima }} Pcs/Ekr</td>
                                    </tr>
                                    @endforeach
                                </table>
                            <td>
                                <table class="table default-tables" >
                                    <tr>
                                        <th> Nama </th>
                                        <th> Berat Terima </th>
                                        <th> Qty Terima </th>
                                        <th> SKU </th>
                                    </th>
                                    @foreach ($row->prodpur->purchasing_item->sortBy('keterangan')->where('status', '!=', NULL) as $no => $itm)
                                        @php 
                                            $cekBarangDiterima = \App\Models\PurchaseItem::totalPenerimaan($itm->item_po,$itm->purchasing_id);
                                            $item = \App\Models\Item::item_sku($itm->item_po);
                                        @endphp
                                        <tr @if($cekBarangDiterima[0]->total_berat_terima > $cekBarangDiterima[0]->berat_ayam) style="background: #ffbdbd" @endif >
                                            <td class="list-record">{{ $item->nama }}</td>
                                            {{-- <td class="list-record">Rp {{ number_format($itm->harga) }}</td> --}}
                                            <td class="list-record">{{ number_format($itm->terima_berat_item,2) ?? 0 }} kg</td>
                                            <td class="list-record">{{ $itm->terima_jumlah_item ?? 0 }} Pcs/Ekr</td>
                                            <td class="list-record">{{ $item->sku }}</td>
                                        </tr>
                                        @php
                                            $totalweighttransaction     += $itm->berat_ayam;
                                            $totalqtytransaction        += $itm->jumlah_ayam;
                                        @endphp
                                    @endforeach
                                </table>
                            </td>
                            <td>{{ $row->ppic_acc == 2 ? 'Proses' : 'Selesai' }}</td>
                            <td>{{ $row->ppic_tujuan }}</td>
                            <td>
                                @if ($row->ppic_acc == 2)
                                    <a href="{{ route('nonkarkas.show', $row->id) }}"
                                        class="btn btn-sm btn-primary btn-rounded">Proses</a>
                                @else
                                    <a href="{{ route('nonkarkas.show', $row->id) }}"
                                        class="btn btn-sm btn-warning btn-rounded">Detail</a>
                                @endif
                            </td>
                        </tr>
                       
                    @endforeach
                </tbody>
            </table>
        </div>
</section>
<style>
.default-tables{
    border: 1px solid #f1f1f1;
    font-size: 9pt;
    line-height: 1.3;
}
table.default-tables th {
    background: #a2c4eb;
}
</style>
<script>
    $(document).ready(function(){
        var totaltransactionlb      = "{{ number_format($counttransaction,0) }}";
        let countsuccesstransaction = "{{ number_format($countsuccesstransaction,0) }}";
        let countprocesstransaction = "{{ number_format($countprocesstransaction,0) }}";
        let totalweighttransaction  = "{{ number_format($totalweighttransaction,0) }}";
        let totalqtytransaction     = "{{ number_format($totalqtytransaction,0) }}";

        if (!isNaN(totaltransactionlb) || !isNaN(countsuccesstransaction) || !isNaN(countprocesstransaction) || !isNaN(totalweighttransaction) || !isNaN(totalqtytransaction)) {
            $('#totaltransactionlb').text(totaltransactionlb);
            $('#totalsuccesslb').text(countsuccesstransaction);
            $('#totalprocesslb').text(countprocesstransaction);
            $('#totalweightlb').text(totalweighttransaction);
            $('#totalqtylb').text(totalqtytransaction);
        } 
    });
</script>
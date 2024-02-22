@if ($download == true)
    <style>
        th,
        td {
            border: 1px solid #ddd;
        }
    </style>
    @php
        header('Content-Transfer-Encoding: none');
        header('Content-type: application/vnd-ms-excel');
        header('Content-type: application/x-msexcel');
        header('Content-Disposition: attachment; filename=Retur_Summary.xls');
    @endphp
@endif
<section class="panel" id="panelretur" >
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <div class="bg-info p-2 text-center text-light font-weight-bold text-uppercase">Total QTY</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ $totalReturQty }}</h5>
                    </div>
                </div>
            </div>
        
            <div class="col">
                <div class="form-group">
                    <div class="bg-warning p-2 text-center text-light font-weight-bold text-uppercase">Total Berat</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ $totalReturBerat }}</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="bg-danger p-2 text-center text-light font-weight-bold text-uppercase">Total Qty FulFillment</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ $totalAllQtyFulFill }}</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <div class="bg-success p-2 text-center text-light font-weight-bold text-uppercase">Persentase Retur</div>
                    <div class="border p-2 text-center">
                        <h5 class="mb-0">{{ $totalqtypercentage }} %</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<table class="table default-table">
    <thead>
        <tr>
            <th width=10px>No</th>
            <th>Tanggal Retur</th>
            <th>Tanggal Kirim</th>
            <th>Customer</th>
            <th>No SO</th>
            <th>Item</th>
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

        @foreach ($retur_list as $i => $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ date('d/m/Y', strtotime($row->to_retur->tanggal_retur)) ?? '' }}</td>
                <td>{{ date('d/m/Y', strtotime($row->tanggal_kirim)) ?? '' }}</td>
                <td>{{ $row->to_retur->to_customer->nama ?? '' }}</td>
                <td>{{ $row->nomer_so ?? '#NonSO' }}<br>{{ $row->to_retur->no_ra ?? 'Tidak ada RA' }}</td>
                <td>{{ $row->to_item->nama ?? '' }}</td>
                <td>{{ $row->unit ?? '' }}</td>
                <td>{{ $row->penanganan ?? '' }}</td>
                <td>{{ $row->qty ?? '' }}</td>
                <td>{{ $row->berat ?? '' }}</td>
                <td>{{ $row->catatan }}</td>
                <td>{{ $row->kategori }}</td>
                <td>{{ $row->satuan }}</td>
                <td>{{ $row->todriver->nama ?? '' }}</td>
                <th>
                    @if ($row->to_retur->status == 1)
                        <span class="status status-danger">Belum Selesai</span>
                    @else
                        <span class="status status-success">Selesai</span>
                    @endif
                </th>

            </tr>
        @endforeach
    </tbody>
</table>

<div class="table-responsive mt-3">
    <table class="table table-sm default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Supplier</th>
                <th>Item</th>
                <th>Daerah</th>
                <th>Tipe</th>
                <th>Ekspedisi</th>
                <th>Jumlah DO</th>
                <th>Tanggal Potong</th>
                <th>Status</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase as $i => $row)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $row->no_po ?? '' }}<br>{{ $row->purcsupp->nama ?? '' }}
                    <br>PO masuk  : {{ $row->created_at ?? ""}}</td>
                    <td>
                        @if ($row->type_po == 'PO LB')
                            <ol style="margin:0; padding-left: 15px">
                                <li>
                                    @foreach ($row->purchasing_item as $item)
                                    {{ $item->description }} - {{ $item->ukuran_ayam }}
                                        <br><span class="status status-success">{{ number_format($item->jumlah_ayam) }}
                                            Ekor</span> || <span
                                            class="status status-info">{{ number_format($item->berat_ayam, 2) }}
                                            Kg</span>
                                    @endforeach
                                </li>
                            </ol>
                        @else
                            <ol style="margin:0; padding-left: 15px">
                                @foreach ($row->purchasing_item as $item)
                                    @if($row->type_po == 'PO Karkas' )
                                    <li>
                                        {{ $item->description }}<br><span
                                            class="status status-success">{{ number_format($item->jumlah_ayam) }}
                                            {{ $row->type_po == 'PO LB' || $row->type_po == 'PO Maklon' ? 'Ekor' : 'Pcs' }}</span>
                                        || <span class="status status-info">{{ number_format($item->berat_ayam, 2) }}
                                            Kg</span>
                                    </li>
                                    @else
                                    <li>
                                        {{ \App\Models\Item::item_sku($item->item_po)->nama ?? "#" }}<br><span
                                            class="status status-success">{{ number_format($item->jumlah_ayam) }}
                                            {{ $row->type_po == 'PO LB' || $row->type_po == 'PO Maklon' ? 'Ekor' : 'Pcs' }}</span>
                                        || <span class="status status-info">{{ number_format($item->berat_ayam, 2) }}
                                            Kg</span>
                                    </li>
                                    @endif
                                @endforeach
                            </ol>
                        @endif
                    </td>
                    <td class="text-capitalize">{{ $row->wilayah_daerah }}</td>
                    <td class="text-capitalize">{{ $row->type_po }}</td>
                    <td class="text-capitalize">{{ $row->type_ekspedisi ?? $row->nama_po }}</td>
                    <td>{{ number_format($row->jumlah_po) }}</td>
                    <td>{{ date('d/m/y', strtotime($row->tanggal_potong)) }}</td>
                    <td>@php echo $row->status_purchase; @endphp
                        @if($row->deleted_at)
                            <span class="status status-danger">Closed</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{-- @if ($row->type_ekspedisi == 'kirim' || $row->type_po != 'PO LB')
                        @else
                            <a href="{{ route('purchasing.show', $row->id) }}" class="btn btn-sm btn-primary">Detail</a>
                        @endif --}}
                        @if ($row->purcsupp->nama =='CITRA GIANDRA FARMS PT' && $row->type_ekspedisi == 'kirim')
                            <a href="{{ route('purchasing.show', $row->id) }}" class="btn btn-sm btn-primary">Detail</a>
                        @elseif($row->type_ekspedisi == 'kirim' || $row->type_po != 'PO LB')
                        @else
                            <a href="{{ route('purchasing.show', $row->id) }}" class="btn btn-sm btn-primary">Detail</a>
                        @endif

                        @if ($row->type_po != 'PO LB')
                            <a href="{{ route('purchasing.retur', $row->id) }}" class="btn btn-sm btn-outline-info">Retur</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div id="daftar_paginate">
    {{ $purchase->appends($_GET)->links() }}
</div>

{{-- $purchase->appends($_GET)->onEachSide(1)->links() --}}

<div class="row">
    <div class="col-md-2 col-6 pr-1">
        <div class="form-group">
            <label for="total_deal">Total Deal</label>
            <input type="text" id="total_deal" class="form-control bg-white text-right" value="{{ $hitung['total'] }}" disabled>
        </div>
    </div>

    <div class="col-md-2 col-6 pl-1">
        <div class="form-group">
            <label for="pending">Pending</label>
            <input type="text" id="pending" class="form-control bg-white text-right" value="{{ $hitung['pending'] }}" disabled>
        </div>
    </div>

    <div class="col d-none d-md-block"></div>
</div>

<script>
$('#daftar_paginate .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#purch').html(response);
        }

    });
});
</script>
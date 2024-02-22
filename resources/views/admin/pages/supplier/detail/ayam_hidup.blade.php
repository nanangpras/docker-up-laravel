<table class="table table-sm default-table">
    <thead>
        <tr>
            <th>PO Masuk</th>
            <th>Nomor PO</th>
            <th>Item</th>
            <th>Daerah</th>
            <th>Tipe</th>
            <th>Ekspedisi</th>
            <th>Jumlah DO</th>
            <th>Tanggal Potong</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($purchase as $row)
        <tr>
            <td>{{ $row->created_at ?? ""}}</td>
            <td>{{ $row->no_po ?? '' }}
            <td>
                @if ($row->type_po == 'PO LB')
                    <ol style="margin:0; padding-left: 15px">
                        <li>AYAM UKURAN {{ $row->ukuran_ayam }}
                            @foreach ($row->purchasing_item as $item)
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
            <td>@php echo $row->status_purchase; @endphp</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="paginate_lb">
    {{ $purchase->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('.paginate_lb .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#ayam_hidup').html(response);
        }

    });
});
</script>


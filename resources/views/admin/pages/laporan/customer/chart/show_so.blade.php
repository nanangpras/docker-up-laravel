<table class="table default-table">
    <thead>
        <tr>
            <th>TanggalSO</th>
            <th>TanggalKirim</th>
            <th>NoSO</th>
            <th>Nama</th>
            <th>Berat</th>
            <th>Fulfill</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($list_order as $row)
        <tr>
            <td>{{ $row->tanggal_so }}</td>
            <td>{{ $row->tanggal_kirim }}</td>
            <td><a target="_blank" href="{{ route('salesorder.detail', $row->id) }}">{{ $row->no_so }}</a></td>
            <td>{{ $row->nama }}</td>
            <td class="text-right">{{ number_format($row->berat) }}</td>
            <td class="text-right">{{ number_format($row->fulfill) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="paginate_pending">
    {{ $list_order->appends($_GET)->onEachSide(1)->links() }}
</div>

<div class="row">
    <div class="col pr-1 text-center">
        <div class="bg-success px-2 py-1 font-weight-bold text-light">TOTAL BERAT</div>
        <div class="border p-2">
            <h3>{{ number_format($berat) }}</h3>
        </div>
    </div>
    <div class="col pl-1 text-center">
        <div class="bg-info px-2 py-1 font-weight-bold text-light">TOTAL FULFILL</div>
        <div class="border p-2">
            <h3>{{ number_format($fulfill) }}</h3>
        </div>
    </div>
</div>

<script>
$('.paginate_pending .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#show_so').html(response);
        }

    });
});
</script>


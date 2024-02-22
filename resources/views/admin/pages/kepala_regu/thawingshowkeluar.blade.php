<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Item</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($keluar as $i => $thawing)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $thawing->nama ?? '' }}</td>
                <td>{{ number_format($thawing->qty, 0) }}</td>
                <td>{{ number_format($thawing->berat, 2) }}</td>
                <td>{!! $thawing->status_keluar !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>

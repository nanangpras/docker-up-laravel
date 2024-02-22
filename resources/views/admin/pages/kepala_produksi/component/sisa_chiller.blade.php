<div class="table-responsive">
    <table width="100%" id="kategori" class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat (Kg)</th>
                <th>Asal</th>
                <th>Tanggal Bahan Baku</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chiller as $i => $chill)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $chill->item_name }}</td>
                    <td>{{ number_format($chill->stock_item) }}</td>
                    <td>{{ number_format($chill->stock_berat, 2) }}</td>
                    <td>{{ $chill->tujuan }}</td>
                    <td>{{ $chill->tanggal_produksi }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>

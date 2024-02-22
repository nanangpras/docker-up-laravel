<section class="panel">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Customer</th>
                        <th>Stok berat</th>
                        <th>Stok qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stok as $i => $row)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->stock_berat }} Kg</td>
                            <td>{{ $row->stock_item }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="card-body">
    <div class="row mb-4">
        <div class="col-md-6 pr-1">
            <div class="card">
                <div class="card-header">10 Stock Booking (Kg)</div>
                <div class="card-body p-2">
                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Berat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($konsumen['stock_book'] as $item)
                            <tr>
                                <td>{{ $item->productitems->nama }}</td>
                                <td class="text-right">{{ number_format($item->sisa, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 pl-1">
            <div class="card">
                <div class="card-header">10 Stock Free (Kg)</div>
                <div class="card-body p-2">
                    <table class="table default-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Berat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($konsumen['stock_free'] as $item)
                            <tr>
                                <td>{{ $item->productitems->nama }}</td>
                                <td class="text-right">{{ number_format($item->sisa, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
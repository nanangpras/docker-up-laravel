
<div class="row">
    <div class="col">
        <div class="outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th colspan="3">Daftar Item</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listayam4feb as $data)
                        <tr>
                            <td>{{ $data->nama }}</td>
                            <td class="text-right">{{ number_format($data->total) }}</td>
                            <td class="text-right">{{ number_format(($data->kg ?? '0'), 2) }} Kg</td>
                        </tr>
                    @empty
                        <td colspan="2">Tidak ada data.</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
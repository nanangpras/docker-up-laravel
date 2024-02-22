
<div class="row">
    <div class="col-6">
        <div class="outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th colspan="2">Daftar Item</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listayam4feb as $data)
                        <tr>
                            <td>{{ $data->nama }}</td>
                            <td>{{ number_format($data->jumlah_nama) }}</td>
                        </tr>
                    @empty
                        <td colspan="2">Tidak ada data.</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-6">
        <div class="outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th colspan="2">Daftar Konsumen</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listkonsumen4feb as $data)
                        <tr>
                            <td><a href="{{ route('warehouse.stock', ['key' => 'detailkonsumen']) }}&tanggal_mulai={{ $tanggal_mulai }}&tanggal_akhir={{ $tanggal_akhir }}&customer_id={{ $data->customer_id }}">{{ $data->konsumen->nama ?? '' }}</a></td>
                            <td>{{ number_format($data->jumlah_konsumen) }}</td>
                        </tr>
                    @empty
                        <td colspan="2">Tidak ada data.</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-6">
        <div class="outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th colspan="2">Daftar Kemasan</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($listplastik4feb as $data)
                        <tr>
                            <td>{{ $data->packaging }}</td>
                            <td>{{ number_format($data->jumlah_plastik) }}</td>
                        </tr>
                    @empty
                        <td colspan="2">Tidak ada data.</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-6">
        <div class="outer-table-scroll">
            <table class="table default-table">
                <thead>
                    <tr>
                        <th colspan="2">Daftar Gudang</th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($liststock4feb as $data)
                        <tr>
                            <td>{{ $data->productgudang->code ?? '' }}</td>
                            <td>{{ number_format($data->jumlah_stock) }}</td>
                        </tr>
                    @empty
                        <td colspan="2">Tidak ada data.</td>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<style>
.outer-table-scroll {
    max-height: 400px;
    overflow-y: auto;
    width: 100%;
}
</style>

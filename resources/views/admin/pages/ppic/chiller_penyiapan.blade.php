<div class="table-responsive">
    <table width="100%" id="kategori" class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
                {{-- <th>Asal</th> --}}
                <th>Tanggal Bahan Baku</th>
                {{-- <th>Aksi</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($chiller_penyiapan as $i => $chill)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $chill->item_name }}</td>
                    <td>{{ $chill->stock_item }} ekor</td>
                    <td>{{ $chill->stock_berat }} Kg</td>
                    {{-- <td>{{ $chill->tujuan }}</td> --}}
                    <td>{{ $chill->tanggal_produksi }}</td>
                    {{-- <td>
                        @if ($chill->status == 2)
                            <button type="submit" class="btn btn-primary btn-sm toabf"
                                data-chiller="{{ $chill->id }}"> Kirim ke
                                ABF</button>
                        @else
                            <button type="submit" class="btn btn-success btn-sm toabf"
                                disabled>
                                Selesai</button>
                        @endif
                    </td> --}}
                </tr>
            @endforeach

        </tbody>
    </table>
    {{ $chiller_penyiapan->render() }}
</div>

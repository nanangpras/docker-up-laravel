<table class="table default-table">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Berat Bersih</th>
            <th class="text-center">Hitung</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($gabungan as $i => $gab)
            <tr>
                <td class="text-center">{{ ++$i }}</td>
                <td>{{ $gab->eviitem->nama }}</td>
                <td class="text-center">{{ $gab->total_item }}</td>
                <td class="text-center">{{ $gab->berat_item }}</td>
                <td class="text-center">{{ $gab->jenis_evis }}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-primary btn-sm p-0 px-1 edit_cart"
                        data-kode="{{ $gab->id }}">
                        <i class="fa fa-edit"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

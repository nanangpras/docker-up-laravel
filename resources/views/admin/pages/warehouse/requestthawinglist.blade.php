<table class="table default-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Sub Item</th>
            <th>Lokasi</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
        <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->gudang->productitems->nama ?? "EEROR:NAMA KOSONG" }}</td>
            <td>{{ $row->gudang->sub_item }}</td>
            <td>{{ $row->gudang->productgudang->code ?? "ERROR:KODE KOSONG" }}</td>
            <td>{{ number_format($row->qty) }}</td>
            <td>{{ number_format($row->berat, 2) }}</td>
            {{-- CHECK DOKUMEN NOMOR NS --}}
            @php
                $trueFalseDokumenNS     = false;
                $checkDokumenNS         = App\Models\Netsuite::where('document_code', 'TW-'. $row->id)->first();

                if ($checkDokumenNS) {
                    if ($checkDokumenNS->document_no == NULL) {
                        $trueFalseDokumenNS = true;

                    }
                } else if (!$checkDokumenNS) {
                    $trueFalseDokumenNS = true;
                }

            @endphp
            
            @if ($trueFalseDokumenNS == true)
            <td class="text-center"><i class="fa fa-trash text-danger hapus_item" data-id="{{ $row->id }}"></i></td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>

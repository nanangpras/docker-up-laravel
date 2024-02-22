<table class="table default-table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
            <th>Input Qty</th>
            <th>Input Berat</th>
            <th>To Gudang</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($gudang as $i => $val)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $val->nama ?? $val->item_name }}</td>
                <td>{{ number_format($val->qty ?? $val->stock_item) }}</td>
                <td>{{ number_format($val->berat ?? $val->stock_berat, 2) }}</td>
                <td>
                    <div class="col px-1">
                        <input type="number" id="qty{{ $val->id }}" placeholder="Qty" class="form-control">
                    </div>
                </td>
                <td>
                    <div class="col px-1">
                        <input type="number" id="berat{{ $val->id }}" placeholder="Berat" class="form-control">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <select name="togudang" class="form-control" id="togudang{{ $val->id }}">
                            <option value="" disabled selected hidden>Pilih </option>
                            @foreach ($pindah as $key)
                                <option value="{{ $key->id }}"> {{ $key->code }}</option>
                            @endforeach
                        </select>
                        @error('togudang') <div class="small text-danger">{{ message }}</div> @enderror
                    </div>
                </td>

                <td>
                    <button type="submit" class="btn btn-primary btn-sm pindahan"
                        data-id="{{ $val->id }}">Pindah</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    $('.pindahan').click(function() {
        var id = $(this).data('id');
        var qty = $("#qty" + id).val();
        var berat = $("#berat" + id).val();
        var tujuan = $("#togudang" + id).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('kepalaproduksi.inventorystore') }}",
            method: "POST",
            data: {
                id: id,
                qty: qty,
                berat: berat,
                tujuan: tujuan,

            },
            success: function(data) {

                console.log(data);

                showNotif('Berhasil Pindah');
                $("#show").load("{{ route('kepalaproduksi.inventory') }}");
            }
        });
    })
</script>

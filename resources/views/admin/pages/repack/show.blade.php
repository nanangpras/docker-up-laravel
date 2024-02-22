<table class="table default-table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat</th>
            <th>Repack Plastik</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($gudang as $i => $val)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $val->nama ?? "" }}<br><b>{{ $val->packaging }}</b></td>
                <td>
                    {{ number_format($val->qty) }}<br>
                    <input type="number" id="qty{{ $val->id }}" placeholder="Qty" class="form-control form-control-sm mt-1 p-1" style="width:70px">
                </td>
                <td>
                    {{ number_format($val->berat, 2) }}<br>
                    <input type="number" id="berat{{ $val->id }}" placeholder="Berat" class="form-control form-control-sm mt-1 p-1" style="width:70px">
                </td>
                <td>
                    <div class="form-group">
                        <select class="form-control select2" data-placeholder="Pilih Plastik" data-width="100%" id="plastik{{ $val->id }}">
                            <option value=""></option>
                            @foreach (App\Models\Bom::repack_plastik($val->id) as $row)
                            @if ($row->item->nama != $val->packaging)
                            <option value="{{ $row->item->id }}">{{ $row->item->nama }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </td>

                <td>
                    <button type="submit" class="btn btn-primary btn-sm pindahan" data-id="{{ $val->id }}">Pindah</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
$('.select2').select2({
    theme: 'bootstrap4'
})
</script>

<script>
    $('.pindahan').click(function() {
        var id      =   $(this).data('id');
        var cold    =   $('input[name=cold]:checked').val();
        var tanggal =   $('#tanggal').val();
        var qty     =   $("#qty" + id).val();
        var berat   =   $("#berat" + id).val();
        var plastik =   $("#plastik" + id).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('repack.store') }}",
            method: "POST",
            data: {
                id      :   id,
                qty     :   qty,
                berat   :   berat,
                plastik :   plastik,
                cold    :   cold,

            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg) ;
                } else {
                    showNotif('Repack berhasil dilakukan');
                    $("#show").load("{{ route('repack.index', ['key' => 'show']) }}&id=" + cold + "&tanggal="+tanggal);
                }
            }
        });
    })
</script>

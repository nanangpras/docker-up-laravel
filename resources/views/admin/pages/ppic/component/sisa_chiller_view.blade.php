<div class="table-responsive mt-3">
    <table width="100%" id="chillertable" class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
                <th>Asal</th>
                <th>Tanggal Bahan Baku</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chiller as $i => $chill)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $chill->item_name }}</td>
                    <td>{{ $chill->stock_item }} ekor</td>
                    <td>{{ $chill->stock_berat }} Kg</td>
                    <td>{{ $chill->tujuan }}</td>
                    <td>{{ $chill->tanggal_produksi }}</td>
                    <td>
                        @if ($chill->status == 2)
                        <div style="width:232px!important">
                            <input type="number" id="kirim_jumlah{{ $chill->id }}" placeholder="Qty Item" class="form-input-table">
                            <input type="number" id="kirim_berat{{ $chill->id }}" step="0.01" placeholder="Berat Item" class="form-input-table"><br>
                            <select name="plastik{{ $chill->id }}" id="plastik{{ $chill->id }}" class="form-input-table" data-placeholder="Pilih Plastik">
                                <option value="" disabled hidden selected>- Pilih Plastik -</option>
                                <option value="Curah">Curah</option>
                                @foreach ($plastik as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                            <input type="number" id="jumlah{{ $chill->id }}" placeholder="Qty Plastik" class="form-input-table"><br>
                            <button type="submit" class="btn btn-primary toabf" data-chiller="{{ $chill->id }}">Kirim ke ABF</button>
                        </div>
                        @else
                            <button class="btn btn-success btn-sm" disabled>Selesai</button>
                        @endif
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

</div>

<style>
    select.form-input-table, .form-input-table{
        width: 100px;
        padding: 3px 5px;
        margin-bottom: 3px;
    }
    select.form-input-table{
        padding: 5px 5px;
        border-radius: 2px;
    }
</style>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#chillertable').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY:        500,
                scrollX:        true,
                scrollCollapse: true,
                paging:         false,
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        } );

        $('.select2').select2({
            theme: 'bootstrap4'
        });
    </script>
@stop

<div class="table-responsive">
    <table width="100%" class="table default-table" id="chillerfbtable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Ekor/Pcs/Pack</th>
                <th>Berat</th>
                {{-- <th>Asal</th> --}}
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chiller_fg as $i => $chill)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $chill->item_name }}</td>
                    <td>{{ $chill->stock_item }} ekor</td>
                    <td>{{ $chill->stock_berat }} Kg</td>
                    {{-- <td>{{ $chill->tujuan }}</td> --}}
                    <td>{{ $chill->tanggal_produksi }}</td>
                    <td>
                        @if ($chill->status == 2)
                            <div class="row">
                                <div class="col pr-1"><input type="number" id="kirim_jumlah{{ $chill->id }}"
                                        class="form-control" placeholder="QTY"></div>
                                <div class="col px-1"><input type="number" id="kirim_berat{{ $chill->id }}"
                                        class="form-control" placeholder="Berat"></div>
                                <div class="col-auto pl-1"><button type="submit" class="btn btn-primary toabf"
                                        data-chiller="{{ $chill->id }}"> Kirim ke ABF</button></div>
                            </div>
                        @else
                            <button class="btn btn-success" disabled>Selesai</button>
                        @endif
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
    {{ $chiller_fg->render() }}
</div>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $('#chillerfbtable').DataTable({
            "bPaginate": true,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": true,
            "bAutoWidth": true
        });
    </script>
@stop
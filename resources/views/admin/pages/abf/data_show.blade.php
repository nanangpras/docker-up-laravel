<section class="panel">
    <div class="row">
        <div class="table-responsive card-body">
            <table class="table default-table dataTable">
                <thead>
                    <tr>
                        <th width="10px">No</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Asal/Tujuan</th>
                        <th>Qty</th>
                        <th>Berat</th>
                        <th width="100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $i => $row)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $row->item_name }}</td>
                            <td>{{ date('d/m/Y', strtotime($row->created_at)) }}</td>
                            <td>{{ $row->asal }}</td>
                            <td>{{ $row->qty_item ?: '0' }}</td>
                            <td>{{ $row->berat_item ?: '0' }}</td>
                            <td class="text-center">
                                @if ($row->status == 1)
                                <a class="btn btn-primary btn-sm" href="{{ route('abf.timbang', $row->id) }}">Timbang</a>
                                @else
                                    <button type="button" class="btn btn-success btn-sm  togudang" disabled data-kode="{{ $row->id }}">
                                        Selesai
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
@stop

@section('footer')
    <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    <script>
        $('.dataTable').DataTable({
            "bPaginate": true,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false
        });
    </script>
@stop
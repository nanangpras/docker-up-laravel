@if ($ekspedisi)

<table class="table default-table table-small">
    <thead>
        <th>Nama</th>
        <th>Detail</th>
        <th>Ekor/Pcs/Pack</th>
        <th>Berat</th>
        <th>#</th>
    </thead>
    <tbody>
        @foreach ($itemorders as $row)
        <tr>
            <td>{{ $row->itemorder->nama }}</td>
            <td>{{ $row->nama_detail }}</td>
            <td>{{ $row->fulfillment_qty }}</td>
            <td>{{ $row->fulfillment_berat }}</td>
            <td>
                <button type="button" data-id='{{ $row->id }}'
                    class="add_route btn btn-blue btn-sm">
                    Set <i class="fa fa-arrow-right"></i>

                </button>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>


@endif

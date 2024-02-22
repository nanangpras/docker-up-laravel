<div class="table-responsive">
    <table width="100%" class="table default-table">
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Nama</th>
                <th>Konsumen / Sub Item</th>
                <th>Plastik</th>
                <th>Lokasi</th>
                <th>Qty/Pcs/Pack</th>
                {{-- <th>Customer ID</th> --}}
                <th>Berat</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stock as $i => $val)
            <tr>
                <td>{{$loop->iteration+($stock->currentpage() - 1) * $stock->perPage()}}</td>
                <td>{{ $val->id ?? '#' }}</td>
                <td>
                    {{ $val->productitems->nama ?? '#' }}
                    @if ($val->selonjor)
                    <div class="font-weight-bold text-danger">SELONJOR</div>
                    @endif
                </td>
                <td>{{ $val->konsumen->nama ?? "" }}<br>@if ($val->sub_item) Keterangan : {{ $val->sub_item }} @endif
                </td>
                <td>{{ $val->plastik_group ?? 'Tidak Ada' }}</td>
                <td>{{ $val->productgudang->code ?? '#' }}</td>
                <td>{{ number_format(($val->total ?? '0')) }} </td>
                {{-- <td>{{ $val->customer_id }}</td> --}}
                <td>{{ number_format(($val->kg ?? '0'), 2) }} Kg</td>
                <td> <button class="btn btn-primary btn-rounded btn-sm float-right" 
                        data-nama="{{ $val->productitems->id ?? '' }}" data-kemasan="{{ $val->packaging ?? '' }}"
                        data-lokasi="{{ $val->productgudang->id ?? '' }}"
                        data-konsumen="{{ $val->konsumen->nama ?? ''}}"
                        data-subitem="{{ $val->sub_item ?? '' }}"
                        data-customerid="{{ $val->customerid ?? '' }}"
                        onclick="detailFilter($(this).data('nama'),$(this).data('kemasan'),$(this).data('lokasi'),$(this).data('konsumen'),$(this).data('subitem'),$(this).data('customerid'))">detail</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>



<div id="paginate_stock">
    {{ $stock->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    $('#paginate_stock .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#warehouse-stock').html(response);
        }

    });
});
</script>


<script>


    function detailFilter(nama,kemasan,lokasi,konsumen,subitem,customerid){
        let tanggal = $('#tanggal_akhir').val()
        window.location = "{{ route('warehouse.data_filter') }}?nama=" + nama +"&kemasan=" + kemasan + "&lokasi=" + lokasi + "&konsumen=" + konsumen + "&key=detail" + "&subitem=" + subitem + "&customerid=" + customerid + "&tanggal=" + tanggal;
    }
</script>
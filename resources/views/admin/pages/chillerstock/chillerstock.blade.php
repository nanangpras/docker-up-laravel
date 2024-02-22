<form method="GET" action="{{ route('chiller.showstock') }}" id="filter-form-submit">
    <div class="row mb-3">
        <div class="col-lg-3 col-6">
            <label>Mulai</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control change-filter" name="tglmulai" value="{{ $tglmulai }}">
        </div>
        <div class="col-lg-3 col-6">
            <label>Sampai</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control change-filter" name="tglend" value="{{ $tglend }}">
        </div>
    </div>
</form>

<table width="100%" id="chillerstock" class="table default-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>No Mobil</th>
            <th>Tanggal</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat (Kg)</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php
        $qty = 0;
        $berat = 0;
        @endphp
        @foreach ($stock as $i => $item)
        @php
        $qty += $item->stock_item;
        $berat += $item->stock_berat;
        @endphp
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $item->item_name }}</td>
            <td>{{ $item->no_mobil ?? '' }}</td>
            <td>{{ $item->tanggal_produksi }}</td>
            <td>{{ number_format($item->stock_item) }}</td>
            <td class="text-right">{{ number_format($item->stock_berat, 2) }}</td>
            <td>{{ $item->tujuan }}</td>
            <td>
                <button type="submit" class="btn btn-primary" data-toggle="modal"
                    data-target="#editchiller{{ $item->id }}">Edit</button>
            </td>
        </tr>

        @endforeach
    </tbody>
</table>
@foreach ($stock as $i => $item)
<div class="modal" id="editchiller{{ $item->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('chiller.editstock') }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $item->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">QTY</label>
                                <input type="text" name="qty" class="form-control" id="qty" placeholder="Tuliskan "
                                    value="" autocomplete="off">
                                @error('qty') <div class="small text-danger">{{ message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="">Berat</label>
                                <input type="text" name="berat" class="form-control" id="berat" placeholder="Tuliskan "
                                    value="" autocomplete="off">
                                @error('berat') <div class="small text-danger">{{ message }}</div> @enderror
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary ">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<br>
<div class="row">
    <div class="rounded mb-2 col-6 pr-1">
        <div class="border p-2">
            <label>Qty</label>
            <h5>{{ number_format($qty) }} Pcs</h5>
        </div>
    </div>
    <div class="rounded mb-2 col-6 pl-1">
        <div class="border p-2">
            <label>Berat</label>
            <h5>{{ number_format($berat, 2) }} Kg</h5>
        </div>
    </div>
</div>
<br>
<a href="{{ route('chiller.export') }}" class="btn btn-blue">Export CSV</a>

@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#chillerstock')) {
                $('#chillerstock').DataTable().destroy();
            }
            $('#chillerstock').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY: 500,
                scrollX: true,
                scrollCollapse: true,
                paging: false,
            });
        });

        var url = "{{ route('chiller.showstock') }}";

        $('.pagination a').on('click', function(e) {
            e.preventDefault();
            url = $(this).attr('href');
            filterChiller();
        });

        $('#filter-form-submit').on('submit', function(e) {
            e.preventDefault();
            url = $(this).attr('action') + "?" + $(this).serialize();
            console.log(url);
            filterChiller();
        })

        $('.change-filter').on('change', function() {
            $('#filter-form-submit').submit();
            filterChiller();
        })


        var searchTimeout = null;  

        $('#search-filter').on('keyup', function() {          
            if (searchTimeout != null) {
                clearTimeout(searchTimeout);
            }

            searchTimeout = setTimeout(function() {
                searchTimeout = null;  
                $('#filter-form-submit').submit();
                filterChiller();
            }, 1000);  

        })


        function filterChiller() {
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#chiller-stock').html(response);
                }
            });
        }
</script>
@stop
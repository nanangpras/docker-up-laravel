<div class="table-responsive">
    <table class="table table-sm table-striped table-hover">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Marketing</th>
                <th>Kategori</th>
                <th>Customer</th>
                <th>Nomor SO</th>
                <th>Memo</th>
                <th>Order</th>
                <th>Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr class="cursor" data-toggle="modal" data-target="#marketing{{ $row->id }}">
                <td>{{ $row->tanggal_so }}</td>
                <td>{{ $row->souser->name ?? '' }}</td>
                <td>{{ $row->socustomer->kategori ?? '' }}</td>
                <td>{{ $row->socustomer->nama ?? '' }}</td>
                <td>{{ $row->no_so ?? '' }}</td>
                <td>{{ $row->memo ?? '' }}</td>
                <td>{{ $row->jumlah }} Item</td>
                <td class="text-right">{{ number_format($row->nominal) }}</td>
            </tr>

<div class="modal fade" id="marketing{{ $row->id }}" aria-labelledby="marketing{{ $row->id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="marketing{{ $row->id }}Label">Data Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="bg-light p-2 mb-2">
                    <div class="row">
                        <div class="col-auto">
                            <div class="small font-weight-bold">Marketing</div>
                            {{ $row->souser->name ?? '' }}
                        </div>
                        <div class="col">
                            <div class="small font-weight-bold">Customer</div>
                            {{ $row->socustomer->nama ?? '' }}
                        </div>
                    </div>
                </div>

                @foreach ($row->listItem as $item)
                <div class="border-bottom py-1 clearfix">

                    <div class="row">
                        <div class="col pr-1">{{ $item->item_nama }}</div>
                        <div class="col-3 px-1 text-right">{{ number_format($item->qty) }} pcs/ekor</div>
                        <div class="col-3 pl-1 text-right">{{ number_format($item->berat, 2) }} kg</div>
                    </div>
                    <div class="float-right">Rp {{ number_format($item->harga) }}</div>
                    @if ($item->plastik)
                    <div class="ml-2">- Plastik :
                        @if($item->plastik=="")
                        Curah
                        @elseif($item->plastik=="1")
                        Meyer
                        @elseif($item->plastik=="2")
                        Avida
                        @elseif($item->plastik=="3")
                        Polos
                        @elseif($item->plastik=="4")
                        Bukan Plastik
                        @elseif($item->plastik=="5")
                        Mojo
                        @elseif($item->plastik=="5")
                        Other
                        @endif
                    </div>
                    @endif
                    @if ($item->bumbu)
                    <div class="ml-2">- Bumbu : {{ $item->bumbu }}</div>
                    @endif
                    {{-- {{ $item }} --}}
                </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
            @endforeach
        </tbody>
    </table>
</div>


<div id="paginate_so">
    {{ $data->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    $('#paginate_so .pagination a').on('click', function(e) {
        e.preventDefault();
        showNotif('Menunggu');

        url = $(this).attr('href');
        $.ajax({
            url: url,
            method: "GET",
            success: function(response) {
                $('#data_view').html(response);
            }
        });
    });
</script>

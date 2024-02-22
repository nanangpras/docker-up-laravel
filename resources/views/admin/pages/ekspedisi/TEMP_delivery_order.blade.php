@foreach ($order as $row)
<div class="card rounded-0 mb-3">
    <div class="card-header">
        <div class="float-right text-right">{{ $row->ordercustomer->nama }}</div>
        {{ $row->no_so }} <i class="fa fa-list-alt text-info" data-toggle="modal" data-target="#infoSO{{ $row->id }}"></i> <br>
        Kirim : {{ $row->tanggal_kirim }}
    </div>
    <div class="modal fade" id="infoSO{{ $row->id }}" tabindex="-1" aria-labelledby="infoSO{{ $row->id }}Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Informasi SO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold" for="no_so">Nama Konsumen</label>
                        <div id="no_so">{{ $row->ordercustomer->nama }}</div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold" for="no_so">Nomor SO</label>
                        <div id="no_so">{{ $row->no_so }}</div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold" for="no_so">Tanggal Kirim</label>
                        <div id="no_so">{{ $row->tanggal_kirim }}</div>
                    </div>
                    @if ($row->keterangan)
                    <div class="form-group">
                        <label class="font-weight-bold" for="no_so">Keterangan</label>
                        <div id="no_so">{{ $row->keterangan }}</div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-1">

        @if (COUNT($row->get_do))
        <table class="table default-table mb-0">
            <thead>
                <tr>
                    <th>Nomor DO</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($row->get_do as $item)
                <tr>
                    <td>
                        {{ $item->no_do }}
                        <i class="fa fa-tags text-info" data-toggle="modal" data-target="#infoDO{{ $item->id }}"></i>
                    </td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->berat }}</td>
                    <td class="p-1">
                        <button class="btn btn-outline-info btn-block buat_rute rounded-0" data-page="{{ $request->page }}" data-id="{{ $item->no_do }}"><i class="fa fa-arrow-right"></i></button>
                    </td>
                </tr>
                <div class="modal fade" id="infoDO{{ $item->id }}" tabindex="-1" aria-labelledby="infoDO{{ $item->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-body">
                            <h6 class="border-bottom pb-3 mb-2">{{ $item->no_do }}</h6>
                            <div class="font-weight-bold border-bottom py-1">
                                <div class="row">
                                    <div class="col-2 pr-1">No</div>
                                    <div class="col-6 px-1">Item</div>
                                    <div class="col-2 px-1 text-right">Qty</div>
                                    <div class="col-2 pl-1 text-right">Berat</div>
                                </div>
                            </div>
                            @foreach (App\Models\Bahanbaku::where('no_do', $item->no_do)->get() as $i => $list)
                            <div class="border-bottom py-1">
                                <div class="row">
                                    <div class="col-2 pr-1">{{ ++$i }}</div>
                                    <div class="col-6 px-1">{{ $list->nama }}</div>
                                    <div class="col-2 px-1 text-right">{{ number_format($list->bb_item) }}</div>
                                    <div class="col-2 pl-1 text-right">{{ number_format($list->bb_berat, 2) }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
        @else
        <h6 class="text-center py-1 text-info">-- DO KOSONG --</h6>
        @endif
    </div>
</div>
@endforeach


<div id="paginate_summary">
    {{ $order->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('#paginate_summary .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_so').html(response);
        }

    });
});
</script>


<script>
    $('.buat_rute').click(function() {
        var no_do           =   $(this).data('id') ;
        var page            =   $(this).data('page') ;
        var tanggal_kirim   =   $("#tanggal_kirim").val() ;
        var cari            =   encodeURIComponent($("#cari").val()) ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.buat_rute').hide() ;

        $.ajax({
            url: "{{ route('ekspedisi.store') }}",
            method: "POST",
            data: {
                no_do   :   no_do ,
                page    :   page ,
                key     :   'temporary'
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari + "&page=" + page) ;
                    $("#show_rute").load("{{ route('ekspedisi.index', ['key' => 'show_rute']) }}");
                }
                $('.buat_rute').show() ;
            }
        });
    })
</script>


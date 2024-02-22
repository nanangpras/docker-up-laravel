<div class="table-responsive">
    <table class="table table-sm default-table  table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Type</th>
                <th>Item</th>
                <th>Berat</th>
                <th>Status</th>
                <th>Proses</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fulfillment as $i => $full)
                @php
                    $berat = 0;
                    $item = 0;
                @endphp
                @foreach ($full->daftar_order as $tot)
                    @php
                        $berat = $berat + $tot->berat;
                        $item = $item + $tot->qty;
                    @endphp
                @endforeach
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $full->nama }}</td>
                    <td></td>
                    <td>{{ $item }} ekor</td>
                    <td>{{ $berat }} Kg</td>

                    <td>{!!$full->status_order!!}</td>

                    <td>
                        <div class="progress">
                            @php
                                $cuk = '';
                                $persen = ($full->status *10);
                                if (($persen) < 50) {
                                    $cuk = 'bg-danger';
                                }
                            @endphp
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: {{ $persen }}%"></div>
                        </div>
                        {{ number_format($persen, 2) }} %
                    </td>
                    <td>
                        <a href="{{ url('progress_report/view_data?role=marketing&key=marketing&_token=LbFMmcNeGOHXiTHMAarQMSDAafVhGILWqe3C2qeDKK7BOAwgRAVXdFB4KZCy.1db55a1e47f0154d8eb0b61c29de0904701c84ee89971576fad2efdc27c07710&name=EBA&GenerateToken=S29kZSBhY2FrIGtvZGUgZGlhY2FrIGFjYWsgYWNhayBhY2FrIGtvZGUgYmlhciBkYXBhdCBrb2RlIGtvZGUgeWFuZyBkaWFjYWs%3D&subkey=view_data_marketing
')}}" class="btn btn-primary btn-sm p-0 px-1 viewDetailMarketing" title='Detail' data-toggle="modal" data-target="#modaldatamarketing" data-id="{{$full->id}}" data-detailkey="detail_order_fulfillment">
                            Detail
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal fade" id="modaldatamarketing" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modaldatamarketingLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg" style="width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group text-center">
                    <b>Fulfillment Order</b>
                </div>
                <div id="spinerdata" class="text-center" style="display: none">
                    <img src="{{ asset('loading.gif') }}" width="30px">
                </div>
                <div id="content_modal_marketing"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(".viewDetailMarketing").click( function(e){
        e.preventDefault();
        var id       = $(this).data('id');
        var key      = $(this).data('detailkey');
        var href     = $(this).attr('href');

        $.ajax({
            url : href,
            type: "GET",
            data: {
                id          : id,
                key         : "view_data_marketing",
                detailkey   : "detail_order_fulfillment",

            },
            beforeSend: function(){
                $("#spinerdata").show();
            },
            success: function(data){
                $('#content_modal_marketing').html(data);
                $("#spinerdata").hide();
            }
        });
    });
</script>

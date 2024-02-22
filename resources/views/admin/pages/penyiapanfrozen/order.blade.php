@if(count($pending)=="0")
<div class="alert alert-danger">Item Order Belum Tersedia</div>
@endif

@foreach ($pending as $i => $val)

<b style="font-size: 9pt">{{ $loop->iteration + ($pending->currentpage() - 1) * $pending->perPage() }}.
    <a href="{{ route('editso.index', $val->id) }}" target="_blank">{{ $val->no_so }}</a> || {{ $val->nama }} <span
        class="pull-right"> {{ $val->sales_channel }} || Kirim : {{date('d/m/y',
        strtotime($val->tanggal_kirim))}}</span></b>
@if ($val->keterangan)
<br>{{ $val->keterangan }}
@endif

<table class="table default-table table-small">
    <thead>
        <tr>
            <th width="30%">Nama</th>
            <th width="60">Qty</th>
            <th width="60">Berat</th>
            <th width="50%">Pengalokasian
            </th>
        </tr>
    </thead>
    <tbody>
        @php
        $close_order = false;
        @endphp
        @foreach ($val->daftar_order_frozen as $i => $item)
        @php
        if($item->status==2){
        $close_order = true;
        }
        @endphp
        <tr>
            <td>{{ $item->nama_detail }}
                @if ($item->memo!="")
                <br><span class="status status-info">{{ $item->memo }}</span>
                @endif
                @if ($item->part!="")
                <br><span class="status status-warning">Potong {{ $item->part }}</span>
                @endif
                @if ($item->bumbu!="")
                <br><span class="status status-success">{{ $item->bumbu }}</span>
                @endif
            </td>
            <td>{{ $item->qty ?? '0' }}</td>
            <td>{{ $item->berat ?? '0' }} kg</td>
            <td>
                <div class="order_item_bahan_baku" data-id="{{$item->id}}" id="order_bahan_baku{{$item->id}}"><img
                        src="{{asset('loading.gif')}}" style="width: 18px"> Loading ...</div>
                @if($val->status == "0" )
                <span class="status status-danger">Dibatalkan</span>
                @else
                @if($item->status== NULL)
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#exampleModal"
                    id="form-item-name{{ $item->id }}"
                    onclick="return selected_id('{{$val->id}}','{{$item->id}}','{{$item->item_id}}')">
                    Pilih Item Dari Storage <span class="fa fa-chevron-down"></span>
                </button>
                <a href="javascript:void(0)" class="btn btn-success btn-sm fulfill-item" data-id="{{$item->id}}"
                    id="fulfill-item{{$item->id}}">Simpan</a>
                <div class="status status-warning status{{$item->id}}" style="display: none">Selesai dialokasikan</div>
                @else
                <div class="status status-success">Selesai</div>
                @endif
                @endif
            </td>
        </tr>

        @endforeach
        <tr>
            <td colspan="4">
                <span style="color: #bbbbbb">SO Masuk : {{ date('d/m/y H:i:s', strtotime($val->created_at)) }}</span>
                @if($val->status == "0" )
                <span class="status status-danger">Dibatalkan</span>
                @else
                @if($close_order==true)
                {{-- <span class="btn btn-green pull-right btn-sm">Diambil Langsung</span> --}}
                @else
                {{-- <a href="{{route('penyiapanfrozen.closeorder')}}?order_id={{$val->id}}"
                    class="btn btn-red pull-right btn-sm">Selesaikan</a> --}}
                @endif
                {{-- <a href="" class="btn btn-blue pull-right btn-sm mr-1">Ekspedisi</a> --}}
                @endif
            </td>
        </tr>
    </tbody>
</table>
@endforeach



<div style="min-width: 200px">

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('penyiapanfrozen.simpanalokasi') }}" method="POST" id="submit-alokasi">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Item Storage</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="selected_order_id" value="">
                        <input type="hidden" name="order_item_id" id="selected_order_item_id" value="">
                        <input type="hidden" name="item_id" id="selected_item_id" value="">
                        <div class="row">
                            <div class="col">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="selected_tanggal" class="form-control"
                                    value="{{date('Y-m-d', strtotime('-1 month'))}}">
                            </div>
                            <div class="col">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif id="selected_tanggal_akhir" class="form-control"
                                    value="{{date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="text" id="pencarian-stock" class="form-control mt-2" value=""
                                    placeholder="Pencarian">
                            </div>
                        </div>
                        <div id="list-penyiapanfrozen-storage"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>


<script>
    var item_id = "";
    var order_id = "";
    var order_item_id = "";
    var tanggal = $('#selected_tanggal').val();
    var tanggal_akhir = $('#selected_tanggal_akhir').val();
    var pencarian = $('#pencarian-stock').val();

    load_penyiapanfrozen();

    $('.order_item_bahan_baku').each(function(i){
        var id = $(this).attr('data-id');
        var url_pemenuhan = "{{route('penyiapanfrozen.pemenuhan')}}"+"?order_item_id="+id;
        $('#order_bahan_baku'+id).load(url_pemenuhan)

    });

    $('.fulfill-item').on('click', function(){
        var orderitemid = $(this).attr('data-id');
        $(".fulfill-item").hide();
        console.log("{{ route('penyiapanfrozen.fulfillitem')}}?order_item_id="+orderitemid)
        $.ajax({
            url: "{{ route('penyiapanfrozen.fulfillitem')}}?order_item_id="+orderitemid,
            type: 'get',
            success: function(data) {
                console.log(data);
                var url_pemenuhan = "{{route('penyiapanfrozen.pemenuhan')}}"+"?order_item_id="+orderitemid;

                if(data.status=="400"){
                    showAlert(data.msg)
                    $(".fulfill-item").show();
                }else{
                    showNotif("Alokasi diselesaikan")
                    $('#order_bahan_baku'+orderitemid).load(url_pemenuhan)
                    $(".fulfill-item").show();
                    $('#fulfill-item'+orderitemid).remove();
                    $('#form-item-name'+orderitemid).hide();
                    $('.status'+orderitemid).show();
                }
            }
        });
    })


    $('#submit-alokasi').on('submit', function(e){
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('penyiapanfrozen.simpanalokasi') }}",
            type: 'post',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    console.log(data);
    
                    id = $('#selected_order_item_id').val();
                    var url_pemenuhan = "{{route('penyiapanfrozen.pemenuhan')}}"+"?order_item_id="+id;
                    console.log(url_pemenuhan);
                    $('#order_bahan_baku'+id).load(url_pemenuhan);
                    $('#exampleModal').modal('toggle');

                }

            }
        });

    })

    function selected_id(orderid, orderitemid, itemid){
        console.log(orderid);
        console.log(orderitemid);
        console.log(itemid);
        $('#selected_order_id').val(orderid);
        $('#selected_item_id').val(itemid);
        $('#selected_order_item_id').val(orderitemid);

        item_id = $('#selected_item_id').val();
        order_id = $('#selected_order_id').val();
        order_item_id = $('#selected_order_item_id').val();

        tanggal = $('#selected_tanggal').val();
        tanggal_akhir = $('#selected_tanggal_akhir').val();

        load_penyiapanfrozen();
    }

    $('#selected_tanggal').on('change', function(){
        load_penyiapanfrozen();
    })

    $('#selected_tanggal_akhir').on('change', function(){
        load_penyiapanfrozen();
    })

    $('#pencarian-stock').on('keyup', function(){
        load_penyiapanfrozen();
    })


    function load_penyiapanfrozen(){

        tanggal = $('#selected_tanggal').val();
        tanggal_akhir = $('#selected_tanggal_akhir').val();
        pencarian = encodeURIComponent($('#pencarian-stock').val());

        $.ajax({
            url: "{{route('penyiapanfrozen.storage')}}"+'?tanggal='+tanggal+'&item_id='+item_id+'&tanggal_akhir='+tanggal_akhir+'&pencarian='+pencarian,
            method: "GET",
            success: function(data) {
                $('#list-penyiapanfrozen-storage').html(data);
                console.log("{{route('penyiapanfrozen.storage')}}"+'?tanggal='+tanggal+'&item_id='+item_id+'&tanggal_akhir='+tanggal_akhir+'&pencarian='+pencarian)
                $('.selected-penyiapanfrozen-storage').on('click', function(){
                    var id = $(this).attr('data-id');
                    var nama = $(this).attr('data-nama');
                    var berat = $(this).attr('data-berat');

                    focusCode(id, id, nama, berat);
                })

            }
        })

    }


    function focusCode(id, code, name, berat) {
        console.log(name)
        console.log($('#form-item'+id).length);
        $('#form-item'+id).val(code);
        $('#form-item-name'+id).html(name);
        $('#berat'+id).on('keyup', function(){
            var input_val = $(this).val();
            if(input_val>berat){
                $(this).val(berat);
                showAlert("Max item tidak boleh lebih dari stock");
            }
        });
    }



</script>

<br>
{{ $pending->appends(\Illuminate\Support\Facades\Request::except('page'))->links() }}
</div>


<script>
    $('.pagination a').on('click', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#show').html(response);
        }

    });
});

</script>
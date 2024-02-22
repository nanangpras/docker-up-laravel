@php
$pcs    =   0 ;
$kg     =   0 ;
@endphp
@foreach($pemenuhan as $row)
    @php
    $pcs    +=  $row->bb_item ;
    $kg     +=  $row->bb_berat ;
    @endphp
    <div class="border-bottom">
        {{$row->chiller_out}}. {{$row->nama}}
        ({{$row->bb_item ?? "#"}} pcs || {{$row->bb_berat}} Kg)
        @if($row->status==1)
        <a href="javascript:void(0)" class="remove-pemenuhan" data-id="{{$row->id}}"  data-orderitemid="{{$row->order_item_id}}"><span class="fa fa-trash"></span></a>
        @endif
    </div>
@endforeach

@if (COUNT($pemenuhan))
Total : {{$pcs ?? "#"}} pcs || {{$kg}} Kg
@endif

<script>
    $('.remove-pemenuhan').on('click', function(){
        var delete_id = $(this).attr('data-id');
        var orderitemid = $(this).attr('data-orderitemid');
        deletePemenuhan(delete_id, orderitemid);
    })

    function deletePemenuhan(delete_id, orderitemid){
        $.ajax({
            url: "{{ route('sampingan.deletealokasi')}}?id="+delete_id,
            type: 'get',
            success: function(data) {
                var url_pemenuhan = "{{route('sampingan.pemenuhan')}}"+"?order_item_id="+orderitemid;
                $('#order_bahan_baku'+orderitemid).load(url_pemenuhan)
                $('#riwayat_ambil').load("{{ route('sampingan.pemenuhan') }}?order_item_id=" + orderitemid);
                $('#info_order').load("{{ route('sampingan.pemenuhan') }}?key=info&order_item_id=" + orderitemid);
                load_sampingan();
            }
        });
    }

</script>

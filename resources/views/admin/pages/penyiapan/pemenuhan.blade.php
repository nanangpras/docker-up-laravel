@php
$pcs    =   0 ;
$kg     =   0 ;
@endphp
@foreach ($pemenuhan as $no => $row)
    @php
    $pcs    +=  $row->bb_item ;
    $kg     +=  $row->bb_berat ;
    @endphp

    <div class="border-bottom">
        @if($row->proses_ambil=="chillerfg" || $row->proses_ambil=="sampingan")
            <a href="{{route('chiller.show',  $row->chiller_out)}}" target="_blank">{{++$no}}.</a>
        @else
            <a href="#">{{++$no}}.</a>
        @endif
        {{ $row->nama }}
        ({{ $row->bb_item ?? '#' }} pcs || {{ $row->bb_berat }} Kg)
        @if ($row->status == 1 && $row->bahanbborder->status <= 6)
            <a href="javascript:void(0)" class="remove-pemenuhan" data-id="{{ $row->id }}"
                data-orderitemid="{{ $row->order_item_id }}"><span class="fa fa-trash"></span>
            </a>
        @endif
        @if ($row->proses_ambil == 'chiller-fg')
            @if ($row->to_chiller->label)
                @php
                    $exp = json_decode($row->to_chiller->label);
                @endphp
                @if ($exp->parting->qty)
                    <span class="status status-info">Parting {{ $exp->parting->qty }}</span>
                @endif
                @if ($exp->additional->tunggir)
                    <span class="status status-warning">Tanpa Tunggir</span>
                @endif
                @if ($exp->additional->maras)
                    <span class="status status-warning">Tanpa Maras</span>
                @endif
                @if ($exp->additional->lemak)
                    <span class="status status-warning">Tanpa Lemak</span>
                @endif
            @endif
        @endif
    </div>
@endforeach

@if (COUNT($pemenuhan))
Total : {{$pcs ?? "#"}} pcs || {{$kg}} Kg
@endif

<script>
    $('.remove-pemenuhan').on('click', function() {
        var delete_id = $(this).attr('data-id');
        var orderitemid = $(this).attr('data-orderitemid');
        deletePemenuhan(delete_id, orderitemid);
    })

    function deletePemenuhan(delete_id, orderitemid) {
        $.ajax({
            url: "{{ route('penyiapan.deletealokasi') }}?id=" + delete_id,
            type: 'get',
            success: function(data) {
                var url_pemenuhan = "{{ route('penyiapan.pemenuhan') }}" + "?order_item_id=" +
                    orderitemid;
                $('#order_bahan_baku' + orderitemid).load(url_pemenuhan)
                $('#info_order').load("{{ route('penyiapan.pemenuhan') }}?key=info&order_item_id=" +
                    orderitemid);
                $('#riwayat_ambil').load("{{ route('penyiapan.pemenuhan') }}?order_item_id=" +
                    orderitemid);
                load_penyiapan();
            }
        });
    }
</script>

@php
$pcs    =   0 ;
$kg     =   0 ;
$krj    =   0 ;
$do     =   [];
@endphp
@foreach ($pemenuhan as $no => $row)
    @php
        $pcs    +=  $row->bb_item ;
        $kg     +=  $row->bb_berat ;
        $krj    +=  $row->keranjang ;
    @endphp

    <div class="border-bottom">

        @if($row->no_do!="")
            @if(!in_array($row->no_do, $do))
                @php
                    $do[] = $row->no_do;
                @endphp
                <span class="red">{{$row->no_do ?? "#DO"}} || {{date('d/m/y H:i:s', strtotime($row->updated_at))}} </span><br>
            @endif
        @endif


        @if($row->proses_ambil=="chillerfg" || $row->proses_ambil=="sampingan")
            <a href="{{route('chiller.show',  $row->chiller_out)}}" target="_blank">{{++$no}}.</a>
        @else
            <a href="#">{{++$no}}.</a>
        @endif
        {{ $row->nama }}
        ({{ $row->bb_item ?? '#' }} pcs || {{ $row->bb_berat }} Kg @if ($row->keranjang) || {{ $row->keranjang }} Keranjang @endif) 
        @if ($row->relasi_netsuite)||
        <a href="{{url('admin/sync-detail/'.$row->netsuite_id)}}" target="_blank"><span class="green">{{$row->relasi_netsuite->document_no ?? "#TI"}}</span></a> || @endif  
        <a href="{{url('admin/sync/'.$row->netsuite_id)}}" target="_blank" class="orange">
            @if($row->netsuite_id!="")
            <span class="fa fa-share"></span>
            @endif
        </a>

        @if ($row->status == 1 && $row->bahanbborder->status <= 6)
            <a href="javascript:void(0)" onclick="return deletePemenuhan({{ $row->id }}, {{ $row->order_item_id }}, {{ $row->order_id }})" data-orderid="{{$row->order_id}}" data-id="{{ $row->id }}"
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
Total : {{$pcs ?? "#"}} pcs || {{$kg}} Kg || {{ $krj }} Keranjang<br>
@endif

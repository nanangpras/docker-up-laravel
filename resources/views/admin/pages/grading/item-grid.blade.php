<div class="item-outer">
    <div class="scroll-inner">

        @php $index_item = 0; @endphp
        @php $last_item_id = $grading_last->item_id ?? "" ;@endphp
        @foreach ($item as $i => $row)
            @if($row->id==$last_item_id)
                @php $index_item = $i; @endphp
            @endif
        @endforeach

        @php 
                $item_selected = $index_item+1;
                if($item_selected>count($item)){
                    $item_selected = $index_item;
                }
            
        @endphp

        @foreach ($item as $i => $row)
            <div class="p-2">
                <input type="radio" name="part" class="part" value="{{ $row->id }}" id="{{ $row->id }}" @if($select=="") @if($type=='normal' || $type == 'memar' || $type == 'utuh' || $type == 'pejantan') { @if($item_selected == $i) checked @endif @endif @else @if($select == $row->id) checked @endif @endif>
                <label for="{{ $row->id }}">{{ str_replace('Pejantan ', '', str_replace('Parent ', '', str_replace('Karkas ', '', str_replace('Ayam ', '', $row->nama)))) }}</label>
            </div>
        @endforeach
    </div>
</div>
<style>

    #jenisayam{
        width: 100%;
    }

    .item-outer{
        overflow-x: scroll;
    }
    .scroll-inner{
        width: 100%;
        display: inline-flex;
    }
</style>

@php 
    $geser         = $item_selected * 65;
    $geserPejantan = $item_selected * 85;
@endphp
<script>
if ({{ $type }} == pejantan) {
    $('.item-outer').scrollLeft({{$geserPejantan}});
} else {
    $('.item-outer').scrollLeft({{$geser}});
}
</script>

@foreach ($list as $item)
<input type="radio" name="ukuran_ayam{{ $item->freestock_id }}" value="{{ $item->id }}" id="freestock{{ $item->id }}">
<label for="freestock{{ $item->id }}">
    <div class="float-right">
        {{ number_format($item->sisa) }}
    </div>
    {{ $item->item->nama }}
</label>
@endforeach

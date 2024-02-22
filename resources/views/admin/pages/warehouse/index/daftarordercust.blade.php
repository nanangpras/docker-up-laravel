<label for="item_order">Item</label>
<select name="item" id="item_order" class="form-control item_order select2" onchange="item_order()" data-width="100%" >
    <option value="">Semua</option>
    @foreach ($daftarordercust as $p)
        <option value="{{ $p->nama_detail }}" {{  $p->nama_detail == $itemorder ? 'selected' : '' }}>{{$p->sku}} - {{ $p->nama_detail }}
        </option>
    @endforeach
</select>

<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>


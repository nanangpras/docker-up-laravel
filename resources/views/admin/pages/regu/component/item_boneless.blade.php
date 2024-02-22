<div class="row mb-3">
    <div class="col-8 pr-1">
        Item
        <select name="itemfree" class="form-control select2" data-width="100%" data-placeholder="Pilih Item" id="itemfree">
            <option value=""></option>
            @foreach ($item as $id => $list)
                <option value="{{ $id }}">{{ $list }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-2 px-1">
        Qty
        <input type="number" name="jumlah" id="jumlah" class="form-control form-control-sm p-1" placeholder="Qty" autocomplete="off">
    </div>
    <div class="col-2 pl-0">
        Berat
        <input type="number" name="berat" id="berat" class="form-control form-control-sm p-1" step="0.01" placeholder="Berat" autocomplete="off">
    </div>
</div>

<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })
</script>

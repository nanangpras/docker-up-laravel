<div class="modal-body">
    <div class="row mb-3">
        <div class="col">
            <label for="nama">Nama</label>
            <input type="text" name="nama_item" class="form-control" id="nama_item" value="{{$detail->nama}}" readonly>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label for="nama">Qty</label>
            <input type="text" name="qty" class="form-control" id="qty" value="{{$detail->qty}}" readonly>
        </div>
        <div class="col">
            <label for="nama">Berat</label>
            <input type="text" name="berat" class="form-control" id="berat" value="{{$detail->berat}}" readonly>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label for="nama">Gudang</label>
            <select name="gudang" class="form-input-table form-control" id="waretujuan" readonly>
                <option value="" disabled selected hidden>Pilih</option>
                @foreach ($list_gudang as $ware)
                    <option value="{{ $ware->id }}" @if ($detail->gudang_id == $ware->id) selected @endif>{{ $ware->code }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
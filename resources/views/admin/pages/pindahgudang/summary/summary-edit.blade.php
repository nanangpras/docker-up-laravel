<form action="{{route('pindah.update',$edit_data->id)}}" method="post">
    @csrf
    @method('PATCH')
    <div class="modal-body">
        {{-- <input type="text" hidden id="idpindah" value="{{$edit_data->id}}">
        <input type="text" hidden name="key" value="update_pindah_gudang"> --}}
        <div class="row mb-3">
            <div class="col">
                <label for="nama">Nama</label>
                <input type="text" name="nama_item" class="form-control" id="nama_item" value="{{$edit_data->nama}}" readonly>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="nama">Qty</label>
                <input type="text" name="qty" class="form-control" id="qty" value="{{$edit_data->qty}}">
            </div>
            <div class="col">
                <label for="nama">Berat</label>
                <input type="text" name="berat" class="form-control" id="berat" value="{{$edit_data->berat}}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="nama">Gudang</label>
                <select name="gudang" class="form-input-table form-control" id="waretujuan">
                    <option value="" disabled selected hidden>Pilih</option>
                    @foreach ($list_gudang as $ware)
                        <option value="{{ $ware->id }}" @if ($edit_data->gudang_id == $ware->id) selected @endif>{{ $ware->code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
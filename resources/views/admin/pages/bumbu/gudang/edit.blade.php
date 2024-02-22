<form action="{{ route('bumbu.update',$edit->id) }}" method="post">
    @csrf 
    @method('patch')
    <input type="hidden" name="key" value="bumbu_gudang">
    <div class="modal-body">
        {{-- <div class="form-group">
            Stock Bumbu
            <input type="text" name="stock" class="form-control" value="{{$edit->stock}}">
        </div> --}}
        <div class="form-group">
            Berat Bumbu
            <input type="text" name="berat" class="form-control" value="{{$edit->berat}}">
        </div>
        <div class="form-group">
            Status
            <select name="status" id="status" class="form-control">
                <option value="masuk" {{$edit->status == 'masuk' ? 'selected' : ''}}>Masuk</option>
                <option value="keluar" {{$edit->status == 'keluar' ? 'selected' : ''}}>Keluar</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
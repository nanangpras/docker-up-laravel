<form action="{{ route('bumbu.update',$edit->id) }}" method="post">
    @csrf 
    @method('patch')
    <input type="hidden" name="key" value="bumbu_admin">
    <div class="modal-body">
        <div class="form-group">
            <label for="namabumbu">Nama Bumbu</label>
            <input type="text" id="namabumbu" name="nama" class="form-control" value="{{$edit->nama}}">
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
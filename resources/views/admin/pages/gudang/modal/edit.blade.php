<form action="{{route('gudang.update',$data->id)}}" method="POST">
    @csrf
    @method('PATCH')
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col">
                <label for="nama">Nama</label>
                <input type="text" name="nama_gudang" class="form-control" id="nama_item" value="{{$data->code}}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="nama">Netsuite ID</label>
                <input type="text" name="netid" class="form-control" id="qty" value="{{$data->netsuite_internal_id}}">
            </div>
            <div class="col">
                <label for="nama">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="1" @if ($data->status == 1) selected @endif >Aktif</option>
                    <option value="0" @if ($data->status == 0) selected @endif>Tidak Aktif</option>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="nama">Subsidiary</label>
                <select name="subsidiary" id="subsidiary" class="form-control">
                    <option value="EBA" @if ($data->subsidiary == "EBA") selected @endif >EBA</option>
                    <option value="CGL" @if ($data->subsidiary == "CGL") selected @endif>CGL</option>
                    <option value="MPP" @if ($data->subsidiary == "MPP") selected @endif>MPP</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
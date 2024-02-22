<div class="modal-body">
    <div class="row mb-3">
        <div class="col">
            <label for="nama">Nama</label>
            <input type="text" name="nama_item" class="form-control" id="nama_item" value="{{$data->code}}" readonly>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label for="nama">Netsuite ID</label>
            <input type="text" name="qty" class="form-control" id="qty" value="{{$data->netsuite_internal_id}}" readonly>
        </div>
        <div class="col">
            <label for="nama">Status</label>
            <input type="text" name="berat" class="form-control" id="berat" value="{{$data->status}}" readonly>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label for="nama">Subsidiary</label>
            <input type="text" name="subsidiary" class="form-control" value="{{$data->subsidiary}}" readonly>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
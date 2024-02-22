<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="modal{{ $datalpah->id }}Label">EDIT TIMBANG LPAH</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">

        <div class="form-group">
            <div class="form-group">
                <div class="small">Tipe
                </div>
                <select name="tipe_timbang" class="form-control" id="tipe_timbang{{ $datalpah->id }}">
                    <option value="isi" {{ $datalpah->type == 'isi' ? 'selected' : '' }}>
                        Isi</option>
                    <option value="kosong" {{ $datalpah->type == 'kosong' ? 'selected' : '' }}>
                        Kosong</option>
                </select>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Berat
                        <input type="number" id="berat{{ $datalpah->id }}" name="berat" class="form-control"
                            value="{{ $datalpah->berat }}" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" data-id="{{ $datalpah->id }}" class="edit_cart btn btn-primary">Save changes</button>
    </div>
</div>

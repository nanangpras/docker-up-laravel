<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="bbLabel">Edit Ambil Bahan Baku</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{ route('evis.updateperuntukan', ['key' => 'bahan_baku']) }}" method="post">
        @csrf @method('patch')
        <input type="hidden" name="x_code" value="{{ $id }}">
        <input type="hidden" name="form-edit-nama-item" value="{{ $nama }}" >
        <div class="modal-body">
            <div class="form-group">
                <div>Item</div>
                <b>{{ $nama }}</b>
            </div>
            <div class="row">
                <div class="col pr-1">
                    <div class="form-group">
                        Ekor/Qty
                        <input type="number" name="qty" class="form-control" value="{{ $qty }}" >
                    </div>
                </div>
                <div class="col pl-1">
                    <div class="form-group">
                        Berat
                        <input type="number" name="berat" step="0.01" class="form-control" value="{{ $berat }}" min="0" max="{{ $sisaBerat }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Edit</button>
        </div>
    </form>
</div>
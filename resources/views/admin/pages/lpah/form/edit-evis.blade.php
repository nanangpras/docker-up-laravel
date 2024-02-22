<form action="{{ route('evis.editevis') }}" method="post">
    @csrf <input type="hidden" name="key" value="checker">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modal{{ $dataevis->id }}Label">EDIT
                EVIS</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="idedit" value="{{ $dataevis->id }}">
            <label for="">Item : {{ $dataevis->eviitem->nama }}</label>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        QTY
                        <input type="number" name="qty" class="form-control" value="{{ $dataevis->total_item }}">
                    </div>
                </div>

                <div class="col">
                    <div class="form-group">
                        BERAT
                        <input type="number" name="berat" class="form-control" value="{{ $dataevis->berat_item }}"
                            step="0.01" required>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">OK</button>
        </div>
    </div>
</form>

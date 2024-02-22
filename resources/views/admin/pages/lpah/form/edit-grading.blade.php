<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Ubah Data Grading</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{ route('grading.ubah', $produksi) }}" method="post">
        @csrf @method('patch') <input type="hidden" name="x_code" value="{{ $datagrading->id }}">
        <input type="hidden" name="checker" value="1">
        <div class="modal-body">
            <div class="form-group">
                Item
                <div>{{ $datagrading->graditem->nama ?? '###' }}</div>
                {{ $datagrading->id }}
            </div>

            <div class="row">
                <div class="col pr-1">
                    <div class="form-group">
                        Ekor
                        <input type="number" value="{{ $datagrading->total_item }}" name="ekor" class="form-control">
                    </div>
                </div>
                <div class="col pl-1">
                    <div class="form-group">
                        Berat
                        <input type="number" value="{{ $datagrading->berat_item }}" name="berat" step="0.01"
                            class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-group">
                Keterangan
                <textarea name="keterangan" rows="2" required class="form-control">{{ $datagrading->keterangan }}</textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Ubah</button>
        </div>
    </form>
</div>

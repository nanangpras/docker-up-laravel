<div class="bg-white p-3" style="margin-top: 30px;">
    <div class="row">
        <div class="col-md-5">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        Total Item
                        <input type="text" readonly class="form-control bg-white" value="{{ number_format($total_item) }}">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        Berat
                        <input type="text" readonly class="form-control bg-white" value="{{ number_format($total_berat, 2) }}">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        Qty
                        <input type="text" readonly class="form-control bg-white" value="{{ number_format($total_qty) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-5">
            <div class="row">
                <div class="col">
                    &nbsp;
                    <form action="{{ route('driver.destroy', $id) }}" method="post">
                        @csrf @method('delete')
                        <button type="submit" class="btn btn-block btn-outline-primary">Batalkan</button>
                    </form>
                </div>
                <div class="col">
                    &nbsp;
                    <a href="{{ route('driver.index') }}" class="btn btn-primary btn-block"> Simpan </a>
                    {{-- <form action="{{ route('driver.ready', $id) }}" method="post">
                        @csrf @method('patch')
                        <button type="submit" class="btn btn-block btn-primary">Selesaikan</button>
                    </form> --}}
                </div>
            </div>
        </div>
    </div>
</div>

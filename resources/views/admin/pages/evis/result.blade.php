<section class="panel">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Total Proses</label>
                    <div class="input-group input-group-lg">
                        <input type="text" value="{{ number_format($total['jumlah']) }}" name="jumlah"
                            class="text-right bg-white form-control" id="jumlah" readonly>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Total Ekor</label>
                    <div class="input-group input-group-lg">
                        <input type="text" value="{{ number_format($total['ekor']) }}" name="ekor"
                            class="text-right bg-white form-control" id="ekor" readonly>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Total Berat</label>
                    <div class="input-group input-group-lg">
                        <input type="text" value="{{ number_format($total['berat'], 2) }}" name="totalberat"
                            class="text-right bg-white form-control" id="totalerat" readonly>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label></label>
                    <form action="{{ route('evis.update', $data->id) }}" method="POST">
                        @csrf @method('patch')
                        @if ($data->evis_status == 1)
                            <button type="submit" class="btn-lg mt-1 btn btn-primary btn-block"
                                disabled>Selesaikan</button>
                        @else
                            <button type="submit" class="btn-lg mt-1 btn btn-primary btn-block">Selesaikan</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

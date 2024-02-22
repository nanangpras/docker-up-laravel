<div class="row mt-3">
    <div class="col-lg-2 col-6">
        <label for="selesai">Selesai</label>
        <input class="form-control form-control-lg text-right bg-white" id="selesai" readonly
            value="{{ number_format($hitung['done']) }}">
    </div>
    <div class="col-lg-2 col-6">
        <label for="pending">Pending</label>
        <input class="form-control form-control-lg text-right bg-white" id="pending" readonly
            value="{{ number_format($hitung['pending']) }}">
    </div>
    <div class="col-lg-2 col-6">
        <label for="berat_total">Berat Total</label>
        <input class="form-control form-control-lg text-right bg-white" id="berat_total" readonly
            value="{{ number_format($hitung['berat_total'], 2) }}">
    </div>
    <div class="col-lg-2 col-6">
        <label for="total_ekor">Total Ekor</label>
        <input class="form-control form-control-lg text-right bg-white" id="total_ekor" readonly
            value="{{ number_format($hitung['total_ekor']) }}">
    </div>
</div>
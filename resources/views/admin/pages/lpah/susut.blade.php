<div class="card mb-4">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg col-sm-3 col-6 pr-1">
                <div class="form-group">
                    Ekor Seckle
                    <input type="number" name="ekoran_seckle" class="form-control form-control-sm" value="{{ $susut->ekoran_seckle }}" id="ekoran_seckle" placeholder="Ekor Seckle" autocomplete="off">
                </div>
            </div>

            <div class="col-lg col-sm-3 col-6 px-sm-1 pl-1">
                <div class="form-group">
                    Keranjang
                    <input type="number" name="keranjang" class="form-control form-control-sm" value="{{ $susut->lpah_jumlah_keranjang }}" id="jml_keranjang" placeholder="Keranjang" autocomplete="off">
                </div>
            </div>

            <div class="col-lg col-sm-3 col-6 px-sm-1 pr-1">
                <div class="form-group">
                    Ekor Mati
                    <input type="number" name="mati" class="form-control form-control-sm" value="{{ $susut->qc_ekor_ayam_mati }}" id="mati" placeholder="Mati" autocomplete="off">
                </div>
            </div>

            <div class="col-lg col-sm-3 col-6 px-lg-1 pl-1">
                <div class="form-group">
                    Berat Mati
                    <input type="number" name="matikg" class="form-control form-control-sm" value="{{ $susut->qc_berat_ayam_mati }}" id="matikg" placeholder="Mati Kg" autocomplete="off">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg col-sm-3 col-6 pr-1">
                <div class="form-group">
                    Ekor Ayam Merah
                    <input type="number" name="ekorayammerah" class="form-control form-control-sm" value="{{ $susut->qc_ekor_ayam_merah }}" id="ekorayammerah" placeholder="Ekor Ayam Merah" autocomplete="off">
                </div>
            </div>
            {{-- <div class="col-lg col-sm-3 col-6 pl-1">
                <div class="form-group">
                    Berat Ayam Merah
                    <input type="number" name="ayammerah" class="form-control form-control-sm" value="{{ $susut->qc_berat_ayam_merah }}" id="ayammerah" placeholder="Ayam Merah Kg" autocomplete="off">
                </div>
            </div> --}}
            {{-- <div class="pr-1">
                &nbsp;
                <div class="form-group">
                    <label class="px-2 pt-2 rounded status-info">
                        <input id="hitung_ayam" type="checkbox" @if($susut->qc_hitung_ayam_merah == 1) checked @endif> <label for="hitung_ayam" style="font-size: 10px"><b>PENGURANGAN</b></label>
                    </label>
                </div>
            </div> --}}

    
            @if(env('NET_SUBSIDIARY', 'CGL')=='CGL')
            <div class="col-lg col-sm-3 pl-1">
                <div class="form-group">
                    Tembolok (kg)
                    <input type="number" name="tembolok" class="form-control form-control-sm" value="{{ $susut->qc_tembolok }}" id="tembolok" placeholder="Tembolok Kg" autocomplete="off">
                </div>
            </div>
            @endif
            <div class="col-lg col-sm-3 col-6 px-lg-1 pr-1">
                <div class="form-group">
                    Bersih Keranjang
                    <input type="text" name="kebersihanKeranjang" class="form-control form-control-sm" value="{{ $susut->lpah_kebersihan_keranjang }}" id="kebersihanKeranjang" placeholder="Bersih/Kotor" autocomplete="off">
                </div>
            </div>
            {{-- <div class="col-lg col-sm-3 col-6 px-lg-1">
                <div class="form-group">
                    Downtime
                    <input type="number" name="downtime" class="form-control form-control-sm" value="{{ $susut->lpah_downtime }}" id="downtime" placeholder="Downtime Menit" autocomplete="off">
                </div>
            </div> --}}
        </div>
        <button type="button" class="btn btn-primary btn-block susut_submit">Simpan</button>
    </div>
</div>

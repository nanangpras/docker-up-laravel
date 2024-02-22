<form action="{{ route('qc.post', $data->id) }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-6 pr-1 pr-lg-3 col-lg-12">
                    <div class="form-group">
                        <label class="form-label">Ayam Merah Ekor</label>
                        <input type="text" value="{{ $postm->ayam_merah }}" name="ayammerah" class="form-control"
                            id="ayammerah" readonly>
                    </div>
                </div>
                <div class="col-6 pl-1 pl-lg-3 col-lg-12">
                    <div class="form-group">
                        <label class="form-label">Jumlah Sampling</label>
                        <select class="form-control" name="sampling" id="sampling">
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Tambahan Catatan</label>
                <textarea name="catatan" class="form-control" id="catatan">{{ $postm->catatan ?? '' }}</textarea>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-6 col-md-6 pr-1">
                    <label class="form-label">Persentase Total Tembolok</label>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" value="{{ $postm->tembolok_kondisi ?? '' }}" name="kondisi"
                                class="form-control text-center" id="kondisi" placeholder="Presentase">
                        </div>
                    </div>
                </div>
                {{-- <div class="col-6 col-md-3 pl-1 px-md-1">
                    <label class="form-label">Berat Tembolok Per Ekor</label>
                    <div class="form-group">
                        <div class="input-group ">
                            <input type="number" value="{{ $postm->tembolok_jumlah ?? '' }}" name="jumlah"
                                class="form-control text-center" id="jumlah" placeholder="Berat">
                        </div>
                    </div>
                </div> --}}
                <div class="col-6 col-md-6 px-md-1">
                    <label class="form-label">Berat Total Tembolok</label>
                    <div class="form-group">
                        <div class="input-group ">
                            <input type="number" value="{{ $postm->postmortem_prod->qc_tembolok ?? 0 }}"
                                name="totaltembolok" class="form-control text-center" id="totaltembolok"
                                placeholder="Total" step="0.01">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <b>Jeroan</b>
                <div class="row">
                    <div class="col-6 col-md-3 pr-1 pr-md-1">
                        <div class="form-group">
                            Hati
                            {{-- <div class="input-group">
                                <input type="text" value="{{ $postm->jeroan_hati ?? '' }}" name="hati"
                                    class="form-control text-center" id="hati" placeholder="Pieces">
                            </div> --}}

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="normal" name="hati[]"
                                        value="normal" {{ $hati['normal'] }}>
                                    <label class="custom-control-label" for="normal">Normal</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="peradangan" name="hati[]"
                                        value="peradangan" {{ $hati['peradangan'] }}>
                                    <label class="custom-control-label" for="peradangan">Peradangan</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="perkejuan" name="hati[]"
                                        value="perkejuan" {{ $hati['perkejuan'] }}>
                                    <label class="custom-control-label" for="perkejuan">Perkejuan</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="bercak" name="hati[]"
                                        value="bercak" {{ $hati['bercak'] }}>
                                    <label class="custom-control-label" for="bercak">Bercak Putih</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="kekuningan" name="hati[]"
                                        value="kekuningan" {{ $hati['kekuningan'] }}>
                                    <label class="custom-control-label" for="kekuningan">Kekuningan</label>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 pl-1 px-md-1">
                        <div class="form-group">
                            Jantung
                            {{-- <div class="input-group ">
                                <input type="text" value="{{ $postm->jeroan_jantung ?? '' }}" name="jantung"
                                    class="form-control text-center" id="jantung" placeholder="Pieces">
                            </div> --}}
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="jantungnormal"
                                        name="jantung[]" value="normal" {{ $jantung['normal'] }}>
                                    <label class="custom-control-label" for="jantungnormal">Normal</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="pembengkakan"
                                        name="jantung[]" value="pembengkakan" {{ $jantung['pembengkakan'] }}>
                                    <label class="custom-control-label" for="pembengkakan">Pembengkakan</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="penebalan"
                                        name="jantung[]" value="penebalan" {{ $jantung['penebalan'] }}>
                                    <label class="custom-control-label" for="penebalan">Penebalan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 pr-1 px-md-1">
                        <div class="form-group">
                            Ampela
                            {{-- <div class="input-group ">
                                <input type="text" value="{{ $postm->jeroan_ampela ?? '' }}" name="ampela"
                                    class="form-control text-center" id="ampela" placeholder="Pieces">
                            </div> --}}
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ampelanormal"
                                        name="ampela[]" value="normal" {{ $ampela['normal'] }}>
                                    <label class="custom-control-label" for="ampelanormal">Normal</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ampelaperadangan"
                                        name="ampela[]" value="peradangan" {{ $ampela['peradangan'] }}>
                                    <label class="custom-control-label" for="ampelaperadangan">Peradangan</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ampelaperkejuan"
                                        name="ampela[]" value="perkejuan"  {{ $ampela['perkejuan'] }}>
                                    <label class="custom-control-label" for="ampelaperkejuan">Perkejuan</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ampelabercak"
                                        name="ampela[]" value="bercak"  {{ $ampela['bercak'] }}>
                                    <label class="custom-control-label" for="ampelabercak">Bercak Putih</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ampelakekuningan"
                                        name="ampela[]" value="kekuningan"  {{ $ampela['kekuningan'] }}>
                                    <label class="custom-control-label" for="ampelakekuningan">Kekuningan</label>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 pl-1 pl-md-1">
                        <div class="form-group">
                            Usus
                            {{-- <div class="input-group ">
                                <input type="text" value="{{ $postm->jeroan_usus ?? '' }}" name="usus"
                                    class="form-control text-center" id="usus" placeholder="Pieces">
                            </div> --}}
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ususnormal"
                                        name="usus[]" value="normal" {{ $usus['normal'] }}>
                                    <label class="custom-control-label" for="ususnormal">Normal</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="ususperadangan"
                                        name="usus[]" value="peradangan" {{ $usus['peradangan'] }}>
                                    <label class="custom-control-label" for="ususperadangan">Peradangan</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="pendarahan"
                                        name="usus[]" value="pendarahan" {{ $usus['pendarahan'] }}>
                                    <label class="custom-control-label" for="pendarahan">Pendarahan</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="cacing" name="usus[]"
                                        value="cacing" {{ $usus['cacing'] }}>
                                    <label class="custom-control-label" for="cacing">Cacing</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <b>Memar</b>
                <div class="row">
                    <div class="col-4 col-md pr-1 mb-2">
                        <div class="form-group">
                            Dada
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->memar_dada ?? '' }}" name="memar_dada"
                                    class="form-control text-center" id="memar_dada" placeholder="Ekor">
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md px-1 mb-2">
                        <div class="form-group">
                            Paha
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->memar_paha ?? '' }}" name="memar_paha"
                                    class="form-control text-center" id="memar_paha" placeholder="Ekor">
                            </div>
                        </div>

                    </div>
                    <div class="col-4 col-md pl-1 mb-2">
                        <div class="form-group">
                            Sayap
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->memar_sayap ?? '' }}" name="memar_sayap"
                                    class="form-control text-center" id="memar_sayap" placeholder="Ekor">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <b>Patah</b>
                <div class="row">
                    <div class="col-md-4 col-6 pr-1">
                        <div class="form-group">
                            Sayap
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->patah_sayap ?? '' }}" name="patah_sayap"
                                    class="form-control text-center" id="patah_sayap" placeholder="Ekor">
                            </div>
                        </div>

                    </div>
                    <div class="col-md-4 col-6 px-md-1 pl-1">
                        <div class="form-group">
                            Kaki
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->patah_kaki ?? '' }}" name="patah_kaki"
                                    class="form-control text-center" id="patah_kaki" placeholder="Ekor">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <b>Keropeng</b>
                <div class="row">
                    <div class="col-6 col-lg pr-1 pr-md-1 mb-2">
                        <div class="form-group">
                            Kaki
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->keropeng_kaki ?? '' }}" name="keropeng_kaki"
                                    class="form-control text-center" id="keropeng_kaki" placeholder="Ekor">
                            </div>
                        </div>

                    </div>
                    <div class="col-6 col-lg px-lg-1 pl-1 mb-2">
                        <div class="form-group">
                            Sayap
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->keropeng_sayap ?? '' }}" name="keropeng_sayap"
                                    class="form-control text-center" id="keropeng_sayap" placeholder="Ekor">
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg px-lg-1 pr-1 mb-2">
                        <div class="form-group">
                            Dada
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->keropeng_dada ?? '' }}" name="keropeng_dada"
                                    class="form-control text-center" id="keropeng_dada" placeholder="Ekor">
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg px-lg-1 pl-1 mb-2">
                        <div class="form-group">
                            Punggung
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->keropeng_pg ?? '' }}" name="keropeng_pg"
                                    class="form-control text-center" id="keropeng_pg" placeholder="Ekor">
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg px-lg-1 pr-1 mb-2">
                        <div class="form-group">
                            Dengkul
                            <div class="input-group ">
                                <input type="text" value="{{ $postm->keropeng_dengkul ?? '' }}"
                                    name="keropeng_dengkul" class="form-control text-center" id="keropeng_dengkul"
                                    placeholder="Ekor">
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg pl-1 mb-2">
                        <div class="form-group">
                            Dengkul Hijau
                            <div class="input-group">
                                <input type="text" value="{{ $postm->kehijauan }}" name="dengkul"
                                    class="form-control text-center" id="dengkul" placeholder="Ekor">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span class=" foot-notes">* Tidak ada berarti 0</span>

        </div>
    </div>
    <div class="form-group mt-2 text-right">
        <button type="submit" class="btn btn-primary">UPDATE DATA</button>
    </div>
</form>

<div class="card-body">

    @foreach ($lainnya as $lain)
        @foreach ($lain->purcprod as $prod)
            @if ($prod->sc_status == 1)
                <div class="radio-toolbar">
                    <div class="form-group">
                        <input type="radio" id="lain{{ $lain->id }}" value="{{ $lain->id }}" name="purchase">
                        <label for="lain{{ $lain->id }}">
                            {{ $lain->nama_po }}
                            <span class="pull-right">
                                Qty : <span class="label label-rounded-grey">{{ $lain->jumlah_ayam ?? '0' }}
                                </span>
                                &nbsp
                                Berat : <span class="label label-rounded-grey">{{ $lain->berat_ayam ?? '0' }}
                                    kg
                                </span> &nbsp
                                @if ($lain->status == 1 and $lain->jenis_po != 'ayamfrozen')
                                    <button type="button" data-kode="{{ $lain->id }}"
                                        class="btn btn-sm btn-primary laintochiller">
                                        Chiller</button>
                                    <button type="button" data-kode="{{ $lain->id }}"
                                        class="btn btn-sm btn-warning laintograding">
                                        Grading</button>
                                @elseif ($lain->status == 1 and $lain->jenis_po == 'ayamfrozen')
                                    <button type="button" data-kode="{{ $lain->id }}"
                                        class="btn btn-sm btn-info laintogudang">
                                        Warehouse</button>
                                @else
                                    <button type="button" data-kode="{{ $lain->id }}"
                                        class="btn btn-sm btn-success " disabled>
                                        Selesai</button>
                                @endif
                            </span>
                        </label>
                    </div>
                </div>
            @endif
        @endforeach
    @endforeach
</div>

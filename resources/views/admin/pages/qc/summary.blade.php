<div class="row mb-2">
    <div class="col pr-1">
        <div class="border rounded p-2">
            Rataan Timbang
            <div>{{ number_format($count['ratatata'], 2) }} kg</div>
        </div>
    </div>

    <div class="col pl-1">
        <div class="border rounded p-2">
            Jumlah Sampel
            <div>{{ $count['total'] }} ekor</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6 pr-1">
        <div class="card">
            {{-- <div class="card-header">Informasi DO</div> --}}
            <div class="card-body">
                <div class="border-bottom">
                    <span class="float-right pl-1">{{ $count['kurang'] }} ekor</span>
                    < 03 </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['noltiga'] }} ekor</span>
                            03 - 04
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['nolempat'] }} ekor</span>
                            04 - 05
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['nollima'] }} ekor</span>
                            05 - 06
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['nolenam'] }} ekor</span>
                            06 - 07
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['noltujuh'] }} ekor</span>
                            07 - 08
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['noldelapan'] }} ekor</span>
                            08 - 09
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['nolsembilan'] }} ekor</span>
                            09 - 10
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['satu'] }} ekor</span>
                            10 - 11
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['satusatu'] }} ekor</span>
                            11 - 12
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['satudua'] }} ekor</span>
                            12 - 13
                        </div>
                        <div class="border-bottom">
                            <span class="float-right pl-1">{{ $count['satutiga'] }} ekor</span>
                            13 - 14
                        </div>
                </div>
            </div>
        </div>
        <div class="col-6 pl-1">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['satuempat'] }} ekor</span>
                        14 - 15
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['satulima'] }} ekor</span>
                        15 - 16
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['satuenam'] }} ekor</span>
                        16 - 17
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['satutujuh'] }} ekor</span>
                        17 - 18
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['satudelapan'] }} ekor</span>
                        18 - 19
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['satusembilan'] }} ekor</span>
                        19 - 20
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['dua'] }} ekor</span>
                        20 - 21
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['duasatu'] }} ekor</span>
                        21 - 22
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['duadua'] }} ekor</span>
                        22 - 23
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['duatiga'] }} ekor</span>
                        23 - 24
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['duaempat'] }} ekor</span>
                        24 - 25
                    </div>
                    <div class="border-bottom">
                        <span class="float-right pl-1">{{ $count['dualima'] }} ekor</span>
                        25 Up
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 px-3">
            <h4>Data Yang Masuk Kategori Uniform</h4>
            <div class="form-group mb-3">
                <div class="row">
                    <div class="col">
                        <label>Berat Terendah</label>
                        <input class="form-control" type="text" name="under" id="under" value="{{ $detailsData['terendah'] }}" readonly>
                    </div>
                    <div class="col">
                        <label>Berat Tertinggi</label>
                        <input class="form-control" type="text" name="over" id="over" value="{{ $detailsData['tertinggi'] }}" readonly>
                    </div>
                    <div class="col">
                        <label>Berat Rata Rata</label>
                        <input class="form-control" type="text" name="uniform" id="uniform" value="{{ $detailsData['ratarata'] }}" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

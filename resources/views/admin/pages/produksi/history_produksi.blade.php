<h5>Produksi Selesai</h5>

@foreach($history as $no => $row)

    <div class="card" style="margin-bottom: 10px">
        <div class="card-header padding-5">
            Produksi {{$no+1}}
        </div>
        <div class="card-body padding-10">
            <div class="row">
                <div class="col-sm-6 pr-sm-1">
                    <div class="card-header padding-5">
                        Bahan Baku
                    </div>
                @foreach($row->listfreestock as $raw)
                <div class="border-bottom">
                    <div class="row">
                        <div class="col pr-1">{{ $raw->chiller->item_name }}</div>
                        <div class="col-auto pl-1">({{ $raw->qty }})</div>
                    </div>
                </div>
                @endforeach
                </div>
                <div class="col-sm-6 pr-sm-1">
                    <div class="card-header padding-5">
                        Hasil Produksi
                    </div>
                    @foreach($row->freetemp as $f)
                    @php
                        $exp    =   json_decode($f->label) ;
                    @endphp
                    <div class="border-bottom">
                        <div class="row">
                            <div class="col">{{ $f->item->nama }}</div>
                            <div class="col">
                                {{ $exp->plastik->jenis }} ({{ $exp->plastik->qty }})<br>
                                @if ($exp->parting->qty) Parting : {{ $exp->parting->qty }} <br> @endif
                                @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Lemak, ' : '' }} {{ $exp->additional->maras ? 'Maras' : '' }} @endif
                            </div>
                            <div class="col-2">{{ $f->qty }}</div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

@endforeach

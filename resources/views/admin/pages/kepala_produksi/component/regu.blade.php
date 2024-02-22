
<input type="hidden" id="selected_category" value="{{$kategori}}">
@foreach($freestock as $no => $row)

    <div class="card mb-2">
        <div class="p-2">
            <div class="float-right">
                @if ($row->status == 2)
                <button type="button" class="btn btn-sm btn-success approved" data-id="{{ $row->id }}">Selesaikan</button>
                @if ($progress < 1)
                || <button class="btn btn-sm btn-info edit_regu" data-id="{{ $row->id }}">Edit</button>
                @endif
                @endif
            </div>
            Produksi {{date('d/m/Y H:i:s', strtotime($row->created_at))}}
        </div>
        <div class="card-body p-2">
            <div class="row">
                <div class="col-sm-6 pr-sm-1">
                <table class="table default-table table-small">
                    <thead>
                        <th>Bahan Baku</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                    </thead>
                    <tbody>
                        @php
                            $item = 0;
                            $berat = 0;
                        @endphp
                        @foreach ($row->listfreestock as $rfs)
                            @php
                                $item += $rfs->qty;
                                $berat += $rfs->berat;
                            @endphp
                        <tr>
                            <td>{{ $rfs->chiller->item_name }}
                                @if($rfs->chiller->asal_tujuan=="retur")
                                    <br><span class="status status-info">{{$row->label}}</span>
                                @endif
                            </td>
                            <td>{{ number_format($rfs->qty) }}</td>
                            <td class="text-right">{{ number_format($rfs->berat, 2) }} Kg</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th> {{ number_format($item) }}</th>
                            <th class="text-right">{{ number_format($berat, 2) }} Kg</th>
                        </tr>
                    </tfoot>
                </table>

                </div>
                <div class="col-sm-6 pl-sm-1">
                    <table class="table default-table table-small">
                        <thead>
                            <th>Hasil Produksi</th>
                            <th>Ekor/Pcs/Pack</th>
                            <th>Berat</th>
                        </thead>
                        <tbody>
                            @php
                                $qty = 0;
                                $berat = 0;
                            @endphp
                            @foreach ($row->freetemp as $item)
                                @php
                                    $qty += $item->qty;
                                    $berat += $item->berat;
                                    $exp    =   json_decode($item->label) ;
                                @endphp
                            <tr>
                                <td> {{ $item->item->nama ?? ''}}</td>
                                <td>{{ number_format($item->qty) }}</td>
                                <td class="text-right">{{ number_format($item->berat, 2) }} Kg</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                     <div class="status status-success">
                                        <div class="row">
                                            <div class="col pr-1">
                                                {{ $item->plastik_nama }}
                                            </div>
                                            <div class="col-auto pl-1">
                                                <span class="float-right">// {{ $item->plastik_qty }} Pcs</span>
                                            </div>
                                        </div>
                                    </div>
                            

                                    @if ($exp->additional) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                                    <div class="row mt-1 text-info">
                                        <div class="col pr-1">
                                            @if ($exp->sub_item ?? "") <div>Customer : {{ $exp->sub_item }}</div> @endif
                                        </div>
                                        <div class="col-auto pl-1 text-right">
                                            @if ($exp->parting->qty) Parting : {{ $exp->parting->qty }} @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th> {{ $qty }} Ekor</th>
                                <th class="text-right">{{ $berat }} Kg</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>


    {{-- <script>

        var tanggal = "";
        var kategori = "";

        $('#tglhasil').on('change', function(){
            tanggal = $(this).val();
            kategori = $('#selected_category').val();
            $("#hasil_harian").load("{{ route('regu.index', ['key' => 'hasil_harian']) }}&kat="+kategori+"&tanggal="+tanggal);
            console.log("{{ route('regu.index', ['key' => 'hasil_harian']) }}&kat="+kategori+"&tanggal="+tanggal);
        })

    </script> --}}

@endforeach

<table class="table default-table table-small">
    <thead>
        <th>Hasil Produksi</th>
        <th>Ekor/Pcs/Pack</th>
        <th>Berat</th>
        <th></th>
    </thead>
    <tbody>
        @php
            $qty    = 0;
            $berat  = 0;
        @endphp
        @foreach ($data as $no => $item)
            @php
                $qty += $item->qty;
                $berat += $item->berat;
                $exp = json_decode($item->label);
            @endphp
            <tr>
                <td>
                    <a href="{{ route('chiller.show', $item->freetempchiller->id ?? '') }}"
                        target="_blank">{{ ++$no }}.</a>
                    {{ $item->item->nama ?? '' }}
                    @if ($item->kategori == '1')
                        <span class="status status-danger">[ABF]</span>
                    @elseif($item->kategori == '2')
                        <span class="status status-warning">[EKSPEDISI]</span>
                    @elseif($item->kategori == '3')
                        <span class="status status-warning">[TITIP CS]</span>
                    @else
                        <span class="status status-info">[CHILLER]</span>
                    @endif
                    <!-- (<span class="text-primary text-bold text-2xl"> {{ $item->freetempchiller->id ?? '' }} </span> ) -->
                </td>
                <td>{{ number_format($item->qty) }}</td>
                <td class="text-right">{{ number_format($item->berat, 2) }} Kg</td>
                <td class="text-right">
                   
                    <!-- <i class="fa fa-trash text-danger hapus_fg px-1" onclick="hapus_fg('{{ $item->id }}')" data-id="{{ $item->id }}" data-nama="{{ $item->item->nama }}" ></i> -->
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div class="row">
                        <div class="col pr-1">
                            @if ($item->kode_produksi)
                                Kode Produksi : {{ $item->kode_produksi }}
                            @endif
                        </div>
                        <div class="col pl-1 text-right">
                            @if ($item->unit)
                                Unit : {{ $item->unit }}
                            @endif
                        </div>
                    </div>
                    @if ($item->keranjang)
                        <div>{{ $item->keranjang }} Keranjang</div>
                    @endif
                    @if ($exp->plastik->jenis)
                        <div class="status status-success">
                            <div class="row">
                                <div class="col pr-1">
                                    {{ $exp->plastik->jenis }}
                                </div>
                                <div class="col-auto pl-1">
                                    @if ($exp->plastik->qty > 0)
                                        <span class="float-right">// {{ $exp->plastik->qty }} Pcs</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($exp->additional)
                        {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }}
                        {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }}
                        {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                    @endif
                    <div class="row mt-1 text-info">
                        <div class="col pr-1">
                            @if ($item->customer_id)
                                <div>Customer : {{ $item->konsumen->nama ?? '-' }}</div>
                            @endif
                            @if ($exp->sub_item)
                                <div>Keterangan : {{ $exp->sub_item }}</div>
                            @endif
                        </div>
                        <div class="col-auto pl-1 text-right">
                            @if ($item->selonjor)
                                <div class="text-danger font-weight-bold">SELONJOR</div>
                            @endif
                            @if ($exp->parting->qty)
                                Parting : {{ $exp->parting->qty }}
                            @endif
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
            @if (Auth::user()->account_role == 'superadmin')
                <td></td>
            @endif
        </tr>
    </tfoot>
</table>
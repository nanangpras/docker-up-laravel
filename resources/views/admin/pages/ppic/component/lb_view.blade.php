<div class="table-responsive mt-3">
    @foreach ($purchase as $i => $val)
        <table width="100%" id="kategori" class="table default-table">
            <thead>
                <tr>
                    <th colspan="6"> {{ $val->no_po }} </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6">Item : <br>
                        <table class="table default-table">
                            @foreach ($val->purchasing_item as $no => $itm)
                                @php
                                    $item = \App\Models\Item::item_sku($itm->item_po);
                                @endphp
                                <tr>
                                    <td class="list-record">{{ $no + 1 }}</td>
                                    <td class="list-record">{{ $item->nama }}</td>
                                    <td class="list-record">Rp {{ number_format($itm->harga) }}</td>
                                    <td class="list-record">{{ number_format(($itm->berat_ayam ?? '0'), 2) }} kg</td>
                                    <td class="list-record">{{ number_format($itm->jumlah_ayam ?? '0') }} Pcs/Ekr</td>
                                    <td class="list-record">{{ $itm->jenis_ayam ?? '###' }}</td>
                                    <td class="list-record">{{ $item->sku }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                @foreach ($val->purcprod as $no => $prod)
                    <tr>
                        <td>DO {{ ++$no }} / Mobil {{ $prod->no_urut }}</td>
                        <td align="right">{!!$prod->ppic_status!!}</td>
                        <td>{{ $prod->sc_nama_kandang }}</td>
                        <td>{{ $prod->prod_tanggal_potong ?? "" }}</td>
                        <td>{{ $prod->prodpur->jenis_po }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    @endforeach
</div>

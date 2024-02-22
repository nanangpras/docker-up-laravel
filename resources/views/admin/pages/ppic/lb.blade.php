<div class="table-responsive">
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
                                    <td class="list-record">{{ $itm->berat_ayam ?? '###' }} kg</td>
                                    <td class="list-record">{{ $itm->jumlah_ayam ?? '###' }} Pcs/Ekr</td>
                                    <td class="list-record">{{ $itm->jenis_ayam ?? '###' }}</td>
                                    <td class="list-record">{{ $item->sku }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>

                @foreach ($val->purcprod as $no => $prod)
                    <tr>
                        <td>DO {{ ++$no }} / Mobil {{ $no }}</td>
                        <td align="right">{{ $prod->ppic_status }}</td>
                        <td>{{ $prod->sc_nama_kandang }}</td>
                        <td></td>
                        <td>{{ $prod->prodpur->jenis_po }}</td>
                        {{-- <td align="right">
                            @if ($prod->ppic_acc == 1 or $prod->ppic_acc == null)
                                <button type="submit" class="btn btn-primary terimanonlb" data-id="{{ $prod->id }}"
                                    data-tujuan="evis">Evis</button>
                                <button type="submit" class="btn btn-danger terimanonlb" data-id="{{ $prod->id }} "
                                    data-tujuan="grading">Grading</button>
                                <button type="submit" class="btn btn-info terimanonlb" data-id="{{ $prod->id }}"
                                    data-tujuan="abf">ABF</button>
                                <button type="submit" class="btn btn-warning terimanonlb"
                                    data-id="{{ $prod->id }} " data-tujuan="chiller">Chiller FG</button>
                            @else
                                <button type="submit" class="btn btn-danger batalnonlb"
                                    data-id="{{ $prod->id }}">Batal</button>
                            @endif
                        </td> --}}
                    </tr>
                @endforeach

            </tbody>
        </table>
    @endforeach
</div>

<script>
    $('.terimalb').click(function() {
        var id = $(this).data('id');
        var tujuan = $(this).data('tujuan');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('ppic.ppic_acc') }}",
            method: "POST",
            data: {
                id: id,
                tujuan: tujuan,
                tanggal: "{{ $tanggal }}"
            },
            success: function(data) {
                showNotif('Berhasil');
                $("#lb").load("{{ route('ppic.lb') }}");
            }
        });
    })

    $('.batalnonlb').click(function() {
        var id = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('ppic.ppic_batal') }}",
            method: "POST",
            data: {
                id: id,
            },
            success: function(data) {
                showNotif('Berhasil');
                $("#nonlb").load("{{ route('ppic.nonlb') }}");
            }
        });
    })
</script>

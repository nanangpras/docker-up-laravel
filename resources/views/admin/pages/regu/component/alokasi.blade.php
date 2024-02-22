<div class="form-group">
    <label for="">Filter</label>
    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
        name="tanggalalokasi" class="form-control" id="tanggalalokasi" placeholder="Tuliskan" value="{{ $tanggal }}"
        autocomplete="off">
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table default-table" width="100%">
                <thead>
                    <tr>
                        <th class="text-info" colspan="4">Hasil Produksi</th>
                        <th class="text-info" colspan="4">Alokasi Order</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Nama Item</th>
                        <th>Ekor/Pcs/Pack</th>
                        <th>Berat</th>
                        <th class="text-info" colspan="4"></th>

                    </tr>
                </thead>
                <tbody>
                    @php
                    $ekor = 0;
                    $berat = 0;
                    @endphp
                    @foreach ($bahanbaku as $i => $row)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $row->item_name }}</td>
                        <td>{{ number_format($row->qty_item) }}</td>
                        <td>{{ number_format($row->berat_item, 2) }} Kg</td>
                        <td>
                            @php
                            $no_alokasi = 0;
                            @endphp
                            @foreach ($row->alokasi_order as $j => $val)
                            <div class="border px-1">
                                {{ ++$j }}. {{ $val->nama }} ({{ $val->bb_item }}pcs || {{ $val->bb_berat }}kg)
                            </div>
                            @php
                            $no_alokasi++;
                            @endphp
                            @endforeach

                            @if($no_alokasi<1) <span class="status status-danger">Belum dialokasikan</span>
                                @endif
                        </td>
                    </tr>
                    @php
                    $ekor += $row->qty_item;
                    $berat += $row->berat_item;
                    @endphp
                    @endforeach
                    <tr>
                        <td></td>
                        <td>Total</td>
                        <td>{{ $ekor }}</td>
                        <td>{{ number_format($berat, 2) }} Kg</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $('#tanggalalokasi').on('change', function() {
        var tanggal = $(this).val();
        console.log("{{ route('produksi.alokasi', ['regu' => $regu]) }}&tanggal=" + tanggal);
        $("#list_alokasi").load("{{ route('produksi.alokasi', ['regu' => $regu]) }}&tanggal=" + tanggal);
    })
</script>
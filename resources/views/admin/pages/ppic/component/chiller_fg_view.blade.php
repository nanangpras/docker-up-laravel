<table class="table default-table" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Ekor/Pcs/Pack</th>
            <th>Berat (Kg)</th>
            <th>Tanggal Bahan Baku</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($chiller_fg as $i => $chill)
            @php 
                $sisaQty        = $chill->sisaQty;
                $sisaBerat      = number_format((float)$chill->sisaBerat, 2, '.', '');  
                $sisaKeranjang  = $chill->keranjang - $chill->total_keranjang ?? '#';
            @endphp
            <tr>
                <td>{{ ++$i }}</td>
                <td>
                    <div class="float-right text-secondary small">CH-{{ $chill->id }}</div>
                    {{ $chill->item_name }}

                    @if($chill->kategori=="1")
                    <span class="status status-danger">[ABF]</span>
                    @elseif($chill->kategori=="2")
                    <span class="status status-warning">[EKSPEDISI]</span>
                    @elseif($chill->kategori=="3")
                    <span class="status status-warning">[TITIP CS]</span>
                    @else
                    <span class="status status-info">[CHILLER]</span>
                    @endif

                    @if ($chill->selonjor)
                    <br><span class="text-danger font-weight-bold">SELONJOR</span>
                    @endif
                    @php
                        $exp = json_decode($chill->label);
                    @endphp

                    @if ($chill->customer_id)
                    <br><span class="text-info">Customer : {{ $chill->konsumen->nama }}</span>
                    @endif

                     <div class="status status-success">
                        <div class="row">
                            <div class="col pr-1">
                                {{ $chill->plastik_nama }}
                            </div>
                            <div class="col-auto pl-1">
                                <span class="float-right">// {{ $chill->plastik_qty }} Pcs</span>
                            </div>
                        </div>
                    </div>
            
                    
                    @if ($exp)<br>
                        @if (isset($exp->additional)) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                        <div class="row mt-1 text-info">
                            <div class="col pr-1">@if ($exp->sub_item ?? '') Keterangan : {{ $exp->sub_item }} @endif</div>
                            <div class="col-auto pl-1">@if ($exp->parting->qty ?? '') Parting : {{ $exp->parting->qty }} @endif</div>
                        </div>
                    @endif
                </td>
                <td>{{ $sisaQty }} ekor</td>
                <td class="text-right">{{ $sisaBerat }}
                </td>
                <td>{{ $chill->tanggal_produksi }}</td>
                <td>
                    @if($sisaBerat <= 0)
                        <br><span class="status status-danger">Dipindahkan</span>
                    @endif
                </td>
                <td>
                    @if ($chill->status == 2)
                        @if($sisaBerat > 0)
                        <div style="width:160px!important">
                            <div class="row">
                                <div class="col-6 pr-1">
                                    <input type="number" id="kirim_jumlah{{ $chill->id }}" style="width:75px" placeholder="QTY">
                                </div>
                                <div class="col-6 pl-1">
                                    <input type="number" id="kirim_berat{{ $chill->id }}" step="0.01" style="width:75px" placeholder="Berat">
                                </div>
                            </div>
                            <div><button type="submit" class="btn btn-primary btn-block toabf_fg mt-1" data-chiller="{{ $chill->id }}">Kirim ke ABF</button></div>
                        </div>
                        @endif
                    @else
                        <button class="btn btn-success" disabled>Selesai</button>
                    @endif

                    <a href="{{ route('chiller.show', $chill->id) }}" class="btn btn-info mt-2">Detail</a>

                </td>
            </tr>
        @endforeach

    </tbody>
</table>

<div id="paginate_chiller_fg">
    {{ $chiller_fg->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
$('#paginate_chiller_fg .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#chiller_fg').html(response);
        }

    });
});
</script>


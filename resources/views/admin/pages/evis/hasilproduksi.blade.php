@php
    $qty    =   0;
    $berat  =   0;
@endphp
@foreach ($bahanbaku as $row)
    @if (COUNT($row->freetemp))
    <div class="font-weight-bold">Hasil Produksi</div>
    <table class="table default-table">
        <thead>
            <tr>
                <th>Finished Good</th>
                <th>Qty</th>
                <th>Berat</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($row->freetemp as $f)
                @php
                    $qty    +=  $f->qty;
                    $berat  +=  $f->berat;
                    $exp    =   json_decode($f->label);
                @endphp
                <tr>
                    <td><span class="status status-info">[{{ $f->kategori ? 'ABF' : 'CHILLER' }}]</span> {{ $f->item->nama ?? '' }}</td>
                    <td>{{ $f->qty ?? '0' }}</td>
                    <td>{{ $f->berat ?? '0' }}</td>
                    
                    <td class="text-center">
                        <span class="text-danger deleteproduksi" data-id="{{ $f->id }}">
                            <i class="fa fa-trash"></i>
                        </span>
                    </td>
                </tr>
                @if ($exp)
                <tr>
                    @if($f->plastik_nama)
                    <td style="padding: 0" colspan="4"><div class="rounded-0 status status-success">{{ $f->plastik_nama }} <span class="float-right">// {{ $f->plastik_qty }} Pieces</span></div>
                    </td>
                    @endif
                </tr>
                @endif
                @if($f->keterangan)
                <tr>
                    <td colspan="4">
                        <div class="rounded-0 status status-warning mt-1">KETERANGAN: {{ $f->keterangan }}
                        </div>
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th>{{ number_format($berat, 2) }}</th>
                <th>{{ number_format($qty) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    @endif
@endforeach

<script>
    $('.deleteproduksi').click(function() {
        var x_code = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('evis.put') }}",
            method: "POST",
            data: {
                x_code: x_code
            },
            success: function(data) {
                showNotif('Berhasil Hapus Data');
                $("#hasilproduksi").load("{{ route('evis.hasilproduksi') }}");
            }
        })
    })
</script>

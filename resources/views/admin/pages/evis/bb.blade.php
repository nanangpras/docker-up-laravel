@php
    $item = 0;
    $berat = 0;
@endphp
@if ($bahanbaku)
    @if (COUNT($bahanbaku->listfreestock))
    <table class="table default-table">
        <thead>
            <tr>
                <th>Bahan Baku</th>
                <th>Qty</th>
                <th>Berat</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bahanbaku->listfreestock as $raw)
                @php
                    $item += $raw->qty;
                    $berat += $raw->berat;
                @endphp
                <tr>
                    <td>[{{ date('d/m/y', strtotime($raw->chiller->tanggal_potong)) }}] {{ $raw->chiller->item_name }} [ {{ $raw->id }} ]</td>
                     <td>{{ number_format($raw->qty ?? '0') }}</td>
                    <td>{{ number_format(($raw->berat ?? '0'), 2) }}</td>
                    <td class="text-center">
                        <span class="text-danger deletebb" data-id="{{ $raw->id }}">
                            <i class="fa fa-trash"></i>
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th>{{ number_format($item) }}</th>
                <th>{{ number_format($berat, 2) }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    @endif
@endif

<script>
    $('.deletebb').click(function() {
        var x_code      =   $(this).data('id');
        var tanggal     =   $("#pencarian").val();
        var free_stock  =   "{{ $bahanbaku->id ?? '' }}" ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('evis.delete') }}",
            method: "POST",
            data: {
                x_code: x_code
            },
            success: function(data) {
                $("#freestock_id").val(data.freestock_id) ;
                $("#list_bahan_baku").load("{{ url('admin/evis/gabung/bahanbaku?tanggal=') }}" + tanggal + "&produksi=" + free_stock);
                $("#bbperuntukan").load("{{ route('evis.bbperuntukan') }}");
                showNotif('Berhasil Hapus Data');
            }
        })
    })
</script>

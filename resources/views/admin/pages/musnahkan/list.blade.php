@if ($data)
<section class="panel">
    <div class="card-body p-2">
        <table class="table default-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Berat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $qty    =   0 ;
                    $berat  =   0 ;
                @endphp
                @foreach ($data->list_data as $row)
                @php
                    $qty    +=  $row->qty ;
                    $berat  +=  $row->berat ;
                @endphp
                <tr>
                    <td>
                        @if($row->gudang_id == '2' or $row->gudang_id == '4' or $row->gudang_id == '23' or $row->gudang_id == '24')
                        {{ $row->chiller->tanggal_produksi }}
                        @else 
                        {{ $row->gudang->production_date }} 
                        @endif
                    </td>
                    <td>
                        <div class="small text-info">{{ $row->warehouse->code ?? "#" }}</div>
                        {{ $request->type == 'gudang' ? ($row->gudang->nama ?? $row->chiller->item_name) : $row->items->nama }}
                    </td>
                    <td class="text-right">{{ number_format($row->qty) }}</td>
                    <td class="text-right">{{ number_format($row->berat, 2) }}</td>
                    <td><i class="fa fa-trash text-danger hapus_temp" data-id="{{ $row->id }}"></i></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">TOTAL</th>
                    <th class="text-right">{{ number_format($qty) }}</th>
                    <th class="text-right">{{ number_format($berat, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</section>

<script>
    $('.hapus_temp').click(function() {
        var id              =   $(this).data('id') ;
        var tanggal_pindah  =   $('#tanggal-pindah').val() ;
        var cold            =   $(".cold:checked").val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.hapus_temp').hide() ;

        $.ajax({
            url: "{{ route('musnahkan.store') }}",
            method: "POST",
            data: {
                id      :   id,
                key     :   'hapus'
            },
            success: function(data) {
                showNotif('List berhasil dihapus');
                $("#list").load("{{ route('musnahkan.index', ['key' => 'list']) }}&type={{ $request->type }}");
                $("#show").load("{{ route('musnahkan.index', ['key' => 'view']) }}&id=" + cold + "&tanggal=" + tanggal_pindah);
            }
        });
    })
</script>
@endif

<table class="table default-table table-bordered" width="100%">
    <thead>
        <tr>
            <th class="text-center" rowspan="2">Tanggal Produksi</th>
            <th class="text-center" rowspan="2">Nama</th>
            <th class="text-center" rowspan="2">Gudang</th>
            <th class="text-center" colspan="2">Stock</th>
            <th class="text-center" colspan="2">Input</th>
            <th class="text-center" rowspan="2">Aksi</th>
        </tr>
        <tr>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Berat</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Berat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($gudang as $val)
            @php
                $sisaQty        = $val->sisaQty;
                $sisaBerat      = number_format((float)$val->sisaBerat, 2, '.', '');
            @endphp
            <tr>
                <td>{{ $val->tanggal_produksi ?? $val->production_date }}</td>
                <td>{{ $val->nama ?? $val->item_name }}</td>
                @if ($id == 2 || $id == 4 || $id == 23 || $id == 24 )
                    <td data-storagecode="{{ $val->type }}">{{ $val->type }}</td>
                @else
                    <td>
                        {{ $val->productgudang->code ?? $val->kode_gudang }} 
                    </td>    
                @endif
                <td class="text-center" style="vertical-align:middle;">
                    <div>{{ $sisaQty ?? 0 }}</div>
                    <div class="small text-danger">{{ App\Models\Musnahkantemp::hitung($val->id, 'qty') ? App\Models\Musnahkantemp::hitung($val->id, 'qty') : '' }}</div>
                </td>
                <td class="text-center" style="vertical-align:middle;">
                    <div>{{ $sisaBerat }}</div>
                    <div class="small text-danger">{{ App\Models\Musnahkantemp::hitung($val->id, 'berat') ? App\Models\Musnahkantemp::hitung($val->id, 'berat') : '' }}</div>
                </td>
                <td style="vertical-align:middle;" class="p-0">
                    {{-- <div class="col px-1"> --}}
                        <input type="number" id="qty{{ $val->id }}" placeholder="Qty" class="form-control form-control-sm px-1 px-0 py-1 rounded-0" style="border: none; font-size:12px; background-color: #fffcd1" max="{{ $sisaQty }}">
                    {{-- </div> --}}
                </td>
                <td style="vertical-align:middle;" class="p-0">
                    {{-- <div class="col px-1"> --}}
                        <input type="hidden" id="stockberat{{ $val->id }}" value="{{ $val->berat ?? $val->stock_berat }}">
                        <input type="number" id="berat{{ $val->id }}" placeholder="Berat" class="form-control form-control-sm px-1 px-0 py-1 rounded-0" style="border: none; font-size:12px; background-color: #fffcd1" max="{{ $sisaBerat }}" required>
                    {{-- </div> --}}
                </td>
                

                <td class="px-0 py-1">
                    <button type="submit" class="btn btn-outline-info btn-sm rounded-0 pindahan" data-id="{{ $val->id }}" data-storagecode="{{ $val->gudang_id ?? $id }}">Submit</button>
                </td>
            </tr>
            <tr>
                <td colspan="8" class="py-0">
                     <div class="status status-success">
                        <div class="row">
                            <div class="col pr-1">
                                {{ $val->plastik_nama }}
                            </div>
                            <div class="col-auto pl-1">
                                <span class="float-right">// {{ $val->plastik_qty }} Pcs</span>
                            </div>
                        </div>
                    </div>
            
                    
                    @if ($val->label)
                        @php
                            $exp = json_decode($val->label);
                        @endphp
                        @if($exp)
                            @if (!empty($exp->additional)) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }} @endif
                            <div class="row text-info">
                                <div class="col pr-1">@if ($exp->sub_item ?? "") Customer : {{ $exp->sub_item }} @endif</div>
                                <div class="col-auto pl-1 text-right">@if ($exp->parting->qty ?? "") Parting : {{ $exp->parting->qty }} @endif</div>
                            </div>
                        @endif
                    @else
                    <div class="text-success">
                        {{ $val->packaging }}
                    </div>
                    <div class="text-info">Sub Item : {{ $val->sub_item }}</div>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div id="paginate_summary">
    {{ $gudang->appends($_GET)->links() }}
</div>

<script>
    $('.pindahan').click(function(e) {
        e.preventDefault()
        // var cold            =   "{{ $id }}" ;
        var cold            =   $(this).data('storagecode') ;
        var id              =   $(this).data('id') ;
        var qty             =   $("#qty" + id).val() ;
        var berat           =   $("#berat" + id).val() ;
        var stockberat      =   $("#stockberat" + id).val() ;
        var tanggal_pindah  =   $('#tanggal-pindah').val() ;

        if(cold === undefined || cold === null || cold === ''){
            showAlert('Pilih Salah Satu Storage');
            return false;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.pindahan').hide() ;

        $.ajax({
            url: "{{ route('musnahkan.store') }}",
            method: "POST",
            data: {
                id      :   id,
                cold    :   cold,
                qty     :   qty,
                berat   :   berat,
                key     :   'temporary'
            },
            success: function(data) {
                console.log(id);
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    document.getElementById("qty"+id).value = "";
                    document.getElementById("berat"+id).value = "";
                    showNotif('Berhasil ditambahkan ke list');
                    $("#show").load("{{ route('musnahkan.index', ['key' => 'view']) }}&id=" + cold + "&tanggal=" + tanggal_pindah);
                    $("#list").load("{{ route('musnahkan.index', ['key' => 'list']) }}&type=gudang");
                }
                $('.pindahan').show() ;
            }
        });
    })

    $('#paginate_summary .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#show').html(response);
        }

    });
});
</script>

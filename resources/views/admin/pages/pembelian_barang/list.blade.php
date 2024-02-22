@if (COUNT($list))
@php
$unit_measure = [
    "Piece",
    "Roll",
    "Lembar",
    "Rim",
    "Unit",
    "Balok",
    "Pack",
    "Galon",
    "Sachet",
    "Tabung",
    "Kaleng",
    "Botol",
    "Box",
    "Buku",
    "Drg",
    "Dus",
    "Kotak",
    "Pasang",
    "Slop",
    "Tablet",
    "Tube",
    "Batang",
    "Lusin",
    "Set",
    "Sak",
    "Lot",
    "Zak",
    "Keranjang",
    "Ekor",
    "Meter",
    "Centimeter",
    "Liter",
    "Mililiter",
    "Kilogram",
    "Gram",
    "Ton",
    "Dump",
    "Rit",
    "Jam",
    "Menit",
    "Detik"
]
@endphp

<section class="panel">
    <div class="card-body p-2">
        <table class="table default-table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Item</th>
                    <th>Qty Awal</th>
                    <th>Qty Sisa</th>
                    <th>Ket.</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($list as $row)
                <tr>
                    <td>{{ $row->item->sku }}</td>
                    <td>{{ $row->item->nama }}</td>
                    <td>{{ $row->qty }} {{ $row->unit }}</td>
                    <td>{{ $row->sisa }} {{ $row->unit }}</td>
                    <td>{{ $row->keterangan }}</td>
                    <td class="text-center" style="width:70px">
                        <i class="fa fa-trash text-danger hapus_item mr-3" data-id="{{ $row->id }}"></i>
                        <i class="fa fa-edit text-primary" data-toggle="modal" data-target="#editPR" data-id="{{ $row->id }}" onclick="editPR($(this).data('id'))" data-backdrop="false"></i>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
<div class="modal fade" id="editPR" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form>
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="row mb-2">
                            <input type="hidden" id="idPR">
                            <div class="col-12">
                                Item
                                <select required name="item" id="itemPR" class="form-control select2" data-placeholder="Pilih Item" data-width="100%">
                                    <option value=""></option>
                                    @foreach ($item as $row)
                                    <option value="{{ $row->id }}">{{ $row->sku }}. {{ $row->nama }} ({{ $row->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 pr-1">
                                Qty
                                <input type="number" required name="qty" id="qtyPR" class="form-control px-2" placeholder="Qty" autocomplete="off">
                            </div>

                            {{-- <div class="col-6 px-1">
                                Unit
                                <select required name="unit" id="unitPR" class="form-control">
                                    @foreach($unit_measure as $u)
                                            <option value="{{$u}}">{{$u}}</option>
                                    @endforeach
                                </select>
                            </div> --}}

                        </div>

                        <div class="row mt-2">
                            <div class="col">
                                <div class="form-group">
                                    Keterangan
                                    <input type="text" name="keterangan" id="keteranganPR" placeholder="Tulis keterangan" class="form-control" autocomplete="off">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="updatePR">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('.select2').select2({
        theme: 'bootstrap4',
    })
</script>
<script>
    $('#updatePR').on('click', function(e){
    e.preventDefault()
    // console.log($(this));
    $.ajax({
        method: 'POST',
        url: "{{ route('pembelian.store') }}",
        data: {
            '_token': $('input[name=_token]').val(),
            key : 'updatePR',
            id  : $('#idPR').val(),
            item : $('#itemPR').val(),
            qty : $('#qtyPR').val(),
            url : $('#urlPR').val(),
            keterangan : $('#keteranganPR').val(),
            unit : $('#unitPR').val(),
        },
        dataType: 'json',
        success: res =>{
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            showNotif(res.msg);
            $("#data_list").load("{{ route('pembelian.index', ['key' => 'list']) }}&id_list=" + $("#id_submit").val());
        }
    })
})
</script>

<script>
    function editPR(id){
        $.ajax({
            url: "{{ route('pembelian.index', ['key' => 'editPR']) }}",
            data:{idlist:id},
            type: "GET",
            dataType: "JSON",
            success: function(data){
                $('#idPR').val(data.id);
                $('#itemPR').val(data.item_id).trigger('change');
                $('#qtyPR').val(data.qty);
                $('#urlPR').val(data.link_url);
                $('#keteranganPR').val(data.keterangan);
                $('#unitPR').val(data.unit).trigger('change');
            }
        });
    }
</script>

<script>
    $('.hapus_item').click(function() {
        var id  =   $(this).data('id') ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // $('.hapus_item').hide() ;

        $.ajax({
            url: "{{ route('pembelian.store') }}",
            method: "POST",
            data: {
                id  :   id,
                key :   'hapus_item'
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    $("#data_list").load("{{ route('pembelian.index', ['key' => 'list']) }}&id_list=" + $("#id_submit").val());
                }
                // $('.hapus_item').show() ;
            }
        });
    })
</script>
@endif

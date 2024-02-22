<div class="row">
    <div class="col-4">
        <div class="form-group">
            <label for="">Filter</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif name="tanggal" class="form-control" id="tanggal" placeholder="Tuliskan " value="{{ $tanggal }}"
                autocomplete="off">
            @error('tanggal') <div class="small text-danger">{{ message }}</div> @enderror
        </div>

    </div>
</div>
<div class="table-responsive">
    <table class="table default-table" width="100%" id="editproduksi">
        <thead>
            <tr>
                <th>No</th>
                <th>Item</th>
                <th>Produk</th>
                <th>Label</th>
                <th>Qty</th>
                <th>Berat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($data as $i => $val)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $val->item_name }}</td>
                <td>{{ $val->regu }}</td>
                <td>
                    @if($val->chillertofreestocktemp)
                    @php
                    $exp = json_decode($val->chillertofreestocktemp->label);
                    @endphp

                    <div class="status status-success">
                        <div class="row">
                            <div class="col pr-1">
                                {{ $item->plastik_nama }}
                            </div>
                            <div class="col-auto pl-1">
                                <span class="float-right">// {{ $item->plastik_qty }} Pcs</span>
                            </div>
                        </div>
                    </div>

                    @if(strlen($val->chillertofreestocktemp->label)>1)

                    @if ($exp->additional ?? false) {{ $exp->additional->tunggir ? 'Tanpa Tunggir, ' : '' }} {{ $exp->additional->lemak ? 'Tanpa Lemak, ' : '' }} {{ $exp->additional->maras ? 'Tanpa Maras' : '' }}
                    @endif
                    <div class="row pt-1 text-info">
                        <div class="col pr-1">@if ($exp->sub_item ?? false)Customer : {{ $exp->sub_item }} @endif</div>
                        <div class="col pl-1 text-right">@if ($exp->parting->qty ?? false) Parting : {{ $exp->parting->qty }} @endif</div>
                    </div>
                    @endif
                    @endif

                </td>
                <td>{{ $val->stock_item }}</td>
                <td>{{ $val->stock_berat }}</td>
                <td>
                    <button type="submit" class="btn btn-primary btn-sm btn-edit-prod" data-toggle="modal"
                        data-id="{{$val->id}}" data-itemid="{{$val->item_id}}" data-qty="{{$val->stock_item}}"
                        data-berat="{{$val->stock_berat}}" data-target="#ediproduksi">Edit</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal" id="ediproduksi" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('hasilproduksi.store') }}" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Stock</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @csrf
                    <input type="hidden" id="edit-id" name="id" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="selected_item">Item </label>
                            <select name="item" class="form-control select2" id="selected_item">
                                @foreach ($item as $key)
                                <option value="{{ $key->id }}">{{ $key->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col pr-1">
                                <div class="form-group">
                                    <label for="">Qty</label>
                                    <input type="text" name="qty" class="form-control" id="edit-qty"
                                        placeholder="Tuliskan Qty" value="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    <label for="">Berat</label>
                                    <input type="text" name="berat" class="form-control" id="edit-berat"
                                        placeholder="Tuliskan Berat" value="" autocomplete="off">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary ">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


</div>
@section('header')
<link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>
    $('.select2').select2({
            theme: 'bootstrap4',
            dropdownParent: "#ediproduksi"
        });

        $('.btn-edit-prod').on('click', function(){
            var id = $(this).attr('data-id');
            var itemid = $(this).attr('data-itemid');
            var qty = $(this).attr('data-qty');
            var berat = $(this).attr('data-berat');

            $('#item').val(itemid).change();
            $('#edit-id').val(id);
            $('#edit-qty').val(qty);
            $('#edit-berat').val(berat);

            $("#selected_item option[value='" + itemid + "']").prop('selected', true).trigger('change');
        })

        $("#tanggal").change(function() {
            var tanggal = $(this).val();
            $('#show').load("{{ route('hasilproduksi.show') }}?tanggal=" + tanggal);
        })

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#editproduksi')) {
                $('#editproduksi').DataTable().destroy();
            }
            $('#editproduksi').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY: 500,
                scrollX: true,
                scrollCollapse: true,
                paging: false,
            });
        });
</script>
@stop
<div class="modal-header">
    <h5 class="modal-title" id="showRiwayat{{ $data->id }}Label">Detail Harga Customer Sampingan</h5>
    <button type="button" class="close closeModalRiwayatSampingan" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <b>{{ $data->kode }} - {{ $data->nama }}</b>
    <div class="border-top mt-2 pt-2">
        <div id="item-loopSampingan">
            @foreach ($data->customersampingan as $key => $list)
            <div class="border-bottom pb-1 mb-1 mt-1">
                <input type="hidden" class="listItemRiwayatSampingan" value="{{ $list->item_id }}">
                <b>{{ $list->item->sku  ?? "#ITEM DIHAPUS" }} - {{ $list->item->nama ?? "ITEMDIHAPUS" }}</b>
                <button class="{{ $list->deleted_at != NULL ? 'btn btn-outline-success' : 'btn btn-outline-danger'}} rounded-0 px-2 py-0 float-right ml-1" data-id="{{ $list->id }}" onclick="hapusItemSampingan($(this).data('id'), {{ $data->id }})">{{ $list->deleted_at != NULL ? 'Aktifkan Item' : 'Nonaktifkan Item' }}</button>
                <div class="row px-0 mb-2">
                    <div class="col px-0">
                        <label for="qty">Qty Ekor/pcs/pack</label>
                        <input type="number" placeholder="Tuliskan Qty" min="0" class="form-control form-control-sm listQtyRiwayatSampingan" value="{{ $list->min_qty }}">
                    </div>
                    <div class="col">
                        <label for="berat">Berat</label>
                        <input type="number" placeholder="Tuliskan Berat" min="0" class="form-control form-control-sm listBeratRiwayatSampingan" value="{{ $list->min_berat}}">
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-auto pl-1 mt-2">
            <button onclick="addItemSampingan()" class="btn btn-primary"><i class="fa fa-plus"></i></button>
        </div>
    </div>
</div>

<script>
    y = 1 ;
    function addItemSampingan() {
        row = `
        <div class="border-bottom pb-1 mb-1 mt-1 addedItem-${y}">
            <div class="row px-0 mt-2">
                <div class="col-9">
                    <select data-width="100%" data-placeholder="Pilih Item" class="form-control form-control-sm select2 listItemRiwayatSampingan">
                        <option value=""></option>
                        @foreach ($itemSampingan as $row)
                            <option value="{{ $row->id }}">{{ $row->sku }}. {{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <button class="btn btn-outline-danger rounded-0 px-2 py-0 float-right ml-1 text-right cursor px-1"  onclick="deleteRowSampingan(${y})">Hapus Item</button>
                </div>
            </div>
            <div class="row mb-2 ml-1 mt-1">
                <div class="col-4 px-0">
                    <label for="qty">Qty Ekor/pcs/pack</label>
                    <input type="number" placeholder="Tuliskan Qty" min="0" class="form-control form-control-sm listQtyRiwayatSampingan">
                </div>
                <div class="col-5">
                    <label for="berat">Berat</label>
                    <input type="number" placeholder="Tuliskan Berat" min="0" class="form-control form-control-sm listBeratRiwayatSampingan">
                </div>
            </div>
        </div>
        `;
    
    
        $('#item-loopSampingan').append(row);
        $('.select2').select2({
            theme: 'bootstrap4'
        })
    
        y++;
    }
    
    function deleteRowSampingan(key) {
        $('.addedItem-'+key).remove();
    }
    
</script>
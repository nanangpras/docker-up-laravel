<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Hasil Produksi Bahan Baku {{ucwords($regu)}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
          <table class="table default-table table-bordered" width="100%">
                <thead>
                    <tr>
                        <td width="2%">No</td>
                        <td width="25%">Nama</td>
                        <td>Regu</td>
                        <td>Qty</td>
                        <td>Berat</td>
                        <td>Kondisi</td>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $ttQty      = 0;
                        $ttBerat    = 0;
                    @endphp
                    @foreach ($data as $item)
                    @php 
                        $ttQty      += $item->qty;
                        $ttBerat    += $item->berat;
                    @endphp
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->nama_items}}</td>
                        <td>{{$item->regu}}</td>
                        <td>{{$item->qty}}</td>
                        <td>{{$item->berat}}</td>
                        <td>{{$item->bb_kondisi}}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="3">Total</td>
                        <td>{{ $ttQty }}</td>
                        <td>{{ $ttBerat }}</td>
                        <td></td>
                    </tr>
                </tbody>
          </table>
    </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
</div>
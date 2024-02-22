<!-- Modal content-->
<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">EDIT FULFILL</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <form action="{{ route('editso.orderbahanbaku') }}" method="post">
        @csrf
        <input type="hidden" name="order_bb_id" value="{{ $data['id'] }}">
        <div class="modal-body">
            <div class="form-group">
                <div>Item</div>
                {{ $data['nama'] }}
            </div>
            <div class="form-group">
                NETSUITEID : {{ $data['netsuite_id'] }}
            </div>
            <div class="form-group">
                <div>NO DO</div>
                <div class="form-group">
                    <input type="text" name="no_do" class="form-control" value="{{$data['no_do']}}">
                </div>
            </div>
            <div class="row">
                <div class="col pr-1">
                    <div class="form-group">
                        Ekor/Qty
                        <input type="number" name="bb_item" class="form-control" value="{{$data['bb_item']}}" max="{{ $data['sisaqty'] }}">
                    </div>
                </div>
                <div class="col pl-1">
                    <div class="form-group">
                        Berat
                        <input type="number" name="bb_berat" step="0.01" class="form-control" value="{{$data['bb_berat']}}" max="{{ $data['sisaberat'] }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Edit</button>
        </div>
    </form>
</div>
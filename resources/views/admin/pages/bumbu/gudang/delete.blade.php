<form action="{{ route('bumbu.delete', $bgudang->id) }}" method="post">
    <div class="modal-body">
        @csrf
        @method('DELETE')
        <input type="hidden" name="key" value="delete_gudang">
        <p class="text-center">Apakah anda ingin menghapus history dengan <br><b>stock {{$bgudang->stock}} pcs <br> berat {{$bgudang->berat}} Kg</b> <br><b>{{$bgudang->status}}</b> ?</p>
    </div>
    <div class="modal-footer">
        <button type="button" onclick="javascript:window.location.reload()" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Ya</button>
    </div>
</form>
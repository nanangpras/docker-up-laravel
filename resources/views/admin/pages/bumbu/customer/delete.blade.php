<form action="{{ route('bumbu.delete', $customer->id) }}" method="post">
    <div class="modal-body">
        @csrf
        @method('DELETE')
        <input type="hidden" name="key" value="delete_customer">
        <p class="text-center">Apakah anda ingin menghapus <br><b>{{$customer->customers->nama}}</b> ?</p>
    </div>
    <div class="modal-footer">
        <button type="button" onclick="javascript:window.location.reload()" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Ya</button>
    </div>
</form>
<form action="{{route('pindah.delete',$hapus->id)}}" method="post">
    @csrf
    @method('delete')
    <div class="modal-body">
        <h6 class="text-center">Anda akan menghapus ? </h6>
        <p class="text-center">{{$hapus->nama}}</p>
        <p class="text-center">dengan qty : {{$hapus->qty}}</p>
        <p class="text-center">dan berat : {{$hapus->berat}}</p>
        
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Hapus</button>
    </div>
</form>
<form action="{{ route('retur.edit') }}" method="post">
    @csrf 
    <input type="hidden" name="key" value="editAlasan">
    <input type="hidden" name="id" class="form-control" value="{{$edit->id}}">
    <input type="hidden" name="jenis" class="form-control" value="{{$edit->jenis}}">
    <div class="modal-body">
        <div class="form-group">
            Kelompok
            <select name="kelompok" data-placeholder="Pilih Jenis" data-width="100%"
                class="form-control select2" required>
                <option value=""></option>
                <option value="Bau" {{$edit->kelompok == "Bau" ? 'selected' : ''}}>Bau</option>
                <option value="Memar" {{$edit->kelompok == "Memar" ? 'selected' : ''}}>Memar</option>
                <option value="Patah" {{$edit->kelompok == "Patah" ? 'selected' : ''}}>Patah</option>
                <option value="Warna Tidak Standar" {{$edit->kelompok == "Warna Tidak Standar" ? 'selected' : ''}}>Warna Tidak Standar</option>
                <option value="Kualitas lain-lain" {{$edit->kelompok == "Kualitas lain-lain" ? 'selected' : ''}}>Kualitas lain-lain</option>
                <option value="Non Kualitas" {{$edit->kelompok == "Non Kualitas" ? 'selected' : ''}}>Non Kualitas</option>
                <option value="Packing bermasalah" {{$edit->kelompok == "Packing bermasalah" ? 'selected' : ''}}>Packing bermasalah</option>
                <option value="Produk tidk sesuai order" {{$edit->kelompok == "Produk tidk sesuai order" ? 'selected' : ''}}>Produk tidk sesuai order</option>
                <option value="Salah order" {{$edit->kelompok == "Salah order" ? 'selected' : ''}}>Salah order</option>
                <option value="Tidak terkirim" {{$edit->kelompok == "Tidak terkirim" ? 'selected' : ''}}>Tidak terkirim</option>
                <option value="Masalah Internal Konsumen" {{$edit->kelompok == "Masalah Internal Konsumen" ? 'selected' : ''}}>Masalah Internal Konsumen</option>
            </select>
        </div>

        <div class="form-group">
            Nama Alasan
            <input type="text" name="nama" placeholder="Tuliskan nama alasan" value="{{$edit->nama}}" class="form-control" autocomplete="off" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
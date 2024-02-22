@if ($retur)
<form action="{{ route('retur.store') }}" method="POST">
    <div class="modal-body">
        @csrf
        <div class="form-group row">
            <div class="col">
                <label for="">Item</label>
                <select name="item" class="form-control select2" id="item" required>
                    <option value="" disabled selected hidden>Pilih </option>
                    @foreach ($item as $val)
                        <option value="{{ $val->id }}">{{ $val->nama }}</option>
                    @endforeach
                </select>
                @error('item') <div class="small text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group row">
            <div class="col-6">
                <label for="">QTY</label>
                <input type="number" name="qty" class="form-control" id="qty" placeholder="Kuantiti "
                    value="" autocomplete="off" required>
                @error('qty') <div class="small text-danger">{{ $message }}</div>
                @enderror

            </div>
            <div class="col-6">

                <label for="">Berat</label>
                <input type="number" name="berat" step="0.01" class="form-control" id="berat"
                    placeholder="Berat " value="" autocomplete="off" required>
                @error('berat') <div class="small text-danger">{{ $message }}
                    </div>
                @enderror
            </div>
        </div>
        <div class="form-group row">

            <div class="col">
                <label for="">Tujuan Retur</label>
                <select name="tujuan" class="form-control" id="tujuan">
                    <option value="" disabled selected hidden>Pilih Tujuan</option>
                    <option value="produksi">Reproses Produksi</option>
                    <option value="chillerfg">Sampingan</option>
                    <option value="gudang">Kembali Ke Freezer</option>
                    <option value="musnahkan">Musnahkan</option>
                </select>
                @error('tujuan') <div class="small text-danger">{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col">

                <label for="">Harga</label>
                <input type="number" name="harga" class="form-control" id="harga" placeholder="Harga "
                    value="" autocomplete="off">
                @error('harga') <div class="small text-danger">{{ $message }}
                    </div>
                @enderror

            </div>
        </div>


    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</form>


<table class="table default-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Rate</th>
            <th>Qty</th>
            <th>Berat</th>
            <th>Tujuan</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $no => $row)
        <tr>
            <td>{{$no+1}}</td>
            <td>
                {{ $row->to_item->nama }}
            </td>
            <td>
                Rp {{ $row->rate ?? "0" }}
            </td>
            <td>{{$row->qty}} Ekor/Pcs</td>
            <td>{{$row->berat}} Kg</td>
            <td>{{$row->unit}}</td>
            <td>
                <button class="btn btn-link text-danger p-0 ml-1" data-toggle="modal"
                    data-target="#modaledit" data-id="{{ $row->id }}" type="button">
                    <i class="fa fa-pencil"></i>
                </button>
                <button class="btn btn-link text-danger p-0 ml-1 deleteitem" data-id="{{ $row->id }}"
                    type="button">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>



        @endforeach
    </tbody>
</table>

@foreach ($data as $no => $row)

    <div class="modal @if (count($data) == 0) fade @endif" id="modaledit" tabindex="-1" role="dialog"
        aria-labelledby="modaleditLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modaleditLabel">Edit Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('retur.edit') }}" method="POST" id="form-edit-retur">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="idcustomer" id="idcustomer" value="{{ $row->id }}">
                        <div class="form-group row">
                            <div class="col">
                                <label for="">Item</label>
                                <input type="text" class="form-control" name="item" id="item" value="{{ $row->to_item->nama }}" readonly>
                                @error('item') <div class="small text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label for="">QTY</label>
                                <input type="number" name="qty" class="form-control" id="qty"
                                    placeholder="Kuantiti " value="{{ $row->qty }}" autocomplete="off" required>
                                @error('qty') <div class="small text-danger">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="col-6">

                                <label for="">Berat</label>
                                <input type="number" name="berat" step="0.01" class="form-control" id="berat"
                                    placeholder="Berat " value="{{ $row->berat }}" autocomplete="off" required>
                                @error('berat') <div class="small text-danger">{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">

                            <div class="col">
                                <label for="">Tujuan Retur</label>
                                <select name="tujuan" class="form-control" id="tujuan">
                                    <option value="" disabled selected hidden>Pilih Tujuan</option>
                                    <option value="produksi" @if($row->unit=="chillerbb") selected @endif>Reproses Produksi</option>
                                    <option value="chillerfg" @if($row->unit=="chillerfg") selected @endif>Sampingan</option>
                                    <option value="gudang" @if($row->unit=="gudang") selected @endif>Kembali Ke Frezeer</option>
                                    <option value="musnahkan" @if($row->unit=="musnahkan") selected @endif>Musnahkan</option>
                                </select>
                                @error('tujuan') <div class="small text-danger">{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col">

                                <label for="">Harga</label>
                                <input type="number" name="harga" class="form-control" id="harga"
                                    placeholder="Harga " value="{{$row->rate}}" autocomplete="off">
                                @error('harga') <div class="small text-danger">{{ $message }}
                                    </div>
                                @enderror

                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="simpan-edit-retur">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endforeach

<form action="{{ route('retur.selesaikan') }}" method="POST">
    @csrf
    <input type="hidden" name="retur_id" id="retur_id" value="{{ $retur->id ?? '' }}">
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Selesaikan</button>
    </div>
</form>

<script>

    $('#simpan-edit-retur').on('click', function(){
        console.log("Haha");
    })

    $('.select2').select2({
        theme: 'bootstrap4'
    });
    $('.deleteitem').click(function() {
        var id = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('retur.deleteitem') }}",
            method: "POST",
            data: {
                id: id
            },
            success: function(data) {
                showNotif('Berhasil Delete');

                $("#itemretur").load("{{ route('retur.itemretur') }}");
            }
        })
    })
</script>
@endif

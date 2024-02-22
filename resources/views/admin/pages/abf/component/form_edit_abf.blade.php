<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Ubah Data ABF Bongkar CS</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form>
        @csrf
        <div class="modal-body">
            <div class="form-group">
                Item - {{ $data_abf->id }}
                <input type="hidden" value="{{ $data_abf->id }}" id="idbongkarabf">
                <div>{{ $data_abf->item_name ?? '###' }}</div>

            </div>

            <div class="form-group">
                <b>ABF</b>
                <div class="row">
                    <div class="col pr-1">
                        <div class="form-group">
                            Qty
                            <input type="number" value="{{ $data_abf->qty_item}}" id="itembongkarabf"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="form-group">
                            Berat
                            <input type="number" value="{{ $data_abf->berat_item}}" id="beratbongkarabf" step="0.01"
                                class="form-control">
                        </div>
                    </div>

                </div>
                <b>ABF</b>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            Plastik
                            <select name="plastik" id="plastik" class="form-control select2">
                                <option value="">-plastik-</option>
                                @foreach ($plastik as $itempl)
                                <option value="{{$itempl->nama}}" {{ ($data_abf->packaging == $itempl->nama) ?
                                    'selected' : '' }} id="plastik">{{$itempl->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                   

                </div>
                {{-- <div class="form-group">
                    <div class="row">
                        <div class="col pr-1 pl-1">

                            Plastik
                            <select name="plastik" id="plastik" class="form-control select2">
                                <option value="">-plastik-</option>
                                @foreach ($plastik as $itempl)
                                <option value="{{$itempl->nama}}" {{ ($data_abf->packaging == $itempl->nama) ?
                                    'selected' : '' }} id="plastik">{{$itempl->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div> --}}
            </div>
            <hr>
            <div class="form-group">
                <b>Hasil Timbang</b>
                @foreach ($data_abf->hasil_timbang_selesai as $item)
                <div class="row">
                    <div class="col pr-1">
                        <div class="form-group">
                            Qty
                            <input type="number" value="{{ $item->qty}}" name="ekor" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col pl-1">
                        <div class="form-group">
                            Berat
                            <input type="number" value="{{ $item->berat}}" name="berat" step="0.01" class="form-control"
                                readonly>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" id="updatebongkarabf" class="btn btn-primary">Ubah</button>
        </div>
    </form>
</div>
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    $("#updatebongkarabf").on('click', function () {
        // alert('ok');
        let id          = $("#idbongkarabf").val();
        let qty_abf     = $("#itembongkarabf").val();
        let berat_abf   = $("#beratbongkarabf").val();
        let plastik     = $("#plastik").val();
        // alert(id)
        // alert(qty_abf)
        // alert(berat_abf)
        // alert(plastik)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('abf.togudang') }}",
            method: 'POST',
            data: {
                id:id,
                qty_item : qty_abf,
                berat_item : berat_abf,
                packaging : plastik,
                key:'updatebongkarabf'
            },
            success: function (data) {
                console.log(data);
                if (data.status == '200') {
                    location.reload()
                    showNotif(data.msg)
                }
            }
        });
    });
</script>
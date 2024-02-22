@extends('admin.layout.template')

@section('title', 'Musnahkan Item')

@section('footer')
<script>
    $('.select2').select2({
    theme: 'bootstrap4'
});
</script>

<script>
    $("#list").load("{{ route('musnahkan.index', ['key' => 'list']) }}&type=item");
</script>

<script>
    $('#submit_list').click(function() {
        var item    =   $("#item").val() ;
        var gudang  =   $("#gudang").val() ;
        var qty     =   $("#qty").val() ;
        var berat   =   $("#berat").val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#submit_list').hide() ;

        $.ajax({
            url: "{{ route('musnahkan.store') }}",
            method: "POST",
            data: {
                id      :   item,
                cold    :   gudang,
                qty     :   qty,
                berat   :   berat,
                key     :   'submit_list'
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    $("#list").load("{{ route('musnahkan.index', ['key' => 'list']) }}&type=item");
                    $("#item").val(null).trigger('change') ;
                    $("#qty").val('') ;
                    $("#berat").val('') ;
                }
                $('#submit_list').show() ;
            }
        });
    })
</script>

<script>
    $('#selesaikan').click(function() {
        var tanggal         =   $("#tanggal_submit").val() ;
        var keterangan      =   $("#keterangan").val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#selesaikan').hide() ;

        $.ajax({
            url: "{{ route('musnahkan.store') }}",
            method: "POST",
            data: {
                tanggal     :   tanggal,
                keterangan  :   keterangan,
                key         :   'selesaikan'
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    $("#list").load("{{ route('musnahkan.index', ['key' => 'list']) }}&type=item");
                    $("#keterangan").val('')
                }
                $('#selesaikan').show() ;
            }
        });
    })
</script>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col"><a href="{{ route('musnahkan.index') }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col font-weight-bold text-center">Musnahkan Item</div>
    <div class="col"></div>
</div>

<div class="row">
    <div class="col-lg-8 pr-lg-1">
        <section class="panel">
            <div class="card-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-8 pr-1">
                            Item
                            <select id="item" class="form-control select2" data-placeholder="Pilih Item"
                                data-width="100%">
                                <option value=""></option>
                                @foreach ($item as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-2 px-1">
                            Qty/Ekor
                            <input type="number" id="qty" placeholder="QTY" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-2 pl-1">
                            Berat
                            <input type="number" id="berat" step="0.01" placeholder="Berat" class="form-control"
                                autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    Gudang
                    <select id="gudang" class="form-control select2" data-placeholder="Pilih Gudang" data-width="100%">
                        <option value=""></option>
                        @foreach ($warehouse as $row)
                        <option value="{{ $row->id }}">{{ $row->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-footer text-right">
                <button class="btn btn-primary" id="submit_list">Submit</button>
            </div>
        </section>
    </div>

    <div class="col-lg-4 pl-lg-1">
        <div id="list"></div>

        <section class="panel">
            <div class="card-body">
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea id="keterangan" rows="2" placeholder="Tuliskan keterangan"
                        class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <label for="tanggal_submit">Tanggal</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal_submit" class="form-control" value="{{ date("Y-m-d") }}">
                </div>
            </div>
            <div class="card-footer text-right">
                <button class="btn btn-primary" id="selesaikan">Submit</button>
            </div>
        </section>
    </div>
</div>
@endsection
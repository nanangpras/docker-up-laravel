@extends('admin.layout.template')

@section('title', 'Detail Penerimaan Masuk')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('lpah.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-6 py-1 text-center">
        <b>Penerimaan Masuk</b>
    </div>
    <div class="col"></div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    @if ($data->lpah_status == 3)
                        <div class="alert alert-info">Menunggu Konfirmasi Check Data</div>
                    @else
                        <div class="alert alert-success">Data Berhasil Diselesaikan</div>
                    @endif
                </div>
                <div id='information'></div>

                <hr>
                @if (User::setijin(33))
                    @if ($data->lpah_status == 3)
                    <form action="{{ route('lpah.store', ['key' => 'selesai']) }}" method="POST">
                        @csrf <input type="hidden" name="x_code" value="{{ $data->id }}">
                        <button class="float-right btn btn-danger btn-rounded">Selesaikan</button>
                    </form>

                    <a href="{{ route('lpah.show', [$data->id, 'key' => 'edit_checker']) }}" class="btn btn-primary">Edit Data LPAH</a>
                    @endif
                    @if ($ceklogedit > 0)
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-id="{{$data->id}}" onclick="history_edit_lpah({{$data->id}})">History Edit</button>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div id='cart'></div>
            </div>
        </div>
    </div>
</div>

{{-- modal history edit --}}
<div class="modal fade" id="modal"
tabindex="-1" aria-labelledby="modalLabel"
aria-hidden="true">
<div class="modal-dialog modal-lg" style="width: 800px;">
    <div class="modal-content">
        <div class="modal-body">
            <div class="form-group text-center">
                <b>HISTORY EDIT DATA</b>
            </div>
            <div id="content_history"></div>
        </div>
    </div>
</div>
</div>
@endsection

@section('footer')
<script>

$(document).ready(function(){
    $('#cart').load("{{ route('lpah.cart', $data->id) }}");
    $('#information').load("{{ route('lpah.show', [$data->id, 'key' => 'info']) }}");
});

$(document).ready(function(){
    // Edit cart
    $(document).on('click','.edit_cart',function(){
        var row_id      =   $(this).data('id');
        var tipe_timbang=   $('#tipe_timbang' + row_id).val();
        var berat       =   $('#berat' + row_id).val();
        var keranjang   =   $('#keranjang' + row_id).val();

        $.ajaxSetup({
            headers : {
                'X-CSRF-TOKEN'  :   $('meta[name="csrf-token"]').attr('content')
            }
        });


        $.ajax({
            url     :   "{{ route('lpah.update', $data->id) }}",
            method  :   "PATCH",
            data    :   { row_id: row_id, berat: berat, keranjang: keranjang, tipe_timbang: tipe_timbang, key: 'editkeranjang' },
            success :   function(data){
                            $('#modal' + data.row_id).modal('hide');
                            $('.modal-backdrop').remove();
                            $('#cart').load("{{ route('lpah.cart', $data->id) }}");
                            $('#susut').load("{{ route('lpah.susut', $data->id) }}");
                            $('#information').load("{{ route('lpah.show', [$data->id, 'key' => 'info']) }}");
                            showNotif('Edit berhasil')
                        }
        });

    });
});

function history_edit_lpah (id) { 
            $.ajax({
                url: "{{route('lpah.index')}}",
                type: "GET",
                data: {
                    id: id,
                    key: 'history_edit_lpah'
                },
                success: function(data){
                    console.log(id);
                    $('#content_history').html(data);
                    $('#modal').modal('show');
                }
            });
        };


</script>
@endsection

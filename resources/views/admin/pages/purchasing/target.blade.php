@extends('admin.layout.template')

@section('title', 'Daftar Toleransi')

@section('footer')
<script>
$("#input_data").load("{{ route('purchasing.target', ['key' => 'input']) }}");
$("#input_daftar").load("{{ route('purchasing.target', ['key' => 'daftar']) }}");
</script>

<script>
$(document).on('click', '.hapus_target', function() {
    var id  =   $(this).data('id') ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('purchasing.targetdestroy') }}",
        method: "DELETE",
        data: {
            id  :   id,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif('Hapus target berhasil');
                $("#input_data").load("{{ route('purchasing.target', ['key' => 'input']) }}");
                $("#input_daftar").load("{{ route('purchasing.target', ['key' => 'daftar']) }}");
            }
        }
    });
})
</script>

<script>
$(document).on('click', '#selesaikan', function() {
    var alamat  =   $("#alamat").val() ;
    var target  =   $("#target").val() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('purchasing.targetstore') }}",
        method: "POST",
        data: {
            alamat  :   alamat,
            target  :   target,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif('Target berhasil ditambahkan');
                $("#input_data").load("{{ route('purchasing.target', ['key' => 'input']) }}");
                $("#input_daftar").load("{{ route('purchasing.target', ['key' => 'daftar']) }}");
            }
        }
    });
})
</script>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('purchasing.index') }}" class="btn btn-outline btn-sm btn-back"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col py-2 text-center"><b>DAFTAR TOLERANSI</b></div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div id="input_data"></div>
    </div>
</section>

<div id="input_daftar"></div>
@endsection

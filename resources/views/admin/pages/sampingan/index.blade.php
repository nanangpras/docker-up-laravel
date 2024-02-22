@extends('admin.layout.template')

@section('title', 'Penjualan Sampingan')

@section('footer')
    <script>
        $("#show").load("{{ route('sampingan.datashow') }}"+"?tanggal={{$tanggal}}&customer={{$customer}}&search={{$search}}&key={{$key ?? ''}}");

        $(document).on('click', '.prosessampingan', function() {
            var x_code      = document.getElementsByClassName("xcode");
            var DB_qty      = document.getElementsByClassName("qty");
            var DB_berat    = document.getElementsByClassName("berat");
            var DB_nom      = document.getElementsByClassName("item");
            var DB_order    = document.getElementsByClassName("order");
            var qty         = [];
            var item        = [];
            var berat       = [];
            var order       = [];
            var xcode       = [];
            for (var i = 0; i < DB_nom.length; ++i) {
                item.push(DB_nom[i].value);
                qty.push(DB_qty[i].value);
                berat.push(DB_berat[i].value);
                order.push(DB_order[i].value);
                xcode.push(x_code[i].value);
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('sampingan.jualsampingan') }}",
                method: "POST",
                data: {
                    xcode   : xcode,
                    qty     : qty,
                    item    : item,
                    berat   : berat,
                    order   : order,
                },
                success: function(data) {
                    if (data.status == 400) {
                        showAlert(data.msg) ;
                    } else {
                        $('#qty').val('');
                        $('#berat').val('');
                        $("#show").load("{{ route('sampingan.datashow') }}");
                        showNotif('Berhasil Simpan');
                    }
                }
            });
        });
    </script>
@endsection

@section('content')
<div class="text-center mb-4">
    <b>EVIS JUAL SAMPINGAN</b>
</div>

<div id="show"></div>
@stop

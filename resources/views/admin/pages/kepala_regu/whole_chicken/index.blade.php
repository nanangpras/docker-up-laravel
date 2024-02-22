@extends('admin.layout.template')

@section('title', 'Preparation Kepala Regu Whole Chicken')

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('kepalaregu.index') }}" class="btn btn-outline btn-sm btn-back"> <i
                    class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>Preparation Kepala Regu Whole Chicken</b>
        </div>
        <div class="col"></div>
    </div>

    {{-- <section class="panel">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group row">
                        <div class="col">
                            <label>Tanggal Potong</label>
                            <input class="form-control" type="text" name="under" id="under" value="" readonly>
                        </div>
                        <div class="col">
                            <label>Nama Customer</label>
                            <input class="form-control" type="text" name="over" id="over" value="" readonly>
                        </div>
                        <div class="col">
                        </div>
                        <div class="col">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <div id="show_data"></div>

    <script>
        $("#show_data").load("{{ route('kepalaregu.wholeshow') }}")

        $(document).ready(function() {

            $(document).on('click', '.selesai_freestock', function() {
                var row_id = $(this).data('id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('kepalaregu.wholefreestockselesai') }}",
                    method: "POST",
                    data: {
                        row_id: row_id,
                    },
                    success: function(data) {
                        $("#freestock").load("{{ route('kepalaregu.wholefreestock') }}");
                    }
                });
            })

            $(document).on('click', '.input_freestock', function() {
                var row_id = $(this).data('id');
                var freestock = $('input:radio[name=ukuran_ayam' + row_id + ']:checked').val();
                var item = $("#item" + row_id).val();
                var qty = $("#qty" + row_id).val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('kepalaregu.wholefreestockstore') }}",
                    method: "POST",
                    data: {
                        row_id: row_id,
                        freestock: freestock,
                        item: item,
                        qty: qty,
                    },
                    success: function(data) {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open');
                        $.get("{{ route('kepalaregu.wholefreestock') }}", {
                            'id': row_id
                        }, function(data) {
                            $('#tempoarary' + row_id).html(data);
                        });

                        $.get("{{ route('kepalaregu.wholefreestock') }}", {
                            'list': row_id
                        }, function(data) {
                            $('#liststock' + row_id).html(data);
                        });

                        $('input:radio[name=ukuran_ayam' + row_id + ']:checked')[0].checked =
                            false;
                        $("#item" + row_id).val('');
                        $("#qty" + row_id).val('');
                    }
                });
            })

            $(document).on('click', '.del_temporary', function() {
                var row_id = $(this).data('id');
                var list = $(this).data('list');
                var item = $(this).data('item');
                var qty = $(this).data('qty');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('kepalaregu.wholefreestockdelete') }}",
                    method: "POST",
                    data: {
                        row_id: row_id,
                        item: item,
                        list: list,
                        qty: qty,
                    },
                    success: function(data) {
                        $.get("{{ route('kepalaregu.wholefreestock') }}", {
                            'id': row_id
                        }, function(data) {
                            $('#tempoarary' + row_id).html(data);
                        });

                        $.get("{{ route('kepalaregu.wholefreestock') }}", {
                            'list': row_id
                        }, function(data) {
                            $('#liststock' + row_id).html(data);
                        });
                    }
                });
            })

            $(document).on('click', '.masuk', function() {
                var row_id = $(this).data('kode');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                console.log(row_id);
                $.ajax({
                    url: "{{ route('kepalaregu.store') }}",
                    method: "POST",
                    data: {
                        row_id: row_id
                    },
                    success: function(data) {
                        $("#show_data").load("{{ route('kepalaregu.wholeshow') }}")
                    }
                });
            })

            $(document).on('click', '.proses', function() {
                var row_id = $(this).data('id');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                console.log(row_id);
                $.ajax({
                    url: "{{ route('kepalaregu.storeall') }}",
                    method: "POST",
                    data: {
                        row_id: row_id
                    },
                    success: function(data) {
                        $("#show_data").load("{{ route('kepalaregu.wholeshow') }}")
                    }
                });
            })

            $(document).on('click', '.selesai', function() {
                var row_id = $(this).data('selesai');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                console.log(row_id);
                $.ajax({
                    url: "{{ route('kepalaregu.selesai') }}",
                    method: "POST",
                    data: {
                        row_id: row_id
                    },
                    success: function(data) {
                        $("#show_data").load("{{ route('kepalaregu.wholeshow') }}")
                    }
                });
            })

            $(document).on('click', '.chiller', function() {
                var order = $(this).data('chiller');
                var item = $(this).data('kode');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('kepalaregu.sendchiller') }}",
                    method: "POST",
                    data: {
                        order: order,
                        item: item
                    },
                    success: function(data) {
                      $("#show_data").load("{{ route('kepalaregu.wholeshow') }}")
                    }
                });
            })

            $(document).on('click', '.abf', function() {
                var order = $(this).data('chiller');
                var item = $(this).data('kode');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('kepalaregu.sendabf') }}",
                    method: "POST",
                    data: {
                        order: order,
                        item: item
                    },
                    success: function(data) {
                       $("#show_data").load("{{ route('kepalaregu.wholeshow') }}")
                    }
                });
            })
        })

    </script>

@stop

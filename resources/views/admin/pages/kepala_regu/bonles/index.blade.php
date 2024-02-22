@extends('admin.layout.template')

@section('title', 'Kepala Regu')

@section('content')

    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('kepalaregu.index') }}" class="btn btn-outline btn-sm btn-back"> <i
                    class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>Preparation Kepala Regu Bonless</b>
        </div>
        <div class="col"></div>
    </div>

    <div id="show_data"></div>

    <script>
        $("#show_data").load("{{ route('kepalaregu.bonelesshow') }}")

        $(document).ready(function() {
            // Edit cart
            $(document).on('click', '.masuk', function() {
                var row_id = $(this).data('kode');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                console.log(row_id);
                $.ajax({
                    url: "{{ route('kepalaregu.storeproses') }}",
                    method: "POST",
                    data: {
                        row_id: row_id
                    },
                    success: function(data) {
                        $("#show_data").load("{{ route('kepalaregu.bonelesshow') }}")
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
                        $("#show_data").load("{{ route('kepalaregu.bonelesshow') }}")
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
                        $("#show_data").load("{{ route('kepalaregu.bonelesshow') }}")
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
                        $("#show_data").load("{{ route('kepalaregu.bonelesshow') }}")
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
                        $("#show_data").load("{{ route('kepalaregu.bonelesshow') }}")
                    }
                });
            })
        })

    </script>
@stop

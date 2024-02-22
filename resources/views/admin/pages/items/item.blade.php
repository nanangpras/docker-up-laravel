@extends('admin.layout.template')

@section('title', 'Data Item')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>

        </div>
        <div class="col text-center">
            <b>Data Item</b>
        </div>
        <div class="col"></div>
    </div>

    {{-- <section class="panel">
<div class="card-body">
    <div class="row">
        <div class="col">
            <form method="POST" action="{{url('admin/upload-item-excel')}}" enctype="multipart/form-data">
            @csrf
                <input type="hidden" value="{{Request::url()}}"  name="url">
                <input type="file" name="file">

                @if (!empty(Session::get('status')) && Session::get('status') == '1')
                    <br><br>Saved : {{Session::get('no_saved')}} - Updated : {{Session::get('no_updated')}} - Unsaved : {{Session::get('no_unsaved')}}
                @endif

                <button class="btn btn-blue">Save</button>
            </form>
        </div>
        <div class="col">
            <form action="{{ route('item.index') }}" method="get">
                <div class="row">
                    <div class="col">
                        <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="Cari...">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-block btn-primary">Cari</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col"><a href="{{url('api/crawl-item?next=item')}}" class="btn btn-blue pull-right">Crawl Item</a></div>
    </div>

</div>
</section> --}}


    <section class="panel">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="category_item">Kategori</label>
                    <select name="category_item" id="category_item" class="form-control select2">
                        <option value="" disabled selected hidden>Pilih Kategori</option>
                        @foreach ($category as $item)
                            <option value="{{ $item->id }}"> {{ $item->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="cari">Cari</label>
                    <input type="text" placeholder="Cari..." autocomplete="off" id="cari" class="form-control">
                </div>
                <div class="col-md-4 mb-3 mt-4">
                    {{-- <a href="{{ route('item.index', array_merge(['key' => 'download'], $_GET)) }}" class="btn btn-success">Unduh</a> --}}
                    <button class="btn btn-primary" id="download-item"> <i class="fa fa-download"></i> <i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i> <span id="text">Export Excel</span> </button>
                </div>
            </div>
            <h5 id="loading" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....
            </h5>
            <div id="data_item"></div>
            

            {{-- {{ $data->links() }} --}}

        </div>
    </section>

@stop

@section('header')
    <link rel="stylesheet" type="text/css" href="{{ asset('') }}plugin/DataTables/datatables.min.css" />
@stop

@section('footer')
    <script>
        $("#loading").attr("style", 'display: block');
        $("#data_item").load("{{ route('item.index', ['key' => 'filter']) }}", function() {
            $("#loading").attr("style", 'display: none');
        });
    </script>
    <script>
        $("#cari").on('keyup', function () {
            var cari = encodeURIComponent($("#cari").val());
            // alert(cari);
            $("#data_item").load("{{ route('item.index', ['key' => 'filter']) }}&cari=" + cari, function() {
                $("#loading").attr("style", 'display: none') ;
            });
        });

        $("#category_item").on('change', function () {
            var id = $("#category_item").val();
            // alert(id);
            $("#data_item").load("{{ route('item.index', ['key' => 'filter']) }}&category_item=" + id, function() {
                $("#loading").attr("style", 'display: none') ;
            });
        });

        $(document).ready(function () {
                $("#download-item").click(function (e) { 
                // e.preventDefault();
                // alert('ok');
                var kategori = $("#category_item").val();
                $.ajax({
                    type: "GET",
                    url: "{{route('item.index', ['key' => 'download'])}}",
                    data: {
                        'key'       : 'download',
                        category_item    : kategori
                    },
                    beforeSend: function() {
                        $(".spinerloading").show();
                        $("#text").text('Downloading...');
                    },
                    success: function(data) {
                        $("#download-item").attr('disabled');
                        setTimeout(() => {
                            $("#text").text('Export Excel');
                            $(".spinerloading").hide();
                            window.location.href    =   "{{ route('item.index', ['key' => 'download']) }}&category_item=" + kategori ;
                        }, 3000);
                    }
                });
            });
        });
    </script>
    {{-- <script type="text/javascript" src="{{ asset('') }}plugin/DataTables/datatables.min.js"></script> --}}
    <script>
        $('.select2').select2({
            theme: 'bootstrap4',
        })
    </script>
@stop

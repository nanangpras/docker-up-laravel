@extends('admin.layout.template')

@section('title', 'Data Customer')

@section('header')
    <style>
        

    </style>
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('customers.index') }}" class="btn btn-outline btn-sm btn-back"> <i
                    class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="col text-center">
            <b>Data Customer</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <div class="table-responsive">
                <table width="100%" class="table table-sm default-table dataTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Judul</th>
                            <th>Item</th>
                            <th>Bom</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($spesifikasi as $i => $cus)

                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $cus->spescus->nama }}</td>
                                <td>{{ $cus->judul }}</td>
                                <td>{{ $cus->spesitem->nama }}</td>
                                <td>{{ $cus->spesbom->bom_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-body">
            <form action="{{ route('customers.store') }}" method="post">
                @csrf
                <input type="hidden" name="idcustomer" id="idcustomer" value="{{ $customer->id }}">
                <div class="form-group row">
                    <div class="col">
                        <div class="form-group">
                            <label>Judul</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-white" name="judul" id="judul">
                            </div>
                            @error('judul') <div class="small text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Item</label>
                            <div class="input-group">
                                <select name="item" id="item" class="form-control">
                                    <option value="">Pilih</option>
                                    @foreach ($item as $item)
                                        <option value="{{ $item->id }}"> {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('item') <div class="small text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col">
                        <div class="form-group">
                            <label>BOM</label>
                            <div class="input-group">
                                <select name="bom" id="bom" class="form-control select2">
                                    <option value="">Pilih</option>
                                    @foreach ($bom as $bom)
                                        <option value="{{ $bom->id }}"> {{ $bom->bom_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('bom') <div class="small text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="box-body">
                        <a href="" data-toggle="modal" class="btn btn-blue btn-sm" data-target="#addImages">Tambah
                            Foto</a><br><br>
                        <textarea class="editor form-control" name="content"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col">
                        <div class="form-group">

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">

                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <div id="addImages" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Images</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-8">
                            <div id="image-list"></div>
                        </div>
                        <div class="col-sm-4">
                            <form id="form-images-ajax" method="post" action="{{ url('admin/store-post-images') }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="url" value="{{ Request::url() }}" required>
                                <div class="form-group row">
                                    <label class="col-sm-12 col-form-label">Photo</label>
                                    <div class="col-sm-12">
                                        <input type="file" name="images" value="" required>
                                        <input type="hidden" name="url" value="0" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-12 col-form-label">Name</label>
                                    <div class="col-sm-12">
                                        <input type="text" name="name" class="form-control" placeholder="Name" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-12 col-form-label">Desc</label>
                                    <div class="col-sm-12">
                                        <input type="text" name="img_desc" class="form-control" placeholder="Description"
                                            value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-12 col-form-label">Keyword (SEO)</label>
                                    <div class="col-sm-12">
                                        <input type="text" name="keyword" class="form-control" placeholder="Keyword"
                                            value="">
                                    </div>
                                </div>
                                <div class="form-group row" id="loading" style="display: none;">
                                    <div class="col-sm-12">
                                        <img src="{{ asset('images/metaball-loader.gif') }}" width="40px">
                                        Loading ...
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-12 col-form-label"></label>
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-blue" id="button-submit-images"
                                            onclick="return false;">Simpan Images</button> &nbsp
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .modal-outer {
            overflow: auto;
            max-height: 650px;
        }

        .modal-inner {
            height: 100%;
        }

    </style>

    <script>
        getStorageGallery();

        function getStorageGallery() {

            var url = "{{ url('admin/image-lists') }}";
            var asset = "{{ asset('/') }}";

            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {

                    $('#image-list').html(response);
                }

            });

        }

        $('#button-submit-images').on('click', function() {

            var data = new FormData($('#form-images-ajax')[0]);

            var images = $('#images').val();

            if (images == '') {
                alert("Please fill the blank");
            } else {
                $.ajax({
                    type: "POST",
                    url: "{{ url('admin/store-image') }}",
                    data: data,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('#loading').fadeIn();
                    },
                    success: function(rsp) {

                        response = JSON.parse(rsp);
                        if (response.status == 1) {

                            setTimeout(function() {
                                $('#form-images-ajax').trigger('reset');
                                getStorageGallery();
                                $('#loading').fadeOut();
                            }, 3000)

                        } else {
                            getStorageGallery();
                        }
                    }
                });
            }

        })

    </script>
    <!-- @section('header')
        <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
    @stop

    @section('footer')
        <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
    @stop -->

@stop

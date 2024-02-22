@extends('admin.layout.template')

@section('title', 'Antemortem')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <a href="{{ route('qc.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
                Back</a>
        </div>
        <div class="col text-center">
            <b>QC</b>
        </div>
        <div class="col"></div>
    </div>

    <section class="panel">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md">
                    <div class="row">
                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Tanggal Potong</div>
                                <b>{{ $data->lpah_tanggal_potong ?? '###' }}</b>
                            </div>
                        </div>
                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">No Urut Mobil</div>
                                <b>{{ $data->no_urut }}</b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md">
                    <div class="row">
                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Berat DO</div>
                                <b>{{ $data->sc_berat_do }} Kg</b>
                            </div>
                        </div>
                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Total DO</div>
                                <b>{{ $data->sc_ekor_do }} Ekor</b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md">
                    <div class="row">
                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Supir</div>
                                <b>{{ $data->sc_pengemudi }}</b>
                            </div>
                        </div>
                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Ukuran</div>
                                <b>@if ($data->prodpur->ukuran_ayam == '&lt; 1.1') {{ '<1.1' }} @else {{ $data->prodpur->ukuran_ayam }} @endif</b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md">
                    <div class="row">
                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Jenis Ekspedisi</div>
                                <b class="text-capitalize">{{ $data->po_jenis_ekspedisi }}</b>
                            </div>
                        </div>
                        <div class="col-6 col-md-12">
                            <div class="form-group">
                                <div class="small">Kondisi Ayam</div>
                                <b>{{ $data->kondisi_ayam }}</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="card-body">

            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link tab-link" id="tab-1-tab" data-toggle="tab" href="#tab-1" role="tab"
                        aria-controls="tab-1" aria-selected="true">Antem</a>
                    <a class="nav-item nav-link tab-link" id="tab-2-tab" data-toggle="tab" href="#tab-2" role="tab"
                        aria-controls="tab-2" aria-selected="false">Post</a>
                    <a class="nav-item nav-link tab-link " id="tab-3-tab" data-toggle="tab" href="#tab-3" role="tab"
                        aria-controls="tab-3" aria-selected="false">Uniformity</a>
                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show card-body" id="tab-1" role="tabpanel" aria-labelledby="tab-1-tab"
                    style=" border: 1px solid #dddddd">
                    @include('admin.pages.qc.antemoretem')
                </div>

                <div class="tab-pane fade card-body" id="tab-2" role="tabpanel" aria-labelledby="tab-2-tab"
                    style=" border: 1px solid #dddddd">
                    @include('admin.pages.qc.postmortem')
                </div>

                <div class="tab-pane fade card-body" id="tab-3" role="tabpanel" aria-labelledby="tab-3-tab"
                    style=" border: 1px solid #dddddd">
                    @include('admin.pages.qc.unifomity')
                </div>
            </div>
    </section>

    <style>
        .border.rounded input {
            padding-left: 0px;
            padding-right: 0px;
            border: 0px;
        }

    </style>

@stop
@section('footer')
    <script>
        $(document).ready(function() {
            $('#cart').load("{{ route('uniform.cart', $data->id) }}");
            $('#summary').load("{{ route('uniform.summary', $data->id) }}");

            // Tambah cart
            $('.add_cart').click(function() {
                var berat = $('#berat').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('.label-timbang').val('') ;

                $.ajax({
                    url: "{{ route('uniform.add', $data->id) }}",
                    method: "POST",
                    data: {
                        berat: berat
                    },
                    success: function(data) {
                        $('#cart').load("{{ route('uniform.cart', $data->id) }}");
                        $('#summary').load("{{ route('uniform.summary', $data->id) }}");
                    }
                });

            });
        });

    </script>

    <script>
        var hash = window.location.hash.substr(1);
        var href = window.location.href;

        deafultPage();

        function deafultPage() {
            if (hash == undefined || hash == "") {
                hash = "tab-1";
            }

            $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');
        }


        $('.tab-link').click(function(e) {
            e.preventDefault();
            status = $(this).attr('aria-controls');
            window.location.hash = status;
            href = window.location.href;

        });

    </script>

@endsection

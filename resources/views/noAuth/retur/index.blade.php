@extends('admin.layout.template-no-auth')

@section('title', 'Retur Authorization')

@section('content')

<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center">
        <b>RETUR</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">

    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-retur-tab" data-toggle="pill"
                            href="#custom-tabs-retur" role="tab" aria-controls="custom-tabs-retur"
                            aria-selected="true">List Retur</a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-tabContent">
                        <div class="tab-pane fade" id="custom-tabs-retur" role="tabpanel"
                            aria-labelledby="custom-tabs-retur-tab">
                            <div class="table-responsive">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Filter Tanggal Retur</label>
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif name="tanggalawal" class="form-control"
                                                id="tanggalawal" placeholder="Tuliskan " value="{{ date('Y-m-d') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">&nbsp;</label>
                                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                                                min="2023-01-01" @endif name="tanggalakhir" class="form-control"
                                                id="tanggalakhir" placeholder="Tuliskan " value="{{ date('Y-m-d') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Tujuan</label>
                                            <select id="tujuanlist" class="form-control" name="tujuan">
                                                <option value="">- Semua -</option>
                                                <option value="chillerfg">Chiller FG</option>
                                                <option value="chillerbb">Chiller BB</option>
                                                <option value="gudang">Gudang</option>
                                                <option value="musnahkan">Musnahkan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Satuan</label>
                                            <select class="form-control" id="satuanlist" name="satuan">
                                                <option value="">- Semua -</option>
                                                <option value="kg">Kg</option>
                                                <option value="ekor">Ekor/Pcs/Pack</option>
                                                <option value="pack">Package</option>
                                                <option value="karung">Karung</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Penanganan</label>
                                            <select class="form-control" id="penangananlist" name="penanganan">
                                                <option value="">- Semua -</option>
                                                <option value="Produksi">Reproses Produksi</option>
                                                <option value="Sampingan">Sampingan</option>
                                                <option value="freezer">Kembali Ke Freezer</option>
                                                <option value="musnahkan">Musnahkan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">Pencarian Kata</label>
                                            <input type="text" name="kata" class="form-control" id="katalist"
                                                autocomplete="off" placeholder="Pencarian" name="pencarian">
                                        </div>
                                    </div>
                                </div>
                                <div id="loading-list-detail" class="text-center" style="display: none">
                                    <img src="{{ asset('loading.gif') }}" style="width: 20px"> Loading ...
                                </div>
                                <div id="retur-summary-list"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $('.select2').select2({
            theme: 'bootstrap4'
        });

        var hash = window.location.hash.substr(1);
        var href = window.location.href;

        defaultPage();

        function defaultPage() {
            if (hash == undefined || hash == "") {
                hash = "custom-tabs-retur";
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

        if(hash === "custom-tabs-retur"){
            loadReturQC();
        }
        $("#custom-tabs-retur-tab").on('click', function(){
            loadReturQC();
        });

        $('#tanggalawal,#tanggalakhir,#satuanlist,#tujuanlist,#penangananlist').change(function() {
            setTimeout(function(){
                loadReturQC();
            },1000)
        })
        $('#katalist').keyup(function() {
            setTimeout(function(){
                loadReturQC();
            },1000)
        })

        function loadReturQC() {
            $('#loading-list-detail').show();
            var tanggalawal     = $("#tanggalawal").val();
            var tanggalakhir    = $("#tanggalakhir").val();
            var kata            = encodeURIComponent($("#katalist").val());
            var tujuan          = $("#tujuanlist").val();
            var satuan          = $("#satuanlist").val();
            var penanganan      = $("#penangananlist").val();

            $.ajax({
                url: "{{ route('view_progress') }}",
                method: "GET",
                data:{
                    'role'          : 'marketing',
                    'key'           : 'retur',
                    '_token'        : "{{ $tToken }}",
                    'name'          : "{{ $subsidiary }}",
                    'GenerateToken' : "{{ $gettoken }}",
                    'subkey'        : 'view_data_retur',
                    'tanggalawal'   : tanggalawal,
                    'tanggalakhir'  : tanggalakhir,
                    'kata'          : kata,
                    'satuan'        : satuan,
                    'tujuan'        : tujuan,
                    'penanganan'    : penanganan
                },
                success: function(data){
                    $("#retur-summary-list").html(data);
                    $("#loading-list-detail").hide()
                }
            });
        }
</script>
@stop
@extends('admin.layout.template')

@section('title', 'Hasil Produksi')

@section('content')

<div class="text-center mb-4">
    <b>TIMBANG HASIL PRODUKSI</b>
</div>

<form action="{{ route('hasilproduksi.index') }}" method="GET">
    <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-6">
            <div class="form-group">
                Pencarian
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif name="tanggalawal" id="tanggalawal" class="form-control change-date" value="{{ $tanggal }}"
                    id="pencarian" placeholder="Cari...." autocomplete="off">
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-6">
            &nbsp;
            <div class="form-group">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif name="tanggalakhir" id="tanggalakhir" class="form-control change-date" value="{{ $tanggal }}"
                    id="pencarian" placeholder="Cari...." autocomplete="off">
            </div>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-6">
            <div class="form-group">
                Lokasi
                <select name="lokasi" id="lokasi" class="form-control">
                    <option value="0">Pilih Lokasi</option>
                    <option value="1">Abf</option>
                    <option value="2">Ekspedisi</option>
                    <option value="3">Titip CS</option>
                    <option value="NULL">Chiller</option>
                </select>
            </div>
        </div>
    </div>
</form>

<div class="form-group">
    <label>
        <input id="stock-kosong" type="checkbox" class="btn btn-blue" name="stock-kosong"> Stock Kosong
    </label>
</div>

<section class="panel">
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" id="custom-tabs-three-stock-tab" data-toggle="pill"
            href="#custom-tabs-three-stock" role="tab" aria-controls="custom-tabs-three-stock" aria-selected="true">
            Stock All
        </a>
        <a class="nav-item nav-link" id="custom-tabs-three-boneless-tab" data-toggle="pill"
            href="#custom-tabs-three-boneless" role="tab" aria-controls="custom-tabs-three-boneless"
            aria-selected="true">
            Boneless
        </a>
        <a class="nav-item nav-link" id="custom-tabs-three-parting-tab" data-toggle="pill"
            href="#custom-tabs-three-parting" role="tab" aria-controls="custom-tabs-three-parting"
            aria-selected="false">
            Parting
        </a>
        <a class="nav-item nav-link" id="custom-tabs-three-marinasi-tab" data-toggle="pill"
            href="#custom-tabs-three-marinasi" role="tab" aria-controls="custom-tabs-three-marinasi"
            aria-selected="false">
            M
        </a>
        <a class="nav-item nav-link" id="custom-tabs-three-whole-tab" data-toggle="pill" href="#custom-tabs-three-whole"
            role="tab" aria-controls="custom-tabs-three-whole" aria-selected="false">
            Whole Chicken
        </a>
        <a class="nav-item nav-link" id="custom-tabs-three-evis-tab" data-toggle="pill" href="#custom-tabs-three-evis"
            role="tab" aria-controls="custom-tabs-three-evis" aria-selected="false">
            Evis
        </a>
        <a class="nav-item nav-link" id="custom-tabs-three-nonlb-tab" data-toggle="pill" href="#custom-tabs-three-nonlb"
            role="tab" aria-controls="custom-tabs-three-nonlb" aria-selected="false">
            Non LB
        </a>
    </div>
    <div class="card-body card-outline card-tabs">
        <div class="tab-content" id="custom-tabs-three-tabContent">

            <div class="tab-pane fade show active" id="custom-tabs-three-stock" role="tabpanel"
                aria-labelledby="custom-tabs-three-stock-tab">
                <h5 class="text-center loading-stockall"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div id="stockall"></div>
            </div>

            <div class="tab-pane fade show" id="custom-tabs-three-boneless" role="tabpanel"
                aria-labelledby="custom-tabs-three-boneless-tab">
                <h5 class="text-center loading-boneless"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div id="boneless"></div>
            </div>

            <div class="tab-pane fade" id="custom-tabs-three-parting" role="tabpanel"
                aria-labelledby="custom-tabs-three-parting-tab">
                <h5 class="text-center loading-parting"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div id="parting"></div>
            </div>

            <div class="tab-pane fade" id="custom-tabs-three-marinasi" role="tabpanel"
                aria-labelledby="custom-tabs-three-marinasi-tab">
                <h5 class="text-center loading-marinasi"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div id="marinasi"></div>
            </div>

            <div class="tab-pane fade" id="custom-tabs-three-whole" role="tabpanel"
                aria-labelledby="custom-tabs-three-whole-tab">
                <h5 class="text-center loading-whole"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div id="whole"></div>
            </div>

            <div class="tab-pane fade" id="custom-tabs-three-evis" role="tabpanel"
                aria-labelledby="custom-tabs-three-evis-tab">
                <h5 class="text-center loading-evis"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div id="evis"></div>
            </div>

            <div class="tab-pane fade" id="custom-tabs-three-nonlb" role="tabpanel"
                aria-labelledby="custom-tabs-three-nonlb-tab">
                <h5 class="text-center loading-nonlb"><i class="fa fa-refresh fa-spin"></i> Loading....</h5>
                <div id="chiller-nonlb"></div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

@stop

@section('footer')

<script>
    var hash = window.location.hash.substr(1);
        var href = window.location.href;

        deafultPage();

        function deafultPage() {
            if (hash == undefined || hash == "") {
                hash = "custom-tabs-three-stock";
            }

            $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');

        }

        $('.nav-link').click(function(e) {
            e.preventDefault();
            status = $(this).attr('aria-controls');
            window.location.hash = status;
            href = window.location.href;
        });

        // -----------------------------------------------------------------------------------
        var kosong        = $('#stock-kosong').is(':checked'); //variabel untuk status stock kosong
        var tanggal       = $('#tanggalawal').val();
        var tanggalakhir  = $('#tanggalakhir').val();
        var lokasi        = $('#lokasi').val();

        $('#tanggalawal, #tanggalakhir,#lokasi,#stock-kosong').on('change', function() {
            tanggal         = $('#tanggalawal').val();
            tanggalakhir    = $('#tanggalakhir').val();
            lokasi          = $('#lokasi').val();
            kosong          = $('#stock-kosong').is(':checked');  //menampilkan stock kosong ketika di checklist
            reloadFG();
            // console.log()


        })

        function reloadFG() {
            if (window.location.hash.substr(1) == "custom-tabs-three-stock") {
                loadStockAll();

            } else if (window.location.hash.substr(1) == "custom-tabs-three-boneless") {
                loadBoneless();

            } else if (window.location.hash.substr(1) == "custom-tabs-three-parting") {
                loadParting();

            } else if (window.location.hash.substr(1) == "custom-tabs-three-marinasi") {
                loadMarinasi();

            } else if (window.location.hash.substr(1) == "custom-tabs-three-whole") {
                loadWhole();

            } else if (window.location.hash.substr(1) == "custom-tabs-three-evis") {
                loadEvis();

            } else if (window.location.hash.substr(1) == "custom-tabs-three-nonlb") {
                loadNonLB();
            }  else {
                loadStockAll();
            }
        }

        reloadFG();



        // -------------------------------------------------------------------------------------

        
        $('#custom-tabs-three-stock-tab').on('click', function() {
            loadStockAll();
        })

        $('#custom-tabs-three-boneless-tab').on('click', function() {
            loadBoneless();
        })

        $('#custom-tabs-three-parting-tab').on('click', function() {
            loadParting();
        })

        $('#custom-tabs-three-marinasi-tab').on('click', function() {
            loadMarinasi();
        })

        $('#custom-tabs-three-whole-tab').on('click', function() {
            loadWhole();
        })

        $('#custom-tabs-three-evis-tab').on('click', function(){
            loadEvis();
        })

        $('#custom-tabs-three-nonlb-tab').on('click', function() {
            loadNonLB();
        })

        // -------------------------------------------------------------------------------------

        function loadStockAll() {
            
            $(".loading-stockall").attr('style', 'display: block');
            $('#stockall').load("{{ route('hasilproduksi.index') }}?tanggal="+tanggal + "&tanggalakhir=" +tanggalakhir + "&kosong=" + kosong + "&lokasi="+lokasi+ "&key=stockall", function() {
                $(".loading-stockall").attr('style', 'display: none');
            }) ;
        }

        function loadBoneless() {

            $(".loading-boneless").attr('style', 'display: block');
            $('#boneless').load("{{ route('hasilproduksi.index') }}?tanggal="+tanggal + "&tanggalakhir=" +tanggalakhir + "&kosong=" + kosong + "&key=boneless", function() {
                $(".loading-boneless").attr('style', 'display: none');
            }) ;
        }

        function loadParting() {
            

            $(".loading-parting").attr('style', 'display: block');
            $('#parting').load("{{ route('hasilproduksi.index') }}?tanggal="+tanggal + "&tanggalakhir=" +tanggalakhir + "&kosong=" + kosong + "&key=parting", function() {
                $(".loading-parting").attr('style', 'display: none');
            }) ;
        }

        function loadMarinasi() {
            
            $(".loading-marinasi").attr('style', 'display: block');
            $('#marinasi').load("{{ route('hasilproduksi.index') }}?tanggal="+tanggal + "&tanggalakhir=" +tanggalakhir + "&kosong=" + kosong + "&key=marinasi", function() {
                $(".loading-marinasi").attr('style', 'display: none');
            }) ;
        }

        function loadWhole() {
            
            $(".loading-whole").attr('style', 'display: block');
            $('#whole').load("{{ route('hasilproduksi.index') }}?tanggal="+tanggal + "&tanggalakhir=" +tanggalakhir + "&kosong=" + kosong + "&key=whole", function() {
                $(".loading-whole").attr('style', 'display: none');
            }) ;
        }

        function loadEvis() {
            

            $(".loading-evis").attr('style', 'display: block');
            $('#evis').load("{{ route('hasilproduksi.index') }}?tanggal="+tanggal + "&tanggalakhir=" +tanggalakhir + "&kosong=" + kosong + "&key=evis", function() {
                $(".loading-evis").attr('style', 'display: none');
            }) ;
        }

        function loadNonLB() {

            $(".loading-nonlb").attr('style', 'display: block');
            $('#chiller-nonlb').load("{{ route('hasilproduksi.nonlb') }}?tanggal="+tanggal + "&tanggalakhir=" +tanggalakhir, function() {
                $(".loading-nonlb").attr('style', 'display: none');
            }) ;
        }

        // -------------------------------------------------------------------------------------

</script>

@stop
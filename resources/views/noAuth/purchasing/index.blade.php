@extends('admin.layout.template-no-auth')

@section('title', 'Purchasing')

@section('content')
<div class="text-center my-4 text-uppercase"><b>PURCHASING</b></div>
<section class="panel">
    <div class="card card-primary card-outline card-tabs">
        <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-lpah-tab" data-toggle="pill" href="#custom-tabs-lpah" role="tab" aria-controls="custom-tabs-lpah" aria-selected="true">Progress LPAH</a>
            </li>
            <li class="nav-item">
                <a class="nav-link tab-link" id="custom-tabs-sebaranlb-tab" data-toggle="pill" href="#custom-tabs-sebaranlb" role="tab" aria-controls="custom-tabs-sebaranlb" aria-selected="true">Sebaran LB</a>
            </li>
        </ul>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-tabContent">
                <div class="tab-pane fade " id="custom-tabs-lpah" role="tabpanel" aria-labelledby="custom-tabs-lpah-tab">
                    @include('noAuth.purchasing.component.lpah.index')
                </div>
                <div class="tab-pane fade " id="custom-tabs-sebaranlb" role="tabpanel" aria-labelledby="custom-tabs-sebaranlb-tab">
                    @include('noAuth.purchasing.component.sebaran_lb.index')
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function(){

        var hash = window.location.hash.substr(1);
        var href = window.location.href;

        defaultPage();

        function defaultPage() {
            if (hash == undefined || hash == "") {
                hash = "custom-tabs-lpah";
            }

            $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');
        }

        $('.tab-link').click(function(e) {
            e.preventDefault();
            status                  = $(this).attr('aria-controls');
            window.location.hash    = status;
            href                    = window.location.href;

        });

        if(hash === "custom-tabs-lpah"){
            loaddatapurchasing();  
        }

        $("#custom-tabs-lpah-tab").on('click', function(){
            loaddatapurchasing()
        });

        $("#custom-tabs-sebaranlb-tab").on('click', function(){
            loadGraphicAllSupplierLb()
        });
    })

</script>
@stop

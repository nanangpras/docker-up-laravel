@extends('admin.layout.template-no-auth')

@section('title', 'Marketing')

@section('content')
<div class="my-4 text-center"><b>MARKETING</b></div>

<section class="panel">
    <div class="row">
        <div class="col-12 col-sm-12">
            <div class="card card-primary card-outline card-tabs">
                <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-fullfilment-tab" data-toggle="pill"
                            href="#custom-tabs-fullfilment" role="tab" aria-controls="custom-tabs-fullfilment"
                            aria-selected="true">Orders Fullfilment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="custom-tabs-stockbyitem-tab" data-toggle="pill"
                            href="#custom-tabs-stockbyitem" role="tab" aria-controls="custom-tabs-stockbyitem"
                            aria-selected="true">Stock By Item</a>
                    </li>
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-fullfilment" role="tabpanel"
                            aria-labelledby="custom-tabs-fullfilment-tab">
                            @include('noAuth.marketing.component.fulfillment.index')
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-stockbyitem" role="tabpanel"
                            aria-labelledby="custom-tabs-stockbyitem-tab">
                            @include('noAuth.marketing.component.soh.index')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $('.select2').select2({
            theme: 'bootstrap4',
        })

        var hash = window.location.hash.substr(1);
        var href = window.location.href;

        defaultPage();

        function defaultPage() {
            if (hash == undefined || hash == "") {
                hash = "custom-tabs-fullfilment";
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

        if(hash === "custom-tabs-fullfilment"){
            loaddatamarketing()
        }else
        if(hash === "custom-tabs-stockbyitem"){
            loaddatastockbyitem()
        }

        $("#custom-tabs-fullfilment-tab").on('click', function(){
            loaddatamarketing()
        });
        $("#custom-tabs-stockbyitem-tab").on('click', function(){
            loaddatastockbyitem()
        });
</script>

@stop

<div class="" id="topbar-wrapper">
    <a href="javascript:void(0)" class="btn btn-neutral" id="mobile-menu">Menu <span class="fa fa-bars"></span></a>
    <div class="list-group list-group-flush">

        <ul class="nav topbar-column">

            <li class="nav-item">
                <a class="nav-link @if(Request::segment(2)=='laporan-dashboard') {{'active'}} @endif" href="{{route('cloud.report.dashboard')}}">
                    <img src="{{asset('Icons/dashboard.png')}}" class="img-responsive topbar-icon-png" width="40" >
                    Dashboard <span class="sr-only">(current)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::segment(2)=='laporan-produksi') {{'active'}} @endif" href="{{route('cloud.report.produksi')}}">
                    <img src="{{asset('Icons/hasil-produksi.png')}}" class="img-responsive topbar-icon-png" width="40" >
                    Produksi <span class="sr-only">(current)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Request::segment(2)=='laporan-produksi') @endif" href="{{route('cloud.report.produksi')}}">
                    <img src="{{asset('Icons/purchasing.png')}}" class="img-responsive topbar-icon-png" width="40" >
                    Sales Order <span class="sr-only">(current)</span>
                </a>
            </li>
            <li class="nav-item"><a href="javascript:void(0)" class="toggle-custom nav-link" id="btn-8" data-toggle="collapse" data-target="#submenu8" aria-expanded="false">
                <img src="{{asset('Icons/netsuite.png')}}" class="img-responsive topbar-icon-png" width="40">
                Netsuite
                <span class="fa fa-chevron-down pull-right" aria-hidden="true"></span></a>
                <ul class="collapse sub-menu" id="submenu8" role="menu" aria-labelledby="btn-6">

                    <li class="nav-sub-item"><a href="{{url('/report/netsuite')}}" class="@if(Request::segment(2)=='logs')
                            {{'active'}}
                            @endif">NS to Apps</a>
                    </li>

                </ul>
            </li>

        </ul>

    </div>
  </div>

<style>
    img.topbar-icon-png{
        float: left;
        margin-right: 15px;
        width: 20px;
    }

    .toggle-custom[aria-expanded='true'] .fa-chevron-down:before {
        content: "\f077";
    }
</style>

<script>
    $('.toggle-custom').on('click', function(){
        $('.toggle-custom').siblings('.sub-menu').collapse('hide');
    })


    $("#mobile-menu").click(function() {
        if($("#top-navbar").css("left") == "0px"){
            $("#top-navbar").animate({"left": "165px"},"fast");
            $("#mobile-menu").html('Close <span class="fa fa-close"></span>')
        }
        else{
            $("#top-navbar").animate({"left": "0px"},"fast");
            $("#mobile-menu").html('Menu <span class="fa fa-bars"></span>')
            $('.toggle-custom').siblings('.sub-menu').collapse('hide');
        }
    });

</script>

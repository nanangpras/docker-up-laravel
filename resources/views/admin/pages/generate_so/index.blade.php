@extends('admin.layout.template')

@section('title', 'Buat Sales Order')

@section('content')
    <div class="my-4 text-center font-weight-bold text-uppercase">Buat Sales Order</div>
    <a href="https://docs.google.com/forms/d/e/1FAIpQLSdXK6lpF8HSNkgQQH6haUHXE2vIhtSjeu2MD-SnBVQf4tl0Bg/viewform" class="btn btn-blue mb-2" target="_blank">Buat SO Non Ayam</a>
    <hr>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link tab-link" id="input-tab" data-toggle="tab" href="#input" role="tab" aria-controls="input" aria-selected="true">Input</a>
        </li>
        @if (User::setijin(59))
            @if (Session::get('subsidiary') == 'CGL')
            <li class="nav-item" role="presentation">
                <a class="nav-link tab-link" id="sampingan-tab" data-toggle="tab" href="#sampingan" role="tab" aria-controls="sampingan" aria-selected="true">Input SO Sampingan</a>
            </li>
            @else
            <li class="nav-item" role="presentation">
                <a class="nav-link tab-link" id="sampingan-tab" data-toggle="tab" href="#sampingan" role="tab" aria-controls="sampingan" aria-selected="true">Input SO Karyawan</a>
            </li>
            @endif
        @endif
        <li class="nav-item" role="presentation">
            <a class="nav-link tab-link" id="summary-tab" data-toggle="tab" href="#summary" role="tab" aria-controls="summary" aria-selected="false">Summary</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link tab-link" id="orderitem-tab" data-toggle="tab" href="#orderitem" role="tab" aria-controls="orderitem" aria-selected="false">Order Item</a>
        </li>
    </ul>

    <div class="tab-content mt-2">
        <div class="tab-pane fade" id="input" role="tabpanel" aria-labelledby="input-tab">
            @include('admin.pages.generate_so.component.input.create-so')
        </div>

        {{-- @if (Session::get('subsidiary') == 'CGL') --}}
        {{-- SAMPINGAN --}}
        <div class="tab-pane fade" id="sampingan" role="tabpanel" aria-labelledby="sampingan-tab">
            @include('admin.pages.generate_so.component.input.create-so-sampingan-excel')
        </div>
        {{-- @endif --}}
        {{-- END SAMPINGAN --}}

        <div class="tab-pane fade" id="summary" role="tabpanel" aria-labelledby="summary-tab">
            @include('admin.pages.generate_so.component.summary.summaries')
        </div>

        <div class="tab-pane fade" id="orderitem" role="tabpanel" aria-labelledby="orderitem-tab">
            @include('admin.pages.generate_so.component.parking.parking-order')
        </div>
    </div>
@endsection

@section('footer')
    <script>
        var hash = window.location.hash.substr(1);
        var href = window.location.href;

        defaultPage();
        function defaultPage() {
            if (hash == undefined || hash == "") {
                hash = "input";
            }
            $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
            $('#' + hash).addClass('active show').siblings().removeClass('active show');
        }

        $('.tab-link').click(function(e) {
            e.preventDefault();
            status                  = $(this).attr('aria-controls');
            window.location.hash    = status;
            href                    = window.location.href;
            hash                    = window.location.hash.substr(1);
        });

        if(hash === 'summary'){
            loadsummarySO()
            loadcustomerSO()
            loadmarketingSO()
        }
        else if(hash === 'orderitem'){
            loadParkingOrders()
        }
        $("#summary-tab").on('click', function(){
            loadsummarySO()
            loadcustomerSO()
            loadmarketingSO()
        });

        $("#sampingan-tab").on('click', function(){
            //
        });

        $("#orderitem-tab").on('click', function(){
            loadParkingOrders()
        });
    </script>
@stop

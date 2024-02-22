@extends('admin.layout.template')

@section('title', 'Daftar User')

@section('content')

    <section class="panel">
        <div class="row">
            <div class="col-12 col-sm-12">
                <ul class="nav nav-tabs" id="custom-tabs-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="tabs-useradmin-tab" data-toggle="pill" href="#tabs-useradmin" role="tab" aria-controls="tabs-useradmin" aria-selected="true">User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="tabs-logadmin-tab" data-toggle="pill" href="#tabs-logadmin" role="tab" aria-controls="tabs-logadmin" aria-selected="true">Log</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="tabs-cutoff-tab" data-toggle="pill" href="#tabs-cutoff" role="tab" aria-controls="tabs-cutoff" aria-selected="true">Cut Off</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="tabs-opencutoff-tab" data-toggle="pill" href="#tabs-opencutoff" role="tab" aria-controls="tabs-opencutoff" aria-selected="true">Open Cut Off</a>
                    </li>
                    @if(User::setIjin('superadmin'))
                    <li class="nav-item">
                        <a class="nav-link tab-link" id="tabs-dataminor-tab" data-toggle="pill" href="#tabs-dataminor" role="tab" aria-controls="tabs-dataminor" aria-selected="true">Perbaiki Data</a>
                    </li>
                    @endif
                </ul>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-tabContent">
                        <div class="tab-pane fade active show" id="tabs-useradmin" role="tabpanel" aria-labelledby="tabs-useradmin">
                            @include('admin.pages.users-data')
                        </div>
                        <div class="tab-pane fade" id="tabs-logadmin" role="tabpanel" aria-labelledby="tabs-logadmin">
                            @include('admin.pages.log.log-admin-all')
                        </div>
                        <div class="tab-pane fade" id="tabs-cutoff" role="tabpanel" aria-labelledby="tabs-cutoff">
                            @include('admin.pages.setting.cutoff.cutoff')
                        </div>
                        <div class="tab-pane fade" id="tabs-opencutoff" role="tabpanel" aria-labelledby="tabs-opencutoff">
                            @include('admin.pages.setting.cutoff.opencutoff')
                        </div>
                        @if(User::setIjin('superadmin'))
                        <div class="tab-pane fade" id="tabs-dataminor" role="tabpanel" aria-labelledby="tabs-dataminor">
                            @include('admin.pages.setting.minor.dataminor')
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .default-table pre{
            font-family: sans-serif;
            line-height: 18pt;
            background: #fff;
            border: 1px solid #f9f9f9;

        }

        .table-outer{
            overflow: auto;
        }

        .table-inner{
            width: 100%;
        }
    </style>

    @if(!empty(Session::get('status')) && Session::get('status') == 1)
        <script>
            showNotif("{{Session::get('message')}}");
        </script>
    @endif

    @if(!empty(Session::get('status')) && Session::get('status') == 2)
        <script>
            showAlert("{{Session::get('message')}}");
        </script>
    @endif

    <script>
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        var hash = window.location.hash.substr(1);
        var href = window.location.href;
        defaultPage();
    
        function defaultPage() {
            
            if (hash == undefined || hash == "") {
                hash = "tabs-useradmin";
            }
            // console.log(hash)
    
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

@stop

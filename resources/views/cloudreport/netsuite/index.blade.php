@extends('cloudreport.template')

@section('title', 'NETSUITE')

@section('content')

<section class="panel">
    <div class="card-body">

        <div class="row mb-2">
            <div class="col">
                <input id="mulai" type="date"  @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif  name="mulai" value="{{$mulai}}" class="change-date mb-1 form-control">
            </div>
            <div class="col">
                <input id="sampai" type="date"  @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif name="sampai"  value="{{$sampai}}"  class="change-date mb-1 form-control">
            </div>
            <div class="col">
                <input id="search" type="text" name="search"  value="{{$search ?? ''}}" placeholder="Search"  class="global-search mb-1 form-control">
                <input id="page" type="hidden" name="page"  value="{{$page ?? ''}}" class="change-date hidden">
            </div>
        </div>
        <button name="type" class="btn btn-primary btn-sm select-type mb-1"  value="">Semua</button>
        <button name="type" class="btn btn-primary btn-sm select-type mb-1" value="purchase-order">Purchase Order</button>
        <button name="type" class="btn btn-primary btn-sm select-type mb-1" value="po-item-receipt">PO Fulfillment</button>
        <button name="type" class="btn btn-primary btn-sm select-type mb-1"  value="sales-order">Sales Order</button>
        <button name="type" class="btn btn-primary btn-sm select-type mb-1"  value="location">Location</button>
        <button name="type" class="btn btn-primary btn-sm select-type mb-1"  value="bom">BOM</button>
        <button name="type" class="btn btn-primary btn-sm select-type mb-1"  value="vendor">Vendor</button>
        <button name="type" class="btn btn-primary btn-sm select-type mb-1"  value="customer">Customer</button>
        <button name="type" class="btn btn-primary btn-sm select-type mb-1"  value="item">Item</button>
        <button name="type" class="btn btn-danger btn-sm reload mb-1"  value=""><span class="fa fa-refresh"></span>&nbsp Refresh</button>
        <hr>
        <div class="table-responsive">
            <div id="ns-loading" class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
                <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
            </div>
            <div id="list-table">
                
            </div>
        </div>

    </div>
</section>

    <script>
        var url = "";
        var mulai = $('#mulai').val();
        var sampai = $('#sampai').val();
        var search = $('#search').val();
        var type = "{{$type ?? ""}}";
        var page = "{{$page ?? ""}}";
        loadDataNS();

        $('.select-type').on('click', function(){
            type = $(this).val();
            loadDataNS();
        })
        $('.change-date').on('change', function(){
            loadDataNS();
        })
        $('.global-search').on('keyup', function(){
            loadDataNS();
        })

        $('.reload').on('click', function(){
            $('#list-table').load(url)
            loadDataNS();
            showNotif('Data reloaded')
        })

        function loadDataNS(){
            $('#ns-loading').show();
            mulai = $('#mulai').val();
            sampai = $('#sampai').val();
            search = encodeURIComponent($('#search').val());
            page = $('#page').val();
            url = "{{url('report/netsuite/list?type=')}}"+type+"&mulai="+mulai+"&sampai="+sampai+"&search="+encodeURIComponent(search)+"&page="+page;
            url_page = "{{url('report/netsuite?type=')}}"+type+"&mulai="+mulai+"&sampai="+sampai+"&search="+encodeURIComponent(search)+"&page="+page;
            console.log(url);
            window.history.pushState('Netsuite', 'Netsuite', url_page);
            
            $('#list-table').load(url, function(){
                $('#ns-loading').hide();
            })
        }


    </script>

    @stop
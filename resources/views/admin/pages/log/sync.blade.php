@extends('admin.layout.template')

@section('title', 'Data Logs')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('dashboard') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col text-center">
        <b>Data Logs</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <a href="javascript:void(0)" class="btn btn-blue" id="button-sync">Sync Now</a><br><br>
        <div id="sync-show"></div>
    </div>
</section>

<script>
    $("#sync-show").load("{{ route('sync.show') }}");

    setInterval(function(){
        console.log('Reload data '+Date.now())
        $("#sync-show").load("{{ route('sync.show') }}");
    }, 10000)

    $('#button-sync').on('click', function(){

        console.log('connecting to sync '+Date.now())
        $.ajax(
        {
            type: 'GET',
            url: "{{url('admin/sync-process')}}",
            beforeSend: function(){
                $('#button-sync').html('Loading ...');
            },
            success: function(data)
            {
                console.log(data.length);

                setTimeout(function(){
                    if(data.length=="0"){
                        showNotif('Sync Selesai tidak ada data baru');
                    }else{
                        showNotif('Sync Selesai '+ data.length + " Telah update");
                        $("#sync-show").load("{{ route('sync.show') }}");
                    }
                    $('#button-sync').html('Sync Now');
                }, 5000)

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log('Error sync status')
                $(this).html('Sync Again');
            }
        })
        
    })
</script>

<style>
.date-notif{
    font-size: 7pt;
    color: #999999
}
</style>

@stop

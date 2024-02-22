@extends('cloudreport.template')

@section('title', 'NETSUITE')

@section('content')

<div class="col">
    <a href="" class="btn btn-outline btn-sm btn-back"> <i
            class="fa fa-arrow-left"></i>
        Back</a>
</div>

<section class="panel">
    <div class="card-body">
        <pre>
        <div id="myDiv"></div>
        </pre>
    </div>
    <textarea style="display: none" id="datajson">{{$json}}</textarea>
</section>


<script>
    var jsonString = $('#datajson').val();
    var jsonPretty = JSON.stringify(JSON.parse(jsonString),null,'\t');  
    $('#myDiv').html(jsonPretty);
</script>

@stop
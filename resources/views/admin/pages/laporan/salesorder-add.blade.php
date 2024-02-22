@extends('admin.layout.template')

@section('title', 'Laporan Sales Order')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('salesorder.laporan') }}" class="btn btn-outline btn-sm btn-back"> <i
                class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Tambah Sales Order</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <h5>Upload SO (Untuk Meyer Global)</h5>
        <hr>
        <form method="POST" action="{{url('admin/upload-so-excel-meyer-global')}}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col">
                    <input type="file" name="file">
                </div>
                <div class="col">
                    <select class="btn btn-neutral" name="customer">
                        <option value="MEYER PROTEINDO PRAKARSA. PT" selected>MEYER PROTEINDO PRAKARSA. PT</option>
                    </select>
                </div>
                <div class="col">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggal" class="form-control" value="{{date('Y-m-d')}}">
                </div>
                <div class="col">
                    <button class="btn btn-blue">Convert</button>
                </div>
            </div>
        </form>
    </div>
    <hr>
</section>

<section class="panel">
    <div class="card-body">
        <h5>Upload SO (Untuk Meyer per Partner)</h5>
        <hr>
        <form method="POST" action="{{url('admin/upload-so-excel')}}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file">
            <select class="btn btn-neutral" name="customer">
                <option value="MEYER PROTEINDO PRAKARSA. PT" selected>MEYER PROTEINDO PRAKARSA. PT</option>
                <option value="sampingan">SAMPINGAN</option>
                <option value="socgl">CUSTOMER CGL</option>
            </select>
            <button class="btn btn-blue">Save</button>
        </form>
    </div>
    <hr>
</section>

{{-- <section class="panel">
    <div class="card-body">
        <h5>Upload Line ID Netsuite</h5>
        <hr>
        <form method="POST" action="{{url('admin/upload_line_idso')}}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file">
            <button class="btn btn-blue">Save</button>
        </form>
    </div>
    <hr>
</section> --}}


@if(!empty(Illuminate\Support\Facades\Session::get('data')))
<section class="panel">
    <div class="card-body">
        <h5>Logs Import</h5>
        <hr>
        @foreach($Session::get('data') as $row)
        <li>{{$row[1]}} - {{$row[3]}} - {{$row[4]}}</li>
        @endforeach
    </div>
</section>
@endif



@stop
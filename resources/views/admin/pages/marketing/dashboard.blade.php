@extends('admin.layout.template')

@section('title', 'Marketing Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="" class="btn btn-outline-dark btn-sm"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Marketing Dashboard</b>
    </div>
    <div class="col"></div>
</div>

@php
    $total  =   0 ;
@endphp
@foreach ($data as $row)
@php
    $total  += 1 ;
@endphp
    <div class="border-bottom p-2">
        {{ $row->tanggal_produksi }} |
        {{ $row->id }} |
        {{ $row->plastik_nama }} =
        {{ $row->plastik_sku }}

        @php
            $update                 =   App\Models\FreestockTemp::find($row->id) ;
            
            $update->plastik_sku    =   App\Models\Item::where('nama', $row->plastik_nama)->where('subsidiary', env('NET_SUBSIDIARY', 'EBA'))->first()->sku ?? '0000000000000' ;
            $update->save() ;
        @endphp


    </div>
@endforeach

{{ $total }}
@stop

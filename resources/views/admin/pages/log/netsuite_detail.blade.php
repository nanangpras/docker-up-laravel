@extends('admin.layout.template')

@section('title', 'Detail Data Netsuite')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('sync.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col text-center">
        <b>Detail Data Netsuite</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">

        @php 

            $sub = App\models\Netsuite::where('id', $data->id)->with('data_children')->first();    // function loop_child($data_child){
                
        @endphp
        
        <h6>Data Detail</h6>
        
        <table class="table default-table">
            <thead>
                <tr>
                    <th>
                            <input type="checkbox" id="ns-checkall">
                        </th>
                        <th>ID</th>
                        <th>C&U Date</th>
                        <th>TransDate</th>
                        <th>Label</th>
                        <th>Activity</th>
                        <th>Location</th>
                        <th>IntID</th>
                        <th>Paket</th>
                        <th width="100px">Data</th>
                        <th width="100px">Action</th>
                        <th>Response</th>
                        <th>Status</th>
                </tr>
            </thead>
            <tbody>
                
                @php 
                    $netsuite = $sub;
                @endphp
                @if($netsuite ?? false)
                @include('admin.pages.log.netsuite_one', ($netsuite = $sub))
                @endif

                {{-- @if($netsuite->data_children ?? false)
                @include('admin.pages.log.netsuite_one', ($netsuite = $netsuite->data_children))
                @php 
                    $sub2 = App\models\Netsuite::where('id', $netsuite->data_children->id ?? null)->with('data_children')->first();    // function loop_child($data_child){
                @endphp

                    @if($sub2 ?? false)
                    @include('admin.pages.log.netsuite_one', ($netsuite = $sub2))
                    @endif
                    @if($sub2->data_children ?? false)
                    @include('admin.pages.log.netsuite_one', ($netsuite = $sub2->data_children))
                    @endif
                @endif --}}

                

            </tbody>
        </table>

         <h6>Related Record</h6>

         <form method="post" action="{{route('sync.cancel')}}">
        @csrf
        <br>
        <button type="submit" class="btn btn-blue mb-1" name="status" value="approve">Approve Integrasi</button> &nbsp
        <button type="submit" class="btn btn-red mb-1" name="status" value="cancel">Batalkan Integrasi</button> &nbsp
        <button type="submit" class="btn btn-info mb-1" name="status" value="retry">Kirim Ulang</button> &nbsp
        <button type="submit" class="btn btn-success mb-1" name="status" value="completed">Selesaikan</button> &nbsp
        <button type="submit" class="btn btn-warning mb-1" name="status" value="hold">Hold</button> &nbsp
        <hr>
        
        <table class="table default-table">
            <thead>
                <tr>
                    <th>
                            <input type="checkbox" id="ns-checkall">
                    </th>
                    <th>ID</th>
                    <th>C&U Date</th>
                    <th>TransDate</th>
                    <th>Label</th>
                    <th>Activity</th>
                    <th>Location</th>
                    <th>IntID</th>
                    <th>Paket</th>
                    <th width="100px">Data</th>
                    <th width="100px">Action</th>
                    <th>Response</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                
                @if($netsuite->document_code!="")
                    @php 
                        $ns_related = App\Models\Netsuite::where('document_code', $netsuite->document_code)->get();
                    @endphp

                    @foreach($ns_related as $r)
                        @include('admin.pages.log.netsuite_one', ($netsuite = $r))
                    @endforeach
                @endif

                @if($netsuite->document_code=="")
                    @if($netsuite->tabel_id!="")
                        @php 
                            $ns_related = App\Models\Netsuite::where('tabel_id', $netsuite->tabel_id)->where('tabel', $netsuite->tabel)->get();
                        @endphp

                        @foreach($ns_related as $r)
                            @include('admin.pages.log.netsuite_one', ($netsuite = $r))
                        @endforeach
                    @endif
                @endif

            </tbody>
        </table>

         </form>
    </div>
</section>



@endsection

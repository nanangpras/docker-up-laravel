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
    @foreach($logs as $log)
        <div style="padding: 5px; border: 1px solid #f1f1f1; margin: 3px;">
            @if($log->activity=="create")
                @if($log->table_name=="purchasing")
                    <a href="{{ route('purchasing.show', $log->table_id) }}" target="_blank"> #{{$log->id}}</a>
                    {{$log->table_name}} baru dari netsuite. <br><span class="date-notif">{{date('d/m/y H:i:s', strtotime($log->created_at))}}</span>
                @endif
            @endif

            @if($log->activity=="update")
                @if($log->table_name=="productions")
                    @if($log->label=="update-security")
                        @php 
                            $prodpur = \App\Models\Production::find($log->table_id);
                        @endphp
                        #{{$log->id}} Truk Masuk | {{$prodpur->sc_pengemudi}} | {{$prodpur->sc_wilayah}} <br><span class="date-notif">{{date('d/m/y H:i:s', strtotime($log->created_at))}}</span>
                    @endif

                    @if($log->label=="lpah-proses")
                        @php 
                            $prodpur = \App\Models\Production::find($log->table_id);
                        @endphp
                        #{{$log->id}} LPAH diproses | {{$prodpur->sc_pengemudi}} | {{$prodpur->sc_wilayah}} <br><span class="date-notif">{{date('d/m/y H:i:s', strtotime($log->created_at))}}</span>
                    @endif

                    @if($log->label=="lpah-selesai")
                        @php 
                            $prodpur = \App\Models\Production::find($log->table_id);
                        @endphp
                        #{{$log->id}} LPAH selesai | {{$prodpur->sc_pengemudi}} | {{$prodpur->sc_wilayah}} <br><span class="date-notif">{{date('d/m/y H:i:s', strtotime($log->created_at))}}</span>
                    @endif

                    @if($log->label=="grading-proses")
                        @php 
                            $prodpur = \App\Models\Production::find($log->table_id);
                        @endphp
                        #{{$log->id}} Grading diproses | {{$prodpur->sc_pengemudi}} | {{$prodpur->sc_wilayah}} <br><span class="date-notif">{{date('d/m/y H:i:s', strtotime($log->created_at))}}</span>
                    @endif

                    @if($log->label=="grading-selesai")
                        @php 
                            $prodpur = \App\Models\Production::find($log->table_id);
                        @endphp
                        #{{$log->id}} Grading selesai | {{$prodpur->sc_pengemudi}} | {{$prodpur->sc_wilayah}} <br><span class="date-notif">{{date('d/m/y H:i:s', strtotime($log->created_at))}}</span>
                    @endif

                    @if($log->label=="evis-proses")
                        @php 
                            $prodpur = \App\Models\Production::find($log->table_id);
                        @endphp
                        #{{$log->id}} Evis diproses | {{$prodpur->sc_pengemudi}} | {{$prodpur->sc_wilayah}} <br><span class="date-notif">{{date('d/m/y H:i:s', strtotime($log->created_at))}}</span>
                    @endif

                    @if($log->label=="evis-selesai")
                        @php 
                            $prodpur = \App\Models\Production::find($log->table_id);
                        @endphp
                        #{{$log->id}} Evis selesai | {{$prodpur->sc_pengemudi}} | {{$prodpur->sc_wilayah}} <br><span class="date-notif">{{date('d/m/y H:i:s', strtotime($log->created_at))}}</span>
                    @endif

                @endif
            @endif
        </div>
    @endforeach
</section>

<style>
.date-notif{
    font-size: 7pt;
    color: #999999
}
</style>

@stop

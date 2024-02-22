@extends('admin.layout.template')

@section('title', 'Dashboard Regu ', $regu)

@section('content')

<div class="row mb-4">
    <div class="col">
        <a href="{{ route('regu.index', ['kategori' => $kategori]) }}" class="btn btn-outline btn-sm btn-back"><i
                class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col-10 text-center">
        <b>Dashboard Regu {{ $regu }}</b>
    </div>
    <div class="col"></div>
</div>


<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control change-date mb-2" id="pencarian_awal" value="{{ $tanggal_awal }}"
                    placeholder="Cari...">
            </div>
            <div class="col-md-4" id="panel-tanggal-akhir">
                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                    @endif class="form-control change-date mb-2" id="pencarian_akhir" value="{{ $tanggal_akhir }}"
                    placeholder="Cari...">
            </div>
        </div>
        <div id="dashboard-regu" style="height: 30px" hidden>
            <div class="text-center mb-2 mt-2" style="position: absolute; left: 0; right: 0;">
                <img src="{{asset('loading.gif')}}" style="width: 20px"> Loading ...
            </div>
        </div>
    </div>
    <div id="loadDashboardRegu">
    </div>

</section>
@endsection

@section('footer')

<script>
    var tanggal_awal    =   $("#pencarian_awal").val();
    var tanggal_akhir   =   $("#pencarian_akhir").val();

    $('#pencarian_awal').on('change', function() {

        $('#dashboard-regu').show()
            tanggal_awal    =   $(this).val();
            tanggal_akhir   =   $("#pencarian_akhir").val();
        setTimeout(function(){
            loadDashboard();
        },200);
    })

    $('#pencarian_akhir').on('change', function() {
        $('#dashboard-regu').show()
        tanggal_awal    =   $("#pencarian_awal").val();
        tanggal_akhir   =   $(this).val();
        setTimeout(function(){
            loadDashboard();
        },200);
    })
    
    loadDashboard()

    function loadDashboard() {
        const regu = encodeURIComponent("{{ $regu }}")
        // console.log("{{ route('regu.index', ['key' => 'dashboardregu']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&regu=" + regu)
        $('#dashboard-regu').attr('hidden',false)
        // console.log(regu)
        $("#loadDashboardRegu").html('');
        $("#loadDashboardRegu").load("{{ route('regu.index', ['key' => 'dashboardregu']) }}&tanggal_awal=" + tanggal_awal + "&tanggal_akhir=" + tanggal_akhir + "&regu=" + regu + "&view=loadDashboardRegu", function() {
            $('#dashboard-regu').attr('hidden',true)
        })
    }

</script>
@endsection
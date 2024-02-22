<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="tanggal_mulai_pindah_gudang">Filter</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_mulai_pindah_gudang"
                value="{{ date('Y-m-d', strtotime('-7 days', time())) }}">
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            <label for="tanggal_akhir_pindah_gudang">&nbsp;</label>
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif class="form-control" id="tanggal_akhir_pindah_gudang" value="{{ date('Y-m-d')}}">
        </div>
    </div>
</div>
<hr>
<br>
<h5 id="loading-pindah-gudang" style="display: none" class="text-center"><i class="fa fa-refresh fa-spin"></i>
    Loading....</h5>
<div id="result-pindah-gudang"></div>

<script>
    var hash = window.location.hash;
    if(hash === "summary-pindah-gudang"){
        loadSummaryPindah();
    }

    $("#summary-pindah-gudang-tab").on('click', function(){
        loadSummaryPindah();
    });

    $("#tanggal_mulai_pindah_gudang,#tanggal_akhir_pindah_gudang").on('change', function () {
        setTimeout(() =>{
            loadSummaryPindah();
        },500)
    });
    loadSummaryPindah();


    function loadSummaryPindah() {

        var tgl_mulai = $("#tanggal_mulai_pindah_gudang").val();
        var tgl_akhir = $("#tanggal_akhir_pindah_gudang").val();

        $.ajax({
            type: "GET",
            url: "{{ route('pindah.show')}}",
            data: {
                'key'       : 'summary_pindah_gudang',
                'tgl_awal'  : tgl_mulai,
                'tgl_akhir' : tgl_akhir
            },
            beforeSend: function (){
                $("#loading-pindah-gudang").show();
            },
            success: function (res) {
                $("#result-pindah-gudang").html(res);
                $("#loading-pindah-gudang").hide();
            }
        });
    }
</script>


<style>
    .table-sticky>thead>tr>th,
    .table-sticky>thead>tr>td {
        background: #009688;
        color: #fff;
        position: sticky;
    }

    .table-height {
        height: 800px;
        display: block;
        overflow: scroll;
        width: 100%;
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
    }

    .table-sticky thead {
        position: sticky;
        top: 0px;
        z-index: 1;
    }

    .table-sticky thead td {
        position: sticky;
        top: 0px;
        left: 0;
        z-index: 4;
        background-color: #f9fbfd;
        color: #95aac9;
    }

    .table-sticky tbody th {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 0;

    }

    /* .table-sticky tbody th {
    position: sticky;
    background-color: #95aac9;
    z-index: 0;
} */

    tbody th.stuck:nth-child(1) {
        left: 0px;
    }

    tbody th.stuck:nth-child(2) {
        left: 42px;
    }



    tbody th.stuck:nth-child(3) {
        left: 330px;
    }

    tbody th.stuck:nth-child(4) {
        left: 380px;
    }



    thead td.stuck:nth-child(1) {
        left: 0px;

    }

    thead td.stuck:nth-child(2) {
        left: 42px;

    }

    thead td.stuck:nth-child(3) {
        left: 330px;

    }

    thead td.stuck:nth-child(4) {
        left: 380px;

    }

    /* thead tr:nth-child(1) th {
    position: sticky; top: 0;
}
thead tr:nth-child(2) th {
    position: sticky; top: 40px;
} */

    /* .table-bordered>thead>tr>th,
.table-bordered>tbody>tr>th,
.table-bordered>thead>tr>td,
.table-bordered>tbody>tr>td {
 border: 1px solid #ddd;
} */
</style>
<header class="panel-heading">
    <h4 class="text-center">Log Admin</h4>
</header>
<div class="row">
    <div class="col">
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            name="mulai" id="filter_mulai" class="form-control"
            value="{{ date('Y-m-d', strtotime('-7 days', time())) }}">
    </div>
    <div class="col">
        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01" @endif
            name="akhir" id="filter_end" class="form-control" value="{{ date('Y-m-d') }}">
    </div>
</div>
<br>
<div id="view_filter"></div>

<div class="modal fade" id="logadmin" aria-labelledby="logadmin" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: 800px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Riwayat Log Admin</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idlog">

                <div id="content_riwayat"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@section('header')
<link rel="stylesheet" type="text/css" href="{{ asset('') }}plugin/DataTables/datatables.min.css" />
@stop
@section('footer')
<script type="text/javascript" src="{{ asset('') }}plugin/DataTables/datatables.min.js"></script>
<script>
    $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#tbl_logadmin')) {
                $('#tbl_logadmin').DataTable().destroy();
            }
            $('#tbl_logadmin').DataTable({
                "bPaginate": true,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "bAutoWidth": false
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
        loadLogAdmin();
        $("#filter_mulai,#filter_end").on("change", function () {
            // alert('ok');
            loadLogAdmin();
            
        });

        function loadLogAdmin() {
            var mulai = $("#filter_mulai").val();
            var akhir = $("#filter_end").val();
            $.ajax({
                type: "get",
                url: "{{route('users.index')}}",
                data: {
                    mulai : mulai,
                    akhir : akhir,
                    'key' : 'filter'
                },
                success: function (response) {
                    // console.log(response);
                  $("#view_filter").html(response);  
                }
            });
        }
</script>
<script>
    $(document).on('click', ".btnlogadmin", function() {
            var id = $(this).data('id');
            var url = $(this).attr('href');
            // alert(id);
            $.ajax({
                type: "get",
                url: url,
                // dataType: "json",
                data: {
                    id: id,
                    'key': 'detail'
                },
                success: function(response) {
                    console.log('adminedit',id);
                    $('#logadmin').modal('show');
                    $("#content_riwayat").html(response);
                    $("#idlog").val(id);
                }
            });
        });
</script>

@stop
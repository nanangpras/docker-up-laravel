@extends('admin.layout.template')

@section('title', 'Request Thawing')

@section('header')
<!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')
<!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script> -->
<script>
    $('.select2').select2({
            theme: 'bootstrap4',
        })

        var x = 1;
        function addRow(){
            var row = '';
            row +=  '<div class="row row-'+(x)+'">' ;
            row +=  '   <div class="col-2 pr-1">' ;
            row +=  '       <div class="form-group">' ;
            row +=  '           Item' ;
            row +=  '            <select name="item[]" class="form-control select2" data-placeholder="Pilih Item" data-width="100%">' ;
            row +=  '                <option value=""></option>' ;
            row +=  '                @foreach ($frozen as $row)' ;
            row +=  '                <option value="{{ $row->id }}">{{ $row->nama }}</option>' ;
            row +=  '                @endforeach' ;
            row +=  '            </select>' ;
            row +=  '        </div>' ;
            row +=  '    </div>' ;
            row +=  '    <div class="col-2 px-1">' ;
            row +=  '        <div class="form-group">' ;
            row +=  '            Tanggal' ;
            row +=  '            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL')=='CGL') min="2023-01-01" @endif name="tanggal_request[]" class="form-control" autocomplete="off" value="{{date("Y-m-d")}}">' ;
            row +=  '        </div>' ;
            row +=  '    </div>' ;
            row +=  '    <div class="col-2 px-1">' ;
            row +=  '        <div class="form-group">' ;
            row +=  '            Keterangan' ;
            row +=  '            <input type="text" name="keterangan[]" class="form-control" autocomplete="off" placeholder="keterangan">' ;
            row +=  '        </div>' ;
            row +=  '    </div>' ;
            row +=  '    <div class="col-2 px-1">' ;
            row +=  '        <div class="form-group">' ;
            row +=  '            Qty' ;
            row +=  '            <input type="number" name="qty[]" class="form-control" autocomplete="off"  placeholder="qty">' ;
            row +=  '        </div>' ;
            row +=  '    </div>' ;
            row +=  '    <div class="col-2 px-1">' ;
            row +=  '        <div class="form-group">' ;
            row +=  '            Berat' ;
            row +=  '            <input type="number" name="berat[]" step="0.01" class="form-control" autocomplete="off"  placeholder="berat">' ;
            row +=  '        </div>' ;
            row +=  '    </div>' ;
            row +=  '    <div class="col-2 pl-1">' ;
            row +=  '        <div class="form-group">' ;
            row +=  '            &nbsp;' ;
            row +=  '            <button type="button" class="btn btn-block btn-danger" onclick="deleteRow('+(x)+')"><i class="fa fa-trash"></i></button>' ;
            row +=  '        </div>' ;
            row +=  '    </div>' ;

            $('.data-loop').append(row);
            $('.select2').select2({
                theme: 'bootstrap4',
            })
            x++;
        }

        function deleteRow(rowid){
            $('.row-'+rowid).remove();
        }
</script>

{{-- <script>
    var url = "{{ route('thawing.requestthawing') }}";

        $('.pagination a').on('click', function(e) {
            e.preventDefault();
            url = $(this).attr('href');
            filterWarehouseRequestThawing();
        });

        $('#filter-form-submit-requestthawing').on('submit', function(e) {
            e.preventDefault();
            url = $(this).attr('action') + "?" + $(this).serialize();
            console.log(url);
            filterWarehouseRequestThawing();
        })

        $('.change-filter-requestthawing').on('change', function() {
            $('#filter-form-submit-requestthawing').submit();
            filterWarehouseRequestThawing();
        })

        $('#search-filter-requestthawing').on('keyup', function() {

            setTimeout(function() {
                $('#filter-form-submit-requestthawing').submit();
                filterWarehouseRequestThawing();
            }, 2000)

        })

        function filterWarehouseRequestThawing() {
            $.ajax({
                url: url,
                method: "GET",
                success: function(response) {
                    $('#warehouse-requestthawing').html(response);
                }

            });
        }

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#warehouseRequestThawing')) {
                $('#warehouseRequestThawing').DataTable().destroy();
            }
            $('#warehouseRequestThawing').DataTable({
                "bInfo": false,
                responsive: true,
                scrollY: 500,
                scrollX: true,
                scrollCollapse: true,
                paging: false,
            });

            $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
</script> --}}

<script>
    $('#warehouse-requestthawing').load("{{ route('thawing.requestthawing') }}");
    $("#mulai").on('change', function() {
        var mulai   =   $("#mulai").val() ;
        var sampai  =   $("#sampai").val() ;
        $('#warehouse-requestthawing').load("{{ route('thawing.requestthawing') }}?mulai=" + mulai + "&sampai=" + sampai);
    });

    $("#sampai").on('change', function() {
        var mulai   =   $("#mulai").val() ;
        var sampai  =   $("#sampai").val() ;
        $('#warehouse-requestthawing').load("{{ route('thawing.requestthawing') }}?mulai=" + mulai + "&sampai=" + sampai);
    });

    $(document).ready(function() {
        $("#thawingRequestForm").submit(function() {
            $(".spinerloading").hide();
            $(".submit").attr("disabled", true);
            // $(".btn").text("Processing ...");
            showNotif('menunggu...');
            $(".btn").hide();
        });
    });
</script>
@stop

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('regu.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back
        </a>
    </div>
    <div class="col text-center">
        <b class="text-capitalize">Request Thawing</b>
    </div>
    <div class="col text-right"></div>
</div>

<section class="panel">
    <div class="card-body p-2">
        <form action="{{ route('thawing.store') }}" method="post" id="thawingRequestForm">
            @csrf <input type="hidden" name="type" value="free">
            <div class="data-loop">
                <div class="row">
                    <div class="col-2 pr-1">
                        <div class="form-group">
                            Item
                            <select name="item[]" class="form-control select2" data-placeholder="Pilih Item"
                                data-width="100%">
                                <option value=""></option>
                                @foreach ($frozen as $row)
                                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-2 px-1">
                        <div class="form-group">
                            Tanggal
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif name="tanggal_request[]" class="form-control" value="{{ date("Y-m-d") }}">
                        </div>
                    </div>
                    <div class="col-2 px-1">
                        <div class="form-group">
                            Keterangan
                            <input type="text" name="keterangan[]" placeholder="KETERANGAN" class="form-control"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="col-2 px-1">
                        <div class="form-group">
                            Qty
                            <input type="number" name="qty[]" placeholder="QTY" class="form-control" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-2 px-1">
                        <div class="form-group">
                            Berat
                            <input type="number" name="berat[]" placeholder="Berat" step="0.01" class="form-control"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="col-2 pl-1">
                        <div class="form-group">
                            &nbsp;
                            <button type="button" class="btn btn-block btn-success" onclick="addRow()"><i
                                    class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group"> Tanggal <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' )
                    min="2023-01-01" @endif name="tanggal_thawing" class="form-control" value="{{ date("Y-m-d") }}">
            </div>
            <div class="form-group">
                Regu
                <select id="regu" name="regu" class="form-control">
                    <option value="">- Semua -</option>
                    <option value="byproduct">Byproduct</option>
                    <option value="parting">Parting</option>
                    <option value="whole">Whole</option>
                    <option value="marinasi">Marinasi</option>
                    <option value="boneless">Boneless</option>
                </select>
            </div>
            {{-- <i class="loading-icon fa-lg fas fa-spinner fa-spin hide"></i> --}}
            <i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i>
            <button type="submit" class="btn btn-block btn-primary mt-3">Request</button>
        </form>
    </div>
</section>

<section class="panel">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-6 mb-3">
                    <label for="mulai">Mulai</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control" id="mulai" value="{{ date("Y-m-d") }}">
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <label for="sampai">Sampai</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif class="form-control" id="sampai" value="{{ date("Y-m-d") }}">
                </div>
            </div>
            <div id="warehouse-requestthawing"></div>
        </div>
    </div>
</section>
@endsection
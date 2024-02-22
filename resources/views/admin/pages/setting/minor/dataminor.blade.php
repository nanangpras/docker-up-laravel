<div class="radio-toolbar row">
    <div class="col-lg-3 col-4">
        <div class="form-group">
            <input type="checkbox" name="destinationdata[]" class="destinationdata" id="kalulasigrading" value="gradinggabungan" >
            <label for="kalulasigrading">Grading</label>
        </div>
    </div>
    <div class="col-lg-3 col-4">
        <div class="form-group">
            <input type="checkbox" name="destinationdata[]" class="destinationdata" id="kalulasievis" value="evisgabungan" >
            <label for="kalulasievis">Evis</label>
        </div>
    </div>
    <div class="col-lg-3 col-4">
        <div class="form-group">
            <input type="checkbox" name="destinationdata[]" class="destinationdata" id="kalulasiretur" value="retur" >
            <label for="kalulasiretur">Retur</label>
        </div>
    </div>
    <div class="col-lg-3 col-4">
        <div class="form-group">
            <input type="checkbox" name="destinationdata[]" class="destinationdata" id="kalulasifreestock" value="free_stock" >
            <label for="kalulasifreestock">Free Stock</label>
        </div>
    </div>
    <div class="col-lg-3 col-4">
        <div class="form-group">
            <input type="checkbox" name="destinationdata[]" class="destinationdata" id="kalulasithawing" value="thawing" >
            <label for="kalulasithawing">Thawing</label>
        </div>
    </div>
    <div class="col-lg-3 col-4">
        <div class="form-group">
            <input type="checkbox" name="destinationdata[]" class="destinationdata" id="kalulasihasilbeli" value="hasilbeli" >
            <label for="kalulasihasilbeli">Hasil Beli</label>
        </div>
    </div>
    <div class="col-lg-3 col-4">
        <div class="form-group">
            <input type="checkbox" name="destinationdata[]" class="destinationdata" id="kalulasiabf" value="abf" >
            <label for="kalulasiabf">ABF</label>
        </div>
    </div>
</div>
<div class="mb-2">
    <button type="button" class="btn btn-danger btn-md injectRecalculate"> 
        <i class="fa fa-calculator iconcalculator"></i>
        <i class="fa fa-spinner fa-spin spinercalculate" style="display:none;"></i> 
        <span id="textdata">Kalkulasi Ulang</span>
    </button>
</div>
<section>
    <table class="table default-table table-bordered" id="tableDataJanggal" width="100%">
        <thead>
            <tr>
                <th> ID Chiller </th>
                <th> Nama Item </th>
                <th> Asal Tujuan </th>
                <th> Tanggal Produksi </th>
                <th> Sisa Stock </th>
            </tr>
        </thead>
    </table>
</section>

<link type="text/css" rel="stylesheet"  href="{{asset('')}}plugin/DataTables/datatables.min.css"/>
<script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script>
<script>

    let elem = [...document.querySelectorAll('.destinationdata')]
    elem.forEach(item => item.addEventListener('change', getChecked))

    function getChecked() {
        var getChex = elem.filter(item => item.checked).map(item => item.value)
        return getChex
    }
   
    $('.destinationdata').change(function() {
        var destinationdata     = [];
        var table               = $('#tableDataJanggal').DataTable()
        $('.destinationdata').each(function() {
            if ($(this).is(":checked")) {
                destinationdata.push($(this).val());
            }
        });
        table.ajax.reload(null,false);
    });

    $(document).ready(function() {
        $('#tableDataJanggal').DataTable({
            "bInfo"         : false,
            responsive      : true,
            scrollY         : 500,
            scrollX         : true,
            scrollCollapse  : true,
            paging          : false,
            searching       : false,
            processing      : true,
			serverSide      : true,
            sort            : false,
            ajax            : {
                url : "{{ route('injectRecalculate') }}",
                type: 'POST',
                data: {
                    'key'    : 'view',
                    'filter' : getChecked,
                    '_token' : "{{ csrf_token() }}"
                }
            }
        });

        $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });

        $(".injectRecalculate").click(function(){
            var table       = $('#tableDataJanggal').DataTable();
            var time        = new Date().getTime();
            $.ajax({
                url         : "{{ route('injectRecalculate') }}",
                method      : "POST",
                cache       : false,
                data        : {
                    'key'    : 'inject',
                    'filter' : getChecked,
                    "_token": "{{ csrf_token() }}"
                },
                beforeSend  :function(data){
                    $('.injectRecalculate').removeClass('btn-danger').addClass('btn-info');
                    $("#textdata").text("Sedang Diproses...");
                    $('.injectRecalculate').attr('disabled');
                    $(".iconcalculator").hide(); 
                    $(".spinercalculate").show(); 
                },
                success     : function(data){
                    setTimeout(() => {
                    $(".spinercalculate").hide();
                    $(".iconcalculator").show();
                    $('.injectRecalculate').attr('disabled');
                    $('.injectRecalculate').removeClass('btn-info').addClass('btn-success');
                    $('.iconcalculator').removeClass('fa-calculator').addClass('fa-check');
                        $("#textdata").text('Berhasil');
                        if (data.status == 200) {
                            showNotif(data.msg);
                        } else {
                            showAlert(data.msg);
                        }
                    }, 500);
                },
                complete:function(data){
                    setTimeout(() => {
                        table.ajax.reload( null, false);
                        $("#textdata").text('Kalkulasi Ulang');
                        $('.iconcalculator').removeClass('fa-check').addClass('fa-calculator');
                        $('.injectRecalculate').removeClass('btn-success').addClass('btn-danger');
                    }, 2000);
                },
                error: function(xhr) { 
                    if(xhr.status > 500){
                        showAlert(xhr.statusText);
                        setTimeout(() => {
                            refreshHalaman();
                        }, 1000);
                    }
                },
            })
        })

        function refreshHalaman() {
            window.location.reload(true);
        }

    });
</script>

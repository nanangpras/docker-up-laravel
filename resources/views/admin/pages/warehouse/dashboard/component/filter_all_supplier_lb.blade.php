
<div class="mt-5">
    <div style="overflow-x: none">
        <!-- <div style="width: 2000px"> -->
            <figure id="container-sebaran-karkas-all-supplier"></figure>
        <!-- </div> -->
    </div>
</div>
<div class="mb-4 ">
    <div class="card-body p-2">
        <div style="float: right;">
            <div class="row">
                <div class="col-6 mb-4" style="display: block;">
                    {!! $filter_supplier_lb !!}
                </div>
            </div>
        </div>
    </div>
    <div id="filterdetailsupplierlb"></div>
</div>
<script>
    $('.select2').select2({
        theme: 'bootstrap4'
    })

    var tanggal_awal                    = $("#tanggal_awal").val();
    var tanggal_akhir                   = $("#tanggal_akhir").val();
    var itemid                          = $("#all_ukuran_lb").val();
    var supplier                        = $("#suppliername").val();

    var hashdetail                      = window.location.hash;
    defaultPage();
    function defaultPage() {
        if (hashdetail == undefined || hashdetail == "") {
            reloaddetailsupplierlb(tanggal_awal,tanggal_akhir,itemid,supplier)
        }
    }
    $("#suppliername").on('change', function() {
        supplier                        = $("#suppliername").val();
        itemid                          = $("#all_ukuran_lb").val();
        $.ajax({
            url: "{{ route('warehouse_dash.filter_lb') }}",
            type: "GET",
            data: {
                'key'                   : 'view_page',
                'loadDetailSupplier'    : 'YES',
                'supplier'              : supplier,
                'itemid'                : itemid,
                'tanggal_awal'          : tanggal_awal,
                'tanggal_akhir'         : tanggal_akhir,
            },
            success: function (data) {
                $("#filterdetailsupplierlb").html(data);
                // reloaddetailsupplierlb(tanggal_awal,tanggal_akhir,itemid,supplier);
            }
        });
    });

    function reloaddetailsupplierlb(tanggal_awal,tanggal_akhir,itemid,supplier) {
        $.ajax({
            url: "{{ route('warehouse_dash.filter_lb') }}",
            type: "GET",
            data: {
                'key'                   : 'view_page',
                'loadDetailSupplier'    : 'YES',
                'supplier'              : supplier,
                'itemid'                : itemid,
                'tanggal_awal'          : tanggal_awal,
                'tanggal_akhir'         : tanggal_akhir,
            },
            success: function(data) {
                $("#filterdetailsupplierlb").html(data);
            }
        });
    }
</script>

<script>
    // var seriessupplier  = supplier
    // var LB              = dataLBSupplier

    Highcharts.chart('container-sebaran-karkas-all-supplier', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Sebaran Ayam Hidup Semua Supplier'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: <?php echo $dataLBSupplier; ?>
        },
        yAxis: {
            title: {
                text: 'Ekor'
            },
            labels: {
                format: '{value}'
            },
        },
        tooltip: {
            formatter: function() {
                var numerator = 250;
                return (this.y).toFixed(1);
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: <?php echo $supplier; ?>
    });
</script>

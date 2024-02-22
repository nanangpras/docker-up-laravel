<div class="mb-4">
    <div class="card-body p-2">
        <div style="float: right;">
            <div class="row">
                <div class="col-6">
                    {!! $filter_ukuran_lb !!}
                </div>
            </div>
        </div>
    </div>
    <div id="filterallsupplierLB"></div>
</div>
<script>

    $('.select2').select2({
        theme: 'bootstrap4'
    })

    var tanggal_awal                    = $("#tanggal_awal").val();
    var tanggal_akhir                   = $("#tanggal_akhir").val();
    var itemid                          = $("#all_ukuran_lb").val();
    var supplier                        = $("#suppliername").val();

    var hash                            = window.location.hash;
    defaultPage();

    function defaultPage() {
        if (hash == "#custom-tabs-sebaranlb") {
            reloadallfilterlb(tanggal_awal,tanggal_akhir)
        }
    }
    $("#all_ukuran_lb").on('change', function () {
        let itemid                     = $("#all_ukuran_lb").val();
        let supplier                   = $("#suppliername").val();
        $.ajax({
            url: "{{ route('view_progress') }}",
            type: "GET",
            data: {
                'subkey'                : 'view_data_livebird',
                'loadSupplier'          : 'YES',
                'itemid'                : itemid,
                'supplier'              : supplier
            },
            success: function (data) {
                reloadallfilterlb(tanggal_awal,tanggal_akhir);
            }
        });
    });

    function reloadallfilterlb(tanggal_awal,tanggal_akhir) {
        let itemid                      = $("#all_ukuran_lb").val();
        $.ajax({
            url: "{{ route('view_progress') }}",
            type: "GET",
            data: {
                'subkey'                : 'view_data_livebird',
                'loadSupplier'          : 'YES',
                'itemid'                : itemid,
                'tanggal_awal'          : tanggal_awal,
                'tanggal_akhir'         : tanggal_akhir,
            },
            success: function(data) {
                $("#filterallsupplierLB").html(data);
            }
        });
    }
</script>
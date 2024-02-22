<section class="panel">
    <div class="card-body">
        <div class="form-group">
            Pencarian Tanggal SO
            <input type="hidden" name="customer" class="form-control" value="{{ $customer ?? '' }}" id="customer">
            <div class="row">
                <div class="col pr-1">
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggal" class="form-control" value="{{ $tanggal }}"
                        id="tanggal_order" placeholder="Cari...." autocomplete="off">
                </div>
                <div class="col pl-1">
                    <input type="text" name="search" class="form-control" value="{{ $search ?? ''}}" id="search"
                        placeholder="Cari...." autocomplete="off">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col pr-1">
                <button type="submit" class="btn btn-outline-primary btn-block proses" data-data="">Semua</button>
            </div>
            <div class="mb-3 col px-1">
                <button type="submit" class="btn btn-outline-success btn-block proses"
                    data-data="selesai">Selesai</button>
            </div>
            <div class="mb-3 col px-1">
                <button type="submit" class="btn btn-outline-info btn-block proses" data-data="proses">Pending</button>
            </div>
            <div class="mb-3 col px-1">
                <button type="submit" class="btn btn-outline-danger btn-block proses" data-data="gagal">Gagal</button>
            </div>
            <div class="mb-3 col pl-1">
                <button type="submit" class="btn btn-outline-warning btn-block proses" data-data="batal">Batal</button>
            </div>
        </div>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div id="loading" class="text-center" style="display: none">
            <img src="{{asset('loading.gif')}}" width="20px">
        </div>
        <div id="summary"></div>
    </div>
</section>

<script>
    var customer    = $('#customer').val();
    var search      = $('#search').val();
    var tanggal     = "";
    var key         = "{{$key ?? ''}}";

    filterOrder();

    $('#tanggal_order').on('change', function() {
        tanggal = $(this).val();
        filterOrder()

    })

    $('#search').on('keyup', function() {
        search = $(this).val();

        filterOrder()

    })

    $('#tanggal_order').on('change', function() {
        tanggal = $(this).val();

        filterOrder()
    })

    $('#search').on('keyup', function() {
        filterOrder()
    })

    $('.proses').on('click', function() {
        key = $(this).attr('data-data');
        filterOrder();
    })

    function filterOrder(){

        $('#loading').show();
        tanggal = $('#tanggal_order').val();
        search  = $("#search").val();

        console.log(key)

        url = "{{ route('sampingan.index') }}?tanggal=" + tanggal + "&customer=" + customer +
                "&search=" + search + "&key=" + key;
        url_data = "{{ route('sampingan.order') }}?tanggal=" + tanggal + "&customer=" + customer +
                "&search=" + search + "&key=" + key;

        window.history.pushState('Sampingan', 'Sampingan', url);

        console.log(url_data);
        $("#summary").load(url_data, function(){
            $('#loading').hide();

            $('.pagination a').on('click', function(e) {

                e.preventDefault();
                console.log('ok masuk')
                var url_page = $(this).attr('href');

                console.log(url_page)
                $.ajax({
                    url: url_page,
                    method: "GET",
                    success: function(response) {
                        $('#summary').html(response);
                    }

                });
            });

        });

    }
</script>
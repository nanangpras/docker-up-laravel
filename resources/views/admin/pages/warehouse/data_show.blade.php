<div class="card-body">
    <div class="tab-content" id="custom-tabs-three-tabContent">
        <div class="tab-pane fade show active" id="custom-tabs-three-stock" role="tabpanel"
            aria-labelledby="custom-tabs-three-stock-tab">

            <form method="get" action="{{route('warehouse.stock')}}" id="filter-form-submit">
                <div class="row">

                    <div class="col-md-3 col-6 mb-3">
                        <label>Lokasi</label>
                        <select class="form-control" class="form-control" name="gudang_id" id="change-gudang">
                            @php
                            $subsidiary = env('NET_SUBSIDIARY', 'CGL');
                            $gudang = \App\Models\Gudang::where('subsidiary', $subsidiary)->where('kategori',
                            'warehouse')->where('status', '1')->get();
                            @endphp
                            <option value="">Semua</option>
                            @foreach($gudang as $g)
                            <option value="{{$g->id}}">{{$g->code}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <label>Pencarian</label>
                        <input type="text" class="form-control" id="search-filter" name="search"
                            value="{{ $search ?? '' }}" placeholder="Pencarian">
                    </div>
                </div>
            </form>

            <script>
                var url = "{{route('warehouse.stock')}}";

                $('#filter-form-submit').on('submit', function(e){
                    e.preventDefault();
                    url = $(this).attr('action')+"?"+$(this).serialize();
                    console.log(url);
                    filterWarehouseStock();
                })

                $('#change-gudang').on('change', function(){
                    $('#filter-form-submit').submit();
                    filterWarehouseStock();
                })

                $('#search-filter').on('keyup', function(){

                    console.log($(this).val())
                    $('#filter-form-submit').submit();
                    filterWarehouseStock();

                })

                function filterWarehouseStock(){
                    $.ajax({
                        url: url,
                        method: "GET",
                        success: function(response) {
                            $('#warehouse-stock').html(response);
                        }

                    });
                }

            </script>

            <div id="warehouse-stock"></div>
        </div>
        <div class="tab-pane fade" id="custom-tabs-three-masuk" role="tabpanel"
            aria-labelledby="custom-tabs-three-masuk-tab">
            <div id="warehouse-masuk"></div>
        </div>
        <div class="tab-pane fade" id="custom-tabs-three-keluar" role="tabpanel"
            aria-labelledby="custom-tabs-three-keluar-tab">
            <div id="warehouse-keluar"></div>
        </div>
        {{-- <div class="tab-pane fade" id="custom-tabs-three-requestthawing" role="tabpanel"
            aria-labelledby="custom-tabs-three-requestthawing-tab">
            <div id="warehouse-requestthawing"></div>
        </div>
        <div class="tab-pane fade" id="custom-tabs-three-thawing" role="tabpanel"
            aria-labelledby="custom-tabs-three-thawing-tab">
            <div id="warehouse-thawing"></div>
        </div> --}}
        {{-- <div class="tab-pane fade" id="custom-tabs-three-abf" role="tabpanel"
            aria-labelledby="custom-tabs-three-abf-tab">
            <div id="warehouse-abf"></div>
        </div> --}}
        <div class="tab-pane fade" id="custom-tabs-three-order" role="tabpanel"
            aria-labelledby="custom-tabs-three-order-tab">
            <div id="warehouse-order"></div>
        </div>
        <div class="tab-pane fade" id="custom-tabs-three-nonlb" role="tabpanel"
            aria-labelledby="custom-tabs-three-nonlb-tab">
            <div class="row mt-2">
                <div class="col-md-3 col-6 mb-3">
                    <div class="form-group">
                        <b>Mulai</b>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="mulai" name="mulai" value="{{ $mulai }}"
                            class="form-control change-filter-nonlb">
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="form-group">
                        <b>Sampai</b>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="sampai" name="sampai" value="{{ $mulai }}"
                            class="form-control change-filter-nonlb">
                    </div>
                </div>
            </div>
            <div id="warehouse-nonlb"></div>

        </div>
    </div>
</div>

<script>
    var hash = window.location.hash.substr(1);
    var href = window.location.href;

    deafultPage();

    function deafultPage() {
        if (hash == undefined || hash == "") {
            hash = "custom-tabs-three-stock";
        }

        $('#' + hash + '-tab').addClass('active').siblings().removeClass('active');
        $('#' + hash).addClass('active show').siblings().removeClass('active show');

    }

    $('#warehouse-stock').load("{{ route('warehouse.stock') }}")
    $('#warehouse-keluar').load("{{ route('warehouse.keluar') }}")
    $('#warehouse-abf').load("{{ route('warehouse.abf') }}")
    $('#warehouse-masuk').load("{{ route('warehouse.masuk') }}")
    // $('#warehouse-requestthawing').load("{{ route('warehouse.requestthawing') }}")
    // $('#warehouse-thawing').load("{{ route('warehouse.thawing') }}")
    $('#warehouse-order').load("{{ route('warehouse.order') }}")
    $('#warehouse-nonlb').load("{{ route('warehouse.nonlb') }}")

    $('#mulai').on('change', function() {
        var mulai = $('#mulai').val();
        var sampai = $('#sampai').val();
        // console.log(mulai);
        // console.log(sampai);
        url_ = "{{ route('warehouse.nonlb') }}?mulai="+mulai+"&sampai="+sampai;
        console.log(url_);
        $("#warehouse-nonlb").load(url_);
    })
    // $('#sampai').on('change', function() {
    //     var sampai = $('#sampai').val();
        // var sampai = $('#sampai').val();
    //     console.log(sampai);
        // console.log(sampai);
    //     url_ = "{{ route('warehouse.nonlb') }}?sampai="+sampai;
    //     console.log(url_);
    //     $("#warehouse-nonlb").load(url_);
    // })
</script>
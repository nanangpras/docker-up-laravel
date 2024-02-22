@foreach ($order as $row)

@php
    if ($row->no_so != NULL){
        $data = App\Models\Order::where('no_so', $row->no_so)->first();
    }
@endphp

    {{-- @if ($data->sales_channel != 'By Product - Paket' || $data->sales_channel != 'By Product - Retail') --}}

    <div class="card rounded-0 mb-2">
        <div class="card-body p-2">

            <div class="float-right text-right">
                <div class="">
                    {{ $row->namaCustomer }}<br>
                    @if ($row->jumlah_rute) <span class="status status-success cursor" data-toggle="modal"
                        data-target="#sales{{ $row->id }}">{{ $row->jumlah_rute }}x rute</span> @endif
                    <button class="btn btn-outline-warning rounded-0 py-0 px-2" data-toggle="modal"
                        data-target="#rutebypass{{ $row->id }}">Rute Bypass</button>
                    <button class="btn btn-outline-info buat_rute rounded-0 py-0 px-2" data-page="{{ $request->page }}"
                        data-id="{{ $row->no_so }}">Tambah Rute <i class="fa fa-arrow-right"></i></button>
                </div>
                
                    <div class="float-right text-right mt-2">
                        <span class="text-danger">
                            {{ $row->status == 3 ? 'Verified' : 'Unverified' }}
                        </span>
                    </div>
                    
            </div>
            

            <span class="blue">{{ $row->no_so }} </span> @if(isset($data->no_do)) // <span class="green small">{{ $data->no_do
                }}</span> @endif <br>
            <span class="small"> Kirim : {{ date('d/m/Y',strtotime($row->tanggal_kirim)) }}</span>
            @if($row->memo)
            <br>
            <span class="small red">
                {{ $row->memo }}
            </span>
            @endif
        </div>
    </div>

    <div class="modal fade" id="rutebypass{{ $row->id }}" tabindex="-1" aria-labelledby="rutebypass{{ $row->id }}Label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rutebypass{{ $row->id }}Label">Buat Rute Bypass</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="border-bottom pb-2 mb-2">
                        <div>{{ $row->nama }}</div>
                        <span class="blue">{{ $row->no_so }} </span> @if($row->no_do) // <span class="green small">{{ $row->no_do }}</span> @endif <br>
                        <span class="small"> Kirim : {{ date('d/m/Y',strtotime($row->tanggal_kirim)) }}</span>
                        @if($row->keterangan)
                        <br>
                        <span class="small red">
                            {{ $row->keterangan}}
                        </span>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6 pr-md-1">
                            <div class="form-group">
                                Nomor Urut
                                <select name="no_urut" id="no_urut{{ $row->id }}" data-placeholder="Pilih Nomor Urut"
                                    data-width="100%" class="form-control select2">
                                    <option value=""></option>
                                    @for ($i = 1; $i <= 8; $i++) <option value="{{ $i }}">Mobil {{ $i }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 pl-md-1">
                            <div class="form-group">
                                Tanggal Ekspedisi
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif value="{{ date("Y-m-d") }}"
                                    id="tanggal_ekspedisi{{ $row->id }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" data-id="{{ $row->id }}" data-page="{{ $request->page }}"
                        class="btn bypass_rute btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>

    @if ($row->jumlah_rute)
    <div class="modal fade" id="sales{{ $row->id }}" tabindex="-1" aria-labelledby="sales{{ $row->id }}Label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sales{{ $row->id }}Label">Info Rute</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nomor_SO{{ $row->id }}">Nomor SO</label>
                        <input type="text" value="{{ $row->no_so }}" id="nomor_SO{{ $row->id }}" class="form-control"
                            readonly>
                    </div>

                    <label><b>Ekspedisi</b></label>
                    @foreach ($row->marketingekpedisirute as $item)
                    @php
                    $eks = $item->ruteekspesidi ;
                    @endphp
                    <div class="border-bottom p-1">
                        <div class="row">
                            <div class="col-auto">{{ $eks->tanggal }}</div>
                            <div class="col">{{ $eks->nama }} {{ $eks->kernek ? ' / ' . $eks->kernek : '' }}</div>
                            <div class="col">{{ $eks->no_polisi }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- @endif --}}
@endforeach


<div id="paginate_summary">
    {{ $order->appends($_GET)->onEachSide(1)->links() }}
</div>

<script>
    $('#paginate_summary .pagination a').on('click', function(e) {
    e.preventDefault();
    showNotif('Menunggu');

    url = $(this).attr('href');
    $.ajax({
        url: url,
        method: "GET",
        success: function(response) {
            $('#data_so').html(response);
        }

    });
});
</script>

<script>
    $(".bypass_rute").on('click', function() {
    var id              =   $(this).data('id') ;
    var page            =   $(this).data('page') ;
    var no_urut         =   $("#no_urut" + id).val() ;
    var tanggal         =   $("#tanggal_ekspedisi" + id).val() ;

    var tanggal_kirim   =   $("#tanggal_kirim").val() ;
    var cari            =   encodeURIComponent($("#cari").val()) ;
    var ekspedisi       =   "{{ $request->id ?? '' }}" ;

    $(".bypass_rute").hide() ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('ekspedisi.store') }}",
        method: "POST",
        data: {
            id          :   id ,
            no_urut     :   no_urut ,
            tanggal     :   tanggal ,
            key         :   'bypass_rute'
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif(data.msg);
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&id=" + ekspedisi + "&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari + "&page=" + page) ;
                $("#show_rute").load("{{ route('ekspedisi.index', ['key' => 'show_rute']) }}&id=" + ekspedisi);
                $("#data_riwayat").load("{{ route('ekspedisi.riwayat', ['key' => 'data_riwayat']) }}") ;
                $("#renderbypass").load("{{ route('ekspedisi.index', ['key' => 'renderbypass']) }}&tanggal_kirim=" + tanggal_kirim);
            }
            $('.bypass_rute').show() ;
        }
    });
})
</script>

<script>
    $('.buat_rute').click(function() {
        var id              =   $(this).data('id') ;
        var page            =   $(this).data('page') ;
        var tanggal_kirim   =   $("#tanggal_kirim").val() ;
        var cari            =   encodeURIComponent($("#cari").val()) ;
        var ekspedisi       =   "{{ $request->id ?? '' }}" ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.buat_rute').hide() ;

        $.ajax({
            url: "{{ route('ekspedisi.store') }}",
            method: "POST",
            data: {
                id          :   id ,
                page        :   page ,
                ekspedisi   :   ekspedisi ,
                key         :   'temporary'
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif(data.msg);
                    $("#data_so").load("{{ route('ekspedisi.index', ['key' => 'sales_order']) }}&id=" + ekspedisi + "&tanggal_kirim=" + tanggal_kirim + "&cari=" + cari + "&page=" + page) ;
                    $("#show_rute").load("{{ route('ekspedisi.index', ['key' => 'show_rute']) }}&id=" + ekspedisi);
                }
                $('.buat_rute').show() ;
            }
        });
    })
</script>

<script>
    $(".select2").select2({
        theme: "bootstrap4"
    });
</script>
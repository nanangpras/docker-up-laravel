@extends('admin.layout.template')

@section('title', 'Detail Timbang Produksi Evis')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('evis.peruntukan') }}" class="btn btn-outline btn-sm btn-back">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="col-7 py-1 text-center">
        <b>DETAIL TIMBANG PRODUKSI EVIS</b>
    </div>
    <div class="col"></div>
</div>

@php
$netsuite = \App\Models\Netsuite::where('label', 'like', '%byproduct%')->where('trans_date', $data->tanggal)->get();
@endphp


<div class="modal fade " id="ambilBB" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Bahan Baku Evis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    Pencarian
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"
                        id="pencarian" placeholder="Cari...." autocomplete="off">
                </div>
                @if(User::setIjin(33))
                @endif
                <input type="checkbox" name="evis_fg" id="evis_fg"> <label for="evis_fg">Hasil Finished Goods</label>
                <div id="loading-ambilbbevis" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....
                </div>
                <form action="{{ route('evis.simpanbahanbaku') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tanggal" class="hidden" value="{{ date('Y-m-d') }}"
                        id="selected-tanggal">
                    <input type="hidden" name="produksi" value="{{ $data->id }}">
                    <div id="list_bahan_baku"></div>
                    {{-- <button type="submit" class="btn btn-blue">Simpan</button> --}}
                </form>
            </div>
        </div>
    </div>
</div>

<section class="panel">
    <div class="card">

        <div class="card-body">
            <div class="row" id="edit-bb-hasil" style="display: none">
                <div class="col-md-12 pl-md-1 mb-4">
                    <div class="card-body">
                        <h5>Hasil Produksi</h5>
                        <div class="row mb-2">
                            <div class="col-8 pr-1">
                                <select name="item" class="form-control select2 item" data-width="100%"
                                    data-placeholder="Pilih Produk Evis" id="item">
                                    <option value=""></option>
                                    @foreach ($item as $id => $list)
                                    <option value="{{ $list->id }}">{{ $list->sku }} -
                                        {{ $list->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-2 pl-0">
                                <input type="number" name="qty" id="qtyproduksi" autocomplete="off"
                                    class="form-control px-1" step="0.01" placeholder="Qty">
                            </div>
                            <div class="col-2 px-1">
                                <input type="number" name="berat" id="beratproduksi" autocomplete="off"
                                    class="form-control px-1" step="0.01" placeholder="Berat">
                            </div>

                        </div>
                        <div class="form-group">
                            Plastik
                            <div class="row">
                                <div class="col-8 pr-1">
                                    <select name="item" class="form-control select2 plastik" data-width="100%"
                                        data-placeholder="Pilih Plastik" id="plastik">
                                        <option value="Curah">Curah</option>
                                        @php
                                        $plastik = \App\Models\Item::where('category_id', '25')->where('subsidiary',
                                        env('NET_SUBSIDIARY', 'EBA'))->where('status', '1')->get();
                                        @endphp
                                        @foreach ($plastik as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama }} - {{$p->subsidiary}}{{ $p->netsuite_internal_id }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4 pl-1">
                                    <input type="number" name="qtyplastik" id="qtyplastik" class="form-control"
                                        placeholder="Qty">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="radio" name="tujuan_produksi" value="0" checked> Chiller Hasil Produksi
                            </label>
                            &nbsp; &nbsp;
                            <label>
                                <input type="radio" name="tujuan_produksi" value="1"> Kirim ABF
                            </label>
                        </div>
                        <button type="submit"
                            class="mt-3 btn btn-sm btn-primary btn-block tambahproduksi">Tambah</button>
                        <a href="javascript:void(0)" onclick="return openTambah()" class="btn btn-red mt-3">Tutup</a>
                        <hr>
                    </div>
                </div>
            </div>

            @if(count($netsuite)>0)
            @if(Auth::user()->account_role == 'superadmin')
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ambilBB" id="evisambilbb">
                Tambah Bahan Baku
            </button>
            <a href="javascript:void(0)" onclick="return openTambah()" class="btn btn-green">Tambah Hasil Produksi</a>
            @endif
            @else
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ambilBB" id="evisambilbb">
                Tambah Bahan Baku
            </button>
            <a href="javascript:void(0)" onclick="return openTambah()" class="btn btn-green">Tambah Hasil Produksi</a>
            @endif


            @if ($data->status == 2)
            <button type="button" class="btn btn-success float-right approveddetailevis"
                data-id="{{ $data->id }}">Selesaikan</button>
            @endif
            <hr>
            <div id="loading-detailEvis" class="text-center"><i class="fa fa-refresh fa-spin"></i> Loading....</div>

            <div id="data_produksi"></div>
        </div>

    </div>
</section>
@endsection


@section('footer')
@if ($data->status == 2)
<script>
    $('.approveddetailevis').click(function() {
    var freestock_id    =   $(this).data('id');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".approveddetailevis").hide() ;
    showNotif('Menunggu produksi diselesaikan');

    $.ajax({
        url: "{{ route('evis.peruntukanselesai') }}",
        method: 'POST',
        data: {
            freestock_id: freestock_id
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                window.location.reload("{{ route('evis.peruntukan') }}") ;
                showNotif('Berhasil Approve');
            }
        }
    })
})
    
</script>
@endif

<script>
    $("#data_produksi").load("{{ route('evis.peruntukan', ['view' => 'data_produksi']) }}&produksi={{ $data->id }}", () => {
        $("#loading-detailEvis").hide();
    });

    function loadbbevis() {
        $("#loading-ambilbbevis").show();
        var tanggal =   $("#pencarian").val();
        // var evis_fg =   document.getElementById("evis_fg").checked ? 1 : 0;
        var evis_fg =   document.getElementById("evis_fg").checked ? 'evis_fg' : '';
        $('#selected-tanggal').val(tanggal);
        // $("#list_bahan_baku").load("{{ url('admin/evis/gabung/bahanbaku?tanggal=') }}" + tanggal + "&produksi={{ $data->id }}&evis_fg=" + evis_fg, () => {
        //     $("#loading-ambilbbevis").hide();
        // });
        $("#list_bahan_baku").load("{{ url('admin/evis/gabung/bahanbaku?tanggal=') }}" + tanggal + "&produksi={{ $data->id }}&bbtype=" + evis_fg, () => {
            $("#loading-ambilbbevis").hide();
        });
    }

    var searchTimeout = null; 
    $("#pencarian").on('change', function(){
        if (searchTimeout != null) {
            clearTimeout(searchTimeout);
        }
        searchTimeout = setTimeout(function() {
            searchTimeout = null;  
            //ajax code
            loadbbevis();
        }, 1000);  
    })
    
    $("#evisambilbb").on("click", function() {
        loadbbevis();
    })

    $("#evis_fg").on('click', function() {
        loadbbevis();
    })

    $('.select2').select2({
        theme: 'bootstrap4'
    })

    function openTambah(){
        $('#edit-bb-hasil').toggle();
    }

    $('.tambahproduksi').click(function() {
        var berat           =   $('#beratproduksi').val();
        var qty             =   $('#qtyproduksi').val();
        var item            =   $('#item').val();
        var plastik         =   $("#plastik").val();
        var qtyplastik      =   $("#qtyplastik").val();
        var freestock_id    =   "{{ $data->id }}";
        var tujuan_produksi =   $('input[name="tujuan_produksi"]:checked').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('evis.evisfreestockstore') }}",
            method: "POST",
            data: {
                berat: berat,
                qty: qty,
                item: item,
                plastik: plastik,
                qtyplastik: qtyplastik,
                freestock_id: freestock_id,
                act:'tambahan',
                tujuan_produksi:tujuan_produksi
            },
            success: function(data) {
                if (data.status == 400) {
                    showAlert(data.msg);
                } else {
                    showNotif('Buat free stock berhasi');
                    $("#data_produksi").load("{{ route('evis.peruntukan', ['view' => 'data_produksi']) }}&produksi={{ $data->id }}", () => {
                        $("#loading-detailEvis").hide();
                    });
                    $('#beratproduksi').val('');
                    $('#qtyproduksi').val('');
                    $('#qtyplastik').val('');
                    $('#item').val(null).trigger('change');
                    $('#plastik').val(null).trigger('change');
                }
            }
        })
    })
</script>

<script>
    $(document).on('click', '.hapus_bb', function() {
    var row_id = $(this).data('id');
    $(".hapus_bb").hide();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('regu.delete') }}",
        method: "DELETE",
        data: {
            row_id: row_id,
            key: 'bb_detail'
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg) ;
            } else {
                $("#data_produksi").load("{{ route('evis.peruntukan', ['view' => 'data_produksi']) }}&produksi={{ $data->id }}", () => {
                    $("#loading-detailEvis").hide();
                });
                showNotif('Bahan baku diambil berhasil dihapus');
            }
            $(".hapus_bb").show();
        }
    });
})
</script>

<script>
    $(document).on('click', '.hapus_fg', function() {
    var row_id = $(this).data('id');
    $(".hapus_fg").hide();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('regu.delete') }}",
        method: "DELETE",
        data: {
            row_id: row_id,
            key: 'hapus_fg'
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg) ;
            } else {
                $("#data_produksi").load("{{ route('evis.peruntukan', ['view' => 'data_produksi']) }}&produksi={{ $data->id }}", () => {
                        $("#loading-detailEvis").hide();
                    });
                showNotif('Hasil produksi berhasil dihapus');
            }
            $(".hapus_fg").show();
        }
    });
})
</script>

<script>
    $(document).on('click', '#submitBB', function() {
    var xcode       =   document.getElementsByClassName("xcode") ;
    var xitemid     =   document.getElementsByClassName('xitemid') ;
    var bbberat     =   document.getElementsByClassName('bbberat') ;
    var bbitem      =   document.getElementsByClassName('bbitem') ;
    var tanggal     =   $("#pencarian").val();
    var evis_fg     =   document.getElementById("evis_fg").checked ? 1 : 0;
    var free_stock  =   "{{ $data->id ?? '' }}" ;

    var x_code      =   [] ;
    var x_item_id   =   [] ;
    var berat       =   [] ;
    var qty         =   [] ;
    for (var i = 0; i < bbberat.length; ++i) {
        x_code.push(xcode[i].value);
        x_item_id.push(xitemid[i].value);
        berat.push(bbberat[i].value);
        qty.push(bbitem[i].value);
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('evis.simpanbahanbaku') }}",
        method: "POST",
        data: {
            free_stock  :   free_stock ,
            tanggal     :   tanggal ,
            x_code      :   x_code ,
            x_item_id   :   x_item_id ,
            berat       :   berat ,
            qty         :   qty ,
        },
        success: function(data) {
            // $("#list_bahan_baku").load("{{ url('admin/evis/gabung/bahanbaku?tanggal=') }}" + tanggal + "&produksi=" + free_stock + "&evis_fg=" + evis_fg);
            $("#bbperuntukan").load("{{ route('evis.bbperuntukan') }}");
            $("#selesaikan").load("{{ route('evis.peruntukan', ['key' => 'selesai']) }}");
            window.location.reload();
            // showNotif('Tambah bahan baku berhasil') ;
        }
    });
});

</script>
@endsection
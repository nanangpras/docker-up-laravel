@extends('admin.layout.template')

@section('title', 'Musnahkan')

@section('footer')
<script>
    $("#list").load("{{ route('musnahkan.index', ['key' => 'list']) }}&type=gudang");
</script>

<script>
    var id                      = "";
    var cari                    = "";
    var tangggal_pindah         = "";
    var tanggal_pindah_akhir    = "";

$('.select2').select2({
    theme: 'bootstrap4'
})

$(document).ready(function(){
    $("input:checkbox").each(function() {
        var $box = $(this);
        if ($box.is(":checked")) {
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            $(group).prop("checked", false);
            $box.prop("checked", true);
            id = $box.val();
        }
    });

    $("input:checkbox").on('click', function() {
        var $box = $(this);
        if ($box.is(":checked")) {
            var group = "input:checkbox[name='" + $box.attr("name") + "']";
            $(group).prop("checked", false);
            $box.prop("checked", true);
            id        = $box.val();
        } else {
            $box.prop("checked", false);
            id      = "";
        }
    });
})

$('.cold').change(function() {
    $('.cold').each(function() {
        if ($(this).is(":checked")) {
            id = $(this).val();
        }
    });
    cari                    = encodeURIComponent($('#item').val());
    tanggal_pindah          = $('#tanggal-pindah').val();
    tanggal_pindah_akhir    = $('#tanggal-pindah-akhir').val();
    $("#show").load("{{ route('musnahkan.index', ['key' => 'view']) }}&id=" + id + "&tanggal="+tanggal_pindah + "&tanggal_akhir=" + tanggal_pindah_akhir + "&cari=" + cari);
});

$('#cari').click(function () {
    cari                    = encodeURIComponent($('#item').val());
    tanggal_pindah          = $('#tanggal-pindah').val();
    tanggal_pindah_akhir    = $('#tanggal-pindah-akhir').val();
    console.log(cari)
    id                      = $('.cold:checked').val();
    // if(id === undefined || id === null){
    //     showAlert('Pilih Salah Satu Storage');
    //     return false;
    // }
    $("#show").load("{{ route('musnahkan.index', ['key' => 'view']) }}&id=" + id + "&tanggal="+tanggal_pindah + "&tanggal_akhir=" + tanggal_pindah_akhir + "&cari=" + cari);
})

// $('#tanggal-pindah, #tanggal-pindah-akhir').change(function(){
   
//     $("#show").load("{{ route('musnahkan.index', ['key' => 'view']) }}&id=" + id + "&tanggal="+tanggal_pindah + "&tanggal_akhir=" + tanggal_pindah_akhir);
// })

// $('.cold').change(function() {
//     id                      =   $(this).val();
//     tanggal_pindah          =   $('#tanggal-pindah').val();
//     tanggal_pindah_akhir    =   $('#tanggal-pindah-akhir').val();
//     console.log(id);
//     $("#show").load("{{ route('musnahkan.index', ['key' => 'view']) }}&id=" + id + "&tanggal="+tanggal_pindah + "&tanggal_akhir=" + tanggal_pindah_akhir);
// })
</script>

<script src="{{ asset('plugin/jquery.validate.min.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        var form    = $('#formberitaacara');
        var error   = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            doNotHideMessage: true, 
            errorElement: 'span', 
            errorClass: 'help-block help-block-error', 
            focusInvalid: true, 
            rules: {
                "beritaacara": {
                    remote: {
                        method: "GET",
                        url: "{{ route('musnahkan.index', ['key' => 'cekavailablebap']) }}",
                    },
                }
            },
            messages:{
                beritaacara: {
                    remote: 'Nomor Berita Acara Sudah digunakan',
                },
            },
            errorPlacement: function (error, element) { 
                error.insertAfter(element); // for other inputs, just perform default behavior
            },
            invalidHandler: function (event, validator) {    
                success.hide();
                error.show();
            },

            highlight: function (element) { 
                $(element)
                    .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { 
                $(element)
                    .closest('.form-group').removeClass('has-error'); 
            },
            
            success: function (label) {
                label
                    .addClass('valid') 
                    .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
            },
            submitHandler: function(form) {
                success.show();
                error.hide();
                form.submit();
            }
        });     

        $('#selesaikan').click(function() {
            var tanggal                 =   $("#tanggal_submit").val() ;
            var keterangan              =   $("#keterangan").val() ;
            var tanggal_pindah          =   $('#tanggal-pindah').val();
            var tanggal_pindah_akhir    =   $('#tanggal-pindah-akhir').val();
            var beritaacara             =   $('#beritaacara').val();
            var cold                    =   $(".cold:checked").val() ;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#selesaikan').hide() ;

            $.ajax({
                url: "{{ route('musnahkan.store') }}",
                method: "POST",
                data: {
                    tanggal     :   tanggal,
                    keterangan  :   keterangan,
                    beritaacara :   beritaacara,
                    key         :   'selesaikan'
                },
                beforeSend:function(){
                    if(!$("#formberitaacara").validate()) { 
                        return false;
                    }
                },
                success: function(data) {
                    if (data.status == 400) {
                        showAlert(data.msg);
                    } else {
                        showNotif(data.msg);
                        $("#list").load("{{ route('musnahkan.index', ['key' => 'list']) }}&type=gudang");
                        $("#show").load("{{ route('musnahkan.index', ['key' => 'view']) }}&id=" + cold + "&tanggal=" + tanggal_pindah + "&tanggal_akhir=" + tanggal_pindah_akhir);
                        $("#keterangan").val('')
                    }
                    $('#selesaikan').show() ;
                }
            });
        })
    });
</script>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col text-center font-weight-bold">Musnahkan</div>
    <div class="col text-right">
        {{-- <a href="{{ route('musnahkan.item') }}" class="btn btn-sm btn-outline-dark">Musnahkan Item</a>&nbsp; --}}
        <a href="{{ route('musnahkan.riwayat') }}" class="btn btn-sm btn-success">Riwayat</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 pr-lg-1">
        <section class="panel">
            <div class="card-body">
                <div class="form-group">
                    <div class="text-center">
                        <h6>Pencarian</h6>
                        <hr>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="tanggal-pindah">Tanggal Mulai</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif class="form-control" name="q" value="{{date('Y-m-d')}}"
                                id="tanggal-pindah">
                        </div>
                        <div class="col">
                            <label for="tanggal-pindah-akhir">Tanggal Akhir</label>
                            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                min="2023-01-01" @endif class="form-control" name="q" value="{{date('Y-m-d')}}"
                                id="tanggal-pindah-akhir">
                        </div>
                        <div class="col">
                            <label for="item">Nama Item</label>
                            <select name="item" id="item" class="form-control select2"">
                                <option value="">All</option>
                                @foreach ($gudang as $item)
                                    <option value="{{$item->item_name ?? $item->nama}}">{{ $item->item_name ?? $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row radio-toolbar mt-2">
                        @foreach ($cold as $i => $warehouse)
                            <div class="col-4">
                                <input type="checkbox" name="cold" class="cold" value="{{ $warehouse->id }}"
                                    id="{{ $warehouse->id }}">
                                <label for="{{ $warehouse->id }}">{{ $warehouse->code }}</label>
                            </div>
                        @endforeach
                        @foreach ($cold1 as $j => $warehouse)
                            <div class="col-4">
                                <input type="checkbox" name="cold" class="cold" value="{{ $warehouse->id }}"
                                    id="{{ $warehouse->id }}">
                                <label for="{{ $warehouse->id }}">{{ $warehouse->code }}</label>
                            </div>
                        @endforeach
                        {{-- <div class="col-4">
                                <input type="checkbox" name="cold" class="cold" value="fg" id="fg">
                                <label for="fg">Chiller Finished Good</label>
                        </div>
                        <div class="col-4">
                            <input type="checkbox" name="cold" class="cold" value="bb" id="bb">
                            <label for="bb">Chiller Bahan Baku</label>
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="col">
                            <button class="btn btn-primary" id="cari" class="cari">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div id="show"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="col-lg-4 pl-lg-1">
        <div id="list"></div>

        <section class="panel">
            <div class="card-body">
                <form id="formberitaacara">
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea id="keterangan" rows="2" placeholder="Tuliskan keterangan"
                            class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="beritaacara">Nomor Berita Acara</label>
                        <input type="text" id="beritaacara" name="beritaacara" placeholder="Nomor berita acara" class="form-control"></input>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_submit">Tanggal</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif id="tanggal_submit" class="form-control" value="{{ date("Y-m-d") }}">
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-primary" id="selesaikan">Submit</button>
                </div>
            </form>
        </section>
    </div>
</div>

<style>
    .has-success .help-block,
    .has-success .control-label
    {
        color: #fff;
    }

    .has-success .form-control {
        border-color: #28a745;
        -webkit-box-shadow: none;
        box-shadow: none;
        background-position: center right 0.9em;
        background-repeat: no-repeat;
        background-size: 20px 16px;
        padding-right: 2.2em;
    }

    .has-success .form-control:focus {
        border-color: #28a745;
        -webkit-box-shadow: none;
        box-shadow: none;
    }

    .has-success .input-group-addon {
        color: #28a745;
        border-color: #28a745;
        background-color: #1e2227;
    }

    .has-success .form-control-feedback {
        color: #28a745;
    }

    .has-success .form-control {
        background-size: 15px 12px;
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 14 12'%3E%3Cpath transform='translate(-189.438 -2446.25)' fill='%2324d9b0' d='M201.45,2446.24l2.121,2.13-9.192,9.19-2.122-2.12Zm-4.949,9.2-2.121,2.12-4.95-4.95,2.121-2.12Z'/%3E%3C/svg%3E");
    }

    .has-error .help-block,
    .has-error .control-label {
        color: #f92552;
    }
    .has-error .form-control {
        border-color: #f92552;
        -webkit-box-shadow: none;
        box-shadow: none;
        background-position: center right 0.9em;
        background-repeat: no-repeat;
        background-size: 20px 16px;
        padding-right: 2.2em;
    }

    .has-error .form-control:focus {
        border-color: #f92552;
        -webkit-box-shadow: none;
        box-shadow: none;
    }

    .has-error .input-group-addon {
        color: #f92552;
        border-color: #f92552;
        background-color: #1e2227;
    }

    .has-error .form-control-feedback {
        color: #f92552;
    }

    .has-error .form-control {
        background-size: 11px 11px;
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 11 11'%3E%3Cpath transform='translate(-190.844 -2353.84)' fill='%23f34141' d='M190.843,2355.96l2.121-2.12,9.193,9.2-2.122,2.12Zm9.192-2.12,2.122,2.12-9.193,9.2-2.121-2.12Z'/%3E%3C/svg%3E");
    }
</style>
@stop
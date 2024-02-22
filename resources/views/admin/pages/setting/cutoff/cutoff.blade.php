<div class="mb-4 text-center font-weight-bold">Tutup Transaksi</div>
<div class="mb-2">
    <a href="{{ route('tutup.transaksi',['key' => 'historycutoff']) }}"
        class="btn btn-info btn-sm p-0 px-1 viewhistorycutoff" title='Lihat History Cutoff' data-toggle="modal"
        data-target="#modalViewHistory" data-inputbyactivity="cut_off">
        <button type="button" class="btn btn-info"> Lihat History Cut Off</button>
    </a>
</div>
<section class="panel">
    <div class="card-body">
        {{-- <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="">Tahun</label>
                    <select class="form-control select2" name="tahun" id="tahun">
                        <option value="">--Pilih Tahun--</option>
                        @php
                        $tg_awal = date('Y') - 2;
                        $tgl_akhir = date('Y');
                        for ($i = $tgl_akhir; $i >= $tg_awal; $i--) {
                        echo '<option value=' . $i . '>' . $i . '</option>';
                        }
                        @endphp
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="">Bulan</label>
                    <select class="form-control select2" name="bulan" id="bulan">
                        <option value="">--Pilih Bulan--</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
            </div>
        </div> --}}
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="cutoffMulai">Pilih tanggal mulai cutoff</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="cutoffMulai" name="mulai" value="{{$mulai}}"
                        class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="cutoffAkhir">Pilih tanggal akhir cutoff</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="cutoffAkhir" name="mulai" value="{{$akhir}}"
                        class="form-control form-control-sm" required>
                </div>
            </div>
        </div>

        <div class="radio-toolbar row">
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="checkbox" name="tujuan[]" value="chiller" class="tujuan" id="sisa">
                    <label for="sisa">Chiller</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="checkbox" name="tujuan[]" value="abf" class="tujuan" id="abf">
                    <label for="abf">ABF</label>
                </div>
            </div>
            {{-- <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="gudang" class="tujuan" id="gudang">
                    <label for="gudang">Gudang</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="radio" name="tujuan" value="retur" class="tujuan" id="retur">
                    <label for="retur">Retur</label>
                </div>
            </div> --}}
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-primary tutupTransaksi">
                <i class="fa fa-spinner fa-spin spinerloading" style="display:none;"></i>
                <span id="text">Tutup Transaksi</span>
            </button>
        </div>
    </div>
</section>

<div class="modal fade" id="modalViewHistory" data-keyboard="false" aria-labelledby="modalViewLabel"
    aria-hidden="false">
    <div class="modal-dialog" style="max-width:900px;">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #f1f1f1;">
                <h5 class="modal-title">History Tanggal Cut Off </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contentViewHistory"></div>
        </div>
    </div>
</div>

<script>
    $(document).on('click','.tutupTransaksi', function () {
        if(confirm( 'Transaksi yang sudah ditutup tidak dapat di buka lagi') === true){
            var tujuan  = [];
            $('.tujuan').each(function() {
                if ($(this).is(":checked")) {
                    tujuan.push($(this).val());
                }
            });
            var tahun       = $("#tahun").val();
            var bulan       = $("#bulan").val();
            var tgl_awal    = $('#cutoffMulai').val();
            var tgl_akhir   = $('#cutoffAkhir').val();

            if (tujuan === undefined || tgl_awal == null || tgl_akhir == null) {
                showAlert('Terdapat field yang belum diisi');
                // return false;
            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url     : "{{ route('tutup.transaksi') }}",
                    method  : "POST",
                    data    : {
                        tujuan      : tujuan,
                        cutoffMulai : tgl_awal,
                        cutoffAkhir : tgl_akhir,
                    },
                    success: function(data) {
                        if (data.status == 400) {
                            showAlert(data.msg) ;
                        } else {
                            showNotif('Transaksi berhasil ditutup');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
            
        }else{
            showAlert('Gagal menutup transaksi');
            return false;
        }
    });

    $(".viewhistorycutoff").click(function (e) {
        e.preventDefault();
        var activity        = $(this).data('inputbyactivity');
        var href            = $(this).attr('href');

        $.ajax({
            url     : href,
            type    : "POST",
            data    : {
                activity    : activity,
                "_token"    : "{{ csrf_token() }}"
            },
            success: function(data){
                $('#contentViewHistory').html(data);
            }
        });
    });
</script>
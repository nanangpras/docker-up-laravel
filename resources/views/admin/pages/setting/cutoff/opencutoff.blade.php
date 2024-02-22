<div class="mb-4 text-center font-weight-bold">Buka Transaksi</div>
<section class="panel">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="startdate">Tanggal Awal </label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="{!! Applib::DefaultTanggalAudit() !!}" @endif id="startdate" name="startdate" value="{{ $mulai ? $mulai : date('Y-m-d') }}" class="form-control form-control-sm" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="enddate">Tanggal Akhir</label>
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="{!! Applib::DefaultTanggalAudit() !!}" @endif id="enddate" name="enddate" value="{{ $akhir ? $akhir : date('Y-m-d') }}" class="form-control form-control-sm" required>
                </div>
            </div>
        </div>

        <div class="radio-toolbar row">
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="checkbox" name="destination[]" class="destination" id="openchillerfg" value="chillerfg" >
                    <label for="openchillerfg">Chiller - FG</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="checkbox" name="destination[]" class="destination" id="openchillerbb" value="chillerbb" >
                    <label for="openchillerbb">Chiller - BB</label>
                </div>
            </div>
            <div class="col-lg-3 col-4">
                <div class="form-group">
                    <input type="checkbox" name="destination[]" class="destination" id="openabf" value="tbabf" >
                    <label for="openabf">ABF</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-primary bukaTransaksi">
                <i class="fa fa-spinner fa-spin spinerloadingOpen" style="display:none;"></i>
                <span id="text">Buka Transaksi</span>
            </button>
        </div>
    </div>
</section>
<script>
    $(document).on('click','.bukaTransaksi', function () {

        if(confirm( 'Transaksi akan di buka kembali ?') === true){
            var destination  = [];
            $('.destination').each(function() {
                if ($(this).is(":checked")) {
                    destination.push($(this).val());
                }
            });
            var startdate   = $('#startdate').val();
            var enddate     = $('#enddate').val();

            if (destination === undefined || startdate == null || enddate == null) {
                showAlert('Terdapat field yang belum diisi');
                // return false;
            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url     : "{{ route('open.transaksi') }}",
                    method  : "POST",
                    data    : {
                        destination : destination,
                        startdate   : startdate,
                        enddate     : enddate,
                    },
                    success: function(data) {
                        if (data.status == 400) {
                            showAlert(data.msg) ;
                        } else {
                            showNotif('Transaksi berhasil dibuka kembali');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });
            }
            
        }else{
            showAlert('Gagal membuka transaksi');
            return false;
        }
    });
</script>
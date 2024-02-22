@extends('admin.layout.template')

@section('title', 'Open Balance')

@section('footer')
<script>
$('.select2').select2({
    theme: 'bootstrap4'
});
</script>

<script>
$("#input_data").load("{{ route('openbalance.index', ['key' => 'input']) }}");
</script>

<script>
$(document).on('click', '#input_balance', function() {
    var item            =   $("#item").val() ;
    var tanggal         =   $("#tanggal").val() ;
    var gudang          =   $("#gudang").val() ;
    var tipe_item       =   $("#tipe_item").val() ;
    var qty             =   $("#qty").val() ;
    var berat           =   $("#berat").val() ;
    var label           =   $("#label").val() ;
    var label_cs        =   $("#label_cs").val() ;
    // var label_abf       =   $("#label_abf").val() ;
    var sub_item        =   $("#sub_item").val() ;
    var parting         =   $("#parting").val() ;

    var pallete         =   $("#pallete").val() ;
    var tujuan          =   $("#tujuan").val() ;
    var packaging       =   $("#packaging").val() ;

    var expired         =   $("input[name=expired]:checked").val() ;
    var stock           =   $("input[name=stock]:checked").val() ;

    // alert(label_cs);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('openbalance.store') }}",
        method: "POST",
        data: {
            item            :   item,
            tanggal         :   tanggal,
            gudang          :   gudang,
            qty             :   qty,
            tipe_item       :   tipe_item,
            berat           :   berat,
            pallete         :   pallete,
            tujuan          :   tujuan,
            packaging       :   packaging,
            expired         :   expired,
            sub_item        :   sub_item,
            stock           :   stock,
            label           :   label,
            label_cs        :   label_cs,
            // label_abf       :   label_abf,
            parting         :   parting,
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg) ;
            } else {
                showNotif('Open balance berhasil') ;
                $("#input_data").load("{{ route('openbalance.index', ['key' => 'input']) }}");
            }
        }
    });
})
</script>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col py-2 text-center">
        <b>Open Balance</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <div id="input_data"></div>
    </div>
</section>

{{-- <section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Upload Excel Chiller</b></div>
        <form action="{{ route('openbalance.import') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('patch')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <select name="warehouse" class="form-control select2" data-placeholder="Pilih Gudang" data-width="100%">
                            <option value=""></option>
                            @foreach ($chiller as $row)
                            <option value="{{ $row->id }}">{{ $row->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section> --}}

{{-- <section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Upload Excel Warehouse</b></div>
        <form action="{{ route('openbalance.import') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('patch')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <select name="warehouse" class="form-control select2" data-placeholder="Pilih Gudang" data-width="100%">
                            <option value=""></option>
                            @foreach ($warehouse as $row)
                            <option value="{{ $row->id }}">{{ $row->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section> --}}

<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Upload Excel Cold Storage</b></div>
        <form action="{{ route('openbalance.upload_stock_cs') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        <a href="https://docs.google.com/spreadsheets/d/1mmUdzbeofEBhVc0QCCpjgBjdoHRQudIe/edit?usp=sharing&ouid=112879792656188164544&rtpof=true&sd=true" target="_blank">Download Template Excel <span class="fa fa-download"></span></a>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Upload Excel Chiller Finished Goods</b></div>
        <form action="{{ route('openbalance.upload_stock_chiller_fg') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        <a href="https://docs.google.com/spreadsheets/d/1tQuWtJJ7Km1HoLvS6htzIH3FGcycR50hRS4gBc6MPQU/edit#gid=0" target="_blank">Download Template Excel <span class="fa fa-download"></span></a>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Upload Excel Chiller Bahan Baku</b></div>
        <form action="{{ route('openbalance.upload_stock_chiller_bb') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        <a href="https://docs.google.com/spreadsheets/d/135fPFUYIvee0scK6agtp843LRbZFU8-GtkCp7QYJVDk/edit#gid=0" target="_blank">Download Template Excel <span class="fa fa-download"></span></a>
    </div>
</section>


{{-- <section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Upload Excel TI ABF ke CS</b></div>
        <form action="{{ route('openbalance.upload_abf_cs') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        <a href="https://docs.google.com/spreadsheets/d/1z1rcNxyEO0N3T5D-zZ_Z7ck307M68dU48wGw7GkmHyo/edit#gid=1031213094" target="_blank">Download Template Excel <span class="fa fa-download"></span></a>
    </div>
</section> --}}
<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Generate TI Custom</b></div>
        <form action="{{ route('openbalance.generate_ti_custom') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        <a href="https://docs.google.com/spreadsheets/d/1s0FGf_F7PGNXZqnUpRpi4vNcJGYmyy3nWGtCLdnnchM/edit#gid=329391302" target="_blank">Download Template Excel <span class="fa fa-download"></span></a>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Import WO Thawing</b></div>
        <form action="{{ route('openbalance.upload_wo_thawing') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        <a href="https://docs.google.com/spreadsheets/d/1AiSp3NTJB0HN99VrMkkSXcdqHIBrD62k2F7V9wP8LJ0/edit#gid=0" target="_blank">Download Template Excel <span class="fa fa-download"></span></a>
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Import WO3 ABF CS</b></div>
        <form action="{{ route('openbalance.upload_abf_cs_wo') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        <a href="https://docs.google.com/spreadsheets/d/1DTFwGNOVN9mU3dARMzLiDiwFjuqIT1WAu54NVp5erzE/edit#gid=821245350" target="_blank">Download Template Excel <span class="fa fa-download"></span></a>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Import TI ABF CS</b></div>
        <form action="{{ route('openbalance.upload_abf_cs_ti') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        <a href="https://docs.google.com/spreadsheets/d/1DTFwGNOVN9mU3dARMzLiDiwFjuqIT1WAu54NVp5erzE/edit#gid=821245350" target="_blank">Download Template Excel <span class="fa fa-download"></span></a>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Import WB3 WO3 Recreate</b></div>
        <form action="{{ route('openbalance.upload_wb3_recreate') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Import Customer</b></div>
        <form action="{{ route('openbalance.upload_customer') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        https://docs.google.com/spreadsheets/d/1IfsOqEwKWgSktGQeA666kPGHaohTXtIA5Ji-1Nr7PKk/edit?usp=sharing
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Import Vendor</b></div>
        <form action="{{ route('openbalance.upload_vendor') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        https://docs.google.com/spreadsheets/d/1ohq9V0RCV-C_jFdYQCOg3HGjVQjY-AFPDI2TvHe6BFE/edit#gid=0
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Import Item</b></div>
        <form action="{{ route('openbalance.upload_item') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        https://docs.google.com/spreadsheets/d/1wLl92FCHNXZmlC25yzvv_wvqmHfj0gjVaJWJLLJmfMw/edit#gid=0
    </div>
</section>
<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>IMPORT WO-2 REGU EVIS</b></div>
        <form action="{{ route('openbalance.upload_wo2_regu') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        https://docs.google.com/spreadsheets/d/18QMdAnFfmZQ32JOuuuGRug8RF59_J1I3ax-CnOdmwgU/edit#gid=0
    </div>
</section>

<section class="panel">
    <div class="card-body">
        <div class="mb-3"><b>Upload Excel Data Stock Opname</b></div>
        <form action="{{ route('openbalance.upload_stock_opname') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('post')
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <input type="file" name="file">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </div>
        </form><hr>
        <a href="https://docs.google.com/spreadsheets/d/19q7IVVNssvtzjnw-KJoIUNMVJkYXKelViNXy-WYhDho/edit?hl=id#gid=0" target="_blank">Contoh Template Excel <span class="fa fa-download"></span></a>
    </div>
</section>
@endsection

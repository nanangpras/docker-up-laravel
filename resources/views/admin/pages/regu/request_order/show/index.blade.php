@extends('admin.layout.template')

@section('title', 'Detail Request Order')

@section('header')
    <style>
        ol.switches {
            padding-left: 0 !important;
        }

        .switches li {
            position: relative;
            counter-increment: switchCounter;
            list-style-type: none;
        }

        .switches li:not(:last-child) {
            border-bottom: 1px solid var(--gray);
        }

        .switches label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 7px
        }

        .switches span:last-child {
            position: relative;
            width: 50px;
            height: 26px;
            border-radius: 15px;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.4);
            background: var(--gray);
            transition: all 0.3s;
        }

        .switches span:last-child::before,
        .switches span:last-child::after {
            content: "";
            position: absolute;
        }

        .switches span:last-child::before {
            left: 1px;
            top: 1px;
            width: 24px;
            height: 24px;
            background: var(--white);
            border-radius: 50%;
            z-index: 1;
            transition: transform 0.3s;
        }

        .switches span:last-child::after {
            top: 50%;
            right: 8px;
            width: 12px;
            height: 12px;
            transform: translateY(-50%);
            background: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/uncheck-switcher.svg);
            background-size: 12px 12px;
        }

        .switches [type="checkbox"] {
            position: absolute;
            left: -9999px;
        }

        .switches [type="checkbox"]:checked+label span:last-child {
            background: var(--green);
        }

        .switches [type="checkbox"]:checked+label span:last-child::before {
            transform: translateX(24px);
        }

        .switches [type="checkbox"]:checked+label span:last-child::after {
            width: 14px;
            height: 14px;
            /*right: auto;*/
            left: 8px;
            background-image: url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/checkmark-switcher.svg);
            background-size: 14px 14px;
        }

    </style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col"><a href="{{ route('regu.index') }}?kategori={{ $regu }}"><i class="fa fa-arrow-left"></i> Kembali</a></div>
    <div class="col-7 text-center">
        <b>DETAIL REQUEST ORDER</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body p-2">
        <table class="table table-sm table-striped mb-0">
            <tbody>
                <tr>
                    <th>Nomor SO</th>
                    <td>{{ $data->itemorder->no_so }}</td>
                </tr>
                <tr>
                    <th>Customer</th>
                    <td>{{ $data->itemorder->nama }}</td>
                </tr>
                <tr>
                    <th>Item</th>
                    <td>{{ $data->nama_detail }}</td>
                </tr>
                <tr>
                    <th>Qty/Berat</th>
                    <td><span class="status status-info">{{ number_format($data->qty) }} pcs</span> <span class="status status-success">{{ number_format($data->berat, 2) }} kg</span></td>
                </tr>
                @if ($data->part || $data->bumbu)
                <tr>
                    <th>Tambahan</th>
                    <td>
                        <ul class="mb-0 pl-3">
                            @if ($data->part) <li>PARTING {{ $data->part }}</li> @endif
                            @if ($data->bumbu) <li>BUMBU {{ $data->bumbu }}</li> @endif
                        </ul>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="card-body">
        @if (!$freestock)
        <div class="text-center">
            <h3 class="text-center">DATA TELAH DIHAPUS</h3>
            @php
                $fs         = App\Models\Freestock::where('orderitem_id', $data->id)->withTrashed()->first();
                if ($fs) {
                    $fstemp     = App\Models\FreestockTemp::where('freestock_id', $fs->id)->withTrashed()->first();
                }
            @endphp
            {{-- {{ $fstemp }} --}}
            <a href="{{route('regu.index',['key' =>'history_delete_hp'])}}&produksi={{ $fstemp->freestock_id }}" class="btn btn-sm btn-info " target="_blank">History Delete
                </a>
        </div>
        {{-- <a href="{{route('regu.index',['key' =>'history_delete_bb'])}}&produksi={{ $data->id }}" class="btn btn-sm btn-info" target="_blank">History Delete Bahan Baku</a> --}}
        @endif

        <div id="hasil_harian"></div>

    </div>
</section>
@endsection

@section('footer')
<script>
$(".select2").select2({
    theme: "bootstrap4"
});
</script>

<script>
$("#ambil_bahanbaku").load("{{ route('regu.index', ['key' => 'bahan_baku']) }}&kat={{ $regu }}&orderitem={{ $data->id }}");
$("#selesaikan").load("{{ route('regu.index', ['key' => 'selesaikan']) }}&kat={{ $regu }}&orderitem={{ $data->id }}");
$("#hasil_harian").load("{{ route('regu.index', ['key' => 'hasil_harian']) }}&kat={{ $regu }}&orderitem={{ $data->id }}") ;

$(document).on('click', '.hapus_bb', function() {
    var row_id = $(this).data('id');

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
            key: 'bahan_baku'
        },
        success: function(data) {
            showNotif('Bahan baku diambil berhasil dihapus');
            $("#ambil_bahanbaku").load("{{ route('regu.index', ['key' => 'bahan_baku']) }}&kat={{ $regu }}&orderitem={{ $data->id }}");
            $("#selesaikan").load("{{ route('regu.index', ['key' => 'selesaikan']) }}&kat={{ $regu }}");
        }
    });
})

$(document).on('click', '.selesaikan', function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var plastik         =   $('#plastik').val();
    var jumlah_plastik  =   $('#jumlah_plastik').val();
    var parting         =   $('#part').val();
    var berat           =   $('#berat').val();
    var jumlah          =   $('#jumlah').val();

    var itemtunggir     =   $('#itemtunggir').val();
    var berattunggir    =   $('#berattunggir').val();
    var jumlahtunggir   =   $('#jumlahtunggir').val();

    var itemmaras       =   $('#itemmaras').val();
    var beratmaras      =   $('#beratmaras').val();
    var jumlahmaras     =   $('#jumlahmaras').val();

    var itemlemak       =   $('#itemlemak').val();
    var beratlemak      =   $('#beratlemak').val();
    var jumlahlemak     =   $('#jumlahlemak').val();

    var sub_item        =   $('#sub_item').val();
    var customer        =   $('#customer').val();

    var tujuan_produksi =   $('input[name="tujuan_produksi"]:checked').val();
    var selonjor        =   $("#selonjor:checked").val() ;

    var additional = [];
    $('.additional').each(function() {
        if ($(this).is(":checked")) {
            additional.push($(this).val());
        }
    });

    if (tujuan_produksi == 1) {
        if (plastik != 'Curah') {
            if (jumlah_plastik > 0) {
                var next = 'TRUE';
            }
        } else {
            // if (jumlah_plastik > 0) {
                var next = 'TRUE';
            // }
        }
    } 
    else {
        if (plastik == 'Curah') {
            var next = 'TRUE';
        } else {
            if (jumlah_plastik > 0) {
                var next = 'TRUE';
            }
        }
    }


    if (next != 'TRUE') {
        showAlert('Lengkapi data plastik');
    } else {

        $.ajax({
            url: "{{ route('regu.store') }}",
            method: "POST",
            data: {
                jenis           :   "{{ $regu }}",
                item            :   "{{ $data->item_id }}",
                berat           :   berat,
                jumlah          :   jumlah,
                itemtunggir     :   itemtunggir,
                berattunggir    :   berattunggir,
                jumlahtunggir   :   jumlahtunggir,
                itemlemak       :   itemlemak,
                beratlemak      :   beratlemak,
                jumlahlemak     :   jumlahlemak,
                itemmaras       :   itemmaras,
                beratmaras      :   beratmaras,
                jumlahmaras     :   jumlahmaras,
                parting         :   parting,
                plastik         :   plastik,
                jumlah_plastik  :   jumlah_plastik,
                additional      :   additional,
                tujuan_produksi :   tujuan_produksi,
                sub_item        :   sub_item,
                customer        :   customer,
                selonjor        :   selonjor,
                orderitem       :   "{{ $data->id }}"
            },
            success: function(data) {
                $.ajax({
                    url: "{{ route('regu.store') }}",
                    method: "POST",
                    data: {
                        key         :   'selesaikan',
                        jenis       :   "{{ $regu }}",
                        orderitem   :   "{{ $data->id }}"
                    },
                    success: function(data) {
                        if (data.status == 400) {
                            showAlert(data.msg);
                        } else {
                            showNotif('Produksi berhasil disimpan');
                            window.location.reload("{{ route('regu.request_view', $data->id) }}");
                        }
                    }
                });
            }
        });
    }
})

$(document).on('click', '.approved', function() {
    var id = $(this).data('id');

    $(".approved").hide();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('regu.store') }}",
        method: "POST",
        data: {
            key: 'selesaikan',
            jenis: "{{ $regu }}",
            cast: 'approve',
            id: id,
        },
        success: function(data) {
            window.location.reload("{{ route('regu.request_view', $data->id) }}");
            showNotif('Produksi berhasil diselesaikan');
        }
    });
})

$(document).on('click', '.removed', function() {
    var id = $(this).data('id');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('regu.store') }}",
        method: "POST",
        data: {
            key: 'selesaikan',
            jenis: "{{ $regu }}",
            cast: 'removed',
            id: id,
        },
        success: function(data) {
            // window.location.reload("{{ route('regu.request_view', $data->id) }}");
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                showNotif('Berhasil', setInterval(function(){
                    window.location.reload("{{ route('regu.request_view', $data->id) }}");
                }, 1000));
            }
            // console.log(data)
        }
    });
})
</script>
@endsection

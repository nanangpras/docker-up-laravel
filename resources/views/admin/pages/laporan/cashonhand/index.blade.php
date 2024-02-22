@extends('admin.layout.template')

@section('title', 'Cash on Hand')

@section('footer')
<script>
    $("#data_view").load("{{ route('dashboard.cashonhand', ['key' => 'view']) }}&regu={{ $request->regu }}&tanggal_awal={{ $request->tanggal_awal }}&tanggal_akhir={{ $request->tanggal_akhir }}")

    $("#cari_data").on('keyup', function() {
        var cari    =   $(this).val() ;
        $("#data_view").load("{{ route('dashboard.cashonhand', ['key' => 'view']) }}&regu={{ $request->regu }}&tanggal_awal={{ $request->tanggal_awal }}&tanggal_akhir={{ $request->tanggal_akhir }}&page=1&cari=" + cari);
    });
</script>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('dashboard') }}"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col font-weight-bold text-uppercase text-center">
        Cash on Hand
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">
        <table class="table default-table">
            <tbody>
                <tr>
                    <th>Product</th>
                    <td>
                        @if ($request->regu == 'whole') KARKAS @endif
                        @if ($request->regu == 'parting') PARTING @endif
                        @if ($request->regu == 'marinasi') M @endif
                        @if ($request->regu == 'boneless') BONELESS @endif
                        @if ($request->regu == 'byproduct') BY PRODUCT @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $request->tanggal_awal . ($request->tanggal_awal == $request->tanggal_akhir ? '' : (' - ' . $request->tanggal_akhir)) }}</td>
                </tr>
            </tbody>
        </table>
        <input type="text" placeholder="Pencarian..." id="cari_data" class="form-control" autocomplete="off">
    </div>
</section>

<div id="data_view"></div>
@endsection

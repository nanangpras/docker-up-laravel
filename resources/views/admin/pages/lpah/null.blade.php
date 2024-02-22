@php
$isi = 0;
$kosong = 0;
$kr_null = 0;
$kr_isi = 0;
@endphp
@foreach ($data as $list)
    @php
        if ($list->type == 'isi') {
            $isi += $list->berat;
            $kr_isi += 1;
        } else {
            $kosong += $list->berat;
            $kr_null += 1;
        }
    @endphp
@endforeach
<div class="row mb-3">
    <div class="col-12">
        <table>
            <thead>
                <tr>
                    <th style="width: 60px">Keranjang Isi</th>
                    <th class="text-center" style="width: 20px">:</th>
                    <th>{{ $kr_isi }}</th>
                </tr>
                <tr>
                    <th>Berat</th>
                    <th class="text-center" style="width: 20px">:</th>
                    <th>{{ number_format($isi, 2) }} Kg</th>
                </tr>

            </thead>

        </table>
    </div>

</div>

<table class="table table-sm default-table dataTable">
    <thead>
        <tr>
            <th class="text-center">Total</th>
            @if ($produksi->lpah_status == 2)
                <th class="text-center">Edit</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td class="text-center">{{ $row->berat }}</td>
                @if ($produksi->lpah_status == 2)
                    <td class="text-center">
                        <button class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal"
                            data-target="#modal{{ $row->id }}">
                            <i class="fa fa-edit"></i>
                        </button>
                    </td>
                @endif
            </tr>
            @if ($produksi->lpah_status == 2)
                <div class="modal fade" id="modal{{ $row->id }}" tabindex="-1"
                    aria-labelledby="modal{{ $row->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="form-group text-center">
                                    <b>EDIT DATA</b>
                                </div>

                                <div class="form-group">
                                    <div class="form-group">
                                        <div class="small">Tipe</div>
                                        <select name="tipe_timbang" class="form-control"
                                            id="tipe_timbang{{ $row->id }}">
                                            <option value="isi" {{ $row->type == 'isi' ? 'selected' : '' }}>Isi
                                            </option>
                                            <option value="kosong" {{ $row->type == 'kosong' ? 'selected' : '' }}>
                                                Kosong</option>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                Berat
                                                <input type="number" id="berat{{ $row->id }}" name="berat"
                                                    class="form-control" value="{{ $row->berat }}"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <button type="button" data-id="{{ $row->id }}"
                                        class="edit_cart btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </tbody>
</table>

@section('header')
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
@stop

@section('footer')
    <!-- <script type="text/javascript" src="{{asset('')}}plugin/DataTables/datatables.min.js"></script> -->
@stop

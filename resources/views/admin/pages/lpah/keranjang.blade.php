@php
$isi = 0;
$kosong = 0;
$kr_null = 0;
$kr_isi = 0;
@endphp
@foreach ($produksi->prodlpah as $list)
    @php
        if ($list->type == 'isi') {
            $isi += $list->berat;
            $kr_isi += $list->berat > 0 ? 1 : 0;
        } else {
            $kosong += $list->berat;
            $kr_null += $list->berat > 0 ? 1 : 0;
        }
    @endphp
@endforeach
<div class="row mb-3">
    <div class="col-lg-6 col-12 pr-lg-1">
        {{ $kr_isi }}x Timbang Keranjang Isi<br>
        {{ $kr_null }}x Timbang Keranjang Kosong
    </div>
    <div class="col-lg-6 col-12 pl-lg-1">
        <table>
            <tbody>
                <tr>
                    <td>Berat Isi</td>
                    <td class="text-center" style="width: 20px">:</td>
                    <td class="text-right">{{ number_format($isi, 2) }} Kg</td>
                </tr>
                <tr>
                    <td>Berat Kosong</td>
                    <td class="text-center">:</td>
                    <td class="text-right">{{ number_format($kosong, 2) }} Kg</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="outer" style="max-height: 450px; overflow:scroll">
    <div class="row">
        <div class="col pr-1">
            <table class="table table-sm default-table">
                <thead>
                    <tr>
                        <th colspan="2">KERANJANG ISI</th>
                    </tr>
                    <tr>
                        <th class="text-center">Total</th>
                        @if (Auth::user()->account_role == 'superadmin')
                            <th class="text-center">Edit</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produksi->prodlpahisi as $row)
                        @if ($row->berat > 0)
                            <tr>
                                <td class="text-center">{{ number_format($row->berat, 2) }}</td>
                                @if (Auth::user()->account_role == 'superadmin')
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal"
                                            data-target="#modal{{ $row->id }}" onclick="edit_keranjang_isi({{$row->id}})">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                            @if (Auth::user()->account_role == 'superadmin')
                                <div class="modal fade" id="modal{{ $row->id }}" tabindex="-1"
                                    aria-labelledby="modalLabel" aria-hidden="true">
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
                                                            <option value="isi" {{ $row->type == 'isi' ? 'selected' : '' }}>
                                                                Isi
                                                            </option>
                                                            <option value="kosong"
                                                                {{ $row->type == 'kosong' ? 'selected' : '' }}>Kosong
                                                            </option>
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
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="button" data-id="{{ $row->id }}"
                                                        class="edit_cart btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col pl-1">
            <table class="table table-sm default-table">
                <thead>
                    <tr>
                        <th colspan="2">KERANJANG KOSONG</th>
                    </tr>
                    <tr>
                        <th class="text-center">Total</th>
                        @if (Auth::user()->account_role == 'superadmin')
                            <th class="text-center">Edit</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produksi->prodlpahkosong as $row)
                        @if ($row->berat > 0)
                            <tr>
                                <td class="text-center">{{ number_format($row->berat, 2) }}</td>
                                @if (Auth::user()->account_role == 'superadmin')
                                    <td class="text-center">
                                        <button class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal"
                                            data-target="#modal{{ $row->id }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                            @if (Auth::user()->account_role == 'superadmin')
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
                                                            <option value="isi" {{ $row->type == 'isi' ? 'selected' : '' }}>
                                                                Isi
                                                            </option>
                                                            <option value="kosong"
                                                                {{ $row->type == 'kosong' ? 'selected' : '' }}>Kosong
                                                            </option>
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
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="button" data-id="{{ $row->id }}"
                                                        class="edit_cart btn btn-primary">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
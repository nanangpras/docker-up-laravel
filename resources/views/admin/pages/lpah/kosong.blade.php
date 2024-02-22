@php
$kosong = 0;
$kr_null = 0;
@endphp
@foreach ($data as $list)
    @php
        if ($list->berat > 0) {
            $kosong += $list->berat;
            $kr_null += 1;
        }
    @endphp
@endforeach

<div class="row mb-3">
    <div class="col-12">
        <b>{{ $kr_null }}x Timbang</b><br>
        <b>Total : {{ number_format($kosong, 2) }} Kg</b>
    </div>
</div>

<div style="overflow: auto; height:470px">
    @foreach ($data as $i => $row)
        <div class="border-bottom p-2">
            {{ COUNT($data) - (++$i) + 1 }} ||
            {{ number_format($row->berat, 2) }}
            <br> <small class="status status-info mt-1 small">{{ $row->created_at }}</small>
            @if ($produksi->lpah_status == 2)
                <button class="btn btn-neutral float-right btn-sm p-0 px-1" data-toggle="modal"
                    data-target="#modal{{ $row->id }}">
                    <i class="fa fa-edit"></i>
                </button>
            @endif
        </div>
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
                                    <select name="tipe_timbang" class="form-control" id="tipe_timbang{{ $row->id }}">
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
                                                class="form-control" value="{{ $row->berat }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="button" data-id="{{ $row->id }}" class="edit_cart btn btn-blue">Save
                                    changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

@extends('admin.layout.template')

@section('title', 'Detail Purchasing')

@section('footer')
<script>
$('.select2').select2({
    theme: 'bootstrap4'
});
</script>
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('purchasing.show', $data->id) }}" class="btn btn-outline btn-sm btn-back"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="col text-center"><b>INFORMASI SOPIR</b></div>
    <div class="col"></div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <div class="small">No PO</div>
                {{ $data->no_po }}

                <div class="small">Supplier</div>
                {{ $data->purcsupp->nama }}
                <div class="row mt-2 mb-2">
                    <div class="col">
                        <div class="small">Tipe PO</div>
                        <small class="text-uppercase">{{ $data->type_po }}</small>
                    </div>
                    <div class="col">
                        <div class="small">Ukuran</div>
                        {{ $data->ukuran_ayam }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="small">Daerah</div>
                        <span class="text-capitalize">{{ $data->wilayah_daerah }}</span>
                    </div>
                    <div class="col">
                        <div class="small">Ekspedisi</div>
                        <span class="text-capitalize">{{ $data->type_ekspedisi }}</span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="small">Harga Awal</div>
                        {{ number_format($data->harga_penawaran) }}
                    </div>
                    <div class="col">
                        <div class="small">Harga Deal</div>
                        {{ number_format($data->harga_deal) }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col">
                        <div class="small">Jumlah DO</div>
                        {{ number_format($data->jumlah_po) }}
                    </div>
                    <div class="col">
                        <div class="small">Tanggal Potong</div>
                        {{ date('d/m/y', strtotime($data->tanggal_potong)) }}
                    </div>
                </div>
                <div class="mb-2">
                    <div class="small">PO ITEM</div>

                    @if ($data->type_po == 'PO LB')
                        <ol style="margin:0; padding-left: 15px">
                            <li>AYAM UKURAN {{ $data->ukuran_ayam }}
                                @foreach ($data->purchasing_item as $item)
                                    <br><span
                                        class="status status-success">{{ number_format($item->jumlah_ayam) }}
                                        Ekor</span> || <span
                                        class="status status-info">{{ number_format($item->berat_ayam, 2) }}
                                        Kg</span>
                                @endforeach
                            </li>
                        </ol>
                    @else
                        <ol style="margin:0; padding-left: 15px">
                            @foreach ($data->purchasing_item as $item)
                                <li>
                                    {{ \App\Models\Item::item_sku($item->item_po)->nama }}<br><span
                                        class="status status-success">{{ number_format($item->jumlah_ayam) }}
                                        Ekor</span> || <span
                                        class="status status-info">{{ number_format($item->berat_ayam, 2) }}
                                        Kg</span>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <form action="{{ route('purchasing.detailpost', [$data->id, $produksi->id]) }}" method="post">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">Supir/Kernek</label>
                                <div class="col-sm-12">
                                    <select name="supir" id="data_supir" class="form-control" required>
                                        <option value="" disabled selected hidden>Pilih Supir</option>
                                        @if ($produksi->sc_pengemudi_id)
                                        <option value="{{ $produksi->sc_pengemudi_id }}" {{ old('supir') ? ((old('supir') == $produksi->sc_pengemudi_id) ? 'selected' : '') : 'selected' }}>{{ App\Models\Driver::find($produksi->sc_pengemudi_id)->nama }}</option>
                                        @endif
                                        @foreach ($supir as $id => $row)
                                        <option value="{{ $id }}" {{ old('supir') == $id ? 'selected' : '' }} >{{ $row }}</option>
                                        @endforeach
                                    </select>
                                    @error('supir') <div class="small text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            {{-- <div class="form-group row">
                                <label class="col-sm-12 col-form-label">Ekor DO</label>
                                <div class="col-sm-12">
                                    <input type="number" name="ekor_do" class="form-control" placeholder="Ekor DO" value="{{ old('ekor_do') ?? $produksi->sc_ekor_do }}" autocomplete="off">
                                    @error('ekor_do') <div class="small text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">Berat DO</label>
                                <div class="col-sm-12">
                                    <input type="text" name="berat_do" class="form-control" placeholder="Berat DO" value="{{ old('berat_do') ?? $produksi->sc_berat_do }}" autocomplete="off">
                                    @error('berat_do') <div class="small text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div> --}}
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">Nama Kandang</label>
                                <div class="col-sm-12">
                                    <input type="text" name="nama_kandang" class="form-control" placeholder="Nama Kandang" value="{{ old('nama_kandang') ?? $produksi->sc_nama_kandang }}" autocomplete="off" required>
                                    @error('nama_kandang') <div class="small text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">No Polisi</label>
                                <div class="col-sm-12">
                                    <input type="text" name="no_polisi" class="form-control" placeholder="No Polisi" value="{{ old('no_polisi') ?? $produksi->sc_no_polisi }}" autocomplete="off" required>
                                    @error('no_polisi') <div class="small text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-12 col-form-label">Alamat Kandang (Toleransi Susut Maksimum)</label>
                                <div class="col-sm-12">
                                    <select name="target" id="target" class="form-control select2" data-placeholder="Pilih Alamat" data-width="100%" required>
                                        <option value=""></option>
                                        @foreach ($target as $row)
                                        <option value="{{ $row->id }}" {{ $produksi->target_id == $row->id ? 'selected' : '' }}>{{ $row->alamat }} ({{ $row->target }})</option>
                                        @endforeach
                                    </select>
                                    @error('target') <div class="small text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-blue form-control">Simpan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>


@stop

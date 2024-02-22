@extends('admin.layout.template')

@section('title', 'Penyiapan')

@section('content')
<div class="row mb-4">
    <div class="col"></div>
    <div class="col-6 py-1 text-center">
        <b>SIAP KIRIM EXPORT</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <form action="{{ route('penyiapan.siapKirimExport') }}" method="GET">
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label for="awal">Awal</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="awal" class="form-control" value="{{ date('Y-m-d') }}"
                            id="awal" placeholder="Cari...." autocomplete="off">
                    </div>
                    <div class="col">
                        <label for="akhir">Akhir</label>
                        <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                            min="2023-01-01" @endif name="akhir" class="form-control" value="{{ date('Y-m-d') }}"
                            id="akhir" placeholder="Cari...." autocomplete="off">
                    </div>

                    <div class="col">
                        <label for="jenistanggal">Jenis Tanggal</label>
                        <select name="jenistanggal" id="jenistanggal" class="form-control">
                            <option value="do">DO</option>
                            <option value="so">SO</option>
                        </select>
                    </div>

                    <div class="col">
                        <label for="jenisitem">Item</label>
                        <select name="jenisitem" id="jenisitem" class="form-control">
                            <option value="semua">Semua</option>
                            <option value="nonsampingan">Non Sampingan</option>
                            <option value="sampingan">Sampingan</option>
                        </select>
                    </div>

                        <div class="col">
                            <label for="jenis">Jenis</label>
                            <select class="form-control" name="jenis" id="jenis">
                                <option value="">Semua</option>
                                <option value="fresh">fresh</option>
                                <option value="frozen">frozen</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="ket">Keterangan</label>
                            <select class="form-control" name="keterangan" id="ket">
                                <option value="">Semua</option>
                                <option value="tidak-terkirim">Tidak terkirim</option>
                                <option value="terkirim">Terkirim</option>
                                <option value="batal">Batal</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="stats">Order Status</label>
                            <select name="status" id="stats" class="form-control">
                                <option value="" disabled>Pilih</option>
                                <option value="1">Selesai</option>
                                <option value="0">Belum Terselesaikan</option>
                            </select>
                        </div>
                        <div class="col">
                            <br>
                            <button type="submit" class="btn btn-blue">Export</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@stop
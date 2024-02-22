@extends('admin.layout.template')

@section('title', 'Detail Nekropsi')

@section('content')
<div class="row mb-4">
    <div class="col">
        <a href="{{ route('qc.index') }}" class="btn btn-outline btn-sm btn-back"> <i class="fa fa-arrow-left"></i>
            Back</a>
    </div>
    <div class="col text-center">
        <b>Detail Nekropsi</b>
    </div>
    <div class="col"></div>
</div>

<section class="panel">
    <div class="card-body">

        <table class="table table-sm">
            <tbody>
                <tr>
                    <td>Hari/Tanggal</td>
                    <td>{{ $isi->created_at ?? '' }}</td>
                </tr>
                <tr>
                    <td>Asal Farm</td>
                    <td>{{ $data->sc_nama_kandang }}</td>
                </tr>
                <tr>
                    <td>Supir</td>
                    <td>{{ $data->sc_pengemudi }}</td>
                </tr>
                <tr>
                    <td>Jumlah Ayam Mati</td>
                    <td>{{ $data->antem->ayam_mati }} ekor</td>
                </tr>
            </tbody>
        </table>

        <form action="{{ route('qc.nekropsi_post', $data->id) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="form-group">
                        Kondisi Umum
                        <input type="text" name="kondisi_umum" class="form-control" id="kondisi_umum" placeholder="Tuliskan Kondisi Umum" value="{{ $isi->kondisi_umum ?? '' }}" autocomplete="off">
                        @error("kondisi_umum") <div class="small text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        Sistem Rangka
                        <input type="text" name="sistem_rangka" class="form-control" id="sistem_rangka" placeholder="Tuliskan Sistem Rangka" value="{{ $isi->sistem_rangka ?? '' }}" autocomplete="off">
                        @error("sistem_rangka") <div class="small text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        Sistem Otot
                        <input type="text" name="sistem_otot" class="form-control" id="sistem_otot" placeholder="Tuliskan Sistem Otot" value="{{ $isi->sistem_otot ?? '' }}" autocomplete="off">
                        @error("sistem_otot") <div class="small text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        Sistem Kekebalan Tubuh
                        <input type="text" name="sistem_kekebalan_tubuh" class="form-control" id="sistem_kekebalan_tubuh" placeholder="Tuliskan Sistem Otot" value="{{ $isi->sistem_kekebalan_tubuh ?? '' }}" autocomplete="off">
                        @error("sistem_kekebalan_tubuh") <div class="small text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        Mata
                        <input type="text" name="mata" class="form-control" id="mata" placeholder="Tuliskan Mata" value="{{ $isi->sp_mata ?? '' }}" autocomplete="off">
                        @error("mata") <div class="small text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-lg-8 mb-4">
                    <div class="form-group">
                        <b>Sistem Pernafasan</b>

                        <div class="row">
                            <div class="col-6 col-md pr-1">
                                <div class="form-group">
                                    Hidung
                                    <input type="text" name="hidung" class="form-control" id="hidung" placeholder="Tuliskan Hidung" value="{{ $isi->sp_hidung ?? '' }}" autocomplete="off">
                                    @error("hidung") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-6 col-md px-md-1 pl-1">
                                <div class="form-group">
                                    Trachea
                                    <input type="text" name="trachea" class="form-control" id="trachea" placeholder="Tuliskan Trachea" value="{{ $isi->sp_trakea ?? '' }}" autocomplete="off">
                                    @error("trachea") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-6 col-md px-md-1 pr-1">
                                <div class="form-group">
                                    Paru Paru
                                    <input type="text" name="paru_paru" class="form-control" id="paru_paru" placeholder="Tuliskan Paru Paru" value="{{ $isi->sp_paru ?? '' }}" autocomplete="off">
                                    @error("paru_paru") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-6 col-md-auto pl-1">
                                <div class="form-group">
                                    Air Sacl (Kantung Udara)
                                    <input type="text" name="air_sacl" class="form-control" id="air_sacl" placeholder="Tuliskan Air Sacl (Kantung Udara)" value="{{ $isi->sp_kantung_udara ?? '' }}" autocomplete="off">
                                    @error("air_sacl") <div class="small text-danger">{{ message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <b>Sistem Pencernaan</b>

                        <div class="row">
                            <div class="col-6 col-md pr-1">
                                <div class="form-group">
                                    Tembolok
                                    <input type="text" name="tembolok" class="form-control" id="tembolok" placeholder="Tuliskan Tembolok/Proventrieulus" value="{{ $isi->sp_tembolok ?? '' }}" autocomplete="off">
                                    @error("tembolok") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-6 col-md px-md-1 pl-1">
                                <div class="form-group">
                                    Proventriculus
                                    <input type="text" name="proventriculus" class="form-control" id="proventriculus" placeholder="Tuliskan Proventriculus" value="{{ $isi->sp_proventriculus ?? '' }}" autocomplete="off">
                                    @error("proventriculus") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-6 col-md px-md-1 pl-1">
                                <div class="form-group">
                                    Ventriculus/Lambung
                                    <input type="text" name="lambung" class="form-control" id="lambung" placeholder="Tuliskan Ventriculus/Lambung" value="{{ $isi->sp_lambung ?? '' }}" autocomplete="off">
                                    @error("lambung") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-6 col-md px-md-1 pl-1">
                                <div class="form-group">
                                    Usus
                                    <input type="text" name="usus" class="form-control" id="usus" placeholder="Tuliskan Usus" value="{{ $isi->sp_usus ?? '' }}" autocomplete="off">
                                    @error("usus") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <b>Lainnya</b>

                        <div class="row">
                            <div class="col pr-1">
                                <div class="form-group">
                                    Jantung
                                    <input type="text" name="jantung" class="form-control" id="jantung" placeholder="Tuliskan Jantung" value="{{ $isi->sp_jantung ?? '' }}" autocomplete="off">
                                    @error("jantung") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col pl-1">
                                <div class="form-group">
                                    Hati
                                    <input type="text" name="hati" class="form-control" id="hati" placeholder="Tuliskan Hati" value="{{ $isi->sp_hati ?? '' }}" autocomplete="off">
                                    @error("hati") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col pl-1">
                                <div class="form-group">
                                    Limpa
                                    <input type="text" name="limpa" class="form-control" id="limpa" placeholder="Tuliskan Limpa" value="{{ $isi->sp_limpa ?? '' }}" autocomplete="off">
                                    @error("limpa") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col pl-1">
                                <div class="form-group">
                                    Bursa Fabricius
                                    <input type="text" name="fabricius" class="form-control" id="fabricius" placeholder="Tuliskan fabricius" value="{{ $isi->sp_fabricius ?? '' }}" autocomplete="off">
                                    @error("fabricius") <div class="small text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        Diagnosa
                        <textarea name="diagnosa" class="form-control" id="diagnosa" rows="3">{{ $isi->diagnosa ?? '' }}</textarea>
                        @error("diagnosa") <div class="small text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group text-right">
                <a href="{{ route('pdfnekropsi', $data->id) }}" class="btn btn-warning" target="_blank">PDF</a>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>

    </div>
</section>

@stop

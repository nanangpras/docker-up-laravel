<form action="{{ route('qc.antem', $data->id) }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                Basah Bulu
                <select name="basah_bulu" id="basah_bulu" class="form-control">
                    <option value="" selected hidden disabled>Pilih Basah Bulu</option>
                    <option value="0" {{ $antem->basah_bulu == '0' ? 'selected' : '' }}>Kering </option>
                    
                     {{-- @if(env('NET_SUBSIDIARY')=='EBA') --}}
                    @php 
                        $basah = explode(",",DataOption::getOption('qc_basah_bulu'));
                    @endphp
                    @foreach($basah as $b)
                        <option value="{{$b}}" {{ $antem->basah_bulu == $b ? 'selected' : '' }}>Basah {{$b}}kg </option>
                    @endforeach
                    
                    {{-- @else

                     <option value="25" {{ $antem->basah_bulu == '25' ? 'selected' : '' }}>Basah 25kg
                    </option>
                    <option value="50" {{ $antem->basah_bulu == '50' ? 'selected' : '' }}>Basah 50kg
                    </option>
                    <option value="75" {{ $antem->basah_bulu == '75' ? 'selected' : '' }}>Basah 75kg
                    </option>
                    <option value="100" {{ $antem->basah_bulu == '100' ? 'selected' : '' }}>Basah
                        100kg</option>
                    @endif --}}

                </select>
                @error('basah_bulu') <div class="small text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                Keaktifan
                <select name="keaktifan" id="keaktifan" class="form-control">
                    <option value="" selected hidden disabled>Pilih Keaktifan</option>
                    <option value="aktif" {{ $antem->keaktifan == 'aktif' ? 'selected' : '' }}>
                        Aktif</option>
                    <option value="tidak_aktif"
                        {{ $antem->keaktifan == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif/Lemas
                    </option>
                </select>
                @error('keaktifan') <div class="small text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                Cairan Mata/Hitung
                <select name="cairan" id="cairan" class="form-control">
                    <option value="" selected hidden disabled>Pilih Cairan</option>
                    <option value="ada" {{ $antem->cairan == 'ada' ? 'selected' : '' }}>Ada </option>
                    <option value="tidak_ada" {{ $antem->cairan == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                </select>
                @error('cairan') <div class="small text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                Keranjang
                <select name="kebersihanKeranjang" id="kebersihanKeranjang" class="form-control">
                    <option value="" selected hidden disabled>Bersih/Kotor</option>
                    <option value="bersih" {{ $data->lpah_kebersihan_keranjang == 'bersih' ? 'selected' : '' }}>Bersih </option>
                    <option value="kotor" {{ $data->lpah_kebersihan_keranjang == 'kotor' ? 'selected' : '' }}>Kotor</option>
                </select>
                @error('kebersihanKeranjang') <div class="small text-danger">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {{-- Ayam    --}}
                {{-- <div class="border rounded mb-3">
                    <div class="row">
                        <div class="col">
                            <div class="p-2">Sakit</div>
                        </div>
                        <div class="col"><input type="number" name="ayam_sakit" value="{{ $antem->ayam_sakit ?? '' }}" class="form-control" placeholder="Jumlah"></div>
                    </div>
                </div> --}}

                <div class="form-group">
                    Nama Penyakit
                    <input type="text" name="ayam_sakit_nama" value="{{ $antem->ayam_sakit_nama ?? '' }}" class="form-control" placeholder="Nama Penyakit" autocomplete="off">
                    @error('ayam_sakit_nama') <div class="small text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="border rounded mb-3 background-grey-2">
                    <div class="row">
                        <div class="col">
                            <div class="p-2">Mati</div>
                        </div>
                        <div class="col"><input type="number" name="ayam_mati" value="{{ $antem->ayam_mati ?? '' }}" class="form-control px-2" placeholder="..." readonly></div>
                    </div>
                </div>

                <div class="border rounded mb-3 background-grey-2">
                    <div class="row">
                        <div class="col">
                            <div class="p-2">Mati kg</div>
                        </div>
                        <div class="col"><input type="number" name="ayam_mati_kg" step="0.01" value="{{ $antem->ayam_mati_kg ?? '' }}" class="form-control px-2" placeholder="..." readonly></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mt-2 text-right">
        <button type="submit" class="btn btn-primary">UPDATE DATA</button>
    </div>
</form>

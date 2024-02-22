<hr>
<h5>Hasil Produksi</h5>
@if (($request->jenis == 'boneless') || ($request->jenis == 'boneless_frozen'))
    <div class="form-group border-top pt-3 radio-toolbar">
        <div class="mb-1">Pilih Jenis Boneless</div>
        <div class="row">
            <div class="col-4 pr-1">
                <div class="form-group">
                    <input type="radio" name="tipe" value="broiler" class="tipe" id="broiler">
                    <label for="broiler">Broiler</label>
                </div>
            </div>
            <div class="col-4 px-1">
                <div class="form-group">
                    <input type="radio" name="tipe" value="pejantan" class="tipe" id="pejantan">
                    <label for="pejantan">Pejantan</label>
                </div>
            </div>
            <div class="col-4 pl-1">
                <div class="form-group">
                    <input type="radio" name="tipe" value="kampung" class="tipe" id="kampung">
                    <label for="kampung">Kampung</label>

                </div>
            </div>
            <div class="col-4 pr-1">
                <div class="form-group">
                    <input type="radio" name="tipe" value="parent" class="tipe" id="parent">
                    <label for="parent">Parent</label>
                </div>
            </div>
        </div>
    </div>

    <div id="jenisayam"></div>

    @if ($request->jenis == 'boneless')
        <script>
            $(document).on('change', '.tipe', function() {
                var jen = $('.tipe:checked').val();

                if (jen == 'broiler') {
                    $('#jenisayam').load("{{ route('produksi.index', ['key' => 'broiler']) }}");
                } else if (jen == 'pejantan') {
                    $('#jenisayam').load("{{ route('produksi.index', ['key' => 'pejantan']) }}");
                } else if (jen == 'kampung') {
                    $('#jenisayam').load("{{ route('produksi.index', ['key' => 'kampung']) }}");
                } else if (jen == 'parent') {
                    $('#jenisayam').load("{{ route('produksi.index', ['key' => 'parent']) }}");
                }
            });
        </script>
    @endif

    @if ($request->jenis == 'boneless_frozen')
        <script>
            $(document).on('change', '.tipe', function() {
                var jen = $('.tipe:checked').val();

                if (jen == 'broiler') {
                    $('#jenisayam').load("{{ route('produksi.index', ['key' => 'frozen_broiler']) }}");
                } else if (jen == 'pejantan') {
                    $('#jenisayam').load("{{ route('produksi.index', ['key' => 'frozen_pejantan']) }}");
                } else if (jen == 'kampung') {
                    $('#jenisayam').load("{{ route('produksi.index', ['key' => 'frozen_kampung']) }}");
                } else if (jen == 'parent') {
                    $('#jenisayam').load("{{ route('produksi.index', ['key' => 'frozen_parent']) }}");
                }
            });
        </script>
    @endif

@else
    <div class="border p-2 mb-2">
        <div class="row mb-2">
            <div class="col-9 pr-1">
                <select name="itemfree" class="form-control select2" data-width="100%" data-placeholder="Pilih Item" id="itemfree">
                    <option value=""></option>
                    @foreach (Item::daftar_sku($request->jenis) as $$list)
                        <option value="{{ $list->id }}">{{ $list->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-3 pl-1">
                <input type="number" name="berat" id="berat" class="form-control" placeholder="Berat">
            </div>
        </div>

        @if (($request->jenis == 'parting') || ($request->jenis == 'parting_marinasi'))
        <div class="form-group">
            Tuliskan Jumlah Parting
            <input type="number" name="part" class="form-control" id="part" placeholder="Tuliskan Jumlah" autocomplete="off">
        </div>
        @endif

        <div class="row">
            <div class="col-9 pr-1">
                <div class="form-group">
                    Plastik
                    <select name="plastik" id='plastik' class="form-control select2" data-width="100%" data-placeholder="Pilih Plastik">
                        <option value=""></option>
                        <option value="Curah">Curah</option>
                        @foreach($plastik as $p)
                            <option value="{{$p->id}}">{{$p->nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-3 pl-1">
                &nbsp;
                <input type="number" name="jumlah_plastik" id="jumlah_plastik" class="form-control" placeholder="Jumlah">
            </div>
        </div>

        @if ($request->jenis == 'whole_chicken')
            <div class="form-group">
                <ol class="switches">
                    <li>
                        <input type="checkbox" name="additional" class="additional" value="tunggir" id="tunggir">
                        <label for="tunggir">
                            <span>Tunggir</span>
                            <span></span>
                        </label>
                    </li>
                    <li>
                        <input type="checkbox" name="additional" class="additional" value="lemak" id="lemak">
                        <label for="lemak">
                            <span>Lemak</span>
                            <span></span>
                        </label>
                    </li>
                    <li>
                        <input type="checkbox" name="additional" class="additional" value="maras" id="maras">
                        <label for="maras">
                            <span>Maras</span>
                            <span></span>
                        </label>
                    </li>
                </ol>
            </div>
        @endif

        <button type="button" class="input_freestock btn btn-sm btn-primary btn-block">Submit xx</button>

    </div>

    <script>
        $('.select2').select2({
            theme: 'bootstrap4'
        })
    </script>
@endif

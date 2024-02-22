<div class="row">
    <div class="col-6">
        @php
            $idorder = 0;
        @endphp
        @foreach ($bonless as $i => $val)
            @php
                $idorder = $val->id;
            @endphp
            <div class="card card-primary card-outline">
                <div class="card-header">
                    Customer : {{ $val->nama }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            @php
                                $qty = 0;
                                $berat = 0;
                            @endphp
                            @foreach (Order::item_order($val->id, 'bonless') as $i => $item)
                                @php
                                    $qty += $item->qty;
                                    $berat += $item->berat;
                                @endphp
                                <div class="form-group">
                                    <div class="btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-primary btn-block text-left"
                                            for="od-{{ $item->id }}">
                                            <input type="checkbox" autocomplete="off" id="od-{{ $item->id }}"
                                                onclick='' data-jenis='' value="{{ $item->id }}" name="purchase">
                                            {{ $item->nama_detail }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="col-6">
        <div class="card card-primary card-outline">
            @php
                $idbahan = 0;
            @endphp
            @foreach ($bahanbaku as $j => $bacok)
                @php
                    $idbahan = $bacok->id;
                @endphp
                <div class="card-header">
                    Bahan Baku {{ $bacok->id }}
                </div>
            @endforeach
            <div class="card-body">
                @foreach ($bhnbb as $i => $bb)
                    <div class="radio-toolbar">
                        <input type="radio" id="do-{{ $bb[0]->id }}" onclick='' data-jenis=''
                            value="{{ $bb[0]->id }}" name="purchase" required>
                        <label for="do-{{ $bb[0]->id }}">
                            {{ $bb[0]->item_name }} <span class=" pull-right">{{ $bb[0]->qty_item }} |
                                {{ $bb[0]->berat_item }}</span>

                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <hr>
            <div class="card card-primary card-outline">
                <div class="card-header">
                    Free Stock
                </div>
                <div class="card-body">
                    @foreach ($free as $i => $free)
                        <div class="radio-toolbar">
                            <input type="radio" id="do-{{ $free->id }}" onclick='' data-jenis=''
                                value="{{ $free->id }}" name="purchase" required>
                            <label for="do-{{ $free->id }}">
                                {{ $free->chillitem->nama }} <span class=" pull-right">{{ $free->qty_item }} |
                                    {{ $free->berat_item }}</span>

                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#myModal">Free
                Stock</button>
        </div>

    </div>
</div>

<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Free Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Item</label>
                    <select name="item" class="form-control select2" id="item">
                        <option value="">Pilih </option>
                        @foreach ($item as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>
                    @error('item') <div class="small text-danger">{{ message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label> Qty</label>
                    <input type="number" name="qtyfree" class="form-control" min="0" id="qtyfree"
                        placeholder="Tuliskan " value="" autocomplete="off">
                    @error('qtyfree') <div class="small text-danger">{{ message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label> Berat</label>
                    <input type="number" name="berat" class="form-control" min="0" id="berat" placeholder="Tuliskan "
                        value="" autocomplete="off">
                    @error('berat') <div class="small text-danger">{{ message }}</div> @enderror
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary freestock">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.select2').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#myModal')
    })

</script>

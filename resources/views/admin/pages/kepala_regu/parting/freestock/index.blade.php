<div class="accordion" id="accrode">
    @foreach ($fresh as $row)
        <div class="card">
            <div class="card-header" id="heading{{ $row->id }}">
                <span data-toggle="collapse" data-target="#datacollapse{{ $row->id }}" aria-expanded="true"
                    aria-controls="datacollapse{{ $row->id }}">
                    {{ $row->nomor_freestock }}
                    <span class="pull-right">
                        @foreach ($row->listfreestock as $status)
                            {{ $status->freechiller->status_free }}
                        @endforeach
                    </span>
                </span>
            </div>

            <div id="datacollapse{{ $row->id }}" class="collapse" aria-labelledby="heading{{ $row->id }}"
                data-parent="#accrode">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="radio-toolbar pt-1">
                                <div id="liststock{{ $row->id }}"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border p-2 mb-2">
                                <div class="row mb-2">
                                    <div class="col-9">
                                        <select name="item" class="form-control select2" id="item{{ $row->id }}">
                                            <option value="" disabled selected hidden>Pilih Produk Parting</option>
                                            @foreach ($items as $id => $list)
                                                <option value="{{ $id }}">{{ $list }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <input type="number" name="qty" id="qty{{ $row->id }}"
                                            class="form-control" placeholder="QTY">
                                    </div>
                                </div>

                                <button type="button" data-id="{{ $row->id }}"
                                    class="input_freestock btn btn-sm btn-primary btn-block">Submit</button>
                            </div>

                            <div id="tempoarary{{ $row->id }}"></div>

                            <button type="button" data-id="{{ $row->id }}"
                                class="selesai_freestock btn btn-success btn-block mt-3">Selesaikan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $("#liststock{{ $row->id }}").load(
                "{{ route('kepalaregu.partingfreestock', ['list' => $row->id]) }}");
            $("#tempoarary{{ $row->id }}").load(
                "{{ route('kepalaregu.partingfreestock', ['id' => $row->id]) }}");

        </script>
    @endforeach
</div>

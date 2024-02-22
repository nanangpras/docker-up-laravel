<tr>
    <td>{{ $loop->iteration + ($data->currentpage() - 1) * $data->perPage() }}</td>
    <td>{{ $row->no_pr ?? '#' }}</td>
    <td>{{ $row->divisi ?? '#' }}</td>
    <td><span class="status status-info">{{ count($item_list) }} Item</span></td>
    <td><button class="btn btn-primary" data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true"
        aria-controls="collapse{{ $row->id }}">Expand Detail
    </button>
    </td>
</tr>
<td colspan="5"><div id="collapse{{ $row->id }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionListPO">
    <div class="p-0">
        @foreach ($item_list as $item)
        <div class="p-1 mt-1">
            <input type="hidden" id="id_history_perPR{{ $item->id }}" value="{{ $item->id }}" data-id="" class="id_history_perPR">
            <div class="cursor mb-1" data-toggle="" data-target="#accept{{ $item->id }}" id="validateheader{{ $item->id }}" onclick="validateheader({{ $item->id }},{{ $row->id }}, {{ $item->item_id }})">
                <div class="border p-1">
                    <div class="row">
                        <div class="col pr-1">
                            <div class="row">
                                <div class="col">
                                    {{ $item->item->sku ?? '' }}. {{ $item->item->nama ?? '' }}<br>
                                </div>
                                <div class="col-auto text-right">
                                    Qty : {{ number_format($item->sisa) }} {{ $item->unit }}<br>
                                    {{-- Harga : Rp {{ number_format($item->harga) }} --}}
                                </div>
                            </div>
                        </div>
                        <div class="col-auto pl-1">
                            <i class="fa fa-chevron-right mt-3 text-info"></i>
                        </div>

                    </div>
                </div>
                @if ($item->keterangan || $item->link_url)
                <div class="border border-top-0 px-1">
                    @if ($item->keterangan)
                    <div>Keterangan : {{ $item->keterangan }}</div>
                    @endif
                    @if ($item->link_url)
                    <div>URL : {{ $item->link_url }}</div>
                    @endif
                </div>
                @endif
            </div>
        </div>
        <div class="modal fade" id="accept{{ $item->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="accept{{ $item->id }}Label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accept{{ $item->id }}Label">Item Pembelian</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="border-bottom px-2 py-1">
                            <div class="row">
                                <div class="col-3 font-weight-bold">
                                    SKU
                                </div>
                                <div class="col">
                                    {{ $item->item->sku ?? ''  }}
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom px-2 py-1">
                            <div class="row">
                                <div class="col-3 font-weight-bold">
                                    Item
                                </div>
                                <div class="col">
                                    {{ $item->item->nama ?? '' }}
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom px-2 py-1">
                            <div class="row">
                                <div class="col-3 font-weight-bold">
                                    Qty / Unit
                                </div>
                                <div class="col">
                                    {{ $item->sisa }} {{ $item->unit }}
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom px-2 py-1">
                            <div class="row">
                                <div class="col-3 font-weight-bold">
                                    Keterangan (INTERNAL)
                                </div>
                                <div class="col">
                                    {{ $item->keterangan }}
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom px-2 py-1">
                            <div class="row">
                                <div class="col-3 font-weight-bold">
                                    URL
                                </div>
                                <div class="col">
                                    {{ $item->link_url }}
                                </div>
                            </div>
                        </div>
                        <div id="historyperPR{{ $item->id }}">

                        </div>

                        <div class="row my-3">
                            <div class="col pr-1">
                                <div class="form-group">
                                    <label for="qty{{ $item->id }}">Qty</label>
                                    <div class="input-group">
                                        <input type="number" id="qty{{ $item->id }}" class="form-control rounded-0 p-1" autocomplete="off" min="1" max="{{ $item->sisa }}" placeholder="Tulis Qty">
                                        <div class="input-group-prepend">
                                        <div class="input-group-text">{{ $item->unit }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    <label for="harga{{ $item->id }}">Harga Unit</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                        <div class="input-group-text">Rp</div>
                                        </div>
                                        <input type="number" id="harga{{ $item->id }}" class="form-control rounded-0 p-1" autocomplete="off" min="0" step="0.01" placeholder="Total Harga">
                                    </div>
                                </div>
                            </div>
                        </div>


                        @php
                            $data_item = App\Models\Item::find($item->item_id);
                        @endphp
                        @if($data_item)
                        @if($data_item->category_id<23)
                        <div class="row my-3">

                            <div class="col pr-1">
                                <div class="form-group">
                                    <label for="berat{{ $item->id }}">Berat DO</label>
                                    <div class="input-group">
                                        <input type="number" id="berat{{ $item->id }}" class="form-control rounded-0 p-1" autocomplete="off" min="1" placeholder="Tulis Berat">
                                        <div class="input-group-prepend">
                                        <div class="input-group-text">Kg</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    <label for="ukuran_ayam{{ $item->id }}">Ukuran Ayam</label>
                                    <div class="input-group">
                                        <select class="form-control" id="ukuran_ayam{{$item->id}}">
                                            <option value="1"> < 1.1 </option>
                                            <option value="2"> 1.1-1.3 </option>
                                            <option value="3"> 1.2-1.4 </option>
                                            <option value="4"> 1.3-1.5 </option>
                                            <option value="5"> 1.4-1.6 </option>
                                            <option value="6"> 1.7-1.9 </option>
                                            <option value="7"> 1.8-2.0 </option>
                                            <option value="8"> 1.9-2.1 </option>
                                            <option value="9"> 2.0 Up</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col pr-1">
                                <div class="form-group">
                                    <label for="jumlah_do{{ $item->id }}">Jumlah DO</label>
                                    <div class="input-group">
                                        <input type="number" id="jumlah_do{{ $item->id }}" class="form-control rounded-0 p-1" autocomplete="off" min="1" placeholder="DO">
                                        <div class="input-group-prepend">
                                        <div class="input-group-text">Mbl</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col pl-1">
                                <div class="form-group">
                                    <label for="unit_cetakan{{ $item->id }}">Harga Cetakan</label>
                                    <div class="input-group">
                                        <select class="form-control" id="unit_cetakan{{$item->id}}">
                                            <option value="1"> Kg </option>
                                            <option value="2"> Ekor/Pcs/Pack </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                        <div class="col px-1">
                            <div class="form-group">
                                <label for="keterangan{{ $item->id }}">Keterangan  <span class="red text-small">* DIISI DENGAN MEREK/SERI PRODUK (TAMPIL DI PO NS)</span></label>
                                <div class="input-group">
                                    <input type="text" id="keterangan{{ $item->id }}" class="form-control rounded-0 p-1" autocomplete="off" placeholder="Keterangan">
                                </div>
                            </div>
                        </div>
                        <div class="col px-1">
                            <div class="form-group">
                                <label for="gudang{{ $item->id }}">Gudang</label>
                                <div class="input-group">
                                    <select class="form-control" id="gudang{{$item->id}}">
                                        <option value=""> - Pilih Gudang - </option>
                                        @php
                                            $gudang = App\Models\Gudang::where('subsidiary', Session::get('subsidiary'))
                                                            ->where('code', 'not like', '%chiller%')
                                                            ->where('code', 'not like', '%storage%')
                                                            // ->where('kategori', NULL)
                                                            ->get();
                                        @endphp
                                        @foreach($gudang as $g)
                                        <option value="{{$g->netsuite_internal_id}}" @if($g->code == Session::get('subsidiary').' - Sparepart') selected @endif>  {{$g->code}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" data-id="{{ $item->id }}" class="tambah_item btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
</td>
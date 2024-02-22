<table width="100%" class="table default-table" id="warehouseRequestThawing">
    <thead>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Jam Request</th>
            <th>Regu</th>
            <th>Item</th>
            <th>Aksi</th>
            <th>Approve Thawing</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($thawing as $i => $row)
        @php
        $time = Carbon\Carbon::parse($row->created_at)->format('H:i');
        $total_qty = 0;
        $total_berat = 0;
        @endphp
        <tr class="{{ $row->deleted_at ? 'table-danger' : ($row->edited > 0 ? 'table-warning' : '') }}">
            <td>{{ ++$i }}</td>
            <td>{{ $row->id }}</td>
            <td>{{ $row->tanggal_request ?? '' }}</td>
            <td>{{ $time }} WIB</td>
            <td>
                @if($row->regu)
                    {{$row->regu}}
                @else
                    
                @endif
            </td>
            <td>
               
                @foreach (json_decode($row->item) as $i => $item)
                    @php
                        $total_qty = 0; // Inisialisasi total_qty untuk setiap iterasi item
                        $total_berat = 0; // Inisialisasi total_berat untuk setiap iterasi item
                        if (!empty($row->thawing_list)) {
                            foreach ($row->thawing_list as $list) {
                                $total_qty += $list->qty;
                                $total_berat += $list->berat;
                            }
                        }
                            if ($item->berat < $total_berat) {
                                $background_color   = '#ffff8d';
                                $color              = '#e65100';
                            } else {
                                $background_color   = '#dbeefd';
                                $color              = '#2196F3';
                            }
                    @endphp
                <div class="border-bottom p-1">
                    {{ ++$i }}. {{ App\Models\Item::find($item->item)->nama }}
                    <span class="status status-success">{{ number_format($item->qty) }} Pcs</span>
                    <span class="status" style="background-color: {{$background_color}}; color: {{$color}};">{{ number_format($item->berat, 2) }} kg</span>
                    @if($item->keterangan)
                    <span class="status status-warning">{{$item->keterangan ?? ''}}</span>
                    @endif
                </div>
                
                @endforeach
            </td>
            <td>
                @if ($row->deleted_at)
                <span class="status status-danger">VOID</span>
                @else
                @if (COUNT($row->thawing_list) < 1) <button class="btn btn-sm btn-warning" data-toggle="modal"
                    data-target="#editRequest{{ $row->id }}">Edit</button>
                    <button class="btn btn-sm btn-danger batal_thawing" data-id="{{ $row->id }}">Batal</button>
                    @endif
                    @endif
            </td>
            <td>
                <div style="font-size: x-small">
                    @foreach ($row->thawing_list as $list)
                    <div class="border-bottom p-1">
                        <div>{{ $list->gudang->nama ?? 'ITEM TIDAK ADA' }}</div>
                            <span class="p-1 status status-success">{{ number_format($list->qty) }} pcs</span>

                        @if($item->berat >= $total_berat) 
                            <span class="p-1 status status-info">{{ number_format($list->berat, 2) }} kg</span>
                        @else
                            <span class="p-1 status" style="background-color: {{$background_color}}; color: {{$color}}">{{ number_format($list->berat, 2) }} kg</span>
                        @endif
                            <br> Pemenuhan : {{$list->created_at}}
                    </div>
                    @endforeach
                </div>

            </td>
        </tr>

        @endforeach
    </tbody>
</table>

@foreach ($thawing as $i => $row)
<form action="{{ route('thawing.update') }}" method="post">
    @csrf @method('patch') <input type="hidden" name="id" value="{{ $row->id }}">
    <div class="modal fade" id="editRequest{{ $row->id }}" tabindex="-1"
        aria-labelledby="editRequest{{ $row->id }}Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRequest{{ $row->id }}Label">Edit Request Thawing</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @foreach (json_decode($row->item) as $i => $item)
                    <div class="border-bottom pb-2 mb-2">
                        <div class="row mb-2">
                            <div class="col-8 pr-1">
                                Item
                                <select name="item[]" class="form-control select2">
                                    @foreach ($data_item as $v)
                                    <option value="{{ $v->id }}" {{ $v->id == $item->item ? 'selected' : '' }}>{{ $v->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2 px-1">
                                Qty
                                <input type="number" value="{{ $item->qty }}" name="qty[]" class="form-control">
                            </div>
                            <div class="col-2 pl-1">
                                Berat
                                <input type="number" step="0.01" value="{{ $item->berat }}" name="berat[]"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                                    min="2023-01-01" @endif value="{{ $item->tanggal_request ?? '' }}"
                                    placeholder="Keterangan" autocomplete="off" name="tanggal_request[]"
                                    class="form-control">
                            </div>
                            <div class="col">
                                <input type="text" value="{{ $item->keterangan }}" placeholder="Keterangan"
                                    autocomplete="off" name="keterangan[]" class="form-control">
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div class="form-group">
                        Regu
                        <select id="regu" name="regu" class="form-control">
                            <option value="">- Semua -</option>
                            <option value="byproduct" {{ $row->regu === 'byproduct' ? 'selected' : '' }}>Byproduct</option>
                            <option value="parting" {{ $row->regu === 'parting' ? 'selected' : '' }}>Parting</option>
                            <option value="whole" {{ $row->regu === 'whole' ? 'selected' : '' }}>Whole</option>
                            <option value="marinasi" {{ $row->regu === 'marinasi' ? 'selected' : '' }}>Marinasi</option>
                            <option value="boneless" {{ $row->regu === 'boneless' ? 'selected' : '' }}>Boneless</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Ubah</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endforeach

<script>
    $('.select2').select2({
    theme: 'bootstrap4',
})
</script>

<script>
    function filterWarehouseRequestThawing () {
            var mulai   =   $("#mulai").val() ;
            var sampai  =   $("#sampai").val() ;
            $('#warehouse-requestthawing').load("{{ route('thawing.requestthawing') }}?mulai=" + mulai + "&sampai=" + sampai);
        
    }

$(".batal_thawing").on('click', function() {
    var id  =   $(this).data('id') ;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".batal_thawing").hide() ;

    $.ajax({
        url: "{{ route('thawing.update') }}",
        method: "PATCH",
        data: {
            id  :   id,
            key :   'batal'
        },
        success: function(data) {
            if (data.status == 400) {
                showAlert(data.msg);
            } else {
                filterWarehouseRequestThawing();
                showNotif(data.msg);
            }
            $(".batal_thawing").show() ;
        }
    });
})
</script>
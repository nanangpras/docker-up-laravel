@if ($dataIdRetail)
<section class="panel">
    <div class="accordion" id="accordion_po">
        <div class="card">
            <div class="card-header" id="headingOne{{ $keys }}">
                <div data-toggle="collapse" data-target="#collapse{{ $keys }}" aria-expanded="true" aria-controls="collapse{{ $keys }}">
                    5 History Terakhir Pembelian {{ App\Models\Customer::find($customer)->nama }}
                    <span class="status status-danger">*Pilih salah satu</span>
                </div>
            </div>
            <div id="collapse{{ $keys }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordion_sampingan{{ $keys }}">
                <div class="card-body">
                    <div class="border-bottom">
                        <table class="table default-table">
                            <thead>
                                <tr>
                                    {{-- <th><input type="checkbox" id="historyCheckbox" class="form-control" onchange="setHistoryCheckbox(this.id)"></th>   --}}
                                    <th></th>  
                                    <th>Jumlah</th>
                                    <th>NO SO</th>
                                    <th>Tgl SO</th>
                                    <th>Tgl Kirim</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataIdRetail as $row)
                                    <tr>
                                        <td><input type="checkbox" name="countCheckboxHistory-{{ $keys }}" id="{{ $row->id }}" class="form-control" onclick="setHistoryCheckbox({{ $customer }}, {{ $keys }}, {{ $row->id }})"></td>
                                        <td>{{ COUNT($row->listItem) ?? '' }}</td>
                                        <td>{{ $row->no_so }}</td>
                                        <td>{{ $row->tanggal_so }}</td>
                                        <td>{{ $row->tanggal_kirim }}</td>
                                        <td><button class="btn btn-primary" data-toggle="collapse" data-target="#collapse{{ $row->id }}" aria-expanded="true"
                                            aria-controls="collapse{{ $row->id }}">Detail
                                        </button>
                                        </td>
                                    </tr>
                                    <td colspan="6"><div id="collapse{{ $row->id }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionListSOSampingan">
                                        <table class="table default-table">
                                            <thead>
                                                <tr>
                                                    {{-- <th><input type="checkbox" id="historyListCheckbox" class="form-control" onchange="setHistoryListCheckbox(this.id)"></th>   --}}
                                                    <th>Nama Item</th>
                                                    <th>Qty</th>
                                                    <th>Berat</th>
                                                    <th>Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($row->listItem as $item)
                                                <tr>
                                                    {{-- <td><input type="checkbox" name="countCheckboxListHistory[]" id="{{ $row->id }}" class="form-control" onchange="setHistoryListCheckbox(this.id)"></td> --}}
                                                    <td>{{ $item->item_nama ?? '' }}</td>
                                                    <td class="text-right">{{ $item->qty }}</td>
                                                    <td class="text-right">{{ number_format($item->berat,2) }} </td>
                                                    <td class="text-right">{{ number_format($item->harga,2,',', '.') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                @empty
                                    <p>Tidak ada data history pembelian</p>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    function setHistoryCheckbox(customer, keys, id){
        $('input[name="countCheckboxHistory-'+keys+'"]').on('click', function(e) {
            $('input[name="countCheckboxHistory-'+keys+'"]').prop('checked', false);
            $(this).prop('checked', true);
        });
        if($(this).prop('checked', true)) {
            // console.log(customer, keys, id)
            setCheckbox(customer, keys, id)
        }
    }
</script>

@else
<span class="status status-info mt-2">*Tidak ada data history SO {{ App\Models\Customer::find($customer)->nama }}</span>
@endif
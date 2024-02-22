<table class="table default-table">
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Ekor/Pcs/Pack</th>
            <th class="text-center">Berat Bersih</th>
            @if (env("NET_SUBSIDIARY", "EBA") == "EBA")
            <th class="text-center">Prosentase</th>
            @endif
            <th class="text-center">Hitung</th>
            @if ($prod->evis_status == 2)
                <th class="text-center">Aksi</th>
            @elseif (User::setIjin('superadmin'))
                <th class="text-center">Aksi</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @php
            $total_hitung   =   0;
            $total_berat    =   0;
        @endphp
        @foreach ($data as $i => $row)
        @php
            $total_hitung   +=  $row->total_item;
            $total_berat    +=  $row->berat_stock;
        @endphp
            <tr>
                <td class="text-center">{{ ++$i }}</td>
                <td class="text-center">{{ $row->eviitem->nama }}</td>
                <td class="text-center">{{ $row->total_item }}</td>
                <td class="text-center">{{ $row->berat_stock }}</td>
                @if (env("NET_SUBSIDIARY", "EBA") == "EBA")
                {{--
                1211840002 = Kepala 5% -> 6%
                1211830001 = Kaki 3% -> 4%
                1211820005 = Usus 5% -> 6%
                1211810007 = Hati Hancur 0,1% -> 0 - 0,1
                1211820004 = Tembolok 0,8% -> 0,9%
                1211810005 = Hati Ampela 6% -> 0,7%
                1211820002 = Jantung 0,1% -> 0 - 0,1
                --}}
                <td class="text-center">
                    @php
                        $prosentase =   ($row->berat_stock / $prod->lpah_berat_terima) * 100 ;
                    @endphp
                    {{-- Kepala --}}
                    @if ($row->eviitem->sku == 1211840002)
                        @if (($prosentase >= 5) && ($prosentase <= 6))
                            <span class='text-success'>
                        @else
                            <span class='text-danger'>
                        @endif
                    @endif
                    {{-- Kaki --}}
                    @if ($row->eviitem->sku == 1211830001)
                        @if (($prosentase >= 3) && ($prosentase <= 4))
                            <span class='text-success'>
                        @else
                            <span class='text-danger'>
                        @endif
                    @endif
                    {{-- Usus --}}
                    @if ($row->eviitem->sku == 1211820005)
                        @if (($prosentase >= 5) && ($prosentase <= 6))
                            <span class='text-success'>
                        @else
                            <span class='text-danger'>
                        @endif
                    @endif
                    {{-- Hati Hancur --}}
                    @if ($row->eviitem->sku == 1211810007)
                        @if (($prosentase >= 0) && ($prosentase <= 0.1))
                            <span class='text-success'>
                        @else
                            <span class='text-danger'>
                        @endif
                    @endif
                    {{-- Tembolok --}}
                    @if ($row->eviitem->sku == 1211820004)
                        @if (($prosentase >= 0.8) && ($prosentase <= 0.9))
                            <span class='text-success'>
                        @else
                            <span class='text-danger'>
                        @endif
                    @endif
                    {{-- Hati Ampela --}}
                    @if ($row->eviitem->sku == 1211810005)
                        @if (($prosentase >= 6) && ($prosentase <= 7))
                            <span class='text-success'>
                        @else
                            <span class='text-danger'>
                        @endif
                    @endif
                    {{-- Jantung --}}
                    @if ($row->eviitem->sku == 1211820002)
                        @if (($prosentase >= 0) && ($prosentase <= 0.1))
                            <span class='text-success'>
                        @else
                            <span class='text-danger'>
                        @endif
                    @endif
                    {{ number_format($prosentase, 2) }}%
                    </span>
                </td>
                @endif
                <td class="text-center">{{ $row->jenis_evis }}</td>
                @if ($prod->evis_status == 2)
                    <td class="text-center">
                        <button type="button" class="btn btn-primary btn-sm p-0 px-1 edit_cart" data-kode="{{ $row->id }}"><i class="fa fa-edit"></i></button>
                        @if (Auth::user()->account_role == 'superadmin')
                            <button class="btn btn-danger btn-sm p-0 ml-2 hapus_evis" data-id="{{ $row->id }}">
                                <div class="fa fa-trash-o"></div>
                            </button>
                        @endif
                    </td>
                @elseif(User::setIjin('superadmin'))
                    <td class="text-center">

                        <button type="button" class="btn btn-primary btn-sm p-0 px-1" data-toggle="modal"
                            data-target="#modal{{ $row->id }}">
                            <i class="fa fa-edit"></i>
                        </button>
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"></td>
            <th class="text-center">{{ number_format($total_hitung) }}</th>
            <th class="text-center">{{ number_format($total_berat, 2) }}</th>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

@foreach ($data as $i => $row)
    <div class="modal fade" id="modal{{ $row->id }}" tabindex="-1"
        aria-labelledby="modal{{ $row->id }}Label" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('evis.editevis') }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal{{ $row->id }}Label">EDIT EVIS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="idedit" value="{{ $row->id }}">
                        <label for="">Item : {{ $row->eviitem->nama }}</label>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    QTY
                                    <input type="number" name="qty" class="form-control" value="{{ $row->total_item }}">
                                </div>
                            </div>

                            <div class="col">
                                <div class="form-group">
                                    BERAT
                                    <input type="number" name="berat" class="form-control" value="{{ $row->berat_item }}" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="form-group">
                            <label for="">Alasan</label>
                            <input type="text" name="alasan" class="form-control" id="alasan" placeholder="Tuliskan " value="" autocomplete="off">
                            @error("alasan") <div class="small text-danger">{{ message }}</div> @enderror
                        </div> --}}

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">OK</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach

<script>
    $('.hapus_evis').click(function() {
        var x_code = $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('evis.deleteitem', $prod->id) }}",
            method: "DELETE",
            data: {
                x_code: x_code
            },
            success: function(data) {
                showNotif('Berhasil Hapus Data');
                $('#cart').load("{{ route('evis.cart', $prod->id) }}");
            }
        })
    })
</script>

<div class="modal-body">
        <input type="text" hidden id="idEditItem" value="">
        <div class="row mb-3">
        <div class="col">
        @if ($dataAvailable == false)
                {{-- JIKA DATA DARI PURCHASE ITEM --}}
                @php
                if ($data->ukuran_ayam == '&lt; 1.1') {
                        $ukuranAlias = '11';
                } 
                else if ($data->ukuran_ayam == '1.1 - 1.3') {
                        $ukuranAlias = '1113';
                }
                else if ($data->ukuran_ayam == '1.2 - 1.4') {
                        $ukuranAlias = '1214';
                }
                else if ($data->ukuran_ayam == '1.2 - 1.5') {
                        $ukuranAlias = '1215';
                }
                else if ($data->ukuran_ayam == '1.3 - 1.5') {
                        $ukuranAlias = '1315';
                }
                else if ($data->ukuran_ayam == '1.3 - 1.6') {
                        $ukuranAlias = '1316';
                }
                else if ($data->ukuran_ayam == '1.4 - 1.6') {
                        $ukuranAlias = '1416';
                }
                else if ($data->ukuran_ayam == '1.4 - 1.7') {
                        $ukuranAlias = '1417';
                }
                else if ($data->ukuran_ayam == '1.5 - 1.7') {
                        $ukuranAlias = '1517';
                }
                else if ($data->ukuran_ayam == '1.5 - 1.8') {
                        $ukuranAlias = '1518';
                }
                else if ($data->ukuran_ayam == '1.6 - 1.8') {
                        $ukuranAlias = '1618';
                }
                else if ($data->ukuran_ayam == '1.7 - 1.9') {
                        $ukuranAlias = '1719';
                }
                else if ($data->ukuran_ayam == '1.8 - 2.0') {
                        $ukuranAlias = '1820';
                }
                else if ($data->ukuran_ayam == '1.9 - 2.1') {
                        $ukuranAlias = '1921';
                }
                else if ($data->ukuran_ayam == '2.0 - 2.2') {
                        $ukuranAlias = '2022';
                }
                else if ($data->ukuran_ayam == '2.2 up') {
                        $ukuranAlias = '22';
                }

                else if ($data->ukuran_ayam == '2.0 - 2.5' || $data->ukuran_ayam == '2.0-2.5') {
                        $ukuranAlias = '2025';
                }

                else if ($data->ukuran_ayam == '2.5-3.0' || $data->ukuran_ayam == '2.5 - 3.0') {
                        $ukuranAlias = '2530';
                }

                else if ($data->ukuran_ayam == '3.0 up') {
                        $ukuranAlias = '30';
                }
                else if ($data->ukuran_ayam == '4.0 up') {
                        $ukuranAlias = '40';
                }

                
                @endphp

                <label for="ukuran">Ukuran Ayam</label>
                <input type="text" class="form-control" id="ukuran_ayam" value="{{ $data->ukuran_ayam == '&lt; 1.1' ? '<1.1' : $data->ukuran_ayam }}" readonly>
                <input type="hidden" class="form-control" id="ukuranAliasAyam" value="{{ $ukuranAlias }}" readonly>

                <label for="jenis" class="mt-2">Jenis Ayam</label>
                <input type="text" class="form-control" id="jenis_ayam" value="{{ $data->jenis_ayam }}" readonly>

                @else 
                {{-- JIKA DATA ADA DARI ADMIN EDIT --}}
                
                @php
                if ($data->type == '&lt; 1.1') {
                        $ukuranAlias = '11';
                } 
                else if ($data->type == '1.1 - 1.3') {
                        $ukuranAlias = '1113';
                }
                else if ($data->type == '1.2 - 1.4') {
                        $ukuranAlias = '1214';
                }
                else if ($data->type == '1.2 - 1.5') {
                        $ukuranAlias = '1215';
                }
                else if ($data->type == '1.3 - 1.5') {
                        $ukuranAlias = '1315';
                }
                else if ($data->type == '1.3 - 1.6') {
                        $ukuranAlias = '1316';
                }
                else if ($data->type == '1.4 - 1.6') {
                        $ukuranAlias = '1416';
                }
                else if ($data->type == '1.4 - 1.7') {
                        $ukuranAlias = '1417';
                }
                else if ($data->type == '1.5 - 1.7') {
                        $ukuranAlias = '1517';
                }
                else if ($data->type == '1.5 - 1.8') {
                        $ukuranAlias = '1518';
                }
                else if ($data->type == '1.6 - 1.8') {
                        $ukuranAlias = '1618';
                }
                else if ($data->type == '1.7 - 1.9') {
                        $ukuranAlias = '1719';
                }
                else if ($data->type == '1.8 - 2.0') {
                        $ukuranAlias = '1820';
                }
                else if ($data->type == '1.9 - 2.1') {
                        $ukuranAlias = '1921';
                }
                else if ($data->type == '2.0 - 2.2') {
                        $ukuranAlias = '2022';
                }
                else if ($data->type == '2.2 up') {
                        $ukuranAlias = '22';
                }
                else if ($data->type == '2.0 - 2.5' || $data->type == '2.0-2.5') {
                        $ukuranAlias = '2025';
                }

                else if ($data->type == '2.5-3.0' || $data->type == '2.5 - 3.0') {
                        $ukuranAlias = '2530';
                }

                else if ($data->type == '3.0 up') {
                        $ukuranAlias = '30';
                }
                else if ($data->type == '4.0 up') {
                        $ukuranAlias = '40';
                }
                
                @endphp


                <label for="ukuran">Ukuran Ayam</label>
                <input type="text" class="form-control" id="ukuran_ayam" value="{{ $data->type == '&lt; 1.1' ? '<1.1' : $data->type }}" readonly>
                <input type="hidden" class="form-control" id="ukuranAliasAyam" value="{{ $ukuranAlias }}" readonly>

                <label for="jenis" class="mt-2">Jenis Ayam</label>
                <input type="text" class="form-control" id="jenis_ayam" value="{{ $data->content }}" readonly>

                @endif

                </div>
        </div>
        <div class="row mb-3">
                @if ($dataAvailable == true)
                        @php
                        $getDataYield = App\Models\Adminedit::where('activity', 'input_yield')->where('content', $data->content)->where('type', $data->type)->first();
                        
                        if ($getDataYield) {
                                $decodeYield = json_decode($getDataYield->data);
                        }

                        @endphp

                <div class="col">
                        <label for="yield_karkas">Yield Karkas</label>
                        <input type="text" class="form-control" id="yield_karkas" placeholder="Contoh: 72% - 77%" value="{{ $decodeYield->yield_karkas }}">
                </div>
                <div class="col">
                        <label for="sku">Yield Evis</label>
                        <input type="text" class="form-control" id="yield_evis" placeholder="Contoh: 20% - 22%" value="{{ $decodeYield->yield_evis }}">
                </div>


                @else
                <div class="col">
                        <label for="yield_karkas">Yield Karkas</label>
                        <input type="text" class="form-control" id="yield_karkas" placeholder="Contoh: 72% - 77%" value="">
                </div>
                <div class="col">
                        <label for="sku">Yield Evis</label>
                        <input type="text" class="form-control" id="yield_evis" placeholder="Contoh: 20% - 22%" value="">
                </div>


                @endif
                
        </div>
</div>

<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="updateYield" data-dismiss="modal">Update</button>
</div>

<script>
$('.select2').select2({
        theme: 'bootstrap4',
});

$(document).on('click','#updateYield', function () {
        var yield_evis      = $("#yield_evis").val();
        var yield_karkas    = $("#yield_karkas").val();
        var ukuranAyam      = $("#ukuranAliasAyam").val();
        var jenisAyam       = $("#jenis_ayam").val();
        $.ajaxSetup({
        headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
        });

        $.ajax({
                url: "{{ route('item.index') }}",
                data: {
                yield_evis          : yield_evis,
                yield_karkas        : yield_karkas,
                ukuranAyam          : ukuranAyam,
                jenisAyam           : jenisAyam,
                key                 : 'updateYield'
        },
        success: function (res) {
                showNotif(res.msg);
                location.reload();
                // console.log(res)
                }
        });
        // alert(id);
        
});
</script>

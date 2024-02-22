@include('admin.pages.regu.component.subcomponent.netsuite')
@if ($ceklogdelete)
    <a href="{{ route('regu.index', ['key' => 'hasil_harian', 'subkey' => 'logdelete']) }}&tanggal={{ $request->tanggal }}&regu={{ $request->kat }}" class="btn btn-warning form-control mb-2" target="_blank">Riwayat Dibatalkan</a>
@endif
<input type="hidden" id="selected_category" value="{{$kategori}}">
@foreach($freestock as $no => $row)
    @php
        $show   = ((Auth::user()->account_role == 'superadmin') || App\Models\User::setIjin(33)) ? TRUE : (($row->user_id == Auth::user()->id) ? TRUE : FALSE) ;
    @endphp

    @if(count($row->listfreestock) > 0)
        @include('admin.pages.regu.component.subcomponent.multipledatafreestock')
    @else
        @include('admin.pages.regu.component.subcomponent.singledatafreestock')
    @endif
@endforeach

<div class="modal fade mymodal" id="hasil-edit" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="hasilLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hasilLabel">Edit Hasil Produksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('regu.editproduksi', ['key' => 'hasil_produksi']) }}" method="post">
                @csrf @method('put')
                <input type="hidden" name="x_code" value="" id="form-edit-id-hasil">
                <div class="modal-body">
                    <div class="form-group">
                        Item
                        <div><b id="form-edit-nama-hasil"></b></div>
                        <div><b id="form-edit-bumbu-hasil"></b></div>
                        <input id="form-item-id" type="hidden" value="" name="item">
                    </div>

                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                Ekor/Qty
                                <input type="number" name="qty" value="" class="form-control" id="form-edit-qty-hasil">
                            </div>
                        </div>
                        <div class="col pl-1">
                            <div class="form-group">
                                Berat
                                <input type="number" name="berat" value="" step="0.01" class="form-control" id="form-edit-berat-hasil" {{ isset($row->netsuite) && $row->netsuite ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div id="dataplastik" style="display: none">
                        <div class="row">
                            <div class="col pr-1">
                                <div class="form-group">
                                    Plastik
                                    <input type="text" disabled class="form-control" id="form-edit-plastik-hasil" {{ isset($row->netsuite) && $row->netsuite ? 'readonly' : '' }}>
                                </div>
                            </div>
                            @if ($kategori == 'parting' || $kategori == 'marinasi')
                            <div class="col-2 px-1">
                                <div class="form-group">
                                    Parting
                                    <input type="number" name="parting" class="form-control" id="form-edit-parting-hasil" {{ isset($row->netsuite) && $row->netsuite ? 'readonly' : '' }}>
                                </div>
                            </div>
                            @endif
                            <div class="col-3 pl-1">
                                <div class="form-group">
                                    Qty
                                    <input type="number" name="jumlah_plastik" class="form-control" id="form-edit-qtyplastik-hasil" {{ isset($row->netsuite) && $row->netsuite ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col pr-1">
                            <div class="form-group">
                                <label for="customers" {{ isset($row->netsuite) && $row->netsuite ? 'hidden' : '' }}>Nama Customer</label>
                                <input type="hidden" class="form-control " id="form-edit-customer-hasil">
                                <select name="customer" id="customers" class="form-control select2" data-width="100%" data-placeholder="Pilih Customer" {{ isset($row->netsuite) && $row->netsuite ? 'disabled' : '' }}>
                                    <option value=''></option>
                                    @foreach ($customer as $cus)
                                        <option value="{{ $cus->id }}">{{ $cus->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col pl-1" id="nama-bumbu">
                            <div class="form-group">
                                Nama Bumbu
                                <select name="bumbu_id" class="form-control select2" id="form-edit-bumbuid-hasil" data-width="100%" data-placeholder="Pilih Bumbu" {{ isset($row->netsuite) && $row->netsuite ? 'disabled' : '' }}>
                                    <option value=""></option>
                                    @foreach ($bumbu as $bmb)
                                    <option value="{{ $bmb->id }}">{{ $bmb->nama }} - ({{$bmb->berat}} Kg)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col pl-1" id="berat-bumbu">
                            <div class="form-group">
                                Berat Bumbu
                                <input type="text" name="bumbu_berat" class="form-control" id="form-edit-beratbumbu-hasil" {{ isset($row->netsuite) && $row->netsuite ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                Keterangan
                                <input type="text" name="keterangan" class="form-control" id="form-edit-keterangan-hasil" {{ isset($row->netsuite) && $row->netsuite ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bb-edit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalLpahLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div id="content_modal_bb_edit"></div>
    </div>
</div>

<style>
    .select2 {
        height: 30px !important
    }

    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2rem + 2px) !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 2rem;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
        line-height: 0;
    }
</style>
<script>
    $(document).ready(function() {
        // Mendapatkan URL saat ini
        var currentURL = window.location.href;

        // Membuat objek URLSearchParams dari URL
        let params = new URLSearchParams(document.location.search);

        // Mengambil nilai parameter 'kategori' dari URL
        let kategori = params.get('kategori');

        if(kategori == 'marinasi')
        {
            $('#nama-bumbu').show();
            $('#berat-bumbu').show();
        }else 
        {
            $('#nama-bumbu').hide();
            $('#berat-bumbu').hide();
        }


        $('.edit-bb-open').on('click', function(){
            var id          =   $(this).data('id');
            var nama        =   $(this).data('nama');
            var qty         =   $(this).data('qty');
            var berat       =   $(this).data('berat');
            var chillerid   =   $(this).attr('data-chillerid');

            $.ajax({
                url : "{{ route('regu.viewmodaledit', ['key' => 'viewmodaledit']) }}",
                type: "GET",
                data: {
                    id          : id,
                    nama        : nama,
                    qty         : qty,
                    berat       : berat,
                    chiller_id  : chillerid
                },
                success: function(data){
                    $('#content_modal_bb_edit').html(data);
                }
            });
        })

        $('.edit-hasil-open').on('click', function(){
            var id          =   $(this).data('id');
            var nama        =   $(this).data('nama');
            var qty         =   $(this).data('qty');
            var berat       =   $(this).data('berat');
            var item        =   $(this).data('itemid');
            var plastik     =   $(this).data('plastik');
            var kategori    =   $(this).data('kategori');
            var qtyplastik  =   $(this).data('qtyplastik');
            var customer    =   $(this).data('customer');
            var subitem     =   $(this).data('subitem');
            var parting     =   $(this).data('parting');
            var bumbu       =   $(this).data('bumbuid');
            var bumbu_berat =   $(this).data('bumbu_berat');

            $('#form-edit-id-hasil').val(id);
            $('#form-edit-nama-hasil').html(nama);
            $('#form-edit-qty-hasil').val(qty);
            $('#form-edit-berat-hasil').val(berat);
            $('#form-item-id').val(item);
            $('#form-edit-plastik-hasil').val(plastik);
            $('#form-edit-parting-hasil').val(parting);
            $('#form-edit-qtyplastik-hasil').val(qtyplastik);
            $('#form-edit-keterangan-hasil').val(subitem);
            $('#form-edit-beratbumbu-hasil').val(bumbu_berat);
            $('#form-edit-bumbuid-hasil').val(bumbu).trigger("change")

            


            $('.select2').select2({
                theme: 'bootstrap4',
                tags: true,
                dropdownParent: $(".mymodal"),
            })

            $("#customers").val(customer).trigger('change');

            $("#bumbu").val(bumbu).trigger('change');

            if (plastik) {
                document.getElementById('dataplastik').style    =   'display:block' ;
            } else {
                document.getElementById('dataplastik').style    =   'display:none' ;
            }

            
        })

    });

</script>

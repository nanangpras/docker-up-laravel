<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    Supir/Kernek
                    <select id="data_supir{{ $produksi->id }}" class="form-control select2" required>
                        <option value="" disabled selected hidden>Pilih Supir</option>
                        @if ($produksi->sc_pengemudi_id)
                        <option value="{{ $produksi->sc_pengemudi_id }}" {{ old('supir') ? ((old('supir')==$produksi->sc_pengemudi_id) ? 'selected' : '') : 'selected' }}>{{App\Models\Driver::find($produksi->sc_pengemudi_id)->nama }}</option>
                        @endif
                        @foreach ($supir as $id => $row)
                        <option value="{{ $id }}" {{ old('supir')==$id ? 'selected' : '' }}>{{ $row }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    Nama Kandang
                    <input type="text" id="nama_kandang{{ $produksi->id }}" class="form-control"
                        placeholder="Nama Kandang" value="{{ $produksi->sc_nama_kandang }}" autocomplete="off" required>
                </div>

                <div class="form-group">
                    Tanggal Potong
                    <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false"
                        min="2023-01-01" @endif id="tanggal_potong{{ $produksi->id }}" class="form-control"
                        placeholder="Tanggal Potong" value="{{ $produksi->prod_tanggal_potong }}" autocomplete="off"
                        required>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    No Polisi
                    <input type="text" id="no_polisi{{ $produksi->id }}" class="form-control" placeholder="No Polisi"
                        value="{{ $produksi->sc_no_polisi }}" style="text-transform: uppercase" autocomplete="off"
                        required>
                </div>

                <div class="form-group">
                    Alamat Kandang (Toleransi Susut Maksimum)
                    <select id="target{{ $produksi->id }}" class="form-control select2" data-placeholder="Pilih Alamat"
                        data-width="100%" required>
                        <option value=""></option>
                        @foreach ($target as $row)
                        <option value="{{ $row->id }}" {{ $produksi->target_id == $row->id ? 'selected' : '' }}>{{ $row->alamat }} ({{ $row->target }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
            <button type="button" class="input_detail btn btn-blue form-control"
                data-id="{{ $produksi->id }}">Simpan</button>
        </div>
    </div>
</div>
<script>
    $('.select2').select2({
    theme: 'bootstrap4'
});
</script>

<script>
    $(document).ready(function() {
    $('.input_detail').click(function() {
        var id              =   $(this).data("id") ;
        var data_supir      =   $("#data_supir" + id).val() ;
        var nama_kandang    =   $("#nama_kandang" + id).val() ;
        var no_polisi       =   $("#no_polisi" + id).val() ;
        var target          =   $("#target" + id).val() ;
        var tanggal_potong          =   $("#tanggal_potong" + id).val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('purchasing.store', $data->id) }}",
            method: "POST",
            data: {
                id              :   id,
                data_supir      :   data_supir,
                nama_kandang    :   nama_kandang,
                no_polisi       :   no_polisi,
                target          :   target,
                prod_tanggal_potong          :   tanggal_potong,
            },
            success: function(data) {
                if (data.status) {
                    showAlert(data.msg);
                } else {
                    $("#show").load("{{ route('purchasing.show', $data->id) }}?key=mobil&id=" + id);
                    showNotif("Detail purchasing order berhasil diperbaharui") ;
                }
            }
        });

    });

    $(document).ready(function() {
        $("#no_polisi{{ $produksi->id }}").autocomplete({
            source: function(req, res){
                $.ajax({
                    url         : "{{ route('purchasing.show', $data->id) }}",
                    dataType    : "JSON",
                    data        : {
                        q       : req.term,
                        key        : 'mobil',
                        subkey     : "nopol_autocomplete"
                    },
                    success: function(data){
                        res(data);
                    }
                });
            },
            minLength: 1
        });
    });

});

$(document).ready(function() {
    $("#no_polisi{{ $produksi->id }}").autocomplete({
        source: function(req, res){
            $.ajax({
                url         : "{{ route('purchasing.show', $data->id) }}",
                dataType    : "JSON",
                data        : {
                    q       : req.term,
                    key        : 'mobil',
                    subkey     : "nopol_autocomplete"
                },
                success: function(data){
                    res(data);
                }
            });
        },
        minLength: 1
    });
});
</script>
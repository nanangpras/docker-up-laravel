@if ($total['jumlah'] > 0)
@if($data->prodpur->type_po != 'PO Karkas')
<section class="panel">
    <div class="card-body">
        <div class="alert alert-danger" id="black_note">
            <h3 class="text-center">INFORMASI SEBELUM DISIMPAN</h3>
            <h4 class="text-center">Ada item yang tidak sesuai dengan ukuran sebenarnya.<br>Perbaiki data terlebih
                dahulu</h4><br>
            <h6 class="text-center">
                <div id="lihat_error"></div>
            </h6>
        </div>
        <form action="{{ route('grading.update', $data->id) }}" method="POST">
            @if ($data->grading_status == 1)
            @csrf @method('patch')
            <button class="btn btn-success btn-block" disabled>Simpan</button>
            @else
            @csrf @method('patch')
            <input type="date" @if(env('NET_SUBSIDIARY', 'CGL' )=='CGL' ) onkeydown="return false" min="2023-01-01"
                @endif hidden value="{{ $data->grading_selesai ? date("Y-m-d", strtotime($data->grading_selesai)) : $data->prod_tanggal_potong }}" id="tanggalGradingSimpan" name="tanggal">
            <button type="submit" class="btn btn-success btn-block">Simpan</button>
            @endif
        </form>

    </div>
</section>
@endif
@endif

<script>
    $(document).ready(function() {
        var jsonString  =   $("#error_data").val() ;
        $('#lihat_error').html(jsonString);

        if (jsonString === "") {
            document.getElementById('black_note').style =   'display:none' ;
        } else {
            document.getElementById('black_note').style =   '' ;
        }
    });

</script>
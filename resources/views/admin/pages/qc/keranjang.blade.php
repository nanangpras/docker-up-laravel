<div class="form-group">
    <div class="row">
        <div class="col">
            <label>Under</label>
            <input class="form-control" type="text" name="under" id="under" value="{{ $count['under'] }}" readonly>
        </div>
        <div class="col">
            <label>Over</label>
            <input class="form-control" type="text" name="over" id="over" value="{{ $count['over'] }}" readonly>
        </div>
        <div class="col">
            <label>Uniform</label>
            <input class="form-control" type="text" name="uniform" id="uniform" value="{{ $count['uni'] }}" readonly>
        </div>
    </div>
    * Uniformity +- 10% dari ukuran pembelian
</div>

<hr>

<div class="form-group">
    <h4>Riwayat Timbang</h4>
    <div class="row">
        @foreach ($data as $i => $qc)
            <div class="col text-center">
                <div class="box-border padding-5" style="width: 80px; margin: 5px;">{{ $qc->berat }} &nbsp;<i class="fa fa-trash text-danger hapus_unifom" data-id="{{ $qc->id }}"></i></div>
            </div>
        @endforeach
    </div>
</div>


<script>
$(document).ready(function() {
    $('.hapus_unifom').click(function() {
        var id  =   $(this).data('id');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('uniform.delete', $id) }}",
            method: "POST",
            data: {
                id  :   id,
            },
            success: function(data) {
                $('#cart').load("{{ route('uniform.cart', $id) }}");
                $('#summary').load("{{ route('uniform.summary', $id) }}");
                $("#berat").focus();
            }
        });
    });
});
</script>

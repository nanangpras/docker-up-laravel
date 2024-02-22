<style>
     .form-check {
    display: flex;
    align-items: center;
    }
    .form-check-label {
        margin-left: 10px;
        font-size: 18px;
        font-weight: 500;
    }
    .form-check .form-check-input[type=checkbox] {
        border-radius: .25em;
        height: 20px;
        width: 20px;
    }
</style>
<div class="modal-body">
    <div class="row">
        <input type="text" hidden id="idAksesItem" value="{{$id}}">
        @foreach (Item::arrayRegu() as $regu)  
            <div class="col-md-6 ">
                <div class="container">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="akses[]" value="{{ $regu['value'] }}" id="akses_{{$regu['nama']}}">
                        <label class="form-check-label" for="akses_{{$regu['nama']}}">{{ $regu['nama'] }}</label>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary" id="updateAkses" data-id="{{$id}}" data-dismiss="modal">Simpan</button>
</div>
<script>
    $(document).ready(function () {
        var id = $('#idAksesItem').val();
        var accessIds = @json($item->access);

        $('input[type="checkbox"]').each(function() {
            var akses = $(this).val();
            if(accessIds != null){
                if (accessIds.includes(akses)) {
                    $(this).prop('checked', true);
                }
            }
        });
    });

    $('#updateAkses').on('click', function() {
        var id = $(this).data('id');
        var akses = [];
        $('input[type="checkbox"]:checked').each(function() {
                akses.push($(this).val());
        });

        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        $.ajax({
            type: 'POST',
            url: 'update/akses/'+id,
            data: {
                akses     : akses,
            },
            success: function(r) {
                showNotif(r.message);
            }
        });
    });
</script>

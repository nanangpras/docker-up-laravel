<div class="row">
    <div class="col-md-3 mb-4">
        <div class="form-group">
            Tukar Mobil
            <select name="idprod_update" id="idprod_update" class="form-control">
                @foreach ($get_mobil as $item)
                    <option value="{{$item->id}}" name="idprod_update" id="idprod_update" <?php if ($data->id == $item->id) : ?>selected<?php endif; ?>>Mobil {{$item->no_urut}}  - {{$item->sc_no_polisi}} - {{ $item->sc_pengemudi }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="float-right mb-4 mt-4">
        <input type="hidden" id="idproduction" name="x_code" value="{{ $data->id }}">
        <button class="btn btn-success" id="tukar_mobil">Update</button>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-4 mb-4 px-2">
        <div class="mb-3">
            <input type="checkbox" id="keyboard">
            <label for="keyboard">Input keyboard</label>
            <input  type="text" id="berat" name="berat" class="form-control bg-white label-timbang" readonly required autocomplete="off">
        </div>
        <div id="hide_pad">
            <div class="row my-3">
                <div class="col pr-2">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="7" onclick="dis('7')" />
                </div>
                <div class="col px-1">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="8" onclick="dis('8')" />
                </div>
                <div class="col pl-2">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="9" onclick="dis('9')" />
                </div>
            </div>
            <div class="row my-3">
                <div class="col pr-2">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="4" onclick="dis('4')" />
                </div>
                <div class="col px-1">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="5" onclick="dis('5')" />
                </div>
                <div class="col pl-2">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="6" onclick="dis('6')" />
                </div>
            </div>
            <div class="row my-3">
                <div class="col pr-2">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="1" onclick="dis('1')" />
                </div>
                <div class="col px-1">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="2" onclick="dis('2')" />
                </div>
                <div class="col pl-2">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="3" onclick="dis('3')" />
                </div>
            </div>
            <div class="row my-3">
                <div class="col pr-1">
                    <input type="button" class="p-2 btn btn-red btn-block form-control tits-calculator" value="C" onclick="clr()" />
                </div>
                <div class="col-8 pl-1">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="0" onclick="dis('0')" />
                </div>
                {{-- <div class="col pl-2">
                    <input type="button" class="p-2 btn btn-default btn-block form-control tits-calculator" value="." onclick="dis('.')" />
                </div> --}}
            </div>
        </div>
        <div class="form-group mt-4" id="submit">
            <button type="button" name="type" value="isi" class="add_cart py-2 btn btn-primary btn-block">Kirim</button>
        </div>
    </div>
    <div class="col-md-8 px-1">
        <div id="summary"></div>
    </div>
</div>

<div class="border-top mt-3 pt-3">
    <div id="cart"></div>
</div>

<script>
    $("#keyboard").on('change', function() {
        if ($("#keyboard:checked").val() == 'on') {
            $("#berat").attr('readonly', false) ;
            $("#hide_pad").attr('style', 'display: none') ;

            $("#berat").keydown(function (e) {
                if (e.keyCode == 13) {
                    console.log(e.keyCode+" Saving ...");
                    saveQCUniform();
                }
            });

        } else {
            $("#berat").attr('readonly', true) ;
            $("#hide_pad").attr('style', 'display: block') ;
        }
    })

    function saveQCUniform(){
        var berat = $('#berat').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.label-timbang').val('') ;

        $.ajax({
            url: "{{ route('uniform.add', $data->id) }}",
            method: "POST",
            data: {
                berat: berat
            },
            success: function(data) {
                $('#cart').load("{{ route('uniform.cart', $data->id) }}");
                $('#summary').load("{{ route('uniform.summary', $data->id) }}");
            }
        });
    }

</script>
<script>
    //function that display value
    function dis(val) {
        let i = document.getElementById("berat").value;
        if (i.length == 3) {
            document.getElementById("berat").value = i ;
        } else {
            document.getElementById("berat").value += val ;
        }
    }

    //function that evaluates the digit and return berat
    function solve() {
        let x = document.getElementById("berat").value
        let y = eval(x)
        document.getElementById("berat").value = y
    }

    //function that clear the display
    function clr() {
        let r   =   document.getElementById("berat").value ;
        let v   =   (r / 10 ^ 0) ;
        if (v == 0) {
            document.getElementById("berat").value =   "" ;
        } else {
            document.getElementById("berat").value =   v ;
        }
    }

    function clrberat() {
        document.getElementById("berat").value = ""
    }

</script>

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
            url: "{{ route('evis.add', $data->id) }}",
            method: "POST",
            data: {
                idedit: idedit,
                row_id: row_id,
                berat: berat,
                result: result,
                part: part,
                jenis: jenis
            },
            beforeSend: function() {
                $('#add_cart').html("Loading ...");
            },
            success: function(data) {
                $("#prosentase").load("{{ route('laporan.prosentase', $data->id) }}") ;
                $('#add_cart').html("Timbang");
                $('#idedit').val("");
                $('#cart').load("{{ route('evis.cart', $data->id) }}");
                $('#hasil').load("{{ route('evis.result', $data->id) }}");
                $('#result').val('');
                $('#berat').val('');
                $('input[type="radio"]').prop('checked', false);
                $('#custom-tabs-three-summary-tab').tab('show');
            }
        });
    });

    $("#tukar_mobil").click(function () { 
        var idlama          = $("#idproduction").val();
        var idupdate    = $("#idprod_update").val();
        // alert(idupdate);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // alert(id);
        $.ajax({
            method:"POST",
            url: "{{route('qc.update')}}",
            data: {
                idlama      : idlama,
                idupdate    : idupdate,
                'key'       : 'tukar_mobil'
            },
            success: function (response) {
                if (response.status == 'success') {
                    showNotif(response.message)
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert(response.message)
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                }

            }
        });
    });
});
</script>

@if ($bahanbaku)
@if (COUNT($bahanbaku->listfreestock))
    {{-- <label><input type="checkbox" id="netsuite_send"> <span class="status status-danger" style="font-size: 15px;"><b>Tidak Proses WO</b></span> </label> --}}
    <button type="submit" class="btn-lg mt-1 btn btn-primary btn-block selesaikanevis btnHiden" data-id="{{ $bahanbaku->id }}">Simpan</button>

    <script>
    $('.selesaikanevis').click(function() {
        var freestock_id    =   $(this).data('id');
        var beratprod       =   $('#beratprod').val();
        var beratbb         =   $('#beratbb').val();
        var total           =   beratprod / beratbb * 100;
        // var netsuite_send   =   "TRUE";

        $(".selesaikanevis").hide() ;
        showNotif('Menunggu produksi disimpan');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // if (total <= 85) {
        //     showNotif('Bahan Baku dan Hasil Produksi Belum Sesuai');
        // } else if(total > 125) {
        //     showNotif('Bahan Baku dan Hasil Produksi Melebihi Batas Kewajaran');
        // } else {

            //  if($('#netsuite_send').get(0).checked) {
            //     // something when checked
            //     netsuite_send = "TRUE";
            // } else {
            //     // something else when not
            //     netsuite_send = "FALSE";
            // }

            // console.log(netsuite_send)

            // return false;

            $.ajax({
                url: "{{ route('evis.peruntukanselesai') }}",
                method: 'POST',
                data: {
                    freestock_id: freestock_id,
                    // netsuite_send: netsuite_send
                },
                success: function(data) {
                    if (data.status == 400) {
                        showAlert(data.msg);
                    } else {
                        showNotif('Berhasil Disimpan');
                        $(".selesaikanevis").show() ;
                        window.location.reload("{{ route('evis.peruntukan') }}") ;
                    }
                }
            })
        // }
    })
    </script>
    @endif
@endif

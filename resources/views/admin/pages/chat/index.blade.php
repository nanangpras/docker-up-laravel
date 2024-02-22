<button class="open-button" onclick="openForm()">Chat <span id="new_chat"></span></button>

<div class="chat-popup" id="chatForm">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs bg-white" role="tablist">
        <li role="presentation" class="active p-2"><a href="#riwayat" aria-controls="riwayat" role="tab" data-toggle="tab">Riwayat</a></li>
        <li role="presentation" class="p-2"><a href="#tulis" aria-controls="tulis" role="tab" data-toggle="tab">Tulis</a></li>
        <li role="presentation" class="p-2"><a href="#read" aria-controls="read" role="tab" data-toggle="tab">Read</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="tulis">
            <div class="form-container">
                <div class="form-group">
                    <b>Tujuan</b>
                    <select name="pengguna" id='pengguna' class="form-control form-control-sm">
                        <option value="" disabled hidden selected>Pilih User</option>
                        @foreach (User::where('id', '!=', Auth::user()->id)->get() as $row)
                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                </div>

                <b>Pesan</b>
                <textarea placeholder="Tuliskan pesan.." name="msg" id='msg'></textarea>

                <div class="text-right">
                    <button type="submit" class="btn kirim_pesan btn-success">Send</button>
                    <button type="button" class="btn btn-danger cancel" onclick="closeForm()">Close</button>
                </div>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane active bg-white p-2" id="riwayat">
            <div class="form-container" id="riwayat_chat"></div>

            <div class="text-right">
                <button type="button" class="btn btn-success mt-3" onclick="openNewChat()">Chat Baru</button>
                <button type="button" class="btn btn-danger cancel mt-3" onclick="closeForm()">Close</button>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane bg-white p-2" id="read">
            <div id="msg-read"></div>

            <input type="hidden" id="master-id">
        </div>
    </div>

</div>


<script>

function openNewChat(){
    $('#riwayat').removeClass('active');
    $('#tulis').addClass('active');
    $('#read').removeClass('active');
}

function openForm() {
  document.getElementById("chatForm").style.display = "block";
}

function closeForm() {
  document.getElementById("chatForm").style.display = "none";
}

$('#riwayat_chat').load("{{ route('dashboard.chat') }}");
$('#new_chat').load("{{ route('dashboard.new_chat') }}");

$(document).ready(function() {
    $(document).on('click', '.kirim_pesan', function() {
        var pengguna    =   $("#pengguna").val() ;
        var msg         =   $("#msg").val() ;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('sendchat') }}",
            method: "POST",
            data: {
                pengguna    : pengguna,
                msg         : msg,
            },
            success: function(data) {
                $("#pengguna").val("");
                $("#msg").val("");
                $('#riwayat_chat').load("{{ route('dashboard.chat') }}");
                showNotif('Chat berhasil dikirim');
            }
        });
    });
});
</script>

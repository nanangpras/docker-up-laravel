<div class="scroll-outer">
    <div class="scroll-inner">

        <div id="list-user">
            @foreach ($lawan_bicara as $row)

                @php
                    $chat_id = \App\Models\Chat::where('sender_id', $row)
                        ->orWhere('receiver_id', $row)
                        ->orderBy('id', 'desc')
                        ->first();
                    $lawan = \App\Models\User::find($row);
                @endphp

                @if ($chat_id)
                    <!-- List lawan bicara -->
                    <div class="border-bottom p-md-1" onclick="return readChat('{{ $row }}')">
                        <span class="pull-right">{{ date('d/m/y H:i:s', strtotime($chat_id->created_at)) }}</span>
                        <b>{{ $lawan->name }}</b>
                        <br>
                        @if ($chat_id->sender_id == Auth::user()->id) You : @endif

                        {{ $chat_id->content }}
                        @if ($chat_id->receiver_id == Auth::user()->id)
                            @if ($chat_id->status == '1')
                                <span class="pull-right badge badge-danger">New</span>
                            @endif
                        @else
                            @if ($chat_id->status == '1')
                                <span class="pull-right badge badge-blue">Sent</span>
                            @else
                                <span class="pull-right badge badge-success">Read</span>
                            @endif
                        @endif
                    </div>

                @endif


            @endforeach
        </div>

    </div>

</div>


<script>

    function backToChat(){
        $('#riwayat').addClass('active');
        $('#tulis').removeClass('active');
        $('#read').removeClass('active');
    }

    function readChat(id) {

        $.ajax({
            url: "{{ url('admin/dashboard/read_chat') }}/" + id,
        }).done(function(response) {
            console.log("read " + id)
            $("#msg-read").html(response);
            $('#new_chat').load("{{ route('dashboard.new_chat') }}");

            $('#riwayat').removeClass('active');
            $('#tulis').removeClass('active');
            $('#read').addClass('active');
            $('#master-id').val(id);
        });
    }

    function submitMsg(id) {

        console.log(id);
        var pengguna = $("#pengguna" + id).val();
        var msg = $("#msg" + id).val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('sendchat') }}",
            method: "POST",
            data: {
                pengguna: pengguna,
                msg: msg,
            },
            success: function(data) {
                $("#msg" + id).val("");
                $('#riwayat_chat').load("{{ route('dashboard.chat') }}");
                showNotif('Chat berhasil dikirim');
                readChat(id);
            }
        });
    };
</script>




<style>

    .msg_receive {
        padding-left: 0;
        margin-left: 0;
    }

    .msg_sent {
        padding-bottom: 20px !important;
        margin-right: 0;
    }

    .messages {
        padding: 10px;
        border-radius: 2px;
        box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.2);
        max-width: 100%;
    }

    .messages.msg_sent {
        background: rgb(184, 248, 255);
    }

    .messages.msg_receive {
        background: rgb(255, 254, 237);
    }

    .messages>p {
        font-size: 13px;
        margin: 0 0 0.2rem 0;
    }

    .messages>time {
        font-size: 11px;
        color: #555;
    }

    .msg_container {
        padding: 10px;
        overflow: hidden;
        display: flex;
        max-width: 100%;
    }

    .msg_container img {
        width: 30px;
    }

    .avatar {
        position: relative;
    }

    .base_receive>.avatar:after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border: 5px solid #FFF;
        border-left-color: rgba(0, 0, 0, 0);
        border-bottom-color: rgba(0, 0, 0, 0);
    }

    .base_sent {
        justify-content: flex-end;
        align-items: flex-end;
    }

    .base_sent>.avatar:after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 0;
        border: 5px solid white;
        border-right-color: transparent;
        border-top-color: transparent;
        box-shadow: 1px 1px 2px rgba(black, 0.2); // not quite perfect but close
    }

    .msg_sent>time {
        float: right;
    }



    .msg_container_base::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        background-color: #F5F5F5;
    }

    .msg_container_base::-webkit-scrollbar {
        width: 12px;
        background-color: #F5F5F5;
    }

    .msg_container_base::-webkit-scrollbar-thumb {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
        background-color: #555;
    }

</style>

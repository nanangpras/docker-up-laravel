
<div class="read-chat" id="read">
    <h6 class="mb-3"><a href="javascript:void(0)"><span class="fa fa-chevron-left" style="font-size: 10pt" onclick="return backToChat()"></span></a> {{$u_sender->name}}</h6>

        <div class="chat-outer chat-detail"  id="to-bottom" style="height: 280px">
            <div class="chat-inner">
                @foreach ($chat as $item)
                    @if ($item->sender_id == Auth::user()->id)

                        <div class=" msg_container base_sent">
                            <div class="messages msg_sent">

                                @if ($item->receiver_id == Auth::user()->id)
                                    @if ($item->status == '1')
                                        <span class="pull-right badge badge-danger">New</span>
                                    @endif
                                @else
                                    @if ($item->status == '1')
                                        <span class="pull-right badge badge-blue">Sent</span>
                                    @else
                                        <span class="pull-right badge badge-success">Read</span>
                                    @endif
                                @endif

                                <p> {{ $item->content }}</p>
                                <time datetime="{{ $item->created_at }}">{{ $item->sender->name }} •
                                    {{ date('d/m/y H:i:s', strtotime($item->created_at)) }}</time>

                            </div>
                        </div>

                    @else

                        <div class=" msg_container base_receive">
                            <div class="messages msg_receive">
                                <p>{{ $item->content }}</p>
                                <time datetime="{{ $item->created_at }}">{{ $item->sender->name }} •
                                    {{ date('d/m/y H:i:s', strtotime($item->created_at)) }}</time>
                            </div>
                        </div>

                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="card-body">
        <input type="hidden" name="pengguna" id='pengguna{{ $u_sender->id }}' value={{ $u_sender->id }}
            class="form-control">
        <input type="text" placeholder="Tuliskan pesan.." name="msg" id='msg{{ $u_sender->id }}'
            class="form-control mb-2">

        <div class="text-right">
            <button type="submit" class="btn kirim_pesan{{ $u_sender->id }} btn-success"
                onclick="return submitMsg('{{ $u_sender->id }}')" data-dismiss="modal"
                aria-label="Close">Send</button>
        </div>
    </div>
</div>

<style>
    .chat-outer{
        height: 180px;
        overflow-y: scroll;
    }

</style>

<script>
    function updateScroll(){
        var element = document.getElementById("to-bottom");
        element.scrollTop = element.scrollHeight;
        console.log("scroll "+element.scrollHeight)
    }

    setTimeout(updateScroll,1);
</script>
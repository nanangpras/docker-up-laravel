
<div class="footer">
    {{-- Copyright © {{\App\Models\DataOption::getOption('company')}}, {{date('Y')}} --}}
    Copyright © {{ Session::get('subsidiary') == 'CGL' ? 'Citra Guna Lestari' : 'Efran Berkat Aditama'}}, {{date('Y')}}
</div>

@if(env('APP_CODE')!="EBA_CLOUD")
<div class="end-footer">
    <div class="text-center">
        <div id="sync-status">Sync Status</div>
        <div style="">
            <span id="server-status">Local Connecting ...</span>
            <span id="cloud-status">Cloud Connecting ...</span>
            <span id="netsuite-status">Netsuite Connecting ...</span>
        </div>
    </div>
</div>
@endif

<button onclick="topFunction()" id="btnToTop" title="Go to top"><span class="fa fa-arrow-up"></span> Top</button>

<script>
    //Get the button
    var mybutton = document.getElementById("btnToTop");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        if (document.body.scrollTop > 40 || document.documentElement.scrollTop > 40) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
</script>

<style>
    #btnToTop {
        display: none; /* Hidden by default */
        position: fixed; /* Fixed/sticky position */
        bottom: 40px; /* Place the button at the bottom of the page */
        right: 30px; /* Place the button 30px from the right */
        z-index: 99; /* Make sure it does not overlap */
        border: none; /* Remove borders */
        outline: none; /* Remove outline */
        background-color: red; /* Set a background color */
        color: white; /* Text color */
        cursor: pointer; /* Add a mouse pointer on hover */
        padding: 15px; /* Some padding */
        border-radius: 10px; /* Rounded corners */
        font-size: 18px; /* Increase font size */
    }
</style>

<div id="toast"></div>

<audio id="notif-sound">
    <input type="hidden" id="count_notif" value="0">
    <source src="{{asset('notif.mp3')}}" type="audio/mpeg">
</audio>

@include('admin.pages.chat.index')
{{--
<script src="{{ asset("highcharts/highcharts.js") }}"></script>
<script src="{{ asset("highcharts/highcharts-more.js") }}"></script>
<script src="{{ asset("highcharts/exporting.js") }}"></script>
<script src="{{ asset("highcharts/export-data.js") }}"></script>
<script src="{{ asset("highcharts/accessibility.js") }}"></script>
--}}

<style>
    #toast{
        position: fixed;
        z-index: 99999;
        right: 0;
        top: 0;
        width: 300px;
        height: auto;
        margin-top: 15px;
        margin-right: 15px;
    }
    .alert-realtime {
        /* display: none; */
        border-left: 3px solid #05ab08;
        background: #ffffff;
        box-shadow: 0px 0px 10px #dddddd;
        padding: 15px;
        margin-bottom: 5px;
    }

    .realtime-title {
        color: #333333;
    }

    .realtime-content {
        color: #999999;
    }
</style>

<style>
    #server-status{
    width: 15px;
    height: 15px;
    background-color: red;
    font-size: 5pt;
    padding: 3px;
    }

    #cloud-status{
    width: 15px;
    height: 15px;
    background-color: red;
    font-size: 5pt;
    padding: 3px;
    }
    #netsuite-status{
    width: 15px;
    height: 15px;
    background-color: red;
    font-size: 5pt;
    padding: 3px;
    }

    .highlight {
        background-color: #fff34d;
        -moz-border-radius: 5px; /* FF1+ */
        -webkit-border-radius: 5px; /* Saf3-4 */
        border-radius: 5px; /* Opera 10.5, IE 9, Saf5, Chrome */
        -moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7); /* FF3.5+ */
        -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7); /* Saf3.0+, Chrome */
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.7); /* Opera 10.5+, IE 9.0 */
    }

    .highlight {
        padding:1px 4px;
        margin:0 -4px;
    }

</style>

<script>

    var env_sub = "{{env('APP_CODE')}}";
    var keepAliveTimeout = 1000 * 10;

    if(env_sub!="EBA_CLOUD"){
        keepSessionAlive();
        keepCloudSessionAlive();
        keepNetsuiteSessionAlive();
        serverStatus();
    }

    // syncStatus();

    function serverStatus(){

        if(env_sub!="EBA_CLOUD"){
            setInterval(function(){
                keepSessionAlive();
            }, 10000);

            setInterval(function(){
                keepCloudSessionAlive();
            }, 10000);
        }

        // setInterval(function(){
        //     syncStatus();
        // }, 10000);

    }

    function keepSessionAlive()
    {

        // console.log('connecting to server '+Date.now())
        $.ajax(
        {
            type: 'GET',
            url: "{{url('server-status')}}",
            timeout: 1000,
            success: function(data)
            {
            $("#server-status").css({"background-color": "#00FF00"});
            //   console.log('connected '+Date.now());
            $("#server-status").html('Local');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                if (XMLHttpRequest.readyState == 4) {
                    // HTTP error (can be checked by XMLHttpRequest.status and XMLHttpRequest.statusText)
                    $("#server-status").css({"background-color": "red"});
                    $("#server-status").html('disconnect');
                }
                else if (XMLHttpRequest.readyState == 0) {
                    // Network error (i.e. connection refused, access denied due to CORS, etc.)
                    $("#server-status").css({"background-color": "orange"});
                    $("#server-status").html('connecting');
                }
                else {
                    // something weird is happening
                    $("#server-status").css({"background-color": "yellow"});
                    $("#server-status").html(textStatus);
                }
            }
        })

    }

    function keepCloudSessionAlive()
    {

        // console.log('connecting to server cloud '+Date.now())
        $.ajax(
        {
            type: 'GET',
            url: "{{url('cloud-status')}}",
            timeout: 1000,
            success: function(data)
            {
            $("#cloud-status").css({"background-color": "#00FF00"});
            //   console.log('connected '+Date.now());
            $("#cloud-status").html('Cloud');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                if (XMLHttpRequest.readyState == 4) {
                    // HTTP error (can be checked by XMLHttpRequest.status and XMLHttpRequest.statusText)
                    $("#cloud-status").css({"background-color": "red"});
                    $("#cloud-status").html('Cloud disconnect');
                }
                else if (XMLHttpRequest.readyState == 0) {
                    // Network error (i.e. connection refused, access denied due to CORS, etc.)
                    $("#cloud-status").css({"background-color": "orange"});
                    $("#cloud-status").html('Cloud connecting');
                }
                else {
                    // something weird is happening
                    $("#cloud-status").css({"background-color": "yellow"});
                    $("#cloud-status").html(textStatus);
                }
            }
        })

    }

    function keepNetsuiteSessionAlive()
    {

        // console.log('connecting to server netsuite '+Date.now())
        $.ajax(
        {
            type: 'GET',
            url: "{{url('netsuite-status')}}",
            timeout: 1000,
            success: function(data)
            {
            $("#netsuite-status").css({"background-color": "#00FF00"});
            //   console.log('connected '+Date.now());
            $("#netsuite-status").html('Netsuite');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                if (XMLHttpRequest.readyState == 4) {
                    // HTTP error (can be checked by XMLHttpRequest.status and XMLHttpRequest.statusText)
                    $("#netsuite-status").css({"background-color": "red"});
                    $("#netsuite-status").html('Netsuite disconnect');
                }
                else if (XMLHttpRequest.readyState == 0) {
                    // Network error (i.e. connection refused, access denied due to CORS, etc.)
                    $("#netsuite-status").css({"background-color": "orange"});
                    $("#netsuite-status").html('Netsuite connecting');
                }
                else {
                    // something weird is happening
                    $("#netsuite-status").css({"background-color": "yellow"});
                    $("#netsuite-status").html(textStatus);
                }
            }
        })

    }

    function syncStatus()
    {

        // console.log('connecting to server cloud '+Date.now())
        $('#new_chat').load("{{ route('dashboard.new_chat') }}");
        $('#riwayat_chat').load("{{ route('dashboard.chat') }}");

        var count_chat      = 0;
        var count_chat_prev = 0;
        var html_chat       = "";
        var master_id       = $('#master-id').val();

        $.ajax(
        {
            type: 'GET',
            url: "{{url('admin/sync-status')}}",
            success: function(data)
            {

                if(data=="" || data==undefined || data==null){
                    return false;
                }

            $('#sync-status').html("Netsuite "+new Date()+" - Pending : "+data.data.pending + " | Approval : "+data.data.approval + " | Gagal : "+data.data.gagal + " | Selesai : "+data.data.selesai + " | Batal : "+data.data.batal + " | Queue : "+data.data.queue);
            chat         = data.data.chat;
            count_chat   = chat.length;

            if(count_chat>count_chat_prev){
                    for(var i=0; i<count_chat; i++){
                        html_chat += (
                            '<div class="alert-realtime notif-background-custom">'+
                                '<div class="pull-right realtime-close"><a href="javascript:void(0)"><span class="fa fa-close"></span></a></div>'+
                                '<div class="realtime-title">Pesan Baru '+chat[i].sender.name+'</div>'+
                                '<div class="realtime-content">'+chat[i].content+'</div>'+
                            '</div>'
                        )
                    }

                    $('#toast').html(html_chat);

                    if($('#count_notif').val()=="0"){

                        var media = document.getElementById("notif-sound");
                        const playPromise = media.play();

                        count_chat_prev = count_chat;

                        if (playPromise !== undefined) {
                            playPromise.then(_ => {
                                // Autoplay started!
                                $('#count_notif').val("1")
                            }).catch(error => {
                                // Autoplay was prevented.
                                // Show a "Play" button so that user can start playback.
                            });

                        }

                    }



                    $('.realtime-close').on('click', function(){
                        $(this).closest('.alert-realtime').fadeOut();
                    })
                }

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log('Error sync status')
            }
        })

    }

    jQuery.fn.highlight = function(pat) {
        function innerHighlight(node, pat) {
            var skip = 0;
            if (node.nodeType == 3) {
                var pos = node.data.toUpperCase().indexOf(pat);
                if (pos >= 0) {
                    var spannode = document.createElement('span');
                    spannode.className = 'highlight';
                    var middlebit = node.splitText(pos);
                    var endbit = middlebit.splitText(pat.length);
                    var middleclone = middlebit.cloneNode(true);
                    spannode.appendChild(middleclone);
                    middlebit.parentNode.replaceChild(spannode, middlebit);
                    skip = 1;
                }
            }
            else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
                for (var i = 0; i < node.childNodes.length; ++i) {
                    i += innerHighlight(node.childNodes[i], pat);
                }
            }
                return skip;
            }
            return this.each(function() {
                innerHighlight(this, pat.toUpperCase());
        });
    };

    jQuery.fn.removeHighlight = function() {
        function newNormalize(node) {
            for (var i = 0, children = node.childNodes, nodeCount = children.length; i < nodeCount; i++) {
                var child = children[i];
                if (child.nodeType == 1) {
                    newNormalize(child);
                    continue;
                }
                if (child.nodeType != 3) { continue; }
                var next = child.nextSibling;
                if (next == null || next.nodeType != 3) { continue; }
                var combined_text = child.nodeValue + next.nodeValue;
                new_node = node.ownerDocument.createTextNode(combined_text);
                node.insertBefore(new_node, child);
                node.removeChild(child);
                node.removeChild(next);
                i--;
                nodeCount--;
            }
        }

        return this.find("span.highlight").each(function() {
            var thisParent = this.parentNode;
            thisParent.replaceChild(this.firstChild, this);
            newNormalize(thisParent);
        }).end();
    };

    // $(function() {
    //     $('#text-search').bind('keyup change', function(ev) {
    //         // pull in the new value
    //         var searchTerm = $(this).val();

    //         // remove any old highlighted terms
    //         $('body').removeHighlight();

    //         // disable highlighting if empty
    //         if ( searchTerm ) {
    //             // highlight the new term
    //             $('body').highlight( searchTerm );
    //         }
    //     });
    // });

</script>

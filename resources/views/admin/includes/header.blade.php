<div id="main-header">

  <div class="topbar-title">
            <div class="topbar-logo">
                
                <a href="{{url('/')}}" id="logo"><img src="{{ Session::get('subsidiary') == 'CGL' ? asset('CGL_export.png') : asset('EBA_export.png') }}" class="img-fluid" style="height: 30px; width: auto"></a>
                <a href="{{url('/')}}" id="logo-mobile"><img src="{{ Session::get('subsidiary') == 'CGL' ? \App\Models\DataOption::getIcon('logo_mobile_cgl') : \App\Models\DataOption::getIcon('logo_mobile') }}" class="img-fluid"></a>

                

                <div class="menu-header">

                    {{-- <a href="javascript:void(0)" class="navbar-brand" id="sidebar-notification" style="margin-right: 30px; position:relative"><span class="fa fa-bell"></span>
                      <span id="badge-notif" class="badge">0</span>
                    </a> --}}

                  <a href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                      document.getElementById('logout-form').submit();">
                      <img src="{{asset('Icons/shutdown.png')}}" width="25px" >
                      Logout
                  </a>

                </div>
                
                

                <div class="pull-right last-login">
                    Hi {{\Illuminate\Support\Facades\Auth::user()->name}} {{ Session::get('subsidiary') }}<br>
                    <span style="color: #aaaaaa; font-size: 8pt;">{{date('d F Y - H:i:s', strtotime(\Illuminate\Support\Facades\Auth::user()->last_login)) }}</span>
                </div>
                
                
            </div>
    </div>

    <nav id="top-navbar">
        <div class="" id="navbarSupportedContent">
            @include('admin.includes.sidebar')
        </div>
    </nav>


<div id="notification-drawer">
    <div class="box-border">
        <div class="pabel-body">
            <nav class="navbar">
                    <div class="navbar-header">
                        <a href="javascript:void(0)" id="close-notification"><span class="fa fa-close"></span></a> &nbsp
                        <a href="javascript:void(0)" id="refresh-notification"><span id="spin" class="fa fa-refresh"></span></a>
                    </div>
            </nav>

            <div class="panel-body notification-outer">
                <div id="notification-content">

                </div>

            </div>
            <div class="" style="text-align: center">
                <a href="{{url('admin/list-notification')}}" class="btn btn-default">Buka Pemberitahuan</a>
            </div>


        </div>
    </div>
</div>


</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
.menu-header{
    position: absolute;
    right: 0;
    margin-top: -30px;
    margin-right: 45px;
}

.navbar-header .fa{
    font-size: 16pt;
}

#notification-drawer{
    position: fixed;
    width: 350px;
    height: 100%;
    margin-top: -100px;
    background: #ffffff;
    right: 0;
    z-index: 1300;
    box-shadow: 2px 2px 15px #999;
    display: none;
}

.notification-outer{
    height: 700px;
    overflow: auto;
}

#notification-content{
    height: auto;
}

@media screen and (min-width: 768px){
    #sidebar-toggle span{
        padding: 0px
    }
}
.last-login{
  margin-right: 225px;
}

#badge-notif{
    background: red; 
    position: absolute; 
    right: 0; 
    margin-right: -10px; 
    margin-top: -5px; 
    border: 2px solid #ffffff; 
    color: #ffffff; 
    font-size: 7pt
}

@media screen and (max-width: 768px){
  .last-login{
    display: none;
  }

  #notification-drawer{
    margin-top: -50px;
  }
}


</style>

<script>


    // $(document).ready(function(){
    //     getNotification();
    //     getCountNotification()
    // })

    // $('#sidebar-notification').on('click', function(){
    //     $('#notification-drawer').fadeIn();
    // })

    // $('#close-notification').on('click', function(){
    //     $('#notification-drawer').fadeOut();
    // })

    // $('#refresh-notification').on('click', function(){
    //     getNotification();
    //     getCountNotification()
    // })

    // var url_notif = "{{url('admin/notification')}}"

    // function getNotification(){
    //     $.ajax({
    //         type:"GET",
    //         url: url_notif,
    //         beforeSend:function(){
    //             $('#spin').toggleClass("fa-spin");
    //             $('#notification-content').html("");
    //         },
    //         success:function(notif){

    //             setTimeout(function(){
    //                 $('#notification-content').html(notif);
    //                 $('#spin').removeClass("fa-spin");
    //             }, 2000)

    //         }
    //     })
    // }

    // var url_count = "{{url('admin/count-notification')}}"
    // function getCountNotification(){
    //     $.ajax({
    //         type:"GET",
    //         url: url_count,
    //         beforeSend:function(){
    //         },
    //         success:function(notif){

    //             $('#badge-notif').html(notif);

    //         }
    //     })
    // }


</script>



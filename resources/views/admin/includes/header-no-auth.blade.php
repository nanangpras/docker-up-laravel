<div id="main-header">
    <div class="topbar-title">
        <div class="topbar-logo">
            <a href="{{url('/')}}" id="logo"><img src="{{ Session::get('subsidiary') == 'CGL' ? asset('CGL_export.png') : asset('EBA_export.png') }}" class="img-fluid" style="height: 30px; width: auto"></a>
            <a href="{{url('/')}}" id="logo-mobile"><img src="{{ Session::get('subsidiary') == 'CGL' ? \App\Models\DataOption::getIcon('logo_mobile_cgl') : \App\Models\DataOption::getIcon('logo_mobile') }}" class="img-fluid"></a>
            <div class="pull-right last-login"></div>
        </div>
    </div>
</div>
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



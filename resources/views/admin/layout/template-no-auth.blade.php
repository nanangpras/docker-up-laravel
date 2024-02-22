<!doctype html>

<html>

<head>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta name="viewport" content="initial-scale=0.1"> -->
    <meta name="csrf-token" content="{{csrf_token()}}">

    <!-- Bootstrap 4 core CSS -->
    <!-- <link href="{{asset('admin-themes')}}/bootstrap/css/bootstrap-reboot.min.css" rel="stylesheet"> -->
    <link href="{{asset('admin-themes')}}/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="{{asset('admin-themes')}}/bootstrap/css/bootstrap-grid.min.css" rel="stylesheet"> -->
    <link href="{{asset('admin-themes')}}/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <!-- Custom styles for this template -->
    <link href="{{asset('admin-themes')}}/bootstrap/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('admin-themes/css/daterangepicker.css')}}">
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}plugin/DataTables/datatables.min.css"/> -->
    <!-- <link rel="stylesheet" type="text/css" href="{{asset('')}}highcharts/highcharts-style.css"/> -->

    @php
        $color = Session::get('color');
    @endphp
    @if($color=="" || $color=="light")
    <link rel="stylesheet" type="text/css" href="{{asset('admin-themes/css/cyber-themes.css')}}">
    @else
    <link rel="stylesheet" type="text/css" href="{{asset('admin-themes/css/cyber-themes-dark.css')}}">
    @endif
    <link rel="stylesheet" type="text/css" href="{{asset('admin-themes/css/jsoneditor.css')}}">

    {{-- <link rel="shortcut icon" type="image/png" href="{{\App\Models\DataOption::getIcon('logo_mobile') ?? asset('img/favicon.png')}}"> --}}
    <link rel="shortcut icon" type="image/png" href="{{ Session::get('subsidiary') == 'CGL' ? \App\Models\DataOption::getIcon('logo_mobile_cgl') : \App\Models\DataOption::getIcon('logo_mobile') }}" />

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="shortcut icon" type="image/png" href="{{ Session::get('subsidiary') == 'CGL' ? \App\Models\DataOption::getIcon('logo_mobile_cgl') : \App\Models\DataOption::getIcon('logo_mobile') }}" />
    <link href="{{asset('admin-themes')}}/bootstrap/css/bootstrap4-toggle.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('sweetalert')}}/dist/sweetalert.css">


    <!-- Bootstrap core JavaScript -->
    <script src="{{asset('admin-themes/')}}/jquery/jquery.min.js"></script>
    <script src="{{asset('admin-themes/')}}/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- <script type="text/javascript" src="{{asset('admin-themes/js/moment.min.js')}}"></script> -->
    <script type="text/javascript" src="{{asset('admin-themes/js/daterangepicker.js')}}"></script>
    <!-- <script type="text/javascript" src="{{asset('admin-themes/js/selectize.js')}}"></script> -->
    <script type="text/javascript" src="{{asset('admin-themes/js/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin-themes/bootstrap/js/bootstrap4-toggle.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin-themes/js/jsoneditor.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin-themes/js/json2.js')}}"></script>
    <script type="text/javascript" src="{{asset('plugin/tinymce/tinymce.min.js')}}"></script>
    {{-- 
        <script src="{{asset('sweetalert')}}/dist/sweetalert.min.js"></script>
        <script src="{{asset('plugin/Chartjs/chart.min.js')}}"></script>
        <script src="{{asset('plugin/Chartjs/utils.js')}}"></script> 
    --}}
    <script src="{{asset('select2/js/select2.full.min.js') }}"></script>

    <style>
        select {
            -webkit-appearance:none;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            /* display: none; <- Crashes Chrome on hover */
            -webkit-appearance: none;
            margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
        }

        input[type=number] {
            -moz-appearance:textfield; /* Firefox */
        }

        .rendemen-outer{
            overflow-x: scroll;
            margin-bottom: 20px;
        }
        .scroll-rendemen{
            width: 100%;
            display: inline-flex;
        }

        .cursor{
            cursor: pointer;
        }
    </style>

    @yield('header')

</head>

<body id="body">

    <div id="topbar-notification">

        <div class="container">
            <div id="text-notif">
                My awesome top bar
            </div>
        </div>

    </div>

    <div id="alert-notification">

        <div class="container">
            <div id="alert-notif">
                My awesome top bar
            </div>
        </div>

    </div>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->

        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">

            <div class="container">

                    @include('admin.includes.header-no-auth')
                    @yield('content')
           </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->
    @yield('footer')

    <script>
        function showNotif(text) {

            $('#text-notif').html(text);
            $('#topbar-notification').fadeIn();

            setTimeout(function() {
                $('#topbar-notification').fadeOut();
            }, 2000)
        }

        function showAlert(text) {

            $('#alert-notif').html(text);
            $('#alert-notification').fadeIn();

            setTimeout(function() {
                $('#alert-notification').fadeOut();
            }, 2000)
        }
    </script>

<script>
  function formatAmountNoDecimals( number ) {
    var rgx = /(\d+)(\d{3})/;
    while( rgx.test( number ) ) {
      number = number.replace( rgx, '$1' + '.' + '$2' );
    }
    return number;
  }

  function formatAmount( number ) {

        // remove all the characters except the numeric values
        number = number.replace( /[^0-9]/g, '' );
        number.substring( number.length - 2, number.length );

        // set the precision
        number = new Number( number );
        number = number.toFixed( 2 );    // only works with the "."

        // change the splitter to ","
        number = number.replace( /\./g, ',' );

        // format the amount
        x = number.split( ',' );
        x1 = x[0];
        x2 = x.length > 1 ? ',' + x[1] : '';

        return formatAmountNoDecimals( x1 );
      }


      $(function() {

        $( '.input-amount' ).keyup( function() {
          $( this ).val( formatAmount( $( this ).val() ) );
        });

      });
    </script>


    <script>
        tinymce.init({
            selector: "textarea.editor",
            theme: "modern",
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons paste textcolor code"
            ],
            toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
            toolbar2: "| link unlink anchor | image media | forecolor backcolor  | print preview code | fontsizeselect",
            image_advtab: true,
            fontsize_formats: '8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 18pt',
            content_style: "div, p { font-size: 14px; }",
            height: "400",
            relative_urls: false,
            remove_script_host: false,
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });

        // $('.dataTable').DataTable({
        //     "bPaginate": true,
        //     "bLengthChange": false,
        //     "bFilter": true,
        //     "bInfo": false,
        //     "bAutoWidth": false
        // });

    </script>

    <script>
        //Format ke ISO Standard
        function formatDateToISO(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }

        // Indonesia Format
        function formatDate(d) {

            var date = new Date(d);

            if (isNaN(date.getTime())) {
                return d;
            } else {

                var weekday = new Array(7);
                weekday[0] = "Minggu";
                weekday[1] = "Senin";
                weekday[2] = "Selasa";
                weekday[3] = "Rabu";
                weekday[4] = "Kamis";
                weekday[5] = "Jumat";
                weekday[6] = "Sabtu";

                var month = new Array();
                month[0] = "Januari";
                month[1] = "Februari";
                month[2] = "Maret";
                month[3] = "April";
                month[4] = "Mei";
                month[5] = "Juni";
                month[6] = "Juli";
                month[7] = "Agustus";
                month[8] = "September";
                month[9] = "October";
                month[10] = "November";
                month[11] = "Desember";

                day = date.getDate();

                if (day < 10) {
                    day = "0" + day;
                }

                var hour;
                var minutes;
                var second;

                if (date.getHours() == 0) {
                    hour = ""
                } else {
                    hour = " | " + date.getHours() + ":";
                }

                if (date.getMinutes() == 0) {
                    minutes = ""
                } else {
                    minutes = date.getMinutes() + ":";
                }

                if (date.getSeconds() == 0) {
                    second = ""
                } else {
                    second = date.getSeconds();
                }

                // return weekday[date.getDay()] + ", " + day + " " + month[date.getMonth()] + " " + date.getFullYear() + "  " + hour + minutes + second;
                return weekday[date.getDay()] + ", " + day + " " + month[date.getMonth()] + " " + date.getFullYear();

            }

        }

        function formatDateTime(d) {

            var date = new Date(d);

            if (isNaN(date.getTime())) {
                return d;
            } else {

                var weekday = new Array(7);
                weekday[0] = "Minggu";
                weekday[1] = "Senin";
                weekday[2] = "Selasa";
                weekday[3] = "Rabu";
                weekday[4] = "Kamis";
                weekday[5] = "Jumat";
                weekday[6] = "Sabtu";

                var month = new Array();
                month[0] = "Januari";
                month[1] = "Februari";
                month[2] = "Maret";
                month[3] = "April";
                month[4] = "Mei";
                month[5] = "Juni";
                month[6] = "Juli";
                month[7] = "Agustus";
                month[8] = "September";
                month[9] = "October";
                month[10] = "November";
                month[11] = "Desember";

                day = date.getDate();

                if (day < 10) {
                    day = "0" + day;
                }

                var hour;
                var minutes;
                var second;

                if (date.getHours() == 0) {
                    hour = ""
                } else {
                    hour = " | " + date.getHours() + ":";
                }

                if (date.getMinutes() == 0) {
                    minutes = ""
                } else {
                    minutes = date.getMinutes() + ":";
                }

                if (date.getSeconds() == 0) {
                    second = ""
                } else {
                    second = date.getSeconds();
                }

                return weekday[date.getDay()] + ", " + day + " " + month[date.getMonth()] + " " + date.getFullYear() + "  " + hour + minutes + second;

            }

        }

        function nominalToCurrency(number) {
            number = number.toFixed(2) + '';
            x = number.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

    $("img").on("error", function () {
        $(this).attr("src", "{{asset('/')}}no-images.png");
    });

</script>


    @yield('scripts')

</body>

</html>

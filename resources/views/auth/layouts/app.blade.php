<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('app/js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('app/css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        
        @if (Str::contains(url()->current(), 'http://8.219.1.73:8082/'))
            <div class="text-center" style="margin-top: 60px; margin-bottom: 10px; margin-left:60px;">
                <img src="{{ asset('EBA_export.png')}}" class="img-fluid" style="max-width: 200px">
                {{-- <img src="{{ asset('x_colabs.png')}}" class="img-fluid mb-3" style="max-width: 30px; margin-left:10px; margin-right:10px"> --}}
                <img src="{{ asset('x_colab.png')}}" class="img-fluid mb-3" style="max-width: 30px; margin-left:10px; margin-right:10px; filter:rgb(123, 115, 115)">

                <img src="{{ asset('CGL_export.png')}}" class="img-fluid mb-2" style="max-width: 200px">
            </div>
        @else
            <div class="text-center" style="margin-top: 30px; margin-bottom: 30px">
                <img src="{{\App\Models\DataOption::getIcon('logo') ?? asset('logo.png')}}" class="img-fluid" style="max-width: 300px">
            </div>
        @endif
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>

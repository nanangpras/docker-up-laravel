@extends('auth.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                            <input id="user_devices" name="user_devices" style="display: none">
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">Remember Me</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">Login</button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">Forgot Your Password?</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="token" style="display: none"></div>
<div id="msg" style="display: none"></div>
<div id="notis" style="display: none"></div>
<div id="err" style="display: none"></div>
<script type="text/javascript" src="{{asset('plugin/jquery.3.1.1.js')}}"></script>

    <script src="https://www.gstatic.com/firebasejs/7.9.3/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.9.3/firebase-messaging.js"></script>
    <script>
        // Your web app's Firebase configuration
        var config = {
            apiKey: "AIzaSyACuRKV9V-tCEUYHUK0jNGn_lJsAomShrs",
            authDomain: "cgl-erp.firebaseapp.com",
            projectId: "cgl-erp",
            storageBucket: "cgl-erp.appspot.com",
            messagingSenderId: "38544840864",
            appId: "1:38544840864:web:81147203cb2c5f92e4d1c5",
            measurementId: "G-2XD5HLJTE3"
        };

        // Initialize Firebase
        firebase.initializeApp(config);

        

    MsgElem = document.getElementById("msg")
    TokenElem = document.getElementById("token")
    NotisElem = document.getElementById("notis")
    ErrElem = document.getElementById("err")
    
    const messaging = firebase.messaging();
    messaging
        .requestPermission()
        .then(function () {
            MsgElem.innerHTML = "Notification permission granted."
            console.log("Notification permission granted.");
            
            // get the token in the form of promise
            return messaging.getToken()
        })
        .then(function(token) {
            TokenElem.innerHTML = "token is : " + token
                $('#user_devices').val(token);
                console.log('Token received :' + token);
        })
        .catch(function (err) {
            ErrElem.innerHTML =  ErrElem.innerHTML + "; " + err
            console.log("Unable to get permission to notify.", err);
        });

    messaging.onMessage(function(payload) {
        console.log("Message received. ", payload);
        NotisElem.innerHTML = NotisElem.innerHTML + JSON.stringify(payload)
    });
</script>

@endsection

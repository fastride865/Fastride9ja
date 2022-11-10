<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
     <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ApporioTaxi</title>
    <link href="{{ asset('admintheme/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admintheme/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admintheme/css/font-awesome1.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admintheme/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admintheme/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admintheme/css/skins/skin-purple.min.css') }}">
</head>
<body class="hold-transition skin-purple login-page">
<div class="login-box">
    <div class="login-logo">
    <a href="{{ url('admin') }}">Apporio-Admin</a>
    </div>
    @include('admin.errors-and-messages')
    <div class="login-box-body">
        <p class="login-box-msg" style="block;">Sign in</p>

        <form action="{{ route('admin.login.submit') }}" method="post">
          @csrf
            <div class="form-group has-feedback">
                <input name="email" type="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input name="password" type="password" class="form-control" placeholder="Password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
           
           
           
            <!--<div class="g-recaptcha" data-callback="capcha_filled" data-expired-callback="capcha_expired"-->
            <!--     data-sitekey="6LfDC5gUAAAAADbpG6LG2zcKWn9ohjfNrMkfuuZh"></div>-->
                 
            <div class="row">
                <div class="col-xs-8">

                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
    </div>
    <!-- /.login-box-body -->
</div>
   
  <script type="text/javascript">
  var onloadCallback = function() {
    //alert("grecaptcha is ready!");
  };
  
   function capcha_filled() {
        allowSubmit = true;
    }

    function capcha_expired() {
        allowSubmit = false;
    }
</script>

    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
        async defer>
    </script>
    
    
<script src="{{ asset('admintheme/js/jquery-2.2.3.min.js') }}"></script>
<script src="{{ asset('admintheme/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('admintheme/js/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('admintheme/js/fastclick.min.js') }}"></script>
<script src="{{ asset('admintheme/js/app.min.js') }}"></script>
</body>
</html>


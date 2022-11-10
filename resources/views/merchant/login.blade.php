<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  @php
    $image = isset($merchant->ApplicationTheme->login_background_image) && !empty($merchant->ApplicationTheme->login_background_image) ? get_image($merchant->ApplicationTheme->login_background_image,'login_background',$merchant->id,true) : asset("theme/examples/images/login.jpg");
    //p($image);
  @endphp
  <style>
    .rc-anchor-light {
      background: #f3f5f1f7 !important;
      border-radius: 4px;
    }
    .page-login:before {
      background-image: url("@php echo"$image";@endphp") !important;
    }
  </style>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="bootstrap admin template">
  <meta name="author" content="">

  <title>Login | {{ $merchant->BusinessName }}</title>

  <link rel="apple-touch-icon" href="{{ asset('theme/images/apple-touch-icon.png') }}">
  <link rel="shortcut icon" href="{{ isset($merchant->BusinessLogo) && !empty($merchant->BusinessLogo) ? get_image($merchant->BusinessLogo,'business_logo',$merchant->id,true): asset('theme/images/favicon.ico') }}">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="{{ asset('global/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('global/css/bootstrap-extend.min.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/css/site.min.css') }}">

  <!-- Plugins -->
  <link rel="stylesheet" href="{{ asset('global/vendor/animsition/animsition.css') }}">
  <link rel="stylesheet" href="{{ asset('global/vendor/asscrollable/asScrollable.css') }}">
  <link rel="stylesheet" href="{{ asset('global/vendor/flag-icon-css/flag-icon.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/examples/css/pages/login.css') }}">


  <!-- Fonts -->
  <link rel="stylesheet" href="{{ asset('global/fonts/web-icons/web-icons.min.css') }}">
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>

  <script src="{{ asset('global/vendor/breakpoints/breakpoints.js' ) }}"></script>
  <script>
    Breakpoints();
  </script>
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body class="animsition page-login layout-full page-dark">
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Page -->
<div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">>
  <div class="page-content vertical-align-middle animation-slide-top animation-duration-1">
    <div class="brand" style="margin-bottom: 22px;">
      <img class="brand-img w-200 h-200" src="{{ get_image($merchant->BusinessLogo,'business_logo',$merchant->id,true) }}" alt="...">
      {{--      <h2 class="brand-text">{{ $merchant->BusinessName }}</h2>--}}
    </div>
    <form method="POST" action="{{ route('merchant.login.submit',$merchant->alias_name) }}" style="margin:0px 0px;">
      @csrf
      <div class="form-group">
        @if ($errors->has('email'))
          <div class="alert dark alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">x</span>
            </button>
            {{ $errors->first('email') }}
          </div>
        @endif
        <label class="sr-only" for="inputEmail">Email</label>
        {{ Form::text('email', old('email'), ["class" => "form-control", "id" => 'email', "placeholder" => "Enter Email", "autocomplete" => "off", "required", "autofocus"]) }}
      </div>
      <div class="form-group">
        <label class="sr-only" for="inputPassword">Password</label>
        {{ Form::password('password', ["class" => "form-control", "id" => "password", "placeholder" => "Enter Password", "autocomplete" => "off", "required", "autofocus"]) }}
      </div>
      {{--      <div class="form-group">--}}
      {{--        <div class="g-recaptcha" data-sitekey="6LcXDdUUAAAAAG5RBMocS1pZxVQuPUEv-TUTxB3x"></div>--}}
      {{--      </div>--}}
      {{--      <div class="form-group clearfix">--}}
      {{--        <div class="checkbox-custom checkbox-inline checkbox-primary float-left">--}}
      {{--          {{ Form::checkbox('remember','',["id" => "inputCheckbox"]) }}--}}
      {{--          <label for="inputCheckbox">Remember me</label>--}}
      {{--        </div>--}}
      {{--      </div>--}}
      {{ Form::submit('Login', ["class" => "btn btn-primary btn-block"]) }}
    </form>
    <br>
    @if($merchant->demo == 1)
      {{--      <div class=" col-md-2 vertical-align-middle p-10" style="background-color: white; color:#000000;">--}}
      {{--        <label>Username : {{$merchant->email}}</label>--}}
      {{--        @if($merchant->id == 1)--}}
      {{--        <label>Password : 12345678</label>--}}
      {{--        @else--}}
      {{--        <label>Password : 1234567</label>--}}
      {{--        @endif--}}
      {{--      </div>--}}
    @endif

    <footer class="page-copyright page-copyright-inverse">
      <p>© {{ date('Y') }}. All RIGHTS RESERVED.</p>
    </footer>
  </div>
</div>
<!-- End Page -->
<!-- Core  -->
<script src="{{ asset('global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ asset('global/vendor/jquery/jquery.js') }}"></script>
<script src="{{ asset('global/vendor/popper-js/umd/popper.min.js') }}"></script>
<script src="{{ asset('global/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ asset('global/vendor/animsition/animsition.js') }}"></script>
<script src="{{ asset('global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ asset('global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
<script src="{{ asset('global/vendor/ashoverscroll/jquery-asHoverScroll.js') }}"></script>

<!-- Plugins -->
<script src="{{ asset('global/vendor/screenfull/screenfull.js') }}"></script>
<!-- Scripts -->
<script src="{{ asset('global/js/Component.js') }}"></script>
<script src="{{ asset('global/js/Plugin.js') }}"></script>
<script src="{{ asset('global/js/Base.js') }}"></script>
<script src="{{ asset('global/js/Config.js') }}"></script>

<script src="{{ asset('theme/js/Section/Menubar.js') }}"></script>
<script src="{{ asset('theme/js/Section/GridMenu.js') }}"></script>
<script src="{{ asset('theme/js/Section/Sidebar.js') }}"></script>
<script src="{{ asset('theme/js/Plugin/menu.js') }}"></script>

<script src="{{ asset('global/js/config/colors.js') }}"></script>

<!-- Page -->
<script src="{{ asset('theme/js/Site.js') }}"></script>

<script>
  (function(document, window, $){
    'use strict';

    var Site = window.Site;
    $(document).ready(function(){
      Site.run();
    });
  })(document, window, jQuery);
</script>
<script type="text/javascript">
  expires = new Date();
  expires = new Date(new Date().getTime() + parseInt(expires) * 1000 * 60 * 60 * 24);
  cookieexpire = expires.toGMTString();
  cookiepath = "/";
  document.cookie = "url=" + window.location.href + "; expires=" + cookieexpire + "; path=" + cookiepath;
</script>
</body>
</html>
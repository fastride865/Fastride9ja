<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">

    <title>{{$merchant->BusinessName}} - Corporate Login</title>

    <link rel="apple-touch-icon" href="{{ asset('theme/images/apple-touch-icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('theme/images/favicon.ico') }}">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/site.min.css') }}">

    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/asscrollable/asScrollable.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/switchery/switchery.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/intro-js/introjs.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/slidepanel/slidePanel.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/flag-icon-css/flag-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/examples/css/pages/login-v2.css') }}">


    <link rel="stylesheet" href="{{ asset('global/fonts/web-icons/web-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('global/fonts/brand-icons/brand-icons.min.css') }}">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>

    <!--[if lt IE 9]>
    <script src="{{ asset('global/vendor/html5shiv/html5shiv.min.js') }}'"></script>
    <![endif]-->

    <!--[if lt IE 10]>
    <script src="{{ asset('global/vendor/media-match/media.match.min.js') }}"></script>
    <script src="{{ asset('global/vendor/respond/respond.min.js') }}"></script>
    <![endif]-->

    <!-- Scripts -->
    <script src="{{ asset('global/vendor/breakpoints/breakpoints.js' ) }}"></script>
    <script>
        Breakpoints();
    </script>
</head>
<body class="animsition page-login-v2 layout-full page-dark">
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->


<style>
    body {
        background: transparent;
    }
</style>
<!-- Page -->
<div class="page" data-animsition-in="fade-in" data-animsition-out="fade-out">
    <div class="page-content">
        <div class="page-brand-info">
            <div class="brand">
                <img class="brand-img w-80 h-80" width="100" height="100" style="border-radius: 50%" src="{{ get_image($merchant->BusinessLogo,'business_logo',$merchant->id,true) }}" alt="...">
                <h2 class="brand-text font-size-40">{{ $merchant->BusinessName }}</h2>
            </div>
        </div>

        <div class="page-login-main animation-slide-right animation-duration-1">
            <div class="brand hidden-md-up">
                <img class="brand-img w-80 h-80" src="{{ asset('theme/images/logo-colored@2x.png" alt="...') }}">
                <h3 class="brand-text font-size-40">{{ $merchant->BusinessName }}</h3>
            </div>
            @if ($errors->has('email'))
                <div class="alert dark alert-icon alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                    <i class="icon wb-info" aria-hidden="true"></i>{{ $errors->first('email') }}
                </div>
            @endif
            <h3 class="font-size-24"> Corporate Login</h3>
            <form method="post" action="{{ route('corporate.login.submit','test-apporio')}}">
                @csrf
                <div class="form-group">
                    <label class="sr-only" for="inputEmail">Email</label>
                    {{ Form::text('email', old('email'), ["class" => "form-control", "id" => 'email', "placeholder" => "Enter Email", "autocomplete" => "off", "required", "autofocus"]) }}
                </div>
                <div class="form-group">
                    <label class="sr-only" for="inputPassword">Password</label>
                    {{ Form::password('password', ["class" => "form-control", "id" => "password", "placeholder" => "Enter Password", "autocomplete" => "off", "required", "autofocus"]) }}
                </div>
                <div class="form-group clearfix">
                    <div class="checkbox-custom checkbox-inline checkbox-primary float-left">
                        {{ Form::checkbox('remember','',["id" => "inputCheckbox"]) }}
                        <label for="inputCheckbox">Remember me</label>
                    </div>
                </div>
                {{ Form::submit('Login', ["class" => "btn btn-primary btn-block"]) }}

            </form>

            <footer class="page-copyright page-copyright-inverse">
                <p>© {{ date('Y') }}. All RIGHT RESERVED.</p>
            </footer>
        </div>

    </div>
</div>
<!-- End Page -->


<!-- Core  -->
<script src="{{ asset('global/vendor/babel-external-helpers/babel-external-helpers.js')}}"></script>
<script src="{{ asset('global/vendor/jquery/jquery.js')}}"></script>
<script src="{{ asset('global/vendor/popper-js/umd/popper.min.js')}}"></script>
<script src="{{ asset('global/vendor/bootstrap/bootstrap.js')}}"></script>
<script src="{{ asset('global/vendor/animsition/animsition.js')}}"></script>
<script src="{{ asset('global/vendor/mousewheel/jquery.mousewheel.js')}}"></script>
<script src="{{ asset('global/vendor/asscrollbar/jquery-asScrollbar.js')}}"></script>
<script src="{{ asset('global/vendor/asscrollable/jquery-asScrollable.js')}}"></script>
<script src="{{ asset('global/vendor/ashoverscroll/jquery-asHoverScroll.js')}}"></script>

<!-- Plugins -->
<script src="{{ asset('global/vendor/switchery/switchery.js')}}"></script>
<script src="{{ asset('global/vendor/intro-js/intro.js')}}"></script>
<script src="{{ asset('global/vendor/screenfull/screenfull.js')}}"></script>
<script src="{{ asset('global/vendor/slidepanel/jquery-slidePanel.js')}}"></script>
<script src="{{ asset('global/vendor/jquery-placeholder/jquery.placeholder.js')}}"></script>

<!-- Scripts -->
<script src="{{ asset('global/js/Component.js')}}"></script>
<script src="{{ asset('global/js/Plugin.js')}}"></script>
<script src="{{ asset('global/js/Base.js')}}"></script>
<script src="{{ asset('global/js/Config.js')}}"></script>

<script src="{{ asset('theme/js/Section/Menubar.js') }}"></script>
<script src="{{ asset('theme/js/Section/GridMenu.js') }}"></script>
<script src="{{ asset('theme/js/Section/Sidebar.js') }}"></script>
<script src="{{ asset('theme/js/Section/PageAside.js') }}"></script>
<script src="{{ asset('theme/js/Plugin/menu.js') }}"></script>

<script src="{{ asset('global/js/config/colors.js')}}"></script>
{{--<script src="{{ asset('theme/js/config/tour.js') }}"></script>--}}
<script>Config.set('theme', '{{ asset('theme') }}');</script>

<!-- Page -->
<script src="{{ asset('theme/js/Site.js') }}"></script>
<script src="{{ asset('global/js/Plugin/asscrollable.js')}}"></script>
<script src="{{ asset('global/js/Plugin/slidepanel.js')}}"></script>
<script src="{{ asset('global/js/Plugin/switchery.js')}}"></script>
<script src="{{ asset('global/js/Plugin/jquery-placeholder.js')}}"></script>

<script>
    (function(document, window, $){
        'use strict';

        var Site = window.Site;
        $(document).ready(function(){
            Site.run();
        });
    })(document, window, jQuery);
</script>
</body>
</html>

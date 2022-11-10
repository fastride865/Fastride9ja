<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="ApporioTaxi">
    <meta name="keywords" content="ApporioTaxi">
    <title>{{ $merchant->BusinessName }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Muli:300,400,500,700"
          rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('merchanttheme/css/vendor.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('merchanttheme/css/icheck.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('merchanttheme/css/custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('merchanttheme/css/themeapp.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('merchanttheme/css/menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('merchanttheme/css/gradient.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('merchanttheme/css/login.css') }}">
</head>
<body class="vertical-layout vertical-menu 1-column   menu-expanded blank-page blank-page" data-open="click"
      data-menu="vertical-menu" data-col="1-column">
<div class="app-content content">

    <div class="content-wrapper">
        <div class="content-header row">



        </div>
        <div class="content-body">
            <section class="flexbox-container">


                <div class="col-12 d-flex align-items-center justify-content-center">

                    <div class="col-md-3 col-10 p-0">
                        <div class="card border-grey border-lighten-3 m-0">
                            @if ($errors->has('email'))
                                <div class="alert alert-icon-left alert-danger alert-dismissible mb-2" role="alert">
                                    <span class="alert-icon"><i class="fa fa-thumbs-o-down"></i></span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <strong>{{ $errors->first('email') }}</strong>
                                </div>
                            @endif

                            <div class="card-header border-0">
                                <div class="card-title text-center">
                                    <div class="p-1"><img src="{{ asset($merchant->BusinessLogo) }}" style="width: 170px;height: 60px;" alt="branding logo"></div>
                                </div>
                                <h6 class="card-subtitle line-on-side text-muted text-center font-small-3
                                pt-2"><span>Login with Apporio Taxi</span>
                                </h6>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <form class="form-horizontal form-simple" method="POST" action="{{ route('taxicompany.login.submit','test-apporio') }}">
                                        @csrf
                                        <fieldset class="form-group position-relative has-icon-left mb-0">
                                            <input type="text" class="form-control form-control-lg input-lg"
                                                   id="email" placeholder="Your Email" name="email" value="" required autofocus>
                                            <div class="form-control-position">
                                                <i class="ft-user"></i>
                                            </div>
                                        </fieldset>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <input type="password" class="form-control form-control-lg input-lg"
                                                   id="password" placeholder="Enter Password" name="password" required>
                                            <div class="form-control-position">
                                                <i class="fa fa-key"></i>
                                            </div>
                                        </fieldset>
                                        <div class="form-group row">
                                            <div class="col-md-6 col-12 text-center text-md-left">
                                                <fieldset>
                                                    <input type="checkbox" id="remember" class="chk-remember" name="remember">
                                                    <label for="remember-me"> Remember Me</label>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-6 col-12 text-center text-md-right"></div>
                                        </div>
                                        <button type="submit" class="btn btn-info btn-lg btn-block"><i
                                                    class="ft-unlock"></i> Login
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script src="{{ asset('merchanttheme/js/vendor.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('merchanttheme/js/icheck.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('merchanttheme/js/menu.js') }}" type="text/javascript"></script>
<script src="{{ asset('merchanttheme/js/themeapp.js') }}" type="text/javascript"></script>
</body>
</html>
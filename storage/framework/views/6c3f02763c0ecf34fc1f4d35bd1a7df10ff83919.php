<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">

    <title>Welcome</title>

    <link rel="apple-touch-icon" href="<?php echo e(asset('theme/images/apple-touch-icon.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('theme/images/favicon.ico')); ?>">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo e(asset('global/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/css/bootstrap-extend.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/css/site.min.css')); ?>">

    <!-- Plugins -->
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/animsition/animsition.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/pages/errors.css')); ?>">


    <!-- Fonts -->

    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>



</head>
<body class="animsition page-error page-error-404 layout-full">
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->


<!-- Page -->
<div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">
    <div class="page-content vertical-align-middle">
        <header>
            <h1 class="animation-slide-top font-weight-500 font-size-50">TIME TO GET STARTED</h1>
            <h4 class="animation-slide-top">Login to continue</h4>
        </header>
    </div>
</div>


<script src="<?php echo e(asset('global/vendor/babel-external-helpers/babel-external-helpers.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/jquery/jquery.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/bootstrap/bootstrap.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/animsition/animsition.js')); ?>"></script>

<!-- Plugins -->
<!-- Scripts -->
<script src="<?php echo e(asset('global/js/Component.js')); ?>"></script>
<script src="<?php echo e(asset('global/js/Base.js')); ?>"></script>
<script src="<?php echo e(asset('global/js/Config.js')); ?>"></script>


<script src="<?php echo e(asset('global/js/config/colors.js')); ?>"></script>

<!-- Page -->
<script src="<?php echo e(asset('theme/js/Site.js')); ?>"></script>

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
<?php /**PATH C:\laragon\www\Fastride\Fastride Backend\resources\views/welcome.blade.php ENDPATH**/ ?>
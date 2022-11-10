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
            <h1 class="animation-slide-top">404</h1>
            <p>Page Not Found !</p>
        </header>
        <p class="error-advise">YOU SEEM TO BE TRYING TO FIND HIS WAY HOME</p>
        <a class="btn btn-primary btn-round" href="<?php echo e(env('APP_URL')); ?>">GO TO HOME PAGE</a>

        <footer class="page-copyright">
            <p> © <?php echo e(date('Y')); ?>. All RIGHTS RESERVED.</p>

        </footer>
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
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    var url = getCookie('url');
    if (url) {
        window.location = url;
    }
    // (function(document, window, $){
    //     'use strict';
    //
    //     var Site = window.Site;
    //     $(document).ready(function(){
    //         Site.run();
    //     });
    // })(document, window, jQuery);
</script>
</body>
</html>
<?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/404.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="author" content="">

    <title>Business Segment Login Template</title>

    <link rel="apple-touch-icon" href="<?php echo e(asset('theme/images/apple-touch-icon.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('theme/images/favicon.ico')); ?>">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo e(asset('global/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/css/bootstrap-extend.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/css/site.min.css')); ?>">

    <!-- Plugins -->
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/animsition/animsition.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/asscrollable/asScrollable.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/switchery/switchery.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/intro-js/introjs.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/slidepanel/slidePanel.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/flag-icon-css/flag-icon.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/pages/business-segment-login-v2.css')); ?>">


    <link rel="stylesheet" href="<?php echo e(asset('global/fonts/web-icons/web-icons.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/fonts/brand-icons/brand-icons.min.css')); ?>">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>

    <!--[if lt IE 9]>
    <script src="<?php echo e(asset('global/vendor/html5shiv/html5shiv.min.js')); ?>'"></script>
    <![endif]-->
    <!--[if lt IE 10]>
    <script src="<?php echo e(asset('global/vendor/media-match/media.match.min.js')); ?>"></script>
    <script src="<?php echo e(asset('global/vendor/respond/respond.min.js')); ?>"></script>
    <![endif]-->
    <!-- Scripts -->
    <script src="<?php echo e(asset('global/vendor/breakpoints/breakpoints.js' )); ?>"></script>
    <script>
        Breakpoints();
    </script>
    <style>

    </style>
</head>
<body class="animsition page-login-v2 layout-full page-dark">
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<?php
if(!empty($business_segment->login_background_image))
    $url=get_image($business_segment->login_background_image,'business_login_background_image',$merchant->id,true);
else
    $url=asset('theme/examples/images/login2.png');
?>
<style>
    /*body {*/
    /*    background: transparent;*/
    /*}*/
    .page-login-v2:before{
        background-image:url(<?php echo $url; ?>);
    }
</style>
<!-- Page -->
<div class="page" data-animsition-in="fade-in" data-animsition-out="fade-out">
    <div class="page-content">







        <div class="page-login-main animation-slide-right animation-duration-1">
            <div class="brand mb-0">
                <img class="brand-img" src="<?php echo e(get_image($business_segment->business_logo,'business_logo',$business_segment->merchant_id,true)); ?>" alt="..." width="80px">

            </div>
            <h3 class="font-size-24"><?php echo e($business_segment->full_name); ?></h3>
            <form method="post" action="<?php echo e(route('business-segment.login.submit',$business_segment->alias_name)); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <label class="sr-only" for="inputEmail">Email</label>
                    <?php echo e(Form::text('email', old('email'), ["class" => "form-control", "id" => 'email', "placeholder" => "Enter Email OR Phone No", "autocomplete" => "off", "required", "autofocus"])); ?>

                </div>
                <div class="form-group">
                    <label class="sr-only" for="inputPassword">Password</label>
                    <?php echo e(Form::password('password', ["class" => "form-control", "id" => "password", "placeholder" => "Enter Password", "autocomplete" => "off", "required", "autofocus"])); ?>

                </div>
                <div class="form-group">
                    <?php if($errors->has('email')): ?>
                        <div class="alert dark alert-icon alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">x</span>
                            </button>
                            <i class="icon wb-info" aria-hidden="true"></i><?php echo e($errors->first('email')); ?>

                        </div>
                    <?php endif; ?>
                </div>






                <?php echo e(Form::submit('Login', ["class" => "btn btn-primary btn-block"])); ?>

            </form>
            <footer class="page-copyright page-copyright-inverse">
                <p>© <?php echo e(date('Y')); ?>. All RIGHT RESERVED.</p>
            </footer>
        </div>

    </div>
</div>
<!-- End Page -->


<!-- Core  -->
<script src="<?php echo e(asset('global/vendor/babel-external-helpers/babel-external-helpers.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/jquery/jquery.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/popper-js/umd/popper.min.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/bootstrap/bootstrap.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/animsition/animsition.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/mousewheel/jquery.mousewheel.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/asscrollbar/jquery-asScrollbar.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/asscrollable/jquery-asScrollable.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/ashoverscroll/jquery-asHoverScroll.js')); ?>"></script>

<!-- Plugins -->
<script src="<?php echo e(asset('global/vendor/switchery/switchery.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/intro-js/intro.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/screenfull/screenfull.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/slidepanel/jquery-slidePanel.js')); ?>"></script>
<script src="<?php echo e(asset('global/vendor/jquery-placeholder/jquery.placeholder.js')); ?>"></script>

<!-- Scripts -->
<script src="<?php echo e(asset('global/js/Component.js')); ?>"></script>
<script src="<?php echo e(asset('global/js/Plugin.js')); ?>"></script>
<script src="<?php echo e(asset('global/js/Base.js')); ?>"></script>
<script src="<?php echo e(asset('global/js/Config.js')); ?>"></script>

<script src="<?php echo e(asset('theme/js/Section/Menubar.js')); ?>"></script>
<script src="<?php echo e(asset('theme/js/Section/GridMenu.js')); ?>"></script>
<script src="<?php echo e(asset('theme/js/Section/Sidebar.js')); ?>"></script>
<script src="<?php echo e(asset('theme/js/Section/PageAside.js')); ?>"></script>
<script src="<?php echo e(asset('theme/js/Plugin/menu.js')); ?>"></script>

<script src="<?php echo e(asset('global/js/config/colors.js')); ?>"></script>

<script>Config.set('theme', '<?php echo e(asset('theme')); ?>');</script>

<!-- Page -->
<script src="<?php echo e(asset('theme/js/Site.js')); ?>"></script>
<script src="<?php echo e(asset('global/js/Plugin/asscrollable.js')); ?>"></script>
<script src="<?php echo e(asset('global/js/Plugin/slidepanel.js')); ?>"></script>
<script src="<?php echo e(asset('global/js/Plugin/switchery.js')); ?>"></script>
<script src="<?php echo e(asset('global/js/Plugin/jquery-placeholder.js')); ?>"></script>
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
<?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/login.blade.php ENDPATH**/ ?>
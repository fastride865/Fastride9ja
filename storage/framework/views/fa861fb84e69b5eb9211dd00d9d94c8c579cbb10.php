<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="author" content="">
      <?php $merchant = get_merchant_id(false); ?>
    <title>Dashboard | <?php echo e($merchant->BusinessName); ?></title>

    <link rel="apple-touch-icon" href="<?php echo e(asset('theme/images/apple-touch-icon.png')); ?>">
      <link rel="shortcut icon" href="<?php echo e(isset($merchant->BusinessLogo) && !empty($merchant->BusinessLogo) ? get_image($merchant->BusinessLogo,'business_logo',$merchant->id,true): asset('theme/images/favicon.ico')); ?>">
  
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo e(asset('global/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/css/bootstrap-extend.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/css/site.min.css')); ?>">
    
    <!-- Plugins -->
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/animsition/animsition.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/asscrollable/asScrollable.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/intro-js/introjs.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/slidepanel/slidePanel.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/flag-icon-css/flag-icon.css')); ?>">
      <link rel="stylesheet" href="<?php echo e(asset('global/vendor/switchery/switchery.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/forms/layouts.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/datatables.net-bs4/dataTables.bootstrap4.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/datatables.net-scroller-bs4/dataTables.scroller.bootstrap4.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/datatables.net-responsive-bs4/dataTables.responsive.bootstrap4.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/tables/datatable.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/chartist/chartist.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/asspinner/asSpinner.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/asspinner/asSpinner.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/aspieprogress/asPieProgress.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/dashboard/ecommerce.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/select2/select2.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/vendor/bootstrap-select/bootstrap-select.css')); ?>">
   <link rel="stylesheet" href="<?php echo e(asset('global/vendor/clockpicker/clockpicker.css')); ?>">
   <link rel="stylesheet" href="<?php echo e(asset('global/vendor/bootstrap-datepicker/bootstrap-datepicker.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('global/vendor/multi-select/multi-select.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/uikit/badges.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/structure/alerts.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('global/vendor/typeahead-js/typeahead.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('global/vendor/summernote/summernote.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('theme/examples/css/forms/advanced.css')); ?>">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.css">

    <!-- Fonts -->
    <link rel="stylesheet" href="<?php echo e(asset('global/fonts/weather-icons/weather-icons.css')); ?>">

    <!-- Fonts -->
        <link rel="stylesheet" href="<?php echo e(asset('global/fonts/font-awesome/font-awesome.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('global/fonts/web-icons/web-icons.min.css')); ?>">
      <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">


      <link rel="stylesheet" href="<?php echo e(asset('global/vendor/timepicker/jquery-timepicker.min.css')); ?>">

    <!--[if lt IE 9]>
    <script src="<?php echo e(asset('global/vendor/html5shiv/html5shiv.min.js')); ?>"></script>
    <![endif]-->
    
    <!--[if lt IE 10]>
    <script src="<?php echo e(asset('global/vendor/media-match/media.match.min.js')); ?>"></script>
    <script src="<?php echo e(asset('global/vendor/respond/respond.min.js')); ?>"></script>
    <![endif]-->
    
    <!-- Scripts -->
    <script src="<?php echo e(asset('global/vendor/breakpoints/breakpoints.js')); ?>"></script>
    <script>
      Breakpoints();
    </script>
    <style>
      .custom-hidden{
          display: none;
      }
      .table a {
          text-decoration: none;
      }
        .custom_datatable_padding{
            padding-bottom: 50px !important;
        }
      .custom_datatable_width{
          word-wrap: break-word;
          word-break: break-all;
                 }
        .list_image{
            height: 50px;
            width: 50px;
        }
      .input-controls {
          margin-top: 10px;
          border: 1px solid transparent;
          border-radius: 2px 0 0 2px;
          box-sizing: border-box;
          -moz-box-sizing: border-box;
          height: 32px;
          outline: none;
          box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }
      #searchInput {
          background-color: #fff;
          font-family: Roboto;
          font-size: 15px;
          font-weight: 300;
          margin-left: 12px;
          padding: 0 11px 0 13px;
          text-overflow: ellipsis;
          width: 50%;
      }
      #searchInput:focus {
          border-color: #4d90fe;
      }
      .segment_class{
          color:#0bb2d4;
      }
      .modal-open .select2-container {
          z-index: 0 ! important;
      }
      .report_table{
        font-size: 14px !important;
      }
      .report_table_row_heading{
        background-color: #e4eaec45;
      }
    </style>



  </head>
  <body class="animsition">


<?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/layouts/header.blade.php ENDPATH**/ ?>
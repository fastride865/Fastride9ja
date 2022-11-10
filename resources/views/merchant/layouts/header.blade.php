<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="bootstrap admin template">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="author" content="">
      @php $merchant = get_merchant_id(false); @endphp
    <title>Dashboard | {{ $merchant->BusinessName }}</title>

    <link rel="apple-touch-icon" href="{{ asset('theme/images/apple-touch-icon.png') }}">
      <link rel="shortcut icon" href="{{ isset($merchant->BusinessLogo) && !empty($merchant->BusinessLogo) ? get_image($merchant->BusinessLogo,'business_logo',$merchant->id,true): asset('theme/images/favicon.ico') }}">
  {{--    <link rel="shortcut icon" href="{{ asset('theme/images/favicon.ico') }}">--}}
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/site.min.css') }}">
    
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/asscrollable/asScrollable.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/intro-js/introjs.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/slidepanel/slidePanel.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/flag-icon-css/flag-icon.css') }}">
      <link rel="stylesheet" href="{{ asset('global/vendor/switchery/switchery.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/examples/css/forms/layouts.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/datatables.net-scroller-bs4/dataTables.scroller.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/datatables.net-responsive-bs4/dataTables.responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/examples/css/tables/datatable.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/chartist/chartist.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/asspinner/asSpinner.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/asspinner/asSpinner.min.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/aspieprogress/asPieProgress.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/examples/css/dashboard/ecommerce.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('global/vendor/bootstrap-select/bootstrap-select.css')}}">
   <link rel="stylesheet" href="{{ asset('global/vendor/clockpicker/clockpicker.css')}}">
   <link rel="stylesheet" href="{{ asset('global/vendor/bootstrap-datepicker/bootstrap-datepicker.css')}}">
  <link rel="stylesheet" href="{{ asset('global/vendor/multi-select/multi-select.css')}}">
  <link rel="stylesheet" href="{{ asset('theme/examples/css/uikit/badges.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/examples/css/structure/alerts.css') }}">
  <link rel="stylesheet" href="{{ asset('global/vendor/typeahead-js/typeahead.css') }}">
  <link rel="stylesheet" href="{{ asset('global/vendor/summernote/summernote.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/examples/css/forms/advanced.css') }}">
{{--  <link rel="stylesheet" href="{{ asset('global/src/vendor/datatables/src/jquery.dataTables.css') }}">--}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.css">

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('global/fonts/weather-icons/weather-icons.css') }}">

    <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('global/fonts/font-awesome/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('global/fonts/web-icons/web-icons.min.css') }}">
      <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic">


      <link rel="stylesheet" href="{{ asset('global/vendor/timepicker/jquery-timepicker.min.css')}}">
{{--      <link rel="stylesheet" href="{{ asset('global/vendor/jquery-labelauty/jquery-labelauty.min.css')}}">--}}
    <!--[if lt IE 9]>
    <script src="{{ asset('global/vendor/html5shiv/html5shiv.min.js') }}"></script>
    <![endif]-->
    
    <!--[if lt IE 10]>
    <script src="{{ asset('global/vendor/media-match/media.match.min.js') }}"></script>
    <script src="{{ asset('global/vendor/respond/respond.min.js') }}"></script>
    <![endif]-->
    
    <!-- Scripts -->
    <script src="{{ asset('global/vendor/breakpoints/breakpoints.js') }}"></script>
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
{{--    <script>--}}
{{--      $("#myLoader").show();--}}
{{--    </script>--}}
  </head>
  <body class="animsition">



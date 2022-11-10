<!DOCTYPE html>

<html>
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
        <title></title>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"/>
        
    </head>
    @php
        $currency = $booking->CountryArea->Country->isoCode;
      /*  date_default_timezone_set($booking->CountryArea->timezone);*/
    @endphp
    <body style="background-color: #d6d6d5; padding:20px">
        <div class="container content-width" style="background-color: #ffffff;max-width: 700px;min-width:300px; margin:auto; font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <div class="logo" style="margin-top:30px;text-align:center; padding-top:40px; padding-left:40px;padding-right:40px;  background-image: url({{asset('basic-images/color-bg.png')}}); background-repeat: no-repeat; background-size: cover;">
                <table style="margin:0;border-collapse: collapse;width: 100%;">
                    <tbody>
                        <tr>
                            <td>
                                <table align="left" style="width:190px;max-width:100%;padding-bottom:10px;">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <img height="80" width="80" src="{{ get_image($booking->Merchant->BusinessLogo,'business_logo',$booking->merchant_id,true) }}"/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table align="right" style="margin:0;width:190px;max-width:100%;padding-bottom:10px;">
                                    <tbody>
                                        <tr>
                                            <td style="border-bottom: none;text-align: right;">
                                                <p style="font-size: 13px; margin-bottom: 5px;">@lang("$s_string_file.total") {{$currency.' '.$booking->final_amount_paid}}</p>
                                                {{--<h6 style="font-size:14px; margin:0;">{{date('D, M d, Y',$booking->booking_timestamp)}}</h6>--}}
                                                <h6 style="font-size:14px; margin:0;">{{convertTimeToUSERzone($booking->created_at,$booking->CountryArea->timezone,null,
                        $booking->Merchant)}}</h6>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p style="font-size: 34px; margin-left:15px; text-align:left;margin-bottom: 5px;">@lang("$s_string_file.mail_content_5"), {{ ucfirst($booking->User->first_name) }}</p>
                <table style="margin-left:15px;border-collapse: collapse;width: 100%;">
                    <tbody>
                        <tr>
                            <td style="border-bottom: none;padding:0;">
                                <table align="left" style="margin:0;width:300px;max-width:100%;padding-bottom:10px;">
                                    <tbody>
                                        <tr>
                                            <td style="border-bottom: none; padding:0;">
                                                <p style="font-size: 20px; font-weight: 500; text-align:left;margin-bottom: 5px;color:#fff;">@lang("$s_string_file.ride_invoice_line_2")</p>
                                                
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table align="right" style="margin:0;width:250px;max-width:100%;padding-bottom:10px;">
                                    <tbody>
                                        <tr>
                                            <td style="border-bottom: none;padding:0; padding-right:25px;">
                                                <img width="150" height="150" align="center" src="{{get_image($booking->VehicleType->vehicleTypeImage, 'vehicle', $booking->merchant_id, true, false)}}"/>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h2 style="margin:20px 25px 0 25px; margin-bottom: 5px; text-align: center; background-color: #f3f3f3; padding:5px;">@lang("$s_string_file.ride") @lang("$s_string_file.details")</h2>
            <div class="details" style="padding:10px 40px;">
                <table style="border-collapse: collapse;width: 100%;">
                    <tbody>
                        <tr>
                            <td style="border-bottom: none;padding:0; padding-bottom: 10px;">
                                <table align="left" style="margin:0; width:220px;max-width:100%;padding-bottom:10px;padding-right: 10px;">
                                    <tbody>
                                            <tr>
                                                <td style="border-bottom: none;">
                                                    <p style="font-size: 16px; margin-bottom: 0;"><img width="20px"src="{{asset('basic-images/green-pin.png')}}" style="margin-right:10px;"/>{{date('H:i A',$booking->BookingDetail->start_timestamp)}}</p>
                                                    <p style="font-size: 16px; margin-top:0;margin-left: 25px;">{{$booking->pickup_location}}</p>
                                                 </td>
                                            </tr>
                                            <tr>
                                                <td style="border-bottom: none;">
                                                    <p style="font-size: 16px;margin-bottom: 0;"><img width="20px"src="{{asset('basic-images/red-pin.png')}}" style="margin-right:10px;"/>{{date('H:i A',$booking->BookingDetail->end_timestamp)}}</p>
                                                    <p style="font-size: 16px; margin-top:0;margin-left: 25px;">{{$booking->drop_location}}</p>
                                                 </td>
                                            </tr>
                                    </tbody>
                                </table>
                                <table align="right" style="width:300px;max-width:100%;padding-right: 10px; padding-bottom:10px;">
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{--<img width="250" src="https://delhitrial.apporioproducts.com/email/images/map.png"/>    --}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h2 style="margin:20px 40px 0 40px; margin-bottom: 5px; text-align: center; background-color: #f3f3f3; padding:5px;">@lang("$s_string_file.fare") @lang("$s_string_file.details")</h2>
            <div class="user-details" style="padding:10px 40px;">
                <table style="border-collapse: collapse;width: 100%;">
                    <tbody>
                        @if(!empty($holder))
                            @foreach($holder as $parameter)
                                <tr style="background-color: #f3f3f3;border-top: 1px solid #d3d3dd; margin-bottom: 10px;">
                                    <td>
                                        <p style="font-size: 16px; padding:10px; margin:0;">{{$parameter['highlighted_text']}}</p>
                                    </td>
                                    <td style="border-bottom: none; text-align: right; ">
                                        <p style="font-size: 16px; padding:10px; margin:0;">{{ $parameter['value_text']}}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <p style="margin:20px 40px 0 40px; font-size: 20px; font-weight: bold; margin-bottom: 5px; text-align: center; background-color: #f3f3f3; padding:5px;">@lang("$s_string_file.payment")</p>
            <div class="user-details" style="padding:10px 40px; margin-right:40px;padding-bottom: 40px;">
                <table style="margin-left:25px;border-collapse: collapse;width: 100%;">
                    <tbody>
                        <tr>
                            <td style="border-bottom: none;padding:0; padding-bottom: 10px;">
                                <table align="left" style="margin:0;max-width:100%;padding-bottom:10px;padding-right: 10px;">
                                    <tbody>
                                        <tr style="display: inline-block;">
                                            <td style="border-bottom: none; padding:0; padding-right: 7px;">
                                                <p style="font-size: 16px;">@lang("$s_string_file.payment_method") {{ $booking->PaymentMethod->payment_method  }}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table align="right" style="margin:0;max-width:100%;padding-bottom:10px;">
                                    <tbody>
                                        <tr style="display: inline-block;">
                                            <td style="border-bottom: none; text-align: right;">
                                                <p style="font-size: 16px;">{{$currency.' '.$booking->final_amount_paid}}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <div class="container" style="background-color: #f8f8fa;margin:auto;">
            <div class="driver-details" style="padding:10px 40px; margin-right:40px;padding-bottom: 40px;">
                <p style="margin-left: 25px;font-size: 16px;">@lang("$s_string_file.driver") @lang("$s_string_file.name"): {{ucfirst($booking->Driver->first_name)}}</p>
                <table style="margin-left:25px;border-collapse: collapse;width: 100%;">
                    <tbody>
                        <tr>
                            <td style="border-bottom: none;padding:0; padding-bottom: 10px;">
                                <table align="left" style="margin:0;max-width:100%;padding-bottom:10px;padding-right: 10px;border-right:1px solid #ddd;">
                                    <tbody>
                                        <tr style="display: inline-block;">
                                            <td style="border-bottom: none; padding:0; padding-right: 30px;">
                                                <img width="80" height="80" align="center" src="@if ($booking->driver_id) {{ get_image($booking->Driver->profile_image,'driver',$booking->merchant_id) }} @else {{ get_image(null,'driver') }} @endif"/>
                                            </td>
                                            <!-- <td style="border-bottom: none; padding:0;">
                                                <img width="50" height="50" align="center" src="https://delhitrial.apporioproducts.com/email/images/star.png"/>
                                            </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                                <table style="margin:0;max-width:100%;padding-bottom:10px; padding-left: 40px;">
                                    <tbody>
                                        <tr>
                                            <td style="border-left:none;">
                                                <p style="font-size: 12px; margin-bottom: 5px;">{{ucfirst($booking->Driver->rating)}} <img width="12px"src="{{asset('basic-images/rate.png')}}"/> @lang("$s_string_file.rating")</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
            <div class="details"style="margin-left:25px; margin-right: 25px; background-color:#fbfbfb;vertical-align: middle; margin:0; text-align:center;font-weight:normal;">
                <p style="font-size:10px;padding-top:15px; padding-bottom:5px;color:#9b9b9b;margin:0">
                <table width="100%" style="padding:0 15px;margin:0; border-collapse: collapse;width: 100%;">
                    <tbody>
                    <tr>
                        <td style="padding:0;border-bottom: 2px solid #ddd;">
                            <table align="left" style="margin:0;">
                                <tbody>
                                <tr>
                                    <td style="border-bottom: none;padding:0">
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td style="border-bottom: none; padding:0px;"><p style="font-family: normal;">@lang("$string_file.get_app"):</p></td>
                                                <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;"><a href="#"><img alt="App Store" height="20" src="{{asset('basic-images/android.png')}}" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="App Store" width="20"/></a></td>
                                                <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;"><a href="#"><img alt="Play Store" height="20" src="{{asset('basic-images/ios.png')}}" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Play Store" width="20"/></a></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            @if (!empty($temp->social_links))
                                @php

                                    $social_links = get_object_vars(json_decode($temp->social_links));

                                    $social_links = $social_links['links'];
                                @endphp

                                <table align="right" style="margin:0; max-width:142px">
                                    <tbody>
                                    <tr>
                                        <td style="border-bottom: none; padding:0">
                                            <table>
                                                <tbody>
                                                <tr align="center" style="display: inline-block;">
                                                    @if(isset($social_links->facebook) && !empty($social_links->facebook))
                                                        <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;">
                                                            <a class="text-dark" href="{{$social_links->facebook}}" target="_blank">
                                                                <img alt="LinkedIn" height="20" src="{{asset('basic-images/facebook2x.png')}}" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Facebook" width="24"/>
                                                            </a>
                                                        {{--                                                <a href="https://www.facebook.com/" target="_blank"><img alt="Facebook" height="20" src="https://delhitrial.apporioproducts.com/email/images/facebook2x.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Facebook" width="24"/></a></td>--}}
                                                    @endif
                                                    @if(isset($social_links->twitter) && !empty($social_links->twitter))
                                                        <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;">
                                                            <a class="text-dark" href="{{$social_links->twitter}}" target="_blank">
                                                                <img alt="LinkedIn" height="20" src="{{asset('basic-images/twitter2x.png')}}" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Twitter" width="24"/>
                                                            </a>
                                                            {{--                                                <a href="https://twitter.com/" target="_blank"><img alt="Twitter" height="20" src="https://delhitrial.apporioproducts.com/email/images/twitter2x.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Twitter" width="24"/></a>--}}
                                                        </td>
                                                    @endif
                                                    @if(isset($social_links->instagram) && !empty($social_links->instagram))
                                                        <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;">
                                                            {{--                                                <a href="https://instagram.com/" target="_blank"><img alt="Instagram" height="20" src="https://delhitrial.apporioproducts.com/email/images/instagram2x.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Instagram" width="24"/></a>--}}
                                                            <a class="text-dark" href="{{$social_links->instagram}}" target="_blank">
                                                                <img alt="LinkedIn" height="20" src="{{asset('basic-images/instagram2x.png')}}" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Instagram" width="24"/>
                                                            </a>
                                                        </td>
                                                    @endif
                                                    @if(isset($social_links->linkedin) && !empty($social_links->linkedin))
                                                        <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;">
                                                            <a class="text-dark" href="{{$social_links->linkedin}}" target="_blank">
                                                                <img alt="LinkedIn" height="20" src="{{asset('basic-images/linkedin2x.png')}}" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="LinkedIn" width="24"/>
                                                            </a>
                                                            {{--                                                <a href="https://www.linkedin.com/" target="_blank"><img alt="LinkedIn" height="20" src="https://delhitrial.apporioproducts.com/email/images/linkedin2x.png" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="LinkedIn" width="24"/></a>--}}
                                                        </td>
                                                    @endif
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
                </p>
                <p style="font-size:10px;padding-top:15px; padding-bottom:5px;color:#9b9b9b;margin:0">©{{$booking->Merchant->BusinessName}}! . @lang("$s_string_file.all_right_reserved")</p>
                <p style="font-size:10px;padding-bottom:20px; color:#9b9b9b;margin:0">@lang("$s_string_file.terms_conditions") | @lang("$s_string_file.privacy_policy")</p>
            </div>
       </div>
    </body>
</html>
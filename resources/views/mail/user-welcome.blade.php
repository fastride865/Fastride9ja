<!DOCTYPE html>

<html>
    <head> 
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
        <meta content="width=device-width" name="viewport"/> 
        <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
        <title></title>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"/>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
        
    </head>
    <body style="background-color: #d6d6d5;font-family: 'Roboto', sans-serif; padding:20px">
        <div class="container content-width" style="max-width: 700px;min-width:300px; margin:auto; border:2px solid #000000; ">
            @php
                $heading=trans("$string_file.welcome");
                $subheading=trans("$string_file.welcome_to").' '.$merchant->BusinessName;
                $message=trans("$string_file.thanks_fo_choosing").' '.$merchant->BusinessName;
                $image='';
                $social_links = [];

            if(!empty($temp))
            {
                $heading = $temp->heading;
                $subheading = $temp->subheading;
                $message = $temp->message;
                $image=json_decode($temp->image);
                $image=$image->filename;
                if(!empty($temp->social_links))
                {
                $social_links = get_object_vars(json_decode($temp->social_links));
                $social_links = $social_links['links'];
                }
            }
            @endphp
            <div style="text-align:center;background-color: #c7c719; padding: 30px; ">
                <img width="80" height="80"  src="{{get_image($merchant->BusinessLogo,'business_logo',$merchant->id, true,true,"email")}}"/>
                <p style="font-size:40px;color:#000000; font-weight: 600;margin: 9px auto;">{{$heading}}!</p>
                <p style="font-size:12px;color:#1d1c1cc4;">{{$subheading}}</p>
            </div>
            <div style="padding:40px; ">            
                <p style="text-align:center;font-size:20px;font-weight: 500;margin: 9px auto;">{{$message}} </p>
                <table style=" margin:10px auto; border-collapse: collapse;width: 100%;">
                    <tbody>
                        <tr>
                            <td style="">
                                <table align="left" style="margin:0;max-width:170px">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <h3>@lang("$string_file.user_name")</h3>
                                                <p style="font-size:12px;color:#1d1c1cc4;">
                                                    @if ($login_type=='EMAIL')
                                                        {{$user->email}}
                                                    @else
                                                        {{$user->UserPhone}}
                                                    @endif
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h3>@lang("$string_file.registered")
                                                @if ($login_type=='EMAIL')
                                                    @lang("$string_file.phone")
                                                @else
                                                    @lang("$string_file.email")
                                                @endif</h3>
                                                <p style="font-size:12px;color:#1d1c1cc4;">
                                                     @if ($login_type=='EMAIL')
                                                        {{$user->UserPhone}}
                                                    @else
                                                        {{$user->email}}
                                                    @endif
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <h3>@lang("$string_file.user_id")</h3>
                                                <p style="font-size:12px;color:#1d1c1cc4;">{{$user->user_merchant_id}}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="">
                                <table align="left" style="margin:0;max-width:150px">
                                    <tbody>
                                        <tr>
                                            <td>
                                                @if(!empty($image))
                                                <img class="rounded img-bordered img-bordered-primary" width="150" height="150" src="{{get_image($image,'email',$merchant->id,true,true,"email")}}" alt="...">
                                               @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="vertical-align: middle; margin:30px 25px 0 25px; text-align:center;font-weight:normal;">
                    <p style="font-size:10px;padding-top:15px; padding-bottom:5px;color:#000000;margin:0">©{{$merchant->BusinessName}}! . @lang("$string_file.all_right_reserved")</p>
                    <p style="font-size:10px;padding-bottom:20px; color:#000000;margin:0">@lang("$string_file.terms_conditions") | @lang("$string_file.privacy_policy")</p>
                </div>
                <div style="vertical-align: middle; margin:30px 25px 0 25px; text-align:center;font-weight:normal;">
                    @if(!empty($temp->social_links))
                            @if(isset($social_links->twitter) && !empty($social_links->twitter))
                                <a class="text-dark" href="{{$social_links->twitter}}" target="_blank"><img src="{{url('/basic-images/twitter2x.png')}}" width="30px" alt="Image"/></a>
                            @endif
                            @if(isset($social_links->facebook) && !empty($social_links->facebook))
                                <a class="text-dark" href="{{$social_links->facebook}}" target="_blank"><img src="{{url('/basic-images/facebook2x.png')}}" width="30px" alt="Image"/></a>
                            @endif
                            @if(isset($social_links->instagram) && !empty($social_links->instagram))
                                <a class="text-dark" href="{{$social_links->instagram}}" target="_blank"><img src="{{url('/basic-images/instagram2x.png')}}" width="30px" alt="Image"/></a>
                            @endif
                            @if(isset($social_links->linkedin) && !empty($social_links->linkedin))
                                <a class="text-dark" href="{{$social_links->linkedin}}" target="_blank"><img src="{{url('/basic-images/linkedin2x.png')}}" width="30px" alt="Image"/></a>
                            @endif
                    @endif
                </div>
            </div>
       </div>
    </body>                                                                      
</html>

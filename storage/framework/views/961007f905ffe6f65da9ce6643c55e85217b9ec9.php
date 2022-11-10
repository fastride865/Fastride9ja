<!DOCTYPE html>

<html>
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta content="width=device-width" name="viewport"/>
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <title></title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css"/>

</head>
<body style="background-color: #f6f6f6; padding:20px">
<div class="container content-width" style="background-color: #ffffff;max-width: 700px;min-width:300px;margin: 20px auto;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
    <div class="logo" style="margin-top:30px;text-align:center; padding:10px 0; ">
        <img align="center" width="100" height="100" src="<?php echo e(get_image($booking->Merchant->BusinessLogo,'business_logo',$booking->merchant_id,true,true,"email")); ?>"/>
    </div>
    <div class="user-details" style="font-size: 13px; font-weight: 400;text-align: left;padding-left:25px; padding-right:25px;">
        <p style="border-bottom: 1px solid #ddd;"></p>
        <?php echo app('translator')->get("$string_file.f_cap_to"); ?>,<br>
        <p><?php echo e($booking->Merchant->BusinessName); ?></p>
        <p>
        <?php echo app('translator')->get("$string_file.new_ride_mail_message"); ?>
        </p>
    </div>
    <div class="user-details" style="padding-left:25px; margin-right:25px;">
        <table style="margin:0;width: 100%;">
            <tbody>
            <tr>
                <td style="border-bottom: none; padding:0;">
                    <p style="font-size: 13px; font-weight: 400; margin-bottom: 5px;"><?php echo app('translator')->get("$string_file.ride_id"); ?>:</p>
                    <h6 style="font-weight:900;font-size:14px;margin:0;"><?php echo e($booking->merchant_booking_id); ?></h6>
                </td>
                <td style="border-bottom: none; padding:0;">
                    <p style="font-size: 13px; font-weight: 400; margin-bottom: 5px;"><?php echo app('translator')->get("$string_file.ride_booked_at"); ?>:</p>
                    <h6 style="font-weight:900;font-size:14px;margin:0;"><?php echo e(date_format($booking->created_at,'D, M d, Y H:i a')); ?></h6>
                </td>
                <td style="border-bottom: none;padding:0; text-align: right;">
                    <p style="font-size: 13px; font-weight: 400; margin-bottom: 5px;"><?php echo app('translator')->get("$string_file.ride_status"); ?>:</p>
                    <h6 style="font-weight:900;font-size:14px; margin:0;color: #79b33b;"><?php echo app('translator')->get("$string_file.new_ride"); ?></h6>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="details" style="margin-left:25px; margin-right: 25px;margin-top:25px;font-size: 12px; font-weight: bold;">
        <table style="border-collapse: collapse;width: 100%;">
            <thead style="background: #e9e9e9; line-height: 1.2em;">
            <tr>
                <th style="text-align: left;padding: 15px;"><?php echo app('translator')->get("$string_file.id"); ?></th>
                <th style="text-align: left;padding: 15px;"><?php echo app('translator')->get("$string_file.pickup"); ?> </th>
                <th style="text-align: left;padding: 15px;"><?php echo app('translator')->get("$string_file.drop_off"); ?></th>
                <th style="text-align: right;padding: 15px;"><?php echo app('translator')->get("$string_file.estimate_distance"); ?></th>
                <th style="text-align: right;padding: 15px;"><?php echo app('translator')->get("$string_file.estimate_amount"); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php $sn = 1; $currency = $booking->CountryArea->Country->isoCode; ?>

            <tr style="border-bottom: 2px solid #ddd;">
                <td style="padding: 15px;"><?php echo e($booking->merchant_booking_id); ?></td>
                <td style="padding: 15px;"><?php echo e($booking->pickup_location); ?></td>
                <td style="padding: 15px;"><?php echo e($booking->drop_location); ?></td>
                <td style="text-align: right; padding: 15px;"><?php echo e($booking->estimate_distance); ?></td>
                <td style="text-align: right; padding: 15px;"><?php echo e($currency.$booking->estimate_bill); ?></td>
            </tr>


























            </tbody>
        </table>
    </div>
    <div class="details" style="margin-left:25px; margin-right: 25px; font-size: 12px;">
        <table style="margin:0;border-collapse: collapse;width: 100%;">
            <tbody>
            <tr>
                <td style="padding-top:15px; padding-left: 0; border-bottom: 2px solid #ddd;">
                    <table align="left" style="margin:0;max-width:100%;padding-bottom:10px;">
                        <tbody>
                        <tr>
                            <td style="border-bottom: none; padding:0;">

                                <h6 style="margin:0;margin-bottom:5px;font-weight:900;font-size:14px;"><?php echo app('translator')->get("$string_file.user_details"); ?>:</h6>

                            </td>




                        </tr>
                        <tr>
                            <td style="border-bottom: none; padding:0;">
                                <p style="margin:0;font-weight:normal;line-height:1.6">
                                    <?php echo app('translator')->get("$string_file.name"); ?> : <?php echo e(is_demo_data($booking->User->first_name.' '.$booking->User->last_name,$booking->Merchant)); ?><br>
                                    <?php echo app('translator')->get("$string_file.phone"); ?> : <?php echo e(is_demo_data($booking->User->UserPhone)); ?> <br>
                                    <?php echo app('translator')->get("$string_file.email"); ?> : <?php echo e(is_demo_data($booking->User->email)); ?> <br>
                                </p>
                            </td>



                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="details" style="margin-left:25px; margin-right: 25px;vertical-align: middle;font-weight:normal;">
        <table width="100%" style="padding:0 15px;margin:0; border-collapse: collapse;width: 100%;">
            <tbody>
            <tr>
                <td style="padding:0;border-bottom: 2px solid #ddd;">
                    <table align="left" style="margin:0;max-width:140px">
                        <tbody>
                        <tr>
                            <td style="border-bottom: none;padding:0">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td style="border-bottom: none; padding:0px;"><p style="font-family: normal;"><?php echo app('translator')->get("$string_file.get_app"); ?>:</p></td>
                                        <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;"><a href="#"><img alt="App Store" height="20" src="<?php echo e(asset('basic-images/android.png')); ?>" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="App Store" width="20"/></a></td>
                                        <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;"><a href="#"><img alt="Play Store" height="20" src="<?php echo e(asset('basic-images/ios.png')); ?>" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Play Store" width="20"/></a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <?php if(!empty($temp->social_links)): ?>
                        <?php

                                $social_links = get_object_vars(json_decode($temp->social_links));

                                $social_links = $social_links['links'];
                        ?>

                        <table align="right" style="margin:0; max-width:142px">
                            <tbody>
                            <tr>
                                <td style="border-bottom: none; padding:0">
                                    <table>
                                        <tbody>
                                        <tr align="center" style="display: inline-block;">
                                            <?php if(isset($social_links->facebook) && !empty($social_links->facebook)): ?>
                                            <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;">
                                                <a class="text-dark" href="<?php echo e($social_links->facebook); ?>" target="_blank">
                                                    <img alt="LinkedIn" height="20" src="<?php echo e(asset('basic-images/facebook2x.png')); ?>" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Facebook" width="24"/>
                                                </a>

                                            <?php endif; ?>
                                            <?php if(isset($social_links->twitter) && !empty($social_links->twitter)): ?>
                                            <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;">
                                                <a class="text-dark" href="<?php echo e($social_links->twitter); ?>" target="_blank">
                                                    <img alt="LinkedIn" height="20" src="<?php echo e(asset('basic-images/twitter2x.png')); ?>" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Twitter" width="24"/>
                                                </a>

                                            </td>
                                            <?php endif; ?>
                                            <?php if(isset($social_links->instagram) && !empty($social_links->instagram)): ?>
                                            <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;">

                                                <a class="text-dark" href="<?php echo e($social_links->instagram); ?>" target="_blank">
                                                    <img alt="LinkedIn" height="20" src="<?php echo e(asset('basic-images/instagram2x.png')); ?>" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="Instagram" width="24"/>
                                                </a>
                                            </td>
                                            <?php endif; ?>
                                            <?php if(isset($social_links->linkedin) && !empty($social_links->linkedin)): ?>
                                            <td style="border-bottom: none; word-break: break-word; padding-right: 1px; padding-left: 1px;">
                                                <a class="text-dark" href="<?php echo e($social_links->linkedin); ?>" target="_blank">
                                                    <img alt="LinkedIn" height="20" src="<?php echo e(asset('basic-images/linkedin2x.png')); ?>" style="text-decoration: none; -ms-interpolation-mode: bicubic; height: auto; border: 0; display: block;" title="LinkedIn" width="24"/>
                                                </a>

                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                            </tbody>
                                        </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                    <?php endif; ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="details"style="margin-left:25px; margin-right: 25px; background-color:#fbfbfb;vertical-align: middle; margin:0; text-align:center;font-weight:normal;">
        <p style="font-size:10px;padding-top:15px; padding-bottom:5px;color:#9b9b9b;margin:0">Â© <?php echo e($booking->Merchant->BusinessName); ?>! . <?php echo app('translator')->get("$string_file.all_right_reserved"); ?></p>
        <p style="font-size:10px;padding-bottom:20px; color:#9b9b9b;margin:0"><?php echo app('translator')->get("$string_file.terms_conditions"); ?> | <?php echo app('translator')->get("$string_file.privacy_policy"); ?></p>
    </div>
</div>
</body>
</html><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/mail/new-ride-request.blade.php ENDPATH**/ ?>
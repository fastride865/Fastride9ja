<?php
namespace App\Http\Controllers\Helper;


use App\Models\CustomerSupport;
use App\Models\Driver;
use App\Models\User;
use App\Models\Merchant;
use App\Models\DriverAccount;
use App\Models\BusinessSegment\BusinessSegment;

class EmailTemplates
{
    
    public function DriverBillTemplate(DriverAccount $driver_account_obj)
    {
        $message = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            </head>
            <body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
            <style>
                @media  only screen and (max-width: 600px) {
                    .inner-body {
                        width: 100% !important;
                    }
            
                    .footer {
                        width: 100% !important;
                    }
                }
            
                @media  only screen and (max-width: 500px) {
                    .button {
                        width: 100% !important;
                    }
                }
            </style>
            <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"><tr>
                    <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                            <tr>
                                <td class="header" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;">
                                    <a href="#" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #bbbfc3; font-size: 19px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 white;">
                                        '.$driver_account_obj->Driver->Merchant->BusinessName.'
                                    </a>
                                </td>
                            </tr>
                            <!-- Email Body --><tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; border-bottom: 1px solid #EDEFF2; border-top: 1px solid #EDEFF2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                                    <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                        <!-- Body content --><tr>
                                            <td class="content-cell" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">
                                                    Hi '.$driver_account_obj->Driver->first_name.' '.$driver_account_obj->Driver->last_name.', Your Bill Details:
                                                </h1>
                                                <table class="panel" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;"><tr>
                                                        <td class="panel-content" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #EDEFF2; padding: 16px;">
                                                            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"><tr>
                                                                    <td class="panel-item" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 0;">
                                                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                                            <tr style="font-size: 22px; font-weight: bold;">
                                                                                <td>Description</td>
                                                                                <td align="right">Values</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>&nbsp;</td>
                                                                                <td>&nbsp;</td>
                                                                            </tr>
                                                                 
                                                                            <tr style="font-size: 20px;">
                                                                                <td align="left">From Date</td>
                                                                                <td align="right">'.$driver_account_obj->from_date.'</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>&nbsp;</td> 
                                                                            </tr>
                                                                            
                                                                            <tr style="font-size: 20px;">
                                                                                <td align="left">To Date</td>
                                                                                <td align="right">'.$driver_account_obj->to_date.'</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>&nbsp;</td> 
                                                                            </tr>
                                                                            
                                                                            <tr style="font-size: 20px;">
                                                                                <td align="left">Amount</td>
                                                                                <td align="right">'.$driver_account_obj->amount.'</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>&nbsp;</td> 
                                                                            </tr>
                                                                            
                                                                            <tr style="font-size: 20px;">
                                                                                <td align="left">Settle Date</td>
                                                                                <td align="right">'.$driver_account_obj->settle_date.'</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>&nbsp;</td> 
                                                                            </tr>
                                                                            
                                                                            <tr style="font-size: 20px;">
                                                                                <td align="left">Total Trips</td>
                                                                                <td align="right">'.$driver_account_obj->total_trips.'</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>&nbsp;</td> 
                                                                            </tr>
                                                                            
                                                                            <tr style="font-size: 20px;">
                                                                                <td align="left">Reference Number</td>
                                                                                <td align="right">'.$driver_account_obj->referance_number.'</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>&nbsp;</td> 
                                                                            </tr>
                                                                            <!--<tr style="font-size: 20px; font-weight: bolder;">
                                                                                <td>Total Bill</td>
                                                                                <td align="right">{{ $template->final_amount_paid }}</td>
                                                                            </tr>-->
                                                                        </table>
                                                                    </td>
                                                                </tr></table>
                                                        </td>
                                                    </tr></table>
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                Thanks,<br>
                                                    '.$driver_account_obj->Driver->Merchant->BusinessName.'</p>
            
            
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                    <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"><tr>
                                            <td class="content-cell" align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #AEAEAE; font-size: 12px; text-align: center;">
                                                ©'.date('Y').' '.$driver_account_obj->Driver->Merchant->BusinessName.'. All rights reserved.</p>
                                            </td>
                                        </tr></table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr></table>
            </body>
            </html>
            ';
        return $message;
    }
    
    public function SignUpOtpTemplate($merchant_id,$message_otp = null)
    {
    
        $merchant = Merchant::findorfail($merchant_id);
        $message = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            </head>
            <body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
            <style>
                @media  only screen and (max-width: 600px) {
                    .inner-body {
                        width: 100% !important;
                    }
            
                    .footer {
                        width: 100% !important;
                    }
                }
            
                @media  only screen and (max-width: 500px) {
                    .button {
                        width: 100% !important;
                    }
                }
            </style>
            <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"><tr>
                    <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                            <tr>
                                <td class="header" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;">
                                    <a href="#" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #bbbfc3; font-size: 19px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 white;">
                                        '.$merchant->BusinessName.'
                                    </a>
                                </td>
                            </tr>
                            <!-- Email Body --><tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; border-bottom: 1px solid #EDEFF2; border-top: 1px solid #EDEFF2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                                    <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                        <!-- Body content --><tr>
                                            <td class="content-cell" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">
                                                    Welcome To Our Taxi Service
                                                </h1>
                                                <table class="panel" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;"><tr>
                                                        <td class="panel-content" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #EDEFF2; padding: 16px;">
                                                            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"><tr>
                                                                    <td class="panel-item" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 0;">
                                                                        <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left; margin-bottom: 0; padding-bottom: 0;">
                                                                            Hi User, '.$message_otp.'
                                                                        </p>
                                                                    </td>
                                                                </tr></table>
                                                        </td>
                                                    </tr></table>
                                                    <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                    Hey, Keep the OTP Secure!!
                                                </p>
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                Thanks,<br>
                                                    '.$merchant->BusinessName.'</p>
            
            
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                    <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"><tr>
                                            <td class="content-cell" align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #AEAEAE; font-size: 12px; text-align: center;">
                                                ©'.date('Y').' '.$merchant->BusinessName.'. All rights reserved.</p>
                                            </td>
                                        </tr></table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr></table>
            </body>
            </html>
            ';
        return $message;
    }
    
    public function DriverSignUpOtpTemplate($merchant_id, $message_otp = null)
    {
        $merchant = Merchant::findorfail($merchant_id);
        $message = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            </head>
            <body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
            <style>
                @media  only screen and (max-width: 600px) {
                    .inner-body {
                        width: 100% !important;
                    }
            
                    .footer {
                        width: 100% !important;
                    }
                }
            
                @media  only screen and (max-width: 500px) {
                    .button {
                        width: 100% !important;
                    }
                }
            </style>
            <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"><tr>
                    <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                            <tr>
                                <td class="header" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;">
                                    <a href="#" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #bbbfc3; font-size: 19px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 white;">
                                        '.$merchant->BusinessName.'
                                    </a>
                                </td>
                            </tr>
                            <!-- Email Body --><tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; border-bottom: 1px solid #EDEFF2; border-top: 1px solid #EDEFF2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                                    <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                        <!-- Body content --><tr>
                                            <td class="content-cell" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">
                                                    Welcome To Our Taxi Service
                                                </h1>
                                                <table class="panel" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;"><tr>
                                                        <td class="panel-content" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #EDEFF2; padding: 16px;">
                                                            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"><tr>
                                                                    <td class="panel-item" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 0;">
                                                                        <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left; margin-bottom: 0; padding-bottom: 0;">
                                                                            Hi Driver, '.$message_otp.'
                                                                        </p>
                                                                    </td>
                                                                </tr></table>
                                                        </td>
                                                    </tr></table>
                                                    <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                    Hey, Keep the OTP Secure!!
                                                </p>
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                Thanks,<br>
                                                    '.$merchant->BusinessName.'</p>
            
            
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                    <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"><tr>
                                            <td class="content-cell" align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #AEAEAE; font-size: 12px; text-align: center;">
                                                ©'.date('Y').' '.$merchant->BusinessName.'. All rights reserved.</p>
                                            </td>
                                        </tr></table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr></table>
            </body>
            </html>
            ';
        return $message;
    }

    public function ForgotPasswordTemplate(User $user, $message_otp  = null)
    {
        $message = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            </head>
            <body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
            <style>
                @media  only screen and (max-width: 600px) {
                    .inner-body {
                        width: 100% !important;
                    }
            
                    .footer {
                        width: 100% !important;
                    }
                }
            
                @media  only screen and (max-width: 500px) {
                    .button {
                        width: 100% !important;
                    }
                }
            </style>
            <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"><tr>
                    <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                            <tr>
                                <td class="header" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;">
                                    <a href="#" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #bbbfc3; font-size: 19px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 white;">
                                        '.$user->Merchant->BusinessName.'
                                    </a>
                                </td>
                            </tr>
                            <!-- Email Body --><tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; border-bottom: 1px solid #EDEFF2; border-top: 1px solid #EDEFF2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                                    <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                        <!-- Body content --><tr>
                                            <td class="content-cell" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">
                                                    Forgot Your Password ?
                                                </h1>
                                                <table class="panel" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;"><tr>
                                                        <td class="panel-content" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #EDEFF2; padding: 16px;">
                                                            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"><tr>
                                                                    <td class="panel-item" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 0;">
                                                                        <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left; margin-bottom: 0; padding-bottom: 0;">
                                                                            Hi '.$user->first_name.' '.$user->last_name.', '.$message_otp.'
                                                                        </p>
                                                                    </td>
                                                                </tr></table>
                                                        </td>
                                                    </tr></table>
                                                    <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                    Hey '.$user->first_name.' '.$user->last_name.', Keep the OTP Secure!!
                                                </p>
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                Thanks,<br>
                                                    '.$user->Merchant->BusinessName.'</p>
            
            
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                    <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"><tr>
                                            <td class="content-cell" align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #AEAEAE; font-size: 12px; text-align: center;">
                                                ©'.date('Y').' '.$user->Merchant->BusinessName.'. All rights reserved.</p>
                                            </td>
                                        </tr></table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr></table>
            </body>
            </html>
            ';

        return $message;
    }

    public function ForgotPasswordTemplateDriver(Driver $driver, $message_otp  = null)
    {
        $message = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            </head>
            <body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
            <style>
                @media  only screen and (max-width: 600px) {
                    .inner-body {
                        width: 100% !important;
                    }
            
                    .footer {
                        width: 100% !important;
                    }
                }
            
                @media  only screen and (max-width: 500px) {
                    .button {
                        width: 100% !important;
                    }
                }
            </style>
            <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"><tr>
                    <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                            <tr>
                                <td class="header" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;">
                                    <a href="#" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #bbbfc3; font-size: 19px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 white;">
                                        '.$driver->Merchant->BusinessName.'
                                    </a>
                                </td>
                            </tr>
                            <!-- Email Body --><tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; border-bottom: 1px solid #EDEFF2; border-top: 1px solid #EDEFF2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                                    <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                        <!-- Body content --><tr>
                                            <td class="content-cell" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">
                                                    Forgot Your Password ?
                                                </h1>
                                                <table class="panel" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;"><tr>
                                                        <td class="panel-content" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #EDEFF2; padding: 16px;">
                                                            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"><tr>
                                                                    <td class="panel-item" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 0;">
                                                                        <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left; margin-bottom: 0; padding-bottom: 0;">
                                                                            Hi '.$driver->first_name.' '.$driver->last_name.', '.$message_otp.'
                                                                        </p>
                                                                    </td>
                                                                </tr></table>
                                                        </td>
                                                    </tr></table>
                                                    <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                    Hey '.$driver->first_name.' '.$driver->last_name.', Keep the OTP Secure!!
                                                </p>
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                Thanks,<br>
                                                    '.$driver->Merchant->BusinessName.'</p>
            
            
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                    <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"><tr>
                                            <td class="content-cell" align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #AEAEAE; font-size: 12px; text-align: center;">
                                                ©'.date('Y').' '.$driver->Merchant->BusinessName.'. All rights reserved.</p>
                                            </td>
                                        </tr></table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr></table>
            </body>
            </html>
            ';

        return $message;
    }


    public function CustomerSupportTemplate(CustomerSupport $customerSupportData)
    {
        $application_name = $customerSupportData->application==1 ? "Rider":"Driver";
        $message = '<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <title> </title>
    <!--[if !mso]><!-- -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        #outlook a {
            padding: 0;
        }

        .ReadMsgBody {
            width: 100%;
        }

        .border_bottom{
            border-bottom: 2px dashed #B2B9C3;
        }

        .ExternalClass {
            width: 100%;
        }

        .ExternalClass * {
            line-height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        p {
            display: block;
            margin: 13px 0;
        }
    </style>
    <!--[if !mso]><!-->
    <style type="text/css">
        @media only screen and (max-width:480px) {
            @-ms-viewport {
                width: 320px;
            }
            @viewport {
                width: 320px;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css">
    <style type="text/css">
        @import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);
    </style>
    <style type="text/css">
        @media only screen and (min-width:480px) {
            .mj-column-per-100 {
                width: 100% !important;
                max-width: 100%;
            }
            .mj-column-per-50 {
                width: 50% !important;
                max-width: 50%;
            }
            .mj-column-per-33 {
                width: 33.333333333333336% !important;
                max-width: 33.333333333333336%;
            }
        }
    </style>
    <style type="text/css">
        @media only screen and (max-width:480px) {
            table.full-width-mobile {
                width: 100% !important;
            }
            td.full-width-mobile {
                width: auto !important;
            }
        }
    </style>
</head>

<body style="background-color:#ccd3e0;">
<div style="background-color:#ccd3e0;">
    <div style="background:#ffffff;background-color:#ffffff;Margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
            <tbody>
            <tr>
                <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:20px;padding-top:20px;text-align:center;vertical-align:top;">
                    <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                            <tr>
                                <td align="center" style="font-size:0px;padding:10px 25px;padding-top:10px;padding-right:0px;padding-bottom:10px;padding-left:0px;word-break:break-word;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                                        <tbody>
                                        <tr>
                                            <td style="width:100px;"> <img alt="" height="auto" src="' . asset($customerSupportData->Merchant->BusinessLogo) . '" style="border:none;display:block;outline:none;text-decoration:none;height:auto;width:100%;" width="100" /> </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </table>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="background:#356cc7;background-color:#356cc7;Margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#356cc7;background-color:#356cc7;width:100%;">
            <tbody>
            <tr>
                <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0px;padding-top:0;text-align:center;vertical-align:top;">
                    <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                            <tr>
                                <td align="center" style="font-size:0px;padding:10px 25px;padding-top:28px;padding-right:25px;padding-bottom:18px;padding-left:25px;word-break:break-word;">
                                    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:27px;line-height:1;text-align:center;color:#ABCDEA;"> Customer Support Query
                                        <p style="font-size:16px; color:white">Name: ' . $customerSupportData->name . '</p>
                                        <p style="font-size:16px; color:white">Phone: ' . $customerSupportData->phone . '</p>
                                        <p style="font-size:16px; color:white">Email: ' . $customerSupportData->email . '</p>
                                        <p style="font-size:16px; color:white">Application: '.$application_name.'</p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="background:#FFFFFF;background-color:#FFFFFF;Margin:0px auto;max-width:600px;" align="center">
        <p>Message:</p>
        <p>'.$customerSupportData->query.'</p>
    </div>
    <div style="background:#356cc7;background-color:#356cc7;Margin:0px auto;max-width:600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#356cc7;background-color:#356cc7;width:100%;">
            <tbody>
            <tr>
                <td style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:5px;padding-top:0;text-align:center;vertical-align:top;">
                    <div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                            <tr>
                                <td style="font-size:0px;padding:10px 25px;padding-top:0;padding-right:20px;padding-bottom:0px;padding-left:20px;word-break:break-word;">
                                    <p style="border-top:solid 2px #ffffff;font-size:1;margin:0px auto;width:100%;"> </p>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="font-size:0px;padding:10px 25px;padding-top:20px;padding-right:25px;padding-bottom:20px;padding-left:25px;word-break:break-word;">
                                    <div style="font-family:Helvetica;font-size:15px;line-height:1;text-align:center;color:#FFFFFF;">  <span style="font-size:20px">'.$customerSupportData->Merchant->BusinessName.'</span> </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</body>

</html>';
        return $message;
    }


    public function ForgotPasswordTemplateBusinessSegment(BusinessSegment $business_segment, $message_otp  = null)
    {
        $message = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            </head>
            <body style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
            <style>
                @media  only screen and (max-width: 600px) {
                    .inner-body {
                        width: 100% !important;
                    }
            
                    .footer {
                        width: 100% !important;
                    }
                }
            
                @media  only screen and (max-width: 500px) {
                    .button {
                        width: 100% !important;
                    }
                }
            </style>
            <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;"><tr>
                    <td align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                        <table class="content" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                            <tr>
                                <td class="header" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 25px 0; text-align: center;">
                                    <a href="#" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #bbbfc3; font-size: 19px; font-weight: bold; text-decoration: none; text-shadow: 0 1px 0 white;">
                                        '.$business_segment->Merchant->BusinessName.'
                                    </a>
                                </td>
                            </tr>
                            <!-- Email Body --><tr>
                                <td class="body" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; border-bottom: 1px solid #EDEFF2; border-top: 1px solid #EDEFF2; margin: 0; padding: 0; width: 100%; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 100%;">
                                    <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #FFFFFF; margin: 0 auto; padding: 0; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;">
                                        <!-- Body content --><tr>
                                            <td class="content-cell" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <h1 style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #2F3133; font-size: 19px; font-weight: bold; margin-top: 0; text-align: left;">
                                                    Forgot Your Password ?
                                                </h1>
                                                <table class="panel" width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 0 21px;"><tr>
                                                        <td class="panel-content" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #EDEFF2; padding: 16px;">
                                                            <table width="100%" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;"><tr>
                                                                    <td class="panel-item" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 0;">
                                                                        <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left; margin-bottom: 0; padding-bottom: 0;">
                                                                            Hi '.$business_segment->full_name.', '.$message_otp.'
                                                                        </p>
                                                                    </td>
                                                                </tr></table>
                                                        </td>
                                                    </tr></table>
                                                    <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                    Hey '.$business_segment->full_name.', Keep the OTP Secure!!
                                                </p>
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; color: #74787E; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">
                                                Thanks,<br>
                                                    '.$business_segment->Merchant->BusinessName.'</p>
            
            
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box;">
                                    <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; margin: 0 auto; padding: 0; text-align: center; width: 570px; -premailer-cellpadding: 0; -premailer-cellspacing: 0; -premailer-width: 570px;"><tr>
                                            <td class="content-cell" align="center" style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; padding: 35px;">
                                                <p style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; line-height: 1.5em; margin-top: 0; color: #AEAEAE; font-size: 12px; text-align: center;">
                                                ©'.date('Y').' '.$business_segment->Merchant->BusinessName.'. All rights reserved.</p>
                                            </td>
                                        </tr></table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr></table>
            </body>
            </html>
            ';

        return $message;
    }

}
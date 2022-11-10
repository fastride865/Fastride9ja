<?php

namespace App\Traits;

use App\Models\User;
use App\Models\UserDevice;
use Auth;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;

trait MailTrait
{
    public function sendMail($configuration, $receiver_email, $html_message, $email_type = 'default', $BusinessName = 'Unknown', $customer_support_number = NULL, $cc_email = "", $string_file = "")
    {
//        p($html_message);
        if (empty($configuration)) {
            $default_config = (object)array(
                'host' => 'smtp.gmail.com',
                'username' => 'messagedelivery2020@gmail.com',
                'password' => 'lsbpusxeyuyvggqv',//'ApHK$$$$$123++6sed', //Apporio@123!! APP password
                'encryption' => 'tls',
                'port' => 587
            );
        } else {
            $default_config = (object)array(
                'host' => $configuration->host,
                'username' => $configuration->username,
                'password' => $configuration->password,
                'encryption' => $configuration->encryption,
                'port' => $configuration->port
            );
        }
        $configuration = $default_config;
        $error = 'Success';
        $mail = new PHPMailer(true);
        try {
            switch ($email_type) {
                case 'welcome':
                    $subject = 'Welcome on ' . $BusinessName;
                    break;
                case 'customer_support':
                    $subject = 'Customer Support Query of ' . $customer_support_number;
                    break;
                case 'driver_bill_settle':
                    $subject = 'Your Settled Bill Details';
                    break;
                case 'signup_otp_varification':
                    $subject = 'SignUp Otp Verification';
                    break;
                case 'forgot_password':
                    $subject = 'Forgot Password';
                    break;
                case 'ride_invoice':
                    $subject = trans("$string_file.ride_invoice");
                    break;
                case 'order_invoice':
                    $subject = trans("$string_file.order_invoice");
                    break;
                case 'booking_invoice':
                    $subject = trans("$string_file.booking_invoice");
                    break;
                case 'new_order':
                    $subject = trans("$string_file.new_order_request");
                    break;
                case 'new_ride':
                    $subject = trans("$string_file.new_ride_request");
                    break;
                case 'otp':
                    $subject = trans("$string_file.otp");
                    break;
                default:
                    $subject = 'Welcome';
            }

            // HTML content not found.
            if ($html_message == NULL) {
                $html_message = $subject;
            }

            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            $mail->Host = isset($configuration->host) ? $configuration->host : 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = isset($configuration->username) ? $configuration->username : NULL;
            $mail->Password = isset($configuration->password) ? $configuration->password : NULL;
            $mail->SMTPSecure = isset($configuration->encryption) ? $configuration->encryption : 'ssl';
            $mail->Port = isset($configuration->port) ? $configuration->port : 465;

            //Recipients
            $mail->setFrom(isset($configuration->username) ? $configuration->username : NULL);
            $mail->addAddress($receiver_email);
            // $mail->addAddress("navdeep.singh@apporio.com");
            if (!empty($cc_email)) {
                $mail->AddCC($cc_email);
            }

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_message;
            $mail->AltBody = 'Alt Body';
            $mail->CharSet = 'UTF-8';  // For Urdu and Arabic Characters
            $mail->send();
            // p($mail);
//        } catch (phpmailerException $e) {
//            $mail->Host = 'smtp.gmail.com';
//            $mail->SMTPAuth = true;
//            $mail->Username = 'messagedelivery2020@gmail.com';
//            $mail->Password = 'RMVC,%euSzaf6fuQ';
//            $mail->SMTPSecure = 'tls';
//            $mail->Port = 587;
//
//            //Recipients
//            $mail->setFrom(isset($configuration->username) ? $configuration->username : NULL);
//            $mail->addAddress($receiver_email);
//            if (!empty($cc_email)) {
//                $mail->AddCC($cc_email);
//            }
//
//            //Content
//            $mail->isHTML(true);
//            $mail->Subject = $subject;
//            $mail->Body = $html_message;
//            $mail->AltBody = 'Alt Body';
//            $res = $mail->send();
//            $error = $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
//            $mail->Host = 'smtp.gmail.com';
//            $mail->SMTPAuth = true;
//            $mail->Username = 'messagedelivery2020@gmail.com';
//            $mail->Password = 'RMVC,%euSzaf6fuQ';
//            $mail->SMTPSecure = 'tls';
//            $mail->Port = 587;
//
//            //Recipients
//            $mail->setFrom(isset($configuration->username) ? $configuration->username : NULL);
//            $mail->addAddress($receiver_email);
//            if(!empty($cc_email))
//            {
//                $mail->AddCC($cc_email);
//            }
//
//            //Content
//            $mail->isHTML(true);
//            $mail->Subject = $subject;
//             $mail->Body = $html_message;
//            $mail->AltBody = 'Alt Body';
//            $res=$mail->send();
            $error = $e->getMessage(); //Boring error messages from anything else!
            // p($error);
        }
        $log_data = array(
            'request_type' => 'Mail Request',
            'data' => $error,
            'additional_notes' => $subject
        );
        $this->mailLog($log_data);
    }

    protected function mailLog($data)
    {
        $log_data = array(
            'request_type' => $data['request_type'],
            'request_data' => $data['data'],
            'additional_notes' => $data['additional_notes'],
            'hit_time' => date('Y-m-d H:i:s')
        );
        \Log::channel('maillog')->info($log_data);
    }
}
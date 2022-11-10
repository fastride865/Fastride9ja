<?php

namespace App\Http\Controllers\Helper;

use App\Models\Configuration;
use App\Models\Driver;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;


class TwilioMaskingHelper
{

    private static function create_session(Client $twilio, $service_sid, $expiry_seconds, $booking_id)
    {
        try {
            $session = $twilio->proxy->v1->services($service_sid)
                ->sessions
                ->create([
                    "uniqueName" => $booking_id . '_' . time(),
                    "mode" => 'voice-only',
                    "ttl" => $expiry_seconds
                ]);

            $service_sid = $session->sid;
            return $service_sid;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }


    private static function mask_number(Client $twilio, $service_sid, $session_sid, $phone, $user_name)
    {
        try {

            $participant1 = $twilio->proxy->v1->services($service_sid)
                ->sessions($session_sid)
                ->participants
                ->create($phone, // identifier
                    array("friendlyName" => $user_name)
                );

            $pid1 = $participant1->proxyIdentifier;
            return $pid1;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }


    private static function twilio_object($sid, $token)
    {
        // create twilio object
        $twilio = new Client($sid, $token);
        return $twilio;
    }

    public static function mask_numbers($booking, $expiry_in_seconds, $driver_id = 0)
    {
        try {
            $config = Configuration::where('merchant_id', $booking->merchant_id)->first();
            $service_sid = $config->twilio_service_id;
            $twilio = self::twilio_object($config->twilio_sid, $config->twilio_token);
            $user = $booking->User;
            $driver = $booking->Driver ?? Driver::find($driver_id);

            // create session
            $session_sid = self::create_session($twilio, $service_sid, $expiry_in_seconds, $booking->id);

            // mask user number
            $user_masked_phone = self::mask_number($twilio, $service_sid, $session_sid, $user->UserPhone, $user->UserName);

            // mask driver number
            $driver_masked_phone = self::mask_number($twilio, $service_sid, $session_sid, $driver->phoneNumber, $driver->fullName);

            $data = [
                'user_masked_number' => $user_masked_phone,
                'driver_masked_number' => $driver_masked_phone,
                'session_sid' => $session_sid
            ];

            // save booking
            return self::save_booking($booking, $data);
        } catch (\Exception $e) {
            Log::channel('callmasking')->info(['error'=>$e->getMessage(), 'user_phone' => $user->UserPhone, 'driver_phone' => $driver->phoneNumber]);
            throw new \Exception($e->getMessage());
        }

    }

    public static function save_booking($booking, $data)
    {
        $booking->driver_masked_number = $data['driver_masked_number'];
        $booking->user_masked_number = $data['user_masked_number'];
        $booking->session_sid = $data['session_sid'];
        $booking->save();
        return $booking;
    }

    public static function close_session($booking)
    {
        try {
            $config = Configuration::where('merchant_id', $booking->merchant_id)->first();
            $twilio = self::twilio_object($config->twilio_sid, $config->twilio_token);
            $service_sid = $config->twilio_service_id;

//            $session = $twilio->proxy->v1->services($service_sid)
//                ->sessions($booking->session_sid)
//                ->update(array(
//                        "status" => "closed"
//                    )
//                );

            $session = $twilio->proxy->v1->services($service_sid)
                ->sessions($booking->session_sid)
                ->delete();

            return $session;
        } catch (\Exception $e) {
            return true;
        }
    }

}
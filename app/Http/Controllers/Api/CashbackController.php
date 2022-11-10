<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\Merchant;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Booking;
use App\Models\Cashback;
use App\Models\DriverWalletTransaction;
use App\Models\Onesignal;
use App\Models\UserDevice;
use App\Models\UserWalletTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CashbackController extends Controller
{
    public function ProvideCashback($area_id = null, $service_id = null, $vehicle_type_id = null, Booking $booking = null, $ride_amount = null)
    {
        $cahback_fetch_try = Cashback::where([['country_area_id', $area_id], ['status', true], ['admin_delete', 0]])->first();
        if (!empty($cahback_fetch_try)):
            $merchant = new Merchant();
            if (!empty($cahback_fetch_try->CashBackVehicles()->where([['service_type_id', $service_id]])->first())):
                if (($cahback_fetch_try['users_cashback_enable'] != null) && ($ride_amount >= $cahback_fetch_try['min_bill_amount'])):
                    $cahback_amount = ($cahback_fetch_try['users_percentage'] / 100) * $ride_amount;
                    $cahback_amount = ($cahback_fetch_try['users_max'] == 1) ? $cahback_amount : ((($cahback_amount > $cahback_fetch_try['users_upto_amount'])) ? $cahback_fetch_try['users_upto_amount'] : $cahback_amount);
//                    $user_wallet_balance = $booking['User']['wallet_balance'];
//                    $amountNew = $merchant->TripCalculation($user_wallet_balance + $cahback_amount, $booking['merchant_id']);
//                    $cahback_amount = $merchant->TripCalculation($cahback_amount, $booking['merchant_id']);
//                    $booking['User']['wallet_balance'] = $amountNew;
//                    $booking['User']->save();
//                    UserWalletTransaction::create([
//                        'merchant_id' => $booking['merchant_id'],
//                        'user_id' => $booking['user_id'],
//                        'platfrom' => 1,
//                        'amount' => $cahback_amount,
//                        'type' => 4,
//                        'booking_id' => $booking['id'],
//                        'receipt_number' => rand(1111, 983939),
//                        'description' => $cahback_fetch_try->UserMessage,
//                    ]);
//                    $message = $cahback_fetch_try->UserMessage;
//                    $data = [];
//                    Onesignal::UserPushMessage($booking['User']['id'], $data, $message, 19, $booking->merchant_id);
                    $paramArray = array(
                        'user_id' => $booking['user_id'],
                        'booking_id' => $cahback_fetch_try->UserMessage,
                        'amount' => $cahback_amount,
                        'narration' => 7,
                        'platform' => 1,
                        'payment_method' => 2,
                        'receipt' => rand(1111, 983939),
                        'transaction_id' => null,
                        'notification_type' => 19
                    );
                    WalletTransaction::UserWalletCredit($paramArray);
//                    \App\Http\Controllers\Helper\CommonController::UserWalletCredit($booking['user_id'],$cahback_fetch_try->UserMessage,$cahback_amount,7,1,2,rand(1111, 983939),null,19);
                endif;

                if (($cahback_fetch_try['drivers_cashback_enable'] != null) && ($ride_amount >= $cahback_fetch_try['min_bill_amount'])):
                    $cahback_fetch_try['drivers_percentage'];
                    $cahback_amount = ($cahback_fetch_try['drivers_percentage'] / 100) * $ride_amount;
                    $cahback_amount = ($cahback_fetch_try['drivers_max'] == 1) ? $cahback_amount : ((($cahback_amount > $cahback_fetch_try['drivers_upto_amount'])) ? $cahback_fetch_try['drivers_upto_amount'] : $cahback_amount);
//                    $driver_wallet_balance = $booking['Driver']['wallet_money'] + $cahback_amount;
//                    $amountNew = $merchant->TripCalculation($driver_wallet_balance, $booking['merchant_id']);
//                    $booking['Driver']['wallet_money'] = $amountNew;
//                    $booking['Driver']->save();
//                    $cahback_amount = $merchant->TripCalculation($cahback_amount, $booking['merchant_id']);
//                    DriverWalletTransaction::create([
//                        'merchant_id' => $booking['merchant_id'],
//                        'driver_id' => $booking['driver_id'],
//                        'transaction_type' => 1,
//                        'payment_method' => 3,
//                        'receipt_number' => rand(1111, 983939),
//                        'amount' => $cahback_amount,
//                        'platform' => 1,
//                        'description' => $cahback_fetch_try->DriverMessage,
//                        'narration' => 5,
//                        'booking_id' => $booking['id'],
//                    ]);
//                    $data = [];
//                    $message = $cahback_fetch_try->DriverMessage;
//                    Onesignal::DriverPushMessage($booking['driver_id'], $data, $message, 19, $booking->merchant_id);
                    $paramArray = array(
                        'driver_id' => $booking['driver_id'],
                        'booking_id' => $cahback_fetch_try->DriverMessage,
                        'amount' => $cahback_amount,
                        'narration' => 10,
                        'platform' => 1,
                        'payment_method' => 2,
                        'receipt' => rand(1111, 983939),
                        'transaction_id' => null,
                        'notification_type' => 19
                    );
                    WalletTransaction::WalletCredit($paramArray);
//                    \App\Http\Controllers\Helper\CommonController::WalletCredit($booking['driver_id'],$cahback_fetch_try->DriverMessage,$cahback_amount,10,1,2,rand(1111, 983939),null,19);
                endif;
            endif;
        endif;

    }
}

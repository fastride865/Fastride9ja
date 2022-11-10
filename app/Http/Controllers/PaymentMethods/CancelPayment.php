<?php
namespace App\Http\Controllers\PaymentMethods;

use App\Http\Controllers\Helper\CommonController;
use App\Http\Controllers\Helper\WalletTransaction;
use App\Models\Merchant;
use App\Models\Outstanding;
use App\Models\User;
//use App\Models\UserWalletTransaction;

class CancelPayment
{
    public function MakePayment($bookingObject, $payment_method_id, $amount, $userId, $card_id = null,$cancel_outstanding , $driver_id = null)
    {
        $cancel_charges_received = 0;
        $merchant_helper = new \App\Http\Controllers\Helper\Merchant();
        switch ($payment_method_id) {
            case "4":   //Online Payment
            case "2":   //Credit Card\ Debit Card
            case "1":   //CASH
                if (!empty($bookingObject->driver_id)) // If Driver Is Assigned in Ride
                {
                    $cancel_amount_deduct_from_wallet = false;
                    $merchant = Merchant::find($bookingObject->merchant_id);
                    if(!empty($merchant) && isset($merchant->cancel_amount_deduct_from_wallet) && $merchant->cancel_amount_deduct_from_wallet == 1){
                        $cancel_amount_deduct_from_wallet = true;
                    }
                    $userObject = $bookingObject->User;
                    if($cancel_amount_deduct_from_wallet){
                        if ($userObject->wallet_balance < $amount) {
                            $remain = $userObject->wallet_balance;
                            if ($userObject->wallet_balance > 0.00) {
                                $amount = $merchant_helper->TripCalculation($amount, $bookingObject->merchant_id);
                                $userObject->wallet_balance = $merchant_helper->TripCalculation($userObject->wallet_balance, $bookingObject->merchant_id);
                                if($cancel_outstanding != 1){
                                    // Is cancel Outstanding is not exist then wallet will be negative
                                    $paid_amount = $amount;
                                    $remain = $merchant_helper->TripCalculation(($userObject->wallet_balance - $amount), $bookingObject->merchant_id);
                                }else{
                                    $amount -= $userObject->wallet_balance;
                                    $paid_amount = $userObject->wallet_balance;
                                    $remain = $merchant_helper->TripCalculation('0.0', $bookingObject->merchant_id);
                                }
//                                $cancel_charges_received = $this->Userwallet($userObject, $paid_amount, $bookingObject->id);
                                $paramArray = array(
                                    'user_id' => $userObject->id,
                                    'booking_id' => $bookingObject->id,
                                    'amount' => $paid_amount,
                                    'narration' => 5,
                                );
                                WalletTransaction::UserWalletDebit($paramArray);
//                                CommonController::UserWalletDebit($userObject->id,$bookingObject->id,$paid_amount,5);
                                $cancel_charges_received = $paid_amount;
                            }
                        } else {
                            $paramArray = array(
                                'user_id' => $userObject->id,
                                'booking_id' => $bookingObject->id,
                                'amount' => $amount,
                                'narration' => 5,
                            );
                            WalletTransaction::UserWalletDebit($paramArray);
//                            CommonController::UserWalletDebit($userObject->id,$bookingObject->id,$amount,5);
//                            $remain = $userObject->wallet_balance - $amount;
                            $cancel_charges_received = $amount;
                            $amount = 0;
                        }
//                        $userObject->wallet_balance = round($remain,2);
//                        $userObject->save();
                    }
                    $remaining_amount = $amount - $cancel_charges_received;
                    if ($cancel_outstanding == 1 && $remaining_amount > 0) //Will Receive Amount From User Further
                    {
                        $outstanding_data['user_id'] = $bookingObject->user_id;
                        $outstanding_data['booking_id'] = $bookingObject->id;
                        $outstanding_data['driver_id'] = $bookingObject->driver_id;
                        $outstanding_data['amount'] = $merchant_helper->TripCalculation($amount, $bookingObject->merchant_id);
                        $outstanding_data['reason'] = 1;
                        $outstanding_data['pay_status'] = 0; //Unpaid
                        \DB::beginTransaction();
                        try {
                            $outstanding_submit = new Outstanding($outstanding_data);
                            $outstanding_submit->save(); //if there is not error/exception in the above code, it'll commit
                            \DB::commit();
                        } catch (\Exception $e) {
                            \DB::rollBack();     //if there is an error/exception in the above code before commit, it'll rollback
                        }
                    }
                }
                break;
            case "3": //Wallet
                if (!empty($bookingObject->driver_id)) // If Driver Is Assigned in Ride
                {
                    $userObject = $bookingObject->User;
                    if ($userObject->wallet_balance < $amount) {
                        $remain = $userObject->wallet_balance;
                        if ($userObject->wallet_balance > 0.00) {
                            $amount = $merchant_helper->TripCalculation($amount, $bookingObject->merchant_id);
                            $userObject->wallet_balance = $merchant_helper->TripCalculation($userObject->wallet_balance, $bookingObject->merchant_id);
                            if($cancel_outstanding != 1){
                                // Is cancel Outstanding is not exist then wallet will be negative
                                $paid_amount = $amount;
                                $remain = $merchant_helper->TripCalculation(($userObject->wallet_balance - $amount), $bookingObject->merchant_id);
                            }else{
                                $amount -= $userObject->wallet_balance;
                                $paid_amount = $userObject->wallet_balance;
                                $remain = $merchant_helper->TripCalculation('0.0', $bookingObject->merchant_id);
                            }
//                            $cancel_charges_received = $this->Userwallet($userObject, $paid_amount, $bookingObject->id);
                            $paramArray = array(
                                'user_id' => $userObject->id,
                                'booking_id' => $bookingObject->id,
                                'amount' => $paid_amount,
                                'narration' => 5,
                            );
                            WalletTransaction::UserWalletDebit($paramArray);
//                            CommonController::UserWalletDebit($userObject->id,$bookingObject->id,$paid_amount,5);
                            $cancel_charges_received = $paid_amount;
                        }
                        $remaining_amount = $amount - $cancel_charges_received;
                        if ($cancel_outstanding == 1 && $remaining_amount > 0) //Will Receive Amount From User Further
                        {
                            $outstanding_data['user_id'] = $bookingObject->user_id;
                            $outstanding_data['booking_id'] = $bookingObject->id;
                            $outstanding_data['driver_id'] = $bookingObject->driver_id;
                            $outstanding_data['amount'] = $merchant_helper->TripCalculation($amount, $bookingObject->merchant_id);
                            $outstanding_data['reason'] = 1;
                            $outstanding_data['pay_status'] = 0; //Unpaid
                            \DB::beginTransaction();
                            try {
                                $outstanding_submit = new Outstanding($outstanding_data);
                                $outstanding_submit->save(); //if there is not error/exception in the above code, it'll commit
                                \DB::commit();
                            } catch (\Exception $e) {
                                \DB::rollBack();     //if there is an error/exception in the above code before commit, it'll rollback
                            }
                        }
                    } else {
                        $paramArray = array(
                            'user_id' => $userObject->id,
                            'booking_id' => $bookingObject->id,
                            'amount' => $amount,
                            'narration' => 5,
                        );
                        WalletTransaction::UserWalletDebit($paramArray);
//                        CommonController::UserWalletDebit($userObject->id,$bookingObject->id,$amount,5);
//                        $remain = $userObject->wallet_balance - $amount;
//                        $cancel_charges_received = $this->Userwallet($userObject, $amount, $bookingObject->id);
                        $cancel_charges_received = $amount;
                    }
//                    $userObject->wallet_balance = round($remain,2);
//                    $userObject->save();
                }
                break;
        }
        return $cancel_charges_received;
    }

//    public function Userwallet($user, $amount, $bookingId)
//    {
//        UserWalletTransaction::create([
//            'merchant_id' => $user->merchant_id,
//            'user_id' => $user->id,
//            'platfrom' => 2,
//            'booking_id' => $bookingId,
//            'amount' => $amount,
//            'receipt_number' => "Application",
//            'type' => 2,
//        ]);
//        return $amount;
//    }
}
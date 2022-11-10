<?php

namespace App\Http\Controllers\PaymentMethods;

use App\Http\Controllers\PaymentSplit\StripeConnect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StripeController extends Controller
{
    protected $StripeKey;

    public function __construct($StripeKey = null)
    {
        $this->StripeKey = $StripeKey;
        \Stripe\Stripe::setApiKey($this->StripeKey);
    }


    public function CreateCustomer($token = null, $email = null)
    {
        try {
            $Customer = \Stripe\Customer::create([
                "description" => $email,
                "source" => $token
            ]);
            return array('id' => $Customer->id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function ListCustomer($cardObj)
    {
        $c = array();
        foreach ($cardObj as $card) {
            try {
                $Customer = \Stripe\Customer::allSources($card->token);
                $c[] = array(
                    'card_id' => $card->id,
                    'card_number' => $Customer['data'][0]['last4'],
                    'card_type' => $Customer['data'][0]['brand'],
                    'exp_month' => $Customer['data'][0]['exp_month'],
                    'exp_year' => $Customer['data'][0]['exp_year']
                );
//                $Customer = \Stripe\Customer::retrieve($card->token);
//                $c[] = array(
//                    'card_id' => $card->id,
//                    'card_number' => $Customer['sources']['data'][0]['last4'],
//                    'card_type' => $Customer['sources']['data'][0]['brand'],
//                    'exp_month' => $Customer['sources']['data'][0]['exp_month'],
//                    'exp_year' => $Customer['sources']['data'][0]['exp_year']
//                );
            } catch (\Exception $e) {
            }
        }
        return $c;

    }

    public function CustomerDetails($card)
    {
        $c = null;
        try {
            $Customer = \Stripe\Customer::allSources($card->token);
            $c = array(
                'card_id' => $card->id,
                'card_number' => $Customer['data'][0]['last4'],
                'card_type' => $Customer['data'][0]['brand'],
                'exp_month' => $Customer['data'][0]['exp_month'],
                'exp_year' => $Customer['data'][0]['exp_year']
            );
//            $Customer = \Stripe\Customer::retrieve($card->token);
//            $c = array(
//                'card_number' => $Customer['sources']['data'][0]['last4'],
//                'card_type' => $Customer['sources']['data'][0]['brand'],
//                'exp_month' => $Customer['sources']['data'][0]['exp_month'],
//                'exp_year' => $Customer['sources']['data'][0]['exp_year']
//            );
        } catch (\Exception $e) {
        }
        return $c;

    }

    public function DeleteCustomer($CustomerID)
    {
        try {
            $cu = \Stripe\Customer::retrieve($CustomerID);
            $cu->delete();
        } catch (\Exception $exception) {
        }

    }

    public function Charge($amount = 0, $currency = null, $CustomerID = null, $email = null)
    {
        try {
            $charge = \Stripe\Charge::create([
                "amount" => $amount * 100,
                "currency" => $currency,
                "customer" => $CustomerID,
                "description" => $email
            ]);
            return array('charge_id' => $charge['id']);
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function Connect_charge($amount = 0, $currency = null, $token , $driver_total_payable_amount , $driver_sc_account_id, $merchant_id)
    {
        try {
            $charge = StripeConnect::charge_amount($driver_total_payable_amount,$amount,$driver_sc_account_id,$token,$currency, $merchant_id);
            return array('charge_id' => $charge->id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
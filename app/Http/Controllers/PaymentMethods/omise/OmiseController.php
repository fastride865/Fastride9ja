<?php

namespace App\Http\Controllers\PaymentMethods\omise;

use OmiseObject;
use App\Http\Controllers\Controller;
require_once 'omise-php-master/lib/Omise.php';

class OmiseController extends Controller
{
    private $OMISE_PUBLIC_KEY = 'pkey_test_5hyll4slsws58aaqb7k';
	private $OMISE_SECRET_KEY = 'skey_test_5he4wyxddagonj7r85z';
	private $OMISE_API_VERSION = '2017-11-02';

	public function __construct($publicKey, $secretKey)
	{
		$this->OMISE_PUBLIC_KEY = $publicKey;
		$this->OMISE_SECRET_KEY = $secretKey;
	}

    function registerOmise($params = null)
    {
        $name = isset($params['name']) ? $params['name'] : 'JOHN DOE';
        $email = isset($params['email']) ? $params['email'] : 'test@gmail.com';
        $desc = isset($params['desc']) ? $params['desc'] : 'Creating Customer';
        $city = isset($params['city']) ? $params['city'] : 'Bangkok';
        $postal_code = isset($params['postal_code']) ? $params['postal_code'] : '10320';
        $card_number = isset($params['card_number']) ? $params['card_number'] : '4242424242424242';
        $security_code = isset($params['security_code']) ? $params['security_code'] : '123';
        $expiration_month = isset($params['expiration_month']) ? $params['expiration_month'] : '2';
        $expiration_year = isset($params['expiration_year']) ? $params['expiration_year'] : '2022';
        // Generate Card Token
        try{
            $customer_detail = [];
            $card_token = $this->createToken($name,$card_number,$expiration_month,$expiration_year,$city,$postal_code,$security_code);
            $customer = '';
            if ($card_token != '') {
                // Create Customer for generate token
                $customer = $this->createCustomer($email, $desc, $card_token);
                if ($customer->offsetGet('id') != '') {
                    return array('card_token' => $card_token,'customer_token' => $customer->offsetGet('id'));
                }
            }
            return false;
        }catch(\Exception $e){
            $error = $e->getMessage();
            throw new \Exception($error);
        }
    }

	function createCustomer($email, $desc, $token)
	{
		$customer = \OmiseCustomer::create(array(
			'email' => $email,
			'description' => $desc,
			'card' => $token
		),$this->OMISE_PUBLIC_KEY,$this->OMISE_SECRET_KEY);
		return $customer;
	}

	function createToken($name,$card_number,$expiration_month,$expiration_year,$city,$postal_code,$security_code)
	{
        $token = \OmiseToken::create(array(
            'card' => array(
                'name' => $name,
                'number' => $card_number,
                'expiration_month' => $expiration_month,
                'expiration_year' => $expiration_year,
                'city' => $city,
                'postal_code' => $postal_code,
                'security_code' => $security_code
            )
        ),$this->OMISE_PUBLIC_KEY,$this->OMISE_SECRET_KEY);
		if ($token->offsetGet('id') != '') {
			return $token->offsetGet('id');
		} else {
			return '';
		}
	}

	function retriveCustomerCard($customer_token = 'cust_test_5iw8bkm5oisb6uqhzzr')
    {
        $omiseObj = \OmiseCustomer::retrieve($customer_token,$this->OMISE_PUBLIC_KEY,$this->OMISE_SECRET_KEY);
        $cards = $omiseObj->cards();
        $card = $cards->offsetGet('data');
        $card = is_array($card) ? $card : [];
        return $card;
    }

    function Charge($token, $amount = 100000)
    {
        $charge = \OmiseCharge::create(array(
            'amount' => $amount,
            'currency' => 'thb',
            'customer' => $token
        ),$this->OMISE_PUBLIC_KEY,$this->OMISE_SECRET_KEY);
        return $charge;
    }

    public function destroyCustomer($customer_token = 'cust_test_5iw8bkm5oisb6uqhzzr')
    {
        $customer = \OmiseCustomer::retrieve($customer_token,$this->OMISE_PUBLIC_KEY,$this->OMISE_SECRET_KEY);
        $result = $customer->destroy();
        return $result;
    }
}

<?php
/*
*	Don't change this file
* 	if you need changes you can change in 
* 	namespace App\CustomClasses\EmailProvider
*/

namespace App\Http\Controllers\PaymentMethods\Paygate;



class CardVaultRequest
{
    	/**
	 * @var string
	 */
	public $Account;
	/**
	 * @var string
	 */
	public $CardNumber;
    /**
	 * @return string
	 */
    public $CardExpiryDate;
	/**
	 * @return string
	 */
	public function getAccount(){
		return $this->Account;
	}

	/**
	 * @param string $PayGateId
	 *
	 * @return Account
	 */
	public function setAccount($Account){
		$this->Account = $Account;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCardNumber(){
		return $this->CardNumber;
	}

	/**
	 * @param string $Password
	 *
	 * @return Account
	 */
	public function setCardNumber($CardNumber){
		$this->CardNumber = $CardNumber;
		return $this;
    }
    /**
	 * @return string
	 */
	public function getCardExpiryDate(){
		return $this->CardExpiryDate;
	}

	/**
	 * @param string $Password
	 *
	 * @return Account
	 */
	public function setCardExpiryDate($ExpiryDate){
       // dd($ExpiryDate);
		$this->CardExpiryDate =  $ExpiryDate;
		return $this;
    }
    
}
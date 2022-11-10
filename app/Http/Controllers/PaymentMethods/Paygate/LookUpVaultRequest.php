<?php
/*
*	Don't change this file
* 	if you need changes you can change in 
* 	namespace App\CustomClasses\EmailProvider
*/

namespace App\Http\Controllers\PaymentMethods\Paygate;



class LookUpVaultRequest
{
    	/**
	 * @var string
	 */
	public $Account;
	/**
	 * @var string
	 */
	public $VaultId;
    /**
	 * @return string
	 */
    public function getAccount(){
		return $this->Account;
	}

	public function setAccount($Account){
		$this->Account = $Account;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getVaultId(){
		return $this->VaultId;
	}

	/**
	 * @param string $Password
	 *
	 * @return Account
	 */
	public function setVaultId($VaultId){
		$this->VaultId = $VaultId;
		return $this;
    }
   
}
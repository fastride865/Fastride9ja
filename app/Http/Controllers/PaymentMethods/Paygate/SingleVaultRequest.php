<?php
/*
*	Don't change this file
* 	if you need changes you can change in 
* 	namespace App\CustomClasses\EmailProvider
*/

namespace App\Http\Controllers\PaymentMethods\Paygate;

class SingleVaultRequest
{
    	/**
	 * @var string
	 */
	public $CardVaultRequest;
	/**
	 * @var string
	 */
    public $LookUpVaultRequest;
    
    public $DeleteVaultRequest;
    
	public function getCardVaultRequest(){
		return $this->CardVaultRequest;
	}

	/**
	 * @param string $PayGateId
	 *
	 * @return Account
	 */
	public function setCardVaultRequest($CardVaultRequest){
		$this->CardVaultRequest = $CardVaultRequest;

		return $this;
	}

    public function getLookUpVaultRequest(){
		return $this->LookUpVaultRequest;
	}

	/**
	 * @param string $PayGateId
	 *
	 * @return Account
	 */
	public function setLookUpVaultRequest($LookUpVaultRequest){
		$this->LookUpVaultRequest = $LookUpVaultRequest;

		return $this;
	}
    public function getDeleteVaultRequest(){
		return $this->DeleteVaultRequest;
	}

	/**
	 * @param string $PayGateId
	 *
	 * @return Account
	 */
	public function setDeleteVaultRequest($DeleteVaultRequest){
		$this->DeleteVaultRequest = $DeleteVaultRequest;

		return $this;
	}

	
}
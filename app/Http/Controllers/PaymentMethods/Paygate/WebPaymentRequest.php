<?php

namespace App\Http\Controllers\PaymentMethods\Paygate;

class WebPaymentRequest
{
    /**
     * @var Account
     */
    public $Account;
    /**
     * @var Customer
     */
    public $Customer;
    /**
     * @var PaymentType
     */
    public $Vault;
    /**
     * @var PaymentType
     */
    public $VaultId;
    /**
     * @var PaymentType
     */
    public $PaymentType;
    /**
     * @var Redirect
     */
    public $Redirect;
    /**
     * @var Order;
     */
    public $Order;
    /**
     * @var ThreeDSecure
     */
    public $ThreeDSecure;
    /**
     * @var Risk
     */
    public $Risk;
    /**
     * @var array
     */
    public $UserDefinedFields;
    /**
     * @var string
     */
    public $BillingDescriptor;
    
    public function getVault()
    {
        return $this->Vault;
    }

    public function setVault($Vault)
    {
        $this->Vault = $Vault;

        return $this;
    }
    
    public function getVaultId()
    {
        return $this->VaultId;
    }

    public function setVaultId($VaultId)
    {
        $this->VaultId = $VaultId;

        return $this;
    }
    

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->Account;
    }

    /**
     * @param Account $Account
     *
     * @return WebPaymentRequest
     */
    public function setAccount($Account)
    {
        $this->Account = $Account;

        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->Customer;
    }

    /**
     * @param Customer $Customer
     *
     * @return WebPaymentRequest
     */
    public function setCustomer($Customer)
    {
        $this->Customer = $Customer;

        return $this;
    }

    /**
     * @return PaymentType
     */
    public function getPaymentType()
    {
        return $this->PaymentType;
    }

    /**
     * @param PaymentType $PaymentType
     *
     * @return WebPaymentRequest
     */
    public function setPaymentType($PaymentType)
    {
        $this->PaymentType = $PaymentType;

        return $this;
    }

    /**
     * @return Redirect
     */
    public function getRedirect()
    {
        return $this->Redirect;
    }

    /**
     * @param Redirect $Redirect
     *
     * @return WebPaymentRequest
     */
    public function setRedirect($Redirect)
    {
        $this->Redirect = $Redirect;

        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->Order;
    }

    /**
     * @param Order $Order
     *
     * @return WebPaymentRequest
     */
    public function setOrder($Order)
    {
        $this->Order = $Order;

        return $this;
    }

    /**
     * @return ThreeDSecure
     */
    public function getThreeDSecure()
    {
        return $this->ThreeDSecure;
    }

    /**
     * @param ThreeDSecure $ThreeDSecure
     *
     * @return WebPaymentRequest
     */
    public function setThreeDSecure($ThreeDSecure)
    {
        $this->ThreeDSecure = $ThreeDSecure;

        return $this;
    }
   
    /**
     * @return Risk
     */
    public function getRisk()
    {
        return $this->Risk;
    }

    /**
     * @param Risk $Risk
     *
     * @return WebPaymentRequest
     */
    public function setRisk($Risk)
    {
        $this->Risk = $Risk;

        return $this;
    }

    /**
     * @return array
     */
    public function getUserDefinedFields()
    {
        return $this->UserDefinedFields;
    }

    /**
     * @param array $UserDefinedFields
     *
     * @return WebPaymentRequest
     */
    public function setUserDefinedFields($UserDefinedFields)
    {
        $this->UserDefinedFields = $UserDefinedFields;

        return $this;
    }

    /**
     * @return string
     */
    public function getBillingDescriptor()
    {
        return $this->BillingDescriptor;
    }

    /**
     * @param string $BillingDescriptor
     *
     * @return WebPaymentRequest
     */
    public function setBillingDescriptor($BillingDescriptor)
    {
        $this->BillingDescriptor = $BillingDescriptor;

        return $this;
    }
}

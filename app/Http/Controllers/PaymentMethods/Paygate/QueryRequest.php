<?php
/*
*	Don't change this file
* 	if you need changes you can change in 
* 	namespace App\CustomClasses\EmailProvider
*/

namespace App\Http\Controllers\PaymentMethods\Paygate;

class QueryRequest
{
    /**
     * @var string
     */
    public $Account, $PayRequestId, $MerchantOrderId, $TransId, $TransactionType;
    public function getAccount()
    {
        return $this->Account;
    }

    public function setAccount($Account)
    {
        $this->Account = $Account;

        return $this;
    }
    public function getTransactionType()
    {
        return $this->TransactionType;
    }

    public function setTransactionType($TransactionType)
    {
        $this->TransactionType = $TransactionType;

        return $this;
    }
    public function getPayRequestId()
    {
        return $this->PayRequestId;
    }

    public function setPayRequestId($PayRequestId)
    {
        $this->PayRequestId = $PayRequestId;

        return $this;
    }
    public function getMerchantOrderId()
    {
        return $this->MerchantOrderId;
    }

    public function setMerchantOrderId($MerchantOrderId)
    {
        $this->MerchantOrderId = $MerchantOrderId;

        return $this;
    }
    public function getTransId()
    {
        return $this->TransId;
    }

    public function setTransId($TransId)
    {
        $this->TransId = $TransId;

        return $this;
    }
}

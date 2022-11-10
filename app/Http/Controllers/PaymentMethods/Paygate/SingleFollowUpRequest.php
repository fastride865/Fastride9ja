<?php
/*
*	Don't change this file
* 	if you need changes you can change in 
* 	namespace App\CustomClasses\EmailProvider
*/

namespace App\Http\Controllers\PaymentMethods\Paygate;

class SingleFollowUpRequest
{
    /**
     * @var string
     */
    public $QueryRequest, $RefundRequest, $SettlementRequest;
    /**
     * @var string
     */
    

    public function getQueryRequest()
    {
        return $this->QueryRequest;
    }

    /**
     * @param string $PayGateId
     *
     * @return Account
     */
    public function setQueryRequest($QueryRequest)
    {
        $this->QueryRequest = $QueryRequest;
        return $this;
    }
    public function getRefundRequest()
    {
        return $this->RefundRequest;
    }

    /**
     * @param string $PayGateId
     *
     * @return Account
     */
    public function setRefundRequest($RefundRequest)
    {
        $this->RefundRequest = $RefundRequest;
        return $this;
    }
    public function getSettlementRequest()
    {
        return $this->SettlementRequest;
    }

    /**
     * @param string $PayGateId
     *
     * @return Account
     */
    public function setSettlementRequest($SettlementRequest)
    {
        $this->SettlementRequest = $SettlementRequest;
        return $this;
    }

    
}

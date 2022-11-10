<?php

namespace App\Http\Controllers\Helper;

use App\Traits\MerchantTrait;

class GetString{
    use MerchantTrait;
    
    public $merchant_id = NULL;
    public function __construct($merchant_id)
    {
        $this->merchant_id = $merchant_id;   
    }
    public function getStringFileText(){
        return $this->getStringFile($this->merchant_id);
    }
}
<?php

require 'Enum.php';

class APIEnvironment extends Enum {

    const SANDBOX = "https://demo2.2c2p.com/2C2PFrontend/PaymentActionV2";
    const PRODUCTION = "https://t.2c2p.com/paymentActionV2";
    const PRODUCTION_INDONESIA = "https://payid.2c2p.com/paymentActionV2";
  }
  
?>

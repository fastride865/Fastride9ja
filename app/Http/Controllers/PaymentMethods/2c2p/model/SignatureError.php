<?php

class SignatureError {

    public $version = "10.0";

    public $respCode = "9042";

    public $respDesc = "Signature doesn't match.";

    public function __construct($version) {
        $this->version = $version;
    }
}

?>

<?php

class PaymentGatewayHelper {

    //Method for generate signature by object 
    public function generateSignature($payment_request_obj, $secret_key) {

        //1) Convert object to JSON string
        $raw_json_string = json_encode(get_object_vars($payment_request_obj));

        //2) Convert JSON string to string array
        $json_array = $this->jsonToArray($raw_json_string);

        //3) Sorting in ascending alphabetical order based on ASCII names
        $this->sortArray($json_array);

        //4) Convert sorted string array to a string
        $signature = $this->arraryToString($json_array);

        //5) Generate hashed signature by using HMAC-SHA256 with merchant sercret key 
        $hashed_signature = $this->hashSignature($signature, $secret_key);
        
        return $hashed_signature;
    }

    //Method for generate signature by JSON string
    public function generateSignatureByJSONString($raw_json_string, $secret_key) {

        //1) Convert JSON string to string array
        $json_array = $this->jsonToArray($raw_json_string);

        //2) Sorting in ascending alphabetical order based on ASCII names
        $this->sortArray($json_array);

        //3) Convert sorted string array to a string
        $signature = $this->arraryToString($json_array);

        //4) Generate hashed signature by using HMAC-SHA256 with merchant sercret key 
        $hashed_signature = $this->hashSignature($signature, $secret_key);
        
        return $hashed_signature;
    }

    //Method for verify API response signature
    public function validateSignature($encoded_response, $secret_key) {
        
        //Decode encoded reponse to JSON string with Base64
        $raw_response = base64_decode($encoded_response);

        //Convert JSON string to string array
        $json_array = $this->jsonToArray($raw_response);
        
        //Keep original signature for compare signature
        $original_signature = $json_array['signature']; 

        //Clear signature for generate self hashed signature
        $json_array['signature'] = "";

        //Convert array to JSON after remove signature
        $raw_json_after_removed_signature = json_encode($json_array); 

        //Generate self hashed signature for verify
        $self_hashed_signature = $this->generateSignatureByJSONString($raw_json_after_removed_signature, $secret_key); 

        //Verify with both signature if equal means it's a valid signature else it's invalid signature should return error response to application
        if(strcasecmp($original_signature, $self_hashed_signature) == 0) {
            return true;
        } else {
            return false;
        }
    }
    
    private function jsonToArray($json) {
        return json_decode($json, true);
    }

    private function jsonToObject($json) {
        return json_decode($json);
    }
    
    private function multipleToSingleDimensionalArray($array) {
        return iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($array)), 0);
    }
    
    private function convertToStringArray($array) {
        return array_map('strval', $array);
    }
    
    private function sortArray(&$array) {

        //This sorting setting equivalent to C# sort() 
        sort($array, SORT_STRING | SORT_FLAG_CASE);
    }
    
    private function arraryToString($array) {
        return implode($array);
    }
    
    private function hashSignature($signature, $secret_key) {
        return hash_hmac('sha256', $signature, $secret_key);
    }

    //Method for API request
    public function requestAPI($api_env, $payment_request_obj) {

        //Convert object to JSON string (Optional if not using object)
        $payment_request_json = json_encode(get_object_vars($payment_request_obj));

        //Get encoded API response
        return $encoded_response = $this->doAPIRequest($api_env, $payment_request_json);
    }

    private function doAPIRequest($api_env, $raw_json_request) {

        //CURL configuration
        $ch = curl_init();
        $curl_options = array(
            CURLOPT_URL => $api_env,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1, //Note: Must use POST method
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2, //Note: Must request with TLS1.2
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json'
            ),
            CURLOPT_POSTFIELDS => base64_encode($raw_json_request) //Note: Must encode the request before send
        );
    
        curl_setopt_array($ch, $curl_options);
    
        //Get encoded API response 
        return $api_response = curl_exec($ch); 
    }

    //Method for Parse encoded response and convert into object
    public function parseAPIResponse($encoded_response) {
    
        //Decode encoded reponse to JSON string with Base64
        $raw_response = base64_decode($encoded_response);

        //Convert JSON string to object
        $json_object = $this->jsonToObject($raw_response);

        return $json_object;
    }
}

?>
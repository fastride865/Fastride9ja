<?php

namespace App\Http\Controllers\Helper;


/* This class provides service for microsoft azure face recognition and verification */


class FaceRecognition
{
    private static $endpoint;
    private static $key;
    private static $params = "?returnFaceId=true&returnFaceLandmarks=false&returnFaceAttributes=gender&recognitionModel=recognition_04&returnRecognitionModel=true&detectionModel=detection_01";

    public function __construct($endpoint = null, $key = null)
    {
        self::$key = $key;
        self::$endpoint = 'https://'.$endpoint.'/face/v1.0/';
    }

    private static function get_headers() {
        return [
            'Content-Type: application/json',
            'Ocp-Apim-Subscription-Key: '. self::$key
        ];
    }

    private static function get_binary_headers() {
        return [
            'Content-Type: application/octet-stream',
            'Ocp-Apim-Subscription-Key: '. self::$key
        ];
    }

    // detect face using image url
    // returns face id
    public function detect_face($image_url) {
        $url = self::$endpoint . 'detect'.self::$params;
        $body = ['url' => $image_url];
        $response = curl_post_request($url , $body , self :: get_headers() );

        if ($response->status == 200) {
            return (json_decode($response->data)) ? json_decode($response->data)[0]->faceId : false;
        }
        return false;
    }

    public function detect_face_binary($image, $string_file) {
        $url = self::$endpoint . 'detect'.self::$params;

        $image = explode(",",$image);
        $body = $this->base64url_decode($image[1]);

        $response = curl_post_request($url , $body , self :: get_binary_headers() , 'stream');
        $response = (array)$response;
        if ($response['status'] == 200) {
            if(!empty($response['data']) && $response['data'] != "[]"){
                $data = json_decode($response['data']);
                return  $data[0]->faceId;
            }else{
                throw new \Exception(trans("$string_file.invalid_image_captured"));
            }
        }else{
            throw new \Exception(implode(",",$response));
        }
    }

    // verify driver image using face ids
    public function verify_faces($face_id1 , $face_id2) {
        $url = self::$endpoint . 'verify';
        $body = [
            'faceId1' => $face_id1,
            'faceId2' => $face_id2
        ];

        $curl_response = curl_post_request($url , $body , self :: get_headers());

        if ($curl_response->status == 200) {
            return (json_decode($curl_response->data)) ? json_decode($curl_response->data)->isIdentical : false;
        }

        return false;
    }
    
    function base64url_decode($data) {
        return base64_decode(str_replace(array('-', '_'), array('+', '/'), $data));
    }
}

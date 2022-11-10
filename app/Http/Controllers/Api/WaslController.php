<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\PolygenController;
use App\Models\CountryArea;
use App\Models\WebsiteFeaturesComponents;
use App\Models\WebSiteHomePage;
use App\Models\WebsiteApplicationFeature;
use App\Models\WebsiteFeature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Application;

class WaslController extends Controller
{

    //
   public function driverVehicleRegister(Request $request)
   {

       $curl = curl_init();

       curl_setopt_array($curl, array(
           CURLOPT_URL => 'https://wasl.api.elm.sa/api/dispatching/v2/drivers',
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => 'POST',
           CURLOPT_POSTFIELDS =>'{
                    "driver": {
                    "identityNumber": "1119246377",
                    "dateOfBirthHijri": "1423/12/02",
                    "emailAddress": "sbr@rasanarabia.com",
                    "mobileNumber": "+966598709542"
                    },
                    "vehicle": { 
                    "sequenceNumber": "383771810",
                    "plateLetterRight": "د",
                    "plateLetterMiddle": "ه",
                    "plateLetterLeft": "ح",
                    "plateNumber": "9202",
                    "plateType": "1"
                    }
                    }',
           CURLOPT_HTTPHEADER => array(
               'Content-Type: application/json',
               'client-id: DDA99287-2334-4289-8280-D40DA50D32D7',
               'app-id: ce5874f94d6f206c0eee2fc21f681473',
               'app-key: 566d684e',
               'Cookie: 2d377f071e1c1b63fab2d1eadf7efba2=3768c0de9ea28486f898f2f7945ece57; NSC_Q-PDQ-3tdbmf_qspevdujpo-TTM=30dfa3db554834e73b5eeed4417ca4c46123aabda235f4b75ebf9d465393f1e2e554b9d3; TS0131994e=015a42f27eda6ebac755e1d171c6749bdbf0d1ab227b89384ee1cd07d59687e48b2e41c0f3691c25cac4e5e6e7d488ad17e998d3526b5925f7806f07e33209ab150dfe8dec54084e720027e7eabf1b191b654e9ef6'
           ),
       ));

       $response = curl_exec($curl);

       curl_close($curl);
       p($response);

   }

}

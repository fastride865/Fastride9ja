<?php

namespace App\Http\Controllers\PaymentMethods\UnionBank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponseTrait;
use App\Traits\MerchantTrait;
use App\Models\Booking;
use App\Models\PaymentOption;
use App\Models\PaymentOptionsConfiguration;
use App\Models\Merchant;
use DateTime;

class UnionBankController extends Controller
{
    use ApiResponseTrait,MerchantTrait;

    public function AuthCodeRedirect(Request $request)
    {
        $auth_code = $request->code ?? '';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-uat.unionbankph.com/partners/sb/customers/v1/oauth2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=authorization_code&client_id=47c74b4a-5aa6-4e13-9e0c-5c681552b287&code=AAIRlh7jylhpBORNZzdFB3puVlPYIdNIrZWnpKILTIw-sjNQixAmLIus5Z_VJDvcJfsQm4mNN0YoojcPCMT1PtHHcBPXAp-X_bQuaCQ41m9a5jkdI4_gAWmGT9Rh_Q1AHlnVsvJeM_3G58j8zeSGp7tGc1GhWMoGd4fGDBp3nc7YxxLhuwopKNBW3n99naIgqsGIOP5uJuzc8gaw6p1L295khVQVSMCJiz9SFCtl1MUdTl3kGXzeKrnYO7-3kviBdDZ2QZTOp9_8hGOa67cbu8xHFoVurW1TikVaEjzDltZfxzp2HNmp-C_QZckdfXt59A6J2pZ9HBK2G9QqSG_nc0AybW-W60t847oK2LYEQXS3pQznwLL58wN1359Z2Ydjvd8&redirect_uri=https%3A%2F%2Fapi-uat.unionbankph.com%2Fubp%2Fuat%2Fv1%2Fredirect',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }

    public function acknowledgePayment(Request $request)
    {
        //
    }

    public function resultWebhook(Request $request)
    {
        //
    }
}

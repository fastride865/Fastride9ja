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

class WebsiteController extends Controller
{

    public function cars(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|string',
            'longitude' => 'required|string',
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['result' => "0", 'message' => $errors[0], 'data' => []]);
        }
        $merchant_id = $request->merchant_id;
        $area = PolygenController::Area($request->latitude, $request->longitude, $merchant_id);
        if (empty($area)) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.no_service_area"), 'data' => []]);
        }
        $area_id = $area['id'];
        $areas = CountryArea::with('ServiceTypes')->find($area_id);
        $currency = $areas->Country->isoCode;
        $newHomeScreen = new HomeController();
        $areas = $newHomeScreen->ServiceType($areas, $merchant_id, $request);
        return response()->json(['result' => "1", 'message' => "cars", 'currency' => $currency, 'data' => $areas]);
    }

    public function HomeScreen(Request $request)
    {
        $checkData = WebSiteHomePage::where([['merchant_id', '=', $request->merchant_id]])->first();
        if (empty($checkData)) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        $data = $this->User($checkData, $request->merchant_id);
        $data['androidDriverLink'] = '';
        $data['iosDriverLink'] = '';
        $application = Application::where([['merchant_id', '=', $request->merchant_id]])->first();
        if(!empty($application->id))
        {
            $data['androidDriverLink'] = isset($application->android_driver_link) ? $application->android_driver_link :'';
            $data['iosDriverLink'] = isset($application->ios_driver_link) ? $application->ios_driver_link :'';
        }
        return response()->json(['result' => "1", 'message' => trans('api.homeScreen'), 'data' => $data]);
    }

    public function DriverHomeScreen(Request $request)
    {
        $checkData = WebSiteHomePage::where([['merchant_id', '=', $request->merchant_id]])->first();
        if (empty($checkData)) {
            return response()->json(['result' => "0", 'message' => trans("$string_file.data_not_found"), 'data' => []]);
        }
        $data = $this->Driver($checkData, $request->merchant_id);
        return response()->json(['result' => "1", 'message' => trans('api.homeScreen'), 'data' => $data]);
    }

    public function User(WebSiteHomePage $checkData, $merchant_id)
    {
        $logo = $checkData->Merchant->WebsiteHomePage ? $checkData->Merchant->WebSiteHomePage->logo : '';
        return [
            'app_logo' => get_image( $logo , 'website_images' , $merchant_id),
            'user_login_bg_image' => get_image($checkData->user_login_bg_image , 'website_images' , $merchant_id),
            'estimate_container' => $this->estimate_container($checkData),
            'book_form_config' => $this->BookingConfig($checkData),
            'banner_image' => get_image($checkData->user_banner_image,'website_images',$merchant_id),
            'feature_board_data' => $this->features($merchant_id, 'USER'),
            'features_component' => $this->Application($merchant_id, 'USER'),
            'featured_component_main_image' => get_image($checkData->featured_component_main_image , 'website_images' , $merchant_id),
            'user_estimate_image' => get_image($checkData->user_estimate_image , 'website_images' , $merchant_id),
            'ios_link' => $checkData->Merchant->WebSiteHomePage ? $checkData->Merchant->WebSiteHomePage->ios_link : "",
            'android_link' => $checkData->Merchant->WebSiteHomePage ? $checkData->Merchant->WebSiteHomePage->android_link : "",
            'footer' => $this->Footer($checkData)
        ];
    }

    public function Driver(WebSiteHomePage $checkData, $merchant_id)
    {
        return [
            'header' => $this->DriverHeader($checkData),
            'driver_login_bg_image' => get_image($checkData->driver_login_bg_image , 'website_images' , $merchant_id),
            'features' => $this->features($merchant_id, 'DRIVER'),
            'how_app_works' => $this->DriverApplication($merchant_id, 'DRIVER'),
            'footer' => $this->Footer($checkData),
        ];
    }

    public function Footer($checkData)
    {
        return [
            "image" => get_image($checkData->driver_footer_image , 'website_images' , $checkData->merchant_id),
            "heading" => $checkData->FooterHeading,
            "subHeading" => $checkData->FooterSubHeading,
            "bgColor" => $checkData->footer_bgcolor,
            "textColor" => $checkData->footer_text_color
        ];
    }

    public function DriverHeader($checkData)
    {
        return [
            "id" => 1,
            "image" => get_image($checkData->driver_banner_image , 'website_images' , $checkData->merchant_id),
            "heading" => $checkData->DriverHeading,
            "subHeading" => $checkData->subHeading,
            "buttonText" => $checkData->driverButtonText
        ];
    }

    public function estimate_container($checkData)
    {
        return [
            "start_address_hint" => $checkData->StartAddress,
            "end_address_hint" => $checkData->EndAddress,
            "book_btn_title" => $checkData->EstimateButton,
            "description" => $checkData->EstimateDescription,
        ];
    }

    public function BookingConfig($checkData)
    {
        return [
            "start_address_hint" => $checkData->StartAddress,
            "end_address_hint" => $checkData->EndAddress,
            "book_btn_title" => $checkData->BookingButton
        ];
    }

    public function features($merchant_id, $application)
    {
        $features = [];
        $websiteFeature = WebsiteFeature::where([['application', '=', $application], ['merchant_id', '=', $merchant_id]])->get();
        if (!empty($websiteFeature->toArray())) {
            foreach ($websiteFeature as $value) {
                $features[] = [
                    "id" => $value->id,
                    "title" => $value->FeatureTitle,
                    "iconUrl" => $value->feature_image,
                    "description" => $value->FeatureDiscription
                ];
            }
        }
        return $features;
    }

    public function Application($merchant_id, $application)
    {
        $features = [];
        $websiteFeature = WebsiteFeaturesComponents::where([['application', '=', $application], ['merchant_id', '=', $merchant_id]])->get();
        if (!empty($websiteFeature->toArray())) {
            foreach ($websiteFeature as $value) {
                $features[] = [
                    "id" => $value->id,
                    "title" => $value->FeatureTitle,
                    "image" => get_image($value->feature_image , 'website_images' , $merchant_id),
                    "description" => $value->FeatureDiscription,
                    'align' => $value->align
                ];
            }
        }
        return $features;
    }

    public function DriverApplication($merchant_id, $application)
    {
        $features = [];
        $websiteFeature = WebsiteApplicationFeature::where([['application', '=', $application], ['merchant_id', '=', $merchant_id]])->get();
        if (!empty($websiteFeature->toArray())) {
            foreach ($websiteFeature as $value) {
                $features[] = [
                    "id" => $value->id,
                    "title" => $value->FeatureTitle,
                    "image" => get_image($value->image , 'website_images' , $merchant_id),
                    "description" => $value->FeatureDiscription,
                    'align' => $value->align
                ];
            }
        }
        return $features;
    }


}

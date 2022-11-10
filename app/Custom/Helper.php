<?php
namespace App\Custom;

use App\Models\Merchant;

Class Helper
{
    public function show_permissions($merchant_id= null, $permission_name = null)
    {
//        p($permission_name);
//        if($permission_name == "handyman_booking")
//        {
//            p('kk');
//        }
        $merchant = Merchant::find($merchant_id);
        $segments = array_pluck($merchant->Segment,'slag');
        $permission_segments = $merchant->Segment->whereIn('sub_group_for_app',[1,2]);
        $permission_segments = array_pluck($permission_segments,'slag');
        $handyman_segments = $merchant->Segment->where('segment_group_id',2)->count();
        $taxi_food_segments = $merchant->Segment->where('segment_group_id',1)->count();
        $grocery_clone = (in_array('GROCERY',$segments)|| in_array('GAS_DELIVERY',$segments)|| in_array('WATER_TANK_DELIVERY',$segments) || in_array('PHARMACY',$segments)|| in_array('PARCEL_DELIVERY',$segments)|| in_array('MEAT_SHOP',$segments)|| in_array('SWEET_SHOP',$segments)|| in_array('PAAN_SHOP',$segments)|| in_array('ARTIFICIAL_JEWELLERY',$segments)|| in_array('GIFT_SHOP',$segments)|| in_array('CONVENIENCE_SHOP',$segments)|| in_array('ELECTRONIC_SHOP',$segments) || in_array('FLOWER_DELIVERY',$segments) || in_array('WINE_DELIVERY',$segments) || in_array('PET_SHOP',$segments));

        switch ($permission_name):
            case 'corporate':
                return ($merchant->Configuration->corporate_admin == 1) ? true : false;
            break;
            case 'package':
                return (in_array(2,$merchant->Service) || in_array(3,$merchant->Service) || in_array(4,$merchant->Service)) ? true : false;
            break;
            case 'taxi_company':
                return ($merchant->Configuration->company_admin == 1) ? true : false;
            break;
            case 'franchisee':
                return ($merchant->franchisees_active == 1) ? true : false;
            break;
            case 'hotel':
                return ($merchant->hotel_active == 1) ? true : false;
            break;
            case 'cashback':
                return ($merchant->Configuration->cashback_module == 1) ? true : false;
            break;
            case 'security_question':
                return ($merchant->ApplicationConfiguration->security_question == 1) ? true : false;
            break;
            case 'subscription_package':
                return ($merchant->Configuration->subscription_package == 1) ? true : false;
            break;
            case 'surcharge':
                return ($merchant->ApplicationConfiguration->sub_charge == 1) ? true : false;
            break;
            case 'wallet_recharge':
                return ($merchant->Configuration->user_wallet_status == 1 || $merchant->Configuration->driver_wallet_status == 1) ? true : false;
            break;
            case 'child_terms_condition':
                return ($merchant->Configuration->family_member_enable == 1) ? true : false;
            break;
            case 'driver_commission_choices':
                return ($merchant->Configuration->subscription_package == 1 && $merchant->ApplicationConfiguration->driver_commission_choice == 1) ? true : false;
            break;
            case 'account-types':
                return ($merchant->Configuration->bank_details_enable == 1) ? true : false;
            break;
            case 'reward_points':
                return ($merchant->ApplicationConfiguration->reward_points == 1) ? true : false;
            break;

            case 'website_driver_home':
            case 'website_user_home':
                return ($merchant->Configuration->website_module == 1) ? true : false;
                break;
            case 'delivery_configuration':
                return (in_array('DELIVERY',$segments)) ? true : false;
                break;
            case 'driver_price_card':
            case 'user_price_card':
            case 'business_segment':
                return (in_array('FOOD',$segments)|| $grocery_clone) ? true : false;
            break;
            case 'handyman_booking':
                return $handyman_segments > 0 ? true : false;
            break;
            case 'service_time_slot':
                return ($handyman_segments > 0 || $grocery_clone) ? true : false;
                break;
            case 'weight_unit':
                return $taxi_food_segments > 0 ? true : false;

            case 'advertisement_banner':
                return $merchant->advertisement_module == 1 ? true : false;
                break;

            case 'vehicle_type':
            case 'vehicle_make':
            case 'vehicle_model':
            case 'category':
            case 'driver_vehicle':
            return $taxi_food_segments > 0 ? true : false;
            break;
            case 'pricing_parameter':
            case 'price_card':
            case 'ride_management':
                return (in_array('TAXI',$segments) || in_array('DELIVERY',$segments)) ? true : false;
            break;
            case 'driver_agency':
                return ($merchant->Configuration->driver_agency == 1) ? true : false;
                break;
                /*Corporate, Packages, Taxi Company, Franchisee, Hotel, Cashback, Email Configurations, Security Question,
                Subscription Package, SurCharge, Wallet_recharge, Child Terms, Delivery Module,Website Module,Rewards Point*/
            break;
            case 'handyman_charge_type':
                return ($merchant->HandymanConfiguration->additional_charges_on_booking == 1) ? true : false;
                break;
            default:
        endswitch;
        if(in_array($permission_name,$permission_segments) || in_array($permission_name,$segments) || in_array($permission_name,$segments) || ($permission_name == "HANDYMAN" && $handyman_segments > 0)){
            return true;
        }
    }
}
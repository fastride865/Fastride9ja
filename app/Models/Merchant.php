<?php

namespace App\Models;

use App\Models\BusinessSegment\BusinessSegment;
use App\Models\BusinessSegment\Order;
use App\Models\BusinessSegment\Product;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Merchant extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;

    protected $guard_name = 'merchant';

    protected $guarded = [];

    protected $hidden = array('pivot');

    public function Segment()
    {
        return $this->belongsToMany(Segment::class, 'merchant_segment', 'merchant_id')->withPivot('segment_icon', 'sequence', 'price_card_owner','is_coming_soon');
    }

    public function Driver()
    {
        return $this->hasMany(Driver::class)->where('driver_delete', '=', NULL);
    }

//    public function getAllSegmentAttribute()
//    {
//        $segment = $this->Segment()->get();
//        return array_pluck($segment, 'slag');
//    }

    public function DemoConfiguration()
    {
        return $this->hasOne(DemoConfiguration::class);
    }

    public function HandymanConfiguration()
    {
        return $this->hasOne(HandymanConfiguration::class);
    }

    public function DriverConfiguration()
    {
        return $this->hasOne(DriverConfiguration::class);
    }

    public function User()
    {
        return $this->hasMany(User::class)->where('user_delete', '=', NULL);
    }


    public function Package()
    {
        return $this->hasMany(Package::class);
    }

    public function GetCountryArea()
    {
        return $this->hasMany(CountryArea::class);
    }

    public function Country()
    {
        return $this->hasMany(Country::class)->where('country_status', '=', 1)->orderBy('sequance', 'ASC');
    }

    public function VehicleType()
    {
        return $this->hasMany(VehicleType::class)->where('vehicleTypeStatus', '=', 1);
    }

    public function VehicleMake()
    {
        return $this->hasMany(VehicleMake::class)->where('vehicleMakeStatus', '=', 1);
    }

    public function VehicleModel()
    {
        return $this->hasMany(VehicleModel::class)->where('vehicleModelStatus', '=', 1);
    }

    public function Document()
    {
        return $this->hasMany(Document::class)->where('documentStatus', '=', 1);
    }

    public function Language()
    {
        return $this->belongsToMany(Language::class);
    }

    public function CountryArea()
    {
        return $this->hasMany(CountryArea::class);
    }

    public function Configuration()
    {
        return $this->hasOne(Configuration::class);
    }

    public function PaymentMethod()
    {
        return $this->belongsToMany(PaymentMethod::class);
    }

    public function RateCard()
    {
        return $this->belongsToMany(RateCard::class);
    }

    public function ServiceType()
    {
        return $this->belongsToMany(ServiceType::class, 'merchant_service_type', 'merchant_id')
        ->withPivot('segment_id', 'sequence', 'service_icon')->orderBy('segment_id');
    }

    public function findForPassport($user_cred = null)
    {
        return Merchant::where([['alias_name', '=', $_SERVER['HTTP_ALIASNAME']], ['email', '=', $user_cred]])->first();
    }

    public function getServiceAttribute()
    {
        $serviceType = $this->ServiceType()->get();
        return array_pluck($serviceType, 'id');
    }

    public function PaymentOption()
    {
        return $this->belongsToMany(PaymentOption::class);
    }

    public function Corporate()
    {
        return $this->hasMany(Corporate::class);
    }

    public function ApplicationConfiguration()
    {
        return $this->hasOne(ApplicationConfiguration::class);
    }

    public function Application()
    {
        return $this->hasOne(Application::class);
    }

    public function Booking()
    {
        return $this->hasMany(Booking::class);
    }

    public function Onesignal()
    {
        return $this->hasOne(Onesignal::class);
    }

    public function ActiveWebOneSignals()
    {
        return $this->hasMany(MerchantWebOneSignal::class, 'merchant_id')->where([['status', true]]);
    }

    public function BookingConfiguration()
    {
        return $this->hasOne(BookingConfiguration::class);
    }

    public function ApplicationTheme()
    {
        return $this->hasOne(ApplicationTheme::class);
    }

    public function Question()
    {
        return $this->hasMany(Question::class);
    }

    public function AppNavigationDrawer()
    {
        return $this->belongsToMany(AppNavigationDrawer::class, 'merchant_nav_drawers');
    }


    public function getNavigationDrawerAttribute()
    {
        $id = $this->id;
        $data = MerchantNavDrawer::where([['merchant_id', '=', $id], ['status', '=', true]])->orderBy('sequence', 'asc')->select(['id', 'app_navigation_drawer_id', 'image', 'sequence','additional_data'])->get();
        foreach ($data as $key => $values):
            $image = !empty($values->image) ? get_image($values->image, 'drawericons', $id, true) :
                get_image($values->AppNavigationDrawer->image, 'drawer_icon', null, false);
            $values['image'] = $image;
//                ($values['image'] == null) ? $values->AppNavigationDrawer->image : $values['image'];
            $values['name'] = $values->name;
            $values['slug'] = $values->slug;
            $values['id'] = $values->app_navigation_drawer_id;
            $values['text_colour'] = $this->ApplicationTheme->navigation_colour;
            $values['text_style'] = $this->ApplicationTheme->navigation_style;
            unset($values['app_navigation_drawer_id']);
            $data[$key] = $values;
        endforeach;
        return $data;
    }


    public function rewardPoint()
    {
        return $this->hasOne(RewardPoint :: class);
    }


    public function rewardPoints()
    {
        return $this->hasMany(RewardPoint :: class);
    }

    public function WebSiteHomePage()
    {
        return $this->hasOne(WebSiteHomePage :: class);
    }

    public function AccountType()
    {
        return $this->hasMany(AccountType::class)->where([['status',1],['admin_delete',0]]);
    }

    public function GeofenceArea()
    {
        return $this->hasMany(GeofenceArea::class);
    }

    public function GeofenceAreaQueue()
    {
        return $this->hasMany(GeofenceAreaQueue::class);
    }
//    public function DeliveryType()
//    {
//        return $this->hasMany(DeliveryType::class);
//    }
    public function Category()
    {
        return $this->hasMany(Category::class);
    }

    public function Product()
    {
        return $this->hasMany(Product::class);
    }

    public function Order()
    {
        return $this->hasMany(Order::class);
    }

    public function BusinessSegment()
    {
        return $this->hasMany(BusinessSegment::class);
    }

    public function VersionManagement()
    {
        return $this->belongsTo(VersionManagement::class);
    }

    public function HandymanCommission()
    {
        return $this->hasMany(Merchant::class);
    }

    public function HandymanOrder()
    {
        return $this->hasMany(HandymanOrder::class);
    }
}

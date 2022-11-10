<?php
return [
    'driver' => array( // driver personal document like profile
        'path' => '/driver/',
    ),
    'driver_document' => array( // driver personal document like DL etc
        'path' => '/driver-document/',
    ),
    'vehicle_owner_bank_cheque' => array(
        'path' => '/vehicle-owner-bank-cheque/',
    ),
    'vehicle' => array( // vehicle  type module images, make module  vehicle logo
        'path' => '/vehicle/',
    ),
    'vehicle_document' => array( // vehicle documents like RC, number plate, vehicle image etc
        'path' => '/vehicle-document/',
    ),
    'email' => array(
        'path' => '/email/',
    ),
    'promotions' => array(
        'path' => '/promotions/',
    ),
    'segment' => array(
        'path' => '/segment/',
    ),
    'segment_super_admin' => array(
        'path' => 'segment/',
    ),
    'service' => array(
        'path' => '/service/',
    ),
    'taxi_company_business_logo' => array(
        'path' => '/taxi-company-business-logo/',
    ),
    'user' => array( // rider or user profile images
        'path' => '/user/',
    ),
    'user_document' => array( // rider or user documents like identity etc
        'path' => '/user_document/',
    ),
    'company_logo' => array(
        'path' => '/company-logo/',
    ),
    'hotel_logo' => array(
        'path' => '/hotel-logo/',
    ),
    'map_icon' => array( //
        'path' => 'mapicon/',
    ),
    'icon' => array(
        'path' => '/icons/',
    ),
    'drawericons' => array( // drawer icon for merchant
        'path' => '/drawericons/',
    ),
    'splash' => array(
        'path' => '/splash/',
    ),
    'package' => array( // subscription package image
        'path' => '/packages/',
    ),
    'owner' => array(
        'path' => '/owner/',
    ),
    'p_icon' => array(
        'path' => '/payment_icon/',
    ),
    'merchant' => array(
        'path' => '/merchant/',
    ),
    'drawer_icon' => array( // drawer icon for super-admin
        'path' => 'drawer_icon/',
    ),
    'website_images' => array(
        'path' => '/website_images/',
    ),
    'business_logo' => array( // merchant business logo, business-segment logo
        'path' => '/business_logo/',
    ),
    'segment_image' => array( // segment images it will not dependent on merchant
        'path' => 'segment-images/',
    ),
    'payment_icon' => array( // payment icon images it will not dependent on merchant
        'path' => 'payment_icon/',
    ),
    'banners' => array(
        'path' => '/banners/',
    ),
    'booking_images' => array( // merchant business logo
        'path' => '/booking_images/',
    ),
    'business_segment' => array( // payment icon images it will not dependent on merchant
        'path' => 'business-segment/',
    ),
    // banner management is no longer used. we are using advertisement banners
//    'banner_images'=>array( //banner images
//        'path'=>'/banner-images/',
//    ),
    'product_cover_image' => array( //product cover images , for product section
        'path' => '/product-cover-image/',
    ),
    'product_image' => array( //product image , mention here
        'path' => '/product-image/',
    ),
    'product_loaded_images' => array( //product image , mention here
        'path' => '/product-loaded-images/',
    ),
    'driver_gallery' => array( //handyman gallery images of bookings
        'path' => '/driver-gallery/',
    ),
    'segment_document' => array( // handyman segments documents like RC, number plate, vehicle image etc
        'path' => '/segment-document/',
    ),
    'category' => array( // handyman segments documents like RC, number plate, vehicle image etc
        'path' => '/category/',
    ),
    'prescription_image' => array( // pharmacy prescription
        'path' => '/prescription-image/',
    ),

    'corporate_logo' => array(
        'path' => '/corporate-logo/',
    ),
    'corporate_user' => array(
        'path' => '/corporate-user/',
    ),
    'login_background' => array(
        'path' => '/login-background/',
    ),
    'send_meter_image' => array( // for rental and outstation meter images
        'path' => '/meter-image/',
    ),
    'booking_image' => array( // for rental and outstation meter images
            'path' => '/booking-image/',
        ),
    'business_login_background_image' => array( // for store/resto login background
            'path' => '/business-login_background_image/',
        ),
    'business_profile_image' => array( // for mobile application
            'path' => '/business-profile-image/',
        ),
    'agency_logo' => array( // for driver agency
        'path' => '/agency-logo/',
    ),

    // normal arrays
    'package_duration' => array( // package durations
        '1' => 'Daily (1 day)',
        '2' => 'Weekly (7 days)',
        '3' => 'Monthly (30 days)',
        '4' => 'Quatrly (90 days)',
        '5' => 'Yearly (365 days)',

    ),

    // driver document status
//    'driver_document_status' => array(
//        '1' => 'uploaded & Approval Pending',
//        '2' => 'Approved',
//        '3' => 'Reject',
//        '4' => 'Expired while running cron job',
//    ),
    'driver_document_status' => array(
        '0' => 'PENDING',
        '1' => 'UPLOADED',
        '2' => 'APPROVED',
        '3' => 'REJECTED',
        '4' => 'EXPIRED',
    ),
    // subscription package status
    'package_type' => array(
        1 => 'Free',
        2 => 'Paid',
    ),
    // driver document status
    'driver_sub_package_status' => array(
        0 => 'Inactive',
        1 => 'Assigned',
        2 => 'Active',
        3 => 'Expired',
        4 => 'Carry forwarded to next package',
    ),

    'social_links' => array(
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn'
    ),
    // vehicle document_statusstatus
    'vehicle_status' => array(
        '1' => 'Active',
        '2' => 'Inactive',
        '3' => 'Reject',
    ),

    // Signup steps
    'driver_signup_status' => array(
        '1' => 'Add basic information', // like name, email, phone etc
        '2' => 'Add more information', // service area
        '3' => 'add segment group', // segment group(vehicle, handyman services)
        '4' => 'add personal document', // add personal document
        '5' => 'add vehicle', // if segment group is 1 then add vehicle
        '6' => 'add vehicle document', // add vehicle document if segment group is 1
        '7' => 'add segment + service (upload document if segment group is 2)', //we are not maintaining separate status of segment document add services and segments
        '8' => 'add availability mode', //driver time slot if segment group 2
        '9' => 'Approved', //driver approved by admin
    ),

    // status
    'static_email' => 'keshav@apporio.com,shilpa@apporio.com',

//    'food_type' => array(
//        '1' => 'Veg',
//        '2' => 'Non-veg',
//        '3' => 'Including Eggs',
//    ),
//    'handyman_order_status' => array(
//        '1' => 'Booking Pending',//Order placed
//        '2' => 'Cancelled by User',//
//        '3' => 'Booking Rejected',
//        '4' => 'Booking Accepted',
//        '5' => 'Cancelled by Provider',
//        '6' => 'Booking Started ',
//        '7' => 'Booking Finished',
//        '8' => 'Booking Expired',
//    ),
//    'order_status' => array(
//        '1' =>'Order placed',
//        '2' =>'Cancelled by User',
//        '3' =>'Rejected by Restaurant',
//        '4' =>'Accepted by Restaurant',
//        '12' =>'Auto Expired', // Order expired because no one(either restaurant or driver) has taken action
//        '6' =>'Accepted by Driver',
//        '7' =>'Arrived at Restaurant/Store',
//        '9' =>'Order in Process', //Queue//Kitchen
//        '10' =>'Order Picked',
//        '11' =>'Order Completed',
//        '5' =>'Cancelled by Driver',
//        '8' =>'Cancelled by Admin', // here admin means Restaurant/Store,
//    ),

    'segment_slug' => array(
        '1' => 'TAXI',
        '2' => 'DELIVERY',
        '3' => 'FOOD',
        '4' => 'GROCERY',
        '5' => 'TOWING',
        '6' => 'SALON',
        '7' => 'PLUMBER_SERVICES',
        '8' => 'ELECTRICIAN',
        '9' => 'LAWYER_SERVICES',
        '10' => 'REAL_ESTATE_SERVICES',
    ),

// its for getting classes and tables (coding purpose use)
    'segment_sub_group' => array(
        'booking' => ['TAXI', 'DELIVERY', 'TOWING'],
        'order' => ['FOOD', 'GROCERY', 'PHARMACY', 'WATER_TANK_DELIVERY', 'GAS_DELIVERY', 'SECURITY_GAURD','MEAT_SHOP','PARCEL_DELIVERY','FLOWER_DELIVERY','WINE_DELIVERY','SWEET_SHOP','PAAN_SHOP','ARTIFICIAL_JEWELLERY','GIFT_SHOP','CONVENIENCE_SHOP','ELECTRONIC_SHOP','PET_SHOP'],
        'handyman_order' => ['SALON', 'PLUMBER_SERVICES', 'ELECTRICIAN', 'LAWYER_SERVICES', 'REAL_ESTATE_SERVICES', 'BABY_SITTING', 'CAR_WASH', 'DOG_WALKING', 'HOME_CLEANING', 'INSURANCE_SERVICES', 'LOCK_SMITH', 'MAIDS', 'PEST_CONTROL', 'PHYSIOTHERPY', 'SECURITY_GAURD', 'TRAVEL_AGENT', 'TUTOR_SERVICES', 'CAR_REPAIR', 'MECHANIC', 'OFFICE_CLEANING', 'PARTY_CLEANING', 'PSYCHOLOGIST', 'YOGA', 'CATERING', 'HOTELS_AND_FLIGHTS', 'CARPENTER', 'GARDENING', 'REMOVALS', 'DECORATOR', 'CONSTRUCTION', 'IRONWORKER', 'TILER', 'LIQUOR_DELIVERY', 'NURSE', 'ELDER_CARE', 'POOL_CLEANING', 'COMPUTING', 'WINCH', 'TINKER', 'MASSEUSE', 'LAUNDRY', 'CASH_DELIVERY', 'PROFESSIONAL_MOVERS', 'TRASH_REMOVAL', 'SECURITY_GUARD', 'WATER_PROOFING', 'BATHROOM', 'WOOD_COATING', 'SHADE_NET', 'SOLAR_HEATER', 'ROOFING', 'PLASTIC_REPAIR', 'IRRIGATION', 'GUTTER', 'CRACK_INJECTION', 'FLOORING', 'CEILING', 'DECOPLASTER', 'DOCTORS', 'FUNERAL_SERVICES', 'HOME_OFFICE_REPAIRS', 'TAILORING', 'AIRTIME', 'WATER', 'ELECTRICITY', 'FLOWER_BOUQUETS_SERVICES', 'LAW_CARE_AND_MOVING', 'VET_WORKERS', 'SNOW_PLOWS', 'PHOTOGRAPHY', 'BEAUTICIAN_PACKAGES_HIRING', 'SPA', 'PAINTING', 'EQUIPMENT_INSTALLATION',],
    ),

    "booking_request_driver" => [
        '1' => 'Sending, in progress',
        '2' => 'Accepted',
        '3' => 'Rejected',
        '4' => 'Cancelled', // not sure
    ],
    "image_size" => [
        'banner' => ["width" => 1280, "height" => 960],
        'product' => ["width" => 256, "height" => 256],
        'category' => ["width" => 128, "height" => 128],
    ],
    'booking_status' => array(
        '1001' => 'New bookings',//Order placed
        '1002' => 'Accepted by Driver',//
        '1003' => 'Arrived at pickup',
        '1004' => 'Ride started',
        '1005' => 'Ride completed',
        '1006' => 'Cancelled by user ',
        '1007' => 'Cancelled by driver',
        '1008' => 'Cancelled by Admin',
        '1012' => 'Partial accepted',
        '1016' => 'Auto cancelled',
        '1018' => 'Expired by cron (rider later case)',
    ),
    'delivery_service' => array(
        '1' => 'By Stores themselves',
        '2' => "By Merchant's Delivery Boy",
    ),
    'price_card_for' => array(
        '1' => 'Driver or Delivery Boy',
        '2' => 'User',
    ),
    'condition' => array(
        '1' => '< (Less Then)',
        '2' => '= (Equals To)',
        '3' => '> (Greater Then)',
        '4' => '<= (Less Then Equals To)',
        '5' => '>= (Greater Then Equals To)',
    ),

    'segment_group' => array(
        '1' => 'TAXI', 'DELIVERY', 'TOWING', 'FOOD', 'GROCERY', //vehicle_based_services
        '2' => 'SALON', 'PLUMBER_SERVICES', 'ELECTRICIAN', 'LAWYER_SERVICES', 'REAL_ESTATE_SERVICES', //handyman_based_services
    ),
    'sub_group_of_group_1' => array(
        '1' => 'FOOD',
        '2' => 'GROCERY, GAS DELIVERY etc',
        '3' => 'TAXI, DELIVERY',
    ),
    'days' => array(
        '0' => 'Sunday',
        '1' => 'Monday',
        '2' => 'Tuesday',
        '3' => 'Wednesday',
        '4' => 'Thursday',
        '5' => 'Friday',
        '6' => 'Saturday',
    ),
];
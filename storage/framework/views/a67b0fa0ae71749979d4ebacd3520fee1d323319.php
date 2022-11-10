<?php
    $merchant = get_merchant_id(false);
    $merchant_id = $merchant->id;
    $payment_config = \App\Models\PaymentConfiguration::firstOrCreate(['merchant_id' => $merchant_id])->first();
    $all_grocery_clone = \App\Models\Segment::where("sub_group_for_app",2)->get()->pluck("slag")->toArray();
    $all_food_clone = \App\Models\Segment::where("sub_group_for_app",1)->get()->pluck("slag")->toArray();
    $all_food_grocery_clone = \App\Models\Segment::whereIn("sub_group_for_app",[1,2])->get()->pluck("slag")->toArray();
    $grocery_clone = (count(array_intersect($merchant_segment, $all_grocery_clone)) > 0) ? true :false;
    $grocery_food_exist = (count(array_intersect($merchant_segment, $all_food_grocery_clone)) > 0) ? true :false;
    $food_clone = (count(array_intersect($merchant_segment, $all_food_clone)) > 0) ? true :false;
    // $grocery_clone = (in_array('GROCERY',$merchant_segment) || in_array('PHARMACY',$merchant_segment) || in_array('GAS_DELIVERY',$merchant_segment)|| in_array('WATER_TANK_DELIVERY',$merchant_segment)|| in_array('MEAT_SHOP',$merchant_segment)|| in_array('SWEET_SHOP',$merchant_segment)|| in_array('PAAN_SHOP',$merchant_segment)|| in_array('ARTIFICIAL_JEWELLERY',$merchant_segment) || in_array('GIFT_SHOP',$merchant_segment)|| in_array('CONVENIENCE_SHOP',$merchant_segment) || in_array('ELECTRONIC_SHOP',$merchant_segment) || in_array('WINE_DELIVERY',$merchant_segment) || in_array('FLOWER_DELIVERY',$merchant_segment) || in_array('PET_SHOP',$merchant_segment));
?>
<div class="site-menubar site-menubar-light">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu" id="myMenu">
                    <?php if(Auth::user('merchant')->can('dashboard')): ?>
                        <li class="site-menu-item">
                            <a href="<?php echo e(route('merchant.dashboard')); ?>">
                                <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.dashboard"); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($config->website_module == 1 && (Auth::user('merchant')->hasAnyPermission(['website_user_home','website_driver_home']))): ?>
                        <li class="site-menu-item has-sub">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon fa-globe" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.website_management"); ?></span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <ul class="site-menu-sub">
                                <?php if(Auth::user('merchant')->can('website_user_home')): ?>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('website-user-home-headings.index')); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.website_user_home"); ?></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if(Auth::user('merchant')->can('website_driver_home')): ?>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('website-driver-home-headings.index')); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.website_driver_home"); ?></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if(in_array('TAXI',$merchant_segment) && Auth::user('merchant')->can('TAXI')): ?>
                        <?php if(($config->company_admin == 1 || Auth::user('merchant')->hotel_active == 1 || $config->corporate_admin == 1 || Auth::user('merchant')->franchisees_active == 1 || $config->driver_agency == 1) && ( Auth::user('merchant')->hasAnyPermission(['taxi_company','corporate','hotel','franchisee','driver_agency']))): ?>
                            <li class="site-menu-category" id="general-title"><?php echo app('translator')->get("$string_file.associates"); ?></li>
                            <?php if($config->company_admin == 1 && Auth::user('merchant')->can('taxi_company')): ?>
                                <li class="site-menu-item">
                                    <a href="<?php echo e(route('merchant.taxi-company')); ?>">
                                        <i class="site-menu-icon fa-handshake-o" aria-hidden="true"></i>
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.taxi_company"); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if($config->driver_agency == 1): ?>
                                <li class="site-menu-item">
                                    <a href="<?php echo e(route('merchant.driver-agency')); ?>">
                                        <i class="site-menu-icon fa-handshake-o" aria-hidden="true"></i>
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.driver_agency"); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Auth::user('merchant')->hotel_active == 1 && Auth::user('merchant')->can('hotel')): ?>
                                <li class="site-menu-item">
                                    <a href="<?php echo e(route('hotels.index')); ?>">
                                        <i class="site-menu-icon fa-hotel" aria-hidden="true"></i>
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.hotel_panels"); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Auth::user('merchant')->can('corporate') && $config->corporate_admin == 1): ?>
                                <li class="site-menu-item">
                                    <a href="<?php echo e(route('corporate.index')); ?>">
                                        <i class="site-menu-icon fa-industry" aria-hidden="true"></i>
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.corporate_panels"); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Auth::user('merchant')->franchisees_active == 1 && Auth::user('merchant')->can('franchisee')): ?>
                                <li class="site-menu-item">
                                    <a class="animsition-link" href="<?php echo e(route('franchisee.index')); ?>">
                                        <span class="site-menu-title"><?php echo app('translator')->get('admin.message559'); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(Auth::user('merchant')->hasAnyPermission(['view_pricing_parameter','view_documents','view_vehicle_model','view_vehicle_make','view_countries','view_area','view_vehicle_type'])): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.mandatory_setup"); ?></li>
                        <?php if(Auth::user('merchant')->hasAnyPermission('view_pricing_parameter','view_documents','view_vehicle_model','view_vehicle_make','view_countries','view_area','view_vehicle_type')): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa-cog" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.basic_setup"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <?php if(Auth::user('merchant')->can('view_countries')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('country.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.countries"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_area')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('countryareas.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.service_area"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if((
                                        (in_array('TAXI',$merchant_segment) && count($merchant_segment) == 1 && $app_config->home_screen_view == 1)
                                     || (in_array('TAXI',$merchant_segment) && in_array('DELIVERY',$merchant_segment) && count($merchant_segment) == 2 && $app_config->home_screen_view == 1)
                                     || (in_array('FOOD',$merchant_segment) || $grocery_clone || $food_clone)
                                   ) && (Auth::user('merchant')->hasAnyPermission(['TAXI','DELIVERY']) || Auth::user('merchant')->hasAnyPermission($all_food_grocery_clone) || Auth::user('merchant')->hasAnyPermission($all_food_clone))): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.category')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.categories"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($merchant->advertisement_module == 1 && Auth::user('merchant')->can('view_banner')): ?>
                                        <li class="site-menu-item">
                                            <a href="<?php echo e(route('advertisement.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.banner_management"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if((in_array('FOOD',$merchant_segment) || $food_clone|| in_array('DELIVERY',$merchant_segment)|| $grocery_clone) && (Auth::user('merchant')->hasAnyPermission(['DELIVERY','FOOD']) || Auth::user('merchant')->hasAnyPermission($all_grocery_clone) || Auth::user('merchant')->hasAnyPermission($all_food_clone))): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link" href="<?php echo e(route('weightunit.index')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.weight_unit"); ?></span>
                                                </a>
                                            </li>
                                        <?php if((in_array('FOOD',$merchant_segment) && Auth::user('merchant')->can('FOOD')) || $food_clone && Auth::user('merchant')->can($all_food_clone)): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link"
                                                   href="<?php echo e(route('merchant.option-type.index')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.option_type"); ?></span>
                                                </a>
                                            </li>

                                            <li class="site-menu-item">
                                                <a class="animsition-link"
                                                   href="<?php echo e(route('merchant.style-management')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.style_management"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(in_array(1,$merchant_segment_group)): ?>
                                        <?php if(Auth::user('merchant')->can('view_vehicle_type')): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link" href="<?php echo e(route('vehicletype.index')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.vehicle_type"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(Auth::user('merchant')->can('view_vehicle_make') && $app_config->vehicle_make_text != 1): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link" href="<?php echo e(route('vehiclemake.index')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.vehicle_make"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(Auth::user('merchant')->can('view_vehicle_model') && $app_config->vehicle_model_text != 1): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link" href="<?php echo e(route('vehiclemodel.index')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.vehicle_model"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_documents')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('documents.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.documents"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_pricing_parameter') && (in_array('TAXI',$merchant_segment) || in_array('DELIVERY',$merchant_segment)) &&
                                        (Auth::user('merchant')->hasAnyPermission(['TAXI','DELIVERY']))): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('pricingparameter.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.pricing_parameter"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(isset($config->bank_details_enable) && $config->bank_details_enable == 1 && Auth::user('merchant')->can('view-account-types')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('account-types.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.account_type"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('TAXI',$merchant_segment) && Auth::user('merchant')->can('TAXI')): ?>
                            <?php if(isset($config->geofence_module) && $config->geofence_module == 1): ?>
                                <li class="site-menu-item has-sub">
                                    <a href="javascript:void(0)">
                                        <i class="site-menu-icon wb-map" aria-hidden="true"></i>
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.geofence_area"); ?></span>
                                        <span class="site-menu-arrow"></span>
                                    </a>
                                    <ul class="site-menu-sub">
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('geofence.restrict.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.restricted_area_management"); ?>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if((Auth::user('merchant')->hasAnyPermission(['TAXI','DELIVERY','HANDYMAN']) || (Auth::user('merchant')->hasAnyPermission($all_food_grocery_clone))) && (in_array(1,$merchant_segment_group) || in_array(2,$merchant_segment_group))): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.price_card"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <?php if(in_array(1,$merchant_segment_group)): ?>
                                        <?php if((in_array('TAXI',$merchant_segment) || in_array('DELIVERY',$merchant_segment)) && Auth::user('merchant')->hasAnyPermission(['TAXI','DELIVERY'])): ?>
                                            <li class="site-menu-item has-sub">
                                                <a href="javascript:void(0)">
                                                    <i class="site-menu-icon fa fa-taxi" aria-hidden="true"></i>
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.taxi"); ?> & <?php echo app('translator')->get("$string_file.logistics_services"); ?></span>
                                                    <span class="site-menu-arrow"></span>
                                                </a>
                                                <ul class="site-menu-sub">
                                                    <li class="site-menu-item">
                                                        <a class="animsition-link"
                                                           href="<?php echo e(route('pricecard.index')); ?>">
                                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.for_user"); ?> & <?php echo app('translator')->get("$string_file.driver"); ?> </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php if($grocery_food_exist && Auth::user('merchant')->hasAnyPermission($all_food_grocery_clone)): ?>
                                            <li class="site-menu-item has-sub">
                                                <a href="javascript:void(0)">
                                                    <i class="site-menu-icon fa fa-ship" aria-hidden="true"></i>
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.delivery_services"); ?></span>
                                                    <span class="site-menu-arrow"></span>
                                                </a>
                                                <ul class="site-menu-sub">
                                                    <li class="site-menu-item">
                                                        <a class="animsition-link"
                                                           href="<?php echo e(route('food-grocery.price_card',1)); ?>">
                                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.for_driver"); ?></span>
                                                        </a>
                                                    </li>
                                                    <li class="site-menu-item">
                                                        <a class="animsition-link"
                                                           href="<?php echo e(route('food-grocery.price_card',2)); ?>">
                                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.for_user"); ?></span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        <?php endif; ?>
                                        <?php if($app_config && $app_config['sub_charge'] == 1 && Auth::user('merchant')->can('TAXI') && Auth::user('merchant')->can('surcharge')): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link"
                                                   href="<?php echo e(route('pricecard.surgecharge')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.sub_charge"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(in_array(2,$merchant_segment_group) && Auth::user('merchant')->can('HANDYMAN') && Auth::user('merchant')->can('price_card_HANDYMAN')): ?>
                                        <li class="site-menu-item has-sub">
                                            <a href="javascript:void(0)">
                                                <i class="site-menu-icon fa fa-lightbulb-o" aria-hidden="true"></i>
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.handyman_services"); ?></span>
                                                <span class="site-menu-arrow"></span>
                                            </a>
                                            <ul class="site-menu-sub">
                                                <li class="site-menu-item">
                                                    <a class="animsition-link"
                                                       href="<?php echo e(route('merchant.segment.price_card')); ?>">
                                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.for_user"); ?></span>
                                                    </a>
                                                </li>
                                                <li class="site-menu-item">
                                                    <a class="animsition-link"
                                                       href="<?php echo e(route('merchant.segment.commission')); ?>">
                                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.for_driver"); ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if((in_array(1,$merchant_segment_group) || $handyman_apply_promocode == true) && (Auth::user('merchant')->hasAnyPermission(['TAXI','DELIVERY']) || (Auth::user('merchant')->hasAnyPermission($all_food_grocery_clone)))): ?>
                            <li class="site-menu-item">
                                <a href="<?php echo e(route('promocode.index')); ?>">
                                    <i class="site-menu-icon fa-percent" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.promo_code"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($add_info['wallet_promo_code'] == 1): ?>
                            <li class="site-menu-item">
                                <a href="<?php echo e(route('walletpromocode.index')); ?>">
                                    <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.wallet_promo_code"); ?> </span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('TAXI',$merchant_segment) && Auth::user('merchant')->can('TAXI')): ?>
                            <?php if($payment_config && $payment_config->cancel_rate_table_enable == 1): ?>
                                <li class="site-menu-item">
                                    <a href="<?php echo e(route('merchant.cancelrate')); ?>">
                                        <i class="site-menu-icon fa-flag" aria-hidden="true"></i>
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.cancel_rate_table"); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Auth::user('merchant')->can('TAXI') && Auth::user('merchant')->can('package') && (in_array(2,$service_types) || in_array(3,$service_types) || in_array(4,$service_types))): ?>
                                <li class="site-menu-item has-sub">
                                    <a href="javascript:void(0)">
                                        <i class="site-menu-icon fa-cubes" aria-hidden="true"></i>
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.package_management"); ?></span>
                                        <span class="site-menu-arrow"></span>
                                    </a>
                                    <ul class="site-menu-sub">
                                        <?php if(in_array(2,$service_types)): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link" href="<?php echo e(route('packages.index')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.package_based_services"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(in_array(3,$service_types)): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link"
                                                   href="<?php echo e(route('transferpackage.index')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.transfer"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(in_array(4,$service_types)): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link"
                                                   href="<?php echo e(route('outstationpackage.index')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.outstation_services"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if((in_array(2,$merchant_segment_group) || $grocery_clone) && (Auth::user('merchant')->can('HANDYMAN') || Auth::user('merchant')->hasAnyPermission($all_grocery_clone))): ?>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="<?php echo e(route('segment.service-time-slot')); ?>">
                                    <i class="site-menu-icon fa-tags" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.service_time_slots"); ?></span>
                                </a>
                            </li>
                            <?php if($merchant->HandymanConfiguration->additional_charges_on_booking == 1): ?>
                                <li class="site-menu-item">
                                    <a class="animsition-link" href="<?php echo e(route('segment.handyman-charge-type')); ?>">
                                        <i class="site-menu-icon fa-tags" aria-hidden="true"></i>
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.charge_types"); ?></span>
                                    </a>
                                </li>
                              <?php endif; ?>  
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(in_array('FOOD',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_FOOD')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.food_management"); ?> </li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','FOOD')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.restaurants"); ?></span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'FOOD'])); ?>">
                                <i class="site-menu-icon fa-cutlery" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                        <?php if(in_array('CATERING_SERVICE',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_CATERING_SERVICE')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.catering_service"); ?> </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','CATERING_SERVICE')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stores"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'CATERING_SERVICE'])); ?>">
                                    <i class="site-menu-icon fa-cutlery" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php if(in_array('GROCERY',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_GROCERY')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.grocery_management"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','GROCERY')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stores"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'GROCERY'])); ?>">
                                    <i class="site-menu-icon fa-list" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('GAS_DELIVERY',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_GAS_DELIVERY')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.gas_delivery_management"); ?> </li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','GAS_DELIVERY')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stores"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'GAS_DELIVERY'])); ?>">
                                    <i class="site-menu-icon fa-list" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('WATER_TANK_DELIVERY',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_WATER_TANK_DELIVERY')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.water_tank_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','WATER_TANK_DELIVERY')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stores"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'WATER_TANK_DELIVERY'])); ?>">
                                    <i class="site-menu-icon fa-list" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('PHARMACY',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_PHARMACY')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.pharmacy_management"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','PHARMACY')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stores"); ?></span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'PHARMACY'])); ?>">
                                <i class="site-menu-icon fa-list" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(in_array('MEAT_SHOP',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_MEAT_SHOP')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.meat_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','MEAT_SHOP')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stores"); ?></span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'MEAT_SHOP'])); ?>">
                                <i class="site-menu-icon fa-cutlery" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(in_array('PAAN_SHOP',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_PAAN_SHOP')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.paan_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','PAAN_SHOP')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stores"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'PAAN_SHOP'])); ?>">
                                    <i class="site-menu-icon fa-list" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('ARTIFICIAL_JEWELLERY',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_ARTIFICIAL_JEWELLERY')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.artificial_jewellery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','ARTIFICIAL_JEWELLERY')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stores"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'ARTIFICIAL_JEWELLERY'])); ?>">
                                    <i class="site-menu-icon fa-diamond" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('SWEET_SHOP',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_SWEET_SHOP')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.sweet_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','SWEET_SHOP')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stores"); ?></span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'SWEET_SHOP'])); ?>">
                                <i class="site-menu-icon fa-list" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(in_array('GIFT_SHOP',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_GIFT_SHOP')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.gift_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','GIFT_SHOP')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'GIFT_SHOP'])); ?>">
                                    <i class="site-menu-icon fa-gift" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('CONVENIENCE_SHOP',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_CONVENIENCE_SHOP')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.convenience_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','CONVENIENCE_SHOP')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'CONVENIENCE_SHOP'])); ?>">
                                <i class="site-menu-icon fa-list" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(in_array('ELECTRONIC_SHOP',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_ELECTRONIC_SHOP')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.electronics_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','ELECTRONIC_SHOP')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'ELECTRONIC_SHOP'])); ?>">
                                    <i class="site-menu-icon fa-lightbulb-o" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('FLOWER_DELIVERY',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_FLOWER_DELIVERY')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.flower_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','FLOWER_DELIVERY')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'FLOWER_DELIVERY'])); ?>">
                                    <i class="site-menu-icon fa-gift" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('WINE_DELIVERY',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_WINE_DELIVERY')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.wine_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','WINE_DELIVERY')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'WINE_DELIVERY'])); ?>">
                                    <i class="site-menu-icon fa-list" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('PET_SHOP',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_PET_SHOP')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.pet_shops"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','PET_SHOP')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'PET_SHOP'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                    <?php if(in_array('HARDWARE_DELIVERY',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_HARDWARE_DELIVERY')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.hardware_delivery"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link"
                               href="<?php echo e(route('merchant.business-segment','HARDWARE_DELIVERY')); ?>">
                                <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                            </a>
                        </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'HARDWARE_DELIVERY'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                    <?php endif; ?>
                        <?php if(in_array('CATERING_SERVICES',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_CATERING_SERVICES')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.catering_service"); ?></li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','CATERING_SERVICES')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'CATERING_SERVICES'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('DRINKS_AND_CIGARS',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_DRINKS_AND_CIGARS')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.drinks_gigas"); ?></li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','DRINKS_AND_CIGARS')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'DRINKS_AND_CIGARS'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('FASHION',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_FASHION')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.fashion"); ?></li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','FASHION')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'FASHION'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('TICKETS',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_TICKETS')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.tickets"); ?></li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','TICKETS')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.shops"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'TICKETS'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('ECOMMERCE',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_ECOMMERCE')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.ecommerce"); ?></li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','ECOMMERCE')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.ecommerce"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'ECOMMERCE'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('WIFI',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_WIFI')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.wifi"); ?></li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','WIFI')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.wifi"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'WIFI'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('HOME_AND_DECOR',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_HOME_AND_DECOR')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.home_decor"); ?></li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','HOME_AND_DECOR')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.home_decor"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'HOME_AND_DECOR'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('OTHER_BUSINESSES',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_OTHER_BUSINESSES')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.other_business"); ?></li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','OTHER_BUSINESSES')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.other_business"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'OTHER_BUSINESSES'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(in_array('SALON_SHOP',$merchant_segment) && Auth::user('merchant')->can('view_business_segment_SALON_SHOP')): ?>
                            <li class="site-menu-category"
                                id="general-title"><?php echo app('translator')->get("$string_file.other_business"); ?></li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment','SALON_SHOP')); ?>">
                                    <i class="site-menu-icon fa-home" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.slon_shop"); ?></span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('merchant.business-segment.orders',['slug'=>'SALON_SHOP'])); ?>">
                                    <i class="site-menu-icon fa-list-ol" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.orders"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php if(in_array(2,$merchant_segment_group) && Auth::user('merchant')->can('booking_management_HANDYMAN')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.handyman_services"); ?></li>
                        <li class="site-menu-item">
                            <a class="animsition-link" href="<?php echo e(route('handyman.orders')); ?>">
                                <i class="site-menu-icon fa-wpforms" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.booking_management"); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(in_array('DELIVERY',$merchant_segment) && Auth::user('merchant')->can('DELIVERY')): ?>
                        <li class="site-menu-category"
                            id="rider-title"><?php echo app('translator')->get("$string_file.delivery_management"); ?></li>
                        <li class="site-menu-item has-sub">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon fa-truck" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.delivery_management"); ?></span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <ul class="site-menu-sub">
                                <li class="site-menu-item">
                                    <a href="<?php echo e(route('delivery_product.index')); ?>">
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.products"); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php if(Auth::user('merchant')->can('ride_management_DELIVERY')): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa-car" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.rides"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub open">
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.activeride',['slug' => 'DELIVERY'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.ongoing_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.completeride',['slug' => 'DELIVERY'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.completed_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.cancelride',['slug' => 'DELIVERY'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.cancelled_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.failride',['slug' => 'DELIVERY'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.failed_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.autocancel',['slug' => 'DELIVERY'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.auto_cancelled_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.all.ride', ['slug' => 'DELIVERY'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.all_rides"); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(in_array('TAXI',$merchant_segment) && Auth::user('merchant')->can('TAXI') && (Auth::user('merchant')->hasAnyPermission(['manualdispach','ride_management_TAXI']))): ?>
                        <li class="site-menu-category"
                            id="booking-title"><?php echo app('translator')->get("$string_file.taxi_management"); ?></li>
                        <?php if(Auth::user('merchant')->can('manualdispach')): ?>
                            <li class="site-menu-item">
                                <a href="<?php echo e(route('merchant.test.manualdispach')); ?>">
                                    <i class="site-menu-icon fa fa-truck" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.manual_dispatch"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->can('ride_management_TAXI')): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa-car" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.rides"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub open">
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.activeride',['slug' => 'TAXI'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.ongoing_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.completeride',['slug' => 'TAXI'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.completed_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.cancelride',['slug' => 'TAXI'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.cancelled_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.failride',['slug' => 'TAXI'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.failed_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.autocancel',['slug' => 'TAXI'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.auto_cancelled_rides"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.all.ride',['slug' => 'TAXI'])); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.all_rides"); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(Auth::user('merchant')->hasAnyPermission(['view_drivers','create_drivers','basic_driver_signup','pending_drivers_approval','rejected_drivers','expired_driver_documents','view_pending_vehicle_apporvels','view_all_vehicles','view_driver_map','view_heat_map'])): ?>
                        <li class="site-menu-category"
                            id="driver-title"><?php echo app('translator')->get("$string_file.driver_management"); ?></li>
                        <?php if(Auth::user('merchant')->hasAnyPermission(['view_drivers','create_drivers','basic_driver_signup','pending_drivers_approval','rejected_drivers','expired_driver_documents'])): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa-drivers-license" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.drivers"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <?php if(Auth::user('merchant')->can('view_drivers')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('driver.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.all_driver"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('create_drivers')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('driver.add')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.add_driver"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('basic_driver_signup')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.driver.basic')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.basic_signup"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('pending_drivers_approval')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.driver.pending.show')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.pending_approval"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('rejected_drivers')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.driver.rejected')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.rejected_drivers"); ?></span>
                                            </a>
                                        </li>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.driver.rejected.temporary')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.temporary_rejected_drivers"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($config->driver_agency == 1 && Auth::user('merchant')->can('driver_agency')): ?>
                                        <li class="site-menu-item">
                                            <a href="<?php echo e(route('merchant.driver-agency.drivers')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.driver_agency_drivers"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('expired_driver_documents')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.driver.goingtoexpiredocuments')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.docs_going_expire"); ?></span>
                                            </a>
                                        </li>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.driver.expiredocuments')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.expired_document"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->hasAnyPermission(['view_all_vehicles','view_pending_vehicle_apporvels','view_rejected_vehicles']) && in_array(1,$merchant_segment_group)): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa-cab" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.vehicles"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <?php if(Auth::user('merchant')->can('view_all_vehicles')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.driver.allvehicles')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.all_vehicles"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(has_driver_multiple_or_existing_vehicle(null,$merchant_id,$by ='merchant') == true): ?>
                                        <?php if(Auth::user('merchant')->can('view_pending_vehicle_apporvels')): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link"
                                                   href="<?php echo e(route('merchant.driver.pending.vehicles')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.pending_approval"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if(Auth::user('merchant')->can('view_rejected_vehicles')): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link" href="<?php echo e(route('merchant.vehicle.rejected')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.rejected_vehicle"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->hasAnyPermission(['view_driver_map','view_heat_map'])): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa-map" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.map"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <?php if(Auth::user('merchant')->can('view_driver_map')): ?>
                                        <?php if($config->lat_long_storing_at == 2): ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link"
                                                   href="http://68.183.85.170/v2/webhooks?package_name=<?php echo e($app_config->merchant_package_name); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.driver_map"); ?></span>
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="site-menu-item">
                                                <a class="animsition-link" href="<?php echo e(route('merchant.drivermap')); ?>">
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.driver_map"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_heat_map')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.heatmap')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.heat_map"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(Auth::user('merchant')->can('view_rider')): ?>
                        <li class="site-menu-category"
                            id="rider-title"><?php echo app('translator')->get("$string_file.user_management"); ?></li>
                        <li class="site-menu-item">
                            <a href="<?php echo e(route('users.index')); ?>">
                                <i class="site-menu-icon wb-users" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.users"); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if(Auth::user('merchant')->can('TAXI') && Auth::user('merchant')->can('subscription_package') && $config->subscription_package == 1): ?>
                        <li class="site-menu-category"
                            id="rider-title"><?php echo app('translator')->get("$string_file.subscription_management"); ?></li>
                        <li class="site-menu-item">
                            <a href="<?php echo e(route('subscription.index')); ?>">
                                <i class="site-menu-icon fa-cube" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.packages"); ?></span>
                            </a>
                        </li>
                        <li class="site-menu-item">
                            <a href="<?php echo e(route('duration.index')); ?>">
                                <i class="site-menu-icon fa-hourglass-2" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.durations"); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if((in_array('TAXI',$merchant_segment) || in_array('DELIVERY',$merchant_segment)) && Auth::user('merchant')->hasAnyPermission(['view_sos_number','view_sos_request','customer_support'])): ?>
                        <li class="site-menu-category"
                            id="rider-title"><?php echo app('translator')->get("$string_file.support_system"); ?></li>
                        <?php if(Auth::user('merchant')->hasAnyPermission(['view_sos_number','view_sos_request'])): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa-volume-control-phone" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.sos"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <?php if(Auth::user('merchant')->can('view_sos_number')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('sos.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.sos_number"); ?> </span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_sos_request')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.sos.requests')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.sos_request"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->can('customer_support')): ?>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="<?php echo e(route('merchant.customer_support')); ?>">
                                    <i class="site-menu-icon fa-support" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.customer_support"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if(Auth::user('merchant')->hasAnyPermission(['view_refer','view_promotion','view_cms','view_child_terms','reward_points','view_language_strings','security_question','wallet_recharge','driver_commission_choices','view_payment_methods']) || $config->referral_code_enable == 1): ?>
                        <li class="site-menu-category" id="other-title"><?php echo app('translator')->get("$string_file.other"); ?></li>
                        <?php if(Auth::user('merchant')->hasAnyPermission(['view_cms','view_child_terms','view_language_strings','driver_commission_choices','view_payment_methods'])): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa-pencil-square" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.content_management"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <?php if(Auth::user('merchant')->can('view_cms')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('cms.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.cms_pages"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if(Auth::user('merchant')->can('view_child_terms') && $config->family_member_enable == 1): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('child-terms-conditions.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.child_terms"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_language_strings')): ?>

                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('applicationstring.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.app_strings"); ?></span>
                                            </a>
                                        </li>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.module-strings')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.admin_strings"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('TAXI') && Auth::user('merchant')->can('driver_commission_choices') && $config->subscription_package == 1): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('driver-commission-choices.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.driver_commission_choice"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>








                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->can('view_promotion')): ?>
                            <li class="site-menu-item">
                                <a href="<?php echo e(route('promotions.index')); ?>">
                                    <i class="site-menu-icon wb-bell" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.promotional_notification"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->can('wallet_recharge')): ?>
                            <li class="site-menu-item">
                                <a href="<?php echo e(route('Wallet.recharge')); ?>">
                                    <i class="site-menu-icon fa-money" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.wallet_recharge"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($app_config && $app_config->reward_points == 1 && Auth::user('merchant')->can('reward_points')): ?>
                            <li class="site-menu-item">
                                <a href="<?php echo e(route('reward-points.index')); ?>">
                                    <i class="site-menu-icon fa-trophy" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.reward_points"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($config->referral_code_enable == 1 && Auth::user('merchant')->can('view_refer')): ?>
                            <li class="site-menu-item">
                                <a href="<?php echo e(route('referral-system')); ?>">
                                    <i class="site-menu-icon fa-share-alt" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.referral_system"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->can('security_question') && $app_config->security_question == 1): ?>
                            <li class="site-menu-item">
                                <a href="<?php echo e(route('questions.index')); ?>">
                                    <i class="site-menu-icon fa-question-circle" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.questions"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(((in_array('FOOD',$merchant_segment) || in_array('GROCERY',$merchant_segment)) && Auth::user('merchant')->hasAnyPermission($grocery_food_exist)) || Auth::user('merchant')->can('view_driver_cash_out')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.transaction_management"); ?></li>
                        <li class="site-menu-item has-sub">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon wb-users" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.cashout_request"); ?></span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <ul class="site-menu-sub">
                                <?php if((in_array('FOOD',$merchant_segment) || in_array('GROCERY',$merchant_segment)) && Auth::user('merchant')->hasAnyPermission(['FOOD','GROCERY'])): ?>
                                    
                                    <li class="site-menu-item">
                                        <a class="animsition-link"
                                           href="<?php echo e(route('merchant.business-segment.cashout_request')); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.business_segment"); ?></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if(Auth::user('merchant')->can('view_driver_cash_out')): ?>
                                    <li class="site-menu-item">
                                        <a class="animsition-link" href="<?php echo e(route('merchant.driver.cashout_request')); ?>">
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.driver"); ?></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if(Auth::user('merchant')->can('view_reports_charts')): ?>
                        <li class="site-menu-category"
                            id="general-title"><?php echo app('translator')->get("$string_file.report_charts"); ?></li>
                        <li class="site-menu-item has-sub">
                            <a href="javascript:void(0)">
                                <i class="site-menu-icon fa fa-line-chart" aria-hidden="true"></i>
                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.earning"); ?></span>
                                <span class="site-menu-arrow"></span>
                            </a>
                            <ul class="site-menu-sub">
                                        <?php if(in_array(1,$merchant_segment_group)): ?>
                                            <?php if(in_array('TAXI',$merchant_segment) || in_array('DELIVERY',$merchant_segment)): ?>
                                                <li class="site-menu-item has-sub">
                                                    <a href="<?php echo e(route("merchant.taxi-services-report")); ?>">
                                                            <i class="site-menu-icon fa fa-taxi" aria-hidden="true"></i>
                                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.taxi"); ?> & <?php echo app('translator')->get("$string_file.logistics_services"); ?></span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php if(in_array('FOOD',$merchant_segment) || in_array('GROCERY',$merchant_segment)|| in_array('PHARMACY',$merchant_segment)|| in_array('GAS_DELIVERY',$merchant_segment)|| in_array('WATER_TANK_DELIVERY',$merchant_segment)|| in_array('PARCEL_DELIVERY',$merchant_segment)|| in_array('MEAT_SHOP',$merchant_segment)|| in_array('SWEET_SHOP',$merchant_segment)|| in_array('PAAN_SHOP',$merchant_segment)|| in_array('ARTIFICIAL_JEWELLERY',$merchant_segment)  || in_array('WINE_DELIVERY',$merchant_segment)): ?>
                                                <li class="site-menu-item has-sub">
                                                    <a href="<?php echo e(route("merchant.delivery-services-report")); ?>">
                                                            <i class="site-menu-icon fa fa-ship" aria-hidden="true"></i>
                                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.delivery_services"); ?></span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(in_array(2,$merchant_segment_group)): ?>
                                            <li class="site-menu-item has-sub">
                                                <a href="<?php echo e(route("merchant.handyman-services-report")); ?>">
                                                        <i class="site-menu-icon fa fa-lightbulb-o" aria-hidden="true"></i>
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.handyman_services"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                <li class="site-menu-item has-sub">
                                    <a href="<?php echo e(route("merchant.driver.earning")); ?>">
                                        <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                                        <span class="site-menu-title"><?php echo app('translator')->get("$string_file.driver_earning"); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fa fa-file" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.wallet_transaction"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <li class="site-menu-item has-sub">
                                        <a href="<?php echo e(route("transaction.wallet-report",["slug" => "USER"])); ?>">
                                            <i class="site-menu-icon fa fa-lightbulb-o" aria-hidden="true"></i>
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.user"); ?></span>
                                        </a>
                                    </li>
                                    <li class="site-menu-item has-sub">
                                        <a href="<?php echo e(route("transaction.wallet-report",["slug" => "DRIVER"])); ?>">
                                            <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.driver"); ?></span>
                                        </a>
                                    </li>
                                    <?php if(in_array(1,$merchant_segment_group)): ?>
                                        <?php if(in_array('FOOD',$merchant_segment) || in_array('GROCERY',$merchant_segment)|| in_array('PHARMACY',$merchant_segment)|| in_array('GAS_DELIVERY',$merchant_segment)|| in_array('WATER_TANK_DELIVERY',$merchant_segment)|| in_array('PARCEL_DELIVERY',$merchant_segment)|| in_array('MEAT_SHOP',$merchant_segment)|| in_array('SWEET_SHOP',$merchant_segment)|| in_array('PAAN_SHOP',$merchant_segment)|| in_array('ARTIFICIAL_JEWELLERY',$merchant_segment)  || in_array('WINE_DELIVERY',$merchant_segment) || in_array('HARDWARE_DELIVERY',$merchant_segment)): ?>
                                            <li class="site-menu-item has-sub">
                                                <a href="<?php echo e(route("transaction.wallet-report",["slug" => "BUSINESS-SEGMENT"])); ?>">
                                                    <i class="site-menu-icon fa fa-ship" aria-hidden="true"></i>
                                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.business_segment"); ?></span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php if($config->transactions_view_enable == 1): ?>
                            <li class="site-menu-item has-sub">
                                <a href="<?php echo e(route('payment.gateway.transactions')); ?>">
                                    <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.payment_transaction"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($config->referral_code_enable == 1): ?>
                            <li class="site-menu-item">
                                <a href="<?php echo e(route('report.referral')); ?>">
                                    <i class="site-menu-icon fa-link" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.referral"); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if(((in_array('TAXI',$merchant_segment) || in_array('DELIVERY',$merchant_segment)) && Auth::user('merchant')->hasAnyPermission(['DELIVERY','TAXI','navigation_drawers'])) ||
                        (Auth::user('merchant')->hasAnyPermission(['DELIVERY','TAXI','HANDYMAN']) || Auth::user('merchant')->hasAnyPermission($all_food_grocery_clone)) ||
                        (isset($config->stripe_connect_enable) && $config->stripe_connect_enable == 1) ||
                        Auth::user('merchant')->hasAnyPermission(['view_admin','view_role','view_configuration','view_service_types','navigation_drawers','view_applications_url','view_onesignal','view_email_configurations','view-driver-account-types','view_payment_methods'])): ?>
                        <li class="site-menu-category" id="settings-title"><?php echo app('translator')->get("$string_file.settings"); ?></li>
                        <?php if( Auth::user('merchant')->hasAnyPermission(['view_admin','view_role'])): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon wb-users" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.sub_admin"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <?php if(Auth::user('merchant')->can('view_admin')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('subadmin.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.admin_list"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_role')): ?>
                                        
                                        
                                        
                                        
                                        
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('new-role.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.role"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if(((in_array('TAXI',$merchant_segment) || in_array('DELIVERY',$merchant_segment)) && Auth::user('merchant')->hasAnyPermission(['DELIVERY','TAXI','navigation_drawers'])) ||
                            (Auth::user('merchant')->hasAnyPermission(['DELIVERY','TAXI','HANDYMAN']) || Auth::user('merchant')->hasAnyPermission($all_food_grocery_clone)) ||
                            (isset($config->stripe_connect_enable) && $config->stripe_connect_enable == 1) ||
                            Auth::user('merchant')->hasAnyPermission(['view_configuration','view_service_types','view_applications_url','view_onesignal','view_email_configurations','view-driver-account-types','view_payment_methods'])): ?>
                            <li class="site-menu-item has-sub">
                                <a href="javascript:void(0)">
                                    <i class="site-menu-icon fas fa-cogs" aria-hidden="true"></i>
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.settings_configuration"); ?></span>
                                    <span class="site-menu-arrow"></span>
                                </a>
                                <ul class="site-menu-sub">
                                    <?php if(Auth::user('merchant')->can('view_configuration')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.general_configuration')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.general"); ?></span>
                                            </a>
                                        </li>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.booking_configuration')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.request_config"); ?></span>
                                            </a>
                                        </li>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.driver_configuration')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.driver_configuration"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_email_configurations')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.emailtemplate')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.email_configuration"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_service_types')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.serviceType.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.service_type_settings"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if((in_array('TAXI',$merchant_segment) || in_array('DELIVERY',$merchant_segment)) && Auth::user('merchant')->hasAnyPermission(['DELIVERY','TAXI','navigation_drawers'])): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('navigation-drawer.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.navigation_drawer"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_applications_url')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.application')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.application_url"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_onesignal')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('merchant.onesignal')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.push_notification_config"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->hasAnyPermission(['DELIVERY','TAXI','HANDYMAN']) || Auth::user('merchant')->hasAnyPermission($all_food_grocery_clone)): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link" href="<?php echo e(route('cancelreason.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.cancel_reason"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('view_payment_methods')): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.paymentMethod.index')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.payment_method"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(isset($config->stripe_connect_enable) && $config->stripe_connect_enable == 1): ?>
                                        <li class="site-menu-item">
                                            <a class="animsition-link"
                                               href="<?php echo e(route('merchant.stripe_connect_configuration')); ?>">
                                                <span class="site-menu-title"><?php echo app('translator')->get("$string_file.stripe_connect"); ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="site-menubar-footer" id="sidebarfooter-title">
        <a href="<?php echo e(route('merchant.general_configuration.store')); ?>" class="fold-show" data-placement="top"
           data-toggle="tooltip" data-original-title="General">
            <span class="icon fa-gears" aria-hidden="true"></span>
        </a>
        <a href="<?php echo e(route('merchant.profile')); ?>" data-placement="top" data-toggle="tooltip"
           data-original-title="Update Profile">
            <span class="icon wb-user" aria-hidden="true"></span>
        </a>
        <a href="<?php echo e(route('merchant.logout')); ?>" data-placement="top" data-toggle="tooltip"
           data-original-title="Logout">
            <span class="icon wb-power" aria-hidden="true"></span>
        </a>
    </div>
</div>
<?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/layouts/sidebar.blade.php ENDPATH**/ ?>
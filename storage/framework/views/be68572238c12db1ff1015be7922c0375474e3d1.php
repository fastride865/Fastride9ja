<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content container-fluid">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->hasAnyPermission(['price_card_TAXI','price_card_DELIVERY'])): ?>
                            <a href="<?php echo e(route('pricecard.add')); ?>">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="<?php echo app('translator')->get("$string_file.add_price_card"); ?>"></i>
                                </button>
                            </a>
                            <a href="<?php echo e(route('excel.pricecard')); ?>">
                                <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="fa-money" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.price_card_management"); ?> </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="<?php echo e(route('merchant.pricecard.search')); ?>" method="GET">
                        <?php echo csrf_field(); ?>
                        <div class="table_search row p-3">
                            <div class="col-md-2 active-margin-top"><?php echo app('translator')->get("$string_file.search"); ?> :</div>
                            <div class="col-md-4 col-xs-12 form-group active-margin-top">
                                <?php echo Form::select('area',add_blank_option($areas,trans("$string_file.area")),old('area',$area_id),["class"=>"form-control serviceAreaList","id"=>"area","required"=>true]); ?>

                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                <button class="btn btn-primary" type="submit" name="seabt12"><i class="fa fa-search"
                                                                                                aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="col-md-1 col-xs-12 form-group active-margin-top">
                                <a href="<?php echo e(route('pricecard.index')); ?>">
                                    <button type="button" class="btn btn-primary"><i class="wb-reply"></i></button>
                                </a>
                            </div>
                        </div>
                    </form>
                    <table class="display nowrap table table-hover table-stripedw-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.services"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.package"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.price_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.base_fare"); ?></th>
                            
                            <?php if($config->user_wallet_status == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.min_wallet_amount"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.commission_from_driver"); ?></th>
                            <?php if($config->company_admin == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.commission_from_taxi_company"); ?></th>
                            <?php endif; ?>
                            <?php if($config->hotel_admin == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.commission_for_hotel"); ?></th>
                            <?php endif; ?>
                            <?php if($config->sub_charge == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.surcharge_status"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.type_of_surcharge"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.surcharge_value"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $pricecards->firstItem();
                        $commission_type = get_commission_type($string_file);
                        $commission_method = get_commission_method($string_file);
                        $on_off = get_on_off($string_file);
                        $charge_type = $sub_charge_type = ["1" => trans($string_file.'.nominal'),"2"=>trans($string_file.".multiplier")];
                        ?>
                        <?php $__currentLoopData = $pricecards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pricecard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php echo e($pricecard->price_card_name); ?>

                                </td>
                                <td>
                                    <?php echo e($pricecard->CountryArea->CountryAreaName); ?> <br>
                                    <i>(<?php echo e(($pricecard->CountryArea->is_geofence == 1) ? trans("$string_file.geofence") : trans("$string_file.service")); ?> <?php echo app('translator')->get("$string_file.area"); ?>
                                        )</i>
                                </td>

                                <td><?php echo e($pricecard->ServiceType->serviceName); ?></td>
                                <td>
                                    <?php echo e($pricecard->VehicleType->VehicleTypeName); ?>

                                </td>
                                <td>
                                    <?php if(empty($pricecard->service_package_id)): ?>
                                        ------
                                    <?php else: ?>
                                        <?php if($pricecard->ServiceType->additional_support == 2): ?>
                                            <?php echo e(isset($pricecard->OutstationPackage) ? $pricecard->OutstationPackage->PackageName : "---"); ?>

                                        <?php elseif($pricecard->ServiceType->additional_support == 1): ?>
                                            <?php echo e(isset($pricecard->ServicePackage) ? $pricecard->ServicePackage->PackageName : "---"); ?>

                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php switch($pricecard->pricing_type):
                                        case (1): ?>
                                        <?php echo app('translator')->get("$string_file.variable"); ?>
                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <?php echo app('translator')->get("$string_file.fixed_price"); ?>
                                        <?php break; ?>
                                        <?php case (3): ?>
                                        <?php echo app('translator')->get("$string_file.input_by_driver"); ?>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php if($pricecard->base_fare == ""): ?>
                                        ------
                                    <?php else: ?>
                                        <?php echo e($pricecard->CountryArea->Country->isoCode." ".$pricecard->base_fare); ?>

                                    <?php endif; ?>
                                </td>
                                
                                <?php if($config->user_wallet_status == 1): ?>
                                    <td><?php echo e($pricecard->CountryArea->Country->isoCode." ".$pricecard->minimum_wallet_amount); ?></td>
                                <?php endif; ?>
                                <td>
                                    
                                    
                                    
                                    
                                    
                                    

                                    <?php if($pricecard->PriceCardCommission->commission_method): ?>
                                        <?php echo app('translator')->get("$string_file.commission_method"); ?>
                                        : <?php echo $commission_method[$pricecard->PriceCardCommission->commission_method]; ?>

                                    <?php else: ?>
                                        -------
                                    <?php endif; ?>
                                    <br>

                                    <?php if($pricecard->PriceCardCommission->commission): ?>
                                        <?php echo app('translator')->get("$string_file.commission_value"); ?> :
                                        <?php if($pricecard->PriceCardCommission->commission_method == 1): ?>
                                            <?php echo e($pricecard->CountryArea->Country->isoCode); ?>

                                        <?php endif; ?>
                                        <?php echo $pricecard->PriceCardCommission->commission; ?>

                                        <?php if($pricecard->PriceCardCommission->commission_method == 2): ?>
                                            %
                                        <?php endif; ?>
                                    <?php else: ?>
                                        -------
                                    <?php endif; ?>
                                </td>
                                <?php if($config->company_admin == 1): ?>
                                    <td>
                                        <?php if($pricecard->PriceCardCommission->taxi_commission_method): ?>
                                            <?php echo app('translator')->get("$string_file.commission_method"); ?>
                                            : <?php echo $commission_method[$pricecard->PriceCardCommission->taxi_commission_method]; ?>

                                        <?php else: ?>
                                            -------
                                        <?php endif; ?>
                                        <br>
                                        <?php if($pricecard->PriceCardCommission->taxi_commission): ?>
                                            <?php echo app('translator')->get("$string_file.commission_value"); ?> :
                                            <?php if($pricecard->PriceCardCommission->taxi_commission_method == 1): ?>
                                                <?php echo e($pricecard->CountryArea->Country->isoCode); ?>

                                            <?php endif; ?>
                                            <?php echo $pricecard->PriceCardCommission->taxi_commission; ?>

                                            <?php if($pricecard->PriceCardCommission->taxi_commission_method == 2): ?>
                                                %
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -------
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if($config->hotel_admin == 1): ?>
                                    <td>
                                        
                                        
                                        
                                        
                                        
                                        
                                        <?php if($pricecard->PriceCardCommission->hotel_commission): ?>
                                            <?php echo app('translator')->get("$string_file.commission_value"); ?> :
                                            <?php if($pricecard->PriceCardCommission->hotel_commission_method == 1): ?>
                                                <?php echo e($pricecard->CountryArea->Country->isoCode); ?>

                                            <?php endif; ?>
                                            <?php echo $pricecard->PriceCardCommission->hotel_commission; ?>

                                            <?php if($pricecard->PriceCardCommission->hotel_commission_method == 2): ?>
                                                %
                                            <?php endif; ?>
                                        <?php else: ?>
                                            -------
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if($config->sub_charge == 1): ?>
                                    <td>
                                        <?php echo isset($on_off[$pricecard->sub_charge_status]) ? $on_off[$pricecard->sub_charge_status] : '------'; ?>

                                    </td>
                                    <td>
                                        <?php echo isset($charge_type[$pricecard->sub_charge_type]) ? $charge_type[$pricecard->sub_charge_type]: '------'; ?>

                                    </td>
                                    <td>
                                        <?php echo e(!empty($pricecard->sub_charge_value) ? $pricecard->CountryArea->Country->isoCode." ".$pricecard->sub_charge_value : '------'); ?>

                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php if($pricecard->status == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($pricecard->status == 1): ?>
                                        <a href="<?php echo e(route('merchant.pricecard.active-deactive',['id'=>$pricecard->id,'status'=>2])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i class="fa fa-eye-slash"></i> </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('merchant.pricecard.active-deactive',['id'=>$pricecard->id,'status'=>1])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                            <i class="fa fa-eye"></i> </a>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->hasAnyPermission(['price_card_TAXI','price_card_DELIVERY'])): ?>
                                        <a href="<?php echo e(route('pricecard.add',$pricecard->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $pricecards, 'data' => $data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/pricecard/index.blade.php ENDPATH**/ ?>
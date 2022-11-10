<?php $__env->startSection('content'); ?>
    <?php
        $sub_charge_type = ["1" => trans($string_file.'.nominal'),"2"=>trans($string_file.".multiplier")];
        $arr_commission_payout =  get_commission_type($string_file);
        $arr_cal_method =  get_commission_method($string_file);
        $arr_hotel_comm_type =  ["1" => trans($string_file.".extra_hotel_charged_to_customer"),"2" => trans($string_file.".commission_from_existing_ride_amount")];
        $arr_hotel_comm_type =  add_blank_option($arr_hotel_comm_type,trans("$string_file.calculation_method"));
        $arr_hotel_cal_method = add_blank_option(["1" => trans("$string_file.flat_amount")]);
        $insurnce_type = add_blank_option(["1" => trans($string_file.'.nominal'),"2"=>trans("$string_file.percentage")]);
        $arr_sub_charges = add_blank_option(get_on_off($string_file),trans("$string_file.status"));
        $arr_yes_no = add_blank_option(get_status(true,$string_file),trans("$string_file.select"));
        $sub_charge_type = add_blank_option($sub_charge_type,trans("$string_file.calculation_method"));
        $arr_fare_type = add_blank_option(["1" => trans($string_file.".round_trip_only"),"2"=>trans($string_file.".one_way_round_trip")],trans("$string_file.select"));
        $rate_card_scope = ["1" => trans($string_file.".with_in_area"),"2"=>trans($string_file.".outside_area")];

   $id = null;
   $disabled = false;
   if(!empty($price_card->id) && isset($price_card->id))
    {
     $id =  $price_card->id;
     $disabled = true;
    }
    $commission_type = NULL;
    if(isset($price_card->PriceCardCommission->commission_type))
    {
     $commission_type = $price_card->PriceCardCommission->commission_type;
    }
    ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->hasAnyPermission(['price_card_TAXI','price_card_DELIVERY'])): ?>
                            <div class="btn-group float-md-right">
                                <a href="<?php echo e(route('pricecard.index')); ?>">
                                    <button type="button" class="btn btn-icon btn-success mr-1" style="margin:10px  "><i
                                                class="wb-reply"></i></button>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.add_price_card"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>"",'class'=>"steps-validation wizard-notification","url"=>route('pricecard.save',$id),"id"=>"pricecard_form"]); ?>


                    <?php echo Form::hidden('id',$id,['class'=>'form-control','id'=>'id']); ?>

                    <?php echo Form::hidden('additional_support',null,['class'=>'form-control','id'=>'additional_support']); ?>

                    <?php echo Form::hidden('merchant_cancel_charges',$merchant->cancel_charges); ?>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5"><?php echo app('translator')->get("$string_file.name"); ?><span
                                            class="text-danger">*</span></label>
                                <?php echo Form::text('price_card_name',old('price_card_name',isset($price_card->price_card_name)? $price_card->price_card_name : NULL),['class'=>'form-control','id'=>'price_card_name','placeholder'=>"",'required'=>true]); ?>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="firstName3">
                                    <?php echo app('translator')->get("$string_file.service_area"); ?><span class="text-danger">*</span>
                                </label>
                                <?php if(empty($id)): ?>
                                    <?php echo Form::select('country_area_id',$areas,old('country_area_id'),["class"=>"form-control","id"=>"area","required"=>true]); ?>

                                    <?php if($errors->has('country_area_id')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('country_area_id')); ?></label>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo Form::text('country_area_id',isset($price_card->CountryArea->CountryAreaName) ? $price_card->CountryArea->CountryAreaName : NULL,['class'=>'form-control','id'=>'area','disabled'=>$disabled]); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.vehicle_type"); ?> <span
                                            class="text-danger">*</span>
                                </label>
                                <?php if(empty($id)): ?>
                                    <?php echo Form::select('vehicle_type_id',add_blank_option([],trans("$string_file.select")),old('vehicle_type_id'),["class"=>"form-control","id"=>"vehicle_type_id"]); ?>

                                    <?php if($errors->has('vehicle_type_id')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('vehicle_type_id')); ?></label>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo Form::text('vehicle_type_id',isset($price_card->VehicleType->VehicleTypeName) ? $price_card->VehicleType->VehicleTypeName : NULL,['class'=>'form-control','id'=>'vehicle_type_id','disabled'=>$disabled]); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lastName3">
                                    <?php echo app('translator')->get("$string_file.segment"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php if(empty($id)): ?>
                                    <?php echo Form::select('segment_id',add_blank_option($arr_segment,trans("$string_file.select")),old('segment_id'),["class"=>"form-control","id"=>"area_segment","required"=>true]); ?>

                                    <?php if($errors->has('segment_id')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('segment_id')); ?></label>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo Form::text('t_segment_id',!empty($price_card->Segment->Name($price_card->merchant_id))? $price_card->Segment->Name($price_card->merchant_id) : NULL,['class'=>'form-control','disabled'=>$disabled]); ?>

                                    <?php echo Form::hidden('segment_id',!empty($price_card->Segment->Name($price_card->merchant_id))? $price_card->segment_id : NULL,['class'=>'form-control','id'=>'area_segment']); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($additional_mover_config == 1): ?>
                            <div class="col-md-4 <?php if(empty($price_card) || $price_card->Segment->slag != "DELIVERY"): ?> custom-hidden <?php endif; ?>"
                                 id="additional_mover_div">
                                <div class="form-group">
                                    <label for="lastName3"><?php echo app('translator')->get("$string_file.additional_mover_charges"); ?>
                                        <span class="text-danger">*</span></label>
                                    <input type="text" min="0" step=".01" name="additional_mover_charge"
                                           id="additional_mover_charge"
                                           value="<?php echo e(!empty($price_card->additional_mover_charge) ? $price_card->additional_mover_charge : 0.0); ?>"
                                           class="form-control" placeholder="">
                                    <?php if($errors->has('additional_mover_charge')): ?>
                                        <label class="danger"><?php echo e($errors->first('additional_mover_charge')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lastName3">
                                    <?php echo app('translator')->get("$string_file.service_type"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php if(empty($id)): ?>
                                    <?php echo Form::select('service_type_id',add_blank_option([],trans("$string_file.select")),old('service_type_id'),["class"=>"form-control","id"=>"service_type","required"=>true]); ?>

                                    <?php if($errors->has('service_type_id')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('service_type_id')); ?></label>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo Form::text('service_type_id',isset($price_card->ServiceType->serviceName)? $price_card->ServiceType->serviceName : NULL,['class'=>'form-control','id'=>'service','disabled'=>$disabled]); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($configuration->outside_area_ratecard == 1): ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lastName3"><?php echo app('translator')->get("$string_file.price_card_scope"); ?><span
                                                class="text-danger">*</span></label>
                                    <?php echo Form::select('rate_card_scope',$rate_card_scope,old('rate_card_scope',isset($price_card->rate_card_scope) ? $price_card->rate_card_scope : NULL),["class"=>"form-control","id"=>"rate_card_scope"]); ?>

                                    <?php if($errors->has('rate_card_scope')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('rate_card_scope')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="col-md-4 corporate_inr <?php if(empty($price_card->outstation_type)): ?> custom-hidden <?php endif; ?>"
                             id="outstation_type_div">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.ride_type"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php if(empty($price_card->outstation_type)): ?>
                                    <?php echo Form::select('outstation_type',$arr_fare_type,old('outstation_type'),["class"=>"form-control","id"=>"outstation_type"]); ?>

                                <?php else: ?>
                                    <?php echo Form::text('outstation_type',isset($arr_fare_type[$price_card->outstation_type])? $arr_fare_type[$price_card->outstation_type] : NULL,['class'=>'form-control','id'=>'outstation_type','disabled'=>$disabled]); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-4 corporate_inr <?php if(empty($price_card->service_package_id)): ?> custom-hidden <?php endif; ?>"
                             id="package-service">
                            <div class="form-group">
                                <label for="emailAddress5" id="newText">
                                    <?php echo app('translator')->get("$string_file.service_package"); ?><span class="text-danger">*</span>
                                </label>
                                <?php if(empty($price_card->service_package_id)): ?>
                                    <?php echo Form::select('package_id',add_blank_option([],trans("$string_file.select")),old('package_id'),["class"=>"form-control","id"=>"package_id"]); ?>

                                    <?php if($errors->has('package_id')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('package_id')); ?></label>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo Form::text('package_id',isset($package_name)? $package_name : NULL,['class'=>'form-control','id'=>'package_id','disabled'=>$disabled]); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.price_type"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('price_type',add_blank_option(merchant_price_type($merchant->RateCard),trans("$string_file.select")),old('price_type',isset($price_card->pricing_type) ? $price_card->pricing_type : NULL),['class'=>'form-control','required'=>true,'id'=>'price_type']); ?>

                                <?php if($errors->has('price_type')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('price_type')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if((!empty($id) && $price_card->service_type_id == 4) && $price_card->outstation_type == 1): ?>
                            <?php $class = ""; ?>
                        <?php else: ?>
                            <?php $class = "custom-hidden"; ?>
                        <?php endif; ?>
                        <div class="col-md-4 corporate_inr <?php echo e($class); ?>" id="max_distance_div">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.max_distance"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::number('max_distance',old('max_distance',isset($price_card->outstation_max_distance) ? $price_card->outstation_max_distance : NULL),["class"=>"form-control","id"=>"max_distance","placeholder"=>"","min"=>"0", "step"=>"0.01"]); ?>

                            </div>
                        </div>
                        <?php if($configuration->user_wallet_status == 1): ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="minimum_wallet_amount">
                                        <?php echo app('translator')->get("$string_file.minimum_wallet_amount"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::number('minimum_wallet_amount',old('minimum_wallet_amount',isset($price_card->minimum_wallet_amount) ? $price_card->minimum_wallet_amount : 0),["class"=>"form-control","id"=>"minimum_wallet_amount","placeholder"=>"","min"=>"0", "step"=>"0.01","required"=>true]); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-4 <?php if((isset($price_card->service_type_id) && $price_card->service_type_id == 5)): ?> <?php else: ?> custom-hidden <?php endif; ?>"
                             id="extra_charge">
                            <div class="form-group">
                                <label for="extra_sheet_charge"><?php echo app('translator')->get("$string_file.extra_seat_charges"); ?>
                                    <span class="text-danger">*</span></label>
                                <?php echo Form::text('extra_sheet_charge',old('extra_sheet_charge',isset($price_card->extra_sheet_charge) ? $price_card->extra_sheet_charge : NULL),['class'=>'form-control','id'=>'extra_sheet_charge','placeholder'=>""]); ?>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group <?php if(isset($price_card->pricing_type) && $price_card->pricing_type == 3): ?> <?php else: ?> custom-hidden <?php endif; ?>"
                                 id="end-div">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.maximum_bill_amount"); ?>
                                    <span class="text-danger">*</span></label>
                                <?php echo Form::number('maximum_bill_amount',old('maximum_bill_amount',isset($price_card->maximum_bill_amount) ? $price_card->maximum_bill_amount : NULL),['class'=>'form-control','id'=>'maximum_bill_amount','placeholder'=>"","min"=>"0", "step"=>"0.01"]); ?>

                            </div>
                        </div>

                    </div>
                    <div id="dynamic_row">
                        <?php if(!empty($id)): ?>
                            <?php echo $input_html; ?>

                        <?php endif; ?>
                    </div>
                    <br>
                    <?php if($config->time_charges == 1): ?>
                        <h5 class="form-section col-md-12" style="color: black"><i
                                    class="fa fa-moon-o"></i> <?php echo app('translator')->get("$string_file.time_charges"); ?>
                        </h5>
                        <hr>
                        <div id="after-add-more">
                            <?php $i=0; ?>
                            <?php if(isset($price_card->ExtraCharges) && count($price_card->ExtraCharges) > 0 && !empty($id)): ?>
                                <?php $__currentLoopData = $price_card->ExtraCharges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="dynamic-copy">
                                        <div class="row" style="text-align: center;">
                                            <div class="col-md-9">
                                                <label for="weekdays"><?php echo app('translator')->get("$string_file.select_week_days"); ?>
                                                    :<span class="text-danger">*</span></label>
                                                <div class="form-group">
                                                    <div class="weekDays-selector" index="<?php echo e($i); ?>">
                                                        <?php $slot_week_days = explode(",",$value->slot_week_days);?>
                                                        <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php $checked =in_array($key,$slot_week_days) ? 'checked'  : ''; ?>
                                                            <?php $day_letter = substr($day, 0, 1); ?>
                                                            <input type="checkbox" value="<?php echo $key; ?>"
                                                                   name="week_days[<?php echo $i; ?>][<?php echo $key; ?>]"
                                                                   id="weekday-<?php echo $day_letter; ?>"
                                                                   class="weekday weekday<?php echo $i; ?>" <?php echo $checked; ?>>
                                                            <label for="weekday-<?php echo $day_letter; ?>"><?php echo $day_letter; ?></label>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if($i==0): ?>
                                                <div class="col-md-3">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-success add-more" type="button">
                                                            <i class="glyphicon glyphicon-plus"></i> <?php echo app('translator')->get("$string_file.add"); ?>  <?php echo app('translator')->get("$string_file.new"); ?>  <?php echo app('translator')->get("$string_file.slots"); ?>
                                                        </button>
                                                        <button class="btn btn-primary float-right" id="reset_week"
                                                                type="button">
                                                            <?php echo app('translator')->get("$string_file.reset"); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($i > 0): ?>
                                                <div class="col-md-3">
                                                    <div class="input-group-btn">
                                                        <button class="btn btn-danger remove_loop remove" value="<?php echo e($i); ?>"
                                                                type="button"><i class="glyphicon glyphicon-remove"></i>
                                                            <?php echo app('translator')->get("$string_file.remove"); ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="parametername"> <?php echo app('translator')->get("$string_file.name"); ?>
                                                        :<span class="text-danger">*</span></label>
                                                    <?php echo Form::text("parametername[".$i."]",old('parametername',$value->parameterName),['class'=>'form-control','id'=>'parametername'.$i,'placeholder'=>'']); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="begintime"><?php echo app('translator')->get("$string_file.start_time"); ?>
                                                        :<span class="text-danger">*</span></label>
                                                    <input type="text" value="<?php echo $value->slot_start_time; ?>"
                                                           class="form-control timepicker" data-plugin="clockpicker"
                                                           data-autoclose="true" id="begintime<?php echo $i; ?>" q="<?php echo $i; ?>"
                                                           name="begintime[<?php echo $i; ?>]"
                                                           onchange="EnableDisableDaySelector(this);" placeholder=""
                                                           autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="endtime"><?php echo app('translator')->get("$string_file.end_time"); ?>:<span
                                                                class="text-danger">*</span></label>
                                                    <input type="text" value="<?php echo $value->slot_end_time; ?>"
                                                           class="form-control timepicker" data-plugin="clockpicker"
                                                           data-autoclose="true" id="endtime<?php echo $i; ?>" q="<?php echo $i; ?>"
                                                           name="endtime[<?php echo $i; ?>]"
                                                           onchange="EnableDisableDaySelector(this);" placeholder=""
                                                           autocomplete="off">

                                                    <label class="radio-inline"
                                                           style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">
                                                        <input type="radio" value="1"
                                                               <?php if($value->slot_end_day == 1): ?> checked
                                                               <?php endif; ?> q=<?php echo e($i); ?> onclick="calculate(this);"
                                                               name="optradio[<?php echo $i; ?>]"><?php echo app('translator')->get("$string_file.next_day"); ?>
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" value="2"
                                                               <?php if($value->slot_end_day == 2): ?> checked
                                                               <?php endif; ?> id="charge_type<?php echo $i; ?>"
                                                               q=<?php echo e($i); ?> onclick="calculate(this);"
                                                               name="optradio[<?php echo $i; ?>]"><?php echo app('translator')->get("$string_file.same_day"); ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="slot_charges">
                                                        <?php echo app('translator')->get("$string_file.slot_charges"); ?>:<span
                                                                class="text-danger">*</span></label>
                                                    <input type="number" value="<?php echo e($value->slot_charges); ?>"
                                                           class="form-control" min="0" step="0.01"
                                                           id="slot_charges<?php echo $i; ?>" name="slot_charges[<?php echo $i; ?>]"
                                                           placeholder="" autocomplete="off">
                                                    <label class="radio-inline"
                                                           style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">
                                                        <input type="radio" value="1"
                                                               <?php if($value->slot_charge_type == 1): ?> checked
                                                               <?php endif; ?> name="charge_type[<?php echo $i; ?>]"><?php echo app('translator')->get("$string_file.nominal"); ?>
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" value="2"
                                                               <?php if($value->slot_charge_type == 2): ?> checked
                                                               <?php endif; ?> id="charge_type<?php echo $i; ?>"
                                                               name="charge_type[<?php echo $i; ?>]"><?php echo app('translator')->get("$string_file.multiplier"); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $i++; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" value="<?php echo e(count($price_card->ExtraCharges)); ?>" id="checkBoxCount">
                            <?php else: ?>
                                <div class="row" style="text-align: center;">
                                    <div class="col-md-9">
                                        <label for="weekdays"><?php echo app('translator')->get("$string_file.select_week_days"); ?>
                                            :<span class="text-danger">*</span></label>
                                        <div class="form-group">
                                            <div class="weekDays-selector" index="0">
                                                <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $day_letter = substr($day, 0, 1); ?>
                                                    <input type="checkbox" value="<?php echo $key; ?>"
                                                           name="week_days[0][<?php echo $key; ?>]"
                                                           id="weekday-<?php echo $day_letter; ?>" class="weekday weekday0"/>
                                                    <label for="weekday-<?php echo $day_letter; ?>"><?php echo $day_letter; ?></label>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group-btn">
                                            <button class="btn btn-success add-more" type="button">
                                                <i class="glyphicon glyphicon-plus"></i> <?php echo app('translator')->get("$string_file.add"); ?>  <?php echo app('translator')->get("$string_file.new"); ?>  <?php echo app('translator')->get("$string_file.slots"); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="parametername"> <?php echo app('translator')->get("$string_file.name"); ?> :<span
                                                        class="text-danger">*</span></label>
                                            <?php echo Form::text('parametername[0]',old('parametername'),['class'=>'form-control','id'=>'parametername0','placeholder'=>'']); ?>

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="begintime"><?php echo app('translator')->get("$string_file.start_time"); ?> :<span
                                                        class="text-danger">*</span></label>
                                            <input type="text" class="form-control timepicker" data-plugin="clockpicker"
                                                   data-autoclose="true" id="begintime0" q="0" name="begintime[0]"
                                                   onchange="EnableDisableDaySelector(this);" placeholder=""
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="endtime"><?php echo app('translator')->get("$string_file.end_time"); ?> :<span
                                                        class="text-danger">*</span></label>
                                            <input type="text" class="form-control timepicker" data-plugin="clockpicker"
                                                   data-autoclose="true" id="endtime0" q="0" name="endtime[0]"
                                                   onchange="EnableDisableDaySelector(this);" placeholder=""
                                                   autocomplete="off">
                                            <label class="radio-inline"
                                                   style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">
                                                <input type="radio" value="1" q="0" onclick="calculate(this);"
                                                       name="optradio[0]"><?php echo app('translator')->get("$string_file.next_day"); ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" value="2" id="charge_type0" q="0"
                                                       onclick="calculate(this);"
                                                       name="optradio[0]"><?php echo app('translator')->get("$string_file.same_day"); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="slot_charges">
                                                <?php echo app('translator')->get("$string_file.slot_charges"); ?> :<span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" min="0" step="0.01"
                                                   id="slot_charges<?php echo $i; ?>" name="slot_charges[0]" placeholder=""
                                                   autocomplete="off">
                                            <label class="radio-inline"
                                                   style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">
                                                <input type="radio" value="1" id="charge_type0" checked
                                                       name="charge_type[0]"><?php echo app('translator')->get("$string_file.nominal"); ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" value="2" id="charge_type0"
                                                       name="charge_type[0]"><?php echo app('translator')->get("$string_file.multiplier"); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="checkBoxCount" id="checkBoxCount" value="0">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if($config->insurance_enable == 1): ?>
                        <h5 class="form-section col-md-12" style="color: black"><i
                                    class="fa fa-book"></i> <?php echo app('translator')->get("$string_file.insurance"); ?></h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5"><?php echo app('translator')->get("$string_file.insurance"); ?><span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::select('insurnce_enable',$arr_yes_no,old('insurnce_enable',isset($price_card->insurnce_enable) ? $price_card->insurnce_enable :NULL),['class'=>'form-control','required'=>true,'id'=>'insurnce_enable']); ?>

                                    <?php if($errors->has('insurnce_enable')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('insurnce_enable')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?php if(isset($price_card->insurnce_enable) && $price_card->insurnce_enable == 1): ?>
                                    <?php $ensu_required = true; ?>
                                <?php else: ?> <?php $ensu_required = false; ?> <?php endif; ?>
                                <div class="form-group" id="insurnce_type">
                                    <label for="emailAddress5"><?php echo app('translator')->get("$string_file.insurance_type"); ?>
                                        :<span class="text-danger">*</span></label>
                                    <?php echo Form::select('insurnce_type',$insurnce_type,old('insurnce_type',isset($price_card->insurnce_type) ? $price_card->insurnce_type:NULL),['class'=>'form-control','required'=>$ensu_required,'id'=>'insurnce_type']); ?>

                                    <?php if($errors->has('insurnce_type')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('insurnce_type')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" id="insurnce_value">
                                    <label for="emailAddress5"><?php echo app('translator')->get("$string_file.insurance_value"); ?>
                                        :<span class="text-danger">*</span></label>
                                    <?php echo Form::number('insurnce_value',old('insurnce_value',isset($price_card->insurnce_value) ? $price_card->insurnce_value:NULL),['class'=>'form-control','id'=>'insurnce_value','placeholder'=>"","min"=>"0", "step"=>"0.01",'required'=>$ensu_required]); ?>

                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if($merchant->cancel_charges == 1): ?>
                        <h5 class="form-section col-md-12" style="color: black"><i
                                    class="fa fa-paperclip"></i> <?php echo app('translator')->get("$string_file.cancel_charges"); ?>
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5"><?php echo app('translator')->get("$string_file.cancel_charges"); ?><span
                                                class="text-danger">*</span></label>
                                    <?php echo Form::select('cancel_charges',$arr_yes_no,old('cancel_charges',isset($price_card->cancel_charges) ? $price_card->cancel_charges :NULL),['class'=>'form-control','required'=>true,'id'=>'cancel_charges']); ?>

                                    <?php if($errors->has('cancel_charges')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('cancel_charges')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4" id="cancel_first">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.cancel_time"); ?>
                                        <span class="text-danger">*</span>
                                    </label><?php echo Form::number('cancel_time',old('cancel_time',isset($price_card->cancel_time) ? $price_card->cancel_time :NULL),['class'=>'form-control','id'=>'cancel_time','placeholder'=>"","min"=>"0"]); ?>

                                </div>
                            </div>
                            <div class="col-md-4" id="cancel_second">
                                <div class="form-group">
                                    <label for="emailAddress5"><?php echo app('translator')->get("$string_file.cancel_amount"); ?><span
                                                class="text-danger">*</span></label>
                                    <?php echo Form::number('cancel_amount',old('cancel_amount',isset($price_card->cancel_amount) ? $price_card->cancel_amount :NULL),['class'=>'form-control','id'=>'cancel_amount','placeholder'=>"","min"=>"0", "step"=>"0.01"]); ?>

                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <br>
                    <h5 class="form-section col-md-12" style="color: black"><i
                                class="fa fa-paperclip"></i> <?php echo app('translator')->get("$string_file.commission_from_driver"); ?>
                    </h5>
                    <hr>
                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5"><?php echo app('translator')->get("$string_file.commission_method"); ?><span
                                            class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('commission_method',add_blank_option($arr_cal_method,trans("$string_file.select")),old('commission_method',isset($price_card->PriceCardCommission->commission_method) ? $price_card->PriceCardCommission->commission_method : NULL),["class"=>"form-control","id"=>"commission_method","required"=>true]); ?>

                                <?php if($errors->has('commission_method')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('commission_method')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="commission">
                                    <?php echo app('translator')->get("$string_file.commission_value"); ?><span class="text-danger">*</span>
                                </label>
                                <?php echo Form::number("commission",old("commission",isset($price_card->PriceCardCommission->commission) ? $price_card->PriceCardCommission->commission : NULL),["step"=>"0.01", "min"=>"0","class"=>"form-control", "id"=>"commission","placeholder"=>"","required"=>true]); ?>

                            </div>
                        </div>
                    </div>
                    <br>
                    <?php if($configuration->company_admin == 1): ?>
                        <h5 class="form-section col-md-12" style="color: black"><i
                                    class="fa fa-paperclip"></i> <?php echo app('translator')->get("$string_file.commission_from_taxi_company"); ?>
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emailAddress5"><?php echo app('translator')->get("$string_file.commission_method"); ?><span
                                                class="text-danger">*</span></label>
                                    <?php echo Form::select('taxi_commission_method',add_blank_option($arr_cal_method,trans("$string_file.select")),old('taxi_commission_method',isset($price_card->PriceCardCommission->taxi_commission_method) ? $price_card->PriceCardCommission->taxi_commission_method : NULL),["class"=>"form-control","id"=>"taxi_commission_method"]); ?>

                                    <?php if($errors->has('taxi_commission_method')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('taxi_commission_method')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taxi_commission"><?php echo app('translator')->get("$string_file.commission_value"); ?><span
                                                class="text-danger">*</span></label>
                                    <?php echo Form::number("taxi_commission",old("taxi_commission",isset($price_card->PriceCardCommission->taxi_commission) ? $price_card->PriceCardCommission->taxi_commission : NULL),["step"=>"0.01", "min"=>"0","class"=>"form-control", "id"=>"taxi_commission","placeholder"=>""]); ?>

                                </div>
                            </div>
                        </div>
                        <br>
                    <?php endif; ?>
                    <?php if($merchant->hotel_active == 1): ?>
                        <h5 class="form-section col-md-12"><i
                                    class="fa fa-paperclip"></i> <?php echo app('translator')->get("$string_file.commission_for_hotel"); ?>
                        </h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hotel_commission_method"><?php echo app('translator')->get("$string_file.commission_method"); ?>
                                        <span class="text-danger">*</span></label>
                                    <?php echo Form::select('hotel_commission_method',$arr_hotel_cal_method,old('hotel_commission_method',isset($price_card->PriceCardCommission->hotel_commission_method) ? $price_card->PriceCardCommission->hotel_commission_method : NULL),["class"=>"form-control","id"=>"hotel_commission_method"]); ?>

                                    <?php if($errors->has('hotel_commission_method')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('hotel_commission_method')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="hotel_commission">
                                        <?php echo app('translator')->get("$string_file.commission_value"); ?><span
                                                class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::number('hotel_commission',old('hotel_commission',isset($price_card->PriceCardCommission->hotel_commission) ? $price_card->PriceCardCommission->hotel_commission : NULL),['class'=>'form-control','id'=>'hotel_commission','placeholder'=>"","min"=>"0", "step"=>"0.01"]); ?>

                                </div>
                            </div>
                        </div>
                        <br>
                    <?php endif; ?>
                    <div class="row">
                        <?php if($config->sub_charge == 1): ?>
                            <h5 class="form-section col-md-12"><i
                                        class="fa fa-money"></i> <?php echo app('translator')->get("$string_file.surcharge"); ?>
                            </h5>
                            <hr>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sub_charge_status"><?php echo app('translator')->get("$string_file.surcharge_status"); ?>
                                        :<span class="text-danger">*</span></label>
                                    <?php echo Form::select('sub_charge_status',$arr_sub_charges,old('sub_charge_status',isset($price_card->sub_charge_status) ? $price_card->sub_charge_status : NULL),["class"=>"form-control","id"=>"sub_charge_status"]); ?>

                                    <?php if($errors->has('sub_charge_status')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('sub_charge_status')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-4">
                                <div class="form-group"
                                     id="sub_type">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.surcharge_type"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::select('sub_charge_type',$sub_charge_type,old('sub_charge_type',isset($price_card->sub_charge_type) ? $price_card->sub_charge_type : NULL),["class"=>"form-control","id"=>"sub_charge_type"]); ?>

                                    <?php if($errors->has('sub_charge_type')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('sub_charge_type')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group " id="sub_value">
                                    <label for="sub_charge_value"><?php echo app('translator')->get("$string_file.surcharge_value"); ?>
                                        :<span class="text-danger">*</span></label>
                                    <?php echo Form::number('sub_charge_value',old('sub_charge_value',isset($price_card->sub_charge_value) ? $price_card->sub_charge_value : NULL),['class'=>'form-control','id'=>'sub_charge_value','placeholder'=>'',"min"=>"0", "step"=>"0.01"]); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-actions right" style="margin-bottom: 3%">
                        <?php if(!$is_demo): ?>
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                            </button>
                        <?php else: ?>
                        <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php echo Form::close(); ?>

                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        function calculate(data) {
            var radio_q_value = $(data).attr('q');
            var radio_type = $(data).val();
            var current_start_time = $("input[name='begintime[" + radio_q_value + "]']").val();
            var current_end_time = $("input[name='endtime[" + radio_q_value + "]']").val();
            if (radio_type == 2) {
                if (current_end_time < current_start_time) {
                    alert("<?php echo e(trans($string_file.'.wrong_night_peak_same_day')); ?>");
                    $("input[name='optradio[" + radio_q_value + "]']").prop('checked', false);
                }
            }
        }

        function EnableDisableDaySelector(data) {
            var q_value = $(data).attr('q');
            var start_time = $("input[name='begintime[" + q_value + "]']").val();
            var end_time = $("input[name='endtime[" + q_value + "]']").val();
            if ((start_time != '') && (end_time != '')) {
                $("input[name='optradio[" + q_value + "]']").attr('disabled', false);
            } else {
                $("input[name='optradio[" + q_value + "]']").prop('checked', false);
                $("input[name='optradio[" + q_value + "]']").attr('disabled', true);
            }
        }

        function insurance() {
            var val = $("#insurnce_enable option:selected").val();
            $("#loader1").show();
            $("#insurnce_type").attr('required', false);
            $("#insurnce_value").attr('required', false);
            if (val == 1) {
                $("#insurnce_type").attr('required', true);
                $("#insurnce_value").attr('required', true);
            }
            $("#loader1").hide();
        }

        function subChargeMethod() {
            var val = $("#sub_charge_status option:selected").val();
            $("#loader1").show();
            $("#sub_type").attr('require', false);
            $("#sub_value").attr('require', false);
            if (val == 1) {
                $("#sub_type").attr('require', true);
                $("#sub_value").attr('require', true);
            }
            $("#loader1").hide();
        }

        function validateNumber(event) {
            var key = window.event ? event.keyCode : event.which;
            if (event.keyCode === 8 || event.keyCode === 46) {
                return true;
            } else if (key < 48 || key > 57) {
                return false;
            } else {
                return true;
            }
        }

        function getVehicle() {
            // var id = $("#package_id option:selected").val();
            var id = $("#area option:selected").val();
            //alert(id);
            if (id != "") {
                // $("#loader1").show();
                //var area = $('[name="area"]').val();
                var token = $('[name="_token"]').val();
                //var service = $('[name="service"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo e(route('get.area.vehicles')); ?>",
                    data: {
                        area_id: id,
                    },
                    success: function (data) {
                        console.log(data);
                        $("#vehicle_type_id").html(data);
                    }
                });
                // $("#loader1").hide();
            }
        }

        function getVehicleSegment() {
            // var id = $("#package_id option:selected").val();
            var area_id = $("#area option:selected").val();
            var vehicle_type_id = $("#vehicle_type_id option:selected").val();

            if (area_id != "" && vehicle_type_id != "") {
                // $("#loader1").show();
                //var area = $('[name="area"]').val();
                var token = $('[name="_token"]').val();
                //var service = $('[name="service"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo e(route('get.area.vehicle.segment')); ?>",
                    data: {
                        area_id: area_id,
                        vehicle_type_id: vehicle_type_id,
                        sub_group_for_admin: 1,
                    },
                    success: function (data) {
                        // console.log(data);
                        $("#area_segment").html(data);
                    }
                });
                // $("#loader1").hide();
            }
        }


        function checkService() {
            outstationMaxDis();
            var val = $("#service_type option:selected").val();
            $("#loader1").show();
            $("#extra_charge").hide();
            $("#additional_support").val('');
            $("#outstation_type_div").hide();
            $("#package-service").hide();
            $("#package_id").prop("required", false);
            $("#package_id").html("<option value=''><?php echo app('translator')->get("$string_file.select"); ?></option>");
            if (val !== "") {
                var token = $('[name="_token"]').val();
                var service_type_id = $("#service_type option:selected").val();
                var additional_support = $("#service_type option:selected").attr('additional_support');
                $("#additional_support").val(additional_support);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo route('merchant.price.card.service.config') ?>",
                    data: {
                        service_type_id: service_type_id,
                        additional_support: additional_support,
                        merchant_id: "<?php echo e($merchant->id); ?>"
                    },
                    success: function (data) {
                        if (val == 5) {
                            $("#extra_charge").show();
                        } else if (additional_support == 1 || additional_support == 2) {
                            $("#package_id").prop("required", true);
                            $("#package_id").html(data); // its div of package or special city
                            if (additional_support == "1") {
                                $("#package-service").show();
                                $("#newText").text("<?php echo app('translator')->get("$string_file.package"); ?>");
                            } else if (additional_support == 2) {
                                $("#newText").text("<?php echo app('translator')->get("$string_file.special_city"); ?>");
                                $("#outstation_type_div").show();
                            }
                        }

                    }
                });
            }
            $("#loader1").hide();
        }


        function checkServiceX() {

            var val = $("#service_type option:selected").val();
            $("#loader1").show();
            $("#extra_charge").hide();
            $("#outstation_div").hide();
            $("#fixed_div").hide();
            $("#vehicle_type_id").html("<option><?php echo app('translator')->get("$string_file.select"); ?></option>");
            $("#fixed_div").hide();
            $("#outstation_div").hide();
            $("#package_id").html("<option><?php echo app('translator')->get("$string_file.select"); ?></option>");
            $("#extra_charge").hide();
            if (val !== "") {
                var token = $('[name="_token"]').val();
                var area = $("#area option:selected").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo e(route('merchant.getRideConfig')); ?>",
                    data: {
                        service: val,
                        manual_area: area,
                    },
                    success: function (data) {
                        if (val == 5) {
                            $("#extra_charge").show();
                            $("#vehicle_type_id").html(data);
                        } else if (val == 4) {
                            $("#outstation_div").show();
                        } else if (val == 2 || val == 3) {
                            $("#fixed_div").show();
                            $("#package_id").html(data);
                        } else {
                            $("#vehicle_type_id").html(data);
                        }
                    }
                });
            }
            $("#loader1").hide();
        }

        function outstationMaxDis() {
            $('#max_distance_div').hide()
            $("#package-service").hide();
            var additional_support = $("#service_type option:selected").attr('additional_support');
            $("#max_distance").prop("required", false);
            //var service = $("#service_type option:selected").val();
            var outstation_type = $("#outstation_type option:selected").val();

            if (additional_support == 2 && outstation_type == 1) {
                $("#package_id").prop("required", false);
                $("#max_distance_div").show();
                $("#max_distance").prop("required", true);
                $("#package_id").prop("required", false);
            } else if (additional_support == 2 && outstation_type == 2) {
                $("#package-service").show();
            }
        }

        function outstation() {
            outstationMaxDis();
        }

        function disableField() {
            $("#loader1").show();
            $('#vehicle_type_id').prop("disabled", false);
            if ($('#all_vehicle_type').prop("checked") == true) {
                $('#vehicle_type_id').prop("disabled", true);
            }
            $("#loader1").hide();
        }

        function pricingType() {
            $("#end-div").hide();
            var val = $("#price_type option:selected").val();
            var id = $("#id").val();
            if(id != ""){
                var segment_id = $("#area_segment").val();
            }else{
                var segment_id = $("#area_segment option:selected").val();
            }
            console.log(segment_id);
            if (val != "" && segment_id != "") {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo route('admin.pricing.parameter'); ?>",
                    data: {type: val, segment_id: segment_id},
                    success: function (data) {
                        $('#dynamic_row').html(data);
                        $("#loader1").hide();
                    }
                });
                if (val == 3) {
                    $("#end-div").show();
                }
            } else {
                if(val == ""){
                    alert("<?php echo app('translator')->get("$string_file.please"); ?> <?php echo app('translator')->get("$string_file.select"); ?> <?php echo app('translator')->get("$string_file.price"); ?> <?php echo app('translator')->get("$string_file.type"); ?>");
                    // alert('<?php echo trans("$string_file.please")." ".trans("$string_file.select")." ".trans("$string_file.price")." ".trans("$string_file.type"); ?>');
                }else if(segment_id == ""){
                    alert("<?php echo app('translator')->get("$string_file.please"); ?> <?php echo app('translator')->get("$string_file.select"); ?> <?php echo app('translator')->get("$string_file.segment"); ?>");
                    // alert('<?php echo trans("$string_file.please")." ".trans("$string_file.select")." ".trans("$string_file.segment"); ?>');
                }
                $("#price_type option:selected").prop('selected', false);
            }
        }

        function NumberInput(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode != 46 && charCode > 31
                && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }

        // New Code
        function getService() {
            var area_id = $("#area option:selected").val();
            var vehicle_type_id = $("#vehicle_type_id option:selected").val();
            var segment_id = $("#area_segment option:selected").val();

            if (area_id != "" && vehicle_type_id != '') {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo e(route('merchant.area.services')); ?>",
                    data: {
                        area_id: area_id,
                        segment_id: segment_id,
                        vehicle_type_id: vehicle_type_id,
                        segment_group: 1,
                    },
                    success: function (data) {
                        $('#service_type').html(data);
                    }
                });
                $("#loader1").hide();
            } else {
                if(area_id == ""){
                    alert("<?php echo app('translator')->get("$string_file.please"); ?> <?php echo app('translator')->get("$string_file.select"); ?> <?php echo app('translator')->get("$string_file.service"); ?> <?php echo app('translator')->get("$string_file.area"); ?>");
                    // alert('<?php echo trans("$string_file.please")." ".trans("$string_file.select")." ".trans("$string_file.service")." ".trans("$string_file.area"); ?>');
                }else if(vehicle_type_id == ""){
                    alert("<?php echo app('translator')->get("$string_file.please"); ?> <?php echo app('translator')->get("$string_file.select"); ?> <?php echo app('translator')->get("$string_file.vehicle"); ?> <?php echo app('translator')->get("$string_file.type"); ?>");
                    // alert('<?php echo trans("$string_file.please")." ".trans("$string_file.select")." ".trans("$string_file.vehicle")." ".trans("$string_file.type"); ?>');
                }
                $("#area option:selected").prop('selected', false);
            }
        }

        function getServiceX() {
            var area_id = $("#area option:selected").val();
            var vehicle_type_id = $("#vehicle_type_id option:selected").val();
            var segment_id = $("#area_segment option:selected").val();

            if (area_id != "" && arr_segment.length > 0) {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo e(route('merchant.area.services')); ?>",
                    data: {area_id: val, arr_segment: arr_segment},
                    success: function (data) {
                        $('#service_type').html(data);
                    }
                });
                $("#loader1").hide();
            } else {
                alert('<?php echo trans("$string_file.select"); ?>');
                $("#area option:selected").prop('selected', false);
            }
        }

        function getCheckedSegment() {
            var segment = [];
            $.each($(".area_segment:checked"), function () {
                segment.push($(this).val());
            });
            return segment;
        }

        function getAreaList(id, segment) {
            $.ajax({
                method: 'GET',
                url: "<?php echo e(route('merchant.country.arealist')); ?>",
                data: {country_id: id, arr_segment: segment, option_group: 1, geo_fence: 1},
                success: function (data) {
                    $('#area').html(data);
                }
            });
        }

        $(document).on("change", "#hotel_commission_method", function () {
        });
        $(document).on("change", "#price_type", function () {
            pricingType();
        });
        $(document).on("click", "#vehicle_type_id", function () {
            getVehicleSegment();
        });
        $(document).on("change", "#service_type", function () {
            checkService();
        });
        $(document).on("change", "#sub_charge_status", function () {
            subChargeMethod();
        });
        $(document).on("change", "#insurnce_enable", function () {
            insurance();
        });
        $(document).on("change", "#package_id", function () {
            // getVehcile();
        });
        $(document).on("change", "#outstation_type", function () {
            outstation();
        });
        $(document).on("change", "#area", function () {
            //$("#service_type option:selected").prop('selected', false);
            //getService();
            getVehicle();

        });
        $(document).on("change", "#area_segment", function () {

            // $("#area option:selected").prop('selected', false);
            // $("#price_type option:selected").prop('selected', false);
            // var  segment = [];
            // $.each($(".area_segment:checked"), function(){
            //     segment.push($(this).val());
            // });
            // if(segment.length > 0)
            // {
            //     getAreaList(null,segment);
            // }
            getService();
        });

        function invisibleInput(val) {
            $('#test' + val).prop('disabled', true);
            $("#loader1").show();
            if (document.getElementById(val).checked) {
                $('#test' + val).prop('disabled', false);
            }
            $("#loader1").hide();
        }

        $('#cancel_charges').on('change', function () {
            if (this.value == "1") {
                $("#cancel_time").prop('required', true);
                $("#cancel_amount").prop('required', true);
            } else {
                $("#cancel_time").prop('required', false);
                $("#cancel_amount").prop('required', false);
            }
        });

        $(document).on('change', '#area_segment', function () {
            if (this.value == 2) {
                $('#additional_mover_div').removeClass('custom-hidden');
                $('#additional_mover_charge').prop('required', true);
            } else {
                $('#additional_mover_div').addClass('custom-hidden');
                $('#additional_mover_charge').prop('required', false);
            }
        });

        $(document).ready(function () {
            $('[id^=slot_charges]').keypress(validateNumber);
            var max_fields = 5;
            // var count = 0;
            var count = $("#checkBoxCount").val();
            $(".add-more").click(function () {
                if (count < max_fields) {
                    count++;
                    document.getElementById("checkBoxCount").value = count;
                    var html = '<div class="dynamic-copy">' +
                        '<div class="row" style="text-align: center;">' +
                        '<div class="col-md-9">' +
                        '<label for="weekdays">' +
                        '<?php echo app('translator')->get("$string_file.select_week_days"); ?>:' +
                        '<span class="text-danger">*</span>' +
                        '</label>' +
                        '<div class="form-group">' +
                        '<div class="weekDays-selector" index="' + count + '">' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="1" id="weekday-mon' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-mon' + count + '">M</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="2" id="weekday-tue' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-tue' + count + '">T</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="3" id="weekday-wed' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-wed' + count + '">W</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="4" id="weekday-thu' + count + '"class="weekday mr-1 ml-1">' +
                        '<label for="weekday-thu' + count + '">T</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="5" id="weekday-fri' + count + '"class="weekday mr-1 ml-1">' +
                        '<label for="weekday-fri' + count + '">F</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="6" id="weekday-sat' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-sat' + count + '">S</label>' +
                        '<input type="checkbox" name="week_days[' + count + '][]" value="7" id="weekday-sun' + count + '" class="weekday mr-1 ml-1">' +
                        '<label for="weekday-sun' + count + '">S</label>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="input-group-btn">' +
                        '<button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i>' +
                        '<?php echo app('translator')->get("$string_file.remove"); ?>' +
                        '</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '<div class="row">' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label for="parametername"> <?php echo app('translator')->get("$string_file.name"); ?> : ' +
                        '<span class="text-danger">*</span>' +
                        '</label>' +
                        '<input type = "text" class = "form-control" id = "parametername" name = "parametername[' + count + ']" placeholder = "" autocomplete = "off">' +
                        '</div>' +
                        '</div>' +

                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label for="begintime">' +
                        '<?php echo app('translator')->get("$string_file.start_time"); ?>:' +
                        '<span class="text-danger">*</span>' +
                        '</label>' +
                        // '<div class="clock-timepicker" style="display:inline-block; position:relative">' +
                        '<input type="text" data-autoclose="true" data-plugin="clockpicker" class="form-control timepicker' + count + '" q="' + count + '" id="begintime" name="begintime[' + count + ']" onchange="EnableDisableDaySelector(this);" placeholder="" autocomplete="off" data-autocomplete-orig="off" autocapitalize="off">' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label for="endtime">' +
                        '<?php echo app('translator')->get("$string_file.end_time"); ?> :' +
                        '<span class="text-danger">*</span>' +
                        '</label>' +
                        '<input type="text" data-autoclose="true" data-plugin="clockpicker" class="form-control timepicker' + count + '" q="' + count + '" id="endtime" name="endtime[' + count + ']" onchange="EnableDisableDaySelector(this);" placeholder="" autocomplete="off" >' +
                        '<label class="radio-inline" style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">' +
                        '<input type="radio" value="1" q="' + count + '" name="optradio[' + count + ']" onclick="calculate(this);"><?php echo app('translator')->get("$string_file.next_day"); ?>' +
                        '</label>' +
                        '<label class="radio-inline"><input type="radio" value="2" id="charge_type" q="' + count + '" name="optradio[' + count + ']" onclick="calculate(this);"><?php echo app('translator')->get("$string_file.same_day"); ?>' +
                        '</label>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3">' +
                        '<div class="form-group">' +
                        '<label for="slot_charges">' +
                        '<?php echo app('translator')->get("$string_file.slot_charges"); ?> :' +
                        '<span class="text-danger">*</span>' +
                        '</label>' +
                        '<input type="text" class="form-control" id="slot_charges" name="slot_charges[' + count + ']" placeholder="" autocomplete="off" >' +
                        '<label class="radio-inline" style="margin-right: 2%;margin-left: 5%;margin-top: 3%;">' +
                        '<input type="radio" value="1" checked name="charge_type[' + count + ']"><?php echo app('translator')->get("$string_file.nominal"); ?>' +
                        '</label>' +
                        '<label class="radio-inline">' +
                        '<input type="radio" value="2" id="charge_type" name="charge_type[' + count + ']"><?php echo app('translator')->get("$string_file.multiplier"); ?> </label>' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-md-3" style="margin-top: 34px;">' +
                        '</div>' +
                        '</div>' +
                        '<hr>' +
                        '</div>';
                    $("#after-add-more").append(html);
                    $('.timepicker' + count).clockpicker({});
                    $('.timepicker' + count).clockpicker({});
                    $('[id^=slot_charges]').keypress(validateNumber);
                    $('.remove').slice(0, ($('.remove').length - 1)).attr('disabled', true);
                }
            });
            $("body").on("click", ".remove", function () {
                count--;
                document.getElementById("checkBoxCount").value = count;
                $(this).parents(".dynamic-copy").remove();
                $('.remove').slice(-1).attr('disabled', false);
            });

            $(document).ready(function () {
                $("#reset_week").click(function () {
                    $(".weekday0").prop("checked", false);
                    $("#parametername0").prop("required", false);
                    $("#slot_charges0").prop("required", false);
                    $('#parametername0').val('');
                    $('#slot_charges0').val('');

                    $("#begintime0").prop("required", false);
                    $("#endtime0").prop("required", false);
                    $('#begintime0').val('');
                    $('#endtime0').val('');
                });
            });
        });
        $('#pricecard_form').on('submit', function (e) {
            var week_validate_error = 0;
            var parameter_validate_error = 0;
            $('.weekDays-selector').each(function () {
                var index = $(this).attr('index');
                if ($(this).children('input[type=checkbox]:checked').length <= 0) {
                    week_validate_error = 1;
                }
                if ($('input[name="parameter[' + index + ']"]').val() == '' || $('input[name="begintime[' + index + ']"]').val() == '' || $('input[name="endtime[' + index + ']"]').val() == '' || $('input[name="slot_charges[' + index + ']"]').val() == '') {
                    parameter_validate_error = 1;
                }
            });

            if (week_validate_error != 0 && parameter_validate_error == 0) {
                e.preventDefault();
                alert("<?php echo app('translator')->get("$string_file.select_week_days"); ?>");
            } else if (week_validate_error == 0 && parameter_validate_error != 0) {
                e.preventDefault();
                alert("<?php echo app('translator')->get("$string_file.enter_week_parameter"); ?>");
            }
        });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/pricecard/form.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('merchant.business-segment',[$data['slug']])); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" fa fa-building" aria-hidden="true"></i>
                        <?php echo $data['title']; ?>

                    </h3>
                </div>
                <?php $id = NULL; $default_open_time = ""; $default_close_time = ""; ?>
                <?php $disabled = false; ?>
                <?php if(isset($data['business_segment']['id'])): ?>
                    <?php $id = $data['business_segment']['id']; $disabled = true ?>
                <?php endif; ?>
                <?php if($id == NULL): ?>
                    <?php $default_open_time = "00:00"; $default_close_time = "23:55"; ?>
                <?php endif; ?>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'business-segment','id'=>'business-segment-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST','autocomplete'=>'off'] ); ?>

                    <?php echo Form::hidden('id',old('id',isset($data['business_segment']['id']) ? $data['business_segment']['id'] : NULL),['id'=>'']); ?>

                    <?php echo Form::hidden('slug',$data['slug']); ?>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="firstName3">
                                    <?php echo app('translator')->get("$string_file.name"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('full_name',old('full_name',isset($data['business_segment']['full_name']) ? $data['business_segment']['full_name'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('full_name')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('full_name')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lastName3">
                                    <?php echo app('translator')->get("$string_file.email"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('email',old('email',isset($data['business_segment']['email']) ? $data['business_segment']['email'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('email')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('email')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-<?php echo !empty($id) ? 2 : 4; ?>">
                            <div class="form-group">
                                <label for="password">
                                    <?php echo app('translator')->get("$string_file.password"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::password('password',['id'=>'password','class'=>'form-control','required'=>true,"disabled"=>$disabled]); ?>

                                <?php if($errors->has('password')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('password')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-2 <?php echo !empty($id) ? "" : "custom-hidden"; ?> mt-40">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" value="1" name="edit_password" id="edit_password">
                                    <?php echo app('translator')->get("$string_file.edit_password"); ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.country"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" name="country_id"
                                        id="country_id" onchange="changeCountry()" required>
                                    <?php $__currentLoopData = $data['countries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option data-currency-code="<?php echo e($country->isoCode); ?>"
                                                value="<?php echo e($country->id); ?>"
                                                <?php if(isset($data['business_segment']['country_id']) && $data['business_segment']['country_id'] == $country->id): ?> selected <?php endif; ?>>
                                            (<?php echo e($country->phonecode); ?>) <?php echo e(isset($country->LanguageCountrySingle->name) ? $country->LanguageCountrySingle->name : $country->LanguageCountryAny->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>

                                <?php if($errors->has('country_id')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('country_id')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="firstName3">
                                    <?php echo app('translator')->get("$string_file.phone"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('phone_number',old('phone_number',isset($data['business_segment']['phone_number']) ? $data['business_segment']['phone_number'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('phone_number')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('phone_number')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.is_popular"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('is_popular',$data['is_popular'],old('is_popular',isset($data['business_segment']['is_popular']) ? $data['business_segment']['is_popular'] : NULL),['id'=>'is_popular','class'=>'form-control','required'=>true]); ?>

                            </div>
                        </div>
                        <div class="col-md-4" id="areaList">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.landmark"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('landmark',old('landmark',isset($data['business_segment']['landmark']) ? $data['business_segment']['landmark'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('landmark')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('landmark')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 corporate_inr">
                            <div class="form-group">
                                <label for="location3">
                                    <?php echo app('translator')->get("$string_file.logo"); ?>
                                    <span class="text-danger">*</span>
                                    <?php $required = true; ?>
                                    <?php if(!empty($data['business_segment']['business_logo'])): ?>
                                        <?php $required = false; ?>
                                        <a href="<?php echo e(get_image($data['business_segment']['business_logo'],'business_logo')); ?>"
                                           target="_blank">View Logo</a>
                                    <?php endif; ?>
                                </label>
                                <input style="height: 0%;" class="form-control" type="file" name="business_logo"
                                       id="business_logo" <?php echo e($required ? "required":""); ?>>
                                <?php if($errors->has('business_logo')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('business_logo')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="login_background_image">
                                    <?php echo app('translator')->get("$string_file.login_background_image"); ?> :
                                    <span class="danger">*</span>
                                </label>
                                <?php if(!empty($data['business_segment']['login_background_image'])): ?>
                                    <a href="<?php echo e(get_image($data['business_segment']['login_background_image'],'business_login_background_image')); ?>"
                                       target="_blank"><?php echo app('translator')->get("$string_file.view"); ?></a>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="login_background_image"
                                       name="login_background_image"
                                       placeholder="<?php echo app('translator')->get("$string_file.login_background_image"); ?>">
                                <br>
                                <span style="color:red;"><?php echo app('translator')->get("$string_file.login_image_warning"); ?></span>
                                <?php if($errors->has('login_background_image')): ?>
                                    <label class="danger"><?php echo e($errors->first('login_background_image')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="profile_image">
                                    <?php echo app('translator')->get("$string_file.profile_image"); ?> :
                                    <span class="danger">*</span>
                                </label>
                                <?php if(!empty($data['business_segment']['business_profile_image'])): ?>
                                    <a href="<?php echo e(get_image($data['business_segment']['business_profile_image'],'business_profile_image')); ?>"
                                       target="_blank"><?php echo app('translator')->get("$string_file.view"); ?></a>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="business_profile_image"
                                       name="business_profile_image"
                                       placeholder="<?php echo app('translator')->get("$string_file.profile_image"); ?>">
                                <br>
                                <span style="color:red;"><?php echo app('translator')->get("$string_file.profile_image_warning"); ?></span>
                                <?php if($errors->has('business_profile_image')): ?>
                                    <label class="danger"><?php echo e($errors->first('business_profile_image')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.status"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('status',$data['arr_status'],old('status',isset($data['business_segment']['status']) ? $data['business_segment']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('status')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('status')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.order_request_open_receiver"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('order_request_receiver',$data['request_receiver'],old('order_request_receiver',isset($data['business_segment']['order_request_receiver']) ? $data['business_segment']['order_request_receiver'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('order_request_receiver')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('order_request_receiver')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($data['slug'] == 'FOOD'): ?>
                            <div class="col-md-4">
                                <label for="delivery_time">
                                    <?php echo app('translator')->get("$string_file.delivery_time"); ?>
                                </label>
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="wb-time" aria-hidden="true"></i></span>
                                    <?php echo Form::number('delivery_time',old('delivery_time',isset($data['business_segment']['delivery_time']) ? $data['business_segment']['delivery_time'] : NULL),['class'=>'form-control','id'=>'delivery_time','autocomplete'=>'off','min'=>0]); ?>

                                </div>
                                <?php if($errors->has('delivery_time')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('delivery_time')); ?></label>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="minimum_amount">
                                        <?php echo app('translator')->get("$string_file.average_price_of_meal"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::number('minimum_amount',old('minimum_amount',isset( $data ['business_segment']['minimum_amount']) ? $data['business_segment']['minimum_amount'] : NULL),['id'=>'minimum_amount','class'=>'form-control','autocomplete'=>'off']); ?>

                                    <?php if($errors->has('minimum_amount')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('minimum_amount')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="form-group">
                                    <label for="minimum_amount">
                                        <?php echo app('translator')->get("$string_file.minimum_amount_for"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <?php echo Form::number('minimum_amount_for',old('minimum_amount_for',isset( $data ['business_segment']['minimum_amount_for']) ? $data['business_segment']['minimum_amount_for'] : NULL),['id'=>'minimum_amount_for','class'=>'form-control','autocomplete'=>'off','placeholder'=>""]); ?>

                                    <?php if($errors->has('minimum_amount_for')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('minimum_amount_for')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        

                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="minimum_amount">
                                    <?php echo app('translator')->get("$string_file.rating"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::number("rating",old("rating",isset($data ['business_segment']['rating']) ? $data ['business_segment']['rating'] : NULL),["step"=>"0.1", "min"=>0,"max"=>5,"class"=>"form-control", "id"=>"rating","placeholder"=>'',"required"=>true]); ?>

                                <?php if($errors->has('rating')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('rating')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="minimum_amount">
                                    <?php echo app('translator')->get("$string_file.merchant_commission_method"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select("commission_method",add_blank_option(array("1" => trans("$string_file.fixed")." ".trans("$string_file.amount"), "2" => trans("$string_file.percentage"))),old("commission_method",isset($data ['business_segment']['commission_method']) ? $data ['business_segment']['commission_method'] : NULL),["class"=>"form-control select2", "id"=>"commission_method","placeholder"=>'',"required"=>true, "onchange"=>"changeOfferType()"]); ?>

                                <?php if($errors->has('commission_method')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('commission_method')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="minimum_amount">
                                    <?php echo app('translator')->get("$string_file.merchant_commission"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="commission_method_symbol">
                                            <?php if(isset($data ['business_segment']['commission_method']) && $data ['business_segment']['commission_method'] == 1): ?>
                                                <?php echo e($data['business_segment']->CountryArea->Country->isoCode); ?>

                                            <?php elseif(isset($data ['business_segment']['commission_method']) && $data ['business_segment']['commission_method'] == 2): ?>
                                                %
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <?php echo Form::number("commission",old("commission",isset($data ['business_segment']['commission']) ? $data ['business_segment']['commission'] : NULL),["step"=>"0.01", "min"=>"0","class"=>"form-control", "id"=>"commission","placeholder"=>'',"required"=>true]); ?>

                                </div>
                                <?php if($errors->has('commission')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('commission')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(isset($driver_agency_config) && $driver_agency_config == 1): ?>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="delivery_service">
                                    <?php echo app('translator')->get("$string_file.delivery_service_method"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select("delivery_service",array("1" => trans("$string_file.merchant")." ".trans("$string_file.driver"), "2" => trans("$string_file.own_driver")),old("delivery_service",isset($data ['business_segment']['delivery_service']) ? $data ['business_segment']['delivery_service'] : NULL),["class"=>"form-control", "id"=>"delivery_service","required"=>true, "onchange"=>"getDriverAgency()"]); ?>

                                <?php if($errors->has('delivery_service')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('delivery_service')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-4 <?php if(empty($id) || isset($data['business_segment']['delivery_service']) && $data['business_segment']['delivery_service'] == 1): ?> custom-hidden <?php else: ?> <?php endif; ?>" id="driver_agencies">
                            <div class="form-group">
                                <label for="driver_agency">
                                    <?php echo app('translator')->get("$string_file.select_driver_agency"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select("driver_agency_id[]",$arr_agencies,old("driver_agency_id",$arr_agency_id),["class"=>"form-control select2", "id"=>"driver_agency_id",'multiple'=>true]); ?>

                                <?php if($errors->has('driver_agency_id')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('driver_agency_id')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="latitude">
                                    <?php echo app('translator')->get("$string_file.latitude"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('latitude',old('latitude',isset($data['business_segment']['latitude']) ? $data['business_segment']['latitude'] : NULL),['id'=>'lat','class'=>'form-control','required'=>true,'readonly' => true]); ?>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.longitude"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('longitude',old('longitude',isset($data['business_segment']['longitude']) ? $data['business_segment']['longitude'] : NULL),['id'=>'lng','class'=>'form-control','required'=>true,'readonly' => true]); ?>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="emailAddress5">
                                    <?php echo app('translator')->get("$string_file.address"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('address',old('address',isset($data['business_segment']['address']) ? $data['business_segment']['address'] : NULL),['id'=>'location','class'=>'form-control','required'=>true,'readonly' => true]); ?>

                                <?php if($errors->has('address')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('address')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="checkbox-custom checkbox-primary">
                                <input type="checkbox" id="edit_lat_long" onclick="editLatLong(this)">
                                <label for="edit_lat_long"><?php echo app('translator')->get("$string_file.edit_latitude_longitude"); ?>
                                    . </label>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <?php echo Form::label('searchInput', trans("$string_file.address"), ['class' => 'control-label']); ?>

                            <input id="searchInput" class="input-controls" type="text"
                                   placeholder="<?php echo app('translator')->get("$string_file.enter_address"); ?>">
                            <div class="map" id="map" style="width: 100%; height: 300px;"></div>
                        </div>
                        <script type="text/javascript"
                                src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyC7lIIgBajzx409vxmmY_CJPcRvDb114w4"></script>
                        
                    </div>
                    <hr>
                    <h5><?php echo app('translator')->get("$string_file.open"); ?> & <?php echo app('translator')->get("$string_file.close_time"); ?></h5>
                    <div class="row">
                        <?php
                            $arr_open_time = isset($data['business_segment']['open_time']) ? json_decode($data['business_segment']['open_time']) : NULL;
                            $arr_close_time = isset($data['business_segment']['close_time']) ? json_decode($data['business_segment']['close_time']) : NULL;


                            $opentime = "00:00";
                            $closetime = "23:55";
                        ?>
                        <?php $__currentLoopData = $data['arr_day']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day_key=> $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="wb-check"
                                                                          aria-hidden="true"></i></span>
                                        
                                        <?php echo Form::text('day_name[]',NULL,['class'=>'form-control','id'=>'time','placeholder'=>$day,'readonly'=>true]); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="wb-time" aria-hidden="true"></i></span>

                                        <?php echo Form::text('open_time['.$day_key.']',old('open_time',isset($arr_open_time[$day_key]) ? $arr_open_time[$day_key] : $opentime),['class'=>'timepicker form-control','data-plugin'=>'clockpicker','data-autoclose'=>"true",'id'=>'time','placeholder'=>'','autocomplete'=>'off']); ?>

                                    </div>
                                    <?php if($errors->has('open_time')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('open_time')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="wb-time" aria-hidden="true"></i></span>
                                        <?php echo Form::text('close_time['.$day_key.']',old('close_time',isset($arr_close_time[$day_key]) ? $arr_close_time[$day_key] : $closetime),['class'=>'timepicker form-control','data-plugin'=>'clockpicker','data-autoclose'=>"true",'id'=>'time','placeholder'=>'','autocomplete'=>'off']); ?>

                                    </div>
                                    <?php if($errors->has('close_time')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('close_time')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </div>
                    <hr>
                    <h5><?php echo app('translator')->get("$string_file.bank_details"); ?></h5>
                    <?php $arr_account_info = !empty($data['business_segment']['bank_details']) ? json_decode($data['business_segment']['bank_details'],true) : [];  ?>
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="minimum_amount">
                                    <?php echo app('translator')->get("$string_file.bank_name"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <?php echo Form::text("bank_name",old("bank_name",isset($arr_account_info['account_number']) ? $arr_account_info['bank_name'] : NULL),["class"=>"form-control", "id"=>"bank_name","placeholder"=>'',"required"=>true]); ?>

                                </div>
                                <?php if($errors->has('bank_name')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('bank_name')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="minimum_amount">
                                    <?php echo app('translator')->get("$string_file.account_holder_name"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <?php echo Form::text("account_holder_name",old("account_holder_name",isset($arr_account_info['account_holder_name']) ? $arr_account_info['account_holder_name'] : NULL),["class"=>"form-control", "id"=>"account_holder_name","placeholder"=>'',"required"=>true]); ?>

                                </div>
                                <?php if($errors->has('account_holder_name')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('account_holder_name')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="minimum_amount">
                                    <?php echo app('translator')->get("$string_file.account_number"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <?php echo Form::text("account_number",old("account_number",isset($arr_account_info['account_number']) ? $arr_account_info['account_number'] : NULL),["class"=>"form-control", "id"=>"account_number","placeholder"=>'',"required"=>true]); ?>

                                </div>
                                <?php if($errors->has('account_number')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('account_number')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="bank_code">
                                    <?php echo app('translator')->get("$string_file.bank_code"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <?php echo Form::text("bank_code",old("bank_code",isset($arr_account_info['bank_code']) ? $arr_account_info['bank_code'] : NULL),["class"=>"form-control", "id"=>"bank_code","placeholder"=>'',"required"=>true]); ?>

                                </div>
                                <?php if($errors->has('bank_code')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('bank_code')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5><?php echo app('translator')->get("$string_file.onesignal_details"); ?></h5>
                    <div class="row">
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="minimum_amount">
                                    <?php echo app('translator')->get("$string_file.application_key"); ?>
                                </label>
                                <div class="input-group">
                                    <?php echo Form::text("application_key",old("application_key",isset($onesignal_config['application_key']) ? $onesignal_config['application_key'] : NULL),["class"=>"form-control", "id"=>"application_key","placeholder"=>'',"required"=>false]); ?>

                                </div>
                                <?php if($errors->has('application_key')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('application_key')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="minimum_amount">
                                    <?php echo app('translator')->get("$string_file.rest_key"); ?>
                                </label>
                                <div class="input-group">
                                    <?php echo Form::text("rest_key",old("rest_key",isset($arr_account_info['account_holder_name']) ? $arr_account_info['account_holder_name'] : NULL),["class"=>"form-control", "id"=>"rest_key","placeholder"=>'',"required"=>false]); ?>

                                </div>
                                <?php if($errors->has('rest_key')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('rest_key')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <div class="form-group">
                                <label for="minimum_amount">
                                    <?php echo app('translator')->get("$string_file.channel_id"); ?>
                                </label>
                                <div class="input-group">
                                    <?php echo Form::text("channel_id",old("channel_id",isset($arr_account_info['account_number']) ? $arr_account_info['account_number'] : NULL),["class"=>"form-control", "id"=>"channel_id","placeholder"=>'',"required"=>false]); ?>

                                </div>
                                <?php if($errors->has('channel_id')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('channel_id')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div> -->
                    </div>
                    <br>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <?php if(!$is_demo): ?>
                        <button type="submit" class="btn btn-primary">
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
    <input type="hidden" id="currency" value="<?php echo e(isset($data ['business_segment']['commission_method']) ? $data ['business_segment']->CountryArea->Country->isoCode : ""); ?>"/>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script type="text/javascript">

        // $(document).ready(function(){
        function initialize() {
            var lat = "<?php echo e(isset($data['business_segment']['latitude']) ? $data['business_segment']['latitude'] : 28.4594965); ?>";
            var long = "<?php echo e(isset($data['business_segment']['longitude']) ? $data['business_segment']['longitude'] : 77.02663830000006); ?>";
            var latlng = new google.maps.LatLng(lat, long);
            var map = new google.maps.Map(document.getElementById('map'), {
                center: latlng,
                zoom: 19
            });
            var marker = new google.maps.Marker({
                map: map,
                position: latlng,
                draggable: true,
                anchorPoint: new google.maps.Point(0, -29)
            });
            var input = document.getElementById('searchInput');
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            var geocoder = new google.maps.Geocoder();
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);
            var infowindow = new google.maps.InfoWindow();
            autocomplete.addListener('place_changed', function () {
                infowindow.close();
                marker.setVisible(false);
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    window.alert("Autocomplete's returned place contains no geometry");
                    return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }

                marker.setPosition(place.geometry.location);
                marker.setVisible(true);

                bindDataToForm(place.formatted_address, place.geometry.location.lat(), place.geometry.location.lng());
                infowindow.setContent(place.formatted_address);
                infowindow.open(map, marker);

            });
            // this function will work on marker move event into map
            google.maps.event.addListener(marker, 'dragend', function () {
                geocoder.geocode({'latLng': marker.getPosition()}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            bindDataToForm(results[0].formatted_address, marker.getPosition().lat(), marker.getPosition().lng());
                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(map, marker);
                        }
                    }
                });
            });
        }

        function editLatLong(ss) {
            var checkValue = ss.checked ? 1 : 0;
            console.log(checkValue);
            if (checkValue == 1) {
                $('#lat').attr('readonly', false);
                $('#lng').attr('readonly', false);
                $('#location').attr('readonly', false);
            } else {
                $('#lat').attr('readonly', true);
                $('#lng').attr('readonly', true);
                $('#location').attr('readonly', true);
            }
        }

        $(document).on("click", "#edit_password", function () {
            $("#password").prop("disabled", true);
            if ($("#edit_password").prop("checked") == true) {
                $("#password").prop("disabled", false);
            }
        });

        function bindDataToForm(address, lat, lng) {
            document.getElementById('location').value = address;
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
        }

        google.maps.event.addDomListener(window, 'load', initialize);
        // })

        function changeOfferType() {
            var commission_method = $("#commission_method").val();
            if (commission_method == 2) {
                $("#commission_method_symbol").html("%");
            } else if (commission_method == 1) {
                $("#commission_method_symbol").html($("#currency").val());
            }
        }
        function getDriverAgency() {
            var delivery_service = $("#delivery_service").val();
            $("#driver_agency_id").prop("required",false);
            $("#driver_agencies").hide();
            if (delivery_service == 2) {
                $("#driver_agencies").show();
                $("#driver_agency_id").prop("required",true);
            }
        }

        function changeCountry(){
            var currency_code = $("#country_id option:selected").attr("data-currency-code");
            $("#currency").val(currency_code);
            var commission_method = $("#commission_method").val();
            if (commission_method == 1) {
                $("#commission_method_symbol").html(currency_code);
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/business-segment/form.blade.php ENDPATH**/ ?>
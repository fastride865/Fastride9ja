<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php if(Session::has('personal-document-expire-warning')): ?>
                <p class="alert alert-info"><?php echo e(Session::get('personal-document-expire-warning')); ?></p>
            <?php endif; ?>
            <?php if(Session::has('personal-document-expired-error')): ?>
                <div class="alert dark alert-icon alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon fa-warning"
                       aria-hidden="true"></i> <?php echo e(Session::get('personal-document-expired-error')); ?>

                </div>
            <?php endif; ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('driver.index')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.all_driver"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                    <?php echo $title; ?>

                </header>
                <div class="panel-body container-fluid">
                    
                    <?php $id = NULL; $required = "required"; ?>
                    <?php if(!empty($driver->id)): ?>
                        <?php $id = $driver->id; $required=""; ?>

                    <?php endif; ?>
                    <?php if(Auth::user()->demo != 1): ?>
                        <?php echo Form::open(["class"=>"steps-validation wizard-notification", "files"=>true,"url"=>route('driver.save',$id),"onsubmit"=>"return validatesignup()"]); ?>

                        <?php echo Form::hidden('id',$id,['id'=>"driver_id"]); ?>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="form-section col-md-12" style="color: #000000;"><i
                                            class="wb-user"></i> <?php echo app('translator')->get("$string_file.personal_details"); ?>
                                </h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="location3"><?php echo app('translator')->get("$string_file.country"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <?php if(empty($id) || (!empty($id) && empty($driver->country_id))): ?>
                                                <select class="form-control" name="country" id="country"
                                                        onchange="getAreaList(this)" required>
                                                    <option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>
                                                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option data-min="<?php echo e($country->minNumPhone); ?>"
                                                                data-max="<?php echo e($country->maxNumPhone); ?>"
                                                                data-ISD="<?php echo e($country->phonecode); ?>"
                                                                value="<?php echo e($country->id); ?>"
                                                                data-id="<?php echo e($country->id); ?>"><?php echo e($country->name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <?php if($errors->has('country')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('country')); ?></label>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo Form::text('county_name',$driver->Country->CountryName,['class'=>'form-control','disabled'=>true]); ?>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="location3"><?php echo app('translator')->get("$string_file.service_area"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <?php if(empty($id) || (!empty($id) && empty($driver->country_area_id))): ?>
                                                <!--<select class="form-control" name="area" id="area">-->
                                                <!--    <option value="">--<?php echo app('translator')->get("$string_file.select"); ?>--</option>-->
                                                <!--</select>-->
                                                <?php echo Form::select("area",$areas,null,["class" => "form-control", "id" => "area", "required" => true]); ?>

                                                <?php if($errors->has('area')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('area')); ?></label>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo Form::text('county_area_name',$driver->CountryArea->CountryAreaName,['class'=>'form-control','disabled'=>true]); ?>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="user_phone"><?php echo app('translator')->get("$string_file.phone"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="row">
                                                <input type="text"
                                                       class="form-control col-md-3 ml-15 col-sm-3 col-3" id="isd"
                                                       name="isd"
                                                       value="<?php echo e(old('isd',isset($driver->Country) ? $driver->Country->phonecode : NULL)); ?>"
                                                       placeholder="<?php echo app('translator')->get("$string_file.isd_code"); ?>" readonly/>

                                                <input type="number" class="form-control col-md-8 col-sm-8 col-8"
                                                       id="user_phone" name="phone"
                                                       value="<?php echo e(old('phone',old('phone',!empty($driver->country_id) ? str_replace($driver->Country->phonecode,"",$driver->phoneNumber): ""))); ?>"
                                                       placeholder="" required/>
                                            </div>
                                            <?php if($errors->has('phonecode')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('phonecode')); ?></label>
                                            <?php endif; ?>
                                            <?php if($errors->has('phone')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('phone')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="first_name"> <?php echo app('translator')->get("$string_file.first_name"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="first_name"
                                                   name="first_name"
                                                   value="<?php echo e(old('first_name',isset($driver->first_name) ? $driver->first_name : NULL)); ?>"
                                                   placeholder=" <?php echo app('translator')->get("$string_file.first_name"); ?>" required
                                                   autocomplete="off"/>
                                        </div>
                                        <?php if($errors->has('first_name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('first_name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="firstName3"><?php echo app('translator')->get("$string_file.last_name"); ?>
                                                
                                            </label>
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                   value="<?php echo e(old('last_name',isset($driver->last_name) ? $driver->last_name : NULL)); ?>"
                                                   placeholder="<?php echo app('translator')->get("$string_file.last_name"); ?>"
                                                   autocomplete="off"/>
                                        </div>
                                        <?php if($errors->has('last_name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('last_name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="emailAddress5"><?php echo app('translator')->get("$string_file.profile_image"); ?>
                                                <span class="text-danger">*</span>
                                                <?php if(!empty($driver->profile_image)): ?>
                                                    <a href="<?php echo e(get_image($driver->profile_image,'driver',$driver->merchant_id)); ?>"
                                                       target="_blank"><?php echo app('translator')->get("$string_file.view"); ?></a>
                                                <?php endif; ?>
                                            </label>
                                            <input type="file" class="form-control" id="image" name="image"
                                                    <?php echo e($required); ?>/>
                                            <?php if($errors->has('image')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('image')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label" for="email"><?php echo app('translator')->get("$string_file.email"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                   placeholder="" autocomplete="off"
                                                   value="<?php echo e(old('email',isset($driver->email) ? $driver->email : NULL)); ?>"
                                                   <?php if($config->driver_email_enable == 1): ?> required <?php endif; ?>/>
                                            <input type="hidden" name="driver_email_enable" value="<?php if($config->driver_email_enable == 1): ?> true <?php else: ?> false <?php endif; ?>">
                                        </div>
                                        <?php if($errors->has('email')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('email')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="emailAddress5"><?php echo app('translator')->get("$string_file.password"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" class="form-control" id="password"
                                                   name="password"
                                                   placeholder="" autocomplete="off"
                                                    <?php echo e($required); ?>/>
                                        </div>
                                        <?php if($errors->has('password')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('password')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="location"><?php echo e(trans("$string_file.confirm_password")); ?>

                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                   name="password_confirmation"
                                                   placeholder="" autocomplete="off"
                                                    <?php echo e($required); ?>/>
                                        </div>
                                    </div>
                                    <div class="col-md-4 <?php if(isset($group['single_group']) && $group['single_group'] == 1): ?> custom-hidden <?php endif; ?>">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="driver_gender"><?php echo app('translator')->get("$string_file.segment_group"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <?php if(empty($id) || (!empty($driver) && $driver->segment_group_id == NULL)): ?>
                                                <?php echo e(Form::select('segment_group_id',$group['arr_group'],old('segment_group_id'),['class'=>'form-control','id'=>'segment_group_id','required'=>true])); ?>

                                                <?php if($errors->has('segment_group_id')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('segment_group_id')); ?></label>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php echo Form::text('segment_group',$group['arr_group'][$driver->segment_group_id],['class'=>'form-control','disabled'=>true]); ?>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if($config->gender == 1): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="driver_gender"><?php echo app('translator')->get("$string_file.gender"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" name="driver_gender" id="driver_gender"
                                                        required>
                                                    <option value="1"
                                                            <?php if(!empty($driver->driver_gender) && $driver->driver_gender == 1): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.male"); ?></option>
                                                    <option value="2"
                                                            <?php if(!empty($driver->driver_gender) && $driver->driver_gender == 2): ?> selected <?php endif; ?>><?php echo app('translator')->get("$string_file.female"); ?></option>
                                                </select>
                                                <?php if($errors->has('driver_gender')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('driver_gender')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>














                                    <?php if($config->stripe_connect_enable == 1): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label">
                                                    <?php echo app('translator')->get("$string_file.dob"); ?> <span class="text-danger">*</span>
                                                </label>
                                                <input type="text"
                                                       class="form-control customDatePicker1"
                                                       name="dob"
                                                       value="<?php echo e(old('dob',isset($driver->dob) ? $driver->dob : NULL)); ?>"
                                                       placeholder=""
                                                       required
                                                       autocomplete="off">
                                                <span class="text-danger"><?php echo e($errors->first('dob')); ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($config->smoker == 1): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="location3"> <?php echo app('translator')->get("$string_file.smoke"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" name="smoker_type" id="smoker_type"
                                                        required>
                                                    <option value="1"
                                                            <?php if(!empty($driver->DriverRideConfig) && $driver->DriverRideConfig->smoker_type == 1): ?>
                                                            selected="selected" <?php endif; ?>> <?php echo app('translator')->get("$string_file.smoker"); ?></option>
                                                    <option value="2"
                                                            <?php if(!empty($driver->DriverRideConfig) && $driver->DriverRideConfig->smoker_type == 2): ?>
                                                            selected="selected" <?php endif; ?>> <?php echo app('translator')->get("$string_file.non_smoker"); ?></option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-control-label"></label>
                                            <div class="checkbox-inline"
                                                 style="margin-left: 5%;margin-top: 1%;">
                                                <input type="checkbox" name="allow_other_smoker" value="1"
                                                       id="allow_other_smoker"
                                                       <?php if(!empty($driver->DriverRideConfig) && $driver->DriverRideConfig->allow_other_smoker == 1): ?> checked
                                                        <?php endif; ?>>
                                                <label> <?php echo app('translator')->get("$string_file.allow_other_to_smoke"); ?></label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <br>
                                <h5 class="form-section col-md-12" style="color: black;">
                                    <i class="fa fa-address-card"></i> <?php echo app('translator')->get("$string_file.address"); ?>
                                </h5>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="location3"> <?php echo app('translator')->get("$string_file.address_line_1"); ?>

                                            </label>
                                            <input type="text" class="form-control" id="address_line_1"
                                                   name="address_line_1"
                                                   value="<?php echo e(old('address_line_1',isset($driver_additional_data->address_line_1) ? $driver_additional_data->address_line_1 : '')); ?>"
                                                   placeholder=""
                                                   autocomplete="off"/>
                                        </div>
                                        <?php if($errors->has('address_line_1')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('address_line_1')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($config->stripe_connect_enable == 1): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"><?php echo app('translator')->get("$string_file.address_line_2"); ?>
                                                </label>
                                                <input type="text" class="form-control" id="address_line_2"
                                                       name="address_line_2"
                                                       value="<?php echo e(old('address_line_2',isset($driver_additional_data->address_line_2) ? $driver_additional_data->address_line_2 : '')); ?>"
                                                       placeholder=""
                                                       autocomplete="off"/>
                                            </div>
                                            <?php if($errors->has('address_line_2')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('address_line_2')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"><?php echo app('translator')->get("$string_file.city_name"); ?>

                                            </label>
                                            <input type="text" class="form-control" id="city_name"
                                                   name="city_name"
                                                   value="<?php echo e(old('city_name', isset($driver_additional_data->city_name) ? $driver_additional_data->city_name : '')); ?>"
                                                   placeholder=""
                                                   autocomplete="off"/>
                                        </div>
                                        <?php if($errors->has('city_name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('city_name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($config->stripe_connect_enable == 1): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="location3"><?php echo app('translator')->get("$string_file.address_suburb"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="address_suburb"
                                                       name="address_suburb"
                                                       value="<?php echo e(old('address_suburb', isset($driver_additional_data->suburb) ? $driver_additional_data->suburb : '')); ?>"
                                                       placeholder="" required
                                                       autocomplete="off"/>
                                            </div>
                                            <?php if($errors->has('address_suburb')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('address_suburb')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($config->stripe_connect_enable == 1): ?>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="location3"><?php echo app('translator')->get("$string_file.address_province"); ?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="address_province"
                                                       name="address_province"
                                                       value="<?php echo e(old('address_province', isset($driver_additional_data->province) ? $driver_additional_data->province : '')); ?>"
                                                       placeholder="" required
                                                       autocomplete="off"
                                                />
                                            </div>
                                            <?php if($errors->has('address_province')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('address_province')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label"
                                                   for="location3"><?php echo app('translator')->get("$string_file.postal_code"); ?>

                                            </label>
                                            <input type="text" class="form-control" id="address_postal_code"
                                                   name="address_postal_code"
                                                   value="<?php echo e(old('address_postal_code',isset($driver_additional_data->pincode) ? $driver_additional_data->pincode : '')); ?>"
                                                   placeholder=""
                                                   autocomplete="off"/>
                                        </div>
                                        <?php if($errors->has('address_postal_code')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('address_postal_code')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($config->paystack_split_payment_enable == 1): ?>
                                    <br>
                                    <h5 class="form-section col-md-12" style="color: black;"><i
                                                class="fa fa-bank"></i> <?php echo app('translator')->get("$string_file.paystack_registration"); ?>
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="lastName3"><?php echo app('translator')->get("$string_file.bank_name"); ?>
                                                </label>
                                                <select class="form-control" id="bank_name" <?php if(isset($driver->paystack_account_status) && $driver->paystack_account_status == "active"): ?> readonly <?php endif; ?>
                                                        name="bank_name" required >
                                                    <option><?php echo app('translator')->get("$string_file.select"); ?></option>
                                                    <?php $__currentLoopData = $paystack_bank_codes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paystack_bank_code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($paystack_bank_code['name']); ?>" bank-code="<?php echo e($paystack_bank_code['code']); ?>" <?php if(!empty($driver->online_code) && $driver->online_code == $paystack_bank_code['code']): ?> selected <?php endif; ?>><?php echo e($paystack_bank_code['name']); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <input type="hidden" id="bank_code" name="bank_code" value="<?php echo e(isset($driver->online_code) ? $driver->online_code : ""); ?>" />
                                            </div>
                                            <?php if($errors->has('bank_name')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('bank_name')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="lastName3"><?php echo app('translator')->get("$string_file.account_holder_name"); ?>
                                                </label>
                                                <input type="text" class="form-control" id="account_holder_name"
                                                       name="account_holder_name"
                                                       <?php if(isset($driver->paystack_account_status) && $driver->paystack_account_status == "active"): ?> readonly <?php endif; ?>
                                                       value="<?php echo e(old('account_holder_name',isset($driver->account_holder_name) ? $driver->account_holder_name : NULL)); ?>"
                                                       placeholder="" required
                                                       autocomplete="off"/>
                                            </div>
                                            <?php if($errors->has('account_holder_name')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('account_holder_name')); ?></label>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="lastName3"><?php echo app('translator')->get("$string_file.account_number"); ?>
                                                </label>
                                                <input type="text" class="form-control" id="account_number"
                                                       name="account_number" required
                                                       <?php if(isset($driver->paystack_account_status) && $driver->paystack_account_status == "active"): ?> readonly <?php endif; ?>
                                                       value="<?php echo e(old('account_number',isset($driver->account_number) ? $driver->account_number : NULL)); ?>"
                                                       placeholder="<?php echo app('translator')->get("$string_file.account_number"); ?>"
                                                       autocomplete="off"/>
                                            </div>
                                            <?php if($errors->has('account_number')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('account_number')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif($config->bank_details == 1): ?>
                                    <br>
                                    <h5 class="form-section col-md-12" style="color: black;"><i
                                                class="fa fa-bank"></i> <?php echo app('translator')->get("$string_file.bank_details"); ?>
                                    </h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="lastName3"><?php echo app('translator')->get("$string_file.bank_name"); ?>
                                                </label>
                                                <input type="text" class="form-control" id="bank_name"
                                                       name="bank_name"
                                                       value="<?php echo e(old('bank_name',isset($driver->bank_name) ? $driver->bank_name : NULL)); ?>"
                                                       placeholder="Your bank name"
                                                       autocomplete="off"/>
                                            </div>
                                            <?php if($errors->has('bank_name')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('bank_name')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="lastName3"><?php echo app('translator')->get("$string_file.account_holder_name"); ?>
                                                </label>
                                                <input type="text" class="form-control" id="account_holder_name"
                                                       name="account_holder_name"
                                                       value="<?php echo e(old('account_holder_name',isset($driver->account_holder_name) ? $driver->account_holder_name : NULL)); ?>"
                                                       placeholder=""
                                                       autocomplete="off"/>
                                            </div>
                                            <?php if($errors->has('account_holder_name')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('account_holder_name')); ?></label>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-control-label"
                                                       for="lastName3"><?php echo app('translator')->get("$string_file.account_number"); ?>
                                                </label>
                                                <input type="text" class="form-control" id="account_number"
                                                       name="account_number"
                                                       value="<?php echo e(old('account_number',isset($driver->account_number) ? $driver->account_number : NULL)); ?>"
                                                       placeholder="<?php echo app('translator')->get("$string_file.account_number"); ?>"
                                                       autocomplete="off"/>
                                            </div>
                                            <?php if($errors->has('account_number')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('account_number')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($config->stripe_connect_enable == 1): ?>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label"
                                                           for="lastName3"><?php echo app('translator')->get("$string_file.bsb_routing_number"); ?>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="bsb_routing_number"
                                                           name="bsb_routing_number"
                                                           value="<?php echo e(old('bsb_routing_number',isset($driver->routing_number) ? $driver->routing_number : NULL)); ?>"
                                                           placeholder=""
                                                           autocomplete="off" required/>
                                                </div>
                                                <?php if($errors->has('bsb_routing_number')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('bsb_routing_number')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label"
                                                           for="lastName3"><?php echo app('translator')->get("$string_file.abn_number"); ?>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" class="form-control" id="abn_number"
                                                           name="abn_number"
                                                           value="<?php echo e(old('abn_number',isset($driver->abn_number) ? $driver->abn_number : '')); ?>"
                                                           placeholder=""
                                                           autocomplete="off"
                                                           required/>
                                                </div>
                                                <?php if($errors->has('abn_number')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('abn_number')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label"
                                                           for="location3"><div id="transaction_label"><?php echo app('translator')->get("$string_file.online_transaction_code"); ?></div>
                                                    </label>
                                                    <input type="text" class="form-control" id="online_transaction"
                                                           name="online_transaction"
                                                           value="<?php echo e(old('online_transaction',isset($driver->online_code) ? $driver->online_code : NULL)); ?>"
                                                           placeholder="<?php echo app('translator')->get("$string_file.online_transaction_code"); ?>"
                                                           autocomplete="off"/>
                                                </div>
                                                <?php if($errors->has('online_transaction')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('online_transaction')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="form-control-label"
                                                           for="location3"><?php echo app('translator')->get("$string_file.account_type"); ?>
                                                    </label>
                                                    <select type="text" class="form-control" name="account_types"
                                                            id="account_types">
                                                        <?php $__currentLoopData = $account_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($account_type->id); ?>"><?php if($account_type->LangAccountTypeSingle): ?><?php echo e($account_type->LangAccountTypeSingle->name); ?>

                                                                <?php else: ?> <?php echo e($account_type->LangAccountTypeAny->name); ?> <?php endif; ?>
                                                            </option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                    <?php if($errors->has('account_types')): ?>
                                                        <label class="text-danger"><?php echo e($errors->first('account_types')); ?></label>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <br>
                        </div>

                        <div id="personal-document">
                            <?php echo $personal_document; ?>

                        </div>
                        <div class="form-actions float-right" style="margin-bottom: 1%">
                            <button type="submit" class="btn btn-primary"><i
                                        class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?> & <?php echo app('translator')->get("$string_file.proceed"); ?>
                            </button>
                        </div>
                        <?php echo Form::close(); ?>

                    <?php else: ?>
                        <div class="alert dark alert-icon alert-danger" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <i class="icon fa-warning" aria-hidden="true"></i> <?php echo app('translator')->get("$string_file.demo_user_cant_edited"); ?>.
                        </div>
                        
                        
                        
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $(document).on('change', '#country', function () {
            var id = this.options[this.selectedIndex].getAttribute('data-id');
            $.ajax({
                method: 'GET',
                url: "<?php echo e(route('merchant.country.config')); ?>",
                data: {id: id},
                success: function (data) {
                    $('#transaction_label').html(data);
                    $('#online_transaction').attr('placeholder', 'Enter ' + data);
                }
            });
        });

        function getAreaList(obj) {
            var id = obj.options[obj.selectedIndex].getAttribute('data-id');
            $.ajax({
                method: 'GET',
                url: "<?php echo e(route('merchant.country.arealist')); ?>",
                data: {country_id: id},
                success: function (data) {
                    $('#area').html(data);
                }
            });
        }

        function validatesignup() {
            var driver_id = $('#driver_id').val();
            if (driver_id == "") {
                var password = document.getElementById('password').value;
                var con_password = document.getElementById('password_confirmation').value;
                if (password == "") {
                    alert("Please enter password");
                    return false;
                }
                if (con_password == "") {
                    alert("Please enter confirm password");
                    return false;
                }
                if (con_password != password) {
                    alert("Password and Confirm password must be same");
                    return false;
                }
            }
        }

        $(document).on('change', '#area', function (e) {
            var area_id = $("#area option:selected").val();
            if (area_id != "") {
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo e(route('merchant.driver.country-area-document')); ?>",
                    data: {area_id: area_id},
                    success: function (data) {
                        $('#personal-document').html(data);
                        var dateToday = new Date();
                        $('.customDatePicker1').datepicker({
                            format: 'yyyy-mm-dd',
                            startDate: dateToday,
                            onRender: function (date) {
                                return date.valueOf() < now.valueOf() ? 'disabled' : '';
                            }
                        });
                    }
                });
            }
        });

        $(document).on('change','#bank_name',function(e){
            var bank_code = $('option:selected', this).attr('bank-code');
            $("#bank_code").val(bank_code);
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/create.blade.php ENDPATH**/ ?>
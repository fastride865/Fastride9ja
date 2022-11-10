<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php if(Session::has('vehicle-document-expire-warning')): ?>
                <p class="alert alert-info"><?php echo e(Session::get('vehicle-document-expire-warning')); ?></p>
            <?php endif; ?>
            <?php if(Session::has('vehicle-document-expired-error')): ?>
                <div class="alert dark alert-icon alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <i class="icon fa-warning" aria-hidden="true"></i> <?php echo e(Session::get('vehicle-document-expired-error')); ?>

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
                        <a href="<?php echo e(route('driver.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.add_vehicle"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          onSubmit="return validateForm();"
                          action="<?php echo e(route('merchant.driver.vehicle.store',$driver->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo Form::hidden('vehicle_model_expire',$vehicle_model_expire,['class'=>'']); ?>

                        <?php
                            $vehicle_image = NULL;
                            $plate_image = NULL;
                            $vehicle_model_id = NULL;
                            $vehicle_id = NULL;
                            $registration_date = NULL;
                            $expire_date = NULL;
                            $yes_no = ["0"=>trans("$string_file.no"),"1"=>trans("$string_file.yes")];
                        ?>
                        <?php if(!empty($vehicle_details)): ?>
                            <?php
                                $vehicle_id = $vehicle_details['id'];
                                $vehicle_image = $vehicle_details['vehicle_image'];
                                $registration_date = $vehicle_details['vehicle_register_date'];
                                $expire_date = $vehicle_details['vehicle_expire_date'];
                                $plate_image = $vehicle_details['vehicle_number_plate_image'];
                            ?>
                        <?php endif; ?>
                        <?php echo Form::hidden('vehicle_id',$vehicle_id,['id'=>"vehicle_id"]); ?>

                        <?php echo Form::hidden('request_from',$request_from,['id'=>"request_from"]); ?>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3"><?php echo app('translator')->get("$string_file.vehicle_type"); ?> <span class="text-danger">*</span>
                                        :</label>
                                    <?php if(empty($vehicle_id)): ?>
                                    <select class="form-control required"
                                            name="vehicle_type_id"
                                            id="vehicle_type_id"
                                            required>
                                        <option value=""><?php echo app('translator')->get("$string_file.vehicle_type"); ?></option>
                                        <?php $__currentLoopData = $vehicletypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($vehicle->id); ?>" <?php echo isset($vehicle_details['vehicle_type_id']) && $vehicle_details['vehicle_type_id'] == $vehicle->id ? "selected" : NULL; ?>><?php echo e($vehicle->VehicleTypeName); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($errors->has('vehicle_type_id')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('vehicle_type_id')); ?></label>
                                    <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo Form::text('vehicle_type',$vehicle_details->VehicleType->vehicleTypeName,['class'=>'form-control','disabled'=>true]); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($appConfig->vehicle_make_text == 1 && empty($vehicle_id)): ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.vehicle_make"); ?> <span class="text-danger">*</span>
                                            :</label>
                                        <input type="text" class="form-control" name="vehicle_make_id"
                                               id="vehicle_make_id" required>
                                        <?php if($errors->has('vehicle_make_id')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('vehicle_make_id')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            <?php else: ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.vehicle_make"); ?> <span class="text-danger">*</span>
                                            :</label>
                                        <?php if(empty($vehicle_id)): ?>
                                        <select class="form-control required"
                                                name="vehicle_make_id"
                                                onchange="return vehicleModel(this.value)"
                                                id="vehicle_make_id"
                                                required>
                                            <option value="">--<?php echo app('translator')->get("$string_file.select"); ?>--</option>
                                            <?php $__currentLoopData = $vehiclemakes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehiclemake): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($vehiclemake->id); ?>" <?php echo isset($vehicle_details['vehicle_make_id']) && $vehicle_details['vehicle_make_id'] == $vehiclemake->id ? "selected" : NULL; ?>><?php echo e($vehiclemake->VehicleMakeName); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <?php if($errors->has('vehicle_make_id')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('vehicle_make_id')); ?></label>
                                        <?php endif; ?>
                                        <?php else: ?>
                                            <?php echo Form::text('vehicle_make',$vehicle_details->VehicleMake->vehicleMakeName,['class'=>'form-control','disabled'=>true]); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if($appConfig->vehicle_model_text == 1 && empty($vehicle_id)): ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.vehicle_model"); ?> <span class="text-danger">*</span>
                                            :</label>
                                        <input class="form-control" type="text" name="vehicle_model_id"
                                               id="vehicle_model_id" required>
                                        <?php if($errors->has('vehicle_make_id')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('vehicle_make_id')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                    
                                        
                                            
                                        
                                               
                                        
                                            
                                        
                                    
                                
                            <?php else: ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.vehicle_model"); ?> <span class="text-danger">*</span>
                                            :</label>
                                        <?php if(empty($vehicle_id)): ?>
                                        <select class="form-control required"
                                                name="vehicle_model_id"
                                                id="vehicle_model_id"
                                                required>
                                            <option value="">--<?php echo app('translator')->get("$string_file.select"); ?>--</option>
                                        </select>
                                        <?php if($errors->has('vehicle_make_id')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('vehicle_make_id')); ?></label>
                                        <?php endif; ?>
                                        <?php else: ?>
                                            <?php echo Form::text('vehicle_model',$vehicle_details->VehicleModel->vehicleModelName,['class'=>'form-control','disabled'=>true]); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3"><?php echo app('translator')->get("$string_file.vehicle_number"); ?> <span class="text-danger">*</span>
                                        :</label>
                                    <input type="text" class="form-control"
                                           id="vehicle_number"
                                           name="vehicle_number"
                                           placeholder="<?php echo app('translator')->get("$string_file.vehicle_number"); ?> "
                                           value="<?php echo isset($vehicle_details['vehicle_number']) ? $vehicle_details['vehicle_number'] : NULL; ?>" pattern="^[a-zA-Z0-9]+$" title="<?php echo app('translator')->get("$string_file.value_should_contain_alpha_numeric"); ?>"
                                           required>
                                    <?php if($errors->has('vehicle_number')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('vehicle_number')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3"><?php echo app('translator')->get("$string_file.vehicle_image"); ?> <span class="text-danger">*</span>
                                        :</label>
                                    <?php if(!empty($vehicle_image)): ?>
                                        <a href="<?php echo e(get_image($vehicle_image,'vehicle_document')); ?>"
                                           target="_blank"><?php echo app('translator')->get("$string_file.view"); ?> </a>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="car_image"
                                           name="car_image" <?php echo empty($vehicle_image) ? "required" : ''; ?>>
                                    <?php if($errors->has('car_image')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('car_image')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3"><?php echo app('translator')->get("$string_file.number_plate"); ?>
                                        :</label>
                                    <?php if(!empty($plate_image)): ?>
                                        <a href="<?php echo e(get_image($plate_image,'vehicle_document')); ?>"
                                           target="_blank"><?php echo app('translator')->get("$string_file.view"); ?> </a>
                                    <?php endif; ?>
                                    <input type="file" class="form-control"
                                           id="car_number_plate_image"
                                           name="car_number_plate_image">
                                    <?php if($errors->has('car_number_plate_image')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('car_number_plate_image')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3"><?php echo app('translator')->get("$string_file.vehicle_color"); ?>  <span
                                                class="text-danger">*</span> :</label>
                                    <input type="text" class="form-control"
                                           id="vehicle_color"
                                           name="vehicle_color"
                                           value="<?php echo isset($vehicle_details['vehicle_color']) ? $vehicle_details['vehicle_color'] : NULL; ?>"
                                           placeholder="<?php echo app('translator')->get("$string_file.vehicle"); ?>  <?php echo app('translator')->get("$string_file.color"); ?> "
                                           required>
                                    <?php if($errors->has('vehicle_color')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('vehicle_color')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($vehicle_model_expire == 1): ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3"><?php echo app('translator')->get("$string_file.registered_date"); ?>  <span class="text-danger">*</span>
                                        :</label>
                                    <input type="text"
                                           class="form-control customDatePicker2"
                                           name="vehicle_register_date"
                                           value="<?php echo e(old('vehicle_register_date',$registration_date)); ?>"
                                           placeholder="<?php echo app('translator')->get("$string_file.vehicle_registered_date"); ?>  "
                                          required autocomplete="off">
                                    <?php if($errors->has('vehicle_register_date')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('vehicle_register_date')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location3"><?php echo app('translator')->get("$string_file.expire_date"); ?>  <span class="text-danger">*</span>
                                        :</label>
                                    <input type="text"
                                           class="form-control customDatePicker1"
                                           name="vehicle_expire_date"
                                           value="<?php echo e(old('vehicle_expire_date',$expire_date)); ?>"
                                           placeholder=""
                                           required
                                           autocomplete="off">
                                    <?php if($errors->has('vehicle_expire_date')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('vehicle_expire_date')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(!empty($baby_seat_enable)): ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="baby_seat"><?php echo app('translator')->get("$string_file.baby_seat_enable"); ?> :</label>
                                        <?php echo Form::select('baby_seat',$yes_no,old('baby_seat',isset($vehicle_details['baby_seat']) ? $vehicle_details['baby_seat'] : 0),['class'=>'form-control','id'=>'baby_seat']); ?>

                                        <?php if($errors->has('baby_seat')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('baby_seat')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(!empty($wheel_chair_enable)): ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="wheel_chair"><?php echo app('translator')->get("$string_file.wheel_chair_enable"); ?> :</label>
                                        <?php echo Form::select('wheel_chair',$yes_no,old('wheel_chair',isset($vehicle_details['wheel_chair']) ? $vehicle_details['wheel_chair'] : 0),['class'=>'form-control','id'=>'wheel_chair']); ?>

                                        <?php if($errors->has('wheel_chair')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('wheel_chair')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(!empty($vehicle_ac_enable)): ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ac_nonac"><?php echo app('translator')->get("$string_file.ac_enable"); ?> :</label>
                                        <?php echo Form::select('ac_nonac',$yes_no,old('ac_nonac',isset($vehicle_details['ac_nonac']) ? $vehicle_details['ac_nonac'] : 0),['class'=>'form-control','id'=>'ac_nonac']); ?>

                                        <?php if($errors->has('ac_nonac')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('ac_nonac')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div id="vehicle-doc-segment">
                            <?php echo $vehicle_doc_segment; ?>

                        </div>

                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $(document).on('change','#vehicle_type_id',function(e){
            var vehicle_type  = $("#vehicle_type_id option:selected").val();
            var driver = <?php echo $driver->id; ?>;
            if (driver != "") {
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo e(route('ajax.services')); ?>",
                    data: {driver_id: driver, vehicle: vehicle_type},
                    success: function (data) {
                        $('#vehicle-doc-segment').html(data);
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

        function vehicleModel() {
            var vehicle_type_id = document.getElementById('vehicle_type_id').value;
            var vehicle_make_id = document.getElementById('vehicle_make_id').value;
            if (vehicle_type_id == "") {
                alert("Select Vehicle Type");
                var vehicle_make_index = document.getElementById('vehicle_make_id');
                vehicle_make_index.selectedIndex = 0;
                return false;
            } else {
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: "<?php echo e(route('ajax.vehiclemodel')); ?>",
                    data: {vehicle_type_id: vehicle_type_id, vehicle_make_id: vehicle_make_id},
                    success: function (data) {
                        $('#vehicle_model_id').html(data);
                    }
                });
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/driver/create_vehicle.blade.php ENDPATH**/ ?>
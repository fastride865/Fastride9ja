<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
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
                    </div>
                    <h3 class="panel-title"><i class="fa fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.all_vehicles"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>"",'url'=>route('merchant.driver.allvehicles'),'method'=>"GET"]); ?>

                    <div class="table_search row">
                        <?php $vehicletype = NULL; $vehicle_number = "";$searched_param = NULL; $searched_area = NULL; $searched_text = ""; ?>
                        <?php if(!empty($arr_search)): ?>
                            <?php $vehicletype = isset($arr_search['vehicletype']) ? $arr_search['vehicletype'] : NULL ;
                             $searched_param = isset($arr_search['parameter']) ? $arr_search['parameter'] : NULL;
                             $searched_area = isset($arr_search['area_id']) ? $arr_search['area_id'] : NULL;
                             $searched_text = isset($arr_search['keyword']) ? $arr_search['keyword'] : "";
                             $vehicle_number = isset($arr_search['vehicleNumber']) ? $arr_search['vehicleNumber'] : ""; ?>
                        <?php endif; ?>
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="">
                                <select class="form-control" name="parameter" id="parameter">
                                    <option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>
                                    <option value="1" <?php echo e($searched_param == 1 ? "selected" : ""); ?>><?php echo app('translator')->get("$string_file.name"); ?></option>
                                    <option value="2" <?php echo e($searched_param == 2 ? "selected" : ""); ?>><?php echo app('translator')->get("$string_file.email"); ?></option>
                                    <option value="3" <?php echo e($searched_param == 3 ? "selected" : ""); ?>><?php echo app('translator')->get("$string_file.phone"); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="input-group">
                                <input type="text" id="" name="keyword" value="<?php echo e($searched_text); ?>"
                                       placeholder="<?php echo app('translator')->get("$string_file.enter_text"); ?>"
                                       class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="">
                                <?php echo Form::select('area_id',add_blank_option($areas,trans("$string_file.area")),$searched_area,['class'=>'form-control select2','id'=>'area_id']); ?>

                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="">
                                <select class="form-control" name="vehicletype" id="vehicletype">
                                    <option value="">--<?php echo app('translator')->get("$string_file.vehicle_type"); ?>--
                                    </option>
                                    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($vehicle->id); ?>"
                                                <?php if($vehicletype == $vehicle->id): ?> selected <?php endif; ?>><?php echo e($vehicle->VehicleTypeName); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12 form-group active-margin-top">
                            <div class="input-group">
                                <input type="text" id="" name="vehicleNumber" value="<?php echo e($vehicle_number); ?>"
                                       placeholder="<?php echo app('translator')->get("$string_file.vehicle_number"); ?> "
                                       class="form-control col-md-12 col-xs-12">
                            </div>
                        </div>
                        <div class="col-sm-2  col-xs-12 form-group active-margin-top">
                            <button class="btn btn-primary" type="submit"><i
                                        class="fa fa-search" aria-hidden="true"></i>
                            </button>
                            <a href="<?php echo e(route('merchant.driver.allvehicles')); ?>">
                                <button class="btn btn-success" type="button"><i class="fa fa-refresh"
                                                                                 aria-hidden="true"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <?php echo Form::close(); ?>

                    
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_type"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.services"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_number"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_id"); ?> </th>
                            <?php if($vehicle_model_expire == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.registered_date"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.expire_date"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.color"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.image"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.number_plate"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?> </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $driver_vehicles->firstItem() ?>
                        <?php $__currentLoopData = $driver_vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $value->DriverVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>
                                    
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <td><span class="long_text">
                                                            <?php echo e("********".substr($value->last_name,-2)); ?>

                                                            <br>
                                                             <?php echo e("********".substr($value->phoneNumber,-2)); ?>

                                                            <br>
                                                            <?php echo e("********".substr($value->email,-2)); ?>

                                                            </span>
                                        </td>
                                    <?php else: ?>
                                        <td><span class="long_text">
                                                            <?php echo e($value->first_name." ".$value->last_name); ?>

                                                            <br>
                                                            <?php echo e($value->phoneNumber); ?>

                                                            <br>
                                                            <?php echo e($value->email); ?>

                                                            </span>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php echo e($vehicle->VehicleType->VehicleTypeName); ?>

                                    </td>
                                    
                                    <?php $a = array(); ?>
                                    <?php $__currentLoopData = $vehicle->ServiceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servicetypes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $a[] = $servicetypes->serviceName; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td>
                                        <?php $__currentLoopData = $a; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($service); ?><br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($vehicle->vehicle_number); ?>

                                    </td>
                                    <td class="text-center">
                                        <?php echo e($vehicle->shareCode); ?>

                                    </td>
                                    <?php if($vehicle_model_expire == 1): ?>
                                        <td>
                                            <?php echo convertTimeToUSERzone($vehicle->vehicle_register_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2); ?>

                                        </td>
                                        <td>
                                            <?php echo convertTimeToUSERzone($vehicle->vehicle_expire_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2); ?>

                                        </td>
                                    <?php endif; ?>
                                    <td class="text-center">
                                        <?php echo e($vehicle->vehicle_color); ?>

                                    </td>
                                    <td class="text-center">
                                        <a target="_blank"
                                           href="<?php echo e(get_image($vehicle->vehicle_image,'vehicle_document')); ?>">
                                            <img src="<?php echo e(get_image($vehicle->vehicle_image,'vehicle_document')); ?>"
                                                 alt="avatar"
                                                 style="width: 80px;height: 80px;border-radius:10px;">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a target="_blank"
                                           href="<?php echo e(get_image($vehicle->vehicle_number_plate_image,'vehicle_document')); ?>">
                                            <img src="<?php echo e(get_image($vehicle->vehicle_number_plate_image,'vehicle_document')); ?>"
                                                 alt="avatar"
                                                 style="width: 80px;height: 80px;border-radius:10px;">
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('merchant.driver-vehicledetails',$vehicle->id)); ?>"
                                           class="btn btn-sm btn-info menu-icon btn_detail action_btn"><span
                                                    class="fa fa-list-alt"
                                                    data-original-title="<?php echo app('translator')->get("$string_file.vehicle"); ?>  <?php echo app('translator')->get("$string_file.details"); ?>"
                                                    data-toggle="tooltip"></span></a>

                                        <?php if(Auth::user('merchant')->can('edit_vehicle')): ?>
                                            
                                            
                                            <a href="<?php echo e(route('merchant.driver.vehicle.create',[$vehicle->driver_id,$vehicle->id])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.edit_vehicle"); ?> "
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                                <i class="fa fa-edit"></i> </a>
                                        <?php endif; ?>
                                        <?php if(Auth::user('merchant')->can('delete_vehicle')): ?>
                                            <button onclick="DeleteEvent(<?php echo e($vehicle->id); ?>,<?php echo e(count($value->DriverVehicles)); ?>)"
                                                    type="submit"
                                                    data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                                <i class="fa fa-trash"></i></button>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo convertTimeToUSERzone($vehicle->created_at, $vehicle->Driver->CountryArea->timezone, null, $vehicle->Driver->Merchant); ?>

                                    </td>
                                </tr>
                                <?php $sr++  ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $driver_vehicles, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        function DeleteEvent(id, vehicle_count) {
            var token = $('[name="_token"]').val();
            if (vehicle_count > 1) {
                swal({
                    title: "<?php echo app('translator')->get("$string_file.are_you_sure"); ?>",
                    text: "<?php echo app('translator')->get("$string_file.delete_warning"); ?>",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((isConfirm) => {
                    if (isConfirm) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            type: "GET",
                            url: "<?php echo e(route('driver.delete.pendingvehicle')); ?>" + "/" + id,
                        }).done(function (data) {
                            swal({
                                title: "DELETED!",
                                text: data,
                                type: "success",
                            });
                            window.location.href = "<?php echo e(route('merchant.driver.allvehicles')); ?>";
                        });
                    } else {
                        swal("<?php echo app('translator')->get("$string_file.data_is_safe"); ?>");
                    }
                });
            } else {
                swal({
                    text: "<?php echo app('translator')->get("$string_file.denied_to_delete_vehicle"); ?>",
                });
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/drivervehicles/all_vehicles.blade.php ENDPATH**/ ?>
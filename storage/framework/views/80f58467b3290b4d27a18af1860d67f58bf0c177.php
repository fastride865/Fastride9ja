<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('merchant.driver.allvehicles')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.all_vehicles"); ?> "></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.rejected_vehicle"); ?> </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th> <?php echo app('translator')->get("$string_file.id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_number"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_type"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.services"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.reject_reason"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.image"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.number_plate"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.registered_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.update"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sn = 1; ?>
                        <?php if(count($vehicles) > 0): ?>
                            <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('driver.show',$vehicle->Driver->id)); ?>"
                                           class="address_link"><?php echo e($sn); ?></a>
                                    </td>
                                    <td>
                                        <?php if(Auth::user()->demo == 1): ?>
                                            <?php echo e("********".substr($vehicle->Driver->last_name, -2)); ?><br>
                                            <?php echo e("********".substr($vehicle->Driver->phoneNumber, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($vehicle->Driver->email, -2)); ?>

                                        <?php else: ?>
                                            <?php echo e($vehicle->Driver->first_name." ".$vehicle->Driver->last_name); ?><br>
                                            <?php echo e($vehicle->Driver->email); ?><br>
                                            <?php echo e($vehicle->Driver->phoneNumber); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($vehicle->vehicle_number); ?></td>
                                    <td>
                                        <?php echo e($vehicle->VehicleType->VehicleTypeName); ?>

                                    </td>
                                    <td class="text-center"> <span class="long_text">
                                                <?php echo e(implode(',',array_pluck($vehicle->ServiceTypes,'serviceName'))); ?>

                                            </span></td>
                                    <td><?php echo e($vehicle->Driver->admin_msg); ?></td>
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
                                    <td>
                                        <?php echo convertTimeToUSERzone($vehicle->created_at, $vehicle->Driver->CountryArea->timezone, null, $vehicle->Driver->Merchant); ?>

                                    </td>
                                    <td>
                                        <?php echo convertTimeToUSERzone($vehicle->updated_at, $vehicle->Driver->CountryArea->timezone, null, $vehicle->Driver->Merchant); ?>

                                    </td>
                                </tr>
                                <?php $sn++; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $vehicles, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/drivervehicles/vehicle-rejected.blade.php ENDPATH**/ ?>
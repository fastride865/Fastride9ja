<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('driver.index')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.all_drivers"); ?>"></i>
                            </button>
                        </a>
                        <a href="<?php echo e(route('merchant.driver.vehicle.create', [$driver->id])); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus" title="<?php echo app('translator')->get("$string_file.add_vehicle"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        <?php echo e($driver->first_name." ".$driver->last_name); ?>'s <?php echo app('translator')->get("$string_file.vehicle"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <!-- Task List table -->
                    <!-- <table id="users-contacts" -->
                    <!-- class="table table-responsive table-white-space table-bordered row-grouping display no-wrap icheck table-middle"> -->
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_type"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.services"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_number"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.color"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.image"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.number_plate"); ?></th>
                            <?php if($vehicle_model_expire == 1): ?>
                            <th><?php echo app('translator')->get("$string_file.registered_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.expire_date"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?> </th>`
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1 ?>
                        <?php $__currentLoopData = $driver->DriverVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td class="text-center">
                                    <?php if($vehicle->VehicleType->LanguageVehicleTypeSingle): ?> <?php echo e($vehicle->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName); ?> <?php else: ?>  <?php echo e($vehicle->VehicleType->LanguageVehicleTypeAny->vehicleTypeName); ?> <?php endif; ?>
                                </td>
                                <?php $a = array() ?>
                                <?php $__currentLoopData = $vehicle->ServiceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serviceType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $a[] = $serviceType->serviceName; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center">
                                    <?php echo e(implode(',',$a)); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($vehicle->vehicle_number); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($vehicle->vehicle_color); ?>

                                </td>
                                <td class="text-center">
                                    <img src="<?php echo e(get_image($vehicle->vehicle_image,'vehicle_document')); ?>"
                                         alt="avatar" style="width: 100px;height: 100px;">
                                </td>
                                <td class="text-center">
                                    <img src="<?php echo e(get_image($vehicle->vehicle_number_plate_image,'vehicle_document')); ?>"
                                         alt="avatar" style="width: 100px;height: 100px;">
                                </td>
                                <?php if($vehicle_model_expire == 1): ?>
                                    <td>
                                        <?php echo convertTimeToUSERzone($vehicle->vehicle_register_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2); ?>

                                    </td>
                                    <td>
                                        <?php echo convertTimeToUSERzone($vehicle->vehicle_expire_date, $driver->CountryArea->timezone,null,$driver->Merchant, 2); ?>

                                    </td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <a href="<?php echo e(route('merchant.driver-vehicledetails',$vehicle->id)); ?>"
                                       class="btn btn-sm btn-info menu-icon btn_detail action_btn"><span
                                                class="fa fa-list-alt"
                                                data-original-title="<?php echo app('translator')->get("$string_file.vehicle_details"); ?>"
                                                data-toggle="tooltip"></span>
                                    </a>
                                    <?php if(Auth::user('merchant')->can('edit_vehicle')): ?>
                                        <a href="<?php echo e(route('merchant.driver.vehicle.create',[$vehicle->driver_id,$vehicle->id])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit_vehicle"); ?> "
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i> </a>
                                    <?php endif; ?>

                                </td>
                                <td class="text-center">
                                    <?php echo convertTimeToUSERzone($vehicle->created_at, $driver->CountryArea->timezone,null,$driver->Merchant); ?>

                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/vehicle.blade.php ENDPATH**/ ?>
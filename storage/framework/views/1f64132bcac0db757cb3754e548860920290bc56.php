<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('driver.index')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.all_driver"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.temporary_rejected_drivers"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_number"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.reject_reason"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.registered_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.updated_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $drivers->firstItem() ?>
                            <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>
                                    <td><a href="<?php echo e(route('driver.show',$driver->id)); ?>"
                                           class="address_link"><?php echo e($driver->merchant_driver_id); ?></a></td>
                                    <td><?php echo e($driver->CountryArea->CountryAreaName); ?></td>
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <td>
                                            <?php echo e("********".substr($driver->last_name, -2)); ?><br>
                                            <?php echo e("********".substr($driver->phoneNumber, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($driver->email, -2)); ?>


                                        </td>
                                    <?php else: ?>
                                        <td><?php echo e($driver->first_name." ".$driver->last_name); ?><br>
                                            <?php echo e($driver->email); ?><br>
                                            <?php echo e($driver->phoneNumber); ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <?php $__currentLoopData = $driver->DriverVehicle; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($vehicle->vehicle_number); ?>,
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td><?php echo e($driver->temp_admin_msg); ?></td>
                                    <td>
                                        <?php echo convertTimeToUSERzone($driver->created_at, $driver->CountryArea->timezone, null, $driver->Merchant); ?>

                                    </td>
                                    <td>
                                        <?php echo convertTimeToUSERzone($driver->updated_at, $driver->CountryArea->timezone, null, $driver->Merchant); ?>

                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('driver.show',$driver->id)); ?>"
                                           class="btn btn-sm btn-info menu-icon btn_detail action_btn">
                                            <span class="fa fa-list-alt" title="View Driver Profile"></span>
                                        </a>
                                    </td>
                                </tr>
                                <?php $sr++; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $('.toast').toast('show');
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/rejected-temp.blade.php ENDPATH**/ ?>
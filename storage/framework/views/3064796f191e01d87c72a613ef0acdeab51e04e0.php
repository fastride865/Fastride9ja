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
                    <h3 class="panel-title"><i class="wb-file" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.expired_document"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th> <?php echo app('translator')->get("$string_file.id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.personal_document"); ?> </th>
                            <?php if($merchant_type == "BOTH" || $merchant_type == "VEHICLE"): ?>
                            <th><?php echo app('translator')->get("$string_file.vehicle_document"); ?></th>
                            <?php endif; ?>
                            <?php if($merchant_type == "BOTH" || $merchant_type == "HANDYMAN"): ?>
                            <th><?php echo app('translator')->get("$string_file.handyman_segment_documents"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><a href="<?php echo e(route('driver.show',$driver->id)); ?>"
                                           class="hyperLink"><?php echo e($driver->merchant_driver_id); ?></a>
                                    </td>
                                    <td><?php echo e(!empty($driver->country_area_id) ? $driver->CountryArea->CountryAreaName : ""); ?></td>
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <td>
                                        <span class="long_text">
                                            <?php echo e("********".substr($driver->last_name, -2)); ?><br>
                                            <?php echo e("********".substr($driver->phoneNumber, -2)); ?> <br>
                                            <?php echo e("********".substr($driver->email, -2)); ?>

                                         </span>
                                        </td>
                                    <?php else: ?>
                                        <td>
                                            <span class="long_text">
                                                <?php echo e($driver->first_name." ".$driver->last_name); ?><br>
                                                <?php echo e($driver->phoneNumber); ?> <br>
                                                <?php echo e($driver->email); ?>

                                            </span>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php if(count($driver->DriverDocument) > 0): ?>
                                            <a data-original-title=""
                                               data-toggle="tooltip"
                                               href="<?php echo e(route('driver.add',$driver->id)); ?>" target="_blank"
                                               data-placement="top"
                                               class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn p-document-upload"> <i
                                                        class="fa fa-upload"></i>
                                                <?php echo app('translator')->get("$string_file.upload"); ?>
                                            </a>

                                        <?php else: ?>
                                            ----------
                                        <?php endif; ?>

                                    </td>
                                    <?php if($merchant_type == "BOTH" || $merchant_type == "VEHICLE"): ?>
                                    <td>
                                        <?php if(count($driver->DriverVehicles) > 0): ?>
                                            <?php $__currentLoopData = $driver->DriverVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a data-original-title=""
                                                   data-toggle="tooltip"
                                                   href="<?php echo e(route('merchant.driver.vehicle.create',[$driver->id,$vehicle->id])); ?>" target="_blank"
                                                   data-placement="top"
                                                   class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn v-document-upload v-document-upload"> <i
                                                            class="fa fa-upload"></i>
                                                    <?php echo app('translator')->get("$string_file.vehicle_number"); ?>: <?php echo e($vehicle->vehicle_number); ?>

                                                </a>
                                                <br>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            ----------
                                        <?php endif; ?>

                                    </td>
                                    <?php endif; ?>
                                    <?php if($merchant_type == "BOTH" || $merchant_type == "HANDYMAN"): ?>
                                        <td class="text-center">
                                            <?php if(count($driver->DriverSegmentDocument) > 0): ?>
                                                <a data-original-title=""
                                                   data-toggle="tooltip"
                                                   href="<?php echo e(route('merchant.driver.handyman.segment',$driver->id)); ?>" target="_blank"
                                                   data-placement="top"
                                                   class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn s-document-upload"> <i
                                                            class="fa fa-upload"></i>
                                                    <?php echo app('translator')->get("$string_file.upload"); ?>
                                                </a>
                                            <?php else: ?>
                                                ----------
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $('.toast').toast('show');
        $(".p-document-upload").click(function () {
            <?php echo e(Session::flash('personal-document-expired-error', trans("$string_file.document_expired_error"))); ?>

        });
        $(".v-document-upload").click(function () {
            <?php echo e(Session::flash('vehicle-document-expired-error', trans("$string_file.document_expired_error"))); ?>

        });
        $(".s-document-upload").click(function () {
            <?php echo e(Session::flash('handyman-document-expired-error', trans("$string_file.document_expired_error"))); ?>

        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/expired_document.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('merchant.driver.allvehicles')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.all_vehicles"); ?> "></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.vehicle_details_of"); ?> <?php echo e($vehicle->vehicle_number); ?>

                        <?php if(!$result): ?>
                            <span style="color:red; font-size: 14px;"><?php echo app('translator')->get("$string_file.mandatory_document_not_uploaded"); ?></span>
                        <?php endif; ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="card-body">


                        <h5><?php echo app('translator')->get("$string_file.vehicle_details"); ?></h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <span class=""><?php echo app('translator')->get("$string_file.vehicle_type"); ?> </span> : <?php echo e($vehicle->VehicleType->VehicleTypeName); ?>

                            </div>
                            <div class="col-md-4">
                                <span class=""><?php echo app('translator')->get("$string_file.vehicle_model"); ?>  </span> : <?php echo e($vehicle->VehicleModel->VehicleModelName); ?>

                            </div>
                            <div class="col-md-4">
                                <span class=""><?php echo app('translator')->get("$string_file.vehicle_make"); ?>  </span> : <?php echo e($vehicle->VehicleMake->VehicleMakeName); ?>

                            </div>
                            <div class="col-md-4">
                                <span class=""><?php echo app('translator')->get("$string_file.vehicle_number"); ?> </span> : <?php echo e($vehicle->vehicle_number); ?>

                            </div>
                            <?php if($vehicle_model_expire == 1): ?>
                            <div class="col-md-4">
                                <?php echo app('translator')->get("$string_file.registered_date"); ?>   : <?php echo convertTimeToUSERzone($vehicle->vehicle_register_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2); ?>

                            </div>
                            <div class="col-md-4">
                                <?php echo app('translator')->get("$string_file.expire_date"); ?>   : <?php echo convertTimeToUSERzone($vehicle->vehicle_expire_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2); ?>

                            </div>
                            <?php endif; ?>
                            <?php if(!empty($baby_seat_enable)): ?>
                            <div class="col-md-4">
                                <?php echo app('translator')->get("$string_file.baby_seat_enable"); ?>   : <?php echo e($vehicle->baby_seat == 1 ? trans("$string_file.yes") : trans("$string_file.no")); ?>

                            </div>
                            <?php endif; ?>
                            <?php if(!empty($wheel_chair_enable)): ?>
                                <div class="col-md-4">
                                    <?php echo app('translator')->get("$string_file.wheel_chair_enable"); ?>   : <?php echo e($vehicle->wheel_chair == 1 ? trans("$string_file.yes") : trans("$string_file.no")); ?>

                                </div>
                            <?php endif; ?>
                            <?php if(!empty($vehicle_ac_enable)): ?>
                                <div class="col-md-4">
                                    <?php echo app('translator')->get("$string_file.ac_enable"); ?>   : <?php echo e($vehicle->ac_nonac == 1 ? trans("$string_file.yes") : trans("$string_file.no")); ?>

                                </div>
                            <?php endif; ?>
                        </div>

                        <strong><?php echo app('translator')->get("$string_file.services"); ?> </strong>: <?php echo e(implode(',',array_pluck($vehicle->ServiceTypes,'serviceName'))); ?>


                        <div class="row">
                            <div class="col-md-5">
                                <h5><?php echo app('translator')->get("$string_file.vehicle_image"); ?> </h5>
                               <?php $vehicle_image = get_image($vehicle->vehicle_image,'vehicle_document'); ?>
                                <div class="" style="width: 6.5rem;">
                                    <div class=" bg-light">
                                        <a href="<?php echo e($vehicle_image); ?>" target="_blank">
                                            <img src="<?php echo e($vehicle_image); ?>" class="rounded" alt="<?php echo app('translator')->get("$string_file.vehicle_image"); ?> " width="100" height="100">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <h5><?php echo app('translator')->get("$string_file.number_plate"); ?> </h5>
                                <?php $number_plate = get_image($vehicle->vehicle_number_plate_image,'vehicle_document'); ?>
                                <div class="" style="width: 6.5rem;">
                                    <div class=" bg-light">
                                        <a href="<?php echo e($number_plate); ?>" target="_blank">
                                            <img src="<?php echo e($number_plate); ?>" class="rounded" alt="<?php echo app('translator')->get("$string_file.vehicle_image"); ?> " width="100" height="100">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable">
                                <thead>
                                <tr>
                                    <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.document_name"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.document"); ?> </th>
                                    <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                                    <th><?php echo app('translator')->get("$string_file.expire_date"); ?>  </th>
                                    <th><?php echo app('translator')->get("$string_file.uploaded_at"); ?> </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $sn= 1; ?>
                                <?php $__currentLoopData = $vehicle->DriverVehicleDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($sn); ?></td>
                                        <td> <?php echo e($document->Document->documentname); ?></td>
                                        <td>
                                            <a href="<?php echo e(get_image($document->document,'vehicle_document')); ?>"
                                               target="_blank"><img
                                                        src="<?php echo e(get_image($document->document,'vehicle_document')); ?>"
                                                        style="width:60px;height:60px;border-radius: 10px"></a>
                                        </td>
                                        <td>
                                            <?php switch($document->document_verification_status):
                                                case (1): ?>
                                                <?php echo app('translator')->get("$string_file.pending_for_verification"); ?>
                                                <?php break; ?>
                                                <?php case (2): ?>
                                                <?php echo app('translator')->get("$string_file.verified"); ?>
                                                <?php break; ?>
                                                <?php case (3): ?>
                                                <?php echo app('translator')->get("$string_file.rejected"); ?>
                                                <?php break; ?>
                                            <?php endswitch; ?>
                                        </td>
                                        <td>
                                            <?php echo convertTimeToUSERzone($document->expire_date, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant, 2); ?>

                                        </td>
                                        <td>
                                            <?php echo convertTimeToUSERzone($document->created_at, $vehicle->Driver->CountryArea->timezone,null,$vehicle->Driver->Merchant); ?>

                                        </td>
                                        <?php $sn = $sn+1; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-right m-3">
                        <?php if($result && $vehicle->vehicle_verification_status != 2): ?>
                            <a href="<?php echo e(route('merchant.driver-vehicle-verify',[$vehicle->id,2])); ?>">
                                <button class="btn btn-md btn-success" style="width: 80px"><?php echo app('translator')->get("$string_file.approve"); ?> </button>
                            </a>
                        <?php endif; ?>
                       <?php if($vehicle->vehicle_verification_status != 2): ?>
                        <a href="#">
                            <button class="btn btn-md btn-danger" style="width: 80px"
                                    data-toggle="modal"
                                    data-target="#exampleModalCenter"><?php echo app('translator')->get("$string_file.reject"); ?>
                            </button>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-group" action="<?php echo e(route('merchant.driver-vehicle-reject')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle"><?php echo app('translator')->get("$string_file.reject_vehicle"); ?> </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h5><?php echo app('translator')->get("$string_file.vehicle_document"); ?></h5>
                            </div>
                            <input type="hidden" value="<?php echo e($vehicle->driver_id); ?>" name="driver_id">
                            <input type="hidden" value="<?php echo e($vehicle->id); ?>"
                                   name="driver_vehicle_id">
                            <?php $__currentLoopData = $vehicle->DriverVehicleDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <input type="checkbox" value="<?php echo e($document->id); ?>"
                                           name="vehicle_documents[]"> <?php echo e($document->Document->documentname); ?>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php echo Form::hidden('request_from','vehicle_details'); ?>

                                <textarea class="form-control" placeholder="<?php echo app('translator')->get("$string_file.comments"); ?>" name="comment" required></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?php echo app('translator')->get("$string_file.close"); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo app('translator')->get("$string_file.reject"); ?> </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/drivervehicles/vehicle-details.blade.php ENDPATH**/ ?>
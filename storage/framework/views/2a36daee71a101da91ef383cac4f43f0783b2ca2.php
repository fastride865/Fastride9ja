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
                        <?php echo app('translator')->get("$string_file.docs_going_expire"); ?><span class="text-danger"> ( <?php echo e($currentDate); ?> to <?php echo e($reminder_last_date); ?> )</span></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                            <thead>
                            <tr>
                                <th><input type="checkbox" name="checkAll" id="checkAll">
                                    <button type="submit" class="btn btn-warning btn-sm" data-original-title="Send Notification To All"
                                            data-toggle="tooltip" data-placement="top"><i class="wb-bell"></i>
                                    </button>
                                </th>
                                <th> <?php echo app('translator')->get("$string_file.id"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.service_area"); ?> </th>
                                <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.personal_document"); ?></th>
                                <?php if($merchant_type == "BOTH" || $merchant_type == "VEHICLE"): ?>
                                <th><?php echo app('translator')->get("$string_file.vehicle_document"); ?></th>
                                <?php endif; ?>
                                <?php if($merchant_type == "BOTH" || $merchant_type == "HANDYMAN"): ?>
                                <th><?php echo app('translator')->get("$string_file.handyman_segment_documents"); ?></th>
                                <?php endif; ?>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><input type="checkbox" name="driver_id[]"
                                                   value="<?php echo e($driver->id); ?>" id="checkItem">
                                        </td>
                                        <td><a href="<?php echo e(route('driver.show',$driver->id)); ?>"
                                               class="hyperLink"><?php echo e($driver->merchant_driver_id); ?></a>
                                        </td>
                                        <td><?php echo e($driver->CountryArea->CountryAreaName); ?></td>
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
                                        <td class="text-center">
                                            <?php if(count($driver->DriverDocument) > 0): ?>
                                                <span data-target="#PersonalDocumnetExpire<?php echo e($driver->id); ?>"
                                                      data-toggle="modal"
                                                      id="<?php echo e($driver->id); ?>">
                                                    <a data-original-title=""
                                                            data-toggle="tooltip"
                                                            id="<?php echo e($driver->id); ?>"
                                                            data-placement="top"
                                                            class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn p-document-upload"> <i
                                                                class="fa fa-file-o"></i>
                                                        <?php echo app('translator')->get("$string_file.view"); ?>
                                                    </a>
                                                </span>
                                                <a data-original-title=""
                                                   data-toggle="tooltip"
                                                   href="<?php echo e(route('driver.add',$driver->id)); ?>" target="_blank"
                                                   data-placement="top"
                                                   class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn"> <i
                                                            class="fa fa-upload"></i>
                                                    <?php echo app('translator')->get("$string_file.upload"); ?>
                                                </a>

                                            <?php else: ?>
                                                ----------
                                            <?php endif; ?>
                                        </td>
                                        <?php if($merchant_type == "BOTH" || $merchant_type == "VEHICLE"): ?>
                                        <td class="text-center">
                                            <?php if(count($driver->DriverVehicles) > 0): ?>
                                                <span data-target="#VehicleDocumnetExpire<?php echo e($driver->id); ?>"
                                                      data-toggle="modal"
                                                      id="<?php echo e($driver->id); ?>">
                                                    <a data-original-title="<?php echo app('translator')->get("$string_file.vehicle_document"); ?>"
                                                            data-toggle="tooltip"
                                                            id="<?php echo e($driver->id); ?>"

                                                            data-placement="top"
                                                            class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn"> <i
                                                                class="fa fa-file-o"></i>
                                                        <?php echo app('translator')->get("$string_file.view"); ?>
                                                    </a>
                                                </span>
                                                <?php $__currentLoopData = $driver->DriverVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a data-original-title=""
                                                       data-toggle="tooltip"
                                                       href="<?php echo e(route('merchant.driver.vehicle.create',[$driver->id,$vehicle->id])); ?>" target="_blank"
                                                       data-placement="top"
                                                       class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn v-document-upload"> <i
                                                                class="fa fa-upload"></i>
                                                        <?php echo app('translator')->get("$string_file.vehicle_number"); ?>:
                                                        <?php echo e($vehicle->vehicle_number); ?>

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
                                                <span data-target="#HandymanDocumnetExpire<?php echo e($driver->id); ?>"
                                                      data-toggle="modal"
                                                      id="<?php echo e($driver->id); ?>">
                                                    <a
                                                            data-original-title="<?php echo app('translator')->get("$string_file.handyman_segment_documents"); ?>"
                                                            data-toggle="tooltip"
                                                            id="<?php echo e($driver->id); ?>"
                                                            data-placement="top"
                                                            class="btn text-white btn-sm btn-success menu-icon btn_detail action_btn"> <i
                                                                class="fa fa-file-o"></i>
                                                         <?php echo app('translator')->get("$string_file.view"); ?>
                                                    </a>
                                                </span>
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

                                        <td>
                                            <a href="<?php echo e(route('goingToExpireDocuments.sendNotification',$driver->id)); ?>"
                                               class="btn btn-warning" data-toggle="tooltip"
                                               data-original-title="<?php echo app('translator')->get("$string_file.send_notification"); ?>"><i
                                                        class="fa fa-bell"></i></a></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $drivers, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
    <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade text-left" id="PersonalDocumnetExpire<?php echo e($driver->id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <label class="modal-title text-text-bold-600" id="myModalLabel33"><?php echo app('translator')->get("$string_file.name"); ?> : <b><?php echo e($driver->first_name." ".$driver->last_name); ?></b> |  <?php echo app('translator')->get("$string_file.title"); ?> : <?php echo app('translator')->get("$string_file.personal_docs_going_expire"); ?></label>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="container col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.document_name"); ?></label>
                                </div>

                                <div class="col-md-3">
                                    <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.expire_date"); ?></label>
                                </div>

                                <div class="col-md-3">
                                    <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.document"); ?></label>
                                </div>






                            </div>



                                <?php $__currentLoopData = $driver->DriverDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driverDocs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="row">
                                        <div class="col-md-3"><?php echo e($driverDocs->Document->DocumentName); ?></div>
                                        <div class="col-md-3"><?php echo e($driverDocs->expire_date); ?></div>
                                        <div class="col-md-3"><a target="_blank"
                                                                 href="<?php echo e(get_image($driverDocs->document_file, 'driver_document', $driver->merchant_id)); ?>"><img
                                                        src="<?php echo e(get_image($driverDocs->document_file, 'driver_document', $driver->merchant_id)); ?>"
                                                        height="50px" width="50px"></a></div>










                                    </div><br>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>





                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="<?php echo app('translator')->get("$string_file.close"); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="VehicleDocumnetExpire<?php echo e($driver->id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <label class="modal-title text-text-bold-600" id="myModalLabel33"><?php echo app('translator')->get("$string_file.name"); ?> : <b><?php echo e($driver->first_name." ".$driver->last_name); ?></b> |  <?php echo app('translator')->get("$string_file.title"); ?> : <?php echo app('translator')->get("$string_file.vehicle_docs_going_expire"); ?></label>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="container col-md-12">
                            <?php $__currentLoopData = $driver->DriverVehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driverVehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row">
                                    <div class="col-md-12 text-center text-danger"><b><?php echo app('translator')->get("$string_file.vehicle_number"); ?>
                                            : <?php echo e($driverVehicle->vehicle_number); ?></b></div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.document"); ?><?php echo app('translator')->get("$string_file.name"); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.expire_date"); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.document"); ?></label>
                                    </div>






                                </div>



                                    <?php $__currentLoopData = $driverVehicle->DriverVehicleDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicleDocs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="row">
                                            <div class="col-md-3"><?php echo e($vehicleDocs->Document->DocumentName); ?></div>
                                            <div class="col-md-3"><?php echo e($vehicleDocs->expire_date); ?></div>
                                            <div class="col-md-3"><a target="_blank"
                                                                     href="<?php echo e(get_image($vehicleDocs->document, 'vehicle_document', $driver->merchant_id)); ?>"><img
                                                            src="<?php echo e(get_image($vehicleDocs->document, 'vehicle_document', $driver->merchant_id)); ?>"
                                                            height="50px" width="50px"></a></div>










                                        </div>
                                        <br>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <hr>






                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="<?php echo app('translator')->get("$string_file.close"); ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade text-left" id="HandymanDocumnetExpire<?php echo e($driver->id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <label class="modal-title text-text-bold-600" id="myModalLabel33"><?php echo app('translator')->get("$string_file.name"); ?> : <b><?php echo e($driver->first_name." ".$driver->last_name); ?></b> |  <?php echo app('translator')->get("$string_file.title"); ?> :  <?php echo app('translator')->get("$string_file.handyman_segment_docs_going_expire"); ?></label>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>



                    <div class="modal-body">
                        <div class="container col-md-12">
                            <?php $__currentLoopData = $driver->DriverSegmentDocument; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seg_doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.segment"); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.name"); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.expired_at"); ?></label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="font-weight-bold"><?php echo app('translator')->get("$string_file.document"); ?></label>
                                    </div>






                                </div>
                                        <div class="row">
                                            <div class="col-md-3"><?php echo e($seg_doc->Segment->Name($merchant_id)); ?></div>
                                            <div class="col-md-3"><?php echo e($seg_doc->Document->DocumentName); ?></div>
                                            <div class="col-md-3"><?php echo e($seg_doc->expire_date); ?></div>
                                            <div class="col-md-3"><a target="_blank"
                                                                     href="<?php echo e(get_image($seg_doc->document, 'segment_document', $driver->merchant_id)); ?>"><img
                                                            src="<?php echo e(get_image($seg_doc->document, 'vehicle_document', $driver->merchant_id)); ?>"
                                                            height="50px" width="50px"></a></div>










                                        </div>
                                        <br>
                                    <hr>





                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-secondary" data-dismiss="modal" value="<?php echo app('translator')->get("$string_file.close"); ?>">
                    </div>

                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>

        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });

        $(".p-document-upload").click(function () {
          <?php echo e(Session::flash('personal-document-expire-warning', trans("$string_file.document_expire_warning"))); ?>

        });
        $(".v-document-upload").click(function () {
          <?php echo e(Session::flash('vehicle-document-expire-warning', trans("$string_file.document_expire_warning"))); ?>

        });
        $(".s-document-upload").click(function () {
          <?php echo e(Session::flash('handyman-document-expire-warning', trans("$string_file.document_expire_warning"))); ?>

        });
        // $('.toast').toast('show');
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/going_to_expire_doc.blade.php ENDPATH**/ ?>
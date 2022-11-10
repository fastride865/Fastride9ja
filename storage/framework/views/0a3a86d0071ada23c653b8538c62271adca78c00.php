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
                        <?php if(Auth::user('merchant')->can('view_sos_request')): ?>
                            <a href="<?php echo e(route('excel.sosrequests')); ?>">
                                <button type="button" class="btn btn-icon btn-primary mr-1 float-right"
                                        style="margin:10px"
                                        data-original-title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"
                                        data-toggle="tooltip"><i
                                            class="fa fa-download"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.sos_request"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <form action="<?php echo e(route('merchant.sos.sreach')); ?>">
                        <div class="table_search">
                            <div class="row">
                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <select class="form-control" name="application"
                                                id="application">
                                            <option value="">--<?php echo app('translator')->get("$string_file.application"); ?>--</option>
                                            <option value="2"><?php echo app('translator')->get("$string_file.driver"); ?></option>
                                            <option value="1"><?php echo app('translator')->get("$string_file.user"); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="rider"
                                               placeholder="<?php echo app('translator')->get("$string_file.user_details"); ?>"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="driver"
                                               placeholder="<?php echo app('translator')->get("$string_file.driver_details"); ?>"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-md-2 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="date"
                                               placeholder="<?php echo app('translator')->get("$string_file.date"); ?>"
                                               class="form-control col-md-12 col-xs-12 datepickersearch"
                                               id="datepickersearch">
                                    </div>
                                </div>
                                <div class="col-sm-2 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.application"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.services"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.vehicle_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.number"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.sos_location"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.request_time"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.date"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $sosRequests->firstItem() ?>
                        <?php $__currentLoopData = $sosRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sosRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><a target="_blank" class="address_link"
                                       href="<?php echo e(route('merchant.booking.details',$sosRequest->booking_id)); ?>">#<?php echo e($sr); ?></a>
                                </td>
                                <?php switch($sosRequest->application):
                                    case (1): ?>
                                    <td><?php echo app('translator')->get("$string_file.user"); ?></td>
                                    <?php break; ?>
                                    <?php case (2): ?>
                                    <td><?php echo app('translator')->get("$string_file.driver"); ?></td>
                                    <?php break; ?>
                                <?php endswitch; ?>

                                <?php if(Auth::user()->demo == 1): ?>
                                    <td>
                                        <?php echo e("********".substr($sosRequest->Booking->User->UserName, -2)); ?>

                                        <br>
                                        <?php echo e("********".substr($sosRequest->Booking->User->UserPhone, -2)); ?>

                                        <br>
                                        <?php echo e("********".substr($sosRequest->Booking->User->email, -2)); ?>

                                    </td>
                                    <td>
                                        <?php if($sosRequest->Booking->driver_id): ?>
                                            <?php echo e("********".substr($sosRequest->Booking->Driver->last_name, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($sosRequest->Booking->Driver->phoneNumber, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($sosRequest->Booking->Driver->email, -2)); ?>

                                        <?php else: ?>
                                            No Driver
                                        <?php endif; ?>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <?php echo e($sosRequest->Booking->User->UserName); ?>

                                        <br>
                                        <?php echo e($sosRequest->Booking->User->UserPhone); ?>

                                        <br>
                                        <?php echo e($sosRequest->Booking->User->email); ?>

                                    </td>
                                    <td>
                                        <?php if($sosRequest->Booking->driver_id): ?>
                                            <?php echo e($sosRequest->Booking->Driver->first_name." ".$sosRequest->Booking->Driver->last_name); ?>

                                            <br>
                                            <?php echo e($sosRequest->Booking->Driver->phoneNumber); ?>

                                            <br>
                                            <?php echo e($sosRequest->Booking->Driver->email); ?>

                                        <?php else: ?>
                                            No Driver
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>

                                <td> <?php echo e($sosRequest->Booking->CountryArea->LanguageSingle == "" ? $sosRequest->Booking->CountryArea->LanguageAny->AreaName : $sosRequest->Booking->CountryArea->LanguageSingle->AreaName); ?></td>
                                <td> <?php echo e($sosRequest->Booking->ServiceType->serviceName); ?></td>
                                <td> <?php echo e($sosRequest->Booking->VehicleType->LanguageVehicleTypeSingle == "" ? $sosRequest->Booking->VehicleType->LanguageVehicleTypeAny->vehicleTypeName : $sosRequest->Booking->VehicleType->LanguageVehicleTypeSingle->vehicleTypeName); ?></td>

                                <td><?php echo e($sosRequest->number); ?></td>
                                <td><a class="map_address address_link" target="_blank"
                                       href="https://www.google.com/maps/place/<?php echo e($sosRequest->latitude); ?>,<?php echo e($sosRequest->longitude); ?>"><?php echo e($sosRequest->latitude); ?>

                                        ,<?php echo e($sosRequest->longitude); ?></a>
                                </td>
                                <td><?php echo convertTimeToUSERzone($sosRequest->created_at, $sosRequest->CountryArea->timezone, null, $sosRequest->Merchant); ?></td>
                                <td><?php echo convertTimeToUSERzone($sosRequest->Booking->created_at, $sosRequest->CountryArea->timezone, null, $sosRequest->Merchant, 2); ?></td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <div class="pagination1 float-right"><?php echo e($sosRequests->appends($data)->links()); ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/sos/request.blade.php ENDPATH**/ ?>
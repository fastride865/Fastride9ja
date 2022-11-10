<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                            <a href="<?php echo e(route('excel.allrides',$arr_search)); ?>" >
                                <button type="button" data-toggle="tooltip" class="btn btn-icon btn-primary float-right" style="margin:10px">
                                    <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                                </button>
                            </a>
                        </div>
                    <h3 class="panel-title"><i class="fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.all_rides"); ?></h3>
                    </header>
                <div class="panel-body container-fluid">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.ride_id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.ride_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_detail"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.pickup_drop"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.payment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $bookings->firstItem() ;
                        $status_keys = array_keys($arr_booking_status);
                        ?>
                        <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($booking->merchant_booking_id); ?></td>
                                <td>
                                    <?php if($booking->booking_type == 1): ?>
                                        <?php echo app('translator')->get("$string_file.ride_now"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.ride_later"); ?><br>(
                                        <?php echo date(getDateTimeFormat($booking->Merchant->datetime_format,2),strtotime($booking->later_booking_date)); ?>

<br>
                                        <br>
                                        <?php echo e($booking->later_booking_time); ?> )
                                    <?php endif; ?>
                                </td>

                                <?php if(Auth::user()->demo == 1): ?>
                                    <td>
                                                         <span class="long_text">
                                                    <?php echo e("********".substr($booking->User->UserName,-2)); ?>

                                                    <br>
                                                    <?php echo e("********".substr($booking->User->UserPhone,-2)); ?>

                                                    <br>
                                                    <?php echo e("********".substr($booking->User->email,-2)); ?>

                                                    </span>
                                    </td>
                                    <td>
                                                         <span class="long_text">
                                                    <?php if($booking->Driver): ?>
                                                                 <?php echo e('********'.substr($booking->Driver->last_name,-2)); ?>

                                                                 <br>
                                                                 <?php echo e('********'.substr($booking->Driver->phoneNumber,-2)); ?>

                                                                 <br>
                                                                 <?php echo e('********'.substr($booking->Driver->email,-2)); ?>

                                                             <?php else: ?>
                                                                 <?php echo app('translator')->get("$string_file.not_assigned_yet"); ?>
                                                             <?php endif; ?>
                                                    </span>
                                    </td>
                                <?php else: ?>
                                    <td>
                                                         <span class="long_text">
                                                    <?php echo e($booking->User->UserName); ?>

                                                    <br>
                                                    <?php echo e($booking->User->UserPhone); ?>

                                                    <br>
                                                    <?php echo e($booking->User->email); ?>

                                                    </span>
                                    </td>
                                    <td>
                                                         <span class="long_text">
                                                    <?php if($booking->Driver): ?>
                                                                 <?php echo e($booking->Driver->first_name.' '.$booking->Driver->last_name); ?>

                                                                 <br>
                                                                 <?php echo e($booking->Driver->phoneNumber); ?>

                                                                 <br>
                                                                 <?php echo e($booking->Driver->email); ?>

                                                             <?php else: ?>
                                                                 <?php echo app('translator')->get("$string_file.not_assigned_yet"); ?>
                                                             <?php endif; ?>
                                                    </span>
                                    </td>
                                <?php endif; ?>

                                <td>
                                    <?php switch($booking->platform):
                                        case (1): ?>
                                        <?php echo app('translator')->get("$string_file.application"); ?>
                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <?php echo app('translator')->get("$string_file.admin"); ?>
                                        <?php break; ?>
                                        <?php case (3): ?>
                                        <?php echo app('translator')->get("$string_file.web"); ?>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                    <br>
                                    <?php
                                        $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : $booking->deliveryType->name ;
                                    ?>
                                    <?php echo e($service_text); ?> <br>
                                    <?php echo e($booking->VehicleType->VehicleTypeName); ?>

                                </td>
                                <td> <?php echo e($booking->CountryArea->CountryAreaName); ?></td>
                                <td>
                                    <a title="<?php echo e($booking->pickup_location); ?>"
                                       href="https://www.google.com/maps/place/<?php echo e($booking->pickup_location); ?>" class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i></a>
                                    <a title="<?php echo e($booking->drop_location); ?>"
                                       href="https://www.google.com/maps/place/<?php echo e($booking->drop_location); ?>" class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                </td>
                                <td style="text-align: center">
                                    <?php if(!empty($arr_booking_status)): ?>
                                        <?php echo isset($arr_booking_status[$booking->booking_status]) ? $arr_booking_status[$booking->booking_status] : ""; ?>

                                        <br>
                                        <?php echo app('translator')->get("$string_file.at"); ?> <?php echo convertTimeToUSERzone($booking->updated_at, $booking->CountryArea->timezone,null,$booking->Merchant, 3); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($booking->PaymentMethod->payment_method); ?>

                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($booking->created_at, $booking->CountryArea->timezone,null,$booking->Merchant); ?>




                                </td>
                                <td>
                                    <a target="_blank" title="<?php echo app('translator')->get("$string_file.requested_drivers"); ?>"
                                       href="<?php echo e(route('merchant.ride-requests',$booking->id)); ?>"
                                       class="btn btn-sm btn-success menu-icon btn_detail action_btn"><span
                                                class="fa fa-list-alt"></span></a>

                                    <a target="_blank" title="<?php echo app('translator')->get("$string_file.ride_details"); ?>"
                                       href="<?php echo e(route('merchant.booking.details',$booking->id)); ?>"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                class="fa fa-info-circle"
                                                title="Booking Details"></span></a>

                                    <?php if($booking->booking_status == 1002 || $booking->booking_status == 1003 || $booking->booking_status == 1004 || $booking->booking_status == 1005): ?>
                                    <a target="_blank" title="<?php echo app('translator')->get("$string_file.invoice"); ?>"
                                       href="<?php echo e(route('merchant.booking.invoice',$booking->id)); ?>"
                                       class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span
                                                class="fa fa-print"></span></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/booking/all-ride.blade.php ENDPATH**/ ?>
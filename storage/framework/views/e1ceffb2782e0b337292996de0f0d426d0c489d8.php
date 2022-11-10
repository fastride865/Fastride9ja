<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
             <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('excel.complete',$arr_search)); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right" style="margin: 10px;">
                                <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.completed_rides"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.ride_id"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.ride_type"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.request_from"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.ride_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.pickup_drop"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.payment_method"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.bill_amount"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.date"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = $bookings->firstItem() ?>
                            <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>
                                    <td><?php echo e($booking->merchant_booking_id); ?></td>
                                    <td>
                                        <?php if($booking->booking_type == 1): ?>
                                            <?php echo app('translator')->get("$string_file.ride_now"); ?>
                                        <?php else: ?>
                                            <?php echo app('translator')->get("$string_file.ride_later"); ?> <br>(
                                            <?php echo date(getDateTimeFormat($booking->Merchant->datetime_format,2),strtotime($booking->later_booking_date)); ?>

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
                                                         <?php echo e("********".substr($booking->Driver->last_name,-2)); ?>

                                                        <br>
                                                       <?php echo e("********".substr($booking->Driver->phoneNumber,-2)); ?>

                                                        <br>
                                                        <?php echo e("********".substr($booking->Driver->email,-2)); ?>

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
                                                         <?php echo e($booking->Driver->first_name.' '.$booking->Driver->last_name); ?>

                                                        <br>
                                                        <?php echo e($booking->Driver->phoneNumber); ?>

                                                        <br>
                                                        <?php echo e($booking->Driver->email); ?>

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
                                    </td>
                                    <?php
                                            $package_name = ($booking->service_type_id == 2) && !empty($booking->service_package_id) ? ' ('.$booking->ServicePackage->PackageName.')' : '';
                                            $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName($booking->merchant_id).$package_name : ($booking->deliveryType ? $booking->deliveryType->name : '---' );
                                    ?>
                                    <td><?php echo nl2br($booking->CountryArea->CountryAreaName ."\n". $service_text."\n".$booking->VehicleType->VehicleTypeName); ?></td>

                                    <td>
                                        <?php if(!empty($booking->BookingDetail->start_location)): ?>
                                            <a title="<?php echo e($booking->BookingDetail->start_location); ?>"
                                               target="_blank"
                                               href="https://www.google.com/maps/place/<?php echo e($booking->BookingDetail->start_location); ?>" class="btn btn-icon btn-success ml-2  0"><i class="icon wb-map"></i></a>
                                        <?php endif; ?>
                                        <?php if(!empty($booking->BookingDetail->end_location)): ?>
                                            <a title="<?php echo e($booking->BookingDetail->end_location); ?>"
                                               target="_blank"
                                               href="https://www.google.com/maps/place/<?php echo e($booking->BookingDetail->end_location); ?>" class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($booking->PaymentMethod->payment_method); ?>

                                    </td>
                                    <td>
                                        <?php echo e($booking->CountryArea->Country->isoCode . $booking->final_amount_paid); ?>

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
                                                    title=""></span></a>

                                        <a target="_blank" title="<?php echo app('translator')->get("$string_file.invoice"); ?>"
                                           href="<?php echo e(route('merchant.booking.invoice',$booking->id)); ?>"
                                           class="btn btn-sm btn-warning menu-icon btn_eye action_btn"><span
                                                    class="fa fa-print"></span></a>
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
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/booking/complete.blade.php ENDPATH**/ ?>
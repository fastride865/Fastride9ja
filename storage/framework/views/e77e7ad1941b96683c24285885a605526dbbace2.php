<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
           <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(Auth::user('merchant')->can('create_taxi_company')): ?>
                            <a href="<?php echo e(route('excel.ridecancel',$arr_search)); ?>">
                                <button type="button" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"
                                        class="btn btn-icon btn-primary float-right"  style="margin:10px"><i class="wb-download"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.cancelled_rides"); ?>
                    </h3>
                </header>
                <div class="panel-body">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.ride_id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.ride_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.ride_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.pickup_drop"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.cancel_reason"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $bookings->firstItem() ?>
                        <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php echo e($sr); ?>

                                </td>
                                <td><a target="_blank" class="address_link"
                                       href="<?php echo e(route('merchant.booking.details',$booking->id)); ?>"><?php echo e($booking->merchant_booking_id); ?></a>
                                </td>
                                <td>
                                    <?php if($booking->booking_type == 1): ?>
                                        <?php echo app('translator')->get("$string_file.ride_now"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.ride_later"); ?> <br>(
                                        <?php echo convertTimeToUSERzone($booking->later_booking_date, $booking->CountryArea->timezone,null,$booking->Merchant, 2); ?><br>
                                        
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
                                <?php endif; ?>

                                <?php
                                    $service_text = ($booking->ServiceType) ? $booking->ServiceType->serviceName : $booking->deliveryType->name ;
                                ?>

                                <td><?php echo nl2br($booking->CountryArea->CountryAreaName ."\n". $service_text."\n".$booking->VehicleType->VehicleTypeName); ?></td>

                                <td>
                                    <a title="<?php echo e($booking->pickup_location); ?>"
                                       href="https://www.google.com/maps/place/<?php echo e($booking->pickup_location); ?>" class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i></a>
                                    <a title="<?php echo e($booking->drop_location); ?>"
                                       href="https://www.google.com/maps/place/<?php echo e($booking->drop_location); ?>" class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                </td>
                                <td>
                                    <span>
                                        <?php echo e(!empty($booking->cancel_reason_id) ? $booking->CancelReason->ReasonName  : ""); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php if(!empty($arr_booking_status)): ?>
                                        <?php echo isset($arr_booking_status[$booking->booking_status]) ? $arr_booking_status[$booking->booking_status] : ""; ?>

                                    <?php endif; ?>
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
                                </td>
                            </tr>
                            <?php $sr++ ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/booking/cancel.blade.php ENDPATH**/ ?>
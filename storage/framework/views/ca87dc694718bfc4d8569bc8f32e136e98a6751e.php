<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="<?php echo e(URL::previous()); ?>">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-users" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.requested_drivers_of_ride"); ?> #<?php echo e($booking->merchant_booking_id); ?>

                    </h3>

                </header>
                <div class="panel-body container-fluid">
                    <?php
                        $arr_failed_player_id = [];
                        $arr_success_player_id = [];
                    ?>
                    <?php if(isset($booking->OneSignalLog) && !empty($booking->OneSignalLog)): ?>
                        <?php
                            $arr_failed_player_id = json_decode($booking->OneSignalLog->failed_driver_id,true);
                            $arr_success_player_id = json_decode($booking->OneSignalLog->success_driver_id,true);
                        ?>
                        <b><?php echo app('translator')->get("$string_file.onesignal_summary"); ?> =></b> <?php echo app('translator')->get("$string_file.request_sent"); ?> : <b><?php echo $booking->OneSignalLog->total_request_sent; ?> &nbsp; &nbsp;</b> <?php echo app('translator')->get("$string_file.total_success"); ?> : &nbsp;&nbsp;<b><?php echo !empty($arr_success_player_id) ? count($arr_success_player_id) : 0; ?> &nbsp;&nbsp;</b> <?php echo app('translator')->get('admin.total_failed'); ?> :<b> &nbsp;&nbsp;<?php echo !empty($arr_failed_player_id) ? count($arr_failed_player_id) : 0; ?></b>
                    <?php endif; ?>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.pickup_distance"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.onesignal_request"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.updated_at"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sn =1; ?>
                        <?php $__currentLoopData = $booking->BookingRequestDriver; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo $sn; ?></td>
                                <td>
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <?php echo e("********".substr($driver->Driver->first_name. $driver->Driver->last_name,-2)); ?>

                                        <br>
                                        <?php echo e("********".substr($driver->Driver->phoneNumber, -2)); ?>

                                        <br>
                                        <?php echo e("********".substr($driver->Driver->email, -2)); ?>

                                    <?php else: ?>
                                        <?php echo e($driver->Driver->first_name. $driver->Driver->last_name); ?>

                                        <br>
                                        <?php echo e($driver->Driver->phoneNumber); ?>

                                        <br>
                                        <?php echo e($driver->Driver->email); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e(round($driver->distance_from_pickup,2)); ?>

                                </td>
                                <td>
                                    <?php if(!empty($arr_success_player_id) && in_array($driver->Driver->player_id,$arr_success_player_id)): ?>
                                        <?php echo app('translator')->get("$string_file.success"); ?>
                                    <?php elseif(!empty($arr_failed_player_id) && in_array($driver->Driver->player_id,$arr_failed_player_id)): ?>
                                        <?php echo app('translator')->get("$string_file.failed"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php switch($driver->request_status):
                                        case (1): ?>
                                        <?php echo app('translator')->get("$string_file.no_action"); ?>
                                        <?php break; ?>
                                        <?php case (2): ?>
                                        <?php echo app('translator')->get("$string_file.accepted"); ?>
                                        <?php break; ?>
                                        <?php case (3): ?>
                                        <?php echo app('translator')->get("$string_file.rejected"); ?>
                                        <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($driver->created_at, $driver->Booking->CountryArea->timezone,null,$driver->Booking->Merchant); ?>

                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($driver->updated_at, $driver->Booking->CountryArea->timezone,null,$driver->Booking->Merchant); ?>

                                </td>
                            </tr>
                            <?php $sn++; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/booking/request.blade.php ENDPATH**/ ?>
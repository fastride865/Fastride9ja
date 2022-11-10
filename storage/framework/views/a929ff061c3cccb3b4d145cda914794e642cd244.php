<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('excel.driver')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-primary float-right"
                                    style="margin:10px">
                                <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon wb-list" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.driver_earning"); ?>
                        </span>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.other_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.job_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.earning_details"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $drivers->firstItem() ?>
                        <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><a href="<?php echo e(route('driver.show',$driver->id)); ?>"
                                       class="hyperLink"><?php echo e($driver->merchant_driver_id); ?></a>
                                </td>
                                <td>
                                <span class="long_text">
                                    <?php echo e(is_demo_data($driver->first_name.' '.$driver->last_name,$driver->Merchant)); ?>

                                </span>
                                </td>
                                <td>
                                    <?php echo e(is_demo_data($driver->phoneNumber,$driver->Merchant)); ?>

                                    <br>
                                    <?php echo e(is_demo_data($driver->email,$driver->Merchant)); ?>

                                </td>
                                <td>
                                    <?php if($driver->segment_group_id == 1): ?>
                                        <?php
                                            $arr_segment_sub_group_for_admin = array_pluck($driver->Segment,'sub_group_for_admin');
                                        ?>
                                        <?php if(in_array(1,$arr_segment_sub_group_for_admin)): ?>
                                            <?php
                                                $bookings = $driver->total_rides;
                                                $bookings_amount = !empty($driver->ride_earning) ? $driver->ride_earning : 0;
                                            ?>
                                            <a href="<?php echo e(route('merchant.driver-taxi-services-report',['driver_id'=>$driver->id])); ?>">
                                                <span class="badge badge-info font-weight-100"><?php echo app('translator')->get("$string_file.rides"); ?> : <?php echo e($bookings); ?></span>
                                            </a>
                                            <br>
                                        <?php endif; ?>
                                        <?php if(in_array(2,$arr_segment_sub_group_for_admin)): ?>
                                            <?php
                                                $orders = $driver->total_orders;
                                                $orders_amount = !empty($driver->order_earning) ? $driver->order_earning : 0;
                                            ?>
                                            <a href="<?php echo e(route('merchant.driver-delivery-services-report',['driver_id'=>$driver->id])); ?>">
                                                <span class="badge badge-info font-weight-100"><?php echo app('translator')->get("$string_file.orders"); ?> : <?php echo e($orders); ?></span>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php
                                            $handyman_orders = isset($driver->total_bookings) ? $driver->total_bookings : 0;
                                            $handyman_orders_amount = !empty($driver->booking_earning) ? $driver->booking_earning : 0;
                                        ?>
                                        <a href="<?php echo e(route('merchant.driver-handyman-services-report',['driver_id'=>$driver->id])); ?>">
                                            <span class="badge badge-info font-weight-100"><?php echo app('translator')->get("$string_file.bookings"); ?> : <?php echo e($handyman_orders); ?></span>
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($driver->segment_group_id == 1): ?>
                                        <?php
                                            $arr_segment_sub_group_for_admin = array_pluck($driver->Segment,'sub_group_for_admin');
                                        ?>
                                        <?php if(in_array(1,$arr_segment_sub_group_for_admin)): ?>
                                            <?php
                                                $bookings = $driver->total_rides;
                                                $bookings_amount = !empty($driver->ride_earning) ? $driver->ride_earning : 0;
                                            ?>
                                            <a href="<?php echo e(route('merchant.driver.jobs',['booking',$driver->id])); ?>">
                                                <span class="badge badge-success font-weight-100"><?php echo app('translator')->get("$string_file.ride_amount"); ?> : <?php echo e($bookings_amount); ?></span>
                                            </a>
                                            <br>
                                        <?php endif; ?>
                                        <?php if(in_array(2,$arr_segment_sub_group_for_admin)): ?>
                                            <?php
                                                $orders = $driver->total_orders;
                                                $orders_amount = !empty($driver->order_earning) ? $driver->order_earning : 0;
                                            ?>
                                            <a href="<?php echo e(route('merchant.driver.jobs',['order',$driver->id])); ?>">
                                                <span class="badge badge-success font-weight-100"><?php echo app('translator')->get("$string_file.orders_amount"); ?>: <?php echo e($orders_amount); ?></span>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php
                                            $handyman_orders_amount = !empty($driver->booking_earning) ? $driver->booking_earning : 0;
                                        ?>
                                        <a href="<?php echo e(route('merchant.driver.jobs',['handyman-order',$driver->id])); ?>">
                                            <span class="badge badge-success font-weight-100"><?php echo app('translator')->get("$string_file.bookings"); ?> : <?php echo e($handyman_orders_amount); ?></span>
                                        </a>
                                    <?php endif; ?>
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
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        function selectSearchFields() {
            var segment_id = $('#segment_id').val();
            var area_id = $('#area_id').val();
            var by = $('#by_param').val();
            var by_text = $('#keyword').val();
            if (segment_id.length == 0 && area_id == "" && by == "" && by_text == "") {
                alert("Please select at least one search field");
                return false;
            } else if (by != "" && by_text == "") {
                alert("Please enter text according to selected parameter");
                return false;
            } else if (by_text != "" && by == "") {
                alert("Please select parameter according to entered text");
                return false;
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/report/driver-earning.blade.php ENDPATH**/ ?>
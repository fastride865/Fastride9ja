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
                        <a href="<?php echo e(route('merchant.handyman-booking-export',$arr_search)); ?>">
                            <button type="button" title="<?php echo app('translator')->get("$string_file.export_bookings"); ?>"
                                    class="btn btn-icon btn-success" style="margin:10px"><?php echo app('translator')->get("$string_file.export_bookings"); ?>
                                <i class="wb-download"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        <?php echo e(trans($string_file.'.handyman').' '.trans($string_file.'.booking').' '.trans("$string_file.management")); ?>

                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $search_view; ?>

                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.booking_id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.payment_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.earning_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.booking_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.pickup"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $arr_orders->firstItem();
                        $user_name = ''; $user_phone = ''; $user_email = '';
                        $driver_name = '';$driver_email = '';
                        $arr_price_type = get_price_card_type("web","BOTH",$string_file);
                        ?>
                        <?php $__currentLoopData = $arr_orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $currency = $order->CountryArea->Country->isoCode;
                            ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($order->merchant_order_id); ?></td>
                                <?php if(Auth::user()->demo == 1): ?>
                                <td>
                                    <?php echo e("********".substr($order->User->UserName,-3)); ?> <br>
                                    <?php echo e("********".substr($order->User->UserPhone,-3)); ?> <br>
                                    <?php echo e("********".substr($order->User->email,-3)); ?> <br>
                                </td>
                                <td>
                                    <?php if(!empty($order->Driver->id)): ?>
                                        <?php echo e("********".substr($order->Driver->last_name,-3)); ?> <br>
                                        <?php echo e("********".substr($order->Driver->UserPhone,-3)); ?> <br>
                                        <?php echo e("********".substr($order->Driver->email,-3)); ?> <br>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.not_assigned_yet"); ?>
                                    <?php endif; ?>
                                </td>
                                <?php else: ?>
                                    <td>
                                        <?php echo e($order->User->UserName); ?> <br>
                                        <?php echo e($order->User->UserPhone); ?> <br>
                                        <?php echo e($order->User->email); ?> <br>
                                    </td>
                                    <td>
                                        <?php if(!empty($order->Driver->id)): ?>
                                            <?php echo e($order->Driver->last_name); ?> <br>
                                            <?php echo e($order->Driver->UserPhone); ?> <br>
                                            <?php echo e($order->Driver->email); ?> <br>
                                        <?php else: ?>
                                            <?php echo app('translator')->get("$string_file.not_assigned_yet"); ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php $arr_services = []; $order_details = $order->HandymanOrderDetail;
                                        foreach($order_details as $details){
                                            $arr_services[] = $details->ServiceType->serviceName;
                                        }
                                    ?>
                                    <?php echo e(trans($string_file.".date").' : '.$order->booking_date); ?>

                                    <br>
                                    <?php echo e(trans("$string_file.price_type").' : '); ?>

                                    <?php echo e(isset($arr_price_type[$order->price_type]) ? $arr_price_type[$order->price_type] : ""); ?>

                                    <br>
                                    <?php if($order->price_type == 2): ?>
                                        <?php echo e(trans("$string_file.service_time").' : '); ?> <?php echo e($order->total_service_hours); ?> <?php echo app('translator')->get("$string_file.hour"); ?>
                                        <br>
                                    <?php endif; ?>
                                    <?php echo e(trans("$string_file.service_type").' : '); ?>

                                    <?php $__currentLoopData = $order_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($details->ServiceType->serviceName); ?>, <br>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <br>
                                    <?php echo app('translator')->get("$string_file.segment"); ?> : <strong><?php echo e($order->Segment->name); ?></strong>
                                    <br>
                                </td>
                                <td>
                                    <?php echo e(trans("$string_file.mode").': '.$order->PaymentMethod->payment_method); ?> <br>
                                    <?php if($order->price_type == 2 && $order->order_status != 7 && $order->is_order_completed !=1): ?>
                                        <?php
                                            $cart_amount =  $order->hourly_amount.' '.trans("$string_file.hourly");
                                            $payment_message = trans("$string_file.handyman_order_payment");
                                        ?>
                                        <?php echo e($currency.$cart_amount); ?>

                                        <br>
                                        <b><?php echo app('translator')->get("$string_file.note"); ?>:</b> <?php echo e($payment_message); ?>

                                    <?php else: ?>
                                        <?php echo e(trans("$string_file.tax").': '); ?> <?php echo e($order->tax_per); ?> % <br>

                                        <?php echo e(trans("$string_file.cart_amount").': '.$currency.' '.$order->cart_amount); ?>

                                        <br>
                                        <?php echo e(trans("$string_file.total_amount").': '.$currency.' '.$order->total_booking_amount); ?>

                                        <br>
                                        <?php echo e(trans("$string_file.minimum_booking_bill").': '.$currency.' '.$order->minimum_booking_amount); ?>

                                        (<?php echo app('translator')->get("$string_file.tax_included"); ?>)<br>

                                        <b><?php echo e(trans("$string_file.final_amount_paid").': '.$currency.' '
                                        .$order->final_amount_paid); ?></b>
                                        <br>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($order->is_order_completed == 1 && !empty($order->HandymanOrderTransaction->id)): ?>
                                        <?php echo app('translator')->get("$string_file.driver"); ?>
                                        : <?php echo e($currency.$order->HandymanOrderTransaction->driver_earning); ?>

                                        <br>
                                        <?php echo app('translator')->get("$string_file.merchant"); ?>
                                        : <?php echo e($currency.$order->HandymanOrderTransaction->company_earning); ?>

                                    <?php endif; ?>
                                </td>
                                <td> <?php echo e($order->CountryArea->CountryAreaName); ?></td>
                                <td style="text-align: center">
                                    <?php echo e($arr_status[$order->order_status]); ?>

                                </td>
                                <td><?php echo $order->booking_date; ?></td>
                                <?php $created_at = convertTimeToUSERzone($order->created_at, $order->CountryArea->timezone,null, $order->Merchant); ?>
                                <td><?php echo $created_at; ?></td>
                                <td>
                                    <a title="<?php echo e($order->drop_location); ?>" target="_blank"
                                       href="https://www.google.com/maps/place/<?php echo e($order->drop_location); ?>"
                                       class="btn btn-icon btn-danger ml-20">
                                        <i class="icon fa-tint"></i>
                                    </a>
                                </td>
                                <td>
                                    <a target="_blank" title="<?php echo app('translator')->get("$string_file.order_details"); ?>"
                                       href="<?php echo e(route('merchant.handyman.order.detail',$order->id)); ?>"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                class="fa fa-info-circle"
                                                title="<?php echo e(trans("$string_file.booking_details")); ?>"></span></a>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $arr_orders, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/handyman-order/index.blade.php ENDPATH**/ ?>
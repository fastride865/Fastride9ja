<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('driver.index')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-reply" title="<?php echo app('translator')->get("$string_file.all_drivers"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa fa-car" aria-hidden="true"></i>
                        <?php echo e($driver->first_name." ".$driver->last_name); ?>'s
                        <?php if(!empty($bookings)): ?>
                        <?php echo app('translator')->get("$string_file.rides"); ?>
                        <?php elseif($food_grocery_orders): ?>
                        <?php echo app('translator')->get("$string_file.orders"); ?>
                        <?php elseif($handyman_orders): ?>
                        <?php echo app('translator')->get("$string_file.bookings"); ?>
                        <?php endif; ?>
                    </h3>
                </header>
                <?php if(!empty($bookings)): ?>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.ride_id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.ride_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_detail"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.payment_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.pickup_drop"); ?></th>
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
                                        <?php echo app('translator')->get("$string_file.ride_later"); ?>
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
                                    <?php echo app('translator')->get("$string_file.ride_configuration"); ?>
                                    <br>
                                 <span><?php echo app('translator')->get("$string_file.service_type"); ?></span> : <?php echo e(isset($booking->ServiceType) ? $booking->ServiceType->serviceName : ''); ?> <br>
                                 <span><?php echo app('translator')->get("$string_file.vehicle_type"); ?></span> : <?php echo e($booking->VehicleType->VehicleTypeName); ?>

                                </td>
                                <td style="text-align: center">
                                    <?php echo e(trans("$string_file.mode").": ". $booking->PaymentMethod->payment_method); ?><br>
                                    <?php echo e(trans("$string_file.total").": ". $booking->CountryArea->Country->isoCode.' '.$booking->final_amount_paid); ?>

                                    <br>
                                </td>
                                <td>
                                    <?php echo app('translator')->get("$string_file.at"); ?> <?php echo e(date_format($booking->created_at,'H:i a')); ?>

                                    <br>
                                    <?php echo e(date_format($booking->created_at,'D, M d, Y')); ?>


                                </td>
                                <td> <?php echo e($booking->CountryArea->CountryAreaName); ?></td>
                                <td style="text-align: center">
                                    <?php if($booking->booking_status == 1005): ?>
                                        <span class="badge badge-success font-weight-100"><?php echo e($booking_status[$booking->booking_status]); ?></span>
                                    <?php elseif(in_array($booking->booking_status,[1001,1012,1002,1003,1004])): ?>
                                        <span class="badge btn-info font-weight-100"><?php echo e($booking_status[$booking->booking_status]); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger font-weight-100"><?php echo e($booking_status[$booking->booking_status]); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($booking->pickup_location)): ?>
                                        <a title="<?php echo e($booking->pickup_location); ?>"
                                           target="_blank"
                                           href="https://www.google.com/maps/place/<?php echo e($booking->pickup_location); ?>" class="btn btn-icon btn-success ml-20"><i class="icon wb-map"></i></a>
                                    <?php endif; ?>
                                    <?php if(!empty($booking->drop_location)): ?>
                                        <a title="<?php echo e($booking->drop_location); ?>"
                                           target="_blank"
                                           href="https://www.google.com/maps/place/<?php echo e($booking->drop_location); ?>" class="btn btn-icon btn-danger ml-20"><i class="icon fa-tint"></i></a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a target="_blank" title="<?php echo app('translator')->get("$string_file.service_detail"); ?>"
                                       href="<?php echo e(route('merchant.booking.details',$booking->id)); ?>"
                                       class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                        <span class="fa fa-info-circle" title="<?php echo app('translator')->get("$string_file.service_detail"); ?>"></span>
                                    </a>
                                </td>
                            </tr>
                            <?php $sr++; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $bookings, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <?php endif; ?>

                <?php if(!empty($food_grocery_orders)): ?>
                    <div class="panel-body container-fluid">
                        <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                            <thead>
                            <tr>
                                <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.order_id"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.product_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.payment_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            </tr>
                            </thead>
                            <tbody>

                                <?php $sr = $food_grocery_orders->firstItem(); $user_name = ''; $user_phone = ''; $user_email = '';
                                                        $driver_name = '';$driver_email = '';
                                ?>
                                <?php $__currentLoopData = $food_grocery_orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                 $user_name = is_demo_data($order->User->UserName,$order->Merchant);
                                 $user_phone = is_demo_data($order->User->UserPhone,$order->Merchant);
                                 $user_email = is_demo_data($order->User->email,$order->Merchant);
                                    ?>
                                    <tr>
                                        <td><?php echo e($sr); ?></td>
                                        <td><?php echo e($order->merchant_order_id); ?></td>
                                        <td>
                                            <?php $product_detail = $order->OrderDetail; $products = "";?>
                                            <?php $__currentLoopData = $product_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                 <?php $weight =  isset($product->ProductVariant->weight) ? $product->ProductVariant->weight : "";
                                             $unit = isset($product->weight_unit_id) ? $product->WeightUnit->WeightUnitName : "";
                                             $unit = !empty($weight)  ? $product->quantity.' x '.$weight.' '.$unit : $product->quantity.$unit;
                                        ?>
                                                <?php echo e($product->quantity.' '.$unit.' '.$product->Product->Name($order->merchant_id)); ?>,<br>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td>
                                            <?php echo e(trans("$string_file.mode").": ". $order->PaymentMethod->payment_method); ?><br>
                                            <?php echo e(trans($string_file.".cart_amount").': '.$order->cart_amount); ?> <br>
                                            <?php echo e(trans("$string_file.delivery_charge").': '. ($order->final_amount_paid - $order->cart_amount)); ?> <br>
                                            <?php echo app('translator')->get("$string_file.grand_total"); ?> :  <?php echo e($order->CountryArea->Country->isoCode.' '.$order->final_amount_paid); ?>

                                            <br>
                                        </td>

                                        <td>
                                            <?php echo e($user_name); ?> <br>
                                            <?php echo e($user_phone); ?> <br>
                                            <?php echo e($user_email); ?> <br>
                                        </td>

                                        <td>
                                            <?php echo app('translator')->get("$string_file.at"); ?> <?php echo e(date_format($order->created_at,'H:i a')); ?>

                                            <br>
                                            <?php echo e(date_format($order->created_at,'D, M d, Y')); ?>

                                        </td>
                                        <td style="text-align: center">
                                            <?php if($order->order_status == 11): ?>
                                                <span class="badge badge-success font-weight-100"><?php echo e($arr_status[$order->order_status]); ?></span>
                                            <?php elseif(in_array($order->order_status,[1,6,7,9,10])): ?>
                                                <span class="badge btn-info font-weight-100"><?php echo e($arr_status[$order->order_status]); ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-danger font-weight-100"><?php echo e($arr_status[$order->order_status]); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a title=""
                                               href="<?php echo e(route('driver.order.detail',$order->id)); ?>"
                                               class="btn btn-sm btn-info menu-icon btn_money action_btn">
                                                <span class="fa fa-info-circle" title="<?php echo app('translator')->get("$string_file.order_details"); ?>"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $sr++  ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $food_grocery_orders, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        
                    </div>
                <?php endif; ?>
                <?php if(!empty($handyman_orders)): ?>
                    <div class="panel-body container-fluid">
                        <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.order_id"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.user_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.service_detail"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.current_status"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.pickup_drop"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = $handyman_orders->firstItem();
                            $user_name = ''; $user_phone = ''; $user_email = '';
                            $driver_name = '';$driver_email = '';
                            ?>
                            <?php $__currentLoopData = $handyman_orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>
                                    <td><?php echo e($order->merchant_order_id); ?></td>
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
                                    <td>
                                        <?php $arr_services = []; $order_details = $order->HandymanOrderDetail;
                                        foreach($order_details as $details){
                                            $arr_services[] = $details->ServiceType->serviceName;
                                        }
                                        ?>
                                        <?php echo e(trans("$string_file.mode").': '.$order->PaymentMethod->payment_method); ?> <br>
                                        <?php echo e(trans("$string_file.amount_paid").': '.$order->CountryArea->Country->isoCode.' '.$order->cart_amount); ?> <br>
                                        <?php echo e(trans($string_file.'.booking').' '.trans("$string_file.date").': '.$order->booking_date); ?> <br>
                                        <?php echo e(trans("$string_file.service_type").': '); ?>

                                        <?php $__currentLoopData = $order_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($details->ServiceType->serviceName); ?>, <br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo app('translator')->get("$string_file.segment"); ?> : <strong><?php echo e($order->Segment->name); ?></strong>
                                        <br>
                                    </td>
                                    <td> <?php echo e($order->CountryArea->CountryAreaName); ?></td>
                                    <td style="text-align: center">
                                        <?php echo e($handyman_status[$order->order_status]); ?>

                                    </td>
                                    <td>
                                        <?php echo e($order->created_at->toDateString()); ?>

                                        <br>
                                        <?php echo e($order->created_at->toTimeString()); ?>

                                    </td>
                                    <td>
                                        <a title="<?php echo e($order->drop_location); ?>" target="_blank"
                                           href="https://www.google.com/maps/place/<?php echo e($order->drop_location); ?>"
                                           class="btn btn-icon btn-danger ml-20">
                                            <i class="icon fa-tint"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a target="_blank" title=""
                                           href="<?php echo e(route('merchant.handyman.order.detail',$order->id)); ?>" class="btn btn-sm btn-info menu-icon btn_money action_btn"><span
                                                    class="fa fa-info-circle" title="<?php echo app('translator')->get("$string_file.booking_details"); ?>"></span></a>
                                    </td>
                                </tr>
                                <?php $sr++  ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $handyman_orders, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/jobs.blade.php ENDPATH**/ ?>
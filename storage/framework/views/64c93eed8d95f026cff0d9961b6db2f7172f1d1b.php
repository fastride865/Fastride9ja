<?php $__env->startSection('content'); ?>
    <style>
        @media  print {
            body * {
                visibility: hidden;
            }
            #section-to-print, #section-to-print * {
                visibility: visible;
            }
        }
    </style>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('admin.send-invoice',$order->id)); ?>">
                            <button class="btn btn-icon btn-warning float-right" style="margin:10px;width:115px;"><i class="icon fa-send"></i>
                                <?php echo app('translator')->get("$string_file.invoice"); ?>
                            </button>
                        </a>
                        <button class="btn btn-icon btn-warning float-right print_invoice" style="margin:10px;width:115px;" ><i class="icon wb-print" aria-hidden="true"></i>
                            <?php echo app('translator')->get("$string_file.print"); ?>
                        </button>
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.booking_details"); ?> # <?php echo e($order->merchant_order_id); ?>

                    </h3>
                </header>
                <div id="section-to-print" class="panel">
                <div class="panel-body container-fluid printableArea">
                    <div class="row">
                        <div class="col-lg-6 col-xs-12">
                            <h5><?php echo app('translator')->get("$string_file.user_details"); ?> : - </h5>
                            <div class="card my-2 shadow  bg-white h-240">
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6">
                                        <img height="100" width="100" class="rounded-circle"
                                             src="<?php if($order->User->UserProfileImage): ?> <?php echo e(get_image($order->User->UserProfileImage,'user')); ?><?php else: ?><?php echo e(get_image()); ?><?php endif; ?>">
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <span class="font-size-20"><?php echo e($order->User->UserName); ?></span>
                                            <br>
                                            <?php if(Auth::user()->demo == 1): ?>
                                                <span title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</span>&nbsp;
                                                &nbsp;<?php echo e("********".substr($order->User->UserPhone,-3)); ?>

                                            <br>
                                                <span title="Phone"><?php echo app('translator')->get("$string_file.email"); ?>:</span>&nbsp;
                                                &nbsp;<?php echo e("********".substr($order->User->email,-3)); ?>

                                            <?php else: ?>
                                                <span title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</span>&nbsp;
                                                &nbsp;<?php echo e($order->User->UserPhone); ?>

                                                <br>
                                                <span title="Phone"><?php echo app('translator')->get("$string_file.email"); ?>:</span>&nbsp;
                                                &nbsp;<?php echo e($order->User->email); ?>

                                            <?php endif; ?>
                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row mt-10 ml-20">
                                    <div class="col-md-4 col-xs-4 pt-20 pl-30">
                                        <?php echo app('translator')->get("$string_file.address"); ?> :
                                    </div>
                                    <div class="col-md-8 col-xs-8">
                                        <address>
                                            <?php if($order->drop_location): ?>
                                                <?php echo e($order->drop_location); ?>

                                            <?php else: ?>
                                                <?php echo e($order->UserAddress->house_name); ?>,
                                                <?php echo e($order->UserAddress->floor); ?>

                                                <?php echo e($order->UserAddress->building); ?>

                                                <br>
                                                <?php echo e($order->UserAddress->address); ?>

                                            <?php endif; ?>
                                        </address>
                                        <br>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php $currency= $order->CountryArea->Country->isoCode; ?>
                        <div class="col-lg-6 col-xs-12">
                            <h5><?php echo app('translator')->get("$string_file.other_details"); ?> : - </h5>
                            <div class="card my-2 shadow  bg-white h-240">
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6 text-info">
                                        <i class="icon fa-money"></i> <?php echo app('translator')->get("$string_file.payment"); ?>
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <span class="font-size-20"><?php echo e($order->PaymentMethod->payment_method); ?></span>
                                            <br>
                                            <span title=""><?php echo app('translator')->get("$string_file.grand_total"); ?>:</span>&nbsp;<?php echo e($currency.$order->final_amount_paid); ?>

                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6 text-info">
                                        <i class="icon fa-money"></i> <?php echo app('translator')->get("$string_file.price_type"); ?>
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <?php $arr_price_type = get_price_card_type("web","BOTH",$string_file); ?>
                                            <span class="font-size-20"><?php echo e(isset($arr_price_type[$order->price_type]) ? $arr_price_type[$order->price_type] : ""); ?></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 ml-30">
                                    <div class="col-md-4 col-xs-6 text-info">
                                        <i class="icon fa-comments"></i> <?php echo app('translator')->get("$string_file.current_status"); ?>
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <?php echo e($arr_status[$order->order_status]); ?>

                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 ml-30">
                                    <div class="col-md-4 col-xs-6 text-success">
                                        <i class="icon fa-calendar fa-2x text-gray-300"></i><?php echo app('translator')->get("$string_file.time_slot"); ?>
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <?php echo e($order->ServiceTimeSlotDetail->slot_time_text); ?>

                                            , <?php echo date(getDateTimeFormat($order->Merchant->datetime_format,2),strtotime($order->booking_date)); ?>

                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row p-3 ml-30">
                                    <div class="col-md-4 col-xs-6 text-success">
                                        <i class="icon fa-calendar fa-2x text-gray-300"></i> <?php echo app('translator')->get("$string_file.created_at"); ?>
                                    </div>
                                    <?php $created_at = $order->created_at; ?>
                                    <?php if(!empty($order->CountryArea->timezone)): ?>
                                        <?php $created_at = convertTimeToUSERzone($created_at, $order->CountryArea->timezone, null, $order->Merchant); ?>
                                    <?php endif; ?>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <?php echo $created_at; ?>

                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="row mt-10 ml-20">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php if(in_array($order->order_status,[4,6,7])): ?>
                        <div class="col-lg-6 col-xs-12">
                            <h5><?php echo app('translator')->get("$string_file.driver_details"); ?> : - </h5>
                            <div class="card my-2 shadow  bg-white h-240">
                                <div class="row p-3 mt-30 ml-30">
                                    <div class="col-md-4 col-xs-6">
                                        <img height="100" width="100" class="rounded-circle"
                                             src="<?php if($order->Driver->profile_image): ?> <?php echo e(get_image($order->Driver->profile_image,'driver')); ?><?php else: ?><?php echo e(get_image()); ?><?php endif; ?>">
                                    </div>
                                    <div class="col-md-8 col-xs-6">
                                        <p>
                                            <span class="font-size-20"><?php echo e($order->Driver->first_name.' '.$order->Driver->last_name); ?></span>
                                            <br>
                                            <?php if(Auth::user()->demo == 1): ?>
                                                <span title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</span>&nbsp;
                                                &nbsp;<?php echo e("********".substr($order->Driver->phoneNumber,-3)); ?>

                                            <br>
                                                <span title="Phone"><?php echo app('translator')->get("$string_file.email"); ?>:</span>&nbsp;
                                                &nbsp;<?php echo e("********".substr($order->Driver->email,-3)); ?>

                                            <?php else: ?>
                                                <span title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</span>&nbsp;
                                                &nbsp;<?php echo e($order->Driver->phoneNumber); ?>

                                                <br>
                                                <span title="Phone"><?php echo app('translator')->get("$string_file.email"); ?>:</span>&nbsp;
                                                &nbsp;<?php echo e($order->Driver->email); ?>

                                            <?php endif; ?>
                                            <br>
                                        </p>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <h5><?php echo app('translator')->get("$string_file.service_details"); ?> : - </h5>

                    <div class="page-invoice-table table-responsive">
                        <table class="table table-hover text-right">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th><?php echo app('translator')->get("$string_file.service_type"); ?></th>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.quantity"); ?></th>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.price"); ?></th>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.amount"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sn = 1 ?>
                            <?php $__currentLoopData = $order->HandymanOrderDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $services): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center">
                                        <?php echo e($sn); ?>

                                    </td>
                                    <td class="text-center">
                                        <?php echo e($services->ServiceType->ServiceName($order->merchant_id)); ?>

                                    </td>
                                    <td>
                                        <?php echo e($services->quantity); ?>

                                    </td>
                                    <td>
                                        <?php echo e($services->price); ?>

                                    </td>
                                    <td>
                                        <?php echo e($services->total_amount); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php $col_span = 4; ?>
                            <?php if($order->price_type == 2 && $order->order_status != 7 && $order->is_order_completed !=1): ?>
                                <tr>
                                    <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.cart_amount"); ?></td>
                                    <td><?php echo e($currency.$order->hourly_amount.' '.trans("$string_file.hourly")); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo e($col_span); ?>"><b><?php echo app('translator')->get("$string_file.note"); ?></b></td>
                                    <td><b><?php echo e(trans("$string_file.handyman_order_payment")); ?></b></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.tax"); ?></td>
                                    <td><?php echo e(trans("$string_file.tax").' : '.$order->tax_per); ?> % <br>
                                        <?php echo e(trans("$string_file.tax")); ?>

                                        : <?php echo e($currency.$order->tax); ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.cart_amount"); ?></td>
                                    <td><?php echo e($currency.$order->cart_amount); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.total_booking_amount"); ?></td>
                                    <td><?php echo e($currency.$order->total_booking_amount); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.minimum_booking_amount"); ?></td>
                                    <td><?php echo e($currency.$order->minimum_booking_amount); ?> (<?php echo app('translator')->get("$string_file.tax_included"); ?>
                                        )
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="<?php echo e($col_span); ?>"><?php echo app('translator')->get("$string_file.final_amount_paid"); ?> </td>
                                    <td><?php echo e($currency.$order->final_amount_paid); ?></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(!empty($order->DriverGallery) && $order->DriverGallery->count() > 0): ?>
                        <?php $images = $order->DriverGallery; ?>
                        <h5><?php echo app('translator')->get("$string_file.booking_image"); ?> : - </h5>
                        <div class="row">
                            <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-2">
                                    <div class="example">
                                        <img class="rounded" width="150" height="150"
                                             src="<?php echo e(get_image($image->image_title,'driver_gallery')); ?>" alt="...">
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/jquery.PrintArea.js')); ?>" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $(".print_invoice").click(function(){
                var mode = 'popup'; //popup
                var close = mode == "popup";
                var options = { mode : mode, popClose : true, popHt : 900, popWd: 900, };
                $(".printableArea").printArea( options );
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/handyman-order/order-detail.blade.php ENDPATH**/ ?>
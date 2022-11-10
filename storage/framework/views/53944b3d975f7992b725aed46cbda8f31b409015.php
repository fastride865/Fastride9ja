<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                      <button class="btn btn-icon btn-warning float-right print_invoice" style="margin:10px;width:115px;" ><i class="icon wb-print" aria-hidden="true"></i>
                          <?php echo app('translator')->get("$string_file.print"); ?>
                      </button>
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                      <?php echo app('translator')->get("$string_file.invoice"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid printableArea">
                    <div class="row">
                        <div class="col-lg-3">
                            <span><img height="60" width="100" src="<?php echo e(get_image($business_segment->business_logo,'business_logo',$business_segment->merchant_id,true)); ?>" alt="...">
                               <br> <?php echo e($business_segment->full_name); ?>,
                            </span>
                            <address>
                                <?php echo e($business_segment->address); ?>

                                <br>
                                <abbr title="Mail"><?php echo app('translator')->get("$string_file.email"); ?>:</abbr>&nbsp;&nbsp;<?php echo e($business_segment->email); ?>

                                <br>
                                <abbr title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</abbr>&nbsp;&nbsp;<?php echo e($business_segment->phone_number); ?>

                                <br>
                            </address>
                        </div>
                        <div class="col-lg-3 offset-lg-6 text-right">
                            <h4><?php echo app('translator')->get("$string_file.order_invoice"); ?></h4>
                            <p>
                                <a class="font-size-20" href="javascript:void(0)">#<?php echo e($order->merchant_order_id); ?></a>
                                <br> <?php echo app('translator')->get("$string_file.f_cap_to"); ?>:
                                <br>
                                <?php if($hide_user_info_from_store == 1): ?>
                                   ******
                                <?php else: ?>
                                <span class="font-size-20"><?php echo e(is_demo_data($order->User->first_name.' '.$order->User->last_name, $order->Merchant)); ?></span>
                                <?php endif; ?>
                            </p>
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
                                <br>
                                <abbr title="Phone"><?php echo app('translator')->get("$string_file.phone"); ?>:</abbr>&nbsp;&nbsp;
                               <?php if($hide_user_info_from_store == 1): ?>
                                   ******
                                <?php else: ?>
                                   <?php echo e(is_demo_data($order->User->UserPhone, $order->Merchant)); ?>

                                <?php endif; ?>
                                <br>
                            </address>
                            <span><?php echo app('translator')->get("$string_file.date"); ?> : <?php echo e(date(getDateTimeFormat($order->Merchant->datetime_format,2))); ?></span>
                            <br>

                        </div>
                    </div>
                    <h3><?php echo app('translator')->get("$string_file.product_details"); ?></h3>
                    <hr>
                    <div class="page-invoice-table table-responsive">
                        <table class="table table-hover text-right">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center"><?php echo app('translator')->get("$string_file.product_name"); ?></th>
                                <th class="text-center"><?php echo app('translator')->get("$string_file.product_variant"); ?></th>
                                <th class="text-center"><?php echo app('translator')->get("$string_file.description"); ?></th>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.quantity"); ?></th>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.price"); ?></th>
                                <?php if($order->Segment->slag =="FOOD"): ?>
                                    <th class="text-right"><?php echo app('translator')->get("$string_file.option_amount"); ?></th>
                                <?php endif; ?>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.discount"); ?></th>
                                <th class="text-right"><?php echo app('translator')->get("$string_file.amount"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sn = 1;$tax =0; $tip=0; $currency = $order->CountryArea->Country->isoCode;
                            $arr_option_amount
                            = []; $option_amount = []; ?>
                            <?php $__currentLoopData = $order->OrderDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $lang = $product->Product->langData($order->merchant_id);
                                 $tax =  !empty($order->tax) ? $order->tax : 0;
                                 $tip =  !empty($order->tip_amount) ? $order->tip_amount : 0;
                                ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo e($sn); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($lang->name); ?>

                                    <?php if(!empty($product->options)): ?>
                                        <?php echo e('('); ?>

                                        <?php  $arr_cart_option = !empty($product->options) ? json_decode($product->options,true) : []; ?>
                                        <?php $__currentLoopData = $arr_cart_option; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                                            <?php $arr_option_amount[] = $option['amount'];
                                                $option_amount[] = $option['amount'];
                                            ?>
                                            <?php echo e($option['option_name']); ?>,

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e(')'); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo e($product->ProductVariant->Name($order->merchant_id)); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($lang->description); ?>

                                </td>
                                <td>
                                    <?php echo e($product->quantity); ?>

                                </td>
                                <td>
                                    <?php echo e($product->price); ?>

                                </td>
                                <?php if($order->Segment->slag =="FOOD"): ?>
                                    <td>
                                        <?php echo e(array_sum($option_amount)); ?>

                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php echo e($product->ProductVariant->discount); ?>

                                </td>
                                <td>
                                    <?php echo e($product->total_amount); ?>

                                </td>
                            </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="text-right clearfix">
                        <div class="float-right">
                            <p><?php echo app('translator')->get("$string_file.cart_amount"); ?> :
                                <span><?php echo e($currency.$order->cart_amount); ?></span>
                            </p>
                            <p><?php echo app('translator')->get("$string_file.delivery_charge"); ?> :
                                <span><?php echo e($currency.$order->delivery_amount); ?></span>
                            </p>
                            <p><?php echo app('translator')->get("$string_file.tax"); ?> :
                                <span><?php echo e($currency.$tax); ?></span>
                            </p>
                            <p><?php echo app('translator')->get("$string_file.tip"); ?> :
                                <span><?php echo e($currency.$tip); ?></span>
                            </p>
                            <p class="page-invoice-amount"><?php echo app('translator')->get("$string_file.grand_total"); ?>:
                                <span><?php echo e($currency.$order->final_amount_paid); ?></span>
                            </p>
                        </div>
                    </div>











                </div>
            </div>
        </div>
    </div>
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

<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/order/invoice.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($redirect_route)): ?>
                            <div class="btn-group float-md-right">
                                <a href="<?php echo e($redirect_route); ?>">
                                    <button type="button" class="btn btn-icon btn-success mr-1" style="margin:10px  "><i class="wb-reply"></i></button>
                                </a>
                            </div>
                            <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="fa-user" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.order_details"); ?> # <?php echo e($order->merchant_order_id); ?>

                    </h3>
                </header>
              <?php echo $__env->make('common-view.order-detail', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/order-detail.blade.php ENDPATH**/ ?>
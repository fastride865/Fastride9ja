<?php $__env->startSection('content'); ?>
    <style>
        .a_text{
            color: #76838f;
        }
    </style>
    <div class="page">
        <div class="page-content">
            <div class="mr--10 ml--10">
                <div class="row" style="margin-right: 0rem;margin-left: 0rem">
                    <!-- First Row -->
                        <div class="col-12 col-md-12 col-sm-12">
                            <!-- Example Panel With Heading -->
                            <div class="panel panel-bordered">
                                <div class="panel-heading">
                                    <div class="panel-actions"></div>
                                    <h3 class="panel-title"><?php echo app('translator')->get("$string_file.site_statistics"); ?></h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="<?php echo e(route("business-segment.earning")); ?>">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-primary"
                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="fa fa-money"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400"><?php echo app('translator')->get("$string_file.earning"); ?></span>

                                                            <span class="font-size-18 font-weight-100 pl-100"><?php echo e($earnings); ?></span>

                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="#">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-primary"
                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="fa fa-money"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400"><?php echo app('translator')->get("$string_file.wallet_money"); ?></span>

                                                            <span class="font-size-18 font-weight-100 pl-100"><?php echo e($wallet_money); ?></span>

                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-xl-6 col-md-6 col-sm-6 info-panel">
                                            <a href="<?php echo e(route('business-segment.product.index')); ?>">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-warning"
                                                                style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="fa fa-window-maximize"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400"><?php echo app('translator')->get("$string_file.products"); ?></span>

                                                            <span class="font-size-18 font-weight-100 pl-100"><?php echo e($products); ?></span>

                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Example Panel With Heading -->
                        <div class="col-12 col-md-12 col-sm-12">
                            <!-- Example Panel With Heading -->
                            <div class="panel panel-bordered">
                                <div class="panel-heading">
                                    <div class="panel-actions"></div>
                                    <h3 class="panel-title"><?php echo app('translator')->get("$string_file.order_statistics"); ?> (<?php echo app('translator')->get("$string_file.total"); ?> : <?php echo e($all_orders); ?>)  </h3>
                                </div>
                                <div class="panel-body">






























                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-warning"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-road"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400"><?php echo app('translator')->get("$string_file.new"); ?></span>
                                                        <span class="font-size-14 font-weight-100 pl-100"><?php echo e($new_orders); ?></span>
                                                        <a href="<?php echo e(route('business-segment.today-order')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.today"); ?> : <?php echo e($today_orders); ?></span></a>
                                                        <?php if($segment_slug !="FOOD"): ?>
                                                         <a href="<?php echo e(route('business-segment.upcoming-order')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.upcoming"); ?> : <?php echo e($upcoming_orders); ?></span></a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">

                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-success"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-circle-o-notch"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400"><?php echo app('translator')->get("$string_file.on_going"); ?></span>
                                                            <span class="font-size-14 font-weight-100 pl-100"><?php echo e($on_going_orders); ?></span>
                                                        <a href="<?php echo e(route('business-segment.pending-process-order')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.process"); ?> : <?php echo e($pending_process_orders); ?></span></a>
                                                        <a href="<?php echo e(route('business-segment.pending-pick-order-verification')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.verification"); ?> : <?php echo e($pending_verification); ?></span></a>
                                                        <a href="<?php echo e(route('business-segment.order-ontheway')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.ontheway"); ?> : <?php echo e($ontheway); ?></span></a>
                                                    </div>
                                                </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
                                                <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                    <div class="card-block bg-white p-20">
                                                        <button type="button" class="btn btn-floating btn-sm btn-danger"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                            <i class="icon fa-times"></i>
                                                        </button>
                                                        <span class="ml-10 font-weight-400"><?php echo app('translator')->get("$string_file.expired"); ?></span>
                                                        <span class="font-size-14 font-weight-100 pl-100"><?php echo e($total_expired_orders); ?></span>
                                                        <a href="<?php echo e(route('business-segment.rejected-order')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.rejected"); ?> : <?php echo e($rejected_orders); ?></span></a>
                                                        <a href="<?php echo e(route('business-segment.cancelled-order')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.cancelled"); ?> : <?php echo e($cancelled_orders); ?></span></a>
                                                        <a href="<?php echo e(route('business-segment.expired-order')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.auto_expired"); ?> : <?php echo e($auto_expired_orders); ?></span></a>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 info-panel">
                                            <div class="card card-shadow" style="margin-bottom:0.243rem">
                                                <div class="card-block bg-white p-20">
                                                    <button type="button" class="btn btn-floating btn-sm btn-success"  style="box-shadow:0 4px 1px rgba(0,0,0,.63)">
                                                        <i class="icon fa-history"></i>
                                                    </button>
                                                    <span class="ml-10 font-weight-400"><?php echo app('translator')->get("$string_file.history"); ?></span>
                                                    <span class="font-size-14 font-weight-100 pl-100"><?php echo e($history_orders); ?></span>
                                                    <a href="<?php echo e(route('business-segment.delivered-order')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.delivered"); ?> : <?php echo e($delivered_orders); ?></span></a>
                                                    <a href="<?php echo e(route('business-segment.completed-order')); ?>"><span class="font-size-14 font-weight-100 pl-100 a_text"><?php echo app('translator')->get("$string_file.completed"); ?> : <?php echo e($completed_orders); ?></span></a>

                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/dashboard.blade.php ENDPATH**/ ?>
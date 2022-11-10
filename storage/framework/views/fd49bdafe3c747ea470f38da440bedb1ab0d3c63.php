<?php
    $business_segment = get_business_segment(false);
    //$order_request_receiver = $business_segment->order_request_receiver;
?>
<div class="site-menubar">
    <div class="site-menubar-body">
        <div>
            <div>
                <ul class="site-menu" data-plugin="menu">
                    
                    <li class="site-menu-item">
                        <a href="<?php echo e(route('business-segment.dashboard')); ?>">
                            <i class="site-menu-icon wb-dashboard" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.dashboard"); ?></span>
                        </a>
                    </li>
                    <li class="site-menu-category" id="general-title"><?php echo app('translator')->get("$string_file.product_management"); ?></li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.product.index')); ?>">
                            <i class="site-menu-icon fa fa-product-hunt" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.product"); ?></span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.product.inventory.index')); ?>">
                            <i class="site-menu-icon fa fa-list-ol" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.product_inventory"); ?></span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.style-segment.index')); ?>">
                            <i class="site-menu-icon fa fa-sort-desc" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.style_segment"); ?></span>
                        </a>
                    </li>
                    <?php if($business_segment->Segment->slag == "FOOD"): ?>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.option.index')); ?>">
                            <i class="site-menu-icon fa fa-optin-monster" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.options"); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="site-menu-category" id="general-title"><?php echo app('translator')->get("$string_file.order_management"); ?></li>






                    <li class="site-menu-item has-sub">
                        <a href="javascript:void(0)">
                            <i class="site-menu-icon fa fa-map-marker" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.new_orders"); ?> </span>
                            <span class="site-menu-arrow"></span>
                        </a>
                        <ul class="site-menu-sub">
                            <li class="site-menu-item">
                                <a class="animsition-link" href="<?php echo e(route('business-segment.today-order')); ?>">
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.today_order"); ?> </span>
                                </a>
                            </li>
                            <?php if($business_segment->Segment->slag != "FOOD"): ?>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="<?php echo e(route('business-segment.upcoming-order')); ?>">
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.upcoming_order"); ?></span>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="site-menu-item has-sub">
                        <a href="javascript:void(0)">
                            
                            <i class="site-menu-icon fa fa-map-marker" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.ongoing_orders"); ?> </span>
                            <span class="site-menu-arrow"></span>
                        </a>
                        <ul class="site-menu-sub">
                            <li class="site-menu-item">
                                <a class="animsition-link" href="<?php echo e(route('business-segment.pending-process-order')); ?>">
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.pending_process"); ?> </span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link"
                                   href="<?php echo e(route('business-segment.pending-pick-order-verification')); ?>">
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.pending_pickup_verification"); ?> </span>
                                </a>
                            </li>
                            <li class="site-menu-item">
                                <a class="animsition-link" href="<?php echo e(route('business-segment.order-ontheway')); ?>">
                                    <span class="site-menu-title"><?php echo app('translator')->get("$string_file.ontheway_orders"); ?> </span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.delivered-order')); ?>">
                            <i class="site-menu-icon fa fa-truck" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.delivered_orders"); ?></span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.completed-order')); ?>">
                            <i class="site-menu-icon fa fa-paypal" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.completed_orders"); ?> </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.cancelled-order')); ?>">
                            <i class="site-menu-icon fa fa-crosshairs" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.cancelled_orders"); ?> </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.rejected-order')); ?>">
                            <i class="site-menu-icon fa fa-recycle" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.rejected_orders"); ?> </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.expired-order')); ?>">
                            <i class="site-menu-icon fa fa-recycle" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.auto_expired_orders"); ?> </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.order')); ?>">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.all_orders"); ?> </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.statistics',$business_segment->id)); ?>">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.order_statistics"); ?>  </span>
                        </a>
                    </li>
                    <li class="site-menu-category" id="general-title"><?php echo app('translator')->get("$string_file.transaction_management"); ?>  </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.wallet')); ?>">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.wallet_transaction"); ?>  </span>
                        </a>
                    </li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.cashouts')); ?>">
                            <i class="site-menu-icon fa fa-list" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.cashout_request"); ?>  </span>
                        </a>
                    </li>
                    <li class="site-menu-category" id="general-title"><?php echo app('translator')->get("$string_file.reports"); ?></li>
                    <li class="site-menu-item">
                        <a class="animsition-link" href="<?php echo e(route('business-segment.earning')); ?>">
                            <i class="site-menu-icon fa fa-money" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.earning"); ?></span>
                        </a>
                    </li>
                    <li class="site-menu-category" id="general-title"><?php echo app('translator')->get("$string_file.configuration"); ?></li>
                    <?php if($business_segment->Segment->slag == "FOOD"): ?>
                    <li class="site-menu-item">
                        <a href="<?php echo e(route('business-segment.configurations')); ?>">
                            <i class="site-menu-icon wb-settings" aria-hidden="true"></i>
                            <span class="site-menu-title"><?php echo app('translator')->get("$string_file.configuration"); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/element/sidebar.blade.php ENDPATH**/ ?>
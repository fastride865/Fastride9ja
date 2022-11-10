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
                        <?php if($price_card_owner_config == 1): ?>
                        <a href="<?php echo e(route('segment.price_card.add')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus" title="<?php echo app('translator')->get("$string_file.add_price_card"); ?>"></i>
                            </button>
                        </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.handyman_services_price_card"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $__env->make('merchant.segment-pricecard.search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <?php if($price_card_owner_config == 2): ?>
                                <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?></th>

                            <th><?php echo app('translator')->get("$string_file.type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.minimum_booking_amount"); ?></th>
                            <?php if($price_type_config == "HOURLY" || $price_type_config == "BOTH"): ?>
                            <th><?php echo app('translator')->get("$string_file.hourly_charges"); ?></th>
                            <?php endif; ?>
                            <?php if($price_type_config == "FIXED" || $price_type_config == "BOTH"): ?>
                            <th><?php echo app('translator')->get("$string_file.service_charges"); ?>(<?php echo app('translator')->get("$string_file.fixed"); ?>)</th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <?php if($price_card_owner_config == 1): ?>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $arr_price_card->firstItem();
                        ?>
                        <?php $__currentLoopData = $arr_price_card; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price_card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php echo e($price_card->CountryArea->CountryAreaName); ?>

                                </td>
                                <?php if($price_card_owner_config == 2): ?>
                                    <td>
                                        <?php if(!empty($price_card->driver_id)): ?>
                                            <?php echo e($price_card->Driver->first_name.' '.$price_card->Driver->last_name); ?>, <br>
                                            <?php echo e($price_card->Driver->phoneNumber); ?>

                                         <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td><?php echo e(!empty($price_card->Segment->Name($price_card->merchant_id)) ? $price_card->Segment->Name($price_card->merchant_id) : $price_card->Segment->slag); ?></td>

                                <td><?php echo e(isset($arr_price_type[$price_card->price_type]) ? $arr_price_type[$price_card->price_type] : ""); ?></td>
                                <td><?php echo e($price_card->minimum_booking_amount); ?></td>
                                <?php if($price_type_config == "HOURLY" || $price_type_config == "BOTH"): ?>
                                <td>
                                    <?php if($price_card->price_type == 2): ?>
                                    <?php echo app('translator')->get("$string_file.per_hour"); ?> <?php echo e($price_card->amount); ?>

                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <?php if($price_type_config == "FIXED" || $price_type_config == "BOTH"): ?>
                                <td>
                                    <?php if($price_card->price_type == 1): ?>
                                        <?php $__currentLoopData = $price_card->SegmentPriceCardDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price_card_details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e(!empty($price_card_details->ServiceType->serviceName($price_card->merchant_id)) ? $price_card_details->ServiceType->serviceName($price_card->merchant_id) : $price_card_details->ServiceType->serviceName); ?>  => <?php echo e($price_card_details->amount); ?>,
                                            <br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.same_for_all_services"); ?>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <td>
                                    <?php if($price_card->status == 1): ?>

                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php if($price_card_owner_config == 1): ?>
                                <td>
                                <a href="<?php echo e(route('segment.price_card.add',$price_card->id)); ?>"
                                   data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip" data-placement="top"
                                   class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                            class="fa fa-edit"></i>
                                </a>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $arr_price_card, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/segment-pricecard/index.blade.php ENDPATH**/ ?>
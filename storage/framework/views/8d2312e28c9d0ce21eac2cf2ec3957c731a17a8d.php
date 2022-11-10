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
                            <a href="<?php echo e(route('food-grocery.price_card.add',[$price_card_for])); ?>">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="<?php echo app('translator')->get("$string_file.add_price_card"); ?>"></i>
                                </button>
                            </a>
                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                       <?php if($price_card_for == 1): ?>
                           <?php $prefix = trans($string_file.'.driver');?>
                       <?php elseif($price_card_for == 2): ?>
                            <?php $prefix = trans("$string_file.user");?>
                       <?php else: ?>
                            <?php $prefix = "";?>
                       <?php endif; ?>
                        <?php echo e($prefix); ?> <?php echo app('translator')->get("$string_file.price_card"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_type"); ?></th>
                            <?php if($price_card_for == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.pickup_amount"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.drop_off_amount"); ?></th>
                            <?php endif; ?>
                            <?php if($price_card_for == 2): ?>
                                <th><?php echo app('translator')->get("$string_file.tax"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.slab"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
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
                                <td><?php echo e(!empty($price_card->Segment->Name($price_card->merchant_id)) ? $price_card->Segment->Name($price_card->merchant_id) : $price_card->Segment->slag); ?></td>
                                <td><?php echo e($price_card->ServiceType->serviceName); ?></td>
                                <?php if($price_card_for == 1): ?>
                                    <td><?php echo e($price_card->pick_up_fee); ?></td>
                                    <td><?php echo e($price_card->drop_off_fee); ?></td>
                                <?php endif; ?>
                                <?php if($price_card_for == 2): ?>
                                    <td><?php echo e(!empty($price_card->tax) ? $price_card->tax .'%': NULL); ?></td>
                                <?php endif; ?>
                                <td>
                                    <?php $__currentLoopData = $price_card->PriceCardDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $country = $price_card->CountryArea->Country;
                                              $unit = $country->distance_unit ==1 ? trans("$string_file.km") : trans("$string_file.miles")
                                        ?>
                                        <?php echo e(($key+1).') '. $detail->distance_from.'-'.$detail->distance_to.$unit.'=>'.$country->isoCode.$detail->slab_amount); ?>

                                        <br>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td>
                                    <?php if($price_card->status == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('food-grocery.price_card.add',[$price_card->price_card_for,$price_card->id])); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip" data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $arr_price_card, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/food-grocery-pricecard/index.blade.php ENDPATH**/ ?>
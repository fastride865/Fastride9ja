<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content container-fluid">
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
                        <a href="<?php echo e(route('excel.promocode')); ?>">
                            <button type="button" class="btn btn-icon btn-primary float-right"
                                    style="margin: 10px;">
                                <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                            </button>
                        </a>
                        <a href="<?php echo e(route('promocode.create')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right"
                                    style="margin: 10px;">
                                <i class="wb-plus"
                                   title="<?php echo app('translator')->get("$string_file.promo_code"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-percent" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.promo_code"); ?>  <?php echo app('translator')->get("$string_file.management"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.promo_code"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.promo_code_parameter"); ?> </th>
                            
                            <th><?php echo app('translator')->get("$string_file.description"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.discount"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.validity"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.start_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.end_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.limit"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.limit_per_user"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.applicable_for"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $promocodes->firstItem() ?>
                        <?php $__currentLoopData = $promocodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promocode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($promocode->promoCode); ?></td>
                                <td><?php echo e(!empty($promocode->country_area_id) ? $promocode->CountryArea->CountryAreaName : ""); ?></td>
                                <td><?php echo e(($promocode->segment_id != "") ? $segment_list[$promocode->segment_id] : "---"); ?></td>
                                <td><?php if(!empty($promocode->LanguageSingle)): ?>
                                        <?php echo e($promocode->LanguageSingle->promo_code_name); ?>

                                    <?php elseif(!empty($promocode->LanguageAny )): ?>
                                        <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                        <span class="text-primary">( In <?php echo e($promocode->LanguageAny->LanguageName->name); ?>

                                                                : <?php echo e($promocode->LanguageAny->promo_code_name); ?>

                                                                )</span>
                                    <?php else: ?>
                                        <span class="text-primary">------</span>
                                    <?php endif; ?>
                                </td>
                                <?php $a = array(); ?>
                                <?php $__currentLoopData = $promocode->PriceCard; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pricecard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $a[] = $pricecard->price_card_name; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                
                                
                                
                                
                                
                                <td>
                                    <span class="long_text"><?php echo e($promocode->promo_code_description); ?></span>
                                </td>
                                <td>
                                    <?php if($promocode->promo_code_value_type == 1): ?>
                                        <?php echo e($promocode->CountryArea->Country->isoCode." ".$promocode->promo_code_value); ?>

                                    <?php else: ?>
                                        <?php echo e($promocode->promo_code_value); ?> %
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($promocode->promo_code_validity == 1): ?>
                                        <?php echo app('translator')->get("$string_file.permanent"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.custom"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($promocode->start_date == ""): ?>
                                        -----
                                    <?php else: ?>
                                        <?php echo e($promocode->start_date); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($promocode->end_date == ""): ?>
                                        -----
                                    <?php else: ?>
                                        <?php echo e($promocode->end_date); ?>

                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($promocode->promo_code_limit); ?></td>
                                <td><?php echo e($promocode->promo_code_limit_per_user); ?></td>
                                <td>
                                    <?php if($promocode->applicable_for == 1): ?>
                                        <?php echo app('translator')->get("$string_file.all_users"); ?>
                                    <?php elseif($promocode->applicable_for == 2): ?>
                                        <?php echo app('translator')->get("$string_file.new_user"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.corporate_users"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($promocode->promo_code_status == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php $created_at = convertTimeToUSERzone($promocode->created_at, $promocode->CountryArea->timezone, null, $promocode->Merchant, 2); ?>
                                <td><?php echo $created_at; ?></td>
                                <td style="width:200px">
                                    <a href="<?php echo e(route('promocode.create',$promocode->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                        <i class="fa fa-edit"></i> </a>
                                    <?php if($promocode->promo_code_status == 1): ?>
                                        <a href="<?php echo e(route('merchant.promocode.active-deactive',['id'=>$promocode->id,'status'=>2])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i class="fa fa-eye-slash"></i> </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('merchant.promocode.active-deactive',['id'=>$promocode->id,'status'=>1])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                            <i class="fa fa-eye"></i> </a>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('merchant.promocode.delete',$promocode->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-danger menu-icon btn_delete action_btn">
                                        <i class="fa fa-trash"></i> </a>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $promocodes, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/promocode/index.blade.php ENDPATH**/ ?>
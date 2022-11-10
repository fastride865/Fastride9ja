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
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.payment_method_management"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.icon"); ?></th>
                            <?php if(Auth::user('merchant')->can('edit_payment_methods')): ?>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tfoot></tfoot>
                        <tbody>
                        <?php $sr = 1 ?>
                        <?php $__currentLoopData = $payment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($value->payment_method); ?></td>
                                <td> <?php if(!empty($value->PaymentMethodTranslation)): ?> <?php echo e($value->PaymentMethodTranslation->name); ?> <?php else: ?>
                                        ---  <?php endif; ?> </td>
                                <td>
                                    <?php
                                        $icon = get_image($value->payment_icon,'payment_icon',$merchant->id,false);
                                        $merchant_payment = $value->Merchant->where('id',$merchant->id);
                                        $merchant_payment = collect($merchant_payment->values());
                                        if(isset($merchant_payment) && !empty($merchant_payment[0]->pivot['icon']))
                                        {
                                            $icon = get_image($merchant_payment[0]->pivot['icon'],'p_icon',$merchant->id);
                                        }
                                    ?>
                                    <img src="<?php echo e($icon); ?>" height="50" width="50">
                                </td>
                                <?php if(Auth::user('merchant')->can('edit_payment_methods')): ?>
                                    <td>
                                        <a href="<?php echo e(route('merchant.paymentMethod.edit',$value->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i> </a>
                                    </td>
                                <?php endif; ?>
                                <?php $sr++ ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/payment_methods/index.blade.php ENDPATH**/ ?>
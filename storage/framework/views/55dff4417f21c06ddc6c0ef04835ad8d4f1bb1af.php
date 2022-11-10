<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("business-segment.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('merchant.option-type.add')); ?>">
                            <button type="button" , class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i title="<?php echo app('translator')->get("$string_file.add_option"); ?>" class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-list" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.option_type_management"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.type"); ?></th>
                            
                            <th><?php echo app('translator')->get("$string_file.charges_type"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.type"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.maximum_options_on_app"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1 ?>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php if(empty($option->LanguageOptionTypeSingle)): ?>
                                        <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                        <span class="text-primary">( In <?php echo e($option->LanguageOptionTypeAny->LanguageName->name); ?>

                                                                : <?php echo e($option->LanguageOptionTypeAny->type); ?>

                                                                )</span>
                                    <?php else: ?>

                                        <?php echo e($option->LanguageOptionTypeSingle->type); ?>

                                    <?php endif; ?>
                                </td>
                                
                                <td><?php echo e($option->charges_type == 1 ? trans("$string_file.free") : trans("$string_file.paid")); ?></td>
                                <td><?php echo e($option->select_type == 1 ? trans("$string_file.optional") : trans("$string_file.mandatory")); ?></td>
                                <td><?php echo e($option->max_option_on_app); ?></td>
                                <td>
                                    <?php if($option->status==1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="width:100px; float:left">
                                    <a href="<?php echo e(route('merchant.option-type.add',$option->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i>
                                    </a>
                                    <?php if($option->status==1): ?>
                                        <a href="<?php echo e(route('merchant.option-type.active-deactive',['id'=>$option->id,'status'=>2])); ?>">
                                            <button type="button" data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>"
                                                    data-toggle="tool-tip"
                                                    data-placement="top"
                                                    class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn mr-1">
                                                <i
                                                        class="fa fa-eye-slash"></i>
                                            </button>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('merchant.option-type.active-deactive',['id'=>$option->id,'status'=>1])); ?>">
                                            <button type="button" data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>"
                                                    data-toggle="tool-tip"
                                                    data-placement="top"
                                                    class="btn btn-sm btn-success menu-icon btn_eye action_button">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('merchant.option-type.delete',$option->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-danger menu-icon btn_delete action_btn"> <i
                                                class="fa fa-trash"></i> </a>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/option-type/index.blade.php ENDPATH**/ ?>
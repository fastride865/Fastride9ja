<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("business-segment.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('business-segment.option.add')); ?>">
                            <button type="button", class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i title="<?php echo app('translator')->get("$string_file.add_option"); ?>" class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-list" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.options_management"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.type"); ?></th>

                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1 ?>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo e($option->Name($option->business_segment_id)); ?></td>
                                <td><?php echo e($option->OptionType->Type($merchant_id)); ?></td>

                                <td>
                                    <?php if($option->status==1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="width:100px; float:left">
                                    <a href="<?php echo e(route('business-segment.option.add',$option->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i>
                                    </a>
                                    <?php if($option->status==1): ?>
                                        <a href="<?php echo e(route('business-segment.option.active-deactive',['id'=>$option->id,'status'=>2])); ?>">
                                        <button type="button" data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tool-tip"
                                            data-placement="top"
                                            class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn mr-1"> <i
                                                    class="fa fa-eye-slash"></i>
                                        </button></a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('business-segment.option.active-deactive',['id'=>$option->id,'status'=>1])); ?>">
                                        <button type="button" data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tool-tip"
                                                data-placement="top"
                                                class="btn btn-sm btn-success menu-icon btn_eye action_button">
                                            <i class="fa fa-eye"></i>
                                        </button></a>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('business-segment.option.delete',$option->id)); ?>"
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/option/index.blade.php ENDPATH**/ ?>
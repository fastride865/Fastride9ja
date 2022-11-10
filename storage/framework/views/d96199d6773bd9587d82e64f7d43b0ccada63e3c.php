
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
                        <a href="<?php echo e(route('weightunit.add')); ?>">
                            <button type="button" title="<?php echo app('translator')->get("$string_file.add_unit"); ?>" data-toggle="modal"
                                    data-target="#myModal"
                                    class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-plus"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                        
                        <?php echo app('translator')->get("$string_file.weight_unit"); ?>
                    </h3>
                </header>
                <div class="panel-body">
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.description"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1 ?>
                        <?php $__currentLoopData = $weightunits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weightunit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php echo e(isset($weightunit->LanguageSingle)?$weightunit->LanguageSingle->name:''); ?>

                                </td>
                                <td>
                                    <?php echo e(isset($weightunit->LanguageSingle)?$weightunit->LanguageSingle->description:''); ?>

                                </td>
                                <td>
                                    <?php $__currentLoopData = $weightunit->Segment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($segment->Name($weightunit->merchant_id)); ?>,
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td>

                                    <form method="POST" action="<?php echo e(route('weightunit.destroy',$weightunit['id'])); ?>"
                                          onsubmit="return confirm('<?php echo app('translator')->get("$string_file.are_you_sure"); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo e(method_field('DELETE')); ?>

                                        <a href="<?php echo e(route('weightunit.add',$weightunit['id'])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>

                                    </form>
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


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/weightunit/index.blade.php ENDPATH**/ ?>
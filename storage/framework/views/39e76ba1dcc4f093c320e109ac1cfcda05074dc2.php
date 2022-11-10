<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(Auth::user('merchant')->can('create_admin')): ?>
                            <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                            <a href="<?php echo e(route('subadmin.create')); ?>">
                                <button type="button" title="<?php echo app('translator')->get('admin.addsub'); ?>"
                                        class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                            class="wb-plus"></i>
                                </button>
                            </a>
                            <a href="<?php echo e(route('excel.subadmin')); ?>">
                                <button type="button" data-toggle="tooltip" data-original-title="<?php echo app('translator')->get("$string_file.export"); ?>"
                                        class="btn btn-icon btn-primary mr-1 float-right" style="margin:10px">
                                    <i class="wb-download" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title">
                        <i class="wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.sub"); ?>-<?php echo app('translator')->get("$string_file.admin_management"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.sub_admin"); ?></th>




                            <th><?php echo app('translator')->get("$string_file.role"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $subadmins->firstItem() ?>
                        <?php $__currentLoopData = $subadmins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subadmin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <?php if(Auth::user()->demo == 1): ?>
                                    <td>
                                        <span class="long_text">
                                            <?php echo e("********".substr($subadmin->merchantFirstName, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($subadmin->merchantLastName, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($subadmin->merchantPhone, -2)); ?>

                                            <br>
                                            <?php echo e("********".substr($subadmin->email, -2)); ?>

                                        </span>
                                        </td>
                                <?php else: ?>
                                    <td>
                                        <span class="long_text">
                                            <?php echo e($subadmin->merchantFirstName." ".$subadmin->merchantLastName); ?>



                                            <br>
                                            <?php echo e($subadmin->merchantPhone); ?>

                                            <br>
                                            <?php echo e($subadmin->email); ?>

                                        </span>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php echo $subadmin->roles->first()->display_name; ?>

                                </td>
                                <td>
                                    <?php if($subadmin->merchantStatus == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo convertTimeToUSERzone($subadmin->created_at, null, $subadmin->parent_id,null, 2); ?>

                                </td>
                                <td>
                                    <?php if(Auth::user('merchant')->can('edit_admin')): ?>
                                        <?php if($subadmin->merchantStatus == 1): ?>
                                            <a href="<?php echo e(route('merchant.subadmin.active-deactive',['id'=>$subadmin->id,'status'=>2])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('merchant.subadmin.active-deactive',['id'=>$subadmin->id,'status'=>1])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-eye"></i> </a>
                                        <?php endif; ?>
                                        <?php if(Auth::user()->demo != 1): ?>
                                            <a href="<?php echo e(route('subadmin.edit',$subadmin->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary menu-icon btn_edit action_btn"> <i
                                                        class="fa fa-edit"></i> </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $subadmins, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/subadmin/index.blade.php ENDPATH**/ ?>
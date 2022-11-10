<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                        <?php echo app('translator')->get("$string_file.navigation_drawer_configuration"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th> 
                            <th><?php echo app('translator')->get("$string_file.description"); ?></th> 
                            <th><?php echo app('translator')->get("$string_file.icon"); ?></th> 
                            <th><?php echo app('translator')->get("$string_file.sequence"); ?></th> 
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $index_list->firstItem() ?>
                        <?php $__currentLoopData = $index_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php if(empty($list->LanguageAppNavigationDrawersOneViews)): ?>
                                        <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                        <span class="text-primary">( In <?php echo e($list->LanguageAppNavigationDrawersAnyViews->LanguageName->name); ?>

                                                            : <?php echo e($list->LanguageAppNavigationDrawersAnyViews->name); ?>

                                                            )</span>
                                    <?php else: ?>
                                        <?php echo e($list->LanguageAppNavigationDrawersOneViews->name); ?>

                                    <?php endif; ?>
                                </td>

                                <td><?php echo e($list->AppNavigationDrawer->name); ?></td>

                                <td>
                                    <?php
                                        $image = !empty($list->image) ? get_image($list->image,'drawericons') :
                                         get_image($list->AppNavigationDrawer->image,'drawer_icon',null,false);
                                    ?>

                                    <img src="<?php echo e($image); ?>" class="img-responsive" height="80" width="80">

                                </td>
                                <td><?php echo e($list->sequence); ?></td>
                                <td>
                                    <?php if($list->status == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                        <a href="<?php echo e(route('navigation-drawer.edit',$list->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>
                                    <?php if($list->status == 1): ?>
                                        <a href="<?php echo e(route('merchant.navigations.active-deactive',['id'=>$list->id,'status'=>0])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i
                                                    class="fa fa-eye-slash"></i> </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('merchant.navigations.active-deactive',['id'=>$list->id,'status'=>1])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                    class="fa fa-eye"></i> </a>
                                    <?php endif; ?>
                                </td>

                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $index_list, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/navigation/index.blade.php ENDPATH**/ ?>
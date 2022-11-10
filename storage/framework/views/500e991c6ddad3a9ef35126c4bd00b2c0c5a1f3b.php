<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
           <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <?php if(Auth::user('merchant')->can('create_cms')): ?>
                        <div class="panel-actions">
                            <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                            <a href="<?php echo e(route('cms.create')); ?>">
                                <button type="button" title="<?php echo app('translator')->get("$string_file.add_cms_page"); ?>"
                                        class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                                </button>
                            </a>
                        </div>
                    <?php endif; ?>
                    <h3 class="panel-title"><i class="wb-copy" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.cms_pages_management"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                            <thead>
                            <tr>
                                <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.country"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.application"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.page_title"); ?></th>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $sr = $cmspages->firstItem() ?>
                            <?php $__currentLoopData = $cmspages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cmspage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>
                                    <td> <?php if($cmspage->country_id != ''): ?>
                                            <?php echo e($cmspage->Country->CountryName); ?>

                                        <?php else: ?>
                                             ----
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($cmspage->application == 1): ?>
                                            <?php echo app('translator')->get("$string_file.user"); ?>
                                        <?php else: ?>
                                            <?php echo app('translator')->get("$string_file.driver"); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td> <?php echo e($cmspage->Page->page); ?> </td>
                                    <td><?php if(empty($cmspage->LanguageSingle)): ?>
                                            <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                            <span class="text-primary">( In <?php echo e($cmspage->LanguageAny->LanguageName->name); ?>

                                                                : <?php echo e($cmspage->LanguageAny->title); ?>

                                                                )</span>
                                        <?php else: ?>
                                            <?php echo e($cmspage->LanguageSingle->title); ?>

                                        <?php endif; ?>
                                    </td>











                                    <td>
                                        <?php if(Auth::user('merchant')->can('edit_cms')): ?>
                                            <a href="<?php echo e(route('cms.edit',$cmspage->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                        class="fa fa-edit"></i> </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php $sr++  ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $cmspages, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/cms/index.blade.php ENDPATH**/ ?>
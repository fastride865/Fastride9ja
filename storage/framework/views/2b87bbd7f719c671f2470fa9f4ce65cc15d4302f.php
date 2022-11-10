<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->can('create_documents')): ?>
                            <a href="<?php echo e(url('merchant/admin/document/add')); ?>">
                                <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                    <i class="wb-plus" title="<?php echo app('translator')->get("$string_file.add_document"); ?>"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title"><i class="fa-files-o" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.document_management"); ?>
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.expire_date"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.mandatory"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.document_number_required"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $documents->firstItem() ?>
                        <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><span class="long_text"> <?php if(empty($document->LanguageSingle)): ?>
                                            <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                            <span class="text-primary">( In <?php echo e($document->LanguageAny->LanguageName->name); ?>

                                                            : <?php echo e($document->LanguageAny->documentname); ?>

                                                            )</span>
                                        <?php else: ?>
                                            <?php echo e($document->LanguageSingle->documentname); ?>

                                        <?php endif; ?>
                                        </span>
                                </td>
                                <td>
                                    <?php echo e($status[$document->expire_date]); ?>

                                </td>
                                <td>
                                    <?php echo e($status[$document->documentNeed]); ?>

                                </td>
                                <td>
                                    <?php if($document->document_number_required == 1): ?>
                                        <?php echo app('translator')->get("$string_file.enable"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.disable"); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($document->documentStatus == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="width: 100px;float: left">
                                    <?php if(Auth::user('merchant')->can('edit_documents')): ?>
                                        <a href="<?php echo e(url('merchant/admin/document/add/'.$document->id)); ?>">
                                            <button type="button"
                                                    class="btn btn-sm btn-warning menu-icon btn_edit action_btn">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </a>

                                        <?php if($document->documentStatus == 1): ?>
                                            <a href="<?php echo e(route('merchant.document.active-deactive',['id'=>$document->id,'status'=>2])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('merchant.document.active-deactive',['id'=>$document->id,'status'=>1])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-success menu-icon btn_eye action_btn"> <i
                                                        class="icon fa-eye"></i> </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $documents, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/document/index.blade.php ENDPATH**/ ?>
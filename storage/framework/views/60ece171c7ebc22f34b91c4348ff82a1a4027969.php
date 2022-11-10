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
                        <?php if(Auth::user('merchant')->can('create_sos_number')): ?>
                            <div class="btn-group float-md-right">
                                <div class="heading-elements">
                                    <button type="button" class="btn btn-icon btn-success mr-1 float-right"
                                            style="margin:10px"
                                            title="<?php echo app('translator')->get("$string_file.add_sos"); ?>" data-toggle="modal"
                                            data-target="#inlineForm">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.sos_management"); ?> </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="<?php echo e(route('merchant.sos.search')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="table_search">
                            <div class="row">
                                <div class="col-md-3 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="name"
                                               placeholder="<?php echo app('translator')->get("$string_file.sos_number"); ?>"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>

                                <div class="col-md-3 form-group ">
                                    <div class="input-group">
                                        <input type="text" id="" name="number"
                                               placeholder="<?php echo app('translator')->get("$string_file.name"); ?>"
                                               class="form-control col-md-12 col-xs-12">
                                    </div>
                                </div>
                                <div class="col-sm-2 form-group ">
                                    <button class="btn btn-primary" type="submit" name="seabt12"><i
                                                class="fa fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.sos_number"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.application"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.added_by"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $Sos->firstItem() ?>
                        <?php $__currentLoopData = $Sos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <?php if(empty($sos->LanguageSingle)): ?>
                                            <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                            <span class="text-primary">( In <?php echo e("********".substr($sos->LanguageAny->LanguageName->name, -2)); ?>

                                                                    : <?php echo e("********".substr($sos->LanguageAny->name, -2)); ?>

                                                                    )</span>
                                        <?php else: ?>
                                            <?php echo e("********".substr($sos->LanguageSingle->name, -2)); ?>

                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(empty($sos->LanguageSingle)): ?>
                                            <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                            <span class="text-primary">( In <?php echo e($sos->LanguageAny->LanguageName->name); ?>

                                                                    : <?php echo e($sos->LanguageAny->name); ?>

                                                                    )</span>
                                        <?php else: ?>
                                            <?php echo e($sos->LanguageSingle->name); ?>

                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <?php echo e("********".substr($sos->number, -2)); ?>

                                    <?php else: ?>
                                        <?php echo e($sos->number); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($sos->application == 1): ?>
                                        <?php echo app('translator')->get("$string_file.user"); ?>
                                    <?php elseif($sos->application == 2): ?>
                                        <?php echo app('translator')->get("$string_file.driver"); ?>
                                    <?php else: ?>
                                        - - -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(Auth::user()->demo == 1): ?>
                                        <?php if($sos->application == 1): ?>
                                            <?php echo e(($sos->User) ? "********".substr($sos->User->first_name, -2) : '- - -'); ?>

                                        <?php elseif($sos->application == 2): ?>
                                            <?php echo e(($sos->Driver) ? "********".substr($sos->Driver->first_name, -2) : '- - -'); ?>

                                        <?php else: ?>
                                            - - -
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($sos->application == 1): ?>
                                            <?php echo e(($sos->User) ? $sos->User->first_name.' ('.$sos->User->UserPhone.')': '- - -'); ?>

                                        <?php elseif($sos->application == 2): ?>
                                            <?php echo e(($sos->Driver) ? $sos->Driver->first_name.' ('.$sos->Driver->phoneNumber.')' : '- - -'); ?>

                                        <?php else: ?>
                                            - - -
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?php if($sos->sosStatus == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(Auth::user('merchant')->can('edit_sos_number')): ?>
                                        <a href="<?php echo e(route('sos.edit',$sos->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-warning btn-sm menu-icon btn_edit action_btn"> <i
                                                    class="fa fa-edit"></i> </a>

                                        <?php if($sos->sosStatus == 1): ?>
                                            <a href="<?php echo e(route('merchant.sos.active-deactive',['id'=>$sos->id,'status'=>2])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-danger btn-sm menu-icon btn_eye_dis action_btn"> <i
                                                        class="fa fa-eye-slash"></i> </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('merchant.sos.active-deactive',['id'=>$sos->id,'status'=>1])); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-success btn-sm menu-icon btn_eye action_btn"> <i
                                                        class="fa fa-eye"></i> </a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if(Auth::user('merchant')->can('delete_sos_number')): ?>
                                        <a href="<?php echo e(route('merchant.sos.delete',$sos->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.delete"); ?>"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-danger btn-sm menu-icon btn_delete action_btn"> <i
                                                    class="fa fa-trash"></i> </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $Sos, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b><?php echo app('translator')->get("$string_file.add_sos"); ?>
                            (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(route('sos.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">

                        <label><?php echo app('translator')->get("$string_file.name"); ?><span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="name"
                                   name="name" placeholder=""
                                   required>
                        </div>


                        <label><?php echo app('translator')->get("$string_file.application"); ?> <span class=" text-danger">*</span></label>
                        <div class="form-group">
                            <select class="form-control" name="application" id="application" required>
                                <option value="1"><?php echo app('translator')->get("$string_file.user"); ?></option>
                                <option value="2"><?php echo app('translator')->get("$string_file.driver"); ?></option>
                            </select>
                        </div>


                        <label> <?php echo app('translator')->get("$string_file.sos_number"); ?><span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="number"
                                   name="number" placeholder="<?php echo app('translator')->get("$string_file.phone"); ?>"
                                   required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" class="btn btn-outline-primary" value="<?php echo app('translator')->get("$string_file.add"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/sos/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="<?php echo e(route('navigation-drawer.index')); ?>" style="margin:10px">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                            <?php if(!empty($info_setting) && $info_setting->edit_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="wb-flag" aria-hidden="true"></i>
                       <?php echo app('translator')->get("$string_file.navigation_drawer"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('navigation-drawer.update', $edit->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_method" value="put">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        <?php echo app('translator')->get("$string_file.name"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control"
                                           id="name" name="name"
                                           placeholder="<?php echo app('translator')->get("$string_file.name"); ?>"
                                           <?php if(!empty($edit->LanguageAppNavigationDrawersOneViews)): ?> value="<?php echo $edit->LanguageAppNavigationDrawersOneViews->name; ?>" <?php endif; ?>
                                           required>
                                    <?php if($errors->has('name')): ?>
                                        <label class="danger"><?php echo e($errors->first('name')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sequence">
                                        <?php echo app('translator')->get("$string_file.sequence"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control"
                                           id="sequence"
                                           name="sequence" min="1"
                                           placeholder="<?php echo app('translator')->get("$string_file.sequence"); ?>"
                                           value="<?php echo e($edit->sequence); ?>"
                                           required>
                                    <?php if($errors->has('sequence')): ?>
                                        <label class="danger"><?php echo e($errors->first('sequence')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">















                                <div class="row"></div>
                                <div class="form-group">
                                    <label for="image"><?php echo app('translator')->get("$string_file.icon"); ?></label>
                                    <input type="file" name="image" id="image" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <?php if(!Auth::user('merchant')->can('edit_navigation_drawer')): ?>
                                <?php if(!$is_demo): ?>
                                <button type="submit" class="btn btn-primary float-right">
                                    <i class="fa fa-check-circle"></i> <?php echo e(trans("$string_file.update")); ?>

                                </button>
                                <?php else: ?>
                                    <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/navigation/edit.blade.php ENDPATH**/ ?>
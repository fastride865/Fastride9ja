<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php if(session('sosedit')): ?>
                <div class="alert dark alert-icon alert-success alert-dismissible">
                    <i class="icon wb-info" aria-hidden="true"></i><?php echo app('translator')->get('admin.message330'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('sos.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success mr-1" style="margin:10px"><i
                                            class="fa fa-reply"></i>
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
                    <h3 class="panel-title"><i class="fa fa-edit"></i> <?php echo app('translator')->get("$string_file.sos"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('sos.update', $sos->id)); ?>">
                        <?php echo e(method_field('PUT')); ?>

                        <?php echo csrf_field(); ?>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.name"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name"
                                               name="name"
                                               value="<?php if($sos->LanguageSingle): ?><?php echo e($sos->LanguageSingle->name); ?><?php endif; ?>"
                                               placeholder="" required>
                                        <?php if($errors->has('name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.sos_number"); ?><span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="number"
                                               name="number"
                                               placeholder="<?php echo app('translator')->get("$string_file.phone"); ?>"
                                               value="<?php echo e($sos->number); ?>" required>
                                        <?php if($errors->has('number')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('number')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions right" style="margin-bottom: 2%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.update"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'edit_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/sos/edit.blade.php ENDPATH**/ ?>
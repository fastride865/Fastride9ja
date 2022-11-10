<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                        <div class="panel-actions">
                            <?php if(!empty($info_setting) && $info_setting->edit_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                            <div class="btn-group float-right">
                                <a href="<?php echo e(route('promotions.index')); ?>">
                                    <button type="button" class="btn btn-icon btn-success" style="margin:10px">
                                        <i class="wb-reply"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    <h3 class="panel-title"><i class="wb-edit"></i>
                        <?php echo app('translator')->get("$string_file.notification"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('promotions.update', $promotion->id)); ?>">
                        <?php echo e(method_field('PUT')); ?>

                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastName3">
                                        <?php echo app('translator')->get("$string_file.title"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title"
                                           name="title"
                                           placeholder="<?php echo app('translator')->get("$string_file.title"); ?>" value="<?php echo e($promotion->title); ?>" required>
                                    <?php if($errors->has('title')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('title')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ProfileImage">
                                        <?php echo app('translator')->get("$string_file.image"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input style="height: 0%" type="file" class="form-control" id="image"
                                           name="image"
                                           placeholder="<?php echo app('translator')->get("$string_file.image"); ?>">
                                    <?php if($errors->has('image')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('image')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.message"); ?><span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="message" name="message"
                                              rows="3"
                                              placeholder="<?php echo app('translator')->get("$string_file.message"); ?>"><?php echo e($promotion->message); ?></textarea>
                                    <?php if($errors->has('message')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('message')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.url"); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="url" class="form-control" id="url"
                                           name="url"
                                           placeholder="<?php echo app('translator')->get("$string_file.url"); ?>" value="<?php echo e($promotion->url); ?>">
                                    <?php if($errors->has('url')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('url')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
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
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/promotion/edit.blade.php ENDPATH**/ ?>
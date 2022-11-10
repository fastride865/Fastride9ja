<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->edit_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('cms.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-edit" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.cms_page"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('cms.update',$cmspage->id)); ?>">
                        <?php echo e(method_field('PUT')); ?>

                        <?php echo csrf_field(); ?>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lastName3">
                                            <?php echo app('translator')->get("$string_file.page_title"); ?><span
                                                    class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="title"
                                               name="title"
                                               value="<?php if($cmspage->LanguageSingle): ?><?php echo e($cmspage->LanguageSingle->title); ?> <?php else: ?> <?php echo e($cmspage->LanguageAny->title); ?> <?php endif; ?>"
                                               placeholder="<?php echo app('translator')->get("$string_file.page_title"); ?>"
                                               required>
                                        <?php if($errors->has('title')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('title')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="emailAddress5">
                                            <?php echo app('translator')->get("$string_file.description"); ?><span
                                                    class="text-danger">*</span>
                                        </label>
                                        <textarea id="summernote" class="form-control"
                                                  name="description" rows="5"
                                                  placeholder="<?php echo app('translator')->get("$string_file.description"); ?>" data-plugin="summernote">
                                                 <?php if($cmspage->LanguageSingle): ?><?php echo e($cmspage->LanguageSingle->description); ?>

                                            <?php else: ?> <?php echo e($cmspage->LanguageAny->description); ?>

                                            <?php endif; ?>
                                             </textarea>
                                        <?php if($errors->has('description')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('description')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
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

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/cms/edit.blade.php ENDPATH**/ ?>
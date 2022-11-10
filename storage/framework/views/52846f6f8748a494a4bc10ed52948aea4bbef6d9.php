<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
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
                        <?php echo app('translator')->get("$string_file.cms_page"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="<?php echo e(route('cms.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.page_type"); ?><span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="page" id="page"
                                            required>
                                        <option value="">--<?php echo app('translator')->get("$string_file.select"); ?>--</option>
                                        <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($page->slug); ?>"><?php echo e($page->page); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($errors->has('page')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('page')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.select"); ?><span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="country" id="country"
                                            required>
                                        <option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>
                                        <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($country->id); ?>"><?php echo e($country->CountryName); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if($errors->has('page')): ?>
                                        <label class="danger"><?php echo e($errors->first('page')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emailAddress5">
                                        <?php echo app('translator')->get("$string_file.application"); ?><span
                                                class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="application"
                                            id="application" required>
                                        <option value="2"><?php echo app('translator')->get("$string_file.driver"); ?></option>
                                        <option value="1"><?php echo app('translator')->get("$string_file.user"); ?></option>
                                    </select>
                                    <?php if($errors->has('application')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('application')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lastName3">
                                        <?php echo app('translator')->get("$string_file.page_title"); ?><span
                                                class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title"
                                           name="title"
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
                                        </textarea>
                                    <?php if($errors->has('description')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('description')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions right" style="margin-bottom: 3%">
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        $(document).on('change', '#page', function () {
            if ($('#page option:selected').val() == 'terms_and_Conditions') {
                $('#country').attr('disabled', false);
            } else {
                $('#country').attr('disabled', true);
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/cms/create.blade.php ENDPATH**/ ?>
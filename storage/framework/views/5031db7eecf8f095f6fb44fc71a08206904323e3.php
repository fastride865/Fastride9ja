<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <div class="btn-group float-right" >
                            <a href="<?php echo e(route('promotions.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.notification"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <div class="nav-tabs-horizontal" data-plugin="tabs">
                        <ul class="nav nav-tabs nav-tabs-line tabs-line-top" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="base-tab11" data-toggle="tab" href="#exampleTabsLineTopOne"
                                   aria-controls="#exampleTabsLineTopOne" role="tab">
                                    <i class="icon fa-cab"></i><?php echo app('translator')->get("$string_file.all"); ?></a></li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="base-tab12" data-toggle="tab" href="#exampleTabsLineTopTwo"
                                   aria-controls="#exampleTabsLineTopTwo" role="tab">
                                    <i class="icon fa-clock-o"></i><?php echo app('translator')->get("$string_file.area_wise"); ?></a></li>
                        </ul>
                        <div class="tab-content pt-20">
                            <div class="tab-pane active" id="exampleTabsLineTopOne" role="tabpanel">
                                <form method="POST" class="steps-validation wizard-notification"
                                      enctype="multipart/form-data"
                                      action="<?php echo e(route('promotions.store')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="location3"><?php echo app('translator')->get("$string_file.application"); ?>
                                                    :</label>
                                                <select class="form-control"
                                                        name="application"
                                                        id="application"
                                                        onchange="DriverNotification(this.value)"
                                                        required>
                                                    <option value="">--<?php echo app('translator')->get("$string_file.application"); ?>--
                                                    </option>
                                                    <option value="1"><?php echo app('translator')->get("$string_file.driver"); ?></option>
                                                    <option value="2"><?php echo app('translator')->get("$string_file.user"); ?></option>
                                                </select>
                                                <?php if($errors->has('application')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('application')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lastName3">
                                                    <?php echo app('translator')->get("$string_file.title"); ?><span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" id="title" name="title" placeholder="" required>
                                                    <?php if($errors->has('title')): ?>
                                                        <label class="text-danger"><?php echo e($errors->first('title')); ?></label>
                                                    <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    <?php echo app('translator')->get("$string_file.description"); ?><span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control" id="message"
                                                          name="message"
                                                          rows="3" required
                                                          placeholder=""></textarea>
                                                <?php if($errors->has('message')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('message')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="profile_image">
                                                    <?php echo app('translator')->get("$string_file.image"); ?>
                                                </label>
                                                <input style="height: 0%;" type="file" class="form-control"
                                                       id="image"
                                                       name="image"
                                                       placeholder="<?php echo app('translator')->get("$string_file.image"); ?>">
                                                <?php if($errors->has('image')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('image')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    <?php echo app('translator')->get("$string_file.url"); ?>
                                                </label>
                                                <input type="url" class="form-control"
                                                       id="url"
                                                       name="url"
                                                       placeholder="">
                                                <?php if($errors->has('url')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('url')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    <?php echo app('translator')->get("$string_file.expire_date"); ?>
                                                </label>
                                                <input type="text" class="form-control customDatePicker1" name="date" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions right" style="margin-bottom: 3%">
                                        <button type="submit" class="btn btn-primary float-right">
                                            <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.send"); ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="exampleTabsLineTopTwo" role="tabpanel">
                                <form method="POST" class="steps-validation wizard-notification"
                                      enctype="multipart/form-data"
                                      action="<?php echo e(route('merchant.areawise-notification')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="firstName3">
                                                    <?php echo app('translator')->get("$string_file.service_area"); ?> <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-control" name="area"
                                                        id="area" required>
                                                    <option value="">--Select Area--
                                                    </option>
                                                    <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($area->id); ?>"><?php if($area->LanguageSingle): ?> <?php echo e($area->LanguageSingle->AreaName); ?> <?php else: ?>  <?php echo e($area->LanguageAny->AreaName); ?> <?php endif; ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <?php if($errors->has('area')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('area')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="lastName3">
                                                    <?php echo app('translator')->get("$string_file.title"); ?><span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control"
                                                       id="title"
                                                       name="title"
                                                       placeholder=""
                                                       required>
                                                <?php if($errors->has('title')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('title')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    <?php echo app('translator')->get("$string_file.description"); ?><span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control" id="message"
                                                          name="message"
                                                          rows="3" required
                                                          placeholder=""></textarea>
                                                <?php if($errors->has('message')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('message')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="profile_image">
                                                    <?php echo app('translator')->get("$string_file.image"); ?>
                                                </label>
                                                <input style="height: 0%" type="file" class="form-control"
                                                       id="image"
                                                       name="image"
                                                       placeholder="<?php echo app('translator')->get("$string_file.image"); ?>">
                                                <?php if($errors->has('image')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('image')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    <?php echo app('translator')->get("$string_file.url"); ?>
                                                </label>
                                                <input type="url" class="form-control"
                                                       id="url"
                                                       name="url"
                                                       placeholder="">
                                                <?php if($errors->has('url')): ?>
                                                    <label class="text-danger"><?php echo e($errors->first('url')); ?></label>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="emailAddress5">
                                                    <?php echo app('translator')->get("$string_file.expire_date"); ?>
                                                </label>
                                                <input type="text" class="form-control customDatePicker1" name="date" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions right" style="margin-bottom: 3%">
                                        <button type="submit" class="btn btn-primary float-right">
                                            <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.send"); ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/promotion/create.blade.php ENDPATH**/ ?>
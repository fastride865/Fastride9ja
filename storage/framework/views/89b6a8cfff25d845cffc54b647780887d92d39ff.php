<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <a href="<?php echo e(route('account-types.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i
                                        class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-user" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.account_type"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)</h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <form method="POST" class="steps-validation wizard-notification"
                              enctype="multipart/form-data" action="<?php echo e(route('account-types.store')); ?>">
                            <?php echo csrf_field(); ?>
                            <fieldset>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">
                                                <?php echo app('translator')->get("$string_file.account_type"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="name"
                                                   required
                                                   name="name"
                                                   placeholder="">
                                            <?php if($errors->has('name')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('name')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?php echo Form::label('status', trans("$string_file.status").'<span class="text-danger">*</span> :', ['class' => 'control-label'], false); ?>

                                            &nbsp;
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <?php echo e(Form::radio('status','1',true,['class' => 'custom-control-input','id'=>'active',])); ?>

                                                    <?php echo Form::label('active', trans("$string_file.active"), ['class' => 'custom-control-label'], false); ?>

                                                </div>
                                            </fieldset>
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <?php echo e(Form::radio('status','0',false,['class' => 'custom-control-input','id'=>'deactive',])); ?>

                                                    <?php echo Form::label('deactive', trans("$string_file.inactive"), ['class' => 'custom-control-label'], false); ?>

                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>

                            </fieldset>
                            <div class="form-actions d-flex flex-row-reverse p-2">
                                <?php if(Auth::user('merchant')->can('create-account-types')): ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/account_types/create.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("business-segment.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="<?php echo e(route('merchant.option-type.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <?php $heading = trans("$string_file.add"); ?>
                        <?php if(isset($data['option']['id'])): ?>
                            <?php $heading = trans("$string_file.edit"); ?>
                        <?php endif; ?>
                        <?php echo e($heading); ?> <?php echo app('translator')->get("$string_file.option_type"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'option','id'=>'option-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ); ?>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optionname">
                                    <?php echo app('translator')->get("$string_file.type"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('type',old('type',isset( $data['option']['id']) ? isset($data['option']->LanguageOptionTypeSingle) ? $data['option']->LanguageOptionTypeSingle->type : "" : NULL),['id'=>'','class'=>'form-control','required'=>true,'placeholder'=>""]); ?>

                                <?php if($errors->has('type')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('type')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optiontype">
                                    <?php echo app('translator')->get("$string_file.charges_type"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('charges_type',$data['charges_type'],old('charges_type',isset($data['option']['charges_type']) ? $data['option']['charges_type'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('charges_type')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('charges_type')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="select_type">
                                    <?php echo app('translator')->get("$string_file.type"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('select_type',$data['select_type'],old('select_type',isset($data['option']['select_type']) ? $data['option']['select_type'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('select_type')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('select_type')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sequence">
                                    <?php echo app('translator')->get("$string_file.maximum_options_on_app"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::number('max_option_on_app',old('max_option_on_app',isset( $data ['option']['max_option_on_app']) ? $data['option']['max_option_on_app'] : NULL),['id'=>'','min'=>'0','class'=>'form-control','required'=>true,'placeholder'=>""]); ?>

                                <?php if($errors->has('max_option_on_app')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('max_option_on_app')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>












                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    <?php echo app('translator')->get("$string_file.status"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('status',$data['status'],old('status',isset($data['option']['status']) ? $data['option']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('status')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('status')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <?php if(!$is_demo): ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                        </button>
                        <?php else: ?>
                            <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php echo Form::close(); ?>

                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/option-type/form.blade.php ENDPATH**/ ?>
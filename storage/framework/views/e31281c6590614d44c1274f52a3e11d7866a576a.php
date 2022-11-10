<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="<?php echo e(route('pricingparameter.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                            <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin-left:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.pricing_parameter"); ?>
                    </h3>
                </header>
                <?php $id = NULL; $on = "add"; $disable = false;?>
                <?php if(isset($data['pricing_parameter']['id'])): ?>
                    <?php $id = $data['pricing_parameter']['id'];
                    $on = "edit"; $disable = true;
                     ?>
                <?php endif; ?>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <?php echo Form::open(["class"=>"steps-validation wizard-notification","files"=>true,"url"=>route("priceparameter.save",$id)]); ?>

                        <?php echo Form::hidden('id',$id); ?>

                        <?php echo $data['segment_html']; ?>

                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.name"); ?>:
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::text('parametername',old('parametername',isset($data['pricing_parameter']->LanguageSingle->parameterName) ? $data['pricing_parameter']->LanguageSingle->parameterName : ''),['class'=>'form-control','placeholder'=>'','required'=>true]); ?>

                                        <?php if($errors->has('parametername')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('parametername')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.parameter_display_name"); ?>:
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::text('parameter_display_name',old('parameter_display_name',isset($data['pricing_parameter']->LanguageSingle->parameterNameApplication) ? $data['pricing_parameter']->LanguageSingle->parameterNameApplication : ''),['class'=>'form-control','id'=>'parameter_display_name','placeholder'=>'','required'=>true]); ?>

                                        <?php if($errors->has('parameter_display_name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('parameter_display_name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.sequence"); ?> :
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::number('sequence_number',old('sequence_number',isset($data['pricing_parameter']['sequence_number']) ? $data['pricing_parameter']['sequence_number'] : ''),['class'=>'form-control','id'=>'sequence_number','placeholder'=>'','required'=>true,'min'=>1]); ?>

                                        <?php if($errors->has('sequence_number')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('sequence_number')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name"><?php echo app('translator')->get("$string_file.type"); ?><span
                                                    class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::select('parameterType',get_price_parameter($string_file,$on),old('parameterType',isset($data['pricing_parameter']['parameterType']) ? $data['pricing_parameter']['parameterType'] :NULL),['class'=>'form-control','required'=>true,'id'=>'parameterType','disabled'=>$disable]); ?>

                                        <?php if($errors->has('parametertype')): ?>
                                            <span class="help-block">
                                                    <strong><?php echo e($errors->first('parametertype')); ?></strong>
                                                </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if(empty($id)): ?>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>
                                                <?php echo app('translator')->get("$string_file.applicable_for"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                <?php echo Form::select('price_type[]',merchant_price_type($merchant->RateCard),old('parameterType',isset($data['priceparameter']['parameterType']) ? $data['priceparameter']['parameterType'] :NULL),['class'=>'form-control select2','required'=>true,'id'=>'parameterType','multiple'=>true]); ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </fieldset>
                        <div class="form-actions float-right">
                            <?php if(!$is_demo): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i><?php echo $data['submit_button']; ?>

                            </button>
                            <?php else: ?>
                                <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php echo Form::close(); ?>

                    </section>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/pricingparameter/form.blade.php ENDPATH**/ ?>
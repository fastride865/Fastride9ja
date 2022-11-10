<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("business-segment.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right" >
                            <a href="<?php echo e(route('business-segment.option.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success"style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <?php $heading = trans("$string_file.add"); ?>
                        <?php if(isset($data['option']['id'])): ?>
                            <?php $heading = trans("$string_file.edit"); ?>
                        <?php endif; ?>
                       <?php echo e($heading); ?> <?php echo app('translator')->get("$string_file.option"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'option','id'=>'option-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ); ?>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optionname">
                                    <?php echo app('translator')->get("$string_file.name"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('name',old('name',isset( $data ['option']['id']) ? $data['option']->Name($data['option']['business_segment_id']) : NULL),['id'=>'','class'=>'form-control','required'=>true,'placeholder'=>""]); ?>

                                <?php if($errors->has('name')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('name')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optiontype">
                                    <?php echo app('translator')->get("$string_file.type"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('option_type_id',$data['arr_option_type'],old('option_type_id',isset( $data ['option']['option_type_id']) ? $data['option']['option_type_id'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('option_type_id')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('option_type_id')); ?></label>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/option/form.blade.php ENDPATH**/ ?>
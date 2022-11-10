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
                       <?php echo e($heading); ?> <?php echo app('translator')->get("$string_file.configuration"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'configurations','id'=>'option-form','files'=>true,'url'=>route('business-segment.save-configurations'),'method'=>'POST'] ); ?>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="optionname">
                                    <?php echo app('translator')->get("$string_file.order_expire_time_minutes"); ?>
                                </label>
                                <?php echo Form::text('order_expire_time',old('name',!empty($config) ? $config->order_expire_time : NULL),['id'=>'','class'=>'form-control','placeholder'=>""]); ?>

                                <?php if($errors->has('order_expire_time')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('order_expire_time')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="is_open">
                                    <?php echo app('translator')->get("$string_file.is_open"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('is_open',$is_open,old('is_open',!empty($config) ? $config->is_open : NULL),['id'=>'is_open','class'=>'form-control','required'=>true]); ?>

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
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/configurations.blade.php ENDPATH**/ ?>
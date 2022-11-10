<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('merchant.style-management')); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-hammer" aria-hidden="true"></i>
                        <?php echo $data['title']; ?>

                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'style-management','id'=>'style-management-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ); ?>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">
                                    <?php echo app('translator')->get("$string_file.name"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('style_name',old('style_name',isset( $data['style_management']['id']) ? $data['style_management']->Name($data['style_management']['merchant_id']) : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('style_name')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('style_name')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    <?php echo app('translator')->get("$string_file.status"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('status',$data['arr_status'],old('status',isset($data['style_management']['status']) ? $data['style_management']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('status')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('status')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <br>
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

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/style-management/form.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('merchant.category')); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
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
                        <i class=" fa fa-building" aria-hidden="true"></i>
                        <?php echo $data['title']; ?>

                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'category','id'=>'category-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ); ?>

                    <?php echo $data['segment_html']; ?>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="banner_name">
                                    <?php echo app('translator')->get("$string_file.name"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('category_name',old('category_name',isset( $data['category']['id']) ? $data['category']->Name($data['category']['merchant_id']) : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('category_name')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('banner_name')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category_parent_id">
                                    <?php echo app('translator')->get("$string_file.parent_category"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('category_parent_id',$data['arr_category'],old('category_parent_id',isset($data['category']['category_parent_id']) ? $data['category']['category_parent_id'] : NULL),['id'=>'category_parent_id','class'=>'form-control','required'=>false]); ?>

                                <?php if($errors->has('category_parent_id')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('category_parent_id')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    <?php echo app('translator')->get("$string_file.status"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('status',$data['arr_status'],old('status',isset($data['category']['status']) ? $data['category']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('status')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('status')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sequence">
                                    <?php echo app('translator')->get("$string_file.sequence"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::number('sequence',old('sequence',isset($data['category']['sequence']) ? $data['category']['sequence'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('sequence')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('sequence')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group  ">
                                <label for="message26">
                                    <?php echo app('translator')->get("$string_file.image"); ?> (W:<?php echo e(Config('custom.image_size.category.width')); ?> *
                                    H:<?php echo e(Config('custom.image_size.category.height')); ?>)
                                </label>
                                <?php if(!empty($data['category']['category_image'])): ?>
                                    <a href="<?php echo e(get_image($data['category']['category_image'],'category',$data['category']['merchant_id'])); ?>"
                                       target="_blank"><?php echo app('translator')->get("$string_file.view"); ?></a>
                                <?php endif; ?>
                                <?php echo Form::file('category_image',['id'=>'category_image','class'=>'form-control']); ?>

                                <?php if($errors->has('category_image')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('category_image')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <?php if(!$is_demo): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i><?php echo app('translator')->get("$string_file.save"); ?>
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


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/category/form.blade.php ENDPATH**/ ?>
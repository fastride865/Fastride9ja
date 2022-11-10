<?php $__env->startSection('content'); ?>
    <?php $images_required = true; $id = NULL; $sub_cat_optional =  $data['segment']->Merchant->configuration->bussiness_seg_sub_cat_optional; ?>
    <?php if(!empty($data['product']['id'])): ?>
        <?php $lang_data = $data['product']->langData($data['product']['merchant_id']); ?>
        <?php $images_required = false; $id = $data['product']['id'];?>
    <?php endif; ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('business-segment.product.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" fa fa-building" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.product"); ?>
                        
                    </h3>
                </div>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'product','id'=>'product-form','files'=>true,'url'=>$data['save_url'],'method'=>'POST'] ); ?>

                    <?php echo Form::hidden('id',$id); ?>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sku_id">
                                    <?php echo app('translator')->get("$string_file.sku_no"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('sku_id',old('sku_id',isset( $data ['product']['sku_id']) ? $data['product']['sku_id'] : NULL),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off']); ?>

                                <?php if($errors->has('sku_id')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('sku_id')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_name">
                                    <?php echo app('translator')->get("$string_file.product_name"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::text('product_name',old('product_name',isset($lang_data->name) ? $lang_data->name : NULL),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off']); ?>

                                <?php if($errors->has('product_name')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('product_name')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    <?php echo app('translator')->get("$string_file.status"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('status',$data['product_status'],old('status',isset($data['product']['status']) ? $data['product']['status'] : NULL),['id'=>'','class'=>'form-control','required'=>true]); ?>

                                <?php if($errors->has('status')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('status')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(isset($data['segment']->Segment->slag) && $data['segment']->Segment->slag != 'FOOD'): ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    <?php echo app('translator')->get("$string_file.is_display_on_home_screen"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('display_type',get_status(true,$string_file),old('display_type',isset($data['product']['display_type']) ? $data['product']['display_type'] : NULL),['id'=>'','class'=>'form-control']); ?>

                                <?php if($errors->has('display_type')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('display_type')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_description">
                                    <?php echo app('translator')->get("$string_file.description"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::textarea('product_description',old('product_description',isset($lang_data->description) ? $lang_data->description : NULL),['id'=>'','class'=>'form-control','required'=>true ,'cols'=>3,'rows'=>2]); ?>

                                <?php if($errors->has('product_description')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('product_description')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="product_ingredients">
                                    <?php echo app('translator')->get("$string_file.ingredients"); ?>
                                </label>
                                <?php echo Form::textarea('product_ingredients',old('product_ingredients',isset($lang_data->ingredients) ? $lang_data->ingredients : NULL),['id'=>'','class'=>'form-control','cols'=>3,'rows'=>2]); ?>

                                <?php if($errors->has('product_ingredients')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('product_ingredients')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if(isset($data['segment']->Segment->slag) && $data['segment']->Segment->slag == 'FOOD'): ?>
                            <div class="col-md-4">
                                <label for="product_preparation_time">
                                    <?php echo app('translator')->get("$string_file.preparation_time"); ?> (<?php echo app('translator')->get("$string_file.in_minutes"); ?>)
                                </label>
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="wb-time" aria-hidden="true"></i></span>
                                    <?php echo Form::number('product_preparation_time',old('product_preparation_time',isset($data['product']['product_preparation_time']) ? $data['product']['product_preparation_time'] : NULL),['class'=>'form-control','id'=>'time','autocomplete'=>'off','min'=>0,'required']); ?>

                                </div>
                                <?php if($errors->has('product_preparation_time')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('product_preparation_time')); ?></label>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category_id">
                                    <?php echo app('translator')->get("$string_file.category"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('category_id',add_blank_option($data['arr_category'],trans("$string_file.select")),old('category_id ',isset($data['product']['category_id']) ? $data['product']['category_id'] : NULL),['id'=>'category_id','class'=>'form-control','required'=>true,'autocomplete'=>'off']); ?>

                                <?php if($errors->has('category_id')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('category_id')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php $sub_category_required = $data['segment']->Segment->slag != 'FOOD' ? true : false ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sub_category_id">
                                    <?php echo app('translator')->get("$string_file.sub_category"); ?>
                                </label>
                                <?php echo Form::select('sub_category_id',add_blank_option($data['sub_category'],trans("$string_file.select")),old('sub_category_id ',isset($data['product']['sub_category_id']) ? $data['product']['sub_category_id'] : NULL),['id'=>'sub_category_id','class'=>'form-control','autocomplete'=>'off','required'=>$sub_cat_optional == 1 ? false : true]); ?>

                                <?php if($errors->has('sub_category_id')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('sub_category_id')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if(isset($data['segment']->Segment->slag) && in_array($data['segment']->Segment->slag,array('FOOD','GROCERY'))): ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="food_type">
                                        <?php echo app('translator')->get("$string_file.type"); ?>

                                    </label>
                                    <?php echo Form::select('food_type',add_blank_option($data['arr_food_type'],trans("$string_file.select")),old('food_type ',isset($data['product']['food_type']) ? $data['product']['food_type'] : NULL),['id'=>'food_type','class'=>'form-control','autocomplete'=>'off']); ?>

                                    <?php if($errors->has('food_type')): ?>
                                        <label class="text-danger"><?php echo e($errors->first('food_type')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sequence">
                                    <?php echo app('translator')->get("$string_file.sequence"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::number('sequence',old('sequence',isset( $data ['product']['sequence']) ? $data['product']['sequence'] : NULL),['id'=>'','class'=>'form-control','required'=>true,'autocomplete'=>'off']); ?>

                                <?php if($errors->has('sequence')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('sequence')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">
                                    <?php echo app('translator')->get("$string_file.do_you_want_to_manage_inventory"); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?php echo Form::select('manage_inventory',get_status(true,$string_file),old('manage_inventory',isset($data['product']['manage_inventory']) ? $data['product']['manage_inventory'] : NULL),['id'=>'','class'=>'form-control']); ?>

                                <?php if($errors->has('manage_inventory')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('manage_inventory')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group  ">
                                <label for="product_cover_image">
                                    <?php echo app('translator')->get("$string_file.cover_image"); ?>

                                    (W:<?php echo e(Config('custom.image_size.product.width')); ?> * H:<?php echo e(Config('custom.image_size.product.height')); ?>)
                                </label>
                                <?php if(!empty($data['product']['id'])): ?>
                                    <a href="<?php echo e(get_image($data['product']['product_cover_image'],'product_cover_image',$data['product']['merchant_id'])); ?>"
                                       target="_blank"><?php echo app('translator')->get("$string_file.view"); ?></a>
                                <?php endif; ?>
                                <?php echo Form::file('product_cover_image',['id'=>'product_cover_image','class'=>'form-control']); ?> 
                                <?php if($errors->has('product_cover_image')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('product_cover_image')); ?></label>
                                <?php endif; ?>
                            </div>
                        </div>






























                    </div>
                    <br>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <?php if(!$is_demo): ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i>
                            <?php if($data ['product'] == null): ?>
                                <?php echo app('translator')->get("$string_file.save_and_continue_to_add_variant"); ?>
                            <?php else: ?>
                                <?php echo app('translator')->get("$string_file.save"); ?>
                            <?php endif; ?>
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
<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function () {
            $('#category_id').change(function () {
                $.ajax({
                    url: "<?php echo e(route('business-segment.get.subcategory')); ?>",
                    type: "GET",
                    data: {id: $(this).val()},
                    dataType: "JSON",
                    success: function (result) {
                        $("#sub_category_id").empty();
                        $.each(result, function (key, value) {
                            $("#sub_category_id").append("<option value='" + key + "'>" + value + "</option>");
                        });
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/product/form.blade.php ENDPATH**/ ?>
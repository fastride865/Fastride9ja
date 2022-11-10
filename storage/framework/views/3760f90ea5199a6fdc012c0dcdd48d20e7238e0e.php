<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('advertisement.index')); ?>">
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
                        <i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.add_banner"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data"
                          action="<?php echo e(route('advertisement.store')); ?><?php if(!empty($banner)): ?><?php echo e('/'.$banner->id); ?> <?php endif; ?>">
                        <?php echo csrf_field(); ?>
                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            <?php echo app('translator')->get("$string_file.name"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo e(Form::text('name', old('name', isset($banner->name) ? $banner->name : ''), ['class' => 'form-control', 'id' => 'name', 'placeholder' => '', 'required'])); ?>

                                        <?php if($errors->has('name')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('name')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            <?php echo app('translator')->get("$string_file.image"); ?>
                                            <span class="text-danger">*</span>
                                            (W:<?php echo e(Config('custom.image_size.banner.width')); ?> *
                                            H:<?php echo e(Config('custom.image_size.banner.height')); ?>)
                                            <?php if(isset($banner->image) && $banner->image != ''): ?>
                                                <a href="<?php echo e(get_image($banner->image,'banners',$banner->merchant_id)); ?>"
                                                   target="_blank">view</a>
                                            <?php endif; ?>
                                        </label>
                                        <?php echo e(Form::File('image', ['class' => 'form-control', 'id' => 'image', isset($banner) ? '' : 'required'])); ?>

                                        <?php if($errors->has('image')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('image')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            <?php echo app('translator')->get("$string_file.url"); ?>
                                        </label>
                                        <?php echo e(Form::url('redirect_url',  old('redirect_url', isset($banner->redirect_url) ? $banner->redirect_url : ''),['class' => 'form-control', 'id' => 'redirect_url'])); ?>

                                        <?php if($errors->has('redirect_url')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('redirect_url')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            <?php echo app('translator')->get("$string_file.sequence"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo e(Form::number('sequence', old('sequence', isset($banner->sequence) ? $banner->sequence : ''), ['class' => 'form-control', 'id' => 'sequence', 'placeholder' =>'', 'required'])); ?>

                                        <?php if($errors->has('sequence')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('sequence')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            <?php echo app('translator')->get("$string_file.banner_for"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo e(Form::select('banner_for', array('1' => 'User', '2' => 'Driver', '4' => 'All'),old('banner_for', isset($banner->banner_for ) ? $banner->banner_for  : ''), ['class' => 'form-control', 'id' => 'banner_for', 'required'])); ?>

                                        <?php if($errors->has('banner_for')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('banner_for')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">
                                            <?php echo app('translator')->get("$string_file.is_display_on_home_screen"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo e(Form::select('home_screen', get_status(true,$string_file),old('home_screen', isset($banner->home_screen ) ? $banner->home_screen  : ''), ['class' => 'form-control', 'id' => 'home_screen', 'required'])); ?>

                                        <?php if($errors->has('home_screen')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('home_screen')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 <?php if(empty($banner->id) || $banner->home_screen == 1): ?>custom-hidden <?php else: ?> <?php endif; ?>"
                                     id="segment">
                                    <div class="form-group">
                                        <label for="">
                                            <?php echo app('translator')->get("$string_file.segment"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo e(Form::select('segment_id', $arr_segment,old('segment_id', isset($banner->segment_id ) ? $banner->segment_id  : ''), ['class' => 'form-control', 'id' => 'segment_id'])); ?>

                                        <?php if($errors->has('segment_id')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('segment_id')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 <?php if(empty($banner->id) || $banner->home_screen == 1): ?>custom-hidden <?php else: ?> <?php endif; ?>"
                                     id="business_segment">
                                    <div class="form-group">
                                        <label for="">
                                            <?php echo app('translator')->get("$string_file.business_segment"); ?>
                                        </label>
                                        <?php echo e(Form::select('business_segment_id', [],old('business_segment_id', isset($banner->business_segment_id ) ? $banner->business_segment_id  : ''), ['class' => 'form-control', 'id' => 'business_segment_id'])); ?>

                                        <?php if($errors->has('business_segment_id')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('business_segment_id')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="<?php echo app('translator')->get('admin.banner_status'); ?>">
                                            <?php echo app('translator')->get("$string_file.status"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo e(Form::select('status', $arr_active_status,old('status', isset($banner->status ) ? $banner->status  : ''), ['class' => 'form-control', 'id' => 'status', 'required'])); ?>

                                        <?php if($errors->has('status')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('status')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.validity"); ?>:</label>
                                        <br>
                                        <div class="form-control">
                                            <label class="radio-inline">
                                                <input type="radio" value="1"
                                                       <?php if(isset($banner) && $banner->validity == 1): ?> checked <?php endif; ?>
                                                       id="validity"
                                                       name="validity"
                                                       required><?php echo app('translator')->get("$string_file.unlimited"); ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" value="2"
                                                       <?php if(isset($banner) && $banner->validity == 2): ?> checked <?php endif; ?>
                                                       name="validity"
                                                       id="validity"
                                                       required><?php echo app('translator')->get("$string_file.limited"); ?>
                                            </label>
                                            <?php if($errors->has('validity')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('validity')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.activate_date"); ?>
                                            :</label>
                                        <input type="text"
                                               class="form-control docs_datepicker"
                                               id="activate_date" name="activate_date"
                                               placeholder=""
                                               autocomplete="off"
                                               value="<?php echo e(old('activate_date', isset($banner->activate_date ) ? $banner->activate_date  : '')); ?>">
                                        <?php if($errors->has('activate_date')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('activate_date')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4 <?php if(empty($banner) || (!empty($banner) && $banner->validity == 1)): ?> custom-hidden <?php endif; ?>"
                                     id="expire-date">
                                    <div class="form-group">
                                        <label for="location3"><?php echo app('translator')->get("$string_file.expire_date"); ?>
                                            :</label>
                                        <input type="text"
                                               class="form-control docs_datepicker"
                                               id="expire_date" name="expire_date"
                                               placeholder=""
                                               autocomplete="off"
                                               value="<?php echo e(old('expire_date', isset($banner->expire_date ) ? $banner->expire_date  : '')); ?>">
                                        <?php if($errors->has('expire_date')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('expire_date')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-actions d-flex flex-row-reverse p-2">
                            <?php if(!$is_demo): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                            </button>
                            <?php else: ?>
                                <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                            <?php endif; ?>
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
        $(document).on('click', '#validity', function () {
            if ($(this).val() == 1) {
                $('#expire-date').hide();
            } else if ($(this).val() == 2) {
                $('#expire-date').show();
            }
        });
        $(document).on('change', '#home_screen', function () {
            if ($(this).val() == 1) {
                $('#segment').hide();
                $('#business_segment_id').hide();
                $("#segment_id").prop("required",false)
            } else if ($(this).val() == 2) {
                $('#segment').show();
                $("#segment_id").prop("required",true)
                $('#business_segment').show();
            }
        });
        $(document).on('click', '.docs_datepicker', function () {
            var dateFrom = new Date($('#banner_activate_date').val());
            var dateTo = new Date($('#banner_expire_date').val());
            var dateCurrent = new Date();

            console.log(dateTo);
            if ((dateFrom != 'Invalid Date' && dateTo != 'Invalid Date') && dateCurrent.getTime() < dateFrom.getTime() || dateCurrent.getTime() < dateTo.getTime())
                console.log("Please select future date");
            else if ((dateFrom != 'Invalid Date' && dateTo != 'Invalid Date') && dateFrom.getTime() >= dateTo.getTime())
                console.log("Expire date will be greater");
            else
                console.log("FIne");
        });
        $(document).on('change', '#segment_id', function () {
            $.ajax({
                type: "GET",
                data: {
                    id: $('#segment_id').val(),
                },
                url: "<?php echo e(route('advertisement.get.business-segment')); ?>",
            }).done(function (data) {
                $('#business_segment_id').empty().append('<option selected="selected" value=""><?php echo app('translator')->get("$string_file.select"); ?></option>');
                $.each(data,function(i,data)
                {
                    var div_data="<option value="+i+">"+data+"</option>";
                    $(div_data).appendTo('#business_segment_id');
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/advertisement_banner/create.blade.php ENDPATH**/ ?>
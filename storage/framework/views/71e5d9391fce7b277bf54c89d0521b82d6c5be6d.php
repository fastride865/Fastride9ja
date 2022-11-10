<?php $__env->startSection('content'); ?>
    <?php
        $arr_cal_method =  get_commission_method($string_file);
    ?>
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
                        <div class="btn-group float-right" style="margin:10px">
                            <a href="<?php echo e(route('merchant.segment.commission')); ?>">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo e($data['title']); ?>

                    </h3>
                </header>
                <?php $id = NULL; ?>
                <?php if(isset($data['commission']['id'])): ?>
                    <?php $id = $data['commission']['id']; ?>
                <?php endif; ?>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <?php echo Form::open(["class"=>"steps-validation wizard-notification","files"=>true,"url"=>route("segment.commission.save",$id)]); ?>

                        <?php echo Form::hidden('id',$id); ?>

                        <fieldset>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name"><?php echo app('translator')->get("$string_file.service_area"); ?> <span class="text-danger">*</span>
                                            </label>
                                            <?php echo Form::select('country_area_id',add_blank_option($data['arr_areas'],trans("$string_file.select")),old('country_area_id',isset($data['commission']['country_area_id']) ? $data['commission']['country_area_id'] :NULL),['class'=>'form-control','required'=>true,'id'=>'country_area_id','onChange'=>"getSegment()"]); ?>

                                            <?php if($errors->has('country_area_id')): ?>
                                                <span class="help-block">
                                                    <strong><?php echo e($errors->first('country_area_id')); ?></strong>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><?php echo app('translator')->get("$string_file.segment"); ?> <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                <?php echo Form::select('segment_id',add_blank_option($data['arr_segment'],trans("$string_file.select")),old('segment_id',isset($data['commission']['segment_id']) ? $data['commission']['segment_id'] :NULL),["class"=>"form-control","id"=>"area_segment","required"=>true]); ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emailAddress5"><?php echo app('translator')->get("$string_file.commission_method"); ?><span class="text-danger">*</span>
                                            </label>
                                            <?php echo Form::select('commission_method',add_blank_option($arr_cal_method,trans("$string_file.select")),old('commission_method',isset($data['commission']['commission_method']) ? $data['commission']['commission_method'] : NULL),["class"=>"form-control","id"=>"commission_method","required"=>true]); ?>

                                            <?php if($errors->has('commission_method')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('commission_method')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.amount"); ?>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <?php echo Form::number('commission',old('commission',isset($data['commission']['commission']) ? $data['commission']['commission'] : ''),['class'=>'form-control','id'=>'sequence_number','placeholder'=>"",'required'=>true,'min'=>0,'step'=>'any']); ?>

                                            <?php if($errors->has('commission')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('commission')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="firstName3">
                                                <?php echo app('translator')->get("$string_file.tax"); ?>
                                            </label>
                                            <?php echo Form::number('tax',old('tax',isset($data['commission']['tax']) ? $data['commission']['tax'] : ''),['class'=>'form-control','id'=>'sequence_number','placeholder'=>"",'required'=>true,'min'=>0]); ?>

                                            <?php if($errors->has('tax')): ?>
                                                <label class="text-danger"><?php echo e($errors->first('tax')); ?></label>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><?php echo app('translator')->get("$string_file.status"); ?> <span class="text-danger">*</span>
                                            </label>
                                            <div class="form-group">
                                                <?php echo Form::select('status',$data['arr_status'],old('service_type_id',isset($data['commission']['status']) ? $data['commission']['status'] :NULL),['class'=>'form-control','required'=>true,'id'=>'status']); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-actions float-right">

                                <?php if(!$data['is_demo']): ?>
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
    <script type="text/javascript">
        function getSegment() {
            $("#area_segment").empty();
            $("#area_segment").append('<option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>');
            $("#service_type_id").empty();
            $("#service_type_id").append('<option value=""><?php echo app('translator')->get("$string_file.select"); ?></option>');
            var area_id = $("#country_area_id option:selected").val();
            if (area_id != "") {
                $("#loader1").show();
                var token = $('[name="_token"]').val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    method: 'POST',
                    url: '<?php echo route('get.area.segment') ?>',
                    data: {area_id: area_id,segment_group_id:2},
                    success: function (data) {
                        $("#area_segment").empty();
                        $('#area_segment').html(data);
                    }
                });
                $("#loader1").hide();
            }
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/segment-commission/form.blade.php ENDPATH**/ ?>
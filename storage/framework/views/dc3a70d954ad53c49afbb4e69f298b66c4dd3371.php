<?php $__env->startSection('content'); ?>
    <?php $id = NULL; ?>
    <?php if(isset($data['service_time_slot']['id'])): ?>
        <?php $id = $data['service_time_slot']['id']; ?>
    <?php endif; ?>
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
                            <a href="<?php echo e(route('segment.service-time-slot')); ?>">
                                <button type="button" class="btn btn-icon btn-success"><i class="wb-reply"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <h3 class="panel-title"><i class="icon wb-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.service_time_slots"); ?><?php if(!empty($id)): ?>
                            <?php echo app('translator')->get("$string_file.for"); ?>
                            <?php echo e(isset($data['arr_day'][$data['service_time_slot']['day']]) ? $data['arr_day'][$data['service_time_slot']['day']] :''); ?>

                        <?php endif; ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <section id="validation">
                        <?php echo Form::open(["class"=>"steps-validation wizard-notification","files"=>true,"url"=>route("segment.service-time-slot.save",$id)]); ?>

                        <?php echo Form::hidden('id',$id); ?>

                        <?php echo Form::hidden('time_format',$data['time_format']); ?>

                        <fieldset>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name"><?php echo app('translator')->get("$string_file.service_area"); ?><span
                                                    class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::select('country_area_id',add_blank_option($data['arr_areas'],trans("$string_file.select")),old('country_area_id',isset($data['service_time_slot']['country_area_id']) ? $data['service_time_slot']['country_area_id'] :NULL),['class'=>'form-control','required'=>true,'id'=>'country_area_id','onChange'=>"getSegment()"]); ?>

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
                                            <?php echo Form::select('segment_id',add_blank_option($data['arr_segment'],trans("$string_file.select")),old('segment_id',isset($data['service_time_slot']['segment_id']) ? $data['service_time_slot']['segment_id'] :NULL),["class"=>"form-control","id"=>"area_segment","required"=>true,'onChange'=>"getService()"]); ?>

                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                                
                                
                                
                                
                                

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="firstName3">
                                            <?php echo app('translator')->get("$string_file.maximum_no_of_slots"); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?php echo Form::number('max_slot',old('max_slot',isset($data['service_time_slot']['max_slot']) ? $data['service_time_slot']['max_slot'] : ''),['class'=>'form-control','id'=>'sequence_number','placeholder'=>'','required'=>true,'min'=>2,'max'=>24]); ?>

                                        <?php if($errors->has('max_slot')): ?>
                                            <label class="text-danger"><?php echo e($errors->first('max_slot')); ?></label>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo app('translator')->get("$string_file.start_time"); ?> <span
                                                    class="text-danger">*</span>
                                        </label>
                                        <div class="form-group">
                                            <input type="text"
                                                   value="<?php echo e(isset($data['service_time_slot']['start_time']) ? $data['service_time_slot']['start_time'] :NULL); ?>"
                                                   class="form-control timepicker" data-autoclose="true" id="start_time"
                                                   q="" name="start_time" placeholder="" autocomplete="off">
                                        </div>
                                    </div>
                                    <?php if(!empty($id)): ?>
                                      <span class="text-danger"><?php echo app('translator')->get("$string_file.time_range_warning"); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo app('translator')->get("$string_file.end_time"); ?><span
                                                    class="text-danger">*</span>
                                        </label>
                                        <div class="form-group">
                                            <input type="text"
                                                   value="<?php echo e(isset($data['service_time_slot']['end_time']) ? $data['service_time_slot']['end_time'] :NULL); ?>"
                                                   class="form-control timepicker" data-autoclose="true" id="end_time"
                                                   q="" name="end_time" placeholder="" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php echo app('translator')->get("$string_file.status"); ?> <span class="text-danger">*</span>
                                        </label>
                                        <div class="form-group">
                                            <?php echo Form::select('status',$data['arr_status'],old('service_type_id',isset($data['service_time_slot']['status']) ? $data['service_time_slot']['status'] :NULL),['class'=>'form-control','required'=>true,'id'=>'status']); ?>

                                        </div>
                                    </div>
                                </div>
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
<?php $__env->startSection('js'); ?>
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
                    // For handyman segments and grocery based segments
                    data: {area_id: area_id,segment_group_id:2, sub_group_for_app: 2, check_where_or:true},
                    success: function (data) {
                        $("#area_segment").empty();
                        $('#area_segment').html(data);
                    }
                });
                $("#loader1").hide();
            }
        }
        <?php if($data['time_format'] == 2): ?>
        $('.timepicker').timepicker({
            timeFormat: 'H:i',
            // showMeridian: false
            // 'showDuration': true
        });
        <?php else: ?>
        $('.timepicker').timepicker({
            // timeFormat: 'H:i',
            // showMeridian: false
            // 'showDuration': true
        });
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/service-time-slot/form.blade.php ENDPATH**/ ?>
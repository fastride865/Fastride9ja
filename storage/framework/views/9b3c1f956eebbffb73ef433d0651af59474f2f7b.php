<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('driver.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right">
                                <i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                         <?php echo app('translator')->get("$string_file.driver_name"); ?> : <?php echo e($driver->first_name .' '.$driver->last_name); ?> ->  <?php echo app('translator')->get("$string_file.service_time_slots"); ?>
                        (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)
                    </h3>
                </header>
                <?php $display = true; $selected_doc = []; $id = NULL ?>
                <?php if(isset($driver->id) && !empty($driver->id)): ?>
                    <?php $display = false;
                    $id =  $driver->id;
                    ?>
                <?php endif; ?>
                <div class="panel-body container-fluid">
                    <?php echo Form::open(['name'=>'','class'=>'steps-validation wizard-notification','id'=>'driver-segment-time-slot','url'=>route('merchant.driver.segment.time-slot.save',$id)]); ?>

                    <?php echo Form::hidden("id",$id,['class'=>'','id'=>'id']); ?>

                     <?php $__currentLoopData = $arr_segment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                         <?php $segment_id = $segment->id; ?>
                            <div class="border rounded p-4 mt-10 shadow-sm bg-light">
                                <div class="border rounded p-4 mb-2 bg-white">
                                    <div class="row">
                                        <div class="col-md-12">
                                        <div class="form-group text-center">
                                            <strong><?php echo $segment->Name($merchant_id); ?>'s <?php echo app('translator')->get("$string_file.time_slot"); ?></strong>
                                        </div>
                                        </div>
                                    </div>
                                    <?php $__currentLoopData = $segment_time_slot['time_slots']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day_slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      <?php if($day_slot['segment_id'] == $segment_id && count($day_slot['service_time_slot']) > 0): ?>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="location3"><?php echo $day_slot['day_title']; ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row">
                                            <?php $arr_uploaded_doc = []; ?>
                                            <?php $__currentLoopData = $day_slot['service_time_slot']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=> $time_slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                             <div class="col-md-4">
                                               <label for="ProfileImage"></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input time-slot-checkbox" id="<?php echo e($time_slot['id']); ?>" name="arr_time_slot[<?php echo e($segment_id); ?>][]" value="<?php echo e($time_slot['id']); ?>" <?php echo e($time_slot['selected'] == 1 ? "checked" : ""); ?> >
                                                                <label class="custom-control-label" for="<?php echo e($time_slot['id']); ?>"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                      <input value="<?php echo e($time_slot['slot_time_text']); ?>" class="form-control" id="time-slot-<?php echo e($time_slot['id']); ?>" name="" placeholder="" aria-describedby="" disabled>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-actions d-flex flex-row-reverse p-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i><?php echo app('translator')->get("$string_file.save"); ?>
                        </button>
                    </div>
                    <?php echo Form::close(); ?>

                </div>
            </div>
        </div>
    </div>
        <?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script>
    $(document).on("click",".time-slot-checkbox",function(e){
        var val = $(this).val();
        if ($(this).is(':checked')) {
        }
    })
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/driver/segment-time-slot.blade.php ENDPATH**/ ?>
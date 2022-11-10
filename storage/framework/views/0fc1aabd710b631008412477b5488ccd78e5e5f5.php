<div class="row">
    <div class="col-md-2">
        <h3 class="panel-title" style="padding: 1px !important;">
            <?php echo app('translator')->get("$string_file.segments"); ?><span class="text-danger">*</span>
        </h3>
    </div>
    <div class="col-md-10">
    <?php $__currentLoopData = $arr_segment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                 <?php echo $segment; ?><input name="segment[]" value="<?php echo $key; ?>" class="form-group mr-10 mt-5 ml-20 area_segment" type="checkbox" <?php if(in_array($key,$selected)): ?>checked <?php endif; ?>>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if($errors->has('segment')): ?>
        <span class="help-block">
            <strong><?php echo e($errors->first('segment')); ?></strong>
        </span>
    <?php endif; ?>
</div>
</div>
<hr><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/segment.blade.php ENDPATH**/ ?>
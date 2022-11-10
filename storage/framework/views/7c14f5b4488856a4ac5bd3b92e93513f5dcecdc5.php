<h5><?php echo app('translator')->get("$string_file.set_service_charges_as_fixed"); ?></h5>
<hr>
<div class="row">
    <?php $__currentLoopData = $arr_services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $amount = isset($service->SegmentPriceCardDetail) ? $service->SegmentPriceCardDetail->amount : NULL;
            $detail_id = isset($service->SegmentPriceCardDetail) ? $service->SegmentPriceCardDetail->id : NULL;
        ?>
    <div class="col-md-4">
        <div class="form-group">
            <label for="firstName3">
               <?php echo e(!empty($service->serviceName($merchant_id)) ? $service->serviceName($merchant_id) : $service->serviceName); ?>

                <span class="text-danger">*</span>
            </label>
            <?php echo Form::hidden('detail_id['.$service->id.']',old('detail_id',$detail_id),['class'=>'form-control','id'=>'detail_id','placeholder'=>"",'required'=>true]); ?>

            <?php echo Form::number('fixed_amount['.$service->id.']',old('fixed_amount',$amount),['class'=>'form-control','id'=>'sequence_number','placeholder'=>"",'required'=>true,'min'=>0]); ?>

            <?php if($errors->has('fixed_amount')): ?>
                <label class="text-danger"><?php echo e($errors->first('fixed_amount')); ?></label>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/segment-pricecard/services-amount.blade.php ENDPATH**/ ?>
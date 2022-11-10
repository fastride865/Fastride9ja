<?php if($errors->all()): ?>
    <?php if(session('error')): ?>
        <div class="alert dark alert-icon alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <i class="icon fa-warning" aria-hidden="true"></i> <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="alert dark alert-icon alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">x</span>
            </button>
            <i class="icon fa-warning" aria-hidden="true"></i><?php echo e($message); ?>

        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php if(session('success')): ?>
    <div class="alert dark alert-icon alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        <i class="icon wb-info" aria-hidden="true"></i><?php echo e(session('success')); ?>

    </div>
<?php endif; ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/shared/errors-and-messages.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
          <?php echo $__env->make("business-segment.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <h3 class="panel-title">
                        <i class=" icon wb-paperclip" aria-hidden="true"></i><?php echo app('translator')->get("$string_file.style_segment"); ?></h3>
                </header>
                <div class="panel-body">
                    <form method="POST" action="<?php echo e(route('business-segment.style-segment.add')); ?>">
                        <?php echo csrf_field(); ?>
                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1 ?>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $style): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php $selected = in_array($style->id,$selected_style) ? 'checked' : ''; ?>
                                    <label>
                                        <input type="checkbox" class="checkbox" name="arr_style[]" value=" <?php echo e(($style->id)); ?>" <?php echo $selected; ?> >  <?php echo e($style->Name($style->merchant_id)); ?>

                                    </label>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>
                                <?php if(!$is_demo): ?>
                                <button type="submit" class="btn btn-primary float-right" onsubmit="">
                                    <i class="fa fa-check-circle"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                                </button>
                                <?php else: ?>
                                    <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('business-segment.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/business-segment/style-segment/index.blade.php ENDPATH**/ ?>
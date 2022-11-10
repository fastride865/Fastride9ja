<table class="table display nowrap table-striped table-bordered">
    <thead>
    <tr>
        <th><?php echo app('translator')->get("$string_file.name"); ?></th>
        <th><?php echo app('translator')->get("$string_file.receiver_type"); ?> </th>
        <th><?php echo app('translator')->get("$string_file.offer_type"); ?> </th>
        <th><?php echo app('translator')->get("$string_file.offer_value"); ?> </th>
        <th><?php echo app('translator')->get("$string_file.status"); ?> </th>
        <th><?php echo app('translator')->get("$string_file.date"); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $receiverBasic; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receiver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td>
                <?php echo e($receiver['name']); ?>

                <br>
                <?php echo e($receiver['phone']); ?>

                <br>
                <?php echo e($receiver['email']); ?>

            </td>
            <td><?php echo e($receiver['type']); ?></td>
            <td>
                <?php switch($receiver['offer_type']):
                    case (1): ?>
                    <?php echo app('translator')->get("$string_file.fixed_amount"); ?>
                    <?php break; ?>
                    <?php case (2): ?>
                    <?php echo app('translator')->get("$string_file.discount"); ?>
                    <?php break; ?>
                <?php endswitch; ?>
            </td>
            <td>
                <?php switch($receiver['offer_type']):
                    case (1): ?>
                    <?php echo e($receiver['currency']." ".$receiver['offer_value']); ?>

                    <?php break; ?>
                    <?php case (2): ?>
                    <?php echo e($receiver['offer_value']." %"); ?>

                    <?php break; ?>
                <?php endswitch; ?>
            </td>
            <td>
                <?php if($receiver['referral_available'] == 1): ?>
                    <?php echo app('translator')->get("$string_file.pending"); ?>
                <?php else: ?>
                    <?php echo app('translator')->get("$string_file.redeemed"); ?>
                <?php endif; ?>
            </td>
            <td><?php echo e($receiver['date']); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/report/referral-receiver-table.blade.php ENDPATH**/ ?>
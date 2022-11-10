<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <a href="<?php echo e(route('users.index')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"><i class="wb-reply"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="wb-map" aria-hidden="true"></i>
                        <?php echo e($user->first_name." ".$user->last_name); ?>'s <?php echo app('translator')->get("$string_file.saved_address"); ?></h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.address"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.latitude_longitude"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.title"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.category"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.created_at"); ?></th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php $a = 1; ?>
                        <?php $__currentLoopData = $user->UserAddress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($a); ?></td>
                                <td><?php echo e($location->address); ?></td>
                                <td><?php echo e($location->latitude.','.$location->longitude); ?></td>
                                <td>
                                    <?php echo e($location->address_title); ?>

                                </td>
                                <?php switch($location->category):
                                    case (1): ?>
                                    <td><?php echo app('translator')->get("$string_file.home"); ?></td>
                                    <?php break; ?>
                                    <?php case (2): ?>
                                    <td><?php echo app('translator')->get("$string_file.work"); ?></td>
                                    <?php break; ?>
                                    <?php case (3): ?>
                                    <td>
                                        <?php if($location->other_name): ?>
                                            <?php echo e($location->other_name); ?>

                                        <?php else: ?>
                                            <?php echo app('translator')->get("$string_file.other"); ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php break; ?>
                                    <?php default: ?>
                                    <td>----</td>
                                <?php endswitch; ?>

                                <td>
                                    <?php if(isset($user->CountryArea->timezone)): ?>
                                        <?php echo convertTimeToUSERzone($location->created_at, $user->CountryArea->timezone, null, $user->Merchant); ?>

                                    <?php else: ?>
                                        <?php echo convertTimeToUSERzone($location->created_at, null, null, $user->Merchant); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $a++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/user/favourite.blade.php ENDPATH**/ ?>
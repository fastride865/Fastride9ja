<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <div class="panel-actions"></div>
                    <h3 class="panel-title"><i class="wb-flag"></i>
                        <?php echo app('translator')->get("$string_file.application_string"); ?></h3>
                </div>
                <div class="panel-body container-fluid">
                    <form method="post" action="<?php echo e(route('exportString')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <?php if(Auth::user('merchant')->can('edit_language_strings')): ?>
                            <div class="col-md-2">
                                    <a href="<?php echo e(route('customEdit')); ?>" class="btn btn-success float-right"
                                       title=""> <?php echo app('translator')->get("$string_file.customize_string"); ?>
                                    </a>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-2">
                                <select class="form-control" name="platform" id="platform">
                                    <option value=""> -- <?php echo app('translator')->get("$string_file.application"); ?> --</option>
                                    <option value="android"> <?php echo app('translator')->get("$string_file.android"); ?> </option>
                                    <option value="ios"> <?php echo app('translator')->get("$string_file.ios"); ?> </option>
                                </select>
                                <?php if($errors->has('platform')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('platform')); ?></label>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="app" id="app" onchange="getKeyVal(this)">
                                    <option value=""> -- <?php echo app('translator')->get("$string_file.app"); ?> --</option>
                                    <option value="USER"> <?php echo app('translator')->get("$string_file.user"); ?> </option>
                                    <option value="DRIVER"> <?php echo app('translator')->get("$string_file.driver"); ?> </option>
                                </select>
                                <?php if($errors->has('app')): ?>
                                    <label class="text-danger"><?php echo e($errors->first('app')); ?></label>
                                <?php endif; ?>
                            </div>







                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.plateform"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.application"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.string_value"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.string_translation"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.group_name"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = 1; ?>
                        <?php if(!empty($application_string)): ?>
                            <?php $__currentLoopData = $application_string; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $general_string): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($sr); ?></td>
                                    <td> <?php echo e($general_string->platform); ?> </td>
                                    <td> <?php echo e($general_string->application); ?> </td>
                                    <td> <?php echo e($general_string->ApplicationStringLanguage[0]->string_value); ?> </td>
                                    <td> <?php echo e(isset($general_string->ApplicationMerchantString[0]) ? $general_string->ApplicationMerchantString[0]->string_value : "--"); ?> </td>
                                    <td> <?php echo e($general_string->string_group_name); ?> </td>
                                </tr>
                                <?php $sr++; $key=0;  ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if(!empty($application_string)): ?>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $application_string, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                   <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/application_string/index.blade.php ENDPATH**/ ?>
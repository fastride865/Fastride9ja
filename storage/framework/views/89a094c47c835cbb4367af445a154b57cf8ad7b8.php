<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <div class="btn-group float-right">
                            <a href="<?php echo e(route('new-role.index')); ?>">
                                <button type="button" class="btn btn-icon btn-success" style="margin:10px"><i
                                            class="wb-reply"></i>
                                </button>
                            </a>
                            <?php if(!empty($info_setting) && $info_setting->add_text != ""): ?>
                                <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                        data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                    <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="panel-title">
                        <i class=" wb-user-plus" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.role"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="POST" class="steps-validation wizard-notification"
                          enctype="multipart/form-data" action="<?php echo e(route('new-role.store',isset($role->id) ? $role->id : null)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.role"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name"
                                           name="name"
                                           value="<?php echo e(isset($role->display_name) ? $role->display_name : ""); ?>"
                                           placeholder="<?php echo app('translator')->get("$string_file.role"); ?>" required>
                                    <?php if($errors->has('name')): ?>
                                        <label class="danger"><?php echo e($errors->first('name')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstName3">
                                        <?php echo app('translator')->get("$string_file.description"); ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="description"
                                              name="description" rows="3"
                                              placeholder=""><?php echo e(isset($role->description) ? $role->description : ""); ?></textarea>
                                    <?php if($errors->has('description')): ?>
                                        <label class="danger"><?php echo e($errors->first('description')); ?></label>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-3">
                                <div class="checkbox-custom checkbox-primary">
                                    <input type="checkbox" id="check_all" class="check_all" onclick="checkall(this);"
                                           name="permission[]"
                                           value="1">
                                    <label for="check_all"><?php echo app('translator')->get("$string_file.check_all"); ?></label>
                                </div>
                            </div>
                        </div>
                        <?php use App\Custom\Helper;  $object = new Helper(); ?>
                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $merchant_id = get_merchant_id(); ?>
                            <?php if(($permission['special_permission'] == 0) || $object->show_permissions($merchant_id, $permission['name'])): ?>
                                <h4><?php echo e($permission['display_name']); ?></h4>
                                <div class="row ml-2">
                                    <?php if(!empty($permission['children'])): ?>
                                        <?php $__currentLoopData = $permission['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-2">
                                                <div class="checkbox-custom checkbox-primary">
                                                    <input type="checkbox" id="<?php echo e($child['id']); ?>" class="checked"
                                                           name="permission[]"
                                                           <?php if(!empty($permission_array) && in_array($child['id'], $permission_array)): ?> checked
                                                           <?php endif; ?>
                                                           value="<?php echo e($child['id']); ?>">
                                                    <label for="<?php echo e($child['id']); ?>"><?php echo e($child['display_name']); ?></label>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <div class="col-md-2">
                                            <div class="checkbox-custom checkbox-primary">
                                                <input type="checkbox" id="<?php echo e($permission['id']); ?>" class="checked"
                                                       name="permission[]"
                                                       <?php if(!empty($permission_array) && in_array($permission['id'], $permission_array)): ?> checked
                                                       <?php endif; ?>
                                                       value="<?php echo e($permission['id']); ?>">
                                                <label for="<?php echo e($permission['id']); ?>"><?php echo e($permission['display_name']); ?></label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $type_two_permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type_two_permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $merchant_id = get_merchant_id(); ?>
                            <?php if(($type_two_permission['special_permission'] == 0) || $object->show_permissions($merchant_id, $type_two_permission['name'])): ?>
                                <?php
                                    $segment_checked_status = false;
                                    if(!empty($permission_array) && !empty($type_two_permission['children'])){
                                        $child_ids = array_pluck($type_two_permission['children'],"id");
                                        $segment_checked_status = !array_diff($child_ids, $permission_array);
                                    }
                                ?>
                                <h4>
                                    <input type="checkbox" id="<?php echo e($type_two_permission['id']); ?>"
                                           class="checked" name="permission[]"
                                           <?php if($segment_checked_status): ?> checked
                                           <?php endif; ?>
                                           onclick="checkSegment(this, '<?php echo e($type_two_permission["name"]); ?>');"
                                           value="<?php echo e($type_two_permission['id']); ?>">
                                    <label for="<?php echo e($type_two_permission['id']); ?>"><?php echo e($type_two_permission['display_name']); ?></label>
                                </h4>
                                <div class="row ml-2">
                                    <?php if(!empty($type_two_permission['children'])): ?>
                                        <?php $__currentLoopData = $type_two_permission['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-2">
                                                <li><?php echo e($child['display_name']); ?></li>
                                                <div class="checkbox-custom checkbox-primary" style="display: none">
                                                    <input type="checkbox" id="<?php echo e($child['id']); ?>"
                                                           class="checked <?php echo e($type_two_permission['name']); ?>" readonly
                                                           name="permission[]"
                                                           <?php if(!empty($permission_array) && in_array($child['id'], $permission_array)): ?> checked
                                                           <?php endif; ?>
                                                           value="<?php echo e($child['id']); ?>">
                                                    <label for="<?php echo e($child['id']); ?>"><?php echo e($child['display_name']); ?></label>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-actions float-right ">
                            <?php if(!$is_demo): ?>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-square-o"></i> <?php echo app('translator')->get("$string_file.save"); ?>
                            </button>
                            <?php else: ?>
                                <span style="color: red" class="float-right"><?php echo app('translator')->get("$string_file.demo_warning_message"); ?></span>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'add_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        function checkall(data) {
            //alert(data);
            var requiredCheckboxes = $('.checked');
            if ($(data).is(':checked')) {
                requiredCheckboxes.prop('checked', true);
            } else {
                requiredCheckboxes.prop('checked', false);
            }
        }

        function checkSegment(data, attr) {
            console.log(attr);
            var requiredCheckboxes = $('.' + attr);
            if ($(data).is(':checked')) {
                requiredCheckboxes.prop('checked', true);
            } else {
                requiredCheckboxes.prop('checked', false);
            }
        }

        $(document).on("change", ".checked", function () {
            if ($('.checked:checked').length == $('.checked').length) {
                $('.check_all').prop('checked', true);
            } else {
                $('.check_all').prop('checked', false);
            }
        });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/new-role/create.blade.php ENDPATH**/ ?>
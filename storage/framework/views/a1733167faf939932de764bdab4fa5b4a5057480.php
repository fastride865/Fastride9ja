<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
           <?php echo $__env->make("merchant.shared.errors-and-messages", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px"
                                data-toggle="modal" data-target="#inlineForm">
                            <i class="wb-plus" title="<?php echo app('translator')->get("$string_file.cancel_reason"); ?> "></i>
                        </button>
                    </div>
                    <h3 class="panel-title"><i class=" wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.cancel_reason_management"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form method="post" action="<?php echo e(route('cancelreason.search')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.cancel_reason"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.reason_for"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?> </th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $cancelreasons->firstItem() ?>
                        <?php $__currentLoopData = $cancelreasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cancelreason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php echo e($cancelreason->ReasonName); ?>

                                   
                                </td>
                                <?php switch($cancelreason->reason_type):
                                    case (1): ?>
                                    <td><?php echo app('translator')->get("$string_file.user"); ?> </td>
                                    <?php break; ?>
                                    <?php case (2): ?>
                                    <td><?php echo app('translator')->get("$string_file.driver"); ?></td>
                                    <?php break; ?>
                                    <?php case (3): ?>
                                    <td><?php echo app('translator')->get("$string_file.dispatcher"); ?> </td>
                                    <?php break; ?>
                                <?php endswitch; ?>
                                <td><?php echo e(array_key_exists($cancelreason->segment_id,$merchant_segments) ? $merchant_segments[$cancelreason->segment_id] : '--'); ?></td>
                                <td>
                                    <?php if($cancelreason->reason_status == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>

                                <td style="width:100px;float:left">
                                    <a href="<?php echo e(route('cancelreason.edit',$cancelreason->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i
                                                class="fa fa-edit"></i> </a>
                                    <?php if($cancelreason->reason_status == 1): ?>
                                        <a href="<?php echo e(route('merchant.cancelreason.active-deactive',['id'=>$cancelreason->id,'status'=>2])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i
                                                    class="fa fa-eye-slash"></i> </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('merchant.cancelreason.active-deactive',['id'=>$cancelreason->id,'status'=>1])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                            <i
                                                    class="fa fa-eye"></i> </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $cancelreasons, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <label class="modal-title text-text-bold-600"
                           id="myModalLabel33"><b><?php echo app('translator')->get("$string_file.cancel_reason"); ?>
                            (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(route('cancelreason.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label><?php echo app('translator')->get("$string_file.reason_for"); ?> <span class="text-danger">*</span></label>
                        <div class="form-group">
                            <select class="form-control" name="reason_for"
                                    id="reason_for" required>
                                <option value="">--<?php echo app('translator')->get("$string_file.select"); ?> --</option>
                                <option value="1"><?php echo app('translator')->get("$string_file.user"); ?> </option>
                                <option value="2"><?php echo app('translator')->get("$string_file.driver"); ?></option>
                                <option value="3"><?php echo app('translator')->get("$string_file.dispatcher"); ?> </option>

                                <?php if(in_array(3,$merchant_segments) || in_array(4,$merchant_segments)): ?>
                                <option value="4"><?php echo app('translator')->get("$string_file.business_segment"); ?> </option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <label><?php echo app('translator')->get("$string_file.segment"); ?>  <span class="text-danger">*</span></label>
                        <div class="form-group">
                            <select class="form-control" name="segment_id" id="segment_id"
                                    required>
                                <option value=""><?php echo app('translator')->get("$string_file.select"); ?> </option>
                                <?php $__currentLoopData = $merchant_segments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <label> <?php echo app('translator')->get("$string_file.reason"); ?>
                            <span class="text-danger">*</span> </label>
                        <div class="form-group">
                            <textarea class="form-control" id="reason" name="reason" rows="3"
                                      placeholder=""></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary btn" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.close"); ?>">
                        <input type="submit" class="btn btn-outline-primary btn" value="<?php echo app('translator')->get("$string_file.add"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/cancelreason/index.blade.php ENDPATH**/ ?>
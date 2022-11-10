<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title">
                        <i class="icon fa-taxi" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.driver_cashout_management"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.driver_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.cashout_amount"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action_by"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.transaction_id"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.comment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.requested_at"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action_date"); ?></th>
                            <?php if(Auth::user('merchant')->can('edit_driver_cash_out')): ?>
                                <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $driver_cashout_requests->firstItem() ?>
                        <?php $__currentLoopData = $driver_cashout_requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver_cashout_request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                    <td>
                                         <span class="long_text">
                                         <?php echo e($driver_cashout_request->Driver->fullName); ?>

                                        <br>
                                        <?php echo e($driver_cashout_request->Driver->phoneNumber); ?>

                                        <br>
                                        <?php echo e($driver_cashout_request->Driver->email); ?>

                                        </span>
                                    </td>
                                <td><?php echo e($driver_cashout_request->Driver->CountryArea->Country->isoCode.' '.$driver_cashout_request->amount); ?></td>
                                <td>
                                    <?php switch($driver_cashout_request->cashout_status):
                                        case (0): ?>
                                        <small class="badge badge-round badge-warning float-left"><?php echo app('translator')->get("$string_file.pending"); ?></small>
                                        <?php break; ?>;
                                        <?php case (1): ?>
                                        <small class="badge badge-round badge-info float-left"><?php echo app('translator')->get("$string_file.success"); ?></small>
                                        <?php break; ?>;
                                        <?php case (2): ?>
                                        <small class="badge badge-round badge-danger float-left"><?php echo app('translator')->get("$string_file.rejected"); ?></small>
                                        <?php break; ?>;
                                        <?php default: ?>
                                        ----
                                    <?php endswitch; ?>
                                </td>
                                <td><?php echo e(($driver_cashout_request->action_by != '') ? $driver_cashout_request->action_by : '---'); ?></td>
                                <td><?php echo e(($driver_cashout_request->transaction_id) ? $driver_cashout_request->transaction_id : '---'); ?></td>
                                <td><?php echo e(($driver_cashout_request->comment != '') ? $driver_cashout_request->comment : '---'); ?></td>
                                <td>
                                    <?php echo convertTimeToUSERzone($driver_cashout_request->created_at, $driver_cashout_request->Driver->CountryArea->timezone,null,$driver_cashout_request->Driver->Merchant); ?>

                                </td>
                                <td>
                                   <?php if($driver_cashout_request->cashout_status != 0): ?>
                                    <?php echo convertTimeToUSERzone($driver_cashout_request->updated_at, $driver_cashout_request->Driver->CountryArea->timezone,null,$driver_cashout_request->Driver->Merchant); ?>

                                    <?php else: ?>
                                       ---
                                    <?php endif; ?>
                                </td>
                                <?php if(Auth::user('merchant')->can('edit_driver_cash_out')): ?>
                                    <td>
                                        <a href="<?php echo e(route('merchant.driver.cashout_status',$driver_cashout_request->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $driver_cashout_requests, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/cashout/index.blade.php ENDPATH**/ ?>
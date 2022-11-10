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
                        <a href="<?php echo e(route('segment.service-time-slot.add')); ?>">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin:10px">
                                <i class="wb-plus"
                                   title="<?php echo app('translator')->get("$string_file.add_time_slot"); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="icon fa-money" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.service_time_slots"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <?php echo $__env->make('merchant.segment-pricecard.search', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_area"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.day"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.maximum_no_of_slots"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.start_time"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.end_time"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $arr_service_time_slot->firstItem();
                        ?>
                        <?php $__currentLoopData = $arr_service_time_slot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_time_slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $start = strtotime($service_time_slot->start_time);
                                $end = strtotime($service_time_slot->end_time);
                            ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php echo e($service_time_slot->CountryArea->CountryAreaName); ?>

                                </td>
                                <td><?php echo e(!empty($service_time_slot->Segment->Name($service_time_slot->merchant_id)) ? $service_time_slot->Segment->Name($service_time_slot->merchant_id) : $service_time_slot->Segment->slag); ?></td>
                                
                                <td><?php echo e($arr_day[$service_time_slot->day]); ?></td>
                                <td><?php echo e($service_time_slot->max_slot); ?></td>
                                <td><?php echo e($time_format == 2 ? date("H:i", $start) : date("h:i a", $start)); ?></td>
                                <td><?php echo e($time_format == 2 ? date("H:i", $end) : date("h:i a", $end)); ?></td>
                                <td>
                                    <?php if($service_time_slot->status == 1): ?>

                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('segment.service-time-slot.add',$service_time_slot->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>

                                    <a href="<?php echo e(route('service-time-slot.detail',$service_time_slot->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.time_slots_configuration"); ?>"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                        <i class="fa fa-clock-o"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $arr_service_time_slot, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/service-time-slot/index.blade.php ENDPATH**/ ?>
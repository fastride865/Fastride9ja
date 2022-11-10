<?php $__env->startSection('content'); ?>
    <div class="page">
        <div class="page-content">
            <?php echo $__env->make('merchant.shared.errors-and-messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="panel panel-bordered">
                <header class="panel-heading"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <div class="panel-actions">
                        <?php if(!empty($info_setting) && $info_setting->view_text != ""): ?>
                            <button class="btn btn-icon btn-primary float-right" style="margin:10px"
                                    data-target="#examplePositionSidebar" data-toggle="modal" type="button">
                                <i class="wb-info ml-1 mr-1" title="Info" style=""></i>
                            </button>
                        <?php endif; ?>
                        <?php if(Auth::user('merchant')->can('create_area')): ?>
                            <a href="<?php echo e(route('excel.serviceareamanagement')); ?>">
                                <button type="button" title="<?php echo app('translator')->get("$string_file.export_excel"); ?>"
                                        class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="fa fa-download"></i>
                                </button>
                            </a>
                            <a href="<?php echo e(route('countryareas.add')); ?>">
                                <button type="button" title="<?php echo app('translator')->get("$string_file.add_service_area"); ?>"
                                        class="btn btn-icon btn-success mr-1 float-right" style="margin:10px"><i
                                            class="fa fa-plus"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title">
                        <i class=" icon wb-flag" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.service_area_management"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <form class="form-group" action="<?php echo e(route('countryArea.Search')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <select name="area_id" class="form-control">
                                    <option value="">-- <?php echo app('translator')->get("$string_file.select"); ?> --</option>
                                    <?php $__currentLoopData = $arr_areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($area->id); ?>" <?php echo e($area_id == $area->id ? "selected" : NULL); ?>>
                                            <?php echo e($area->CountryAreaName); ?>


                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-small btn-primary" type="submit"><i class="fa fa-search"></i></button>
                            </div>
                            <div class="col-md-1 ml--25">
                                <a href="<?php echo e(route('countryareas.index')); ?>">
                                <button class="btn btn-small btn-success" type="button"><i class="fa fa-refresh"></i></button>
                                </a>
                            </div>
                        </div>
                    </form>
                    <table id="customDataTable" class="display nowrap table table-hover table-stripedw-full" style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.name"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.country"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.segment"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.personal_document"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.timezone"); ?></th>
                            <?php if($config->driver_wallet_status == 1): ?>
                                <th title=""><?php echo app('translator')->get("$string_file.wallet_money"); ?></th>
                            <?php endif; ?>
                            <?php if($config->no_driver_availabe_enable == 1): ?>
                                <th><?php echo app('translator')->get("$string_file.auto_upgradation"); ?></th>
                            <?php endif; ?>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tfoot></tfoot>
                        <tbody>
                        <?php $sr = $areas->firstItem() ?>
                        <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td>
                                    <?php echo e($area->CountryAreaName); ?>

                                </td>
                                <td><?php echo e($area->country->CountryName); ?></td>

                                <?php $arr_segment = ""?>
                                <?php $__currentLoopData = $area->Segment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $arr_segment .= $segment->Name($area->merchant_id).', ';
                                    ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php $arr_segment = substr($arr_segment, 0, -2) ?>

                                <td title="<?php echo e($arr_segment); ?>">
                                    <span class="">
                                        <?php if(strlen($arr_segment) > 20): ?>
                                            <?php $trimstring = substr($arr_segment, 0, 20). ' ....etc'; ?>
                                        <?php else: ?>
                                            <?php $trimstring = $arr_segment; ?>
                                        <?php endif; ?>
                                        <?php echo e($trimstring); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php if($area->is_geofence == 1): ?>
                                        <?php echo app('translator')->get("$string_file.geofence_area"); ?>
                                    <?php else: ?>
                                        <?php echo app('translator')->get("$string_file.service_area"); ?>
                                    <?php endif; ?>
                                </td>
                                <?php $a = array() ?>
                                <?php $__currentLoopData = $area->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $a[] = $document->DocumentName; $arr_doc = implode(',',$a) ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td title="<?php echo e($arr_doc); ?>">
                                    <span class="">
                                        <?php if(strlen($arr_doc) > 20): ?>
                                        <?php $trimstring = substr($arr_doc, 0, 20). ' ....etc'; ?>
                                        <?php else: ?>
                                        <?php $trimstring = $arr_doc; ?>
                                        <?php endif; ?>
                                    <?php echo e($trimstring); ?>

                                    </span>
                                </td>
                                <td><?php echo e($area->timezone); ?></td>
                                <?php if($config->driver_wallet_status == 1): ?>
                                    <td>
                                        <?php if(!empty($area->minimum_wallet_amount)): ?>
                                            <?php echo e($area->Country->isoCode.' '.$area->minimum_wallet_amount); ?>

                                        <?php else: ?>
                                            0
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <?php if($config->no_driver_availabe_enable == 1): ?>
                                    <td>
                                        <?php if($area->auto_upgradetion): ?>
                                            <?php if($area->auto_upgradetion == 1): ?> Enable <?php elseif($area->auto_upgradetion == 2): ?> Disable <?php endif; ?>
                                        <?php else: ?>
                                            ------
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php if($area->status == 1): ?>
                                        <span class="badge badge-success font-size-14"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(Auth::user('merchant')->can('edit_area')): ?>
                                        <a href="<?php echo e(route('countryareas.add',$area->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit_area_config"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">
                                            <img src="<?php echo e(asset('basic-images/basic-edit.png')); ?>" height="20" width="20">
                                        </a>

                                        <?php if($segment_group_vehicle == true): ?>
                                        <a href="<?php echo e(route('countryareas.add.step2',$area->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.vehicle_configuration"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary menu-icon btn_edit action_btn">

                                            <img src="<?php echo e(asset('basic-images/taxi.png')); ?>" height="20" width="20">
                                        </a>
                                        <?php endif; ?>
                                        <?php if($segment_group_handyman == true): ?>
                                        <a href="<?php echo e(route('countryareas.add.step3',$area->id)); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.handyman_configuration"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-primary btn_edit action_btn">
                                            <img src="<?php echo e(asset('basic-images/handyman.png')); ?>" height="20" width="20">
                                        </a>
                                        <?php endif; ?>
                                        <?php if($category_vehicle_type_module == 1 && in_array('TAXI',array_pluck($area->Segment,'slag'))): ?>
                                            <a href="<?php echo e(route('country-area.category.vehicle.type',$area->id)); ?>"
                                               data-original-title="<?php echo app('translator')->get("$string_file.vehicle_type_categorization"); ?>" data-toggle="tooltip"
                                               data-placement="top"
                                               class="btn btn-sm btn-primary btn_edit action_btn">
                                                <i class="fa fa-list-alt" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>






                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $areas, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/area/index.blade.php ENDPATH**/ ?>
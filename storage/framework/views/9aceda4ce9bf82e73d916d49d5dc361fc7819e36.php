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
                        <?php if(Auth::user('merchant')->can('create_business_segment_'.$slug)): ?>
                            <a href="<?php echo e(route('merchant.business-segment/add',[$slug])); ?>">
                                <button type="button" title="<?php echo app('translator')->get("$string_file.add"); ?> <?php echo e($title); ?>"
                                        class="btn btn-icon btn-success float-right"  style="margin:10px"><i class="wb-plus"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-building" aria-hidden="true"></i>
                        <?php echo e($title); ?>

                    </h3>
                </header>
                <div class="panel-body">
                    <?php echo $search_view; ?>

                    <table class="display nowrap table table-hover table-striped w-full" id="customDataTable" style="width:100%"
                           cellspacing="0">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.contact_details"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.address"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.login_url"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.rating"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                            <?php if(Auth::user('merchant')->can('order_statistics_'.$slug)): ?>
                                <th><?php echo app('translator')->get("$string_file.order_statistics"); ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $data->firstItem(); ?>
                        <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $business_segment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php echo app('translator')->get("$string_file.name"); ?>: <?php echo e($business_segment->full_name); ?> <br>
                                    <?php echo app('translator')->get("$string_file.phone"); ?>: <?php echo e($business_segment->phone_number); ?>

                                </td>
                                <td>
                                    <?php if(!empty($business_segment->address)): ?>
                                        <a title="<?php echo e($business_segment->address); ?>"
                                           target="_blank"
                                           href="https://www.google.com/maps/place/<?php echo e($business_segment->address); ?>">
                                            <?php if($business_segment->business_logo): ?>
                                            <img src="<?php echo e(get_image($business_segment->business_logo,'business_logo',$business_segment->merchant_id)); ?>" height="40" width="60">
                                            <?php else: ?>
                                            <span class="btn btn-icon btn-success"><i class="icon wb-map"></i></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $merchant_alias = $business_segment->merchant->alias_name;
                                            $url = "business-segment/admin/$merchant_alias/$business_segment->alias_name/login";
                                    ?>
                                    <a href="<?php echo URL::to('/'.$url); ?>"
                                       target="_blank" rel="noopener noreferrer"class="btn btn-icon btn-info btn_eye action_btn">
                                        <?php echo app('translator')->get("$string_file.login_url"); ?>
                                    </a>
                                    <br>
                                    <?php echo app('translator')->get("$string_file.email"); ?>: <?php echo e($business_segment->email); ?>

                                </td>
                                <td><?php echo e($business_segment->rating); ?></td>
                                <td>

                                    <?php if($business_segment->status == 1): ?>
                                        <span class="badge badge-success font-size-14"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                    <?php if(Auth::user('merchant')->can('create_business_segment_'.$slug)): ?>
                                        <a href="<?php echo e(route('merchant.business-segment/add',['slug'=>$business_segment->Segment->slag,'id'=>$business_segment->id])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                           class="btn btn-sm btn-warning">
                                            <i class="wb-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <?php if(Auth::user('merchant')->can('order_statistics_'.$slug)): ?>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('merchant.business-segment.statistics',['slug'=>$business_segment->Segment->slag,'b_id'=>$business_segment->id])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.view_statistics"); ?>" data-toggle="tooltip"
                                           class="btn btn-sm btn-success">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $data, 'data' => $arr_search], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/business-segment/index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <style>
    </style>
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
                    </div>
                    <h3 class="panel-title">
                        <i class="fa fa-cog fa-spin" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.services"); ?></h3>
                </header>
                <div id="exampleTransition" class="page-content container-fluid" data-plugin="animateList">
                    <ul class="blocks-sm-100 blocks-xxl-3">
                        <?php $__currentLoopData = $segment_services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $services): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <div class="panel panel-bordered" style="border: 1px solid #e4eaec;">
                                    <div class="panel-heading">
                                        <a href="">
                                            <h3 class="panel-title segment_class">
                                                <?php echo $services['slag']; ?>

                                            </h3>
                                        </a>
                                        <div class="panel-actions">
                                            <img class="img-responsive" height="50px"
                                                 src="<?php echo $services['segment_icon']; ?>">
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <span style="font-size:20px;"><?php echo $services['name']; ?></span>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                            <a href="<?php echo e(route('merchant.segment.edit',$services['segment_id'])); ?>"
                                               class="panel-action" data-toggle="panel-close" aria-hidden="true"
                                               title="<?php echo app('translator')->get("$string_file.edit"); ?>"><i class="fa-pencil"></i> </a>

                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <?php if($services['segment_group_id'] == 2): ?>
                                            <a href="<?php echo e(route('merchant.serviceType.edit',$services['segment_id'])); ?>"
                                               class="panel-action float-right" data-toggle="panel-close"
                                               aria-hidden="true" title="<?php echo app('translator')->get('admin.add_service'); ?>"><i
                                                        class="fa-plus"></i> </a>
                                        <?php endif; ?>
                                        <div class="example table-responsive">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th><?php echo app('translator')->get("$string_file.type"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.service_type"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.description"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.sequence"); ?></th>
                                                    <th><?php echo app('translator')->get("$string_file.icon"); ?></th>
                                                    <?php if(Auth::user('merchant')->can('edit_service_types')): ?>
                                                        <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                                                    <?php endif; ?>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i = 1; ?>
                                                <?php $__currentLoopData = $services['arr_services']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($i); ?></td>
                                                        <td><?php echo $service['serviceName']; ?></td>
                                                        <td><?php echo $service['locale_service_name']; ?></td>
                                                        <td><?php echo $service['locale_service_description']; ?></td>
                                                          <td><?php echo $service['service_sequence']; ?></td>
                                                        <td>
                                                          <img class="img-responsive" height="50px" width="50px"
                                                               src="<?php echo $service['service_icon']; ?>">
                                                        </td>
                                                        <td>
                                                            <?php if(Auth::user('merchant')->can('edit_service_types')): ?>
                                                            <a href="<?php echo e(route('merchant.serviceType.edit',[$service['segment_id'],$service['id']])); ?>"
                                                               class="panel-action" data-toggle="panel-close"
                                                               aria-hidden="true" title="<?php echo app('translator')->get("$string_file.edit"); ?>"><i
                                                                        class="fa-pencil"
                                                                        style="padding-left: 19%;"></i> </a>
                                                            <?php endif; ?>
                                                        </td>
                                                        <?php $i++; ?>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/fastride9ja/public_html/multi-service-v1/resources/views/merchant/service_types/index.blade.php ENDPATH**/ ?>
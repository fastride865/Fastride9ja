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
                        <a href="<?php echo e(route('outstationpackage.create')); ?>" data-toggle="tooltip">
                            <button type="button" class="btn btn-icon btn-success float-right" style="margin: 10px;">
                                <i class="wb-plus" title="<?php echo app('translator')->get('admin.message102'); ?>"></i>
                            </button>
                        </a>
                    </div>
                    <h3 class="panel-title"><i class="fa-building-o" aria-hidden="true"></i>
                        <?php echo app('translator')->get("$string_file.outstation_service"); ?>
                    </h3>
                </header>
                <div class="panel-body container-fluid">
                    <table id="customDataTable" class="display nowrap table table-hover table-striped w-full"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th><?php echo app('translator')->get("$string_file.sn"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.special_city"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.service_type"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.description"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.status"); ?></th>
                            <th><?php echo app('translator')->get("$string_file.action"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $sr = $packages->firstItem() ?>
                        <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($sr); ?></td>
                                <td><?php if(empty($package->LanguageSingle)): ?>
                                        <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                        <span class="text-primary">( In <?php echo e($package->LanguageAny->LanguageName->name); ?>

                                                            : <?php echo e($package->LanguageAny->city); ?>

                                                            )</span>
                                    <?php else: ?>
                                        <?php echo e($package->LanguageSingle->city); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($package->ServiceType->serviceName); ?>

                                </td>
                                <td><?php if(empty($package->LanguageSingle)): ?>
                                        <span style="color:red"><?php echo e(trans("$string_file.not_added_in_english")); ?></span>
                                        <span class="text-primary map_address">( In <?php echo e($package->LanguageAny->LanguageName->name); ?>

                                                            : <?php echo e(substr($package->LanguageAny->description,0,50)); ?>

                                                            )</span>
                                    <?php else: ?>
                                        <span class="map_address"><?php echo e(substr($package->LanguageSingle->description,0,50)); ?>..</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($package->status  == 1): ?>
                                        <span class="badge badge-success"><?php echo app('translator')->get("$string_file.active"); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?php echo app('translator')->get("$string_file.inactive"); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('outstationpackage.edit',$package->id)); ?>"
                                       data-original-title="<?php echo app('translator')->get("$string_file.edit"); ?>" data-toggle="tooltip"
                                       data-placement="top"
                                       class="btn btn-sm btn-warning menu-icon btn_edit action_btn"> <i
                                                class="fa fa-edit"></i> </a>
                                    <?php if($package->status == 1): ?>
                                        <a href="<?php echo e(route('merchant.outstationpackage.active-deactive',['id'=>$package->id,'status'=>2])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.inactive"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-danger menu-icon btn_eye_dis action_btn">
                                            <i class="fa fa-eye-slash"></i> </a>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('merchant.outstationpackage.active-deactive',['id'=>$package->id,'status'=>1])); ?>"
                                           data-original-title="<?php echo app('translator')->get("$string_file.active"); ?>" data-toggle="tooltip"
                                           data-placement="top"
                                           class="btn btn-sm btn-success menu-icon btn_eye action_btn">
                                            <i class="fa fa-eye"></i> </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php $sr++  ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('merchant.shared.table-footer', ['table_data' => $packages, 'data' => []], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                           id="myModalLabel33"><b><?php echo app('translator')->get("$string_file.special_city"); ?>
                            (<?php echo app('translator')->get("$string_file.you_are_adding_in"); ?> <?php echo e(strtoupper(Config::get('app.locale'))); ?>)</b></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="<?php echo e(route('outstationpackage.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label><?php echo app('translator')->get("$string_file.name"); ?><span class="text-danger">*</span></label>
                        <div class="form-group">
                            <input type="text" class="form-control" id="city" name="city" placeholder="" required>
                        </div>
                        <label> <?php echo app('translator')->get("$string_file.description"); ?><span class="text-danger">*</span></label>
                        <div class="form-group">
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder=""></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="reset" class="btn btn-outline-secondary" data-dismiss="modal"
                               value="<?php echo app('translator')->get("$string_file.reset"); ?>">
                        <input type="submit" class="btn btn-outline-primary" value="<?php echo app('translator')->get("$string_file.save"); ?>">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php echo $__env->make('merchant.shared.info-setting',['info_setting'=>$info_setting,'page_name'=>'view_text'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=get_merchant_google_key(NULL, 'admin_backend');?>&v=3.exp&libraries=places&language=en&region=ES"></script>
    <script>
        function initialize() {
            var input = document.getElementById('city');
            var options = {
                types: ['(cities)'],
            };
            var autocomplete = new google.maps.places.Autocomplete(input, options);
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('merchant.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/outstation/index.blade.php ENDPATH**/ ?>